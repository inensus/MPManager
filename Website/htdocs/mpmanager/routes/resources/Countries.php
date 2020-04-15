<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 05.07.18
 * Time: 17:32
 */
/* Country*/
Route::group(['prefix' => 'countries'], function () {
    Route::get('/', 'CountryController@index'); // list of all countries
    Route::post('/', 'CountryController@store'); // store new country
    Route::get('/{country}', 'CountryController@show'); // country detail

});
