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
          <li><a href="{{ route('admin.billing.promotional-codes') }}">Promotional Codes</a></li>
          <li class="active"><a href="{{ route('admin.billing.tos') }}">TOS</a></li>
        </ul>
      </div>
    </div>
    <div class="col-xs-12">
        <form method="POST" action="{{ route('admin.billing.tos.update') }}">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit the TOS</h3>
                </div>
                <div class="box-body">
                    <textarea style="width: 100%; height: 500px;" name="tos">@foreach ($tos as $text){{ $text->tos }}@endforeach</textarea>
                    <span>* It is possible to use html elements in the tos such as a br tag.</span>
                </div>
                <div class="box-footer">
                    @csrf
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    {!! Theme::js('js/admin/billing.js') !!}
@endsection
