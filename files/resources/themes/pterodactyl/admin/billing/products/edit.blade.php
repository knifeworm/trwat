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
  @foreach ($products as $product)
  <form method="POST" action="{{ route('admin.billing.product.edit.store', $product->id) }}">
  @endforeach
    <div class="col-xs-12">
      <div class="box box-secondary">
        <div class="box-header with-border">
          <h3 class="box-title">Edit: @foreach ($products as $product) {{ $product->name }} @endforeach</h3>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="form-group col-md-4">
                <label class="control-label">Product Name</label>
                <div>
                  <input type="text" class="form-control" name="name" value="@foreach ($products as $product) {{ $product->name }} @endforeach">
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Price</label>
                <div>
                  <input type="text" class="form-control" name="price" value="@foreach ($products as $product) {{ $product->price }} @endforeach">
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Description</label>
                <div>
                  <input type="text" class="form-control" name="description" value="@foreach ($products as $product) {{ $product->description }} @endforeach">
                </div>
              </div>
              <div class="form-group col-md-6">
                <label class="control-label">Category</label>
                <div>
                  <select class="form-control" name="category">
                  @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @foreach ($products as $product) @if ($product->category == $category->id) selected @endif @endforeach>({{ $category->id }}) {{ $category->name }}</option>
                  @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group col-md-6">
                <label class="control-label">Visible</label>
                <select name="visible" class="form-control">
                  <option value="0" @foreach ($products as $product) @if ($product->visible == 0) selected @endif @endforeach>No</option>
                  <option value="1" @foreach ($products as $product) @if ($product->visible == 1) selected @endif @endforeach>Yes</option>
                </select>
              </div>
            </div>
          </div>
          <div class="box-footer">
            <p class="text-muted small">You can change your currency in the general tab of the billing page. If you want to use or disable categories, you can do this in the general tab of the billing page.</p><p>
          </div>
        </div>
      </div>
    <div class="col-xs-12">
      <div class="box box-secondary">
        <div class="box-header with-border">
          <h3 class="box-title">Server Settings</h3>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="form-group col-md-4">
                <label>Egg</label>
                <select name="egg_id" class="form-control">
                  @foreach ($eggs as $egg)
                    <option value="{{ $egg->id }}" @foreach ($products as $product) @if ($product->egg_id == $egg->id) selected @endif @endforeach>({{ $egg->id }}) {{ $egg->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-4">
                <label>Node</label>
                <select name="node_id" class="form-control">
                  @foreach ($nodes as $node)
                    <option value="{{ $node->id }}" @foreach ($products as $product) @if ($product->node_id == $node->id) selected @endif @endforeach>({{ $node->id }}) {{ $node->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Memory (in MB)</label>
                <div>
                  <input type="text" class="form-control" name="memory" value="@foreach ($products as $product) {{ $product->memory }} @endforeach">
                </div>
              </div>
            </div>
          </div>
          <div class="box-footer no-border no-pad-top no-pad-bottom">
            <p class="text-muted small">If you want to disable memory limiting on a server, simply enter <code>0</code> into the memory field.</p><p>
            </p>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="form-group col-md-4">
                <label class="control-label">CPU (%)</label>
                <div>
                  <input type="text" class="form-control" name="cpu" value="@foreach ($products as $product) {{ $product->cpu }} @endforeach">
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Block IO Weight</label>
                <div>
                  <input type="text" class="form-control" name="io" value="@foreach ($products as $product) {{ $product->io }} @endforeach">
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Disk (in MB)</label>
                <div>
                  <input type="text" class="form-control" name="disk" value="@foreach ($products as $product) {{ $product->disk }} @endforeach">
                </div>
              </div>
            </div>
          </div>
          <div class="box-footer no-border no-pad-top no-pad-bottom">
              <p class="text-muted small">If you do not want to limit CPU usage, set the value to <code>0</code>. To determine a value, take the number of <em>physical</em> cores and multiply it by 100. For example, on a quad core system <code>(4 * 100 = 400)</code> there is <code>400%</code> available. To limit a server to using half of a single core, you would set the value to <code>50</code>. To allow a server to use up to two physical cores, set the value to <code>200</code>. BlockIO should be a value between <code>10</code> and <code>1000</code>. Please see <a href="https://docs.docker.com/engine/reference/run/#/block-io-bandwidth-blkio-constraint" target="_blank">this documentation</a> for more information about it.</p><p>
              </p>
          </div>
      </div>
    </div>
    <div class="col-xs-12">
      <div class="box box-secondary">
        <div class="box-header with-border">
          <h3 class="box-title">Application Feature Limits</h3>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="form-group col-md-6">
                <label>Database Limit</label>
                <div>
                  <input type="text" class="form-control" name="database_limit" value="@foreach ($products as $product) {{ $product->database_limit }} @endforeach">
                </div>
                <p class="text-muted small">The total number of databases a user is allowed to create for this server.</p>
              </div>
              <div class="form-group col-md-6">
                <label>Allocation Limit</label>
                <div>
                  <input type="text" class="form-control" name="allocation_limit" value="@foreach ($products as $product) {{ $product->allocation_limit }} @endforeach">
                </div>
                <p class="text-muted small">The total number of allocation a user is allowed to create for this server.</p>
              </div>
            </div>
        </div>
        <div class="box-footer with-border">
          @csrf
          <button type="submit" class="btn btn-sm btn-primary pull-right">Save</button>
        </div>
      </div>
    </div>
  </form>
</div>

@endsection
