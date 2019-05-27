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
// Route::get('/', ['middleware' => 'auth', 'uses' => 'HomeController@index']);

#Supplier  Routes
Route::get('/recordmaintenance/supplier', ['middleware' => 'auth', 'uses' => 'SupplierController@index']);

Route::resource('ajaxsuppliers','SupplierController');
Route::post('allsuppliers', 'SupplierController@allSuppliers' )->name('allsuppliers');
Route::post('deactivateOrActivateSupplier', 'SupplierController@deactivateOrActivateSupplier')->name('deactivateOrActivateSupplier');

Route::get('exportSupplier', 'SupplierController@export')->name('exportSupplier');
Route::post('importSupplier', 'SupplierController@import')->name('importSupplier');
Route::post('getSupplier', 'SupplierController@getSupplier')->name('getSupplier');
#End Supplier Route


#Truck  Routes
Route::get('/recordmaintenance/truck', ['middleware' => 'auth', 'uses' => 'TruckController@index']);

Route::resource('ajaxtrucks','TruckController');
Route::post('alltrucks', 'TruckController@alltrucks' )->name('alltrucks');
Route::post('alltrucks_suppliers', 'TruckController@allsuppliers' )->name('alltrucks_suppliers');
Route::post('deactivateOrActivateTruck', 'TruckController@deactivateOrActivateTruck')->name('deactivateOrActivateTruck');

Route::get('exportTruck', 'TruckController@export')->name('exportTruck');
Route::post('importTruck', 'TruckController@import')->name('importTruck');
Route::post('getTruck', 'TruckController@getTruck')->name('getTruck');
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
Route::post('getDriver', 'DriverController@getDriver')->name('getDriver');
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
Route::post('getAssistant', 'AssistantController@getAssistant')->name('getAssistant');
#End importAssistant Route


#SCHEDULER
Route::get('/scheduler/slottingschedule', ['middleware' => 'auth', 'uses' => 'SchedulerController@index']);
Route::get('/scheduler/index', ['middleware' => 'auth', 'uses' => 'SchedulerController@scheduling']);
Route::post('allschedules', 'SchedulerController@allSchedules' )->name('allschedules');
Route::post('getSlottingTime', 'SchedulerController@getSlottingTime' )->name('getSlottingTime');
Route::post('getSupplierData', 'SchedulerController@getSupplierData' )->name('getSupplierData');
Route::resource('ajaxschedules','SchedulerController');
Route::post('deactivateOrActivateSchedule', 'SchedulerController@deactivateOrActivateSchedule')->name('deactivateOrActivateSchedule');
Route::post('fetchIncompleteMaterials', 'SchedulerController@fetchIncompleteMaterials' )->name('fetchIncompleteMaterials');

Route::post('getEditDockUnavailability', 'SchedulerController@getEditDockUnavailability' )->name('getEditDockUnavailability');
Route::get('scheduler/printVoucher/{id}','SchedulerController@getVoucher');
Route::post('changeToFinalized', 'SchedulerController@changeToFinalized' )->name('changeToFinalized');
#END SCHEDULER

#Docker
Route::get('/scheduler/dock', ['middleware' => 'auth', 'uses' => 'DockController@index']);

Route::resource('ajaxdockers','DockController');
Route::post('alldockers', 'DockController@allDockers' )->name('alldockers');
Route::post('deactivateOrActivateDocker', 'DockController@deactivateOrActivateDocker')->name('deactivateOrActivateDocker');

Route::get('exportDocker', 'DockController@export')->name('exportDocker');
Route::post('importDocker', 'DockController@import')->name('importDocker');
Route::post('getDock', 'DockController@getDock')->name('getDock');
Route::post('getUserType', 'DockController@getUserType')->name('getUserType');
#END Docker




#MASTERFILE
#Role Routes
Route::get('/masterfile/roles', ['middleware' => 'auth', 'uses' => 'RoleController@index']);

Route::resource('ajaxroles','RoleController');
Route::post('allroles', 'RoleController@allroles' )->name('allroles');
Route::post('deactivateOrActivateRole', 'RoleController@deactivateOrActivateRole')->name('deactivateOrActivateRole');
Route::post('getRole', 'RoleController@getRole')->name('getRole');

#End Role Routes

#UserRole Routes
Route::get('/masterfile/users', ['middleware' => 'auth', 'uses' => 'UserRoleController@index']);

Route::resource('ajaxusers','UserRoleController');
Route::post('allusers', 'UserRoleController@allusers' )->name('allusers');
Route::post('deactivateOrActivateUser', 'UserRoleController@deactivateOrActivateUser')->name('deactivateOrActivateUser');
#End UserRole Routes
#END MASTERFILE


#General Dashboard
Route::get('/', ['middleware' => 'auth', 'uses' => 'DashboardController@index']);
Route::post('allgeneraldashboardsched', 'DashboardController@allGeneralSchedule' )->name('allgeneraldashboardsched');
Route::post('alldockdashboardsched', 'DashboardController@allDockSchedule' )->name('alldockdashboardsched');
Route::post('getCountDock', 'DashboardController@getCountDock' )->name('getCountDock');
Route::post('getFirstDockData', 'DashboardController@getFirstDockData' )->name('getFirstDockData');
Route::post('changeProcessStatus', 'DashboardController@changeProcessStatus' )->name('changeProcessStatus');
Route::post('checkIfIncoming', 'DashboardController@checkIfIncoming' )->name('checkIfIncoming');
#end dashboard

#all stand alone dashboards
Route::get('/dashboard/executive', ['middleware' => 'auth', 'uses' => 'DashboardController@executive']);
Route::get('/dashboard/parking', ['middleware' => 'auth', 'uses' => 'DashboardController@parking']);
Route::get('/dashboard/dock', ['middleware' => 'auth', 'uses' => 'DashboardController@docking']);
Route::get('/dashboard/gate', ['middleware' => 'auth', 'uses' => 'DashboardController@gate']);
Route::get('/dashboard/manual', ['middleware' => 'auth', 'uses' => 'DashboardController@manual']);

Route::post('dockingDashboard', 'DashboardController@dockingDashboard' )->name('dockingDashboard');
Route::post('parkingDashboard', 'DashboardController@parkingDashboard' )->name('parkingDashboard');
Route::post('gateDashboard', 'DashboardController@gateDashboard' )->name('gateDashboard');
Route::post('setOvertime', 'DashboardController@setOvertime' )->name('setOvertime');
Route::post('securityDashboard', 'DashboardController@securityDashboard' )->name('securityDashboard');

#end all stand alone dashboards

#Parking  Routes
Route::get('/others/parking', ['middleware' => 'auth', 'uses' => 'ParkingController@index']);
Route::resource('ajaxparking','ParkingController');
Route::post('allparking', 'ParkingController@allParking' )->name('allparking');
Route::post('deactivateOrActivateParking', 'ParkingController@deactivateOrActivateParking')->name('deactivateOrActivateParking');

Route::get('exportParking', 'ParkingController@export')->name('exportParking');
Route::post('importParking', 'ParkingController@import')->name('importParking');

Route::get('changeProcessStatus_jsonp', 'DashboardController@changeProcessStatus_jsonp' )->name('changeProcessStatus_jsonp');
#End Parking Route

#executivedashboard

Route::post('getTrucksCount', 'DashboardController@getTrucksCount' )->name('getTrucksCount');
Route::post('getOnTimeDepartures', 'DashboardController@getOnTimeDepartures' )->name('getOnTimeDepartures');
Route::post('getOnTimeArrivals', 'DashboardController@getOnTimeArrivals' )->name('getOnTimeArrivals');
Route::post('getSlottingCompliance', 'DashboardController@getSlottingCompliance' )->name('getSlottingCompliance');
Route::post('getAverageTurnAroundTime', 'DashboardController@getAverageTurnAroundTime' )->name('getAverageTurnAroundTime');
Route::post('getOverStaying', 'DashboardController@getOverStaying' )->name('getOverStaying');
Route::post('getOvertime', 'DashboardController@getOvertime' )->name('getOvertime');
Route::post('getDelayed', 'DashboardController@getDelayed' )->name('getDelayed');
Route::post('getUnloading', 'DashboardController@getUnloading' )->name('getUnloading');
Route::post('getOnSite', 'DashboardController@getOnSite' )->name('getOnSite');
#endexecutivedashboard


#BannedIssue  Routes
Route::get('/others/bannedIssueReporting', ['middleware' => 'auth', 'uses' => 'BannedIssueController@index']);

Route::resource('ajaxBannedIssue','BannedIssueController');
Route::post('allBannedIssue', 'BannedIssueController@allBannedIssue' )->name('allBannedIssue');
Route::post('deactivateOrActivateBannedIssue', 'BannedIssueController@deactivateOrActivateBannedIssue')->name('deactivateOrActivateBannedIssue');

Route::get('exportBannedIssue', 'BannedIssueController@export')->name('exportBannedIssue');
Route::post('importBannedIssue', 'BannedIssueController@import')->name('importBannedIssue');
#End BannedIssue Route