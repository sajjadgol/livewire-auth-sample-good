<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Stores\Create as StoresCreate;
use App\Http\Livewire\Stores\Edit as StoresEdit;
use App\Http\Livewire\Stores\Index as StoresIndex;
use App\Http\Livewire\RestaurantTypes\Create as RestaurantTypesCreate;
use App\Http\Livewire\RestaurantTypes\Edit as RestaurantTypesEdit;
use App\Http\Livewire\RestaurantTypes\Index as RestaurantTypesIndex;
use App\Http\Livewire\Account\Store\Edit as AccountStore;
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

    Route::get('stores', StoresIndex::class)->name('store-management');
    Route::get('stores/edit/{id}', StoresEdit::class)->name('edit-store');
    Route::get('stores/create/new', StoresCreate::class)->name('add-store');
    Route::get('unverified/stores', StoresIndex::class)->name('unverified-stores');

   
    Route::get('restaurant-types', RestaurantTypesIndex::class)->name('restaurant-type-management');
    Route::get('restaurant-types/edit/{id}/{ref_lang?}', RestaurantTypesEdit::class)->name('edit-restaurant-type');
    Route::get('restaurant-types/create', RestaurantTypesCreate::class)->name('add-restaurant-type');
});

Route::group(['middleware' => 'auth'], function () {
     Route::get('settings/store', AccountStore::class)->name('provider-manage-store'); 
});