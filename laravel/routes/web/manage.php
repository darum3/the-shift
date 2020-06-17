<?php

Route::group(['prefix' => 'work_type'], function() {
    Route::get('/', 'WorkTypeController@index')->name('manage.work_type');
    Route::group(['prefix' => 'add'], function() {
        Route::get('/', 'WorkTypeController@input')->name('manage.work_type.add.input');
        Route::post('/confirm', 'WorkTypeController@confirm')->name('manage.work_type.add.confirm');
        Route::post('/exec', 'WorkTypeController@exec')->name('manage.work_type.add.exec');
    });
    Route::group(['prefix' => 'edit/{id}'], function () {
        Route::get('/', 'WorkTypeController@edit')->name('manage.work_type.edit.input');
        Route::post('/confirm', 'WorkTypeController@editConfirm')->name('manage.work_type.edit.confirm');
        Route::post('/exec', 'WorkTypeController@editExec')->name('manage.work_type.edit.exec');
    });
});

Route::group(['prefix' => '/group'], function() {
    Route::get('/', 'GroupController@index')->name('manage.group');
    Route::group(['prefix' => 'add'], function() {
        Route::get('/', 'GroupController@input')->name('manage.group.add.input');
        Route::post('/confirm', 'GroupController@confirm')->name('manage.group.add.confirm');
        Route::post('/exec', 'GroupController@exec')->name('manage.group.add.exec');
    });
    Route::group(['prefix' => '{id}/admin_add'], function() {
        Route::get('/', 'AdminAddController@input')->name('manage.group.admin_add.input');
        Route::post('/confirm', 'AdminAddController@confirm')->name('manage.group.admin_add.confirm');
        Route::post('/exec', 'AdminAddController@exec')->name('manage.group.admin_add.exec');
    });
});

