<?php

Route::group(['middleware' => ['web']], function() {
    Route::get('/terms', function (){return view('static.terms');});
});

Route::get('/', function () {
    return redirect()->route('admin.dashboard.index');  // jump to home page
});

Route::group(['middleware' => ['web'], 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
	Route::auth();

	Route::group(['middleware' => ['auth']], function() {
		Route::get('/', ['uses' => 'DashboardController@index', 'as' => 'admin.dashboard.index']);
        Route::put('/distributors/{distributor}/restore', ['uses' => 'DistributorController@restore', 'as' => 'admin.distributors.restore']);
		Route::resource('distributors', "DistributorController");
		Route::resource('shops', "ShopController", ['except' => ['update']]);
        Route::put('/shops/{shop}/restore', ['uses' => 'ShopController@restore', 'as' => 'admin.shops.restore']);
        Route::post('/shops/import', ['uses' => 'ShopController@import', 'as' => 'admin.shops.import']);
        Route::post('/shops/upload', ['uses' => 'ShopController@upload', 'as' => 'admin.shops.upload']);
        Route::post('/shops/{shop}', ['uses' => 'ShopController@update', 'as' => 'admin.shops.update']);
        Route::put('/shops/{shop}/change', ['uses' => 'ShopController@change', 'as' => 'admin.shops.change']);
		Route::resource('users', "UserController");
        Route::resource('replies',"RepliesController");
		Route::put('users/{users}/status', ['uses' => "UserController@updateStatus", 'as' => 'admin.users.update.status']);
        Route::put('/users/{user}/restore', ['uses' => 'UserController@restore', 'as' => 'admin.users.restore']);
		Route::delete('users/{user}/untie', ['uses' => 'UserController@untie', 'as' => 'admin.users.untie']);
        Route::resource('salesmen', "SalesmanController", ['only' => ['index']]);

		Route::resource('scans', "ScanLogController");
		Route::resource('extra', "ExtraController", ['only' => ['index']]);
        Route::resource('net', "NetScanLogController", ['only' => ['index']]);
		// Route::resource('drawrules', "DrawRuleController");
		Route::resource('activities', "ActivitiesController");
        Route::put('activities/{activity}/change', ['uses' => "ActivitiesController@change", 'as' => 'admin.activities.change']);
        Route::resource('codes', "CodeController", ['only' => ['index']]);
        Route::resource('codebatches', "CodeBatchController", ['only' => ['index', 'store']]);
		Route::get('codebatches/export', ['uses' => 'CodeBatchController@export', 'as' => 'admin.codebatches.export']);
        Route::put('codebatches/{codebatches}/status', ['uses' => "CodeBatchController@updateStatus", 'as' => 'admin.codebatches.update.status']);

		Route::group(['namespace' => 'Store', 'prefix' => 'store'], function() {
			Route::resource('items', "ItemController");
			Route::resource('orders', "OrderController");
		});

        Route::group(['namespace' => 'Goods', 'prefix' => 'goods'], function() {
            Route::resource('items', "ItemController");
            Route::resource('canceled', "CanceledController", ['only' => ['index', 'update']]);
            Route::resource('drawback', "DrawbackController", ['only' => ['index', 'update']]);
            Route::resource('orders', "OrderController");
            Route::put('/orders/{order}/checked', ['uses' => 'OrderController@checked', 'as' => 'admin.goods.orders.checked']);
        });
        Route::resource('banners', 'BannersController');
		Route::resource('news', 'NewsController');
        Route::put('/feedbacks/{feedback}/restore', ['uses' => 'FeedbackController@restore', 'as' => 'admin.feedbacks.restore']);
		Route::resource('feedbacks', 'FeedbackController');
		Route::get('notifications', ['uses' => 'NotificationController@index', 'as' => 'admin.notifications.index']);
		Route::put('notifications', ['uses' => 'NotificationController@update', 'as' => 'admin.notifications.update']);
		Route::post('notifications', ['uses' => 'NotificationController@store', 'as' => 'admin.notifications.store']);
		Route::delete('notifications', ['uses' => 'NotificationController@destroy', 'as' => 'admin.notifications.destroy']);

		Route::resource('admins', "AdminController");

		Route::resource('sms', 'SMSController', ['only' => ['index', 'store']]);
		Route::resource('areas', 'AreaController', ['only' => ['index']]);
        Route::get('fundingpool', ['uses' => 'FundingPoolController@index', 'as' => 'admin.fundingpool.index']);
        Route::get('fundingpool/summary', ['uses' => 'FundingPoolController@summary', 'as' => 'admin.fundingpool.summary']);
        Route::put('fundingpool/deposit', ['uses' => 'FundingPoolController@deposit', 'as' => 'admin.fundingpool.deposit']);

		Route::get('settings', ['uses' => 'SettingController@index', 'as' => 'admin.settings.index']);
		Route::put('settings', ['uses' => 'SettingController@update', 'as' => 'admin.settings.update']);

		Route::resource('categories', 'CategoriesController');
		Route::resource('coupons', 'ScanCouponLogsController', ['only' => ['index', 'destroy']]);

        // App Version Update
        Route::resource('versions', 'AppVersionsController');
        Route::resource('pays', "PaysController");
        Route::put('pays/{pay}/change', ['uses' => 'PaysController@change', 'as' => 'admin.pays.change']);
        Route::get('/scan/warnings', ['uses' => 'ScanWarningController@index', 'as' => 'admin.scan.warning']);
	});
});

Route::group(['middleware' => ['api'], 'namespace' => 'API', 'prefix' => 'api'], function() {
	Route::post('/sms/code', ['uses' => 'SMSController@code', 'as' => 'api.sms.code']);
    Route::post('/sms/code/check_verify_reset_password', ['uses' => 'SMSController@checkVerifyResetPasswordCode', 'as' => 'api.sms.code.check_verify_reset_password']);
    Route::put('/reset_account_password', ['uses' => 'V2LoginController@resetAccountPassword', 'as' => 'api.reset_account_password']);
    Route::post('/login', ['uses' => 'LoginController@login', 'as' => 'api.login']);
    Route::resource('orderaddress','OrderaddressController');
    Route::post('/orderaddress/update', ['uses' => 'OrderaddressController@update', 'as' => 'api.orderaddress.update']);
    Route::post('/orderaddress/store', ['uses' => 'OrderaddressController@store', 'as' => 'api.orderaddress.store']);
    Route::group(['prefix' => 'v2'], function () {
        Route::post('/login', ['uses' => 'V2LoginController@login', 'as' => 'api.v2.login']);
        Route::post('/account_login', ['uses' => 'V2LoginController@accountLogin', 'as' => 'api.v2.account_login']);
        Route::post('/register', ['uses' => 'V2LoginController@register', 'as' => 'api.v2.register']);
    });
    Route::get('/replies/banners', ['uses' => 'RepliesController@banners', 'as' => 'api.replies.banners']);
    Route::get('/replies/fbshow', ['uses' => 'RepliesController@fbshow', 'as' => 'api.replies.fbshow']);
    Route::get('/replies/replyshow', ['uses' => 'RepliesController@replyshow', 'as' => 'api.replies.replyshow']);
    
    // App Version Update
    Route::get('/app/versions', ['uses' => 'AppVersionsController@version', 'as' => 'api.app.versions']);
    Route::post('/goods/orders/notifications', ['uses' => 'Goods\OrderController@notifications', 'as' => 'api.goods.orders.notifications']);
	
	// Test
	Route::post('/test/withdraw', ['uses' => 'TestController@withdraw', 'as' => 'api.test.withdraw']);
	Route::get('/test/noti', ['uses' => 'TestController@checkNotification', 'as' => 'api.test.noti']);

    		
    Route::group(['middleware' => ['auth.jwt']], function() {

        Route::post('/scans', ['uses' => 'ScanLogController@store', 'as' => 'api.scans.store']);
        Route::get('/scans/logs', ['uses' => 'ScanLogController@index', 'as' => 'api.scans.log']);
        Route::get('/scans/rank', ['uses' => 'ScanLogController@rank', 'as' => 'api.scans.rank']);
        Route::get('/users/{user}/scans', ['uses' => 'ScanLogController@listByUser', 'as' => 'api.scans.listByUser']);

        Route::get('/users/@me', ['uses' => 'UserController@showMe', 'as' => 'api.user.show.me']);
        Route::get('/users/@me/statics', ['uses' => 'UserController@showMeStatics', 'as' => 'api.user.show.me.statics']);
        Route::put('/users/@me', ['uses' => 'UserController@storeMe', 'as' => 'api.user.store.me']);
        Route::put('/users/@me/unbind_wechat', ['uses' => 'UserController@unbindWechat', 'as' => 'api.user.unbind_wechat']);
            Route::put('goods/orders/canceled', ['uses' => 'Goods\OrderController@canceled', 'as' => 'api.goods.orders.canceled']);
        Route::resource('news', 'NewsController', ['only' => 'index']);
        Route::post('/news/newlog', ['uses' => 'NewsController@newlog', 'as' => 'api.news.newlog']);
        	Route::resource('salesmen', 'SalesmenController', ['only' => 'index']);
		Route::get('/salesmen/shopmen', ['uses' => 'SalesmenController@shopmen', 'as' => 'api.salesmen.shopmen']);
		Route::resource('pays', 'PaysController', ['only' => 'index']);
        
		Route::resource('feedbacks', 'FeedbackController', ['only' => 'store']);
		Route::resource('settings', 'SettingController', ['only' => 'index']);
		Route::resource('shops', 'ShopController', ['only' => ['index', 'store']]);

        Route::post('/withdraw', ['uses' => 'UserController@withdraw', 'as' => 'api.withdraw']);
        Route::post('/exchange', ['uses' => 'UserController@exchange', 'as' => 'api.exchange']);
		Route::get('/wallet/log', ['uses' => 'UserController@walletLog', 'as' => 'api.user.money_log']);

		Route::group(['namespace' => 'Store', 'prefix' => 'store'], function() {
			Route::resource('items', "ItemController", ['only' => ['index']]);

			Route::post('/orders', ['uses' => 'OrderController@store', 'as' => 'api.store.order.store']);
			Route::get('/users/{user}/orders', ['uses' => 'OrderController@listByUser', 'as' => 'api.store.order.listByUser']);
		});

        Route::group(['namespace' => 'Goods', 'prefix' => 'goods'], function() {
            Route::resource('items', "ItemController", ['only' => ['index']]);

            Route::get('/orders/show', ['uses' => 'OrderController@show', 'as' => 'api.goods.orders.show']);
            Route::post('/orders', ['uses' => 'OrderController@store', 'as' => 'api.goods.orders.store']);
            Route::get('/orders', ['uses' => 'OrderController@orderList', 'as' => 'api.goods.orders.orderList']);
            Route::put('/orders/cancel', ['uses' => 'OrderController@cancel', 'as' => 'api.goods.orders.cancel']);
            Route::post('/orders/generate', ['uses' => 'OrderController@generate', 'as' => 'api.goods.orders.generate']);
        });

        Route::post('/sms/code/check_auth', ['uses' => 'SMSController@checkAuthCode', 'as' => 'api.sms.code.check_auth']);
        Route::put('/users/@me/set_password', ['uses' => 'V2LoginController@setPassword', 'as' => 'api.users.set_password']);
	});
});

Route::group(['middleware' => ['net'], 'namespace' => 'Net', 'prefix' => 'net'], function () {
    Route::get('/shops/check', ['uses' => 'ShopsController@check', 'as' => 'net.shops.check']);
    Route::resource('shops', 'ShopsController', ['only' => ['index', 'store']]);
    Route::get('shops/show', 'ShopsController@show')->name('net.shops.show');
    Route::get('/data/link', ['uses' => 'DataController@link', 'as' => 'net.data.link']);
    Route::get('/data/areas', ['uses' => 'DataController@areas', 'as' => 'net.data.areas']);
    Route::get('/data/categories', ['uses' => 'DataController@categories', 'as' => 'net.data.categories']);
    Route::get('/users/scans', ['uses' => 'ScanLogController@scans', 'as' => 'net.users.scans']);
    Route::get('/statistics/sales', ['uses' => 'StatisticsController@sales', 'as' => 'net.statistics.sales']);
    Route::get('/statistics/sales_count', ['uses' => 'StatisticsController@salesCount', 'as' => 'net.statistics.sales_count']);
    Route::get('/statistics/sales_date_count', ['uses' => 'StatisticsController@salesDateCount', 'as' => 'net.statistics.sales_date_count']);
    Route::get('/statistics/sales_date_percent_count', ['uses' => 'StatisticsController@salesDatePercentCount', 'as' => 'net.statistics.sales_date_percent_count']);
    Route::get('/statistics/scans', ['uses' => 'StatisticsController@scans', 'as' => 'net.statistics.scans']);
    Route::get('/statistics/sales/section', ['uses' => 'StatisticsController@salesSection', 'as' => 'net.statistics.sales.section']);

    Route::get('/statistics/get_scan_count_by_salesman_id', ['uses' => 'StatisticsController@getScanCountBySalesmanId', 'as' => 'net.statistics.get_scan_count_by_salesman_id']);
    Route::get('/statistics/get_scan_count_by_filter', ['uses' => 'StatisticsController@getScanCountByFilter', 'as' => 'net.statistics.get_scan_count_by_filter']);

    Route::resource('distributors', 'DistributorsController', ['only' => ['index']]);
    Route::resource('salesmen', 'SalesmenController', ['only' => ['index']]);
    Route::get('/salesmen/users', 'SalesmenController@users')->name('net.salesmen.users');
    Route::post('/salesmen', 'SalesmenController@store')->name('net.salesmen.store');
    Route::post('/salesmen/update', 'SalesmenController@update')->name('net.salesmen.update');
    Route::post('/users', 'UsersController@update')->name('net.users.update');
    Route::post('/users/profile', 'UsersController@updateProfile')->name('net.users.updateProfile');
    Route::get('/users', 'UsersController@show')->name('net.users.show');

    Route::group(['namespace' => 'Goods', 'prefix' => 'goods'], function() {
        Route::get('orders/sales', ['uses' => 'OrderController@salesOrder', 'as' => 'net.goods.orders.sales']);
    });
});
