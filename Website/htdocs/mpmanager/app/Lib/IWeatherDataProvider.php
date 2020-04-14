<?php


namespace App\Lib;


use Psr\Http\Message\ResponseInterface;

interface IWeatherDataProvider
{
    public function getCurrentWeatherData(Array $geoLocation): ResponseInterface;


    public function getWeatherForeCast(Array $geoLocation): ResponseInterface;
}
