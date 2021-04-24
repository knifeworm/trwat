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
          <li class="active"><a href="{{ route('admin.billing') }}">General</a></li>
          <li><a href="{{ route('admin.billing.categories') }}">Categories</a></li>
          <li><a href="{{ route('admin.billing.products') }}">Products</a></li>
          <li><a href="{{ route('admin.billing.promotional-codes') }}">Promotional Codes</a></li>
          <li><a href="{{ route('admin.billing.tos') }}">TOS</a></li>
        </ul>
      </div>
    </div>
    <div class="col-xs-8">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Invoices History</h3>
                <div class="box-tools">
                    <a href="{{ route('admin.billing.new') }}" class="btn btn-sm btn-primary">Create New</a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-hover">
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>User</th>
                        <th></th>
                    </tr>
                    @foreach($invoices as $invoice)
                        @if ($invoice->reason == 'Top up Credit')
                            <tr>
                                <td><b>#{{ $invoice->id }}</b></td>
                                <td>@foreach ($billing as $settings) &{{ $settings->currency }}; @endforeach {{ number_format($invoice->amount, 2) }}</td>
                                <td>{{ date(__('d-m-Y'), strtotime($invoice->created_at)) }}</td>
                                <td>{{ $invoice->user->getNameAttribute() }}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.billing.pdf', ['id' => $invoice->id]) }}"><i class="fa fa-file-pdf-o"></i></a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-4">
        <div class="box box-secondary">
            <div class="box-body">
                @if ($this_version == $latest_version)
                <p>You are running the Billing addon on the latest version <code>1.0.0</code>.</p>
                @else
                <p>Your Billing addon is not up-to-date. There is a newer version ready for you on mc-market to download. The latest version is <code>{{ $latest_version }}</code>, and you are running on <code>{{ $this_version }}</code></p>
                @endif
            </div>
        </div>
        <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="fa fa-globe"></i></span>
            <div class="info-box-content number-info-box-content">
                <span class="info-box-text">{{ date('Y')}} Income </span>
                <span class="info-box-number">@foreach ($billing as $settings) &{{ $settings->currency }}; @endforeach{{ number_format($this_year_income, 2) }}</span>
            </div>
        </div>
        <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="ion ion-ios-calendar"></i></span>
            <div class="info-box-content number-info-box-content">
                <span class="info-box-text">{{ date('F') }} Income</span>
                <span class="info-box-number">@foreach ($billing as $settings) &{{ $settings->currency }}; @endforeach{{ number_format($this_month_income, 2) }}</span>
            </div>
        </div>
        <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="ion ion-ios-pricetags"></i></span>
            <div class="info-box-content number-info-box-content">
                <span class="info-box-text">User Balance left</span>
                <span class="info-box-number">@foreach ($billing as $settings) &{{ $settings->currency }}; @endforeach{{ number_format($user_accounts, 2) }}</span>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.billing.settings') }}">
            @foreach ($billing as $settings)
                <div class="box box-secondary">
                    <div class="box-header with-border">
                        <h3 class="box-title">General Settings</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label">Currency</label>
                            <select name="currency" class="form-control">
                                <option value="euro" @if ($settings->currency == 'euro') selected @endif>&euro; EUR</option>
                                <option value="pound" @if ($settings->currency == 'pound') selected @endif>&pound; GBP</option>
                                <option value="dollar" @if ($settings->currency == 'dollar') selected @endif>&dollar; USD</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Use categories</label>
                            <select name="use_categories" class="form-control">
                                <option value="0" @if ($settings->use_categories == 0) selected @endif>No</option>
                                <option value="1" @if ($settings->use_categories == 1) selected @endif>Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        @csrf
                        <button type="submit" class="btn btn-primary pull-right">Save</button>
                    </div>
                </div>
            @endforeach
        </form>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    {!! Theme::js('js/admin/billing.js') !!}
@endsection
