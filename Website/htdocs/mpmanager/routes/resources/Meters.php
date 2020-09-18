<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 05.07.18
 * Time: 17:33
 */

/* Meter */
Route::group(['prefix' => 'meters', 'middleware' => 'jwt.verify'], function () {
    Route::get('/', 'MeterController@index');
    Route::get('/{id}', 'MeterController@show')->where('id', '[0-9]+');
    Route::post('/', 'MeterController@store');
    Route::put('/{id}', 'MeterController@update')->where('id', '[0-9]+');
    Route::get('/search', 'MeterController@Search');
    Route::delete('/{ownerId}', 'MeterController@destroy');
    Route::get('{serialNumber}/revenue', 'RevenueController@meterRevenue');
    /* Meter types */
    Route::group(['prefix' => 'types'], function () {
        Route::get('/', 'MeterTypeController@index');
        Route::get('/{id}', 'MeterTypeController@show');
        Route::post('/', 'MeterTypeController@store');
        Route::put('/{meterType}', 'MeterTypeController@update');
        Route::get('/{id}/list', 'MeterTypeController@meterList');
    });

    Route::get('/{id}/all', 'MeterController@allRelations');


    Route::group(['prefix' => 'parameters'], function () {
        Route::get('/', 'MeterParameterController@index'); // list of all meters which are related to a customer
        Route::post('/', 'MeterParameterController@store');
        Route::get('/connection-types', 'MeterParameterController@connectionTypes');
    });

    Route::get('/parameters/{meterParameter}', 'MeterParameterController@show');


    Route::put('/{serialNumber}/parameters', 'MeterParameterController@update');


    Route::get('/{serialNumber}/transactions', 'MeterController@transactionList');
    Route::get('{serialNumber}/consumptions/{start}/{end}', 'MeterController@consumptionList');

    Route::get('/geoList', 'MeterController@meterGeoList');
});
