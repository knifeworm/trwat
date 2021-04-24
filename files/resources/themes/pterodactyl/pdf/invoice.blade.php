<!DOCTYPE HTML>
<html>
<head>
    <title>Invoice - #{{ $id }}</title>
    <meta charset="utf-8">
    <style>
        body {
          font-family: sans-serif;
        }

        h1 {
          margin-top: 50px;
          margin-bottom: 50px;
        }

        table {
          width: 100%;
        }

        .text-right {
          text-align: right;
        }

        .text-center {
          text-align: center;
        }

        .text-center {
          text-align: center;
        }

        .items {
          margin-top: 50px;
        }

        .items tr:first-child {
          background-color: #bfcbff;
          padding-left: 50px;
        }
    </style>
<head>
<body>
  <h1 class="text-center">{{ config('app.name', 'Pterodactyl') }}</h1>
  <table>
    <tr>
      <td>
        {{ config('app.name', 'Pterodactyl') }}<br />
        Adress,<br />
        City, Postal Code<br />
        Legal Number<br />
      </td>
      <td class="text-right">
        <b>{{ $billing_first_name }} {{ $billing_last_name }}</b><br />{{ $billing_address }},<br />
        {{ $billing_city }} {{ $billing_zip }}, {{ $billing_country }}
      </td>
    </tr>
  </table>
  <table class="items">
    <tr>
      <th style="width: 90%">Item Name</th>
      <th style="min-width: 150px; text-align: center;">Price</th>
    </tr>
    <tr>
      <td>{{ $reason }}</td>
      <td class="text-center">@php $billing = DB::table('billing')->get(); foreach ($billing as $setting) {echo '&'. $setting->currency;}@endphp; {{ number_format($amount, 2) }}</td>
    </tr>
  </table>
  <hr />
  Payment ID: #{{ $id }}<br />
  Payment amount: @php $billing = DB::table('billing')->get(); foreach ($billing as $setting) {echo '&'. $setting->currency;}@endphp; {{ number_format($amount, 2) }}<br />
  Payment date: {{ date(__('d-m-Y'), strtotime($created_at)) }}<br />
</body>
</html>
