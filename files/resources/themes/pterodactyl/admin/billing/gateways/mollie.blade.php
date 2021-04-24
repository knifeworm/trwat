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
    @if ($mollie == 1)
    <form method="POST" action="{{ route('admin.billing.mollie.edit') }}">
    @else
    <form method="POST" action="{{ route('admin.billing.mollie.store') }}">
    @endif
    <h3>@if ($mollie == 1) Edit @else Set up @endif Mollie</h3>
      <div class="row mt-4">
        <div class="col-md-12">
          <div class="box box-secondary">
            <div class="box-header with-border">
              <h4 class="box-title">About Mollie</h4>
            </div>
            <div class="box-body">
              <p>If you are looking to support a wide range of European gateways then Mollie is perfect. Mollie are a professional organisation who offer a wide range of payout options, and an even wider range of gateways from iDEAL, SOFORT, paysafecard and Bancontact to more common credit/debit cards
              <br><br>
              Mollie's fees are very fair, with no minimum costs or contracts, with credit/debit cards as low as 1.8% + €0.25 and a flat €0.29 for iDEAL</p>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-md-12">
          <div class="box box-secondary">
            <div class="box-header with-border">
              <h4 class="pull-left box-title">Mollie</h4>
              <div class="pull-right">
                  <span class="label label-success">Recommended</span>
              </div>
            </div>
            <div class="box-body">
              <div class="form-group col-md-4">
                <label class="control-label">Mollie API key</label>
                <div>
                  <input type="text" class="form-control" name="api_key" placeholder="API key" @if ($gateway_mollie !== "none") @foreach ($gateway_mollie as $gateway) value="{{ $gateway->api }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Minimum basket value</label>
                <div>
                  <input type="text" class="form-control" name="min_basket" placeholder="0.00" @if ($gateway_mollie !== "none") @foreach ($gateway_mollie as $gateway) value="{{ $gateway->min_basket }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Maximum basket value</label>
                <div>
                  <input type="text" class="form-control" name="max_basket" placeholder="0.00" @if ($gateway_mollie !== "none") @foreach ($gateway_mollie as $gateway) value="{{ $gateway->max_basket }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Percentage for using gateway</label>
                <div>
                  <input type="text" class="form-control" name="percentage_gateway" placeholder="0.00" @if ($gateway_mollie !== "none") @foreach ($gateway_mollie as $gateway) value="{{ $gateway->percentage }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Amount for using gateway</label>
                <div>
                  <input type="text" class="form-control" name="amount_gateway" placeholder="0.00" @if ($gateway_mollie !== "none") @foreach ($gateway_mollie as $gateway) value="{{ $gateway->amount }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-12">
                <p class="text-muted small">If you don't want to charge the customer a fee for using this payment gateway, fill in a 0 for the <code>Percentage</code> and/or the <code>Amount</code>.</p>
              </div>
            </div>
            <div class="box-footer with-border">
            @csrf
            @if ($mollie == 1)
            <a class="btn btn-sm btn-danger" href="{{ route('admin.billing.mollie.delete') }}">Delete Gateway</a>
            @if ($gateway_mollie !== "none")
              @foreach ($gateway_mollie as $gateway)
                @if ($gateway->enabled == 1)
                <a class="btn btn-sm btn-warning" href="{{ route('admin.billing.mollie.deactivate') }}">Set as inactive</a>
                @else
                <a class="btn btn-sm btn-success" href="{{ route('admin.billing.mollie.activate') }}">Set as active</a>
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
