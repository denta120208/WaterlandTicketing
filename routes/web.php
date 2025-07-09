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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/report', 'Reports\ReportController@index')->name('report');
Route::get('/constraction_report', 'Reports\ReportController@constraction_report')->name('constraction_report');

// Ticket Purchase
Route::get('/ticket_purchase', 'Sales\TicketPurchase\TicketPurchaseController@index')->name('ticket_purchase');
Route::get('/buy_ticket_purchase', 'Sales\TicketPurchase\TicketPurchaseController@buyTicketPurchase')->name('buy_ticket_purchase');
Route::get('/get_ticket_price_by_id_group/{id}',[
    'as' => 'get_ticket_price_by_id_group',
    'uses' => 'Sales\TicketPurchase\TicketPurchaseController@getTicketPriceByIdGroup'
]);
Route::get('/get_ticket_price_by_id_price/{id}',[
    'as' => 'get_ticket_price_by_id_price',
    'uses' => 'Sales\TicketPurchase\TicketPurchaseController@getTicketPriceByIdPrice'
]);
Route::post('/save_ticket_purchase',[
    'as' => 'save_ticket_purchase',
    'uses' => 'Sales\TicketPurchase\TicketPurchaseController@saveTicketPurchase'
]);
Route::get('/print_ticket_purchase/{id}',[
    'as' => 'print_ticket_purchase',
    'uses' => 'Sales\TicketPurchase\TicketPurchaseController@printTicketPurchase'
]);
Route::get('/print_ticket_purchase_one/{id}',[
    'as' => 'print_ticket_purchase_one',
    'uses' => 'Sales\TicketPurchase\TicketPurchaseController@printTicketPurchaseOne'
]);
Route::get('/print_receipt_ticket_purchase/{id}',[
    'as' => 'print_receipt_ticket_purchase',
    'uses' => 'Sales\TicketPurchase\TicketPurchaseController@printReceiptTicketPurchase'
]);
Route::get('/cancel_ticket_purchase/{id}',[
    'as' => 'cancel_ticket_purchase',
    'uses' => 'Sales\TicketPurchase\TicketPurchaseController@cancelTicketPurchase'
]);
Route::get('/get_promo_ticket_purchase/{id}/{id2}/{id3}/{id4}/{id5}/{id6}',[
    'as' => 'get_promo_ticket_purchase',
    'uses' => 'Sales\TicketPurchase\TicketPurchaseController@getPromoTicketPurchase'
]);
Route::get('/get_promo_by_id_ticket_purchase/{id}',[
    'as' => 'get_promo_by_id_ticket_purchase',
    'uses' => 'Sales\TicketPurchase\TicketPurchaseController@getPromoByIdTicketPurchase'
]);

// Scan QRCode Ticket
Route::get('/scan_ticket_purchase', 'Scanning\ScanTicketPurchase\ScanTicketPurchaseController@index')->name('scan_ticket_purchase');
Route::get('/scanning_ticket_purchase/{id}',[
    'as' => 'scanning_ticket_purchase',
    'uses' => 'Scanning\ScanTicketPurchase\ScanTicketPurchaseController@scanningTicketPurchase'
]);
Route::post('/change_camera_setting',[
    'as' => 'change_camera_setting',
    'uses' => 'Scanning\ScanTicketPurchase\ScanTicketPurchaseController@changeCameraSetting'
]);
Route::post('/input_qr_number',[
    'as' => 'input_qr_number',
    'uses' => 'Scanning\ScanTicketPurchase\ScanTicketPurchaseController@inputQRNumber'
]);

// Rental Swimming Equipment
Route::get('/swimming_equipment', 'Rental\SwimmingEquipment\SwimmingEquipmentController@index')->name('swimming_equipment');
Route::get('/rental_swimming_equipment', 'Rental\SwimmingEquipment\SwimmingEquipmentController@rentalSwimmingEquipment')->name('rental_swimming_equipment');
Route::get('/get_perlengkapan_renang_price_by_id/{id}',[
    'as' => 'get_perlengkapan_renang_price_by_id',
    'uses' => 'Rental\SwimmingEquipment\SwimmingEquipmentController@getPerlengkapanRenangPriceById'
]);
Route::post('/save_rental_swimming_equipment',[
    'as' => 'save_rental_swimming_equipment',
    'uses' => 'Rental\SwimmingEquipment\SwimmingEquipmentController@saveSwimmingEquipment'
]);
Route::get('/retur_rental_swimming_equipment/{id}',[
    'as' => 'retur_rental_swimming_equipment',
    'uses' => 'Rental\SwimmingEquipment\SwimmingEquipmentController@returSwimmingEquipment'
]);
Route::get('/print_rental_swimming_equipment/{id}',[
    'as' => 'print_rental_swimming_equipment',
    'uses' => 'Rental\SwimmingEquipment\SwimmingEquipmentController@printSwimmingEquipment'
]);
Route::get('/cancel_rental_swimming_equipment/{id}',[
    'as' => 'cancel_rental_swimming_equipment',
    'uses' => 'Rental\SwimmingEquipment\SwimmingEquipmentController@cancelSwimmingEquipment'
]);

// Rental Locker
Route::get('/locker', 'Rental\Locker\LockerController@index')->name('locker');
Route::get('/rental_locker', 'Rental\Locker\LockerController@rentalLocker')->name('rental_locker');
Route::get('/get_locker_price_by_id/{id}',[
    'as' => 'get_locker_price_by_id',
    'uses' => 'Rental\Locker\LockerController@getLockerPriceById'
]);
Route::post('/save_rental_locker',[
    'as' => 'save_rental_locker',
    'uses' => 'Rental\Locker\LockerController@saveLocker'
]);
Route::get('/retur_rental_locker/{id}',[
    'as' => 'retur_rental_locker',
    'uses' => 'Rental\Locker\LockerController@returLocker'
]);
Route::get('/print_rental_locker/{id}',[
    'as' => 'print_rental_locker',
    'uses' => 'Rental\Locker\LockerController@printLocker'
]);
Route::get('/cancel_rental_locker/{id}',[
    'as' => 'cancel_rental_locker',
    'uses' => 'Rental\Locker\LockerController@cancelLocker'
]);

// Rental Equipment
Route::get('/rentEquipment', 'Rental\Equipment\EquipmentController@index')->name('rentEquipment');
Route::get('/rental_equipment', 'Rental\Equipment\EquipmentController@rentalEquipment')->name('rental_equipment');
Route::get('/get_equipment_price_by_id/{id}',[
    'as' => 'get_equipment_price_by_id',
    'uses' => 'Rental\Equipment\EquipmentController@getEquipmentPriceById'
]);
Route::post('/save_rental_equipment',[
    'as' => 'save_rental_equipment',
    'uses' => 'Rental\Equipment\EquipmentController@saveEquipment'
]);
Route::post('/retur_rental_equipment',[
    'as' => 'retur_rental_equipment',
    'uses' => 'Rental\Equipment\EquipmentController@returEquipment'
]);
Route::get('/print_rental_equipment/{id}',[
    'as' => 'print_rental_equipment',
    'uses' => 'Rental\Equipment\EquipmentController@printEquipment'
]);
Route::get('/cancel_rental_equipment/{id}',[
    'as' => 'cancel_rental_equipment',
    'uses' => 'Rental\Equipment\EquipmentController@cancelEquipment'
]);
Route::get('/get_promo_equipment/{id}/{id2}/{id3}/{id4}/{id5}',[
    'as' => 'get_promo_equipment',
    'uses' => 'Rental\Equipment\EquipmentController@getPromoEquipment'
]);
Route::get('/get_promo_by_id_equipment/{id}/{id2}',[
    'as' => 'get_promo_by_id_equipment',
    'uses' => 'Rental\Equipment\EquipmentController@getPromoByIdEquipment'
]);

// Visitors Counter
Route::get('/visitors_counter', 'Scanning\VisitorsCounter\VisitorsCounterController@index')->name('visitors_counter');
Route::get('/get_visitors_counter',[
    'as' => 'get_visitors_counter',
    'uses' => 'Scanning\VisitorsCounter\VisitorsCounterController@getVisitorsCounter'
]);
Route::get('/send_visitors_counter_plus/{param}',[
    'as' => 'send_visitors_counter_plus',
    'uses' => 'Scanning\VisitorsCounter\VisitorsCounterController@sendVisitorsCounterPlus'
]);
Route::get('/send_visitors_counter_minus/{param}',[
    'as' => 'send_visitors_counter_minus',
    'uses' => 'Scanning\VisitorsCounter\VisitorsCounterController@sendVisitorsCounterMinus'
]);

// Master Data Periode Membership
Route::get('/periodeMembership', 'MasterData\PeriodeMembership\PeriodeMembershipController@index')->name('periodeMembership');
Route::post('/listTblPeriodeMembership', 'MasterData\PeriodeMembership\PeriodeMembershipController@listTblPeriodeMembership')->name('listTblPeriodeMembership');
Route::get('/get_periode_membership/{id}',[
    'as' => 'get_periode_membership',
    'uses' => 'MasterData\PeriodeMembership\PeriodeMembershipController@getPeriodeMembership'
]);
Route::post('/save_periode_membership',[
    'as' => 'save_periode_membership',
    'uses' => 'MasterData\PeriodeMembership\PeriodeMembershipController@savePeriodeMembership'
]);
Route::post('/edit_periode_membership',[
    'as' => 'edit_periode_membership',
    'uses' => 'MasterData\PeriodeMembership\PeriodeMembershipController@editPeriodeMembership'
]);
Route::get('/delete_periode_membership/{id}',[
    'as' => 'delete_periode_membership',
    'uses' => 'MasterData\PeriodeMembership\PeriodeMembershipController@deletePeriodeMembership'
]);

// Master Data Group Type Membership
Route::get('/groupTypeMembership', 'MasterData\GroupTypeMembership\GroupTypeMembershipController@index')->name('groupTypeMembership');
Route::post('/listTblGroupTypeMembership', 'MasterData\GroupTypeMembership\GroupTypeMembershipController@listTblGroupTypeMembership')->name('listTblGroupTypeMembership');
Route::get('/get_group_type_membership/{id}',[
    'as' => 'get_group_type_membership',
    'uses' => 'MasterData\GroupTypeMembership\GroupTypeMembershipController@getGroupTypeMembership'
]);
Route::post('/save_group_type_membership',[
    'as' => 'save_group_type_membership',
    'uses' => 'MasterData\GroupTypeMembership\GroupTypeMembershipController@saveGroupTypeMembership'
]);
Route::post('/edit_group_type_membership',[
    'as' => 'edit_group_type_membership',
    'uses' => 'MasterData\GroupTypeMembership\GroupTypeMembershipController@editGroupTypeMembership'
]);
Route::get('/delete_group_type_membership/{id}',[
    'as' => 'delete_group_type_membership',
    'uses' => 'MasterData\GroupTypeMembership\GroupTypeMembershipController@deleteGroupTypeMembership'
]);

// Master Data Group Membership
Route::get('/groupMembership', 'MasterData\GroupMembership\GroupMembershipController@index')->name('groupMembership');
Route::post('/listTblGroupMembership', 'MasterData\GroupMembership\GroupMembershipController@listTblGroupMembership')->name('listTblGroupMembership');
Route::get('/get_group_membership/{id}',[
    'as' => 'get_group_membership',
    'uses' => 'MasterData\GroupMembership\GroupMembershipController@getGroupMembership'
]);
Route::post('/save_group_membership',[
    'as' => 'save_group_membership',
    'uses' => 'MasterData\GroupMembership\GroupMembershipController@saveGroupMembership'
]);
Route::post('/edit_group_membership',[
    'as' => 'edit_group_membership',
    'uses' => 'MasterData\GroupMembership\GroupMembershipController@editGroupMembership'
]);
Route::get('/delete_group_membership/{id}',[
    'as' => 'delete_group_membership',
    'uses' => 'MasterData\GroupMembership\GroupMembershipController@deleteGroupMembership'
]);

// Master Data Price Membership
Route::get('/priceMembership', 'MasterData\PriceMembership\PriceMembershipController@index')->name('priceMembership');
Route::post('/listTblPriceMembership', 'MasterData\PriceMembership\PriceMembershipController@listTblPriceMembership')->name('listTblPriceMembership');
Route::get('/add_new_price_membership', 'MasterData\PriceMembership\PriceMembershipController@addNewPriceMembership')->name('add_new_price_membership');
Route::post('/save_price_membership',[
    'as' => 'save_price_membership',
    'uses' => 'MasterData\PriceMembership\PriceMembershipController@savePriceMembership'
]);
Route::get('/edit_view_price_membership/{id}',[
    'as' => 'edit_view_price_membership',
    'uses' => 'MasterData\PriceMembership\PriceMembershipController@editViewPriceMembership'
]);
Route::post('/edit_price_membership',[
    'as' => 'edit_price_membership',
    'uses' => 'MasterData\PriceMembership\PriceMembershipController@editPriceMembership'
]);
Route::get('/delete_price_membership/{id}',[
    'as' => 'delete_price_membership',
    'uses' => 'MasterData\PriceMembership\PriceMembershipController@deletePriceMembership'
]);

// Master Data Payment Method
Route::get('/paymentMethod', 'MasterData\PaymentMethod\PaymentMethodController@index')->name('paymentMethod');
Route::post('/listTblPaymentMethod', 'MasterData\PaymentMethod\PaymentMethodController@listTblPaymentMethod')->name('listTblPaymentMethod');
Route::get('/add_new_payment_method', 'MasterData\PaymentMethod\PaymentMethodController@addNewPaymentMethod')->name('add_new_payment_method');
Route::post('/save_payment_method',[
    'as' => 'save_payment_method',
    'uses' => 'MasterData\PaymentMethod\PaymentMethodController@savePaymentMethod'
]);
Route::get('/edit_view_payment_method/{id}',[
    'as' => 'edit_view_payment_method',
    'uses' => 'MasterData\PaymentMethod\PaymentMethodController@editViewPaymentMethod'
]);
Route::post('/edit_payment_method',[
    'as' => 'edit_payment_method',
    'uses' => 'MasterData\PaymentMethod\PaymentMethodController@editPaymentMethod'
]);
Route::get('/delete_payment_method/{id}',[
    'as' => 'delete_payment_method',
    'uses' => 'MasterData\PaymentMethod\PaymentMethodController@deletePaymentMethod'
]);

// Master Data Ticket Group
Route::get('/ticketGroup', 'MasterData\TicketGroup\TicketGroupController@index')->name('ticketGroup');
Route::post('/listTblTicketGroup', 'MasterData\TicketGroup\TicketGroupController@listTblTicketGroup')->name('listTblTicketGroup');
Route::get('/add_new_ticket_group', 'MasterData\TicketGroup\TicketGroupController@addNewTicketGroup')->name('add_new_ticket_group');
Route::post('/save_ticket_group',[
    'as' => 'save_ticket_group',
    'uses' => 'MasterData\TicketGroup\TicketGroupController@saveTicketGroup'
]);
Route::get('/edit_view_ticket_group/{id}',[
    'as' => 'edit_view_ticket_group',
    'uses' => 'MasterData\TicketGroup\TicketGroupController@editViewTicketGroup'
]);
Route::post('/edit_ticket_group',[
    'as' => 'edit_ticket_group',
    'uses' => 'MasterData\TicketGroup\TicketGroupController@editTicketGroup'
]);
Route::get('/delete_ticket_group/{id}',[
    'as' => 'delete_ticket_group',
    'uses' => 'MasterData\TicketGroup\TicketGroupController@deleteTicketGroup'
]);

// Master Data Ticket Price
Route::get('/ticketPrice', 'MasterData\TicketPrice\TicketPriceController@index')->name('ticketPrice');
Route::post('/listTblTicketPrice', 'MasterData\TicketPrice\TicketPriceController@listTblTicketPrice')->name('listTblTicketPrice');
Route::get('/add_new_ticket_price', 'MasterData\TicketPrice\TicketPriceController@addNewTicketPrice')->name('add_new_ticket_price');
Route::post('/save_ticket_price',[
    'as' => 'save_ticket_price',
    'uses' => 'MasterData\TicketPrice\TicketPriceController@saveTicketPrice'
]);
Route::get('/edit_view_ticket_price/{id}',[
    'as' => 'edit_view_ticket_price',
    'uses' => 'MasterData\TicketPrice\TicketPriceController@editViewTicketPrice'
]);
Route::post('/edit_ticket_price',[
    'as' => 'edit_ticket_price',
    'uses' => 'MasterData\TicketPrice\TicketPriceController@editTicketPrice'
]);
Route::get('/delete_ticket_price/{id}',[
    'as' => 'delete_ticket_price',
    'uses' => 'MasterData\TicketPrice\TicketPriceController@deleteTicketPrice'
]);

// Master Data Equipment
Route::get('/equipment', 'MasterData\Equipment\EquipmentController@index')->name('equipment');
Route::post('/listTblEquipment', 'MasterData\Equipment\EquipmentController@listTblEquipment')->name('listTblEquipment');
Route::get('/add_new_equipment', 'MasterData\Equipment\EquipmentController@addNewEquipment')->name('add_new_equipment');
Route::post('/save_equipment',[
    'as' => 'save_equipment',
    'uses' => 'MasterData\Equipment\EquipmentController@saveEquipment'
]);
Route::get('/edit_view_equipment/{id}',[
    'as' => 'edit_view_equipment',
    'uses' => 'MasterData\Equipment\EquipmentController@editViewEquipment'
]);
Route::post('/edit_equipment',[
    'as' => 'edit_equipment',
    'uses' => 'MasterData\Equipment\EquipmentController@editEquipment'
]);
Route::get('/delete_equipment/{id}',[
    'as' => 'delete_equipment',
    'uses' => 'MasterData\Equipment\EquipmentController@deleteEquipment'
]);

// Master Data Equipment Category
Route::get('/equipmentCategory', 'MasterData\EquipmentCategory\EquipmentCategoryController@index')->name('equipmentCategory');
Route::post('/listTblEquipmentCategory', 'MasterData\EquipmentCategory\EquipmentCategoryController@listTblEquipmentCategory')->name('listTblEquipmentCategory');
Route::get('/add_new_equipment_category', 'MasterData\EquipmentCategory\EquipmentCategoryController@addNewEquipmentCategory')->name('add_new_equipment_category');
Route::post('/save_equipment_category',[
    'as' => 'save_equipment_category',
    'uses' => 'MasterData\EquipmentCategory\EquipmentCategoryController@saveEquipmentCategory'
]);
Route::get('/edit_view_equipment_category/{id}',[
    'as' => 'edit_view_equipment_category',
    'uses' => 'MasterData\EquipmentCategory\EquipmentCategoryController@editViewEquipmentCategory'
]);
Route::post('/edit_equipment_category',[
    'as' => 'edit_equipment_category',
    'uses' => 'MasterData\EquipmentCategory\EquipmentCategoryController@editEquipmentCategory'
]);
Route::get('/delete_equipment_category/{id}',[
    'as' => 'delete_equipment_category',
    'uses' => 'MasterData\EquipmentCategory\EquipmentCategoryController@deleteEquipmentCategory'
]);

// Master Data Promo Equipment
Route::get('/promoEquipment', 'MasterData\PromoEquipment\PromoEquipmentController@index')->name('promoEquipment');
Route::get('/add_new_promo_equipment', 'MasterData\PromoEquipment\PromoEquipmentController@addNewPromoEquipment')->name('add_new_promo_equipment');
Route::post('/save_promo_equipment',[
    'as' => 'save_promo_equipment',
    'uses' => 'MasterData\PromoEquipment\PromoEquipmentController@savePromoEquipment'
]);
Route::get('/edit_view_promo_equipment/{id}',[
    'as' => 'edit_view_promo_equipment',
    'uses' => 'MasterData\PromoEquipment\PromoEquipmentController@editViewPromoEquipment'
]);
Route::post('/edit_promo_equipment',[
    'as' => 'edit_promo_equipment',
    'uses' => 'MasterData\PromoEquipment\PromoEquipmentController@editPromoEquipment'
]);
Route::get('/delete_promo_equipment/{id}',[
    'as' => 'delete_promo_equipment',
    'uses' => 'MasterData\PromoEquipment\PromoEquipmentController@deletePromoEquipment'
]);
Route::get('/terminate_promo_equipment/{id}',[
    'as' => 'terminate_promo_equipment',
    'uses' => 'MasterData\PromoEquipment\PromoEquipmentController@terminatePromoEquipment'
]);
Route::get('/appr_smm_promo_equipment/{id}',[
    'as' => 'appr_smm_promo_equipment',
    'uses' => 'MasterData\PromoEquipment\PromoEquipmentController@apprSMMPromoEquipment'
]);
Route::get('/appr_gm_promo_equipment/{id}',[
    'as' => 'appr_gm_promo_equipment',
    'uses' => 'MasterData\PromoEquipment\PromoEquipmentController@apprGMPromoEquipment'
]);
Route::get('/unappr_smm_promo_equipment/{id}',[
    'as' => 'unappr_smm_promo_equipment',
    'uses' => 'MasterData\PromoEquipment\PromoEquipmentController@unapprSMMPromoEquipment'
]);
Route::get('/unappr_gm_promo_equipment/{id}',[
    'as' => 'unappr_gm_promo_equipment',
    'uses' => 'MasterData\PromoEquipment\PromoEquipmentController@unapprGMPromoEquipment'
]);

// Master Data Promo Ticket
Route::get('/promo', 'MasterData\Promo\PromoController@index')->name('promo');
Route::get('/add_new_promo', 'MasterData\Promo\PromoController@addNewPromo')->name('add_new_promo');
Route::get('/get_ticket_price_promo/{id}',[
    'as' => 'get_ticket_price_promo',
    'uses' => 'MasterData\Promo\PromoController@getTicketPrice'
]);
Route::post('/save_promo',[
    'as' => 'save_promo',
    'uses' => 'MasterData\Promo\PromoController@savePromo'
]);
Route::get('/edit_view_promo/{id}',[
    'as' => 'edit_view_promo',
    'uses' => 'MasterData\Promo\PromoController@editViewPromo'
]);
Route::post('/edit_promo',[
    'as' => 'edit_promo',
    'uses' => 'MasterData\Promo\PromoController@editPromo'
]);
Route::get('/delete_promo/{id}',[
    'as' => 'delete_promo',
    'uses' => 'MasterData\Promo\PromoController@deletePromo'
]);
Route::get('/terminate_promo/{id}',[
    'as' => 'terminate_promo',
    'uses' => 'MasterData\Promo\PromoController@terminatePromo'
]);
Route::get('/appr_smm_promo/{id}',[
    'as' => 'appr_smm_promo',
    'uses' => 'MasterData\Promo\PromoController@apprSMMPromo'
]);
Route::get('/appr_gm_promo/{id}',[
    'as' => 'appr_gm_promo',
    'uses' => 'MasterData\Promo\PromoController@apprGMPromo'
]);
Route::get('/unappr_smm_promo/{id}',[
    'as' => 'unappr_smm_promo',
    'uses' => 'MasterData\Promo\PromoController@unapprSMMPromo'
]);
Route::get('/unappr_gm_promo/{id}',[
    'as' => 'unappr_gm_promo',
    'uses' => 'MasterData\Promo\PromoController@unapprGMPromo'
]);

// Master Data Holiday
Route::get('/holiday', 'MasterData\Holiday\HolidayController@index')->name('holiday');
Route::post('/listTblHoliday', 'MasterData\Holiday\HolidayController@listTblHoliday')->name('listTblHoliday');
Route::get('/add_new_holiday', 'MasterData\Holiday\HolidayController@addNewHoliday')->name('add_new_holiday');
Route::post('/save_holiday',[
    'as' => 'save_holiday',
    'uses' => 'MasterData\Holiday\HolidayController@saveHoliday'
]);
Route::get('/edit_view_holiday/{id}',[
    'as' => 'edit_view_holiday',
    'uses' => 'MasterData\Holiday\HolidayController@editViewHoliday'
]);
Route::post('/edit_holiday',[
    'as' => 'edit_holiday',
    'uses' => 'MasterData\Holiday\HolidayController@editHoliday'
]);
Route::get('/delete_holiday/{id}',[
    'as' => 'delete_holiday',
    'uses' => 'MasterData\Holiday\HolidayController@deleteHoliday'
]);

// Master Data Tenant
Route::get('/master_data/tenant/viewlistdatatenant',[
    'as' => 'masterdata.tenant.viewlistdatatenant',
    'uses' => 'MasterData\Tenant@viewListDataTenant'
]);
Route::get('/master_data/tenant/viewadddatatenant',[
    'as' => 'masterdata.tenant.viewadddatatenant',
    'uses' => 'MasterData\Tenant@viewFormAddDataTenant'
]);
Route::get('/master_data/tenant/deletedatatenant/{id}',[
    'as' => 'masterdata.tenant.deletedatatenant',
    'uses' => 'MasterData\Tenant@deleteDataTenant'
]);
Route::get('/master_data/tenant/view_edit_data/{id}',[
    'as' => 'masterdata.tenant.vieweditdata',
    'uses' => 'MasterData\Tenant@viewFormEditDataTenant'
]);
Route::post('/master_data/tenant/adddatatenant', [
    "before" => "csrf",
    'as'     => 'masterdata.tenant.adddatatenant',
    'uses'   => 'MasterData\Tenant@saveDataTenant'
]);
Route::post('/master_data/tenant/deleteitemaddresstenant',[
    'as' => 'masterdata.tenant.deleteitemaddresstenant',
    'uses' => 'MasterData\Tenant@deleteItemAddressTenant'
]);
Route::post('/master_data/tenant/getitemaddresstenant',[
    'as' => 'masterdata.tenant.getitemaddresstenant',
    'uses' => 'MasterData\Tenant@getItemAddressTenant'
]);
Route::post('/master_data/tenant/saveaddresstenant/', [
    'as' => 'masterdata.tenant.saveaddresstenant',
    'uses' => 'MasterData\Tenant@saveAddressTenant'
]);
Route::post('/master_data/tenant/editdatatenant', [
    "before" => "csrf",
    'as'     => 'masterdata.tenant.editdatatenant',
    'uses'   => 'MasterData\Tenant@saveEditDataTenant'
]);

// Report Pengunjung (Visitors)
Route::get('/report_visitors', 'Sales\Report\ReportVisitorsController@index')->name('report_visitors');
Route::get('/view_report_visitors/{param1}/{param2}',[
    'as' => 'view_report_visitors',
    'uses' => 'Sales\Report\ReportVisitorsController@viewReportVisitors'
]);
Route::get('/view_report_visitors_excel/{param1}/{param2}',[
    'as' => 'view_report_visitors_excel',
    'uses' => 'Sales\Report\ReportVisitorsController@viewReportVisitorsExcel'
]);
Route::get('/view_report_visitors_details_excel/{param1}/{param2}',[
    'as' => 'view_report_visitors_excel',
    'uses' => 'Sales\Report\ReportVisitorsController@viewReportVisitorsDetailsExcel'
]);
Route::get('/view_report_visitors_print/{param1}/{param2}',[
    'as' => 'view_report_visitors_print',
    'uses' => 'Sales\Report\ReportVisitorsController@viewReportVisitorsPrint'
]);
Route::get('/view_report_visitors_details_print/{param1}/{param2}',[
    'as' => 'view_report_visitors_print',
    'uses' => 'Sales\Report\ReportVisitorsController@viewReportVisitorsDetailsPrint'
]);

// Report Pengunjung By Time (Visitors Time)
Route::get('/report_visitors_by_time', 'Sales\Report\ReportVisitorsByTimeController@index')->name('report_visitors_by_time');
Route::get('/view_report_visitors_by_time/{param1}/{param2}/{param3}',[
    'as' => 'view_report_visitors_by_time',
    'uses' => 'Sales\Report\ReportVisitorsByTimeController@viewReportVisitorsByTime'
]);
Route::get('/view_report_visitors_by_time_excel/{param1}/{param2}/{param3}',[
    'as' => 'view_report_visitors_by_time_excel',
    'uses' => 'Sales\Report\ReportVisitorsByTimeController@viewReportVisitorsByTimeExcel'
]);
Route::get('/view_report_visitors_by_time_print/{param1}/{param2}/{param3}',[
    'as' => 'view_report_visitors_by_time_print',
    'uses' => 'Sales\Report\ReportVisitorsByTimeController@viewReportVisitorsByTimePrint'
]);

// Report Rental Swimming Equipment
Route::get('/report_swimming_equipment', 'Rental\Report\ReportSwimmingEquipmentController@index')->name('report_swimming_equipment');
Route::get('/view_report_swimming_equipment/{param1}/{param2}',[
    'as' => 'view_report_swimming_equipment',
    'uses' => 'Rental\Report\ReportSwimmingEquipmentController@viewReportSwimmingEquipment'
]);
Route::get('/view_report_swimming_equipment_excel/{param1}/{param2}',[
    'as' => 'view_report_swimming_equipment_excel',
    'uses' => 'Rental\Report\ReportSwimmingEquipmentController@viewReportSwimmingEquipmentExcel'
]);
Route::get('/view_report_swimming_equipment_details_excel/{param1}/{param2}',[
    'as' => 'view_report_swimming_equipment_excel',
    'uses' => 'Rental\Report\ReportSwimmingEquipmentController@viewReportSwimmingEquipmentDetailsExcel'
]);
Route::get('/view_report_swimming_equipment_print/{param1}/{param2}',[
    'as' => 'view_report_swimming_equipment_print',
    'uses' => 'Rental\Report\ReportSwimmingEquipmentController@viewReportSwimmingEquipmentPrint'
]);
Route::get('/view_report_swimming_equipment_details_print/{param1}/{param2}',[
    'as' => 'view_report_swimming_equipment_print',
    'uses' => 'Rental\Report\ReportSwimmingEquipmentController@viewReportSwimmingEquipmentDetailsPrint'
]);

// Report Rental Locker
Route::get('/report_locker', 'Rental\Report\ReportLockerController@index')->name('report_locker');
Route::get('/view_report_locker/{param1}/{param2}',[
    'as' => 'view_report_locker',
    'uses' => 'Rental\Report\ReportLockerController@viewReportLocker'
]);
Route::get('/view_report_locker_excel/{param1}/{param2}',[
    'as' => 'view_report_locker_excel',
    'uses' => 'Rental\Report\ReportLockerController@viewReportLockerExcel'
]);
Route::get('/view_report_locker_details_excel/{param1}/{param2}',[
    'as' => 'view_report_locker_excel',
    'uses' => 'Rental\Report\ReportLockerController@viewReportLockerDetailsExcel'
]);
Route::get('/view_report_locker_print/{param1}/{param2}',[
    'as' => 'view_report_locker_print',
    'uses' => 'Rental\Report\ReportLockerController@viewReportLockerPrint'
]);
Route::get('/view_report_locker_details_print/{param1}/{param2}',[
    'as' => 'view_report_locker_print',
    'uses' => 'Rental\Report\ReportLockerController@viewReportLockerDetailsPrint'
]);

// Report Revenue
Route::get('/report_revenue', 'Sales\Report\ReportRevenueController@index')->name('report_revenue');
Route::get('/view_report_revenue/{param1}/{param2}',[
    'as' => 'view_report_revenue',
    'uses' => 'Sales\Report\ReportRevenueController@viewReportRevenue'
]);
Route::get('/view_report_revenue_excel/{param1}/{param2}',[
    'as' => 'view_report_revenue_excel',
    'uses' => 'Sales\Report\ReportRevenueController@viewReportRevenueExcel'
]);
Route::get('/view_report_revenue_print/{param1}/{param2}',[
    'as' => 'view_report_revenue_print',
    'uses' => 'Sales\Report\ReportRevenueController@viewReportRevenuePrint'
]);

// Report Rental Equipment
Route::get('/report_equipment', 'Rental\Report\ReportEquipmentController@index')->name('report_equipment');
Route::get('/view_report_equipment/{param1}/{param2}',[
    'as' => 'view_report_equipment',
    'uses' => 'Rental\Report\ReportEquipmentController@viewReportEquipment'
]);
Route::get('/view_report_equipment_excel/{param1}/{param2}',[
    'as' => 'view_report_equipment_excel',
    'uses' => 'Rental\Report\ReportEquipmentController@viewReportEquipmentExcel'
]);
Route::get('/view_report_equipment_details_excel/{param1}/{param2}',[
    'as' => 'view_report_equipment_details_excel',
    'uses' => 'Rental\Report\ReportEquipmentController@viewReportEquipmentDetailsExcel'
]);
Route::get('/view_report_equipment_print/{param1}/{param2}',[
    'as' => 'view_report_equipment_print',
    'uses' => 'Rental\Report\ReportEquipmentController@viewReportEquipmentPrint'
]);
Route::get('/view_report_equipment_details_print/{param1}/{param2}',[
    'as' => 'view_report_equipment_details_print',
    'uses' => 'Rental\Report\ReportEquipmentController@viewReportEquipmentDetailsPrint'
]);

// Report Revenue Ticket & Equipment By Payment Method
Route::get('/report_rev_ticket_by_payment_method', 'Sales\Report\ReportRevenueTicketByPaymentMethodController@index')->name('report_rev_ticket_by_payment_method');
Route::get('/view_report_rev_ticket_by_payment_method/{param1}/{param2}/{param3}',[
    'as' => 'view_report_rev_ticket_by_payment_method',
    'uses' => 'Sales\Report\ReportRevenueTicketByPaymentMethodController@viewReportRevenueTicketByPaymentMethod'
]);
Route::get('/view_report_rev_ticket_by_payment_method_excel/{param1}/{param2}/{param3}',[
    'as' => 'view_report_rev_ticket_by_payment_method_excel',
    'uses' => 'Sales\Report\ReportRevenueTicketByPaymentMethodController@viewReportRevenueTicketByPaymentMethodExcel'
]);
Route::get('/view_report_rev_ticket_by_payment_method_print/{param1}/{param2}/{param3}',[
    'as' => 'view_report_rev_ticket_by_payment_method_print',
    'uses' => 'Sales\Report\ReportRevenueTicketByPaymentMethodController@viewReportRevenueTicketByPaymentMethodPrint'
]);

// Report Visitors Login
Route::get('/report_visitors_login', 'Scanning\Report\ReportVisitorsLoginController@index')->name('report_visitors_login');
Route::get('/view_report_visitors_login/{param1}/{param2}',[
    'as' => 'view_report_visitors_login',
    'uses' => 'Scanning\Report\ReportVisitorsLoginController@viewReportVisitorsLogin'
]);
Route::get('/view_report_visitors_login_excel/{param1}/{param2}',[
    'as' => 'view_report_visitors_login_excel',
    'uses' => 'Scanning\Report\ReportVisitorsLoginController@viewReportVisitorsLoginExcel'
]);
Route::get('/view_report_visitors_login_print/{param1}/{param2}',[
    'as' => 'view_report_visitors_login_print',
    'uses' => 'Scanning\Report\ReportVisitorsLoginController@viewReportVisitorsLoginPrint'
]);

///////////////////////////////// MARKETING /////////////////////////////////////
Route::get('/marketing/leaseagreement/viewlistdatanew',[
    'as' => 'marketing.leaseagreement.viewlistdatanew',
    'uses' => 'Marketing\LeaseAgreement@viewListDataNew'
]);
Route::get('/marketing/leaseagreement/viewadddataleaseAgreement',[
    'as' => 'marketing.leaseagreement.viewadddataleaseAgreement',
    'uses' => 'Marketing\LeaseAgreement@viewAddDataLeaseAgreement'
]);
Route::post('/marketing/leaseagreement/deleteitemschedule',[
    'as' => 'marketing.leaseagreement.deleteitemschedule',
    'uses' => 'Marketing\LeaseAgreement@deleteItemSchedule'
]);
Route::post('/marketing/leaseagreement/insertupdateitemschedule',[
    'as' => 'marketing.leaseagreement.insertupdateitemschedule',
    'uses' => 'Marketing\LeaseAgreement@insertUpdateItemSchedule'
]);
Route::post('/marketing/leaseagreement/deleteitemsecuredeposito',[
    'as' => 'marketing.leaseagreement.deleteitemsecuredeposito',
    'uses' => 'Marketing\LeaseAgreement@deleteItemSecureDeposito'
]);
Route::post('/marketing/leaseagreement/getitemsecuredeposit',[
    'as' => 'marketing.leaseagreement.getitemsecuredeposit',
    'uses' => 'Marketing\LeaseAgreement@getitemSecureDeposit'
]);
Route::post('/marketing/leaseagreement/insertupdatesecuredeposit/', [
    'as' => 'marketing.leaseagreement.insertupdatesecuredeposit',
    'uses' => 'Marketing\LeaseAgreement@insertUpdateSecureDeposit'
]);
Route::post('/marketing/leaseagreement/adddatapsm', [
    "before" => "csrf",
    'as'     => 'marketing.leaseagreement.adddatapsm',
    'uses'   => 'Marketing\LeaseAgreement@saveDataPSM'
]);
Route::get('/marketing/leaseagreement/view_edit_data/{id}',[
    'as' => 'marketing.leaseagreement.vieweditdata',
    'uses' => 'Marketing\LeaseAgreement@viewFormEditDataPSM'
]);
Route::get('/marketing/leaseagreement/viewlistdata',[
    'as' => 'marketing.leaseagreement.viewlistdata',
    'uses' => 'Marketing\LeaseAgreement@viewListData'
]);
Route::post('/marketing/leaseagreement/deleteitemrentscamt',[
    'as' => 'marketing.leaseagreement.deleteitemrentscamt',
    'uses' => 'Marketing\LeaseAgreement@deleteItemRentSCAmt'
]);
Route::post('/marketing/leaseagreement/insertrentsclot/', [
    'as' => 'marketing.leaseagreement.insertrentsclot',
    'uses' => 'Marketing\LeaseAgreement@insertRentSCLot'
]);
Route::post('/marketing/leaseagreement/insertrentscdisc/', [
    'as' => 'marketing.leaseagreement.insertrentscdisc',
    'uses' => 'Marketing\LeaseAgreement@insertRentSCDisc'
]);
Route::post('/marketing/leaseagreement/insertrentscamt/', [
    'as' => 'marketing.leaseagreement.insertrentscamt',
    'uses' => 'Marketing\LeaseAgreement@insertRentSCAmt'
]);
Route::post('/marketing/leaseagreement/editdatapsm', [
    "before" => "csrf",
    'as'     => 'marketing.leaseagreement.editdatapsm',
    'uses'   => 'Marketing\LeaseAgreement@saveEditDataPSM'
]);
Route::post('/marketing/leaseagreement/voidschedule', [
    "before" => "csrf",
    'as'     => 'marketing.leaseagreement.voidschedule',
    'uses'   => 'Marketing\LeaseAgreement@voidSchedule'
]);
Route::post('/marketing/leaseagreement/editdataadmindoc', [
    "before" => "csrf",
    'as'     => 'marketing.leaseagreement.editdataadmindoc',
    'uses'   => 'Marketing\LeaseAgreement@saveEditDataAdminDoc'
]);
Route::post('/marketing/leaseagreement/editdescschedule', [
    "before" => "csrf",
    'as'     => 'marketing.leaseagreement.editdescschedule',
    'uses'   => 'Marketing\LeaseAgreement@saveEditDescSchedule'
]);
Route::post('/marketing/leaseagreement/upload_bs/',[
    "before" => "csrf",
    'as' => 'marketing.leaseagreement.uploadbillingschedule',
    'uses' => 'Marketing\LeaseAgreement@uploadBillingSchedule'
]);
Route::get('/marketing/leaseagreement/generatescheddatapsmdp/{id1}',[
    'as' => 'marketing.leaseagreement.generatescheddatapsmdp',
    'uses' => 'Marketing\LeaseAgreement@generateSchedDataPSMDP'
]);
Route::get('/marketing/leaseagreement/generatescheddatapsm/{id1}',[
    'as' => 'marketing.leaseagreement.generatescheddatapsm',
    'uses' => 'Marketing\LeaseAgreement@generateSchedDataPSM'
]);
Route::get('/marketing/leaseagreement/deletescheddatapsmdp/{id1}',[
    'as' => 'marketing.leaseagreement.deletescheddatapsmdp',
    'uses' => 'Marketing\LeaseAgreement@deleteSchedDataPSMDP'
]);
Route::get('/marketing/leaseagreement/deletescheddatapsm/{id1}',[
    'as' => 'marketing.leaseagreement.deletescheddatapsm',
    'uses' => 'Marketing\LeaseAgreement@deleteSchedDataPSM'
]);
Route::get('/marketing/leaseagreement/viewscheddiscount/{id}',[
    'as' => 'marketing.leaseagreement.viewscheddiscount',
    'uses' => 'Marketing\LeaseAgreement@viewSchedDiscount'
]);
Route::get('/marketing/leaseagreement/viewrequestrevenuesharing/{id}',[
    'as' => 'marketing.leaseagreement.viewrequestrevenuesharing',
    'uses' => 'Marketing\LeaseAgreement@viewRequestRevenueSharing'
]);
Route::get('/marketing/leaseagreement/viewaddaddendum/{id}/{id1}',[
    'as' => 'marketing.leaseagreement.viewaddaddendum',
    'uses' => 'Marketing\LeaseAgreement@viewAddAddendum'
]);
Route::get('/marketing/leaseagreement/vieweditaddendum/{id}/{id1}',[
    'as' => 'marketing.leaseagreement.vieweditaddendum',
    'uses' => 'Marketing\LeaseAgreement@viewEditAddendum'
]);
Route::get('/marketing/leaseagreement/canceldataAddendum/{id}',[
    'as' => 'marketing.leaseagreement.canceldataAddendum',
    'uses' => 'Marketing\LeaseAgreement@cancelDataAddendum'
]);
Route::get('/marketing/leaseagreement/getnumberbast/{id}',[
    'as' => 'marketing.leaseagreement.getnumberbast',
    'uses' => 'Marketing\LeaseAgreement@getNumberBAST'
]);
Route::get('/marketing/leaseagreement/printbast/{id}',[
    'as' => 'marketing.leaseagreement.printbast',
    'uses' => 'Marketing\LeaseAgreement@PrintBAST'
]);
Route::get('/marketing/leaseagreement/getnumberleaseagreement/{id}',[
    'as' => 'marketing.leaseagreement.getnumberleaseagreement',
    'uses' => 'Marketing\LeaseAgreement@getNumberLeaseAgreement'
]);
Route::get('/marketing/confirmationletter/print_sks/{id}',[
    'as' => 'marketing/confirmationletter.printsks',
    'uses' => 'Marketing\ConfirmationLetter@PrintSKS'
]);
Route::post('/marketing/leaseagreement/addscheddiscount', [
    "before" => "csrf",
    'as'     => 'marketing.leaseagreement.addscheddiscount',
    'uses'   => 'Marketing\LeaseAgreement@addSchedDiscount'
]);
Route::get('/marketing/leaseagreement/viewlistdatascheddisc',[
    'as' => 'marketing.leaseagreement.viewlistdatascheddisc',
    'uses' => 'Marketing\LeaseAgreement@viewListDataSchedDisc'
]);
Route::get('/marketing/leaseagreement/viewprocessdiscount/{id}',[
    'as' => 'marketing.leaseagreement.viewprocessdiscount',
    'uses' => 'Marketing\LeaseAgreement@viewProcessDiscount'
]);
Route::post('/marketing/leaseagreement/uploadfilediscount', [
    "before" => "csrf",
    'as'     => 'marketing.leaseagreement.uploadfilediscount',
    'uses'   => 'Marketing\LeaseAgreement@uploadFileDiscount'
]);
Route::post('/marketing/leaseagreement/addrequestrevenuesharing', [
    "before" => "csrf",
    'as'     => 'marketing.leaseagreement.addrequestrevenuesharing',
    'uses'   => 'Marketing\LeaseAgreement@addRequestRevenueSharing'
]);
Route::post('/marketing/leaseagreement/adddataaddendum', [
    "before" => "csrf",
    'as'     => 'marketing.leaseagreement.adddataaddendum',
    'uses'   => 'Marketing\LeaseAgreement@saveDataAddendum'
]);
Route::post('/marketing/leaseagreement/insertrentsclotadd/', [
    'as' => 'marketing.leaseagreement.insertrentsclotadd',
    'uses' => 'Marketing\LeaseAgreement@insertRentSCLotAdd'
]);
Route::post('/marketing/leaseagreement/insertrentscamtadd/', [
    'as' => 'marketing.leaseagreement.insertrentscamtadd',
    'uses' => 'Marketing\LeaseAgreement@insertRentSCAmtAdd'
]);
Route::post('/marketing/leaseagreement/insertrentscdiscadd/', [
    'as' => 'marketing.leaseagreement.insertrentscdiscadd',
    'uses' => 'Marketing\LeaseAgreement@insertRentSCDiscAdd'
]);
Route::post('/marketing/leaseagreement/deleteitemrentscamtadd',[
    'as' => 'marketing.leaseagreement.deleteitemrentscamtadd',
    'uses' => 'Marketing\LeaseAgreement@deleteItemRentSCAmtAdd'
]);
Route::post('/marketing/leaseagreement/editdataaddendum', [
    "before" => "csrf",
    'as'     => 'marketing.leaseagreement.editdataaddendum',
    'uses'   => 'Marketing\LeaseAgreement@saveEditDataAddendum'
]);
Route::post('/marketing/leaseagreement/editsqmrtaddendum', [
    "before" => "csrf",
    'as'     => 'marketing.leaseagreement.editsqmrtaddendum',
    'uses'   => 'Marketing\LeaseAgreement@saveEditSqmRtAddendum'
]);
Route::post('/marketing/leaseagreement/editsqmscaddendum', [
    "before" => "csrf",
    'as'     => 'marketing.leaseagreement.editsqmscaddendum',
    'uses'   => 'Marketing\LeaseAgreement@saveEditSqmScAddendum'
]);
Route::get('/marketing/leaseagreement/approvedataAddendum/{id}',[
    'as' => 'marketing.leaseagreement.approvedataAddendum',
    'uses' => 'Marketing\LeaseAgreement@approveDataAddendum'
]);
Route::get('/marketing/leaseagreement/printloi/{id}',[
    'as' => 'marketing.leaseagreement.printloi',
    'uses' => 'Marketing\LeaseAgreement@PrintLOI'
]);
Route::get('/marketing/leaseagreement/printleaseagreement/{id}',[
    'as' => 'marketing.leaseagreement.printleaseagreement',
    'uses' => 'Marketing\LeaseAgreement@PrintLeaseAgreement'
]);
Route::get('/marketing/leaseagreement/inactivedatapsm/{id1}',[
    'as' => 'marketing.leaseagreement.inactivedatapsm',
    'uses' => 'Marketing\LeaseAgreement@inactiveDataPSM'
]);
Route::get('/lot/lotmaster/listdatalot', [
    'as' => 'lot.lotmaster.listdatalot',
    'uses' => 'LotMasterFile\LotMaster@listDataLot'
]);
Route::get('lot/lotmaster/viewadddatalot', [
    'as' => 'lot.lotmaster.viewadddatalot',
    'uses' => 'LotMasterFile\LotMaster@viewAddDataLot'
]);
Route::post('lot/lotmaster/savedatalot', [
    'as' => 'lot.lotmaster.savedatalot',
    'uses' => 'LotMasterFile\LotMaster@saveDataLot'
]);
Route::get('lot/lotmaster/deletedatalot/{id}', [
    'as' => 'lot.lotmaster.deletedatalot',
    'uses' => 'LotMasterFile\LotMaster@deleteDataLot'
]);
Route::get('lot/lotmaster/vieweditdatalot/{id}', [
    'as' => 'lot.lotmaster.vieweditdatalot',
    'uses' => 'LotMasterFile\LotMaster@viewEditDataLot'
]);
Route::post('lot/lotmaster/saveeditdatalot', [
    'as' => 'lot.lotmaster.saveeditdatalot',
    'uses' => 'LotMasterFile\LotMaster@saveEditDataLot'
]);
Route::post('/lot/lotmaster/deleteitemlotprice',[
    'as' => 'lot.lotmaster.deleteitemlotprice',
    'uses' => 'LotMasterFile\LotMaster@deleteItemLotPrice'
]);
Route::post('/lot/lotmaster/getitemLotPrice',[
    'as' => 'lot.lotmaster.getitemLotPrice',
    'uses' => 'LotMasterFile\LotMaster@getitemLotPrice'
]);
Route::post('/lot/lotmaster/insertupdatelotprice/', [
    'as' => 'lot.lotmaster.insertupdatelotprice',
    'uses' => 'LotMasterFile\LotMaster@insertUpdateLotPrice'
]);
Route::get('/marketing/leaseagreement/viewlistdatascheddiscappr',[
    'as' => 'marketing.leaseagreement.viewlistdatascheddiscappr',
    'uses' => 'Marketing\LeaseAgreement@viewListDataSchedDiscAppr'
]);
Route::get('/marketing/leaseagreement/approvedatascheddisc/{id}',[
    'as' => 'marketing.leaseagreement.approvedatascheddisc',
    'uses' => 'Marketing\LeaseAgreement@approveDataSchedDisc'
]);
Route::get('/marketing/leaseagreement/canceldatascheddisc/{id}',[
    'as' => 'marketing.leaseagreement.canceldatascheddisc',
    'uses' => 'Marketing\LeaseAgreement@cancelDataSchedDisc'
]);
///////////////////////////////// MARKETING /////////////////////////////////////
///////////////////////////////// ACCOUNT RECEIVABLE /////////////////////////////////////
Route::get('/invoice/listgenerateinvoice',[
    'as' => 'invoice.listgenerateinvoice',
    'uses' => 'AccountReceivable\Invoice@listGenerateInvoice'
]);
Route::post('/generateInvoiceEditDesc/rb', [
    'as'   => 'gi.generateInvoiceEditDesc',
    'uses' => 'AccountReceivable\Invoice@editDescription'
]);
Route::post('/invoice/viewlistgenerateinvoice',[
    'as' => 'invoice.viewlistgenerateinvoice',
    'uses' => 'AccountReceivable\Invoice@viewListGenerateInvoice'
]);
Route::post('/invoice/generateinvoicerental',[
    'as' => 'invoice.generateinvoicerental',
    'uses' => 'AccountReceivable\Invoice@generateInvoiceRental'
]);
Route::post('/invoice/generateinvoicesecuritydesposit',[
    'as' => 'invoice.generateinvoicesecuritydesposit',
    'uses' => 'AccountReceivable\Invoice@generateInvoiceSecurityDesposit'
]);
Route::post('/invoice/generateinvoiceservicecharge',[
    'as' => 'invoice.generateinvoiceservicecharge',
    'uses' => 'AccountReceivable\Invoice@generateInvoiceServiceCharge'
]);
Route::post('/invoice/generateinvoiceutility',[
    'as' => 'invoice.generateinvoiceutility',
    'uses' => 'AccountReceivable\Invoice@generateInvoiceUtility'
]);
Route::post('/invoice/generateinvoicecasual',[
    'as' => 'invoice.generateinvoicecasual',
    'uses' => 'AccountReceivable\Invoice@generateInvoiceCasual'
]);
Route::post('/invoice/generateinvoiceothers',[
    'as' => 'invoice.generateinvoiceothers',
    'uses' => 'AccountReceivable\Invoice@generateInvoiceOthers'
]);
Route::get('/invoice/printinvoiceperforma/{id}/{id1}',[
    'as' => 'invoice.printinvoiceperforma',
    'uses' => 'AccountReceivable\Invoice@PrintInvoicePerforma'
]);
Route::get('/invoice/printinvoiceperformaservicecharge/{id}/{id1}',[
    'as' => 'invoice.printinvoiceperformaservicecharge',
    'uses' => 'AccountReceivable\Invoice@PrintInvoicePerformaServiceCharge'
]);
Route::get('/invoice/printinvoiceperformautility/{id}/{id1}',[
    'as' => 'invoice.printinvoiceperformautility',
    'uses' => 'AccountReceivable\Invoice@PrintInvoicePerformaUtility'
]);
Route::get('/invoice/viewgetlistgenerateinvoice/{param1}/{param2}',[
    'as' => 'invoice.viewgetlistgenerateinvoice',
    'uses' => 'AccountReceivable\Invoice@viewGetListGenerateInvoice'
]);
Route::get('/invoice/listdatainvoice',[
    'as' => 'invoice.listdatainvoice',
    'uses' => 'AccountReceivable\Invoice@listDataInvoice'
]);
Route::post('/invoice/changepphstatusinvoice',[
    'as' => 'invoice.changepphstatusinvoice',
    'uses' => 'AccountReceivable\Invoice@changePPHStatusInvoice'
]);
Route::get('/invoice/viewadddatainvoicemanual',[
    'as' => 'invoice.viewadddatainvoicemanual',
    'uses' => 'AccountReceivable\Invoice@viewAddDataInvoiceManual'
]);
Route::get('/invoice/viewadddatarevenuesharing/',[
    'as' => 'invoice.viewadddatarevenuesharing',
    'uses' => 'AccountReceivable\Invoice@viewAddDataRevenueSharing'
]);
Route::post('/invoice/viewlistdatainvoice',[
    'as' => 'invoice.viewlistdatainvoice',
    'uses' => 'AccountReceivable\Invoice@viewListDataInvoice'
]);
Route::get('/invoice/voidinvoice/{id1}',[
    'as' => 'invoice.voidinvoice',
    'uses' => 'AccountReceivable\Invoice@voidInvoice'
]);
Route::get('/invoice/vieweditdatainvoicemanual/{id}/{id1}',[
    'as' => 'invoice.vieweditdatainvoicemanual',
    'uses' => 'AccountReceivable\Invoice@viewEditDataInvoiceManual'
]);
Route::get('/invoice/vieweditdatainvoicerevenuesharing/{id}',[
    'as' => 'invoice.vieweditdatainvoicerevenuesharing',
    'uses' => 'AccountReceivable\Invoice@viewEditDataInvoiceRevenueSharing'
]);
Route::post('/invoice/deleteiteminv',[
    'as' => 'invoice.deleteiteminv',
    'uses' => 'AccountReceivable\Invoice@deleteItemInv'
]);
Route::post('/invoice/getitemgltrans',[
    'as' => 'invoice.getitemgltrans',
    'uses' => 'AccountReceivable\Invoice@getItemInv'
]);
Route::post('/invoice/insertupdateiteminvoice',[
    'as' => 'invoice.insertupdateiteminvoice',
    'uses' => 'AccountReceivable\Invoice@insertUpdateItemInvoice'
]);
Route::post('/invoice/saveeditinvoicemanual', [
    "before" => "csrf",
    'as'     => 'invoice.saveeditinvoicemanual',
    'uses'   => 'AccountReceivable\Invoice@saveEditInvoiceManual'
]);
Route::get('/invoice/printinvoicekwitansi/{id}/{id1}',[
    'as' => 'invoice.printinvoicekwitansi',
    'uses' => 'AccountReceivable\Invoice@PrintInvoiceKwitansi'
]);
Route::get('/invoice/postinginvoice/{id1}',[
    'as' => 'invoice.postinginvoice',
    'uses' => 'AccountReceivable\Invoice@postingInvoice'
]);
Route::get('/invoice/viewpaidinvoice/{id}',[
    'as' => 'invoice.viewpaidinvoice',
    'uses' => 'AccountReceivable\Invoice@viewPaidInvoice'
]);
Route::post('/invoice/saveinvoicepayment', [
    "before" => "csrf",
    'as'     => 'invoice.saveinvoicepayment',
    'uses'   => 'AccountReceivable\Invoice@saveInvoicePayment'
]);
Route::get('/invoice/printkwitansireceipt/{id1}',[
    'as' => 'invoice.printkwitansireceipt',
    'uses' => 'AccountReceivable\Invoice@PrintKwitansiReceipt'
]);
Route::get('/invoice/listdatainvoiceappr/',[
    'as' => 'invoice.listdatainvoiceappr',
    'uses' => 'AccountReceivable\Invoice@listDataInvoiceAppr'
]);
Route::get('/invoice/approveinvoicepayment/{id1}',[
    'as' => 'invoice.approveinvoicepayment',
    'uses' => 'AccountReceivable\Invoice@approveInvoicePayment'
]);
Route::get('/invoice/rejectinvoicepayment/{id1}',[
    'as' => 'invoice.rejectinvoicepayment',
    'uses' => 'AccountReceivable\Invoice@rejectInvoicePayment'
]);
Route::get('/invoice/listdatainvoiceunappr/',[
    'as' => 'invoice.listdatainvoiceunappr',
    'uses' => 'AccountReceivable\Invoice@listDataInvoiceUnappr'
]);
Route::get('/invoice/unapproveinvoicepayment/{id1}',[
    'as' => 'invoice.unapproveinvoicepayment',
    'uses' => 'AccountReceivable\Invoice@unapproveInvoicePayment'
]);
Route::post('/invoice/saveinvoicemanual', [
    "before" => "csrf",
    'as'     => 'invoice.saveinvoicemanual',
    'uses'   => 'AccountReceivable\Invoice@saveInvoiceManual'
]);
Route::post('/invoice/saveinvoicerevenuesharing', [
    "before" => "csrf",
    'as'     => 'invoice.saveinvoicerevenuesharing',
    'uses'   => 'AccountReceivable\Invoice@saveInvoiceRevenueSharing'
]);
Route::post('/invoice/getitemgltransrs',[
    'as' => 'invoice.getitemgltransrs',
    'uses' => 'AccountReceivable\Invoice@getItemInvRS'
]);
Route::post('/invoice/insertupdateiteminvoicers',[
    'as' => 'invoice.insertupdateiteminvoicers',
    'uses' => 'AccountReceivable\Invoice@insertUpdateItemInvoiceRS'
]);
Route::get('/invoice/creditpph/',[
    'as' => 'invoice.creditpph',
    'uses' => 'AccountReceivable\Invoice@creditPPH'
]);
Route::post('/invoice/processcreditpph',[
    'as' => 'invoice.processcreditpph',
    'uses' => 'AccountReceivable\Invoice@processCreditPPH'
]);
Route::get('/creditnotes/listdatacreeditnotes/',[
    'as' => 'creditnotes.listdatacreeditnotes',
    'uses' => 'AccountReceivable\CreditNotes@listDataCreditNotes'
]);
Route::get('/creditnotes/viewadddatacreditnotes',[
    'as' => 'creditnotes.viewadddatacreditnotes',
    'uses' => 'AccountReceivable\CreditNotes@viewAddDataCreditNotes'
]);
Route::get('/creditnotes/canceldatacreditnotes/{id}',[
    'as' => 'creditnotes.canceldatacreditnotes',
    'uses' => 'AccountReceivable\CreditNotes@cancelDatacreditNotes'
]);
Route::post('/creditnotes/savecreditnotes', [
    "before" => "csrf",
    'as'     => 'creditnotes.savecreditnotes',
    'uses'   => 'AccountReceivable\CreditNotes@saveCreditNotes'
]);
Route::get('/creditnotes/vieweditdatacreditnotes/{id}/{id1}',[
    'as' => 'creditnotes.vieweditdatacreditnotes',
    'uses' => 'AccountReceivable\CreditNotes@viewEditDataCreditNotes'
]);
Route::post('/creditnotes/deleteitemcreditnotes',[
    'as' => 'creditnotes.deleteitemcreditnotes',
    'uses' => 'AccountReceivable\CreditNotes@deleteItemCreditNotes'
]);
Route::post('/creditnotes/getitemcreditnotes',[
    'as' => 'creditnotes.getitemcreditnotes',
    'uses' => 'AccountReceivable\CreditNotes@getItemCreditNotes'
]);
Route::post('/creditnotes/insertupdateitemcreditnotes',[
    'as' => 'creditnotes.insertupdateitemcreditnotes',
    'uses' => 'AccountReceivable\CreditNotes@insertUpdateItemCreditNotes'
]);
Route::post('/creditnotes/saveeditcreditnotes', [
    "before" => "csrf",
    'as'     => 'creditnotes.saveeditcreditnotes',
    'uses'   => 'AccountReceivable\CreditNotes@saveEditCreditNotes'
]);
Route::get('/creditnotes/approvedatacreditnotes/{id1}',[
    'as' => 'creditnotes.approvedatacreditnotes',
    'uses' => 'AccountReceivable\CreditNotes@approveDataCreditNotes'
]);
Route::get('/virtualaccount/uploaddownloadva/',[
    'as' => 'virtualaccount.uploaddownloadva',
    'uses' => 'AccountReceivable\VirtualAccount@uploadDownloadVA'
]);
Route::post('/virtualaccount/proses_upload_download_va/',[
    'as' => 'virtualaccount.prosesuploaddownloadva',
    'uses' => 'AccountReceivable\VirtualAccount@prosesUploadDownloadVA'
]);
///////////////////////////////// ACCOUNT RECEIVABLE /////////////////////////////////////
///////////////////////////////// TAX /////////////////////////////////////
Route::get('/accounting/tax/listdatafakturpajak/',[
    'as' => 'accounting.tax.listdatafakturpajak',
    'uses' => 'Accounting\Pajak@listDataFakturPajak'
]);
Route::post('/accounting/tax/deletetaxinvoice/', [
    'as' => 'accounting.tax.deletetaxinvoice',
    'uses' => 'Accounting\Pajak@deleteTaxInvoice'
]);
Route::post('/accounting/tax/generatetaxinvoice/', [
    'as' => 'accounting.tax.generatetaxinvoice',
    'uses' => 'Accounting\Pajak@generateTaxInvoice'
]);
Route::get('/accounting/tax/listtransaksifaktur/',[
    'as' => 'accounting.tax.listtransaksifaktur',
    'uses' => 'Accounting\Pajak@listTransaksiFaktur'
]);
Route::post('/listTransactionFakturEditTrxCode/tf', [
    'as'   => 'tf.listTransactionFakturEditTrxCode',
    'uses' => 'Accounting\Pajak@editTrxCodeFaktur'
]);
Route::post('/accounting/tax/viewlisttransaksifaktur/',[
    'as' => 'accounting.tax.viewlisttransaksifaktur',
    'uses' => 'Accounting\Pajak@viewListTransaksiFaktur'
]);
Route::post('/accounting/tax/exportdatafaktur/',[
    'as' => 'accounting.tax.exportdatafaktur',
    'uses' => 'Accounting\Pajak@exportDataFaktur'
]);
///////////////////////////////// TAX /////////////////////////////////////
///////////////////////////////// ENGINEERING /////////////////////////////////////
Route::any('/engineering/input_meter/', ['as' => 'engineering.meterIn', 'uses' => 'Engineering\MasterEngineeringController@meterIn']);
Route::post('/engineering/set_formula/', ['as' => 'engineering.set_formula', 'uses' => 'Engineering\MasterEngineeringController@set_formula']);
Route::get('/engineering/meter_input/{id}', ['as' => 'engineering.meter_input', 'uses' => 'Engineering\MasterEngineeringController@meter_input']);
Route::post('/engineering/getitemutilstenantmeter',[
    'as' => 'engineering.getitemutilstenantmeter',
    'uses' => 'Engineering\MasterEngineeringController@getItemUtilsTenantMeter'
]);
Route::post('/engineering/meterInput/', ['as' => 'engineering.meterInput', 'uses' => 'Engineering\MasterEngineeringController@meterInput']);
Route::any('/engineering/util_formula/', ['as' => 'engineering.util_formula', 'uses' => 'Engineering\MasterEngineeringController@util_formula']);
Route::get('/engineering/edit_formula/', ['as' => 'engineering.edit_formula', 'uses' => 'Engineering\MasterEngineeringController@edit_formula']);
Route::get('/engineering/find_formula/{id}', ['as' => 'engineering.find_formula', 'uses' => 'Engineering\MasterEngineeringController@find_formula']);
Route::any('/engineering/util_meter/', ['as' => 'engineering.util_meter', 'uses' => 'Engineering\MasterEngineeringController@util_meter']);
Route::post('/engineering/set_meter/', ['as' => 'engineering.set_meter', 'uses' => 'Engineering\MasterEngineeringController@set_meter']);
Route::get('/engineering/edit_meter/', ['as' => 'engineering.edit_meter', 'uses' => 'Engineering\MasterEngineeringController@edit_meter']);
Route::post('/engineering/edit_meter2/', ['as' => 'engineering.edit_meter2', 'uses' => 'Engineering\MasterEngineeringController@edit_meter2']);
Route::get('/engineering/find_meter/{id}', ['as' => 'engineering.find_meter', 'uses' => 'Engineering\MasterEngineeringController@find_meter']);
Route::any('/engineering/util_tenant/', ['as' => 'engineering.util_tenant', 'uses' => 'Engineering\MasterEngineeringController@util_tenant']);
Route::post('/engineering/deleteitemutilstenant',[
    'as' => 'engineering.deleteitemutilstenant',
    'uses' => 'Engineering\MasterEngineeringController@deleteItemUtilsTenant'
]);
Route::post('/engineering/getitemutilstenant',[
    'as' => 'engineering.getitemutilstenant',
    'uses' => 'Engineering\MasterEngineeringController@getItemUtilsTenant'
]);
Route::post('/engineering/save_tenant/', ['as' => 'engineering.tenant.save', 'uses' => 'Engineering\MasterEngineeringController@save_tenant_meter']);
Route::any('/engineering/util_billing/', [
    'as' => 'engineering.util_billing',
    'uses' => 'Engineering\MasterEngineeringController@util_billing'
]);
Route::post('/engineering/deleteutilbilling',[
    'as' => 'engineering.deleteutilbilling',
    'uses' => 'Engineering\MasterEngineeringController@deleteUtilBilling'
]);
Route::any('/engineering/util_billing_appr/', [
    'as' => 'engineering.util_billing_appr',
    'uses' => 'Engineering\MasterEngineeringController@util_billing_appr'
]);
Route::post('/engineering/approveutilbilling', [
    "before" => "csrf",
    'as'     => 'engineering.approveutilbilling',
    'uses'   => 'Engineering\MasterEngineeringController@approveUtilBilling'
]);
Route::any('/engineering/util_billing_unappr/', [
    'as' => 'engineering.util_billing_unappr',
    'uses' => 'Engineering\MasterEngineeringController@util_billing_unappr'
]);
Route::post('/engineering/unapproveutilbilling',[
    'as' => 'engineering.unapproveutilbilling',
    'uses' => 'Engineering\MasterEngineeringController@unapproveUtilBilling'
]);
///////////////////////////////// ENGINEERING /////////////////////////////////////

// Report Revenue Ticket & Equipment By Promo
Route::get('/report_rev_ticket_by_promo', 'Sales\Report\ReportRevenueTicketByPromoController@index')->name('report_rev_ticket_by_promo');
Route::get('/view_report_rev_ticket_by_promo/{param1}/{param2}/{param3}',[
    'as' => 'view_report_rev_ticket_by_promo',
    'uses' => 'Sales\Report\ReportRevenueTicketByPromoController@viewReportRevenueTicketByPromo'
]);
Route::get('/view_report_rev_ticket_by_promo_excel/{param1}/{param2}/{param3}',[
    'as' => 'view_report_rev_ticket_by_promo_excel',
    'uses' => 'Sales\Report\ReportRevenueTicketByPromoController@viewReportRevenueTicketByPromoExcel'
]);
Route::get('/view_report_rev_ticket_by_promo_print/{param1}/{param2}/{param3}',[
    'as' => 'view_report_rev_ticket_by_promo_print',
    'uses' => 'Sales\Report\ReportRevenueTicketByPromoController@viewReportRevenueTicketByPromoPrint'
]);

// Master Data Category Revenue
Route::get('/category_revenue', 'MasterData\CategoryRevenue\CategoryRevenueController@index')->name('category_revenue');
Route::post('/listTblCategoryRevenue', 'MasterData\CategoryRevenue\CategoryRevenueController@listTblCategoryRevenue')->name('listTblCategoryRevenue');
Route::get('/add_new_category_revenue', 'MasterData\CategoryRevenue\CategoryRevenueController@addNewCategoryRevenue')->name('add_new_category_revenue');
Route::post('/save_category_revenue',[
    'as' => 'save_category_revenue',
    'uses' => 'MasterData\CategoryRevenue\CategoryRevenueController@saveCategoryRevenue'
]);
Route::get('/edit_view_category_revenue/{id}',[
    'as' => 'edit_view_category_revenue',
    'uses' => 'MasterData\CategoryRevenue\CategoryRevenueController@editViewCategoryRevenue'
]);
Route::post('/edit_category_revenue',[
    'as' => 'edit_category_revenue',
    'uses' => 'MasterData\CategoryRevenue\CategoryRevenueController@editCategoryRevenue'
]);
Route::get('/delete_category_revenue/{id}',[
    'as' => 'delete_category_revenue',
    'uses' => 'MasterData\CategoryRevenue\CategoryRevenueController@deleteCategoryRevenue'
]);

// Revenue Settings
Route::get('/revenue_settings', 'Sales\RevenueSettings\RevenueSettingsController@index')->name('revenue_settings');
Route::post('/listTblRevenueSettings', 'Sales\RevenueSettings\RevenueSettingsController@listTblRevenueSettings')->name('listTblRevenueSettings');
Route::get('/add_new_revenue_settings', 'Sales\RevenueSettings\RevenueSettingsController@addNewRevenueSettings')->name('add_new_revenue_settings');
Route::post('/save_revenue_settings',[
    'as' => 'save_revenue_settings',
    'uses' => 'Sales\RevenueSettings\RevenueSettingsController@saveRevenueSettings'
]);
Route::get('/delete_revenue_settings/{id}',[
    'as' => 'delete_revenue_settings',
    'uses' => 'Sales\RevenueSettings\RevenueSettingsController@deleteRevenueSettings'
]);
Route::get('/revenue_settings_get_category_revenue_by_project/{id}',[
    'as' => 'revenue_settings_get_category_revenue_by_project',
    'uses' => 'Sales\RevenueSettings\RevenueSettingsController@getCategoryRevenueByProject'
]);
Route::get('/revenue_settings_get_category_revenue_source/{id}/{id2}',[
    'as' => 'revenue_settings_get_category_revenue_source',
    'uses' => 'Sales\RevenueSettings\RevenueSettingsController@getCategoryRevenueSource'
]);

// Report Revenue Settings
Route::get('/report_revenue_settings', 'Sales\Report\ReportRevenueSettingsController@index')->name('report_revenue_settings');
Route::get('/view_report_revenue_settings/{param1}/{param2}',[
    'as' => 'view_report_revenue_settings',
    'uses' => 'Sales\Report\ReportRevenueSettingsController@viewReportRevenueSettings'
]);
Route::get('/view_report_revenue_settings_excel/{param1}/{param2}',[
    'as' => 'view_report_revenue_settings_excel',
    'uses' => 'Sales\Report\ReportRevenueSettingsController@viewReportRevenueSettingsExcel'
]);
Route::get('/view_report_revenue_settings_print/{param1}/{param2}',[
    'as' => 'view_report_revenue_settings_print',
    'uses' => 'Sales\Report\ReportRevenueSettingsController@viewReportRevenueSettingsPrint'
]);

// Report Revenue Console
Route::get('/report_revenue_console', 'Sales\Report\ReportRevenueConsoleController@index')->name('report_revenue_console');
Route::get('/view_report_revenue_console/{param1}',[
    'as' => 'view_report_revenue_console',
    'uses' => 'Sales\Report\ReportRevenueConsoleController@viewReportRevenueConsole'
]);
Route::get('/view_report_revenue_console_excel/{param1}',[
    'as' => 'view_report_revenue_console_excel',
    'uses' => 'Sales\Report\ReportRevenueConsoleController@viewReportRevenueConsoleExcel'
]);
Route::get('/view_report_revenue_console_print/{param1}',[
    'as' => 'view_report_revenue_console_print',
    'uses' => 'Sales\Report\ReportRevenueConsoleController@viewReportRevenueConsolePrint'
]);

// Master Data Product POS Category
Route::get('/product_pos_category', 'MasterData\ProductPOSCategory\ProductPOSCategoryController@index')->name('product_pos_category');
Route::get('/view_add_product_pos_category', 'MasterData\ProductPOSCategory\ProductPOSCategoryController@viewAddProductPOSCategory')->name('view_add_product_pos_category');
Route::post('/add_new_product_pos_category',[
    'as' => 'add_new_product_pos_category',
    'uses' => 'MasterData\ProductPOSCategory\ProductPOSCategoryController@addProductPOSCategory'
]);
Route::get('/view_edit_product_pos_category/{id}', 'MasterData\ProductPOSCategory\ProductPOSCategoryController@viewEditProductPOSCategory')->name('view_edit_product_pos_category');
Route::post('/edit_product_pos_category',[
    'as' => 'edit_product_pos_category',
    'uses' => 'MasterData\ProductPOSCategory\ProductPOSCategoryController@editProductPOSCategory'
]);
Route::get('/delete_product_pos_category/{id}', 'MasterData\ProductPOSCategory\ProductPOSCategoryController@deleteProductPOSCategory')->name('delete_product_pos_category');

// Master Data Product POS
Route::get('/product_pos', 'MasterData\ProductPOS\ProductPOSController@index')->name('product_pos');
Route::get('/view_add_product_pos', 'MasterData\ProductPOS\ProductPOSController@viewAddProductPOS')->name('view_add_product_pos');
Route::post('/add_new_product_pos',[
    'as' => 'add_new_product_pos',
    'uses' => 'MasterData\ProductPOS\ProductPOSController@addProductPOS'
]);
Route::get('/view_edit_product_pos/{id}', 'MasterData\ProductPOS\ProductPOSController@viewEditProductPOS')->name('view_edit_product_pos');
Route::post('/edit_product_pos',[
    'as' => 'edit_product_pos',
    'uses' => 'MasterData\ProductPOS\ProductPOSController@editProductPOS'
]);
Route::get('/delete_product_pos/{id}', 'MasterData\ProductPOS\ProductPOSController@deleteProductPOS')->name('delete_product_pos');

// Master Data Promo POS
Route::get('/promoPOS', 'MasterData\PromoPOS\PromoPOSController@index')->name('promoPOS');
Route::get('/add_new_promo_pos', 'MasterData\PromoPOS\PromoPOSController@addNewPromoPOS')->name('add_new_promo_pos');
Route::post('/save_promo_pos',[
    'as' => 'save_promo_pos',
    'uses' => 'MasterData\PromoPOS\PromoPOSController@savePromoPOS'
]);
Route::get('/edit_view_promo_pos/{id}',[
    'as' => 'edit_view_promo_pos',
    'uses' => 'MasterData\PromoPOS\PromoPOSController@editViewPromoPOS'
]);
Route::post('/edit_promo_pos',[
    'as' => 'edit_promo_pos',
    'uses' => 'MasterData\PromoPOS\PromoPOSController@editPromoPOS'
]);
Route::get('/delete_promo_pos/{id}',[
    'as' => 'delete_promo_pos',
    'uses' => 'MasterData\PromoPOS\PromoPOSController@deletePromoPOS'
]);
Route::get('/terminate_promo_pos/{id}',[
    'as' => 'terminate_promo_pos',
    'uses' => 'MasterData\PromoPOS\PromoPOSController@terminatePromoPOS'
]);
Route::get('/appr_smm_promo_pos/{id}',[
    'as' => 'appr_smm_promo_pos',
    'uses' => 'MasterData\PromoPOS\PromoPOSController@apprSMMPromoPOS'
]);
Route::get('/appr_gm_promo_pos/{id}',[
    'as' => 'appr_gm_promo_pos',
    'uses' => 'MasterData\PromoPOS\PromoPOSController@apprGMPromoPOS'
]);
Route::get('/unappr_smm_promo_pos/{id}',[
    'as' => 'unappr_smm_promo_pos',
    'uses' => 'MasterData\PromoPOS\PromoPOSController@unapprSMMPromoPOS'
]);
Route::get('/unappr_gm_promo_pos/{id}',[
    'as' => 'unappr_gm_promo_pos',
    'uses' => 'MasterData\PromoPOS\PromoPOSController@unapprGMPromoPOS'
]);
Route::get('/get_product_pos_promo/{id}',[
    'as' => 'get_product_pos_promo',
    'uses' => 'MasterData\PromoPOS\PromoPOSController@getProduct'
]);

// POS
Route::get('/listPOS', 'Sales\POS\POSController@index')->name('listPOS');
Route::get('/pos', 'Sales\POS\POSController@viewPos')->name('pos');
Route::get('/get_product_pos_price_by_id/{id}',[
    'as' => 'get_product_pos_price_by_id',
    'uses' => 'Sales\POS\POSController@getProductPOSPriceById'
]);
Route::post('/save_pos',[
    'as' => 'save_pos',
    'uses' => 'Sales\POS\POSController@savePOS'
]);
Route::get('/print_pos/{id}',[
    'as' => 'print_pos',
    'uses' => 'Sales\POS\POSController@printPOS'
]);
Route::get('/cancel_pos/{id}',[
    'as' => 'cancel_pos',
    'uses' => 'Sales\POS\POSController@cancelPOS'
]);
Route::get('/get_promo_pos/{id}/{id2}/{id3}/{id4}/{id5}',[
    'as' => 'get_promo_pos',
    'uses' => 'Sales\POS\POSController@getPromoPOS'
]);
Route::get('/get_promo_by_id_product_pos/{id}/{id2}',[
    'as' => 'get_promo_by_id_product_pos',
    'uses' => 'Sales\POS\POSController@getPromoByIdProductPOS'
]);

// Report POS
Route::get('/report_pos', 'Sales\Report\ReportPOSController@index')->name('report_pos');
Route::get('/view_report_pos/{param1}/{param2}',[
    'as' => 'view_report_pos',
    'uses' => 'Sales\Report\ReportPOSController@viewReportPOS'
]);
Route::get('/view_report_pos_excel/{param1}/{param2}',[
    'as' => 'view_report_pos_excel',
    'uses' => 'Sales\Report\ReportPOSController@viewReportPOSExcel'
]);
Route::get('/view_report_pos_details_excel/{param1}/{param2}',[
    'as' => 'view_report_pos_details_excel',
    'uses' => 'Sales\Report\ReportPOSController@viewReportPOSDetailsExcel'
]);
Route::get('/view_report_pos_print/{param1}/{param2}',[
    'as' => 'view_report_pos_print',
    'uses' => 'Sales\Report\ReportPOSController@viewReportPOSPrint'
]);
Route::get('/view_report_pos_details_print/{param1}/{param2}',[
    'as' => 'view_report_pos_details_print',
    'uses' => 'Sales\Report\ReportPOSController@viewReportPOSDetailsPrint'
]);

Auth::routes();
Route::get('/SSO/{id}/{ix?}','SsoController@token');
Route::get('/logout','SsoController@logout');
Route::get('/logoutWaterGroup', function(){
    Session::flush();
    return redirect('https://sso.metropolitanland.com/LogoutAllApps');
});
Route::get('/change_project/{id}','WaterGroupController@change_project')->name('change_project');

// API Tiket Kolam Renang


Route::post('/api/check-ticket', 'Api\SwimmingTicketApiController@checkTicket');
Route::post('/api/check-in', 'Api\SwimmingTicketApiController@checkIn');
Route::post('/api/check-out', 'Api\SwimmingTicketApiController@checkOut');
Route::get('/api/gate-check', 'Api\SwimmingTicketApiController@gateCheck');
