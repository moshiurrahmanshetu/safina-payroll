<?php

use Illuminate\Support\Facades\Route;

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
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\IndentController;
use App\Http\Controllers\MrsItemController;
use App\Http\Controllers\PurposeController;
use App\Http\Controllers\PurchaseTransactionController;
use App\Http\Controllers\TicketCategoryController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketSaleController;
use App\Http\Controllers\GateController;
use App\Http\Controllers\TicketCashHandoverController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceMetaFieldController;
use App\Http\Controllers\CategoryMetaFieldController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\BookingController;
// use App\Http\Controllers\PricingRuleController;
use App\Http\Controllers\DiscountRuleController;
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\BookingCashHandoverController;
use App\Http\Controllers\WaterParkTicketController;
use App\Http\Controllers\WaterParkTimeRangeController;
use App\Http\Controllers\WaterParkSettingController;
use App\Http\Controllers\WaterParkCounterController;
use App\Http\Controllers\WaterParkCashHandoverController;
use App\Http\Controllers\ParkingTicketController;
use App\Http\Controllers\ParkingReportController;
use App\Http\Controllers\ParkingCounterController;
use App\Http\Controllers\ParkingCashHandoverController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PackageBookingController;
use App\Http\Controllers\PackageReportController;
use App\Http\Controllers\PackageCounterController;
use App\Http\Controllers\PackageCashHandoverController;
use App\Http\Controllers\LockerItemController;
use App\Http\Controllers\GearItemController;
use App\Http\Controllers\ItemPricingController;
use App\Http\Controllers\LockerGearTicketController;
use App\Http\Controllers\LockerGearReportController;
use App\Http\Controllers\LockerGearCounterController;
use App\Http\Controllers\LockerGearCashHandoverController;
use App\Http\Controllers\PermanentEmployeeController;
use App\Http\Controllers\WorkAreaController;
use App\Http\Controllers\DailyWorkerController;
use App\Http\Controllers\ContractWorkerController;
use App\Http\Controllers\SalaryStructureController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\SalaryDisbursementController;
use App\Http\Controllers\SalaryRevisionController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\DailyAttendanceController;
use App\Http\Controllers\EmployeeShiftController;
use App\Http\Controllers\FingerprintLogController;
use App\Http\Controllers\FingerprintSessionController;
use App\Http\Controllers\FingerprintAttendanceController;

Route::get('/', function () {
  return view('welcome');
});
Auth::routes();

Route::group(['middleware' => 'auth'], function (){
  Route::get('/home', [HomeController::class, 'index']);
  Route::get('/myadmin', [HomeController::class, 'index'])->name('dashboard');
  Route::get('/received_products', [AjaxController::class,'received_products'])->name('ajax.received_products');
  Route::get('/get_supplier_lists', [AjaxController::class,'get_supplier_lists'])->name('ajax.get_supplier_lists');
  Route::get('/get_supllier_info', [AjaxController::class, 'get_supllier_info'])->name('ajax.get_supllier_info');
  Route::get('/get_item_info', [AjaxController::class, 'get_item_info'])->name('ajax.get_item_info');
  Route::get('/check_availability', [AjaxController::class,'check_availability'])->name('ajax.check_availability');
  Route::get('/show_user_item_mrs', [AjaxController::class,'show_user_item_mrs'])->name('ajax.show_user_item_mrs');
  Route::get('/get_mrs_item_info', [AjaxController::class, 'get_mrs_item_info'])->name('ajax.get_mrs_item_info');
  Route::get('/lowstock_summary', [AjaxController::class, 'lowstock_summary'])->name('ajax.lowstock_summary');
  Route::get('/show_type_wise_item_list', [AjaxController::class, 'show_type_wise_item_list'])->name('ajax.show_type_wise_item_list');
  Route::get('/show_purpose_names', [AjaxController::class, 'show_purpose_names'])->name('ajax.show_purpose_names');
});

  // Room availability check - OUTSIDE admin middleware to avoid 302 redirect on AJAX
  Route::get('/check-room-availability', [BookingController::class, 'checkRoomAvailability'])->name('ajax.check_room_availability');

  Route::get('/ticket/verify/{qr_code}', [TicketSaleController::class, 'verifyTicket'])->name('ticket.verify');
  Route::post('/ticket/verify', [TicketSaleController::class, 'confirmVerify'])->name('ticket.verify.confirm');
  // Package ticket verification routes (new token-based architecture)
  Route::get('/package/scan/{package_token}/{ticket_token}', [PackageBookingController::class, 'scanByToken'])->name('package.scan.token');
  Route::post('/package/scan', [PackageBookingController::class, 'validateByToken'])->name('package.validate.token');


Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'permissions']], function () {
	Route::resource('roles', RoleController::class);
	Route::get('/users', [RegisterController::class, 'showUserLists'])->name('users.index');
	Route::get('/users/create', [RegisterController::class, 'create'])->name('users.create');
	Route::put('/users/store', [RegisterController::class, 'store'])->name('users.store');
	Route::get('/users/{user}', [RegisterController::class, 'showUser'])->name('users.show');
	Route::get('/users/{user}/edit', [RegisterController::class, 'editUser'])->name('users.edit');
	Route::put('/user/{user}/update', [RegisterController::class, 'updateUser'])->name('user.update');
	Route::get('/password', [RegisterController::class, 'password'])->name('password');
	Route::put('/users/changePassword', [RegisterController::class, 'changePassword'])->name('users.changePassword');
	Route::put('/users/changeAllUserPassword', [RegisterController::class, 'changeAllUserPassword'])->name('users.changeAllUserPassword');
  Route::get('/profile', [RegisterController::class, 'profile'])->name('profile');
  Route::put('/update_profile', [RegisterController::class,'updateProfile'])->name('update_profile');
	Route::delete('/users/{user}', [RegisterController::class, 'destroyUser'])->name('users.destroy');
	Route::resource('site_settings', SiteSettingController::class,['only' => ['edit', 'update']]);	
	Route::resource('designation',DesignationController::class,['only'=>['create','store','destroy']]);
	Route::resource('department', DepartmentController::class,['only'=>['create','store','destroy']]);
  Route::resource('category', CategoryController::class,['only' => ['create', 'store', 'destroy']]);
  Route::resource('warehouse', WarehouseController::class,['only' => ['create', 'store','edit','update']]);
  Route::resource('purpose', PurposeController::class,['only' => ['create', 'store','edit','update']]);
  Route::resource('supplier', SupplierController::class);
  Route::resource('item', ItemController::class);

  Route::resource('purchase', PurchaseController::class);
  Route::get('/purchase_print',[PurchaseController::class,'purchase_print'])->name('purchase_print');
  Route::resource('stock_in', StockInController::class);
  Route::get('/stock_summary',[StockInController::class,'stock_summary'])->name('stock_summary');
  Route::resource('purchase_transaction', PurchaseTransactionController::class);
 
  Route::get('/low_stock_reminder',[StockInController::class,'low_stock_reminder'])->name('low_stock_reminder');

  Route::get('/stock_in_print',[StockInController::class,'stock_in_print'])->name('stock_in_print');
  Route::resource('requisition', RequisitionController::class);
  Route::get('/counter_sign_list',[RequisitionController::class,'counter_sign_list'])->name('counter_sign_list');
  Route::get('/counter_sign_show/{id}',[RequisitionController::class,'counter_sign_show'])->name('counter_sign_show');
  Route::put('/counter_sign_update/{id}',[RequisitionController::class,'counter_sign_update'])->name('counter_sign_update');
  Route::get('/admin_requisition_list',[RequisitionController::class,'admin_requisition_list'])->name('admin_requisition_list');
  Route::get('/admin_requisition_show/{id}',[RequisitionController::class,'admin_requisition_show'])->name('admin_requisition_show');
  Route::put('/admin_requisition_update/{id}',[RequisitionController::class,'admin_requisition_update'])->name('admin_requisition_update');
  Route::get('/admin_requisition_summary',[RequisitionController::class,'admin_requisition_summary'])->name('admin_requisition_summary');
  Route::get('/item_wise_requisition',[RequisitionController::class,'item_wise_requisition'])->name('item_wise_requisition');
  Route::get('/indent_list',[IndentController::class,'indent_list'])->name('indent_list');
  Route::get('/admin_indent_list',[IndentController::class,'admin_indent_list'])->name('admin_indent_list');
  Route::resource('mrs_item', MrsItemController::class);
  Route::get('/my_mrs_item_list',[MrsItemController::class,'my_mrs_item_list'])->name('my_mrs_item_list');
  Route::get('/my_mrs_item_show/{id}',[MrsItemController::class,'my_mrs_item_show'])->name('my_mrs_item_show');
  
  Route::get('/mrs_item_summary',[MrsItemController::class,'mrs_item_summary'])->name('mrs_item_summary');

  // Ticket Managements
  Route::resource('ticket_category', TicketCategoryController::class, ['only' => ['create', 'store', 'destroy']]);
  Route::resource('gates', GateController::class);
  Route::resource('tickets', TicketController::class);
  Route::get('/ticket_sales', [TicketSaleController::class, 'index'])->name('ticket_sales.index');
  Route::get('/ticket_sales/create', [TicketSaleController::class, 'create'])->name('ticket_sales.create');
  Route::post('/ticket_sales', [TicketSaleController::class, 'store'])->name('ticket_sales.store');
  Route::delete('/ticket_sales/{qr_code}', [TicketSaleController::class, 'destroy'])->name('ticket_sales.destroy');
  Route::get('/ticket_sales/print/{qr_code}', [TicketSaleController::class, 'print'])->name('ticket_sales.print');
  Route::get('/ticket_sales/group-print/{sale_group_token}', [TicketSaleController::class, 'groupPrint'])->name('ticket_sales.group_print');
  Route::get('/ticket_sales/get_price/{ticket_id}', [TicketSaleController::class, 'getTicketPrice'])->name('ticket_sales.get_price');
  Route::get('/ticket_sales_report', [TicketSaleController::class, 'report'])->name('ticket_sales.report');
  Route::get('/ticket_validation', [TicketSaleController::class, 'validationForm'])->name('ticket_sales.validation_form');
  Route::post('/ticket_validate', [TicketSaleController::class, 'validateTicket'])->name('ticket_sales.validate');
  Route::get('/ticket_validate/{qr_code}', [TicketSaleController::class, 'validateTicket'])->name('ticket.validate');
  Route::get('/ticket_scan', [TicketSaleController::class, 'scan'])->name('ticket_sales.scan');
  Route::post('/ticket_scan_validate', [TicketSaleController::class, 'scanValidate'])->name('ticket_sales.scan.validate');
  Route::get('/ticket-cash-handovers', [TicketCashHandoverController::class, 'index'])->name('ticket_cash_handovers.index');
  Route::post('/ticket-cash-handovers', [TicketCashHandoverController::class, 'store'])->name('ticket_cash_handovers.store');
  Route::get('/ticket-cash-handovers/approval', [TicketCashHandoverController::class, 'approval'])->name('ticket_cash_handovers.approval');
  Route::post('/ticket-cash-handovers/{id}/approve', [TicketCashHandoverController::class, 'approve'])->name('ticket_cash_handovers.approve');
  Route::post('/ticket-cash-handovers/{id}/reject', [TicketCashHandoverController::class, 'reject'])->name('ticket_cash_handovers.reject');

  // Service and Bookings Routes
  Route::resource('service_categories', ServiceCategoryController::class);
  Route::resource('services', ServiceController::class);
  Route::get('/services/ajax/get_service/{id}', [ServiceController::class, 'getService'])->name('services.ajax.get_service');
  Route::get('/categories/{category}/meta_fields/create', [CategoryMetaFieldController::class, 'create'])->name('category_meta_fields.create');
  Route::post('/categories/{category}/meta_fields/store', [CategoryMetaFieldController::class, 'store'])->name('category_meta_fields.store');
  Route::get('/categories/{category}/meta_fields/{meta_field}/edit', [CategoryMetaFieldController::class, 'edit'])->name('category_meta_fields.edit');
  Route::put('/categories/{category}/meta_fields/{meta_field}/update', [CategoryMetaFieldController::class, 'update'])->name('category_meta_fields.update');
  Route::delete('/categories/{category}/meta_fields/{meta_field}/destroy', [CategoryMetaFieldController::class, 'destroy'])->name('category_meta_fields.destroy');
  Route::post('/category_meta_fields/update_sort_order', [CategoryMetaFieldController::class, 'updateSortOrder'])->name('category_meta_fields.update_sort_order');
  Route::get('/services/{service}/meta_fields/create', [ServiceMetaFieldController::class, 'create'])->name('service_meta_fields.create');
  Route::post('/services/{service}/meta_fields/store', [ServiceMetaFieldController::class, 'store'])->name('service_meta_fields.store');
  Route::get('/services/{service}/meta_fields/{meta_field}/edit', [ServiceMetaFieldController::class, 'edit'])->name('service_meta_fields.edit');
  Route::put('/services/{service}/meta_fields/{meta_field}/update', [ServiceMetaFieldController::class, 'update'])->name('service_meta_fields.update');
  Route::delete('/services/{service}/meta_fields/{meta_field}/destroy', [ServiceMetaFieldController::class, 'destroy'])->name('service_meta_fields.destroy');
  Route::post('/service_meta_fields/update_sort_order', [ServiceMetaFieldController::class, 'updateSortOrder'])->name('service_meta_fields.update_sort_order');
  Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability.index');
  Route::get('/availability/booking-details', [AvailabilityController::class, 'getBookingDetails'])->name('availability.booking-details');
  
  Route::resource('bookings', BookingController::class);
  Route::get('/bookings/get_fields/{service_id}', [BookingController::class, 'get_fields'])->name('bookings.get_fields');
  Route::post('/bookings/validate_promo', [BookingController::class, 'validatePromo'])->name('bookings.validate_promo');
  Route::get('/bookings/availability/check', [BookingController::class, 'checkAvailability'])->name('bookings.availability.check');
  Route::get('/bookings/room-availability/check', [BookingController::class, 'checkRoomAvailability'])->name('bookings.room_availability.check');
  Route::get('/bookings/availability/calendar', [BookingController::class, 'availabilityCalendar'])->name('bookings.availability.calendar');
  Route::get('/bookings/availability/get_calendar_data', [BookingController::class, 'getCalendarData'])->name('bookings.availability.get_calendar_data');
  Route::get('/get-services/{category_id}', [ServiceController::class, 'getServicesByCategory'])->name('get.services');
  // Route::resource('pricing_rules', PricingRuleController::class);
  Route::resource('discount_rules', DiscountRuleController::class);
  Route::resource('time-slots', TimeSlotController::class);
  Route::get('/get-slots/{service_id}', [TimeSlotController::class, 'getSlotsByService'])->name('get.slots');
  Route::resource('counters', CounterController::class);
  Route::get('/counter-reports', [BookingController::class, 'counterReport'])->name('bookings.counter_report');
  Route::get('/counter-reports/data', [BookingController::class, 'getCounterReportData'])->name('bookings.counter_report.data');
  // Route::get('print_customer_details', array('as' => 'print_customer_details', 'uses' => 'CustomerController@print_customer_details'));
  Route::get('/booking-cash-handovers', [BookingCashHandoverController::class, 'index'])->name('booking_cash_handovers.index');
  Route::post('/booking-cash-handovers', [BookingCashHandoverController::class, 'store'])->name('booking_cash_handovers.store');
  Route::get('/booking-cash-handovers/approval', [BookingCashHandoverController::class, 'approval'])->name('booking_cash_handovers.approval');
  Route::post('/booking-cash-handovers/{id}/approve', [BookingCashHandoverController::class, 'approve'])->name('booking_cash_handovers.approve');
  Route::post('/booking-cash-handovers/{id}/reject', [BookingCashHandoverController::class, 'reject'])->name('booking_cash_handovers.reject');

  // Water Park Routes
  Route::get('/water-park-tickets', [WaterParkTicketController::class, 'index'])->name('water_park_tickets.index');
  Route::get('/water-park-tickets/create', [WaterParkTicketController::class, 'create'])->name('water_park_tickets.create');
  Route::post('/water-park-tickets', [WaterParkTicketController::class, 'store'])->name('water_park_tickets.store');
  Route::get('/water-park-tickets/scan-camera', [WaterParkTicketController::class, 'scanCamera'])->name('water_park_tickets.scan_camera');
  Route::get('/water-park-tickets/bulk-print', [WaterParkTicketController::class, 'bulkPrint'])->name('water_park_tickets.bulk_print');
  Route::get('/water-park-tickets/{ticket_number}', [WaterParkTicketController::class, 'show'])->name('water_park_tickets.show');
  Route::get('/water-park-tickets/scan/{ticket_number}', [WaterParkTicketController::class, 'scan'])->name('water_park_tickets.scan');
  Route::post('/water-park-tickets/check-in/{ticket_number}', [WaterParkTicketController::class, 'checkIn'])->name('water_park_tickets.check_in');
  Route::post('/water-park-tickets/check-out/{ticket_number}', [WaterParkTicketController::class, 'checkOut'])->name('water_park_tickets.check_out');
  Route::get('/water-park-time-ranges', [WaterParkTimeRangeController::class, 'index'])->name('water_park_time_ranges.index');
  Route::get('/water-park-settings', [WaterParkSettingController::class, 'edit'])->name('water_park_settings.edit');
  Route::post('/water-park-settings', [WaterParkSettingController::class, 'update'])->name('water_park_settings.update');
  Route::resource('water_park_counters', WaterParkCounterController::class);
  Route::get('/water-park-cash-handovers', [WaterParkCashHandoverController::class, 'index'])->name('water_park_cash_handovers.index');
  Route::post('/water-park-cash-handovers', [WaterParkCashHandoverController::class, 'store'])->name('water_park_cash_handovers.store');
  Route::get('/water-park-cash-handovers/approval', [WaterParkCashHandoverController::class, 'approval'])->name('water_park_cash_handovers.approval');
  Route::post('/water-park-cash-handovers/{id}/approve', [WaterParkCashHandoverController::class, 'approve'])->name('water_park_cash_handovers.approve');
  Route::post('/water-park-cash-handovers/{id}/reject', [WaterParkCashHandoverController::class, 'reject'])->name('water_park_cash_handovers.reject');


// Parking Ticket Routes (separate module)
 // Vehicle Routes
  Route::resource('vehicles', VehicleController::class)->only(['index', 'create', 'store', 'edit', 'update']);
  Route::get('/vehicles/rates/json', [VehicleController::class, 'getRates'])->name('vehicles.rates');
  Route::get('/parking-tickets', [ParkingTicketController::class, 'index'])->name('parking_tickets.index');
  Route::get('/parking-tickets/create', [ParkingTicketController::class, 'create'])->name('parking_tickets.create');
  Route::post('/parking-tickets', [ParkingTicketController::class, 'store'])->name('parking_tickets.store');
  Route::get('/parking-tickets/scan-camera', [ParkingTicketController::class, 'scanCamera'])->name('parking_tickets.scan_camera');
  Route::get('/parking-tickets/view-all', [ParkingTicketController::class, 'view_all_parking_tickets'])->name('parking_tickets.view_all');
  Route::get('/parking-tickets/{ticket_number}', [ParkingTicketController::class, 'show'])->name('parking_tickets.show');
  Route::get('/parking-tickets/{ticket_number}/edit', [ParkingTicketController::class, 'edit'])->name('parking_tickets.edit');
  Route::put('/parking-tickets/{ticket_number}', [ParkingTicketController::class, 'update'])->name('parking_tickets.update');
  Route::delete('/parking-tickets/{ticket_number}', [ParkingTicketController::class, 'destroy'])->name('parking_tickets.destroy');
  Route::get('/parking/scan/{ticket_number}', [ParkingTicketController::class, 'scan'])->name('parking_tickets.scan');
  Route::post('/parking/checkin/{ticket_number}', [ParkingTicketController::class, 'checkIn'])->name('parking_tickets.checkin');
  Route::post('/parking/checkout/{ticket_number}', [ParkingTicketController::class, 'checkOut'])->name('parking_tickets.checkout');
  Route::get('/parking/receipt/{ticket_number}', [ParkingTicketController::class, 'receipt'])->name('parking_tickets.receipt');
  Route::get('/parking/entry-receipt/{ticket_number}', [ParkingTicketController::class, 'entryReceipt'])->name('parking_tickets.entry_receipt');
  Route::get('/parking/extra-payment/{ticket_number}', [ParkingTicketController::class, 'extraPayment'])->name('parking_tickets.extra_payment');
  Route::post('/parking/extra-payment/{ticket_number}', [ParkingTicketController::class, 'processExtraPayment'])->name('parking_tickets.process_extra_payment');
  Route::get('/parking-tickets/view-all', [ParkingTicketController::class, 'view_all_parking_tickets'])->name('parking_tickets.view_all');
  Route::get('/parking-tickets/scan-camera', [ParkingTicketController::class, 'scanCamera'])->name('parking_tickets.scan_camera');
  Route::resource('parking_counters', ParkingCounterController::class);
  Route::get('/parking-reports', [ParkingReportController::class, 'index'])->name('parking_reports.index');
  Route::get('/parking-reports/view-all', [ParkingReportController::class, 'view_all_parking_reports'])->name('parking_reports.view_all');
  Route::get('/parking-cash-handovers', [ParkingCashHandoverController::class, 'index'])->name('parking_cash_handovers.index');
  Route::post('/parking-cash-handovers', [ParkingCashHandoverController::class, 'store'])->name('parking_cash_handovers.store');
  Route::get('/parking-cash-handovers/approval', [ParkingCashHandoverController::class, 'approval'])->name('parking_cash_handovers.approval');
  Route::post('/parking-cash-handovers/{id}/approve', [ParkingCashHandoverController::class, 'approve'])->name('parking_cash_handovers.approve');
  Route::post('/parking-cash-handovers/{id}/reject', [ParkingCashHandoverController::class, 'reject'])->name('parking_cash_handovers.reject');
  
  // Package Management Routes
  Route::resource('packages', PackageController::class);
  Route::resource('package_bookings', PackageBookingController::class);
  Route::get('/package_bookings/get-package/{package_id}', [PackageBookingController::class, 'getPackageDetails'])->name('package_bookings.get_package');
  Route::get('/package-bookings/print/{id}', [PackageBookingController::class, 'print'])->name('package_bookings.print');
  Route::get('/package-bookings/{id}/tickets/preview', [PackageBookingController::class, 'previewTickets'])->name('package_bookings.preview');
  Route::resource('package_counters', PackageCounterController::class);
  Route::get('/package-bookings/{id}/tickets/print', [PackageBookingController::class, 'printTickets'])->name('package_bookings.print_tickets');
  Route::get('/package-bookings/scan', [PackageBookingController::class, 'showScanForm'])->name('package_bookings.scan_form');
  Route::get('/package-reports', [PackageReportController::class, 'index'])->name('package_reports.index');
  Route::get('/package-reports/generate', [PackageReportController::class, 'generate'])->name('package_reports.generate');
  Route::get('/package-reports/print', [PackageReportController::class, 'print'])->name('package_reports.print');
  Route::get('/package-cash-handovers', [PackageCashHandoverController::class, 'index'])->name('package_cash_handovers.index');
  Route::post('/package-cash-handovers', [PackageCashHandoverController::class, 'store'])->name('package_cash_handovers.store');
  Route::get('/package-cash-handovers/approval', [PackageCashHandoverController::class, 'approval'])->name('package_cash_handovers.approval');
  Route::post('/package-cash-handovers/{id}/approve', [PackageCashHandoverController::class, 'approve'])->name('package_cash_handovers.approve');
  Route::post('/package-cash-handovers/{id}/reject', [PackageCashHandoverController::class, 'reject'])->name('package_cash_handovers.reject');

  // Locker & Gear Routes
  Route::resource('locker_items', LockerItemController::class);
  Route::resource('gear_items', GearItemController::class);
  Route::resource('item_pricings', ItemPricingController::class);
  Route::resource('locker_gear_counters', LockerGearCounterController::class);
  Route::get('/locker-gear-counters/{id}/assign-users', [LockerGearCounterController::class, 'assignUsers'])->name('locker_gear_counters.assign_users');
  Route::post('/locker-gear-counters/{id}/update-users', [LockerGearCounterController::class, 'updateUsers'])->name('locker_gear_counters.update_users');
  Route::resource('locker_gear_tickets', LockerGearTicketController::class)->only(['index', 'create', 'store', 'show']);
  Route::get('/locker-gear-tickets/scan-camera', [LockerGearTicketController::class, 'scanCamera'])->name('locker_gear_tickets.scan_camera');
  Route::get('/locker-gear-tickets/scan/{ticket_number}', [LockerGearTicketController::class, 'scan'])->name('locker_gear_tickets.scan');
  Route::post('/locker-gear-tickets/check-out/{ticket_number}', [LockerGearTicketController::class, 'checkOut'])->name('locker_gear_tickets.check_out');
  Route::get('/locker-gear-reports', [LockerGearReportController::class, 'index'])->name('locker_gear_reports.index');
  Route::get('/locker-gear-reports/item', [LockerGearReportController::class, 'itemReport'])->name('locker_gear_reports.item_report');
  Route::get('/locker-gear-reports/stock', [LockerGearReportController::class, 'stockReport'])->name('locker_gear_reports.stock_report');
  Route::get('/locker-gear-reports/user', [LockerGearReportController::class, 'userReport'])->name('locker_gear_reports.user_report');
  Route::get('/locker-gear-reports/active', [LockerGearReportController::class, 'activeRentals'])->name('locker_gear_reports.active_rentals');
  Route::get('/locker-gear-reports/overdue', [LockerGearReportController::class, 'overdueReport'])->name('locker_gear_reports.overdue');
  Route::get('/locker-gear-cash-handovers', [LockerGearCashHandoverController::class, 'index'])->name('locker_gear_cash_handovers.index');
  Route::post('/locker-gear-cash-handovers', [LockerGearCashHandoverController::class, 'store'])->name('locker_gear_cash_handovers.store');
  Route::get('/locker-gear-cash-handovers/approval', [LockerGearCashHandoverController::class, 'approval'])->name('locker_gear_cash_handovers.approval');
  Route::post('/locker-gear-cash-handovers/{id}/approve', [LockerGearCashHandoverController::class, 'approve'])->name('locker_gear_cash_handovers.approve');
  Route::post('/locker-gear-cash-handovers/{id}/reject', [LockerGearCashHandoverController::class, 'reject'])->name('locker_gear_cash_handovers.reject');

  // HR & Payroll Routes
  Route::resource('permanent_employees', PermanentEmployeeController::class);
  Route::get('/permanent_employees/get_user_details/{id}', [PermanentEmployeeController::class, 'getUserDetails'])->name('permanent_employees.get_user_details');
  Route::resource('work_areas', WorkAreaController::class);
  Route::resource('daily_workers', DailyWorkerController::class);
  Route::get('/daily_workers/get_user_details/{id}', [DailyWorkerController::class, 'getUserDetails'])->name('daily_workers.get_user_details');
  Route::resource('contract_workers', ContractWorkerController::class);
  Route::get('/contract_workers/get_user_details/{id}', [ContractWorkerController::class, 'getUserDetails'])->name('contract_workers.get_user_details');
  Route::resource('salary_structures', SalaryStructureController::class);
  Route::resource('salaries', SalaryController::class);
  Route::get('/salaries/timeline/{user_id}', [SalaryController::class, 'timeline'])->name('salaries.timeline');
  Route::get('/salaries/{id}/lock', [SalaryController::class, 'lock'])->name('salaries.lock');
  Route::get('/salaries/{id}/unlock', [SalaryController::class, 'unlock'])->name('salaries.unlock');
  Route::resource('salary_revisions', SalaryRevisionController::class);
  Route::get('/salary_revisions/{id}/lock', [SalaryRevisionController::class, 'lock'])->name('salary_revisions.lock');
  Route::get('/salary_revisions/{id}/unlock', [SalaryRevisionController::class, 'unlock'])->name('salary_revisions.unlock');
  Route::get('/api/salaries/current/{userId}', [SalaryRevisionController::class, 'getCurrentSalary'])->name('api.salaries.current');
  Route::get('/payrolls/approval', [PayrollController::class, 'approval'])->name('payrolls.approval');
  Route::get('/payrolls/approved', [PayrollController::class, 'approved'])->name('payrolls.approved');
  Route::resource('payrolls', PayrollController::class);
  Route::get('/payrolls/get-salary-structure/{id}', [PayrollController::class, 'getSalaryStructure'])->name('payrolls.get_salary_structure')->withoutMiddleware('permissions');
  Route::post('/payrolls/get-attendance-summary', [PayrollController::class, 'getAttendanceSummary'])->name('payrolls.get_attendance_summary')->withoutMiddleware('permissions');
  Route::post('/payrolls/calculate-generated-salary', [PayrollController::class, 'calculateGeneratedSalary'])->name('payrolls.calculate_generated_salary')->withoutMiddleware('permissions');
  Route::get('/payrolls/{id}/submit', [PayrollController::class, 'submit'])->name('payrolls.submit');
  Route::get('/payrolls/{id}/approve', [PayrollController::class, 'approve'])->name('payrolls.approve');
  Route::post('/payrolls/{id}/return', [PayrollController::class, 'returnPayroll'])->name('payrolls.return');
  Route::resource('salary_disbursements', SalaryDisbursementController::class);
  Route::post('/salary_disbursements/process-payment', [SalaryDisbursementController::class, 'processPayment'])->name('salary_disbursements.process_payment');
  Route::get('/salary_disbursements/{id}/payslip', [SalaryDisbursementController::class, 'payslip'])->name('salary_disbursements.payslip');
  Route::get('/salary_disbursements/{id}/cancel', [SalaryDisbursementController::class, 'cancel'])->name('salary_disbursements.cancel');
  Route::get('/salary_disbursements/reports/payment-register', [SalaryDisbursementController::class, 'paymentRegister'])->name('salary_disbursements.payment_register');
  Route::get('/salary_disbursements/reports/cash-payment', [SalaryDisbursementController::class, 'cashPaymentReport'])->name('salary_disbursements.cash_payment');
  Route::resource('shifts', ShiftController::class);
  Route::resource('employee_shifts', EmployeeShiftController::class);
  Route::resource('fingerprint_logs', FingerprintLogController::class);
  Route::resource('fingerprint_sessions', FingerprintSessionController::class);
  Route::post('/fingerprint_sessions/process', [FingerprintSessionController::class, 'process'])->name('fingerprint_sessions.process');
  Route::get('/fingerprint_attendance', [FingerprintAttendanceController::class, 'index'])->name('fingerprint_attendance.index');
  Route::post('/fingerprint_attendance/generate', [FingerprintAttendanceController::class, 'generate'])->name('fingerprint_attendance.generate');
  Route::get('/fingerprint_attendance/history', [FingerprintAttendanceController::class, 'history'])->name('fingerprint_attendance.history');
  Route::get('/daily-attendance', [DailyAttendanceController::class, 'index'])->name('daily_attendance.index');
  Route::post('/daily-attendance/save', [DailyAttendanceController::class, 'save'])->name('daily_attendance.save');
  Route::post('/daily-attendance/load', [DailyAttendanceController::class, 'load'])->name('daily_attendance.load');
  Route::post('/daily-attendance/calculate', [DailyAttendanceController::class, 'calculate'])->name('daily_attendance.calculate');
  Route::post('/daily-attendance/store', [DailyAttendanceController::class, 'store'])->name('daily_attendance.store');
  Route::post('/daily-attendance/load-day', [DailyAttendanceController::class, 'loadDay'])->name('daily_attendance.load_day');
  Route::post('/daily-attendance/get-assigned-shift', [DailyAttendanceController::class, 'getAssignedShift'])->name('daily_attendance.get_assigned_shift');
  
  // Attendance Reports
  Route::get('/attendance-reports', [AttendanceReportController::class, 'index'])->name('attendance_reports.index');
  Route::get('/attendance-reports/employee-daily', [AttendanceReportController::class, 'employeeDaily'])->name('attendance_reports.employee_daily');
  Route::get('/attendance-reports/employee-daily/print', [AttendanceReportController::class, 'employeeDailyPrint'])->name('attendance_reports.employee_daily_print');
  Route::get('/attendance-reports/employee-monthly', [AttendanceReportController::class, 'employeeMonthly'])->name('attendance_reports.employee_monthly');
  Route::get('/attendance-reports/employee-monthly/print', [AttendanceReportController::class, 'employeeMonthlyPrint'])->name('attendance_reports.employee_monthly_print');
  Route::get('/attendance-reports/daily-register', [AttendanceReportController::class, 'dailyRegister'])->name('attendance_reports.daily_register');
  Route::get('/attendance-reports/daily-register/print', [AttendanceReportController::class, 'dailyRegisterPrint'])->name('attendance_reports.daily_register_print');
  Route::get('/attendance-reports/monthly-register', [AttendanceReportController::class, 'monthlyRegister'])->name('attendance_reports.monthly_register');
  Route::get('/attendance-reports/late-report', [AttendanceReportController::class, 'lateReport'])->name('attendance_reports.late_report');
  Route::get('/attendance-reports/department-report', [AttendanceReportController::class, 'departmentReport'])->name('attendance_reports.department_report');
  Route::get('/attendance-reports/shift-report', [AttendanceReportController::class, 'shiftReport'])->name('attendance_reports.shift_report');
  Route::get('/salary_disbursements/reports/bank-payment', [SalaryDisbursementController::class, 'bankPaymentReport'])->name('salary_disbursements.bank_payment');
  Route::get('/salary_disbursements/reports/monthly-register', [SalaryDisbursementController::class, 'monthlySalaryRegister'])->name('salary_disbursements.monthly_register');
  Route::get('/attendances/daily', [AttendanceController::class, 'daily'])->name('attendances.daily');
  Route::post('/attendances/daily-store', [AttendanceController::class, 'dailyStore'])->name('attendances.daily_store');
  Route::post('/attendances/load-day', [AttendanceController::class, 'loadDay'])->name('attendances.load_day');
  Route::get('/attendances/bulk', [AttendanceController::class, 'bulk'])->name('attendances.bulk');
  Route::post('/attendances/bulk-store', [AttendanceController::class, 'bulkStore'])->name('attendances.bulk_store');
  Route::post('/attendances/load-month', [AttendanceController::class, 'loadMonth'])->name('attendances.load_month');
  Route::post('/attendances/{id}/update-day', [AttendanceController::class, 'updateDay'])->name('attendances.update_day');
  Route::get('/attendances/{id}/lock', [AttendanceController::class, 'lock'])->name('attendances.lock');
  Route::get('/attendances/{id}/unlock', [AttendanceController::class, 'unlock'])->name('attendances.unlock');
  Route::resource('attendances', AttendanceController::class);


});
