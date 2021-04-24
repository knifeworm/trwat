{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.error')

@section('title')
    Inactive License
@endsection

@section('content-header')
@endsection

@section('content')
<!-- This is why we don't let Pterodactyl's make links... fat fingered dinosaurs... -->
<div class="row">
    <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
        <div class="box box-warning">
            <div class="box-body text-center">
                <h1 class="text-yellow" style="font-size: 80px !important;font-weight: 100 !important;">Inactive License</h1>
                <p class="text-muted">Your system is not running on an active billing license and therefor can not be used.</p>
                <br><br>
                <b><p>Have you bought this addon? Contact me on Discord or email to whitelist your panel.</p></b>
                <br>
                <p>Kevko#4832</p>
                <p>kevko1606@gmail.com</p>
            </div>
            <div class="box-footer with-border">
                <a href="{{ URL::previous() }}"><button class="btn btn-warning">&larr; @lang('base.errors.return')</button></a>
                <a href="/"><button class="btn btn-default">@lang('base.errors.home')</button></a>
            </div>
        </div>
    </div>
</div>
@endsection
