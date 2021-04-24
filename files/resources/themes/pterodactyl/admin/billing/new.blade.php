@extends('layouts.admin')

@section('title')
    Billing
@endsection

@section('content-header')
    <h1>Billing<small>Create a new inboice.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.billing') }}">Billing</a></li>
        <li class="active">New Invoice</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
          <div class="nav-tabs-custom nav-tabs-floating">
            <ul class="nav nav-tabs">
              <li class="active"><a href="{{ route('admin.billing') }}">General</a></li>
              <li><a href="{{ route('admin.billing.categories') }}">Categories</a></li>
              <li><a href="{{ route('admin.billing.products') }}">Products</a></li>
              <li><a href="{{ route('admin.billing.promotional-codes') }}">Promotional Codes</a></li>
              <li><a href="{{ route('admin.billing.tos') }}">TOS</a></li>
            </ul>
          </div>
        </div>
        <div class="col-xs-12">
            <form method="POST" action="{{ route('admin.billing.submit') }}">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Create Invoice</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Amount in USD">
                        </div>
                        <div class="form-group">
                            <label for="user">User ID</label>
                            <input class="form-control" name="user_id" id="user">
                        </div>
                    </div>
                    <div class="box-footer">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-success btn-sm pull-right">Create Invoice</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('footer-scripts')
    @parent
    {!! Theme::js('js/admin/new-invoice.js') !!}
@endsection
