<?php


Route::group(['prefix' => 'energy'], function(){
    Route::post('/', 'EnergyController@store');
    Route::get('/{id}', 'EnergyController@show');
});

