<?php

Route::group(['prefix' => 'user'], function() {
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
});