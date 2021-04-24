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
      </ul>
    </div>
  </div>
  <div class="col-xs-12">
    <h3>Payment Gateways</h3>
    <div class="row mt-4">
      <div class="col-md-4">
        <div class="box box-secondary">
          <div class="box-header with-border">
            <h4 class="pull-left box-title">Paypal</h4>
            <div class="pull-right">
              @foreach ($gateways as $gateway)
                @if ($gateway->gateway == "paypal")
                  @if ($gateway->enabled == 0)
                    <span class="label label-warning">Inactive</span>
                  @elseif ($gateway->enabled == 1)
                    <span class="label label-success">Active</span>
                  @endif
                @endif
              @endforeach
              @if (strpos($gateways, "paypal") == false)
                <span class="label label-success">Recommended</span>
              @endif
            </div>
          </div>
          <div class="box-body">
            <img src="https://cdn.pixabay.com/photo/2015/05/26/09/37/paypal-784404_960_720.png" style="margin-left:36%;width:150px;">
            <p style="margin-top: 5px;">The latest PayPal integration allowing you to access a wider range of features</p>
          </div>
          <div class="box-footer with-border">
            @if (strpos($gateways, "paypal") == false)
            <a class="btn btn-sm btn-success pull-right" href="{{ route('admin.billing.payoptions.paypal') }}">Setup Gateway</a>
            @else
            <a class="btn btn-sm btn-success pull-right" href="{{ route('admin.billing.payoptions.paypal') }}">Edit Gateway</a>
            @endif
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="box box-secondary">
          <div class="box-header with-border">
            <h4 class="pull-left box-title">Paygol</h4>
            <div class="pull-right">
              @foreach ($gateways as $gateway)
                @if ($gateway->gateway == "paygol")
                  @if ($gateway->enabled == 0)
                    <span class="label label-warning">Inactive</span>
                  @elseif ($gateway->enabled == 1)
                    <span class="label label-success">Active</span>
                  @endif
                @endif
              @endforeach
              @if (strpos($gateways, "paygol") == false)
                <span class="label label-success">Recommended</span>
              @endif
            </div>
          </div>
          <div class="box-body">
            <img src="https://www.paygol.com/site/resources/images/header_logo-paygol.png" style="margin-top: 10px; margin-left: 37%;">
            <p style="margin-top: 15px;">Paysafecard, Credit and Debit Cards, Local European Payments and Latin American Payments</p>
          </div>
          <div class="box-footer with-border">
            @if (strpos($gateways, "paygol") == false)
            <a class="btn btn-sm btn-success pull-right" href="{{ route('admin.billing.payoptions.paygol') }}">Setup Gateway</a>
            @else
            <a class="btn btn-sm btn-success pull-right" href="{{ route('admin.billing.payoptions.paygol') }}">Edit Gateway</a>
            @endif
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="box box-secondary">
          <div class="box-header with-border">
            <h4 class="pull-left box-title">Mollie</h4>
            <div class="pull-right">
                @foreach ($gateways as $gateway)
                  @if ($gateway->gateway == "mollie")
                    @if ($gateway->enabled == 0)
                      <span class="label label-warning">Inactive</span>
                    @elseif ($gateway->enabled == 1)
                      <span class="label label-success">Active</span>
                    @endif
                  @endif
                @endforeach
                @if (strpos($gateways, "mollie") == false)
                  <span class="label label-success">Recommended</span>
                @endif
            </div>
          </div>
          <div class="box-body">
            <img src="https://d2zr9w65gdacs9.cloudfront.net/20506/mollie-homerun-logo21563263813logo.png" style="margin-top:10px;margin-left:36%;width:150px;">
            <p style="margin-top: 15px;">Credit Cards, SOFORT, iDEAL, PaySafeCard, Bancontact, Paypal, Bitcoin</p>
          </div>
          <div class="box-footer with-border">
            @if (strpos($gateways, "mollie") == false)
            <a class="btn btn-sm btn-success pull-right" href="{{ route('admin.billing.payoptions.mollie') }}">Setup Gateway</a>
            @else
            <a class="btn btn-sm btn-success pull-right" href="{{ route('admin.billing.payoptions.mollie') }}">Edit Gateway</a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
