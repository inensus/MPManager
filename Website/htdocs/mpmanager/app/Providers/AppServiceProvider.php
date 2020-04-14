<?php

namespace App\Providers;


use App\Helpers\MailHelper;
use App\Lib\IWeatherDataProvider;
use App\ManufacturerApi\CalinApi;
use App\Misc\LoanDataContainer;
use App\Models\AccessRate\AccessRate;
use App\Models\AssetRate;
use App\Models\Cluster;
use App\Models\Manufacturer;
use App\Models\Meter\MeterParameter;
use App\Models\Meter\MeterToken;
use App\Models\MiniGrid;
use App\Models\Person\Person;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\VodacomTransaction;
use App\Sms\AndroidGateway;
use App\Transaction\AirtelTransaction;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Weather\OpenWeatherMap;
use PHPMailer\PHPMailer\PHPMailer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Maria DB work-around
        Schema::defaultStringLength(191);

        //Rename polymorphic relations
        Relation::morphMap(
            [
                'person' => Person::class,
                'manufacturer' => Manufacturer::class,
                'meter_parameter' => MeterParameter::class,
                'token' => MeterToken::class,
                'transaction' => Transaction::class,
                'vodacom_transaction' => VodacomTransaction::class,
                'airtel_transaction' => \App\Models\Transaction\AirtelTransaction::class,
                'access_rate' => AccessRate::class,
                'asset_loan' => AssetRate::class,
                'cluster' => Cluster::class,
                'mini-grid' => MiniGrid::class,
            ]
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {

        $this->app->singleton('CalinApi', static function ($app) {
            return new CalinApi(new Client());
        });


        $this->app->singleton('MailProvider', static function ($app) {
            return new MailHelper(new PHPMailer());
        });

        /** Use the current sms provider */
        //$this->app->singleton('SmsProvider', function ($app) {
        //   return new \App\Sms\Bongo();
        //});
        /*Use android device as sms gateway*/
        $this->app->singleton('SmsProvider', static function ($app) {
            return new AndroidGateway();
        });
        $this->app->singleton('LoanDataContainerProvider', static function ($app) {
            return new LoanDataContainer();
        });
        $this->app->singleton('VodacomPaymentProvider', static function () {
            return new \App\Transaction\VodacomTransaction();
        });

        $this->app->singleton('AirtelPaymentProvider', static function () {
            return new AirtelTransaction(
                new \App\Models\Transaction\AirtelTransaction(),
                new Transaction()
            );
        });

        //Bind open weather map  to IWeatherDataProvider interface
        $this->app->bind(IWeatherDataProvider::class, OpenWeatherMap::class);

    }
}
