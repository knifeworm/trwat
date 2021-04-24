<?php

namespace Pterodactyl\Http\Controllers\Base;

use Illuminate\Http\Request;
use Pterodactyl\Services\Servers\ServerCreationService;
use Pterodactyl\Models\Nest;
use Pterodactyl\Models\Node;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Http\Controllers\Controller;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use Mollie\Laravel\Facades\Mollie;
use Stripe\Stripe;
use App\User;
use Stripe\Customer;
use Stripe\Charge;
use Validator;
use Session;
use Carbon;

class BillingController extends Controller
{

    private $creationService;


    private const COUNTRIES = array
    (
        'AF' => 'Afghanistan',
        'AX' => 'Aland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua And Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia And Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo',
        'CD' => 'Congo, Democratic Republic',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Cote D\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands (Malvinas)',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island & Mcdonald Islands',
        'VA' => 'Holy See (Vatican City State)',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran, Islamic Republic Of',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle Of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KR' => 'Korea',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Lao People\'s Democratic Republic',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia, Federated States Of',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'AN' => 'Netherlands Antilles',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory, Occupied',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts And Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin',
        'PM' => 'Saint Pierre And Miquelon',
        'VC' => 'Saint Vincent And Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome And Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia And Sandwich Isl.',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard And Jan Mayen',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad And Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks And Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'UM' => 'United States Outlying Islands',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Viet Nam',
        'VG' => 'Virgin Islands, British',
        'VI' => 'Virgin Islands, U.S.',
        'WF' => 'Wallis And Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
    );

    public function __construct(ServerCreationService $creationService)
    {
        $this->creationService = $creationService;
    }

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
            return 'active';
        }

        if ($data->code == 'ka-02') {
            return false;
        }
    }

    public function index(Request $request)
    {
      return view('base.billing')
          ->with('user', $request->user())->with('countries', self::COUNTRIES)
          ->with('invoices', $request->user()->invoices()->orderBy('id', 'desc')->paginate(5))
          ->with('billing', DB::table('billing')->get());
    }

    public function billing(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|min:3|max:255',
            'last_name' => 'required|string|min:3|max:255',
            'address' => 'required|string|min:3|max:255',
            'city' => 'required|string|min:3|max:255',
            'country' => 'required|string|max:2|in:'.implode(',', array_keys(self::COUNTRIES)),
            'zip' => 'required|string|min:3|max:6',
        ]);
        $user = $request->user();
        $user->billing_first_name = $request->first_name;
        $user->billing_last_name = $request->last_name;
        $user->billing_address = $request->address;
        $user->billing_city = $request->city;
        $user->billing_country = $request->country;
        $user->billing_zip = $request->zip;
        $user->save();
        return redirect()->back();
    }

    private function validateBilling($user)
    {
        if (!$user->billing_first_name) return false;
        if (!$user->billing_last_name) return false;
        if (!$user->billing_address) return false;
        if (!$user->billing_city) return false;
        if (!$user->billing_country) return false;
        if (!$user->billing_zip) return false;
        return true;
    }

    public function link(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5|max:1000',
            'card_token' => 'required',
        ]);
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $user = $request->user();
        if (!$this->validateBilling($user)) {
            return redirect()->back()->withErrors('You need to fill up your billing info before making any payments.');}
        try {
            $customer = Customer::create([
                'email' => $user->email,
                'source'  => $request->card_token
            ]);
            $charge = Charge::create([
                'customer' => $customer->id,
                'amount'   => $request->amount * 100,
                'currency' => strtolower(trans('currency.code'))
            ]);
            if ($charge->paid) {
                $user->stripe_card_brand = $request->card_brand;
                $user->stripe_card_last4 = $request->card_last4;
                $user->stripe_customer_id = $customer->id;
                $user->addBalance($request->amount);
            } else {
                return redirect()->back()->withErrors('Sorry, your credit union rejected our request.');}
        } catch (\Exception $ex) {}
        return redirect()->back();
    }

    public function unlink(Request $request)
    {
        $user = $request->user();
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        try {
            Customer::retrieve($user->stripe_customer_id)->delete();
        } catch (\Exception $ex) {}
        $user->stripe_customer_id = null;
        $user->stripe_card_brand = null;
        $user->stripe_card_last4 = null;
        $user->save();
        return redirect()->back();
    }

    public function invoicePdf(Request $request)
    {
        $invoice = $request->user()->invoices()->find($request->id);
        if (!$invoice) return abort(404);
        return $invoice->downloadPdf();
    }

    private function getPaypalApiContext()
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                env('PAYPAL_CLIENT_ID'),
                env('PAYPAL_CLIENT_SECRET'),
                env('PAYPAL_CLIENT_ENV')
            )
        );

        if (env('PAYPAL_MODE') == 'live') {
            $apiContext->setConfig(
              array(
                'mode' => 'live',
                'log.LogEnabled' => true,
                'log.FileName' => 'PayPal.log',
                'log.LogLevel' => 'FINE'
              )
            );
        }

        return $apiContext;
    }

    public function paypal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:2|max:1000',
        ]);
        if (!$this->validateBilling($request->user())) {
            return redirect()->back()->withErrors('You need to fill up your billing info before making any payments.');}
        $billing = DB::table('billing')->get();
        foreach ($billing as $setting) {
            $apiContext = $this->getPaypalApiContext();
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');
            $amount = new Amount();
            $amount->setTotal($request->amount);
            if ($setting->currency = 'euro') {
                $amount->setCurrency('EUR');}
            if ($setting->currency = 'dollar') {
                $amount->setCurrency('USD');}
            if ($setting->currency = 'pound') {
                $amount->setCurrency('GBP');}
            $transaction = new Transaction();
            $transaction->setAmount($amount);
            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(route('account.billing.paypal.callback'));
            $redirectUrls->setCancelUrl(route('account.billing.paypal.callback'));
            $payment = new Payment();
            $payment->setIntent('sale');
            $payment->setPayer($payer);
            $payment->setTransactions(array($transaction));
            $payment->setRedirectUrls($redirectUrls);
            try {
                $payment->create($apiContext);
                $links = array_filter($payment->links, function($link) {
                    return $link->rel == 'approval_url';});
                $link = reset($links)->getHref();
                $meta[$payment->id] = $request->amount;
                session()->put('paypal_meta', $meta);
                return redirect($link);
            } catch (\Exception $ex) { }
            return redirect()->back()->withErrors('Something went wrong with getting the Paypal Link, try again.'. $ex);
        }
    }

    public function paypalCallback(Request $request) {
        $apiContext = $this->getPaypalApiContext();

        if (!$request->has('paymentId') || !session()->has("paypal_meta.$request->paymentId")) {
            return redirect()->route('account.billing')
                ->withErrors('Something went wrong during the paypal transaction!');
        }
        $user = $request->user();
        $amount = $request->session()->pull("paypal_meta.$request->paymentId");
        $apiContext = $this->getPaypalApiContext();

        $payment = Payment::get($request->paymentId, $apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->PayerID);

        $result = $payment->execute($execution, $apiContext);
        if ($result->getState() == 'approved') {
            $newAmount = $request->user()->balance + $amount;
            DB::table('users')->where('id', '=', $request->user()->id)->update([
              'balance' => $newAmount,
            ]);

            DB::table('invoices')->insert([
              'amount' => $amount,
              'reason' => 'Top up Credit',
              'user_id' => $request->user()->id,
              'billing_first_name' => $request->user()->billing_first_name,
              'billing_last_name' => $request->user()->billing_last_name,
              'billing_address' => $request->user()->billing_address,
              'billing_city' => $request->user()->billing_city,
              'billing_country' => $request->user()->billing_country,
              'billing_zip' => $request->user()->billing_zip
            ]);
        }
        return redirect()->route('account.billing');
    }

    public function store()
    {
        $categories = DB::table('categories')->orderBy('priority', 'desc')->get();
        $billings = DB::table('billing')->get();
        $products = DB::table('products')->get();

        $useCategories = 1;

        foreach ($billings as $billing) {
            if ($billing->use_categories == 0) {
                $useCategories = 0;
            }
        }

        $billing = DB::table('billing')->get();

        $total_price = 0;

        return view('base.store', [
          'categories' => $categories,
          'total_price' => $total_price,
          'use_categories' => $useCategories,
          'products' => $products,
          'billing' => $billing,
        ]);
    }

    public function checkout()
    {
        $categories = DB::select('select * from categories');
        $paypal = DB::table('gateways')->where('gateway', '=', 'paypal')->get();
        $billing = DB::table('billing')->get();
        $mollie = DB::table('gateways')->where('gateway', '=', 'mollie')->get();
        $paygol = DB::table('gateways')->where('gateway', '=', 'paygol')->get();

        $hasPaypal = 0;
        $hasMollie = 0;
        $hasPaygol = 0;
        $total_price = 0;

        if (Session::has('cart')) {
            if (count($paypal) !== 0) {
                $hasPaypal = 1;
            }

            if (count($paygol) !== 0) {
                $hasPaygol = 1;
            }

            if (count($mollie) !== 0) {
                $hasMollie = 1;
            }

            return view('base.checkout', [
                'categories' => $categories,
                'total_price' => $total_price,
                'hasMollie' => $hasMollie,
                'hasPaygol' => $hasPaygol,
                'hasPaypal' => $hasPaypal,
                'paypal' => $paypal,
                'mollie' => $mollie,
                'paygol' => $paygol,
                'billing' => $billing,
            ]);
        } else {
            return redirect(route('store'));
        }
    }

    public function category($category)
    {
        $products = DB::table('products')->where('category', '=', $category)->get();
        $total_price = 0;
        $billing = DB::table('billing')->get();

        return view('base.category', [
          'products' => $products,
          'total_price' => $total_price,
          'billing' => $billing
        ]);
    }

    public function add_product($product)
    {
        $products = DB::table('products')->where('id', '=', $product)->get();
        if (count($products) == 0) {
            abort(404);
        } else {
            foreach ($products as $product) {
                $itemArray = array($product->id=>array(
                    'name' => $product->name,
                    'code' => $product->id,
                    'quantity' => '1',
                    'price' => $product->price,
                ));
                Session::push('cart', $itemArray);
            }

            return redirect(route('store'));
        }
    }

    public function empty_cart()
    {
        Session::forget('cart');
        Session::forget('cart-extend');

        return redirect(route('index'));
    }

    public function buy(Request $request) {
        $total_price = 0;

        if (!Session::get('cart')) {
            abort(404);
        } else {

            $count = 0;

            foreach (Session::get('cart') as $cart) {
                $total_price = $total_price + end($cart)['price'];
                if (end($cart)['name'] !== 'Promotional Code') {
                    $count = $count + 1;
                }
            }

            if (auth()->user()->balance < $total_price) {
              return redirect()->back()->withErrors('You don\'t have enough founds on your account to start this server.');}

            DB::table('users')->where('id', '=', auth()->user()->id)->update([
                'balance' => auth()->user()->balance - $total_price
            ]);

            $promotional = 0;
            $promotionalAmount = 0;

            foreach (Session::get('cart') as $cart) {
                if (end($cart)['name'] == 'Promotional Code') {
                    $promotional = 1;
                    $promotionalAmount = end($cart)['price'];
                }
            }

            foreach (Session::get('cart') as $cart) {
                if (end($cart)['name'] !== 'Promotional Code') {
                    $product_id = end($cart)['code'];
                    $products = DB::table('products')->where('id', '=', $product_id)->get();
                    foreach ($products as $product) {
                        $eggs = DB::table('eggs')->where('id', '=', $product->egg_id)->get();
                        foreach ($eggs as $egg) {
                            $nests = DB::table('nests')->where('id', '=', $egg->nest_id)->get();
                            foreach ($nests as $nest) {
                                if (!$nest || !$egg) return redirect()->back();

                                $allocation = $this->getAllocationId($product->memory);
                                if ($promotional == 1) {
                                    $cost = ($promotionalAmount / $count) + $product->price;
                                } elseif ($promotional == 0) {
                                    $cost = $product->price;
                                }

                                if (!$allocation) return redirect()->back()->withErrors('We are sorry but at the moment there is no space left on our servers.');
                                if ($request->user()->balance < $cost) return redirect()->back()->withErrors('You don\'t have enough founds on your account to start this server.');

                                $data = [
                                    'name' => $request->user()->username,
                                    'owner_id' => $request->user()->id,
                                    'egg_id' => $egg->id,
                                    'nest_id' => $nest->id,
                                    'allocation_id' => $allocation,
                                    'environment' => [],
                                    'memory' => $product->memory,
                                    'disk' => $product->disk,
                                    'cpu' => $product->cpu,
                                    'swap' => 0,
                                    'io' => $product->io,
                                    'database_limit' => $product->database_limit,
                                    'allocation_limit' => $product->allocation_limit,
                                    'image' => $egg->docker_image,
                                    'startup' => $egg->startup,
                                    'start_on_completion' => true,
                                ];

                                foreach (DB::table('egg_variables')->where('user_editable', 1)->get() as $var) {
                                    $key = "v{$nest->id}-{$egg->id}-{$var->env_variable}";
                                    $data['environment'][$var->env_variable] = $request->get($key, $var->default_value);
                                }

                                $server = $this->creationService->handle($data);
                                $server->monthly_cost = $product->price;
                                $server->save();

                                DB::table('invoices')->insert([
                                  'amount' => $cost,
                                  'reason' => $product->name,
                                  'user_id' => $request->user()->id,
                                  'billing_first_name' => $request->user()->billing_first_name,
                                  'billing_last_name' => $request->user()->billing_last_name,
                                  'billing_address' => $request->user()->billing_address,
                                  'billing_city' => $request->user()->billing_city,
                                  'billing_country' => $request->user()->billing_country,
                                  'billing_zip' => $request->user()->billing_zip
                                ]);

                                if ($promotional == 1) {
                                    foreach (Session::get('cart') as $cart) {
                                        if (end($cart)['name'] == 'Promotional Code') {
                                            $promotional_codess = DB::table('promotional_codes')->where('id', '=', end($cart)['code'])->get();

                                            foreach($promotional_codess as $promotional_codes) {
                                                $new_uses = $promotional_codes->uses + 1;

                                                DB::table('promotional_codes')->where('id', '=', end($cart)['code'])->update([
                                                  'uses' => $new_uses,
                                                ]);
                                            }
                                        }
                                    }
                                }

                                $servers = DB::table('servers')->get();

                                foreach ($servers as $server) {
                                    $renewal_date = date("Y-m-d h:m:s", strtotime(date("Y-m-d h:m:s") ." +1 month" ));

                                    DB::table('servers')->where('created_at', '>=', Carbon::now()->subMinutes(1)->toDateTimeString())->update([
                                        'renewal_date' => $renewal_date
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
        Session::forget('cart');
        return redirect()->route('index');
    }

    public function promotional(Request $request) {
        $total_price = 0;

        foreach (Session::get('cart') as $cart) {
            if (end($cart)['name'] == 'Promotional Code') {
                return redirect()->back()->withErrors('You are already using a promotional code. Clear your basket to use another one.');}
            if (end($cart)['name'] !== 'Promotional Code') {
                $total_price = $total_price + end($cart)['price'];}
        }

        $input = $request->code;
        $codes = DB::table('promotional_codes')->get();

        foreach ($codes as $code) {
            if ($code->code == $input) {
                if ($total_price < $code->min_basket) {
                    return redirect()->back()->withErrors('To use this promotion code, your basket has to have a minimum value of $'. $code->min_basket);}

                if ($code->max_basket !== 0.00) {
                    if ($total_price > $code->max_basket) {
                        return redirect()->back()->withErrors('To use this promotion code, your basket is allowed to have a value of $'. $code->max_basket);}
                }

                if (now() > $code->lasts_till) {
                    if ($code->lasts_till !== '2018-04-03 12:28:38') {
                        return redirect()->back()->withErrors('This coupon code has expired');}}

                $total_price = 0;

                foreach (Session::get('cart') as $cart) {
                    $total_price = $total_price + end($cart)['price'];
                }

                $total_price = (($total_price / 100) * ($code->percentage) - ($code->amount * -1)) * -1;

                $itemArray = array($code->id=>array(
                    'name' => 'Promotional Code',
                    'code' => $code->id,
                    'quantity' => '1',
                    'price' => $total_price,
                ));
                Session::push('cart', $itemArray);
            }
        }

        return redirect()->back();
    }

    private function getAllocationId($memory = 0, $attempt = 0)
    {
        if ($attempt > 6) return null;
        $node = Node::where('nodes.public', true)->where('nodes.maintenance_mode', false)
            ->whereRaw('nodes.memory - ? > (SELECT IFNULL(SUM(servers.memory), 0) FROM servers WHERE servers.node_id = nodes.id)', [$memory])
            ->whereRaw('nodes.disk > (SELECT IFNULL(SUM(servers.disk), 0) FROM servers WHERE servers.node_id = nodes.id)')->inRandomOrder()->first();
        if (!$node) return false;
        $allocation = $node->allocations()->where('server_id', null)->inRandomOrder()->first();
        if (!$allocation) return getAllocationId($memory, $attempt+1);
        return $allocation->id;
    }
}
