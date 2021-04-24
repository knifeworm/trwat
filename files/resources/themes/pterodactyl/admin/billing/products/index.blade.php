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
        <li class="active"><a href="{{ route('admin.billing.products') }}">Products</a></li>
        <li><a href="{{ route('admin.billing.promotional-codes') }}">Promotional Codes</a></li>
        <li><a href="{{ route('admin.billing.tos') }}">TOS</a></li>
      </ul>
    </div>
  </div>
  <div class="col-xs-12">
    <div class="box box-secondary">
      <div class="box-header">
        <h3 class="box-title">Products</h3>
        <a class="btn btn-sm btn-primary pull-right" href="{{ route('admin.billing.products.new') }}">Create New</a>
      </div>
      <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tbody>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
                <th class="text-center hidden-sm hidden-xs">Memory</th>
                <th class="text-center hidden-sm hidden-xs">CPU</th>
                <th class="text-center hidden-sm hidden-xs">Disk</th>
                <th class="text-center hidden-sm hidden-xs">Visisble</th>
              </tr>
              @foreach ($billing as $setting)
                  @foreach ($products as $product)
                    <tr>
                      <td>{{ $product->id }}</td>
                      <td><a href="{{ route('admin.billing.product.edit', $product->id) }}">{{ $product->name }}</a></td>
                      <td>&{{ $setting->currency }};{{ $product->price }}</td>
                      <td>{{ $product->description }}</td>
                      <td class="text-center">{{ $product->memory }}MB</td>
                      <td class="text-center">{{ $product->cpu }}%</td>
                      <td class="text-center">{{ $product->disk }}MB</td>
                      <td class="text-center">@if ($product->visible == 1)<i class="fa fa-eye text-success text-center"></i>@else<i class="fa fa-eye-slash text-danger text-center"></i>@endif</td>
                    </tr>
                  @endforeach
              @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
