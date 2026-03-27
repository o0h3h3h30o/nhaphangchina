<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Install routes
$routes->get('install', 'InstallController::index');
$routes->get('install/database', 'InstallController::database');
$routes->post('install/setup-database', 'InstallController::setupDatabase');
$routes->get('install/admin', 'InstallController::admin');
$routes->post('install/create-admin', 'InstallController::createAdmin');
$routes->get('install/complete', 'InstallController::complete');

// Public routes
$routes->get('/', 'HomeController::index');
$routes->post('tracking', 'HomeController::tracking');
$routes->get('tin-tuc', 'HomeController::news');
$routes->get('tin-tuc/(:segment)', 'HomeController::newsDetail/$1');
$routes->get('bai-viet/(:segment)', 'HomeController::page/$1');

// Auth routes
$routes->group('auth', static function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::login');
    $routes->get('register', 'AuthController::register');
    $routes->post('register', 'AuthController::register');
    $routes->get('logout', 'AuthController::logout');
    $routes->get('forgot-password', 'AuthController::forgotPassword');
    $routes->post('forgot-password', 'AuthController::forgotPassword');
    $routes->get('reset-password', 'AuthController::resetPassword');
    $routes->post('reset-password', 'AuthController::resetPassword');
});

// Protected user routes
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Profile
    $routes->get('profile', 'ProfileController::index');
    $routes->post('profile', 'ProfileController::index');
    $routes->post('profile/password', 'ProfileController::updatePassword');

    // Consignment orders
    $routes->get('consignments', 'ConsignmentController::index');
    $routes->get('consignments/create', 'ConsignmentController::create');
    $routes->post('consignments/create', 'ConsignmentController::create');
    $routes->get('consignments/(:num)', 'ConsignmentController::show/$1');
    $routes->get('consignments/(:num)/edit', 'ConsignmentController::edit/$1');
    $routes->post('consignments/(:num)/edit', 'ConsignmentController::edit/$1');
    $routes->post('consignments/(:num)/cancel', 'ConsignmentController::cancel/$1');

    // Wallet
    $routes->get('wallet', 'WalletController::index');
    $routes->get('wallet/transactions', 'WalletController::transactions');

    // Topup
    $routes->get('topup', 'TopupController::index');
    $routes->get('topup/create', 'TopupController::create');
    $routes->post('topup/create', 'TopupController::create');
    $routes->get('topup/(:num)', 'TopupController::show/$1');

    // Withdrawal
    $routes->get('withdrawal', 'WithdrawalController::index');
    $routes->get('withdrawal/create', 'WithdrawalController::create');
    $routes->post('withdrawal/create', 'WithdrawalController::create');
    $routes->get('withdrawal/(:num)', 'WithdrawalController::show/$1');

    // Bank accounts
    $routes->get('bank-accounts', 'BankAccountController::index');
    $routes->get('bank-accounts/create', 'BankAccountController::create');
    $routes->post('bank-accounts/create', 'BankAccountController::create');
    $routes->get('bank-accounts/(:num)/edit', 'BankAccountController::edit/$1');
    $routes->post('bank-accounts/(:num)/edit', 'BankAccountController::edit/$1');
    $routes->post('bank-accounts/(:num)/delete', 'BankAccountController::delete/$1');
    $routes->post('bank-accounts/(:num)/set-default', 'BankAccountController::setDefault/$1');

    // Delivery (user view)
    $routes->get('deliveries', 'DeliveryController::index');
    $routes->get('deliveries/(:num)', 'DeliveryController::show/$1');

    // Pickup requests
    $routes->get('pickup', 'PickupController::index');
    $routes->post('pickup/create', 'PickupController::create');
    $routes->post('pickup/(:num)/cancel', 'PickupController::cancel/$1');
});

// API routes (for Chrome extension)
$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    $routes->post('auth/login', 'AuthController::login');

    $routes->group('', ['filter' => 'apiAuth'], static function ($routes) {
        $routes->get('me', 'AuthController::me');
        $routes->post('orders', 'OrderController::create');
        $routes->get('orders', 'OrderController::list');
    });
});

// Admin routes
$routes->group('admin', ['filter' => 'admin', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // User management
    $routes->get('users', 'UserController::index');
    $routes->get('users/(:num)', 'UserController::show/$1');
    $routes->post('users/(:num)/lock', 'UserController::lock/$1');
    $routes->post('users/(:num)/unlock', 'UserController::unlock/$1');
    $routes->post('users/(:num)/flag', 'UserController::flag/$1');
    $routes->post('users/(:num)/update-group', 'UserController::updateGroup/$1');

    // China warehouse receiving (legacy)
    $routes->get('china-receiving', 'ChinaReceivingController::index');
    $routes->post('china-receiving/process', 'ChinaReceivingController::process');
    $routes->get('china-receiving/divisor', 'ChinaReceivingController::getDivisor');

    // Kho TQ - nhap kien
    $routes->get('cn-warehouse', 'CnWarehouseController::index');
    $routes->post('cn-warehouse/receive', 'CnWarehouseController::receive');
    $routes->get('cn-warehouse/search', 'CnWarehouseController::search');

    // Dong bao
    $routes->get('bags', 'BagController::index');
    $routes->get('bags/create', 'BagController::create');
    $routes->post('bags/parse-excel', 'BagController::parseExcel');
    $routes->post('bags/import-excel', 'BagController::importExcel');
    $routes->post('bags/import-manual', 'BagController::importManual');
    $routes->get('bags/(:num)', 'BagController::show/$1');
    $routes->post('bags/(:num)/add-parcel', 'BagController::addParcel/$1');
    $routes->post('bags/(:num)/remove-parcel/(:num)', 'BagController::removeParcel/$1/$2');
    $routes->post('bags/(:num)/seal', 'BagController::seal/$1');
    $routes->post('bags/(:num)/depart', 'BagController::depart/$1');

    // Kho VN nhan bao
    $routes->get('vn-receiving', 'VnReceivingController::index');
    $routes->post('vn-receiving/(:num)/arrive', 'VnReceivingController::arrive/$1');
    $routes->post('vn-receiving/(:num)/unpack', 'VnReceivingController::unpack/$1');
    $routes->post('vn-receiving/scan', 'VnReceivingController::scanParcel');
    $routes->get('vn-receiving/orphans', 'VnReceivingController::orphanParcels');
    $routes->post('vn-receiving/assign-user', 'VnReceivingController::assignUser');
    $routes->get('vn-receiving/search-users', 'VnReceivingController::searchUsers');

    // Consignment management
    $routes->get('consignments', 'ConsignmentController::index');
    $routes->get('consignments/(:num)', 'ConsignmentController::show/$1');
    $routes->post('consignments/lookup', 'ConsignmentController::lookup');
    $routes->post('consignments/(:num)/status', 'ConsignmentController::updateStatus/$1');
    $routes->post('consignments/(:num)/weight', 'ConsignmentController::updateWeight/$1');
    $routes->post('consignments/(:num)/calculate-fee', 'ConsignmentController::calculateFee/$1');

    // Topup management
    $routes->get('topups', 'TopupController::index');
    $routes->get('topups/(:num)', 'TopupController::show/$1');
    $routes->post('topups/(:num)/approve', 'TopupController::approve/$1');
    $routes->post('topups/(:num)/reject', 'TopupController::reject/$1');

    // Withdrawal management
    $routes->get('withdrawals', 'WithdrawalController::index');
    $routes->get('withdrawals/(:num)', 'WithdrawalController::show/$1');
    $routes->post('withdrawals/(:num)/approve', 'WithdrawalController::approve/$1');
    $routes->post('withdrawals/(:num)/complete', 'WithdrawalController::complete/$1');
    $routes->post('withdrawals/(:num)/reject', 'WithdrawalController::reject/$1');

    // User groups
    $routes->get('user-groups', 'UserGroupController::index');
    $routes->get('user-groups/create', 'UserGroupController::create');
    $routes->post('user-groups/create', 'UserGroupController::create');
    $routes->get('user-groups/(:num)/edit', 'UserGroupController::edit/$1');
    $routes->post('user-groups/(:num)/edit', 'UserGroupController::edit/$1');
    $routes->post('user-groups/(:num)/delete', 'UserGroupController::delete/$1');

    // Shipping rates
    $routes->get('shipping-rates', 'ShippingRateController::index');
    $routes->get('shipping-rates/create', 'ShippingRateController::create');
    $routes->post('shipping-rates/create', 'ShippingRateController::create');
    $routes->get('shipping-rates/(:num)/edit', 'ShippingRateController::edit/$1');
    $routes->post('shipping-rates/(:num)/edit', 'ShippingRateController::edit/$1');
    $routes->post('shipping-rates/(:num)/toggle', 'ShippingRateController::toggleActive/$1');

    // Truck trips
    $routes->get('truck-trips', 'TruckTripController::index');
    $routes->get('truck-trips/create', 'TruckTripController::create');
    $routes->post('truck-trips/create', 'TruckTripController::create');
    $routes->get('truck-trips/(:num)', 'TruckTripController::show/$1');
    $routes->get('truck-trips/(:num)/edit', 'TruckTripController::edit/$1');
    $routes->post('truck-trips/(:num)/edit', 'TruckTripController::edit/$1');
    $routes->post('truck-trips/(:num)/add-order', 'TruckTripController::addOrder/$1');
    $routes->post('truck-trips/(:num)/remove-order/(:num)', 'TruckTripController::removeOrder/$1/$2');
    $routes->post('truck-trips/(:num)/status', 'TruckTripController::updateStatus/$1');

    // Delivery management
    $routes->get('deliveries', 'DeliveryController::index');
    $routes->post('deliveries/create', 'DeliveryController::create');
    $routes->post('deliveries/(:num)/assign', 'DeliveryController::assign/$1');
    $routes->post('deliveries/(:num)/status', 'DeliveryController::updateStatus/$1');
    $routes->post('deliveries/(:num)/proof', 'DeliveryController::uploadProof/$1');

    // Posts management
    $routes->get('posts', 'PostController::index');
    $routes->get('posts/create', 'PostController::create');
    $routes->post('posts/create', 'PostController::create');
    $routes->get('posts/(:num)/edit', 'PostController::edit/$1');
    $routes->post('posts/(:num)/edit', 'PostController::edit/$1');
    $routes->post('posts/(:num)/delete', 'PostController::delete/$1');
    $routes->post('posts/(:num)/toggle', 'PostController::togglePublish/$1');

    // Post categories
    $routes->get('post-categories', 'PostCategoryController::index');
    $routes->get('post-categories/create', 'PostCategoryController::create');
    $routes->post('post-categories/create', 'PostCategoryController::create');
    $routes->get('post-categories/(:num)/edit', 'PostCategoryController::edit/$1');
    $routes->post('post-categories/(:num)/edit', 'PostCategoryController::edit/$1');
    $routes->post('post-categories/(:num)/delete', 'PostCategoryController::delete/$1');

    // Pickup management
    $routes->get('pickups', 'PickupController::index');
    $routes->post('pickups/(:num)/confirm', 'PickupController::confirm/$1');
    $routes->post('pickups/(:num)/complete', 'PickupController::complete/$1');

    // Site settings
    $routes->get('settings', 'SettingController::index');
    $routes->post('settings', 'SettingController::update');
});
