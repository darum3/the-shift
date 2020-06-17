<?php

use App\Http\Controllers\Member\ShiftViewController;

Route::group(['prefix' => 'shift'], function() {
    Route::get('/', ShiftViewController::class.'@list')->name('member.shift');

    Route::get('/view', ShiftViewController::class.'@view')->name('member.shift.view');
});
