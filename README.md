# ⚠️ IMPORTANT NOTE

This repository is **deprecated**.
Work on the MicroPowerManager is continued in the [EnAccess](https://enaccess.org/) fork of the repo [here](https://github.com/EnAccess/micropowermanager).

**Background:**

The MicroPowerManager was orginally developed by [Inensus](https://inensus.com/) and published under and Open Source license in this current repository.
However, it is no longer maintained in it's current form.
In 2024 EnAccess took over development and maintenance of the MicroPowerManager project.

To benefit from

- Future feature developments
- Additional provider integrations
- Bug and security fixes

It is highly-recommended that you move to the EnAccess version of the MicroPowerManager.

**Further information:**

- <https://micropowermanager.io/>
- <https://enaccess.org/materials/micropowermanager-mpm/>

---

## System Requirements

PHP ^8.0

Node ^v14.3

## Installation
1. Clone or download the repository.
2. Build the docker containers with `docker-compose up`.

## Installing Dependencies
All dependencies will be automatically installed during the installation step. However, if you require additional dependencies, install them in the `laravel` container. To install additional PHP dependencies, enter the Docker container named `laravel`, navigate to `mpmanager`, and run `composer install XXX`.

## Migrate the Database
- Run `docker exec -it laravel /bin/bash` to enter the Laravel container.
- Navigate to the `mpmanager` directory with `cd mpmanager`.
- Run `php artisan migrate --seed` to initialize the database. The `--seed` option will create the default user for login.
- The default login credentials are `admin@admin.com` and password will be given in the output of the seed command.

## phpMyAdmin
The project also includes phpMyAdmin (**only in the DEV environment**), enabling quick database operations without installing third-party software or writing commands in the terminal.

The default credentials for the database are:
```
Username: laravel
Password: laravel
```
**Please remember to change these before publishing your project.**

## Building the Frontend
The project will automatically build the frontend in **development** mode. 
If you want to build the project in **production** mode, change the `APP_ENV` variable in the `.env` file.

## Essential Configurations
The system has various bound services like Payment Services (Vodacom Tanzania and Airtel Tanzania), Ticketing Service (Trello API), Critical Logging Notification (Slack Channel), WebSocket (Pusher), etc. If you plan to use these services for payments, you need to modify the following files/configurations.

### Mobile Payment Configurations - Vodacom
1. Edit the `ips` array element in `services.php`, located under `app/config/`. The `ips` element contains a list of authorized IP addresses allowed to send transaction data.
2. Modify the following entries in the `.env` file:
```bash
VODACOM_SPID=YOUR-SPID
VODACOM_SPPASSWORD="YOUR-PASSWORD"
VODACOM_REQUEST_URL="ENDPOINT-WHERE-YOU-CONFIRM-THE-TRANSACTION"
VODACOM_BROKER_CRT="LOCATION-OF-.CRT-FILE"
VODACOM_SLL_KEY="LOCATION-OF-.KEY-FILE"
VODACOM_CERTIFICATE_AUTHORITY="LOCATION-OF-.CER-FILE"
VODACOM_SSL_CERT="LOCATION-OF-.PEM-FILE"


### Mobile Payment Configurations - Airtel
As of now, Airtel payment integration is on a separate project due to the absence of experience in setting up VPN tunnels for multiple hosts. The link to the project will be provided upon uploading to GitHub. Modify the api_user, api_password, and ips entries in services.php:

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
    ]
```
Additionally, change the AIRTEL_REQUEST_URL in the `.env` file:
```bash
AIRTEL_REQUEST_URL="AIRTEL-SERVICE-URL"
```

### STS Meter Configuration
The system currently supports only CALIN-STS meters. To communicate with Calin and generate STS Tokens, make the following changes:
1. Provide your key and the endpoint for creating tokens in the .env file:
``` bash
CALIN_KEY="CALIN-KEY"
CALIN_CLIENT_URL="CALIN-CLIENT-URL"
```
2. If you have meters sending consumption data to CALIN's server, fill in these variables: 
```bash
METER_DATA_URL="REMOTE-METER-READING-URL"
METER_DATA_KEY="METER-READING-KEY"
METER_DATA_USER="METER-READING-USER"
```
### Pusher(Web Socket)
Pusher notifies admins when a new ticket is created.
```
PUSHER_APP_ID="PUSHER-APP-ID"
PUSHER_APP_KEY="PUSHER-KEY"
PUSHER_APP_SECRET="PUSHER-APP-SECRET"
PUSHER_APP_CLUSTER="YOUR-CLUSTER ex. eu"
```

### Slack
Slack serves as the critical logging service, alerting admins about issues such as canceled transactions.
```bash
LOG_SLACK_WEBHOOK_URL="SLACK-WEBHOOK-URL"
```


## Installing  Customer Registration App (Android)
Refer to the project documentation for details on why a separate Android app is used for customer registration. Follow the provided link to access the Customer Register App Project.

## Setup Sms Communication
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

## Weather Data
The system shows the weather data on the Mini-Grid level. To be able to read out the data from  `Open Weather Map` service you have to register yourself there and get an **API-KEY**
Change the following value in `services.php`
```bash
'weather' => [
        'owm_app_id' => 'api_key',
    ]
```

## Email
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

# Deploy for Production

The production mode will automatically install **Let's Encrypt SSL certificates**. Therefore you need firstly register a domain. 


When you have your domain, the first thing to do is editing `app.conf` and `db.conf`(if you planning to use phpMyAdmin as well) files under `NginxProxy/conf.p`.

Afer that, paste `chmod +x ./install-production.sh` to make the file executable and run it via `./install-production.sh`. This will guide you through the installation and finally, it will start the services.


# Development
The development environment is served under **http://mpmanager.local**
To reach the site over the given url;
enter the following lines to your hosts file.
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



## Generate API Documentation
To generate the API documentation, jump in the `laravel` container and type `php artisan apidoc:generate` in the **mpmanager** directory. That will create a new **docs** folder under **public** folder.
The API documentation should be available under `http://mpmanager.local/docs/`. The whole API documentation will be migrated to third-party tools like Postman or Swagger.
