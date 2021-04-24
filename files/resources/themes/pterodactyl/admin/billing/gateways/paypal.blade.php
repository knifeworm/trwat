{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}

{{-- Billing System made by Kevko - https://mrkevko.nl --}}
@extends('layouts.admin')

@section('title')
    Billing
@endsection

@section('content-header')
    <h1>Billing<small>Manage your billing settings</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.billing') }}">Billing</a></li>
        <li class="active">Index</li>
    </ol>
@endsection

@section('content')
<div class="row">
  <div class="col-xs-12">
    <div class="nav-tabs-custom nav-tabs-floating">
      <ul class="nav nav-tabs">
        <li><a href="{{ route('admin.billing') }}">General</a></li>
        <li><a href="{{ route('admin.billing.categories') }}">Categories</a></li>
        <li><a href="{{ route('admin.billing.products') }}">Products</a></li>
        <li class="active"><a href="{{ route('admin.billing.payoptions') }}">Gateways</a></li>
        <li><a href="{{ route('admin.billing.payments') }}">Payments</a></li>
        <li><a href="{{ route('admin.billing.statistics') }}">Statistics</a></li>
      </ul>
    </div>
  </div>
  <div class="col-xs-12">
    @if ($paypal == 1)
    <form method="POST" action="{{ route('admin.billing.paypal.edit') }}">
    @else
    <form method="POST" action="{{ route('admin.billing.paypal.store') }}">
    @endif
    <h3>@if ($paypal == 1) Edit @else Set up @endif Paypal</h3>
      <div class="row mt-4">
        <div class="col-md-12">
          <div class="box box-secondary">
            <div class="box-header with-border">
              <h4 class="box-title">About Paypal</h4>
            </div>
            <div class="box-body">
              <p>
              For a quick start up with an industry standard gateway, look no further than PayPal. PayPal only allow payment processing using PayPal accounts and Credit/Debit card, but for most start-up servers that is exactly what is needed.
              <br><br>
              PayPal fees vary depending on volume, but are all very reasonable.
              <br><br>
              Do you need to attract customers that don't use PayPal? Consider Mollie, which supports everything in PayPal, plus regional-specific methods such as iDEAL and paysafecard.
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-md-12">
          <div class="box box-secondary">
            <div class="box-header with-border">
              <h4 class="pull-left box-title">Paypal</h4>
              <div class="pull-right">
                  <span class="label label-success">Recommended</span>
              </div>
            </div>
            <div class="box-body">
              <div class="form-group col-md-4">
                <label class="control-label">Paypal Email</label>
                <div>
                  <input type="text" class="form-control" name="email" placeholder="paypal@yourname.com" @if ($gateway_paypal !== "none") @foreach ($gateway_paypal as $gateway) value="{{ $gateway->email }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Minimum basket value</label>
                <div>
                  <input type="text" class="form-control" name="min_basket" placeholder="0.00" @if ($gateway_paypal !== "none") @foreach ($gateway_paypal as $gateway) value="{{ $gateway->min_basket }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Maximum basket value</label>
                <div>
                  <input type="text" class="form-control" name="max_basket" placeholder="0.00" @if ($gateway_paypal !== "none") @foreach ($gateway_paypal as $gateway) value="{{ $gateway->max_basket }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Percentage for using gateway</label>
                <div>
                  <input type="text" class="form-control" name="percentage_gateway" placeholder="0.00" @if ($gateway_paypal !== "none") @foreach ($gateway_paypal as $gateway) value="{{ $gateway->percentage }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Amount for using gateway</label>
                <div>
                  <input type="text" class="form-control" name="amount_gateway" placeholder="0.00" @if ($gateway_paypal !== "none") @foreach ($gateway_paypal as $gateway) value="{{ $gateway->amount }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-12">
                <p class="text-muted small">If you don't want to charge the customer a fee for using this payment gateway, fill in a 0 for the <code>Percentage</code> and/or the <code>Amount</code>.</p>
              </div>
            </div>
            <div class="box-footer with-border">
            @csrf
            @if ($paypal == 1)
            <a class="btn btn-sm btn-danger" href="{{ route('admin.billing.paypal.delete') }}">Delete Gateway</a>
            @if ($gateway_paypal !== "none")
              @foreach ($gateway_paypal as $gateway)
                @if ($gateway->enabled == 1)
                <a class="btn btn-sm btn-warning" href="{{ route('admin.billing.paypal.deactivate') }}">Set as inactive</a>
                @else
                <a class="btn btn-sm btn-success" href="{{ route('admin.billing.paypal.activate') }}">Set as active</a>
                @endif
              @endforeach
            @endif
            <button type="submit" class="btn btn-sm btn-success">Save Gateway</button>
            @else
            <button type="submit" class="btn btn-sm btn-success">Create Gateway</button>
            @endif
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
