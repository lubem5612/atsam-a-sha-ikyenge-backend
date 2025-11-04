<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

if (!function_exists('isAdmin')) {
    function isAdmin() {
        return Auth::user() && Auth::user()->role == 'admin' ;
    }
}
if (!function_exists('isUser')) {
    function isUser() {
        return Auth::user() && Auth::user()->role == 'user' ;
    }
}
if (!function_exists('generateReference')) {
    function generateReference() {
        return "nkst-atsam-".Carbon::now()->format('Ymdhis');
    }
}
