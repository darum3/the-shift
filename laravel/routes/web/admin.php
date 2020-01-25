<?php

Route::get('/', 'HomeController@index')->name('admin.home');

Route::group(['prefix' => '/contract'], function() {
    Route::get('/', 'ContractController@index')->name('admin.contract');

    Route::group(['prefix' => '/input'], function() {
        Route::get('/', 'ContractController@input')->name('admin.contract.input');
        Route::post('/confirm', 'ContractController@inputConfirm')->name('admin.contract.input.confirm');
        Route::post('/exec', 'ContractController@inputExec')->name('admin.contract.input.exec');
        Route::post('/back', 'ContractController@inputBack')->name('admin.contract.input.back');
    });
});
