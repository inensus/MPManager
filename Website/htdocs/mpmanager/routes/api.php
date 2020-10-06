<?php

use App\Http\Requests\AndroidAppRequest;
use App\Http\Resources\ApiResource;
use App\Http\Services\PersonService;
use App\Models\Address\Address;
use App\Models\GeographicalInformation;
use App\Models\Manufacturer;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterParameter;
use App\Models\Meter\MeterTariff;
use App\Models\Meter\MeterType;
use App\Models\Person\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

//Routes for City resource
require_once 'resources/Cities.php';
//Routes for Country resource
require_once 'resources/Countries.php';
//Routes for meter resource
require_once 'resources/Meters.php';
//Routes for Addresses resource
require_once 'resources/Addresses.php';
// Transaction routes
require_once 'api_paths/transactions.php';
// Agent routes
require_once 'resources/AgentApp.php';
// Agent Web panel routes
require_once 'resources/AgentWeb.php';

//               ['middleware' => 'jwt.verify', 'uses' => 'AdminController@index']


//JWT authentication
Route::group(['middleware' => 'api', 'prefix' => 'auth'], static function () {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');

});
// admin
Route::group(['prefix' => 'admin', 'middleware' => 'jwt.verify'], static function () {
    Route::post('/', 'AdminController@store');
    Route::post('/forgot-password', 'AdminController@forgotPassword');
    Route::put('/{user}', 'AdminController@update');
    Route::get('/users', 'AdminController@index');
    Route::post('{user}/addresses', 'AddressController@storeAdmin');
    Route::put('{user}/addresses', 'AddressController@updateAdmin');
    Route::get('{user}/addresses', 'AddressController@adminAddress');
});
// Assets
Route::group(['prefix' => 'assets', 'middleware' => 'jwt.verify'], function () {
    Route::group(['prefix' => 'types'], function () {
        Route::get('/', 'AssetTypeController@index');
        Route::post('/', 'AssetTypeController@store');
        Route::put('/{asset_type}', 'AssetTypeController@update');
        Route::delete('/{asset_type}', 'AssetTypeController@destroy');

        Route::post('/{asset_type}/people/{person}', 'AssetPersonController@store');
        Route::get('/people/{person}', 'AssetPersonController@show');
    });

    Route::group(['prefix' => 'rates'], static function () {
        Route::put('/{asset_rate}', 'AssetRateController@update');
    });

});
// Batteries
Route::group(['prefix' => 'batteries'], static function () {
    Route::post('/', 'BatteryController@store');
});
// Clusters
Route::group(['prefix' => '/clusters', 'middleware' => 'jwt.verify'], static function () {
    Route::post('/{id}/revenue/analysis', 'RevenueController@getRevenueAnalysisForCluster');
    Route::get('/', 'ClusterController@index');
    Route::get('/geo', 'ClusterController@geo');
    Route::get('/revenue', 'RevenueController@getPeriodicClustersRevenue');
    Route::get('/{id}/revenue', 'RevenueController@getClusterRevenue');
    Route::post('/', 'ClusterController@store');
    Route::get('/{id}', 'ClusterController@show');
    Route::get('/{cluster}/geo', 'ClusterController@showGeo');
    Route::get('/{id}/cities-revenue', 'RevenueController@getPeriodicMiniGridsRevenue');
});
// Connection-Groups
Route::group(['prefix' => 'connection-groups', 'middleware' => 'jwt.verify'], static function () {
    Route::get('/', 'ConnectionGroupController@index');
    Route::post('/', 'ConnectionGroupController@store');
    Route::put('/{connectionGroup}', 'ConnectionGroupController@update');
});
// Connection-Types
Route::group(['prefix' => 'connection-types', 'middleware' => 'jwt.verify'], static function () {
    Route::get('/', 'ConnectionTypeController@index');
    Route::post('/', 'ConnectionTypeController@store');
    Route::get('/{connectionTypeId?}', 'ConnectionTypeController@show');
    Route::put('/{connectionType}', 'ConnectionTypeController@update');

});
// Energies
Route::group(['prefix' => 'energies', 'middleware' => 'jwt.verify'], static function () {
    Route::post('/', 'EnergyController@store');

});
// Generation-Assets
Route::group(['prefix' => 'generation-assets', 'middleware' => 'jwt.verify'], static function () {
    Route::post('/grid', 'MiniGridFrequencyController@store');
});
// Maintenance
Route::group(['prefix' => '/maintenance', 'middleware' => 'jwt.verify'], static function () {
    Route::get('/', 'MaintenanceUserController@index');
    Route::post('/user', 'MaintenanceUserController@store')
        ->middleware('restriction:maintenance-user');
});
// Manufacturers
Route::group(['prefix' => 'manufacturers', 'middleware' => 'jwt.verify'], static function () {
    Route::get('/', 'ManufacturerController@index');
    Route::get('/{manufacturer}', 'ManufacturerController@show');
    Route::post('/', 'ManufacturerController@store');
    Route::put('/{id}', 'ManufacturerController@update');

});
// Mini-Grids
Route::group(['prefix' => 'mini-grids', 'middleware' => 'jwt.verify'], static function () {
    Route::get('/', 'MiniGridController@index');
    Route::post('/', 'MiniGridController@store');
    Route::get('/{id}', 'MiniGridController@show');
    Route::post('/{id}/transactions', 'RevenueController@transactionRevenuePerMiniGrid');
    Route::post('/{id}/energy', 'RevenueController@soldEnergyPerMiniGrid');
    Route::get('/{id}/batteries', 'BatteryController@showByMiniGrid');
    Route::get('/{miniGridId}/solar', 'SolarController@showByMiniGrid');

    Route::put('/{miniGrid}', 'MiniGridController@update')->middleware('restriction:enable-data-stream');
    Route::get('/{MiniGrid}/battery-readings', 'BatteryController@show');
    Route::get('/{MiniGrid}/energy-readings', 'EnergyController@show');
    Route::get('/{MiniGrid}/solar-readings', 'SolarController@listByMiniGrid');
    Route::get('/{MiniGrid}/pv-readings', 'PVController@showReadings');
    Route::get('/{MiniGrid}/weather-readings', 'BatteryController@show');

});
// PaymentHistories
Route::group(['prefix' => 'paymenthistories', 'middleware' => 'jwt.verify'], function () {
    Route::get('/{personId}/flow/{year?}', 'PaymentHistoryController@byYear')->where('personId', '[0-9]+');
    Route::get('/{personId}/period', 'PaymentHistoryController@getPaymentPeriod')->where('personId', '[0-9]+');
    Route::get('/debt/{personId}', 'PaymentHistoryController@debts')->where('personId', '[0-9]+');
    Route::post('/overview', 'PaymentHistoryController@getPaymentRange');
    Route::get('/{personId}/payments/{period}/{limit?}/{order?}', 'PaymentHistoryController@show')->where('personId',
        '[0-9]+');
});
// People
Route::group(['prefix' => 'people', 'middleware' => 'jwt.verify'], static function () {
    Route::get('/{person}/transactions', 'PersonController@transactions');
    Route::get('/{person}/addresses', 'PersonController@addresses');
    Route::get('/{person}/meters', 'MeterController@personMeters');
    Route::get('/{person}/meters/geo', 'MeterController@meterGeo');
    Route::post('/{person}/addresses', 'AddressController@store');
    Route::put('/{person}/addresses', 'AddressController@update');
    Route::get('/search', 'PersonController@search');
    Route::delete('/{person}', 'PersonController@destroy');

    Route::get('/', 'PersonController@index');
    Route::get('/all', 'PersonController@list');
    Route::get('/{person}', 'PersonController@show');
    Route::post('/', 'PersonController@store');
    Route::put('/{person}', 'PersonController@update');

});
// PV
Route::group(['prefix' => 'pv'], static function () {
    Route::post('/', 'PVController@store')->middleware('data.controller');
    Route::get('/{miniGridId}', ['middleware' => 'jwt.verify', 'uses' => 'PVController@show']);

});
// Reports
Route::group(['prefix' => 'reports', 'middleware' => 'jwt.verify'], function () {
    Route::get('/', 'ReportController@index');
    Route::get('/{id}/download', 'ReportController@download');
});
// Revenue
Route::group(['prefix' => 'revenue', 'middleware' => 'jwt.verify'], static function () {
    Route::post('/analysis/', 'RevenueController@analysis');
    Route::post('/trends/{id}', 'RevenueController@trending');
    Route::post('/', 'RevenueController@revenueData');
    Route::get('/tickets/{id}', 'RevenueController@ticketData');
});
// Sms
Route::group(['prefix' => 'sms', 'middleware' => 'jwt.verify'], static function () {
    Route::get('/{number}', 'SmsController@show');
    Route::get('/phone/{number}', 'SmsController@byPhone');
    Route::get('/{uuid}/confirm', 'SmsController@update');
    Route::get('/', 'SmsController@index');
    Route::get('search/{search}', 'SmsController@search');
    Route::post('/storeandsend', 'SmsController@storeAndSend');
    Route::post('/', 'SmsController@store');
    Route::post('/bulk', 'SmsController@storeBulk');

});
// Solar
Route::group(['prefix' => 'solar'], static function () {
    Route::post('/', 'SolarController@store');
});
// Sub-Connection-Types
Route::group(['prefix' => 'sub-connection-types', 'middleware' => 'jwt.verify'], static function () {
    Route::get('/{connectionTypeId?}', 'SubConnectionTypeController@index');
    Route::post('/', 'SubConnectionTypeController@store');
    Route::get('/{id}', 'SubConnectionTypeController@show');
    Route::put('/{subConnectionType}', 'SubConnectionTypeController@update');

});
// Targets
Route::group(['prefix' => 'targets', 'middleware' => 'jwt.verify'], static function () {
    Route::get('/', 'TargetController@index');
    Route::post('/', 'TargetController@store');
    Route::get('/{id}', 'TargetController@show');
    Route::post('/slots', 'TargetController@getSlotsForDate');
});
// Tariffs
Route::group(['middleware' => 'jwt.verify', 'prefix' => 'tariffs'], static function () {
    Route::get('/', 'TariffController@index');
    Route::post('/', 'TariffController@store');
    Route::put('/{tariff}', 'TariffController@update');
    Route::get('/{tariff}', 'TariffController@show');
    Route::delete('/{tariff}', 'TariffController@destroy');
    Route::get('/{tariff}/usage-count', 'TariffController@usages');
    Route::put('/{tariff}/change-meters-tariff/{changeId}', 'TariffController@changeMetersTariff');

});
// Transactions
Route::group(['prefix' => 'transactions', 'middleware' => ['transaction.auth', 'transaction.request']],
    static function () {
        Route::post('/airtel', 'TransactionController@store');

        Route::post('/vodacom', ['as' => 'vodacomTransaction', 'uses' => 'TransactionController@store']);
        Route::post('/agent',
            ['as' => 'agent-transaction', 'uses' => 'TransactionController@store', 'middleware' => 'agent.balance']);

    });

Route::group(['prefix' => 'time-of-usages', 'middleware' => 'jwt.verify'], static function () {
    Route::delete('/{timeOfUsage}', 'TimeOfUsageController@destroy');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('androidApp', static function (AndroidAppRequest $r) {
    try {
        DB::beginTransaction();
        //check if the meter id or the phone already exists
        $meter = Meter::query()->where('serial_number', $r->get('serial_number'))->first();
        $person = null;

        if ($meter === null) {
            $meter = new Meter();
            $meterParameter = new MeterParameter();
            $geoLocation = new GeographicalInformation();
        } else {

            $meterParameter = MeterParameter::query()->where('meter_id', $meter->id)->first();
            $geoLocation = $meterParameter->geo()->first();
            if ($geoLocation === null) {
                $geoLocation = new GeographicalInformation();
            }

            $person = Person::query()->whereHas('meters', static function ($q) use ($meterParameter) {
                return $q->where('id', $meterParameter->id);
            })->first();

        }

        if ($person === null) {
            $personService = new PersonService(new App\Models\Person\Person());
            $person = $personService->createFromRequest($r);
        }

        $meter->serial_number = $r->get('serial_number');
        $meter->manufacturer()->associate(Manufacturer::query()->findOrFail($r->get('manufacturer')));
        $meter->meterType()->associate(MeterType::query()->findOrFail($r->get('meter_type')));
        $meter->updated_at = date('Y-m-d h:i:s');
        $meter->save();


        $geoLocation->points = $r->get('geo_points');


        $meterParameter->meter()->associate($meter);


        $meterParameter->owner()->associate($person);
        $meterParameter->tariff()->associate(MeterTariff::query()->findOrFail($r->get('tariff_id')));
        $meterParameter->save();
        $meterParameter->geo()->save($geoLocation);


        $address = new Address();
        $address = $address->newQuery()->create([
            'city_id' => request()->input('city_id') ?? 1,
        ]);
        $address->owner()->associate($meterParameter);

        $address->geo()->associate($meterParameter->geo);
        $address->save();

        //initializes a new Access Rate Payment for the next Period
        event('accessRatePayment.initialize', $meterParameter);
        // changes in_use parameter of the meter
        event('meterparameter.saved', $meterParameter->meter_id);
        DB::commit();

        return (new ApiResource($person))->response()->setStatusCode(201);
    } catch (Exception $e) {
        DB::rollBack();
        Log::critical('Error while adding new Customer', ['message' => $e->getMessage()]);

        return (new Response($e->getMessage()))->setStatusCode(409);
    }
});

Route::get('/clusterlist', 'ClusterController@index');

Route::post('/restrictions', 'RestrictionController@store');


