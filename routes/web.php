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
Route::post('importSupplier', 'SupplierController@import')->name('importSupplier');
#End Supplier Route


#Truck  Routes
Route::get('/recordmaintenance/truck', ['middleware' => 'auth', 'uses' => 'TruckController@index']);

Route::resource('ajaxtrucks','TruckController');
Route::post('alltrucks', 'TruckController@alltrucks' )->name('alltrucks');
Route::post('alltrucks_suppliers', 'TruckController@allsuppliers' )->name('alltrucks_suppliers');
Route::post('deactivateOrActivateTruck', 'TruckController@deactivateOrActivateTruck')->name('deactivateOrActivateTruck');

Route::get('exportTruck', 'TruckController@export')->name('exportTruck');
Route::post('importTruck', 'TruckController@import')->name('importTruck');
#End Truck Route

#Driver  Routes
Route::get('/recordmaintenance/driver', ['middleware' => 'auth', 'uses' => 'DriverController@index']);

Route::resource('ajaxdrivers','DriverController');
Route::post('alldrivers', 'DriverController@alldrivers' )->name('alldrivers');
Route::post('showPendingRegistrationsDriver', 'DriverController@showPendingRegistrations' )->name('showPendingRegistrationsDriver');
// Route::get('/showPendingRegistrations', ['middleware' => 'auth', 'uses' => 'DriverController@showPendingRegistrations']);
Route::post('alldrivers_suppliers', 'DriverController@allsuppliers' )->name('alldrivers_suppliers');
Route::post('deactivateOrActivateDriver', 'DriverController@deactivateOrActivateDriver')->name('deactivateOrActivateDriver');
Route::post('completeDriverRegistration', 'DriverController@completeDriverRegistration')->name('completeDriverRegistration');

Route::get('exportDriver', 'DriverController@export')->name('exportDriver');
Route::post('importDriver', 'DriverController@import')->name('importDriver');
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
Route::post('importAssistant', 'AssistantController@import')->name('importAssistant');
#End importAssistant Route


#SCHEDULER
Route::get('/scheduler/slottingschedule', ['middleware' => 'auth', 'uses' => 'SchedulerController@index']);
Route::get('/scheduler/index', ['middleware' => 'auth', 'uses' => 'SchedulerController@scheduling']);
Route::post('allschedules', 'SchedulerController@allSchedules' )->name('allschedules');
Route::post('getSlottingTime', 'SchedulerController@getSlottingTime' )->name('getSlottingTime');
Route::post('getSupplierData', 'SchedulerController@getSupplierData' )->name('getSupplierData');
Route::resource('ajaxschedules','SchedulerController');
#END SCHEDULER

#Docker
Route::get('/scheduler/dock', ['middleware' => 'auth', 'uses' => 'DockController@index']);

Route::resource('ajaxdockers','DockController');
Route::post('alldockers', 'DockController@allDockers' )->name('alldockers');
Route::post('deactivateOrActivateDocker', 'DockController@deactivateOrActivateDocker')->name('deactivateOrActivateDocker');

Route::get('exportDocker', 'DockController@export')->name('exportDocker');
Route::post('importDocker', 'DockController@import')->name('importDocker');
#END Docker




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
