<?php

Route::group(['prefix' => 'user'], function() {
    // 自グループのユーザ管理
    Route::get('/', 'UserController@index')->name('g-manage.user');

    Route::group(['prefix' => 'add'], function() {
        Route::get('/input', 'UserController@add')->name('g-manage.user.add.input');
        Route::post('/confirm', 'UserController@confirm')->name('g-manage.user.add.confirm');
        Route::post('/exec', 'UserController@exec')->name('g-manage.user.add.exec');
    });

    Route::group(['prefix' => '{user_id}/delete'], function() {
        Route::post('/confirm', 'UserController@delConfirm')->name('g-manage.user.del.confirm');
        Route::get('/confirm', 'UserController@delConfirm');
        Route::post('/exec', 'UserController@delExec')->name('g-manage.user.del.exec');
    });

    Route::group(['prefix' => 'json', 'middleware' => 'json'], function() {
        Route::get('/', 'UserJsonController@list')->name('g-manage.user.json.list');
    });
});

Route::group(['prefix' => 'shift'], function() {
    // 自グループのシフト操作
    Route::get('/', 'ShiftEditController@view')->name('g-manage.shift.view');

    Route::group(['prefix' => 'json', 'middleware' => 'json'], function() {
        Route::post('/', 'ShiftMaintenanceController@insert')->name('g-manage.shift.json.insert');
        Route::get('/{date?}', 'ShiftMaintenanceController@get')->name('g-manage.shift.json.get');
        Route::delete('/',ShiftMaintenanceController::class.'@delete')->name('g-manage.shift.json.delete');
        Route::post('/fix', ShiftMaintenanceController::class.'@fix')->name('g-manage.shift.json.fix');
    });
});

Route::group(['prefix' => 'desired'], function() {
    // シフト提出確認
    Route::get('/{start_date?}', DesiredController::class.'@list')->name('g-manage.desired.list');
});
