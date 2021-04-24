@extends('layouts.admin')
@include('partials/admin.settings.nav', ['activeTab' => 'basic'])

@section('title')
    Billing
@endsection

@section('content-header')
    <h1>Billing<small>Monitor your income.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Billing</li>
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
            <li class="active"><a href="{{ route('admin.billing.promotional-codes') }}">Promotional Codes</a></li>
            <li><a href="{{ route('admin.billing.tos') }}">TOS</a></li>
          </ul>
        </div>
      </div>
      <div class="col-xs-12">
          <div class="box">
              <div class="box-header with-border">
                  <h3 class="box-title">Create Promotional Code</h3>
              </div>
              <form action="{{ route('admin.billing.promotional-codes.new.post') }}" method="POST">
                  <div class="box-body box-secondary">
                      <div class="row">
                          <div class="form-group col-md-4">
                              <label class="control-label">Code</label>
                              <div>
                                <input class="form-control" name="code">
                              </div>
                          </div>
                          <div class="form-group col-md-4">
                              <label class="control-label">Discount Amount</label>
                              <div>
                                <input class="form-control" name="amount" value="0">
                              </div>
                          </div>
                          <div class="form-group col-md-4">
                              <label class="control-label">Discount Percentage</label>
                              <div>
                                <input class="form-control" name="percentage" value="0">
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="box-footer no-border no-pad-top no-pad-bottom">
                      <p class="text-muted small">If you don't want to use a amount or percentage for this promotional code, leave these options <code>0</code>.</p>
                  </div>
                  <div class="box-body box-secondary">
                      <div class="row">
                          <div class="form-group col-md-4">
                              <label class="control-label">Min Basket</label>
                              <div>
                                <input class="form-control" name="min_amount" value="0">
                              </div>
                          </div>
                          <div class="form-group col-md-4">
                              <label class="control-label">Max Basket</label>
                              <div>
                                <input class="form-control" name="max_amount" value="0">
                              </div>
                          </div>
                          <div class="form-group col-md-4">
                              <label class="control-label">Lasts Till</label>
                              <div>
                                <input class="form-control" name="lasts_till" placeholder="2020-01-05 12:00:00">
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="box-footer no-border no-pad-top no-pad-bottom">
                      <p class="text-muted small">If you don't want to use a minimum or maximum basket value for this promotional code, leave these options <code>0</code>. When it is past the lasts till timestamp, you can't use this code anymore.
                      The timestamp works as follow: <code>year-month-day hour:minute:second</code>. So if you want to let this code last till the first of januar, you do <code>2020-01-01 00:00:00</code>. Leave the lasts till value blank if you want this code to last for ever.</p>
                  </div>
                  <div class="box-footer">
                      @csrf
                      <button type="submit" class="btn btn-primary pull-right">Submit</button>
                  </div>
              </form>
            </div>
        </div>
    </div>
</div>
@endsection
