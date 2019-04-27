<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('home');
// });

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', ['middleware' => 'auth', 'uses' => 'HomeController@index']);

#Supplier  Routes
Route::get('/recordmaintenance/supplier', ['middleware' => 'auth', 'uses' => 'SupplierController@index']);

Route::resource('ajaxsuppliers','SupplierController');
Route::post('allsuppliers', 'SupplierController@allSuppliers' )->name('allsuppliers');
Route::post('deactivateOrActivateSupplier', 'SupplierController@deactivateOrActivateSupplier')->name('deactivateOrActivateSupplier');

Route::get('exportSupplier', 'SupplierController@export')->name('exportSupplier');
#End Supplier Route


#Truck  Routes
Route::get('/recordmaintenance/truck', ['middleware' => 'auth', 'uses' => 'TruckController@index']);

Route::resource('ajaxtrucks','TruckController');
Route::post('alltrucks', 'TruckController@alltrucks' )->name('alltrucks');
Route::post('alltrucks_suppliers', 'TruckController@allsuppliers' )->name('alltrucks_suppliers');
Route::post('deactivateOrActivateTruck', 'TruckController@deactivateOrActivateTruck')->name('deactivateOrActivateTruck');

Route::get('exportTruck', 'TruckController@export')->name('exportTruck');
#End Truck Route

#Driver  Routes
Route::get('/recordmaintenance/driver', ['middleware' => 'auth', 'uses' => 'DriverController@index']);

Route::resource('ajaxdrivers','DriverController');
Route::post('alldrivers', 'DriverController@alldrivers' )->name('alldrivers');
Route::post('showPendingRegistrations', 'DriverController@showPendingRegistrations' )->name('showPendingRegistrations');
// Route::get('/showPendingRegistrations', ['middleware' => 'auth', 'uses' => 'DriverController@showPendingRegistrations']);
Route::post('alldrivers_suppliers', 'DriverController@allsuppliers' )->name('alldrivers_suppliers');
Route::post('deactivateOrActivateDriver', 'DriverController@deactivateOrActivateDriver')->name('deactivateOrActivateDriver');
Route::post('completeDriverRegistration', 'DriverController@completeDriverRegistration')->name('completeDriverRegistration');

Route::get('exportDriver', 'DriverController@export')->name('exportDriver');
#End Driver Route



#Assistant  Routes
Route::get('/recordmaintenance/assistant', ['middleware' => 'auth', 'uses' => 'AssistantController@index']);

Route::resource('ajaxassistants','AssistantController');
Route::post('allassistants', 'AssistantController@allassistants' )->name('allassistants');
Route::get('/showPendingRegistrations', ['middleware' => 'auth', 'uses' => 'AssistantController@showPendingRegistrations']);
Route::post('allassistants_suppliers', 'AssistantController@allsuppliers' )->name('allassistants_suppliers');
Route::post('deactivateOrActivateAssistant', 'AssistantController@deactivateOrActivateAssistant')->name('deactivateOrActivateAssistant');
Route::post('completeAssistantRegistration', 'AssistantController@completeAssistantRegistration')->name('completeAssistantRegistration');

Route::get('exportAssistant', 'AssistantController@export')->name('exportAssistant');
#End Assistant Route


#SCHEDULER
Route::get('/scheduler/slottingschedule', ['middleware' => 'auth', 'uses' => 'SchedulerController@index']);
#END SCHEDULER




#MASTERFILE
#Role Routes
Route::get('/masterfile/roles', ['middleware' => 'auth', 'uses' => 'RoleController@index']);

Route::resource('ajaxroles','RoleController');
Route::post('allroles', 'RoleController@allroles' )->name('allroles');
Route::post('deactivateOrActivateRole', 'RoleController@deactivateOrActivateRole')->name('deactivateOrActivateRole');
#End Role Routes

#UserRole Routes
Route::get('/masterfile/users', ['middleware' => 'auth', 'uses' => 'UserRoleController@index']);

Route::resource('ajaxusers','UserRoleController');
Route::post('allusers', 'UserRoleController@allusers' )->name('allusers');
Route::post('deactivateOrActivateUser', 'UserRoleController@deactivateOrActivateUser')->name('deactivateOrActivateUser');
#End UserRole Routes
#END MASTERFILE