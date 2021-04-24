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
<a class="btn btn-sm btn-primary" href="{{ route('store') }}" style="margin-top: 15px; margin-bottom: 15px;">Continue Shopping</a>
  <form method="POST" action="{{ route('account.billing.buy') }}">
  <div class="row">
  	<div class="col-md-9">
          <div class="box">
              <div class="box-header with-border">
                  <h3 class="box-title">TOS</h3>
              </div>
              <div class="box-body">
                  <p>By purchasing you automatically agree with our <a href="#tos" onclick="tos()">terms of service</a></p>
                  <div style="display: none;" id="tos">
                      <br><br>
                      <p>@foreach ($billing as $setting) @php echo htmlspecialchars_decode($setting->tos) @endphp @endforeach</p>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="box">
              <div class="box-header with-border">
                  <h3 class="box-title">Summary</h3>
              </div>
              <div class="box-body table-responsive no-padding">
                  <div class="col-md-12">
                      <table class="table table-hover">
                          <tbody>
                              <tr>
                                  <th></th>
                                  <th></th>
                              </tr>
                              @foreach ($billing as $setting)
                                  @foreach (Session::get('cart') as $cart)
                                      @if (strpos('Extend Server', end($cart)['name']) == false)
                                      <tr>
                                          <td><b>@php echo end($cart)['name']; @endphp</b></td>
                                          <td>&{{ $setting->currency }};@php echo end($cart)['price']; $total_price = $total_price + end($cart)['price']; @endphp</td>
                                      </tr>
                                      @endif
                                  @endforeach
                                  <tr>
                                      <td></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td><b>Subtotal:</b></td>
                                      <td>&{{ $setting->currency }};{{ $total_price }}</td>
                                  </tr>
                                  <tr>
                                      <td><b>Fees:</b></td>
                                      <td>&{{ $setting->currency }};0</td>
                                  </tr>
                                  <tr>
                                      <td><b>Total:</b></td>
                                      <td>&{{ $setting->currency }};{{ $total_price }}</td>
                                  </tr>
                              @endforeach
                          </tbody>
                      </table>
                  </div>
              </div>
              <div class="box-footer">
                  @csrf
                  <a class="btn btn-sm btn-danger" href="{{ route('store.empty.cart') }}"><i class="fa fa-trash"></i> Empty Cart</a>
                  <button class="btn btn-sm btn-success" type="submit"><i class="fa fa-shopping-cart"></i> Checkout</button>
              </div>
          </div>
      </div>
    </form>
    <div class="col-md-9"></div>
  	<div class="col-md-3">
          <div class="box">
              <div class="box-header with-border">
                  <h3 class="box-title">Promotional Code</h3>
              </div>
              <form method="POST" action="{{ route('store.promotional') }}">
                <div class="box-body">
                	<div class="col-md-12">
    	            	<div class="row">
    		            	<div class="col-md-12">
    			            	<div class="form-group">
    			                    <label class="form-label">Promotional Code</label>
    			                    <input type="text" name="code" class="form-control" value="">
    			                </div>
                          @csrf
    			                <button type="submit" class="btn btn-sm btn-warning">Validate Code</button>
    			            </div>
    			        </div>
              </form>
  			    </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    <script>
    function tos() {
        document.getElementById('tos').style.display = 'inline';
    }
    </script>
@endsection
