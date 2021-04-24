@extends('layouts.master')

@section('title')
    Billing
@endsection

@section('content-header')
    <h1>Billing<small>Manage your account</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('index') }}">@lang('strings.home')</a></li>
        <li class="active">Billing</li>
    </ol>
@endsection

@section('content')
@foreach ($billing as $setting)
    <div class="row">
        <div class="col-xs-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Billing Summary</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 text-center {{ Auth::user()->balance > 0 ? 'text-success' : 'text-danger' }}">
                            <b>Account Balance</b>
                            <h1>&{{ $setting->currency }}; {{ number_format(Auth::user()->balance, 2) }}</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Add founds with PayPal</h3>
                </div>
                <form method="POST" action="{{ route('account.billing.paypal') }}">
                    <div class="box-body">
                        <p>You can add founds to your wallet without linking a credit card using paypal.</p>
                        <div class="form-group">
                            <label class="control-label">Charge Amount</label>
                            <div>
                                <input type="number" name="amount" value="20" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        {!! csrf_field() !!}
                        <button type="submit" class="btn btn-success btn-sm">Add founds</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Invoices History</h3>
                </div>
                <div class="box-body">
                    <table class="table table-hover">
                        <tr>
                            <th>#</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th class="text-center">Item Name</th>
                            <th class="text-center"></th>
                        </tr>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td><b>#{{ $invoice->id }}</b></td>
                                <td>&{{ $setting->currency }}; {{ number_format($invoice->amount, 2) }}</td>
                                <td>{{ date(('d-m-Y'), strtotime($invoice->created_at)) }}</td>
                                <td class="text-center">{{ $invoice->reason }}</td>
                                <td class="text-center">
                                    <a href="{{ route('account.invoice.pdf', ['id' => $invoice->id]) }}"><i class="fa fa-file-pdf-o"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection

@section('footer-scripts')
    @parent
    <script src="https://js.stripe.com/v3/"></script>
    <script type="application/javascript">
        var form = $('#card-element').closest('form');
        var stripe = Stripe('{{ env("STRIPE_KEY") }}');
        var elements = stripe.elements();
        var card = stripe.elements().create('card', {
            style: {
                base: {
                    color: '#32325d',
                    lineHeight: '18px',
                    padding: '.5em',
                    margin: {
                        top: '10px',
                    },
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'}
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            }
        });
        card.mount('#card-element');
        form.on('submit', function(ev) {
            var token = form.find('[name="card_token"]');
            if (token.val()) return true;
            ev.preventDefault();
            stripe.createToken(card).then(function(result) {
                if (result.error) return alert(result.error.message);
                form.find('[name="card_brand"]').val(result.token.card.brand);
                form.find('[name="card_last4"]').val(result.token.card.last4);
                token.val(result.token.id);
                form.submit();
            });
            return false;
        })
    </script>
    <style>
        .StripeElement {
            background-color: white;
            height: 40px;
            padding: 10px 12px;
            border-radius: 4px;
            border: 1px solid transparent;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;}
        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;}
        .StripeElement--invalid {
            border-color: #fa755a;}
        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;}
        .AmountSelector {
            display: flex;}
    </style>
@endsection
