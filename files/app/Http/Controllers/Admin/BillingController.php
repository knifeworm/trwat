<?php

namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Models\Invoice;
use Pterodactyl\Models\User;
use Validator;
use DB;

class BillingController extends Controller
{
    public function licensing() {
        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, 'http://licensing.mrkevko.nl/checker/'. env('APP_URL', 'no_url.com'));
        curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $data = json_decode(curl_exec($curlSession));
        curl_close($curlSession);

        $actual_link = $_SERVER['HTTP_HOST'];

        if (env('APP_URL', 'no_url.com') !== $_SERVER['HTTP_HOST']) {
            return false;
        }

        if ($data->code == 'ka-01') {
            return $data;
        }

        if ($data->code == 'ka-02') {
            return false;
        }
    }

    public function index(Request $req)
    {
        $this_month_income = DB::table('invoices')->select(DB::raw('SUM(amount) as total_amount'))->where('reason', '=', 'Top up Credit')->groupBy(DB::raw('YEAR(created_at) DESC, MONTH(created_at) DESC'))->sum('amount');
        $this_year_income = DB::table('invoices')->select(DB::raw('SUM(amount) as total_amount'))->where('reason', '=', 'Top up Credit')->groupBy(DB::raw('YEAR(created_at) DESC'))->sum('amount');
        $user_accounts = DB::table('users')->sum('balance');
        $latest_version = '1.0.1';
        $this_version = env('BILLING_VERSION');
        $billing = DB::table('billing')->get();

        return view('admin.billing.index', [
          'invoices' => Invoice::orderBy('id', 'desc')->where('reason', '=', 'Top up Credit')->paginate(25),
          'this_month_income' => $this_month_income,
          'this_year_income' => $this_year_income,
          'user_accounts' => $user_accounts,
          'latest_version' => $latest_version,
          'this_version' => $this_version,
          'billing' => $billing,
        ]);
    }

    public function new(Request $req)
    {
        return view('admin.billing.new');
    }

    public function categories_edit($category)
    {
        $this_category = DB::table('categories')->where('id', '=', $category)->get();

        return view('admin.billing.categories.edit', ['category' => $this_category]);
    }

    public function categories_edit_store($category, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'priority' => 'required',
            'visible' => 'required',
        ]);

        DB::table('categories')->where('id', '=', $category)->update([
          'name' => $request->name,
          'description' => $request->description,
          'priority' => $request->priority,
          'visible' => $request->visible,
        ]);

        return redirect(route('admin.billing.categories'));
    }

    public function tos()
    {
        $tos = DB::table('billing')->get();

        return view('admin.billing.tos.index', ['tos' => $tos]);
    }

    public function tos_update(Request $request)
    {
        DB::table('billing')->update([
          'tos' => $request->tos
        ]);

        return redirect()->back();
    }

    public function submit(Request $req)
    {
        $req->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:-500|max:500',
        ]);

        $user = User::find($req->user_id);
        $user->addBalance($req->amount);

        return redirect()->back();
    }

    public function pdf(Request $req)
    {
        return Invoice::find($req->id)->downloadPdf();
    }

    public function categories(): View
    {
        $categories = DB::table('categories')->get();
        $billing = DB::table('billing')->get();

        return view('admin.billing.categories.index', ['categories' => $categories]);
    }

    public function categories_new(): View
    {
        $billing = DB::table('billing')->get();
        $categories = DB::table('categories')->get();

        return view('admin.billing.categories.new', ['categories' => $categories]);
    }

    public function settings(Request $request)
    {
        $currency = $request->currency;
        $use_categories = $request->use_categories;

        DB::table('billing')->update([
          'currency' => $currency,
          'use_categories' => $use_categories,
        ]);

        return redirect()->back();
    }

    public function categories_store(Request $request)
    {
      $request->validate([
          'name' => 'required',
          'priority' => 'required',
          'description' => 'required',
          'visible' => 'required',
      ]);

      $name = $request->name;
      $priority = $request->priority;
      $description = $request->description;
      $visible = $request->visible;

      DB::table('categories')->insert([
        'name' => $name,
        'priority' => $priority,
        'description' => $description,
        'visible' => $visible,
      ]);

      return redirect(route('admin.billing.categories'));

    }

    public function products(): View
    {
        $billing = DB::table('billing')->get();
        $products = DB::select('select * from products');

        return view('admin.billing.products.index', [
          'products' => $products,
          'billing' => $billing,
        ]);
    }

    public function payoptions(): View
    {
        $billing = DB::table('billing')->get();
        $gateways = DB::table('gateways')->get();

        $paypal = 0;
        $paygol = 0;
        $mollie = 0;

        if (count($gateways) == 0) {
          $gateways == "none";
        }

        return view('admin.billing.gateways.gateways', [
          'gateways' => $gateways,
          'paypal' => $paypal,
          'paygol' => $paygol,
          'mollie' => $mollie,
        ]);
    }

    public function paypal(): View
    {
        $gateways = DB::table('gateways')->get();

        $paypal = 0;

        if (count($gateways) == 0) {
          $gateways == "none";

          return view('admin.billing.gateways.paypal', [
            'gateways' => $gateways,
            'paypal' => $paypal,
            'gateway_paypal' => "none",
          ]);

        } else {

          if (strpos($gateways, 'paypal')) {
            $paypal = 1;

            $gateway_paypal = DB::table('gateways')->where('gateway', '=', 'paypal')->get();

            return view('admin.billing.gateways.paypal', [
              'gateways' => $gateways,
              'paypal' => $paypal,
              'gateway_paypal' => $gateway_paypal,
            ]);

          } else {

            return view('admin.billing.gateways.paypal', [
              'gateways' => $gateways,
              'paypal' => $paypal,
              'gateway_paypal' => "none",
            ]);

          }
        }
    }

    public function paypal_store()
    {
        $email = $_REQUEST['email'];
        $min_basket = $_REQUEST['min_basket'];
        $max_basket = $_REQUEST['max_basket'];
        $percentage_gateway = $_REQUEST['percentage_gateway'];
        $amount_gateway = $_REQUEST['amount_gateway'];

        DB::table('gateways')->insert([
          'email' => $email,
          'gateway' => "paypal",
          'enabled' => 1,
          'api' => "none",
          'private_key' => "none",
          'name' => "none",
          'min_basket' => $min_basket,
          'max_basket' => $max_basket,
          'percentage' => $percentage_gateway,
          'amount' => $amount_gateway,
          'service_id' => "none",
        ]);

        return redirect(route('admin.billing.payoptions'));
    }

    public function paypal_edit()
    {
        $email = $_REQUEST['email'];
        $min_basket = $_REQUEST['min_basket'];
        $max_basket = $_REQUEST['max_basket'];
        $percentage_gateway = $_REQUEST['percentage_gateway'];
        $amount_gateway = $_REQUEST['amount_gateway'];

        DB::table('gateways')->where('gateway', '=', 'paypal')->update([
          'email' => $email,
          'gateway' => "paypal",
          'api' => "none",
          'private_key' => "none",
          'name' => "none",
          'min_basket' => $min_basket,
          'max_basket' => $max_basket,
          'percentage' => $percentage_gateway,
          'amount' => $amount_gateway,
          'service_id' => "none",
        ]);

        return redirect(route('admin.billing.payoptions'));
    }

    public function paypal_activate()
    {
    	DB::table('gateways')->where('gateway', '=', 'paypal')->update([
          'enabled' => 1,
        ]);

        return redirect(route('admin.billing.payoptions'));
    }

    public function paypal_deactivate()
    {
    	DB::table('gateways')->where('gateway', '=', 'paypal')->update([
          'enabled' => 0,
        ]);

        return redirect(route('admin.billing.payoptions'));
    }

    public function paypal_delete()
    {
    	DB::table('gateways')->where('gateway', '=', 'paypal')->delete();

        return redirect(route('admin.billing.payoptions'));
    }

    public function paygol(): View
    {
        $gateways = DB::table('gateways')->get();

        $paygol = 0;

        if (count($gateways) == 0) {
          $gateways == "none";

          return view('admin.billing.gateways.paygol', [
            'gateways' => $gateways,
            'paygol' => $paygol,
            'gateway_paygol' => "none",
          ]);

        } else {

          if (strpos($gateways, 'paygol')) {
            $paygol = 1;

            $gateway_paygol = DB::table('gateways')->where('gateway', '=', 'paygol')->get();

            return view('admin.billing.gateways.paygol', [
              'gateways' => $gateways,
              'paygol' => $paygol,
              'gateway_paygol' => $gateway_paygol,
            ]);

          } else {

            return view('admin.billing.gateways.paygol', [
              'gateways' => $gateways,
              'paygol' => $paygol,
              'gateway_paygol' => "none",
            ]);

          }
        }
    }

    public function paygol_store()
    {
        $service_id = $_REQUEST['service_id'];
        $private_key = $_REQUEST['private_key'];
        $min_basket = $_REQUEST['min_basket'];
        $max_basket = $_REQUEST['max_basket'];
        $percentage_gateway = $_REQUEST['percentage_gateway'];
        $amount_gateway = $_REQUEST['amount_gateway'];

        DB::table('gateways')->insert([
          'email' => "none",
          'gateway' => "paygol",
          'enabled' => 1,
          'api' => "none",
          'private_key' => $private_key,
          'name' => "none",
          'min_basket' => $min_basket,
          'max_basket' => $max_basket,
          'percentage' => $percentage_gateway,
          'amount' => $amount_gateway,
          'service_id' => $service_id,
        ]);

        return redirect(route('admin.billing.payoptions'));
    }

    public function paygol_edit()
    {
        $service_id = $_REQUEST['service_id'];
        $private_key = $_REQUEST['private_key'];
        $min_basket = $_REQUEST['min_basket'];
        $max_basket = $_REQUEST['max_basket'];
        $percentage_gateway = $_REQUEST['percentage_gateway'];
        $amount_gateway = $_REQUEST['amount_gateway'];

        DB::table('gateways')->where('gateway', '=', 'paygol')->update([
          'email' => "none",
          'gateway' => "paygol",
          'api' => "none",
          'private_key' => $private_key,
          'name' => "none",
          'min_basket' => $min_basket,
          'max_basket' => $max_basket,
          'percentage' => $percentage_gateway,
          'amount' => $amount_gateway,
          'service_id' => $service_id,
        ]);

        return redirect(route('admin.billing.payoptions'));
    }

	public function paygol_activate()
    {
    	DB::table('gateways')->where('gateway', '=', 'paygol')->update([
          'enabled' => 1,
        ]);

        return redirect(route('admin.billing.payoptions'));
    }

    public function paygol_deactivate()
    {
    	DB::table('gateways')->where('gateway', '=', 'paygol')->update([
          'enabled' => 0,
        ]);

        return redirect(route('admin.billing.payoptions'));
    }

    public function paygol_delete()
    {
    	DB::table('gateways')->where('gateway', '=', 'paygol')->delete();

        return redirect(route('admin.billing.payoptions'));
    }

    public function mollie(): View
    {
        $gateways = DB::table('gateways')->get();

        $mollie = 0;

        if (count($gateways) == 0) {
          $gateways == "none";

          return view('admin.billing.gateways.mollie', [
            'gateways' => $gateways,
            'mollie' => $mollie,
            'gateway_mollie' => "none",
          ]);

        } else {

          if (strpos($gateways, 'mollie')) {
            $mollie = 1;

            $gateway_mollie = DB::table('gateways')->where('gateway', '=', 'mollie')->get();

            return view('admin.billing.gateways.mollie', [
              'gateways' => $gateways,
              'mollie' => $mollie,
              'gateway_mollie' => $gateway_mollie,
            ]);

          } else {

            return view('admin.billing.gateways.mollie', [
              'gateways' => $gateways,
              'mollie' => $mollie,
              'gateway_mollie' => "none",
            ]);

          }
        }
    }

    public function mollie_store()
    {
        $min_basket = $_REQUEST['min_basket'];
        $api_key = $_REQUEST['api_key'];
        $max_basket = $_REQUEST['max_basket'];
        $percentage_gateway = $_REQUEST['percentage_gateway'];
        $amount_gateway = $_REQUEST['amount_gateway'];

        DB::table('gateways')->insert([
          'email' => "none",
          'gateway' => "mollie",
          'api' => $api_key,
          'private_key' => "none",
          'name' => "none",
          'min_basket' => $min_basket,
          'max_basket' => $max_basket,
          'percentage' => $percentage_gateway,
          'amount' => $amount_gateway,
          'service_id' => "none",
        ]);

        return redirect(route('admin.billing.payoptions'));
    }

    public function mollie_edit()
    {
        $min_basket = $_REQUEST['min_basket'];
        $api_key = $_REQUEST['api_key'];
        $max_basket = $_REQUEST['max_basket'];
        $percentage_gateway = $_REQUEST['percentage_gateway'];
        $amount_gateway = $_REQUEST['amount_gateway'];

        DB::table('gateways')->where('gateway', '=', 'mollie')->update([
          'email' => "none",
          'gateway' => "mollie",
          'enabled' => 1,
          'api' => $api_key,
          'private_key' => "none",
          'name' => "none",
          'min_basket' => $min_basket,
          'max_basket' => $max_basket,
          'percentage' => $percentage_gateway,
          'amount' => $amount_gateway,
          'service_id' => "none",
        ]);

        return redirect(route('admin.billing.payoptions'));
    }

	public function mollie_activate()
    {
    	DB::table('gateways')->where('gateway', '=', 'mollie')->update([
          'enabled' => 1,
        ]);

        return redirect(route('admin.billing.payoptions'));
    }

    public function mollie_deactivate()
    {
    	DB::table('gateways')->where('gateway', '=', 'mollie')->update([
          'enabled' => 0,
        ]);

        return redirect(route('admin.billing.payoptions'));
    }

    public function mollie_delete()
    {
    	DB::table('gateways')->where('gateway', '=', 'mollie')->delete();

        return redirect(route('admin.billing.payoptions'));
    }

    public function products_new(): View
    {
      $nests = DB::select('select * from nests');
      $nodes = DB::select('select * from nodes');
      $eggs = DB::select('select * from eggs');
      $categories = DB::select('select * from categories');

      return view('admin.billing.products.new', [
        'nests' => $nests,
        'eggs' => $eggs,
        'nodes' => $nodes,
        'categories' => $categories,
      ]);
    }

    public function products_store()
    {
      $name = $_REQUEST['name'];
      $price = $_REQUEST['price'];
      $description = $_REQUEST['description'];
      $visible = $_REQUEST['visible'];
      $category = $_REQUEST['category'];
      $egg_id = $_REQUEST['egg_id'];
      $node_id = $_REQUEST['node_id'];
      $memory = $_REQUEST['memory'];
      $cpu = $_REQUEST['cpu'];
      $io = $_REQUEST['io'];
      $disk = $_REQUEST['disk'];
      $database_limit = $_REQUEST['database_limit'];
      $allocation_limit = $_REQUEST['allocation_limit'];

      DB::table('products')->insert([
        'name' => $name,
        'price' => $price,
        'description' => $description,
        'category' => $category,
        'egg_id' => $egg_id,
        'visible' => $visible,
        'node_id' => $node_id,
        'memory' => $memory,
        'cpu' => $cpu,
        'io' => $io,
        'disk' => $disk,
        'database_limit' => $database_limit,
        'allocation_limit' => $allocation_limit
      ]);

      $products = DB::select('select * from products');

      return redirect(route('admin.billing.products'));

    }

    public function products_edit($product)
    {
      $products = DB::table('products')->where('id', '=', $product)->get();
      $eggs = DB::table('eggs')->get();
      $nodes = DB::table('nodes')->get();
      $categories = DB::table('categories')->get();

      if (count($products) == 0) {

          abort(404);

      } else {

      	return view('admin.billing.products.edit', [
          'products' => $products,
          'eggs' => $eggs,
          'nodes' => $nodes,
          'categories' => $categories,
      	]);

      }
    }

    public function products_edit_store($product)
    {

	  $name = $_REQUEST['name'];
      $price = $_REQUEST['price'];
      $description = $_REQUEST['description'];
      $visible = $_REQUEST['visible'];
      $category = $_REQUEST['category'];
      $egg_id = $_REQUEST['egg_id'];
      $node_id = $_REQUEST['node_id'];
      $memory = $_REQUEST['memory'];
      $cpu = $_REQUEST['cpu'];
      $io = $_REQUEST['io'];
      $disk = $_REQUEST['disk'];
      $database_limit = $_REQUEST['database_limit'];
      $allocation_limit = $_REQUEST['allocation_limit'];

      $products = DB::table('products')->where('id', '=', $product)->update([
        'name' => $name,
        'price' => $price,
        'description' => $description,
        'category' => $category,
        'egg_id' => $egg_id,
        'visible' => $visible,
        'node_id' => $node_id,
        'memory' => $memory,
        'cpu' => $cpu,
        'io' => $io,
        'disk' => $disk,
        'database_limit' => $database_limit,
        'allocation_limit' => $allocation_limit
      ]);

      return redirect(route('admin.billing.products'));
    }

    public function promotional_codes() {
        $promotional_codes = DB::table('promotional_codes')->get();

        return view('admin.billing.promotional-codes.index', [
          'promotional_codes' => $promotional_codes
        ]);
    }

    public function promotional_codes_new_post(Request $request) {
        $request->validate([
            'code' => 'required',
            'amount' => 'required|between:0,99.99',
            'percentage' => 'required|between:0,99.99',
            'min_amount' => 'required|between:0,99.99',
            'max_amount' => 'required|between:0,99.99',
        ]);

        if ($request->lasts_till == null) {
            DB::table('promotional_codes')->insert([
              'code' => $request->code,
              'amount' => $request->amount,
              'percentage' => $request->percentage,
              'min_basket' => $request->min_amount,
              'max_basket' => $request->max_amount,
              'lasts_till' => '2018-04-03 12:28:38'
            ]);
        } else {
            DB::table('promotional_codes')->insert([
              'code' => $request->code,
              'amount' => $request->amount,
              'percentage' => $request->percentage,
              'min_basket' => $request->min_amount,
              'max_basket' => $request->max_amount,
              'lasts_till' => $request->lasts_till,
            ]);
        }

        return redirect(route('admin.billing.promotional-codes'));
    }

    public function promotional_codes_new() {
        return view('admin.billing.promotional-codes.new');
    }

    public function promotional_codes_delete($code) {
        $code = DB::table('promotional_codes')->where('id', '=', $code)->delete();

        return redirect(route('admin.billing.promotional-codes'));
    }
}
