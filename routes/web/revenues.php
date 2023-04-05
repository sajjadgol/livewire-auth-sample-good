<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Revenues\Index as RevenuesIndex;
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

Route::group(['middleware' => 'auth'], function () {

    Route::get('revenues/{type?}/{id?}', RevenuesIndex::class)->name('revenues-management');
    
});