<?php

use App\Http\Controllers\Member\DesiredController;
use App\Http\Controllers\Member\ShiftViewController;

Route::group(['prefix' => 'shift'], function() {
    Route::get('/', ShiftViewController::class.'@list')->name('member.shift');

    Route::get('/view', ShiftViewController::class.'@view')->name('member.shift.view');
});

Route::group(['prefix' => 'desired'], function() {
    Route::get('/{week?}', DesiredController::class.'@list')->name('member.desired');
    Route::get('/edit/{date}', DesiredController::class.'@edit')->name('member.desired.edit');
    Route::post('/fix', DesiredController::class.'@fix')->name('member.desired.fix');

    Route::group(['prefix' => 'json', 'middleware' => 'json'], function () {
        Route::post('register', DesiredController::class.'@register')->name('member.desired.register');
    });
});
