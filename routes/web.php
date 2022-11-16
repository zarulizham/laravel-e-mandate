<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\FPX\Controller;
use ZarulIzham\EMandate\Http\Controllers\PaymentController;

$directPath = Config::get('e-mandate.direct_path');
$indirectPath = Config::get('e-mandate.indirect_path');

Route::post('e-mandate/payment/auth', [PaymentController::class, 'handle'])->name('e-mandate.payment.auth.request');

Route::post($directPath, [Controller::class, 'webhook'])->name('e-mandate.payment.direct.callback');
Route::post($indirectPath, [Controller::class, 'callback'])->name('e-mandate.payment.indirect.callback');

// Route::match(
//     ['get', 'post'],
//     'fpx/initiate/payment/{iniated_from?}/{test?}',
//     [Controller::class, 'initiatePayment']
// )->name('fpx.initiate.payment');

// Route::get(
//     'fpx/csr/request',
//     function () {
//         $countries = CountryListFacade::getList('en');
//         return view('fpx-payment::csr_request', compact('countries'));
//     }
// )->name('fpx.csr.request');
