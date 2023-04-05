<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Checkout\Hyperpay as Checkout;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('payment/checkout', Checkout::class)->name('payment.checkout');
Route::get('payment/status/{status}', function ($post)
{
    return '';
})->name('payment.status');
