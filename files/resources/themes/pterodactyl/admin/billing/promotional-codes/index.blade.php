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
                <h3 class="box-title">Promotional Codes</h3>
                <div class="box-tools">
                    <a href="{{ route('admin.billing.promotional-codes.new') }}" class="btn btn-sm btn-primary">Create New</a>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                  <tbody>
                    <tr>
                      <th>ID</th>
                      <th>Code</th>
                      <th>Percentage</th>
                      <th>Amount</th>
                      <th class="text-center hidden-sm hidden-xs">Uses</th>
                      <th class="text-center hidden-sm hidden-xs">Lasts till</th>
                      <th class="text-center hidden-sm hidden-xs"></th>
                    </tr>
                    @foreach ($promotional_codes as $code)
                      <tr>
                        <td>{{ $code->id }}</td>
                        <td><code>{{ $code->code }}</code></a></td>
                        <td>{{ $code->percentage }}%</td>
                        <td>${{ $code->amount }}</td>
                        <td class="text-center">{{ $code->uses }} x</td>
                        <td class="text-center">@if ($code->lasts_till !== '2018-04-03 12:28:38') {{ $code->lasts_till }} @else Forever @endif</td>
                        <td class="text-center"><a class="btn btn-danger btn-sm" href="{{ route('admin.billing.promotional-codes.delete', $code->id) }}"><i class="fa fa-trash"></i></a></td>
                      </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
        </div>
    </div>
</div>
@endsection
