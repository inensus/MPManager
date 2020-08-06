<?php
// Android App Services
Route::group([
    'middleware' => ['agent_api',],
    'prefix' => 'app'
], function () {

    Route::post('login', 'AgentAuthController@login');
    Route::post('logout', 'AgentAuthController@logout');
    Route::post('refresh', 'AgentAuthController@refresh');
    Route::get('me', 'AgentAuthController@me');


    Route::group(['prefix' => 'agents', 'middleware' => ['jwt.verify:agent'],], function () {
        Route::post('/firebase', 'AgentController@setFirebaseToken');
        Route::get('/balance', 'AgentController@showBalance');

        Route::group(['prefix' => 'customers'], function () {
            Route::get('/', 'AgentCustomerController@index');
            Route::get('/search', 'AgentCustomerController@search');
        });
        Route::group(['prefix' => 'transactions'], function () {
            Route::get('/', 'AgentTransactionsController@index');
            Route::get('/{customerId}', 'AgentTransactionsController@show');

        });
        Route::group(['prefix' => 'appliances'], function () {
            Route::get('/', 'AgentSoldApplianceController@index');
            Route::get('/{customer}', 'AgentSoldApplianceController@customerSoldAppliances');
            Route::post('/', [
                'middleware' => 'agent.balance',
                'uses' => 'AgentSoldApplianceController@store'
            ])->name('agent-sell-appliance');
        });
        Route::group(['prefix' => 'appliance_types'], function () {
            Route::get('/', 'AgentAssignedAppliancesController@index');
        });

        Route::group(['prefix' => 'ticket'], function () {
            Route::get('/', 'AgentTicketController@index');
            Route::get('/customer/{customerId}', 'AgentTicketController@agentCustomerTickets');
            Route::post('/', 'AgentTicketController@store');
            Route::get('/{ticketId}', 'AgentTicketController@show');
        });
        Route::group(['prefix' => 'balance'], function () {
            Route::group(['prefix' => 'history'], function () {
                Route::post('/{agent}', 'AgentBalanceHistoryController@store');
            });

        });
    });

});
