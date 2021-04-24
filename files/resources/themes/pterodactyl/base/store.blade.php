{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}

{{-- Billing System made by Kevko - https://mrkevko.nl --}}
@extends('layouts.master')

@section('title')
    Store
@endsection

@section('content-header')
    <h1>Store<small>Purchase your favorite game server</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('index') }}">@lang('strings.home')</a></li>
        <li class="active">Store</li>
    </ol>
@endsection

@section('content')
@if (Session::has('cart'))
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Checkout</h4><small> You are having products in your shopping cart, do you want to check out?</small>
                <a class="pull-right btn btn-sm btn-success" href="{{ route('checkout') }}">Checkout</a>
            </div>
        </div>
    </div>
</div>
@endif
<div class="row">
  @if ($use_categories == 1)
  	@foreach ($categories as $category)
      @if ($category->visible == 1)
        <div class="col-md-3">
          <div class="box">
            <div class="box-header">
            	<h3 class="box-title">{{ $category->name }}</h3>
            </div>
            <div class="box-body">
            	<p>{{ $category->description }}</p>
            </div>
            <div class="box-footer">
            	<a style="display:inline-block;" class="pull-right btn btn-sm btn-primary" href="{{ route('store.view.category', $category->id) }}"> View Products</a>
            </div>
          </div>
        </div>
      @endif
    @endforeach
  @else
    @foreach ($billing as $setting)
        @foreach ($products as $product)
          <form method="POST" action="{{ route('store.add.product', $product->id) }}">
              <div class="col-md-3">
                  <div class="box">
                      <div class="box-header">
                      	<h3 class="box-title">{{ $product->name }}</h3>
                      </div>
                      <div class="box-body">
                      	<p>{{ $product->description }}</p>
                      </div>
                      <div class="box-footer">
                        <b><p style="display:inline-block;">&{{ $setting->currency }};{{ $product->price }}.00</p></b>
                          @csrf
                      	<button type="submit" style="display:inline-block;" class="pull-right btn btn-sm btn-success"><i class="fa fa-shopping-cart"></i> Purchase</button>
                      </div>
                  </div>
                </div>
            </form>
          @endforeach
      @endforeach
    @endif
</div>
@endsection
