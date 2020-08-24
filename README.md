
# Production 

  #### Production mode starts MPM with Let's Encrypt SSL certificate assigned to your domain name, You have to get a domain and  a subdomain (for PhpMyAdmin page) before the start 
  
  
1. Clone or download the repository
2. Open  conf.p  in NginxProxy  folder change "server_name" fields with your domain names on app.conf & db.conf files 
3. Go MPManager folder and type "install-production.sh" in terminal
4. Fill necessary informations with your domain names that used on app.conf & db.conf
   #### Example
  
   - "domain-name-used-in-app-conf.com"
   - "www.domain-name-used-in-app-conf.com"
   - "db.domain-name-used-in-db-conf.com"
5. Pass the "1.Setup the Project" step and go on set the all other steps and complete the setup

   In production setup, you have to use these two commands to starts and stops project;
    - "docker-compose -f docker-compose-prod.yml up" 
    - "docker-compose -f docker-compose-prod.yml down"
    Otherwise, with only docker-compose up command will try to run with "vhost" option then the project would not be reached
# Development

## System Requirements

PHP ^7.3

Node ^v14.3



## 1.Setup the Project
1. Clone or download the repository
2. Build the docker containers with docker-compose up
3. The project will be reachable under `mpmanager.local` as default. To be able to reach the site with that domain please 
enter the following lines to;
#### For Linux/Mac Users
```
/etc/hosts
127.0.0.1       mpmanager.local
127.0.0.1       db.mpmanager.local
``` 
#### For Windows
```
c:\windows\system32\drivers\etc\hosts
127.0.0.1       mpmanager.local
127.0.0.1       db.mpmanager.local
```
## 2. Install Dependencies 
All  dependencies will be automatically installed on step **1.2**. However, if you need additional dependencies, install them in the `laravel` container.
To Install additional php dependencies enter the Docker-Container named `laravel`  navigate to `mpmanager`  & run `php ../composer.phar install XXX`

## 3. Migrate the database changes ; 
  - Run `docker exec -it laravel /bin/bash` to jump into the laravel container
  - navigate to `mpmanager` directory with `cd mpmanager`
  - Run `php artisan migrate --seed` to initialize the Database. The `--seed`  option will create the default user to login.
  - Default user to login is `admin@admin.com` and `basic-password` .


For any further Database operations you can directly access `db.mpmanager.local` with following credentials 
```
username : laravel
password: laravel
```
## 4. Build Frontend
The project  will automatically build in **production** mode. If you want to  build the project in **development** mode, change `NMP_MODE` variable in the `.env` file.

## 5. Essential Configuration
There are bound services like the Payment Services (Vodacom Tanzania and Airtel Tanzania), Ticketing Service(Trello API), Critical Logging notification(Slack Channel), WebSocket(Pusher), etc. if you plan to get your payments through these services you need to change/edit following files/configurations

### Mobile Payment Configurations - Vodacom
1. `ips` array element in `services.php`. The file is located under `app/config/`. The element `ips` hold a list of authorized IP-addresses that are allowed to send transaction data.
2. Following changes should be done in the `.env` file
```bash
VODACOM_SPID=YOUR-SPID
VODACOM_SPPASSWORD="YOUR-PASSWORD"
VODACOM_REQUEST_URL="END-POINT WHERE YOU CONFIRM THE TRANSACTION"
VODACOM_BROKER_CRT="LOCATION-OF-.CRT-FILE"
VODACOM_SLL_KEY="LOCATION-OF-.KEY-FILE"
VODACOM_CERTIFICATE_AUTHORITY="LOCATION-OF-.CER-FILE"
VODACOM_SSL_CERT="LOCATION-OF-.PEM-FILE"
```

### Mobile Payment Configurations - Airtel
When we set up the second payment provider in our live system, we were not that experienced by setting up **VPN Tunnels** that's why we go with the idea 'one tunnel per host`. Thatswhy the airtel payment integration is on a separate project for now. We're planning to migrate it into this project in the near future. 

 --> **The project link comes as soon as we uploaded the project to GitHub** <-- 

Change the `api_user`, `api_password`, and `ips` in `services.php`
```php
  'airtel' => [
        'request_url' => env('AIRTEL_REQUEST_URL'),
        'api_user' => 'YOUR-USER',
        'api_password' => 'YOUR-PASSWORD',
        'ips' => [
            'ALLOWED_IPS TO SEND YOU TRANSACTION DATA'
        ],
    ],
```
The following change should be done in the `.env` file
```bash
AIRTEL_REQUEST_URL="AIRTEL SERVICE URL"
```

### STS Meter Configuration
Currently, the system supports only CALIN-STS meters. To be able to communicate with Calin and generate STS-Tokens, the following changes should be done;
1. Your key end the endpoint where you create those tokens. 
``` bash
CALIN_KEY="CALIN-KEY"
CALIN_CLIENT_URL="CALIN-CLIENT-URL"
```
2. If you have meters which are able to send their consumption data to CALIN's server please fill the below listed variables too 
```bash
METER_DATA_URL="REMOTE-METER-READING-URL"
METER_DATA_KEY="METER-READING-KEY"
METER_DATA_USER="METER-READING-USER"
```
### Pusher(Web Socket)
Pusher is used to notify your admins when a new ticket is been created.
```
PUSHER_APP_ID="PUSHER-APP-ID"
PUSHER_APP_KEY="PUSHER-KEY"
PUSHER_APP_SECRET="PUSHER-APP-SECRET"
PUSHER_APP_CLUSTER="YOUR-CLUSTER ex. eu"
```

### Slack
Slack is the current critical logging service that alerts the admins when something went wrong. Like a transaction is been canceled.

```bash
LOG_SLACK_WEBHOOK_URL="SLACK-WEBHOOK-URL"
```

## 6. Setup Horizon
Please follow the documentation on Laravels official website to configure horizon [Documentation](https://laravel.com/docs/7.x/horizon)

We're running 2-16 instances of each Queue. 16 on important queues like; payment, SMS & token.

## 7. Installing  Customer Registration App (Android)
Please read the project documentation to get an idea of why we're using a separate app to register customers via an Android-App.
Follow the link to get to the Customer Register App Project

## 8. Setup Sms Communication
There are currently two supported SMS-Gateways.
1. Bongo Live Tanzania
2. Inhouse SMS-Gateway Application


### Configuration for BongoLive
**Important Note: The Bongo API integration on our system is not been maintained since early-2019.**

Firstly, you have to uncomment these lines in `app/Providers/AppServiceProvider.php`. Because the default SMSProvider is the 2nd option above.

```php
 //$this->app->singleton('SmsProvider', function ($app) {
        //   return new \App\Sms\Bongo();
        //});
```
After that, change the following configuration
```bash
 'bongo' => [
            'url' => 'http://www.bongolive.co.tz/api/sendSMS.php',
            'sender' => 'SENDER_NUMBER',
            'username' => 'USER NAME',
            'password' => 'PASSWORD',
            'key' =>'KEY',
        ],
```

### Configuration for SMS-Gateway Application
**Advice: Please read the SMS-Gateway documentation before you continue.**


To lower the costs of the system we are using the following application to send and receive SMSes over that application.
To be able to use the application you need to assign following configuration values in `services.php`

You are not forced to use our inhouse solution for SMS communication. You can change the SmsProvider easily in `app/Providers/AppServiceProvider.php`

```php
 $this->app->singleton('SmsProvider', static function ($app) {
            return new AndroidGateway();
        });
```


```bash
'sms' => [

        'android' => [
            'url' => 'https://fcm.googleapis.com/fcm/send',
            'token' => 'FIREBASE_TOKEN',
            'key' => 'PHONE_KEY',
        ],
        'callback' => 'https://mpmanager.local/api/sms/%s/confirm',
    ],
```
**Dont forget to change the `callback` variable to a globaly reachable domain**

### Change Predefined SMS Text
To change the predefined SMS texts, please edit `app/Sms/SmsTypes.php`

## 9.Weather Data
The system shows the weather data on the Mini-Grid level. To be able to readout the data from  `Open Weather Map` service you have to register yourself there and get an **Api-KEY**
Change the following value in `services.php`
```bash
'weather' => [
        'owm_app_id' => 'api_key',
    ]
```

## 10.Email
To be able to send E-Mails please edit following configuration variables
```bash
return [
    'host' => '', //your host to send through
    'smtp_auth' => true, // enable SMTP authentication
    'username' => '',// SMTP username
    'password' => '', //SMTP username
    'smtp_secure' => PHPMailer::ENCRYPTION_STARTTLS,// default is tls
    'port' => '',
    'default_sender' => '',
    'default_message' => 'Please do not reply to this email', // adds a small footer text to your email
];


```
