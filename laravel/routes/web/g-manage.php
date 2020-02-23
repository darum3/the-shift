<?php

Route::group(['prefix' => 'user'], function() {
    Route::get('/', 'UserController@index')->name('g-manage.user');

    Route::group(['prefix' => 'add'], function() {
        Route::get('/input', 'UserController@add')->name('g-manage.user.add.input');
        Route::post('/confirm', 'UserController@confirm')->name('g-manage.user.add.confirm');
        Route::post('/exec', 'UserController@exec')->name('g-manage.user.add.exec');
    });
});
