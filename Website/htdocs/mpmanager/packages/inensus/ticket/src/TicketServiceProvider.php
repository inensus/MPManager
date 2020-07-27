<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 21.06.18
 * Time: 13:20
 */

namespace Inensus\Ticket;


use Illuminate\Support\ServiceProvider;

class TicketServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');


        $this->publishes([
            __DIR__ . '/../config/tickets.php' => config_path('tickets.php'),
        ], 'config');

        if (!class_exists('CreateTicketingTables')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../database/migrations/create_outsource_report.php.stub' => $this->app->databasePath() . "/migrations/{$timestamp}_create_outsource_report.php",
                __DIR__ . '/../database/migrations/create_ticketing_tables.php.stub' => $this->app->databasePath() . "/migrations/{$timestamp}_create_ticketing_tables.php",
                __DIR__ . '/../database/migrations/create_ticketing_tables_outsource.php.stub' => $this->app->databasePath() . "/migrations/{$timestamp}_create_ticketing_tables_outsource.php",
            ], 'migrations');
        }

    }


    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/tickets.php',
            'tickets'
        );
    }

}
