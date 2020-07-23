<?php

use App\Events\Kemal;
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


require_once 'api_paths/transactions.php';


Route::group(['prefix' => 'energies'], static function () {
    Route::post('/', 'EnergyController@store');

});


Route::group(['prefix' => 'solar'], static function () {
    Route::post('/', 'SolarController@store');
});

Route::group(['prefix' => 'batteries'], static function () {
    Route::post('/{miniGridId}', 'BatteryController@store');
});

Route::group(['prefix' => 'mini-grids'], static function () {
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

Route::post('/revenue/analysis/', 'RevenueController@analysis');
Route::post('/revenue/trends/{id}', 'RevenueController@trending');
Route::post('/revenue', 'RevenueController@revenueData');
Route::get('/revenue/tickets/{id}', 'RevenueController@ticketData');

/*Manufacturer*/
Route::group(['prefix' => 'manufacturers'], static function () {
    Route::get('/', 'ManufacturerController@index');
    Route::get('/{manufacturer}', 'ManufacturerController@show');
    Route::post('/', 'ManufacturerController@store');
    Route::put('/{id}', 'ManufacturerController@update');

});

Route::group(['prefix' => 'pv',], static function () {
    Route::post('/', 'PVController@store')->middleware('data.controller');
    Route::get('/{miniGridId}', 'PVController@show');

}
);

/* Tariff */
Route::group(['middleware' => 'jwt.verify', 'prefix' => 'tariffs'], function () {
    Route::get('/', 'TariffController@index');
    Route::post('/', 'TariffController@store');
    Route::get('/{tariff}', 'TariffController@show');


});


//JWT authentication
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], static function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');

});

//JWT authentication for agent
Route::group([
    'middleware' => 'agent_api',
    'prefix' => 'agents'

], static function ($router) {

    Route::post('login', 'AgentAuthController@login');
    Route::post('logout', 'AgentAuthController@logout');
    Route::post('refresh', 'AgentAuthController@refresh');
    Route::get('me', 'AgentAuthController@me');

    Route::post('/', 'AgentController@store');
    Route::post('/reset-password', 'AgentController@resetPassword');
    Route::put('/{agent}', 'AgentController@update');

    Route::get('/', 'AgentController@index');
    Route::post('/firebase', 'AgentController@setFirebaseToken');
    Route::get('/balance', 'AgentController@showBalance');
    Route::group(['prefix' => 'customers'], function () {
        Route::get('/', 'AgentCustomerController@index');

    });
    Route::group(['prefix' => 'transactions'], function () {
        Route::get('/', 'AgentTransactionsController@index');
        Route::get('/{customerId}', 'AgentTransactionsController@agentCustomerTransactions');


    });
    Route::group(['prefix' => 'assigned'], function () {
        Route::get('/appliances', 'AgentAssignedAppliancesController@index');
        Route::post('/appliances', 'AgentAssignedAppliancesController@store');
    });
    Route::group(['prefix' => 'sold'], function () {
        Route::get('/appliances', 'AgentSoldApplianceController@index');
        Route::post('/appliances', 'AgentSoldApplianceController@store');
    });
    Route::group(['prefix' => 'ticket'], function () {
        Route::get('/', 'AgentTicketController@index');
        Route::get('/customer/{customerId}', 'AgentTicketController@agentCustomerTickets');
        Route::post('/', 'AgentTicketController@store');
    });
    Route::group(['prefix' => 'balance'], function () {
        Route::group(['prefix' => 'history'], function () {
            Route::get('/', 'AgentBalanceHistoryController@index');
            Route::post('/{agent}', 'AgentBalanceHistoryController@store');
        });

    });
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => 'assets'], function () {
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

Route::group(['prefix' => 'people'], function () {
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


Route::group(['prefix' => 'admin'], static function () {
    Route::post('/', 'AdminController@store');
    Route::post('/forgot-password', 'AdminController@forgotPassword');
    Route::put('/{user}', 'AdminController@update');
    Route::get('/users', 'AdminController@index');
    Route::post('{user}/addresses', 'AddressController@storeAdmin');
    Route::put('{user}/addresses', 'AddressController@updateAdmin');
    Route::get('{user}/addresses', 'AddressController@adminAddress');
});
Route::group(['prefix' => 'transactions', 'middleware' => ['transaction.auth', 'transaction.request']],
    function () {
        Route::post('/airtel', 'TransactionController@store');
        Route::post('/vodacom', ['as' => 'vodacomTransaction', 'uses' => 'TransactionController@store']);
        Route::post('/agent', ['as' => 'agentTransaction', 'uses' => 'TransactionController@store']);

    });


Route::get('hearthBeat', function () {

    event(new Kemal());
    broadcast(new Kemal());

});

Route::get('paymenthistories/{personId}/payments/{period}/{limit?}/{order?}',
    'PaymentHistoryController@show')->where('personId', '[0-9]+');
Route::get('paymenthistories/{personId}/flow/{year?}', 'PaymentHistoryController@byYear')->where('personId', '[0-9]+');
Route::get('paymenthistories/{personId}/period', 'PaymentHistoryController@getPaymentPeriod')->where('personId',
    '[0-9]+');
Route::get('paymenthistories/debt/{personId}', 'PaymentHistoryController@debts')->where('personId', '[0-9]+');
Route::post('androidApp', function (AndroidAppRequest $r) {
    try {
        DB::beginTransaction();

        //check if the meter id or the phone already exists
        $meter = Meter::where('serial_number', $r->get('serial_number'))->first();
        $person = null;

        if ($meter === null) {
            $meter = new Meter();
            $meterParameter = new MeterParameter();
            $geoLocation = new GeographicalInformation();
        } else {
            $meterParameter = MeterParameter::where('meter_id', $meter->id)->first();
            $geoLocation = $meterParameter->geo()->first();
            if ($geoLocation === null) {
                $geoLocation = new GeographicalInformation();
            }

            $person = Person::whereHas('meters', function ($q) use ($meterParameter) {
                return $q->where('id', $meterParameter->id);
            })->first();

        }

        if ($person === null) {
            $personService = new PersonService(new App\Models\Person\Person());
            $person = $personService->createFromRequest($r);
        }

        $meter->serial_number = $r->get('serial_number');
        $meter->manufacturer()->associate(Manufacturer::findOrFail($r->get('manufacturer')));
        $meter->meterType()->associate(MeterType::findOrFail($r->get('meter_type')));
        $meter->updated_at = date('Y-m-d h:i:s');
        $meter->save();


        $geoLocation->points = $r->get('geo_points');


        $meterParameter->meter()->associate($meter);


        $meterParameter->owner()->associate($person);
        $meterParameter->tariff()->associate(MeterTariff::findOrFail($r->get('tariff_id')));
        $meterParameter->save();
        $meterParameter->geo()->save($geoLocation);


        $address = new Address();
        $address = $address->create([
            'city_id' => request('city_id') ?? 1,
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


Route::group(['prefix' => 'sms'], function () {
    Route::get('/{number}', 'SmsController@show');
    Route::get('/phone/{number}', 'SmsController@byPhone');
    Route::get('/{uuid}/confirm', 'SmsController@update');
    Route::get('/', 'SmsController@index');
    Route::get('search/{search}', 'SmsController@search');
    Route::post('/storeandsend', 'SmsController@storeAndSend');
    Route::post('/', 'SmsController@store');
    Route::post('/bulk', 'SmsController@storeBulk');

});


Route::post('paymenthistories/overview', 'PaymentHistoryController@getPaymentRange');


Route::group(['prefix' => 'targets'], function () {
    Route::get('/', 'TargetController@index');
    Route::post('/', 'TargetController@store');
    Route::get('/{id}', 'TargetController@show');
    Route::post('/slots', 'TargetController@getSlotsForDate');
});

Route::group(['prefix' => 'connection-types'], function () {
    Route::get('/', 'ConnectionTypeController@index');
    Route::post('/', 'ConnectionTypeController@store');
});
Route::get('/sub-connection-types', 'SubConnectionTypeController@index');


Route::group(['prefix' => '/clusters'], function () {
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

Route::get('/clusterlist', 'ClusterController@index');


Route::get('/export/weekly', '\App\Http\Controllers\Export\Reports@generate');

Route::get('/reports', 'ReportController@index');
Route::get('/reports/{id}/download', 'ReportController@download');


Route::group(['prefix' => 'connection-groups'], function () {
    Route::get('/', 'ConnectionGroupController@index');
    Route::post('/', 'ConnectionGroupController@store');
});


Route::group(['prefix' => '/maintenance'], function () {
    Route::get('/', 'MaintenanceUserController@index');
    Route::post('/user', 'MaintenanceUserController@store')
        ->middleware('restriction:maintenance-user');
});

Route::post('/restrictions', 'RestrictionController@store');
