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
        <li class="active"><a href="{{ route('admin.billing.categories') }}">Categories</a></li>
        <li><a href="{{ route('admin.billing.products') }}">Products</a></li>
        <li><a href="{{ route('admin.billing.promotional-codes') }}">Promotional Codes</a></li>
        <li><a href="{{ route('admin.billing.tos') }}">TOS</a></li>
      </ul>
    </div>
  </div>
  @foreach ($category as $setting)
    <form method="POST" action="{{ route('admin.billing.categories.edit.store', $setting->id) }}">
      <div class="col-xs-12">
        <div class="box box-secondary">
          <div class="box-header with-border">
            <h3 class="box-title">General Information</h3>
          </div>
          <div class="box-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label class="control-label">Category Name</label>
                  <div>
                    <input type="text" class="form-control" name="name" placeholder="Category Name" value="{{ $setting->name }}">
                  </div>
                </div>
                <div class="form-group col-md-6">
                  <label class="control-label">Description</label>
                  <div>
                    <input type="text" class="form-control" name="description" placeholder="For Minecraft Servers" value="{{ $setting->description }}">
                  </div>
                </div>
                <div class="form-group col-md-6">
                  <label class="control-label">Priority</label>
                  <div>
                    <input type="text" class="form-control" name="priority" placeholder="5" value="{{ $setting->priority }}">
                  </div>
                </div>
                <div class="form-group col-md-6">
                  <label class="control-label">Visible</label>
                  <select name="visible" class="form-control">
                    <option value="0" @if ($setting->visible == 0) selected @endif>No</option>
                    <option value="1" @if ($setting->visible == 1) selected @endif>Yes</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="box-footer with-border">
              @csrf
              <button type="submit" class="btn btn-sm btn-primary pull-right">Create</button>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </form>
</div>
@endsection

@section('footer-scripts')
    @parent
    {!! Theme::js('vendor/lodash/lodash.js') !!}
    {!! Theme::js('js/admin/new-server.js') !!}
@endsection
