<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 02.08.18
 * Time: 14:39
 */

namespace App\Http\Controllers\Meter;


use App\Http\Requests\MeterRequest;
use App\Http\Resources\ApiResource;
use App\Models\Address\Address;
use App\Models\Manufacturer;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterParameter;
use App\Models\Meter\MeterType;

/**
 * Class MeterController
 * @package App\Http\Controllers\Meter
 *
 * @group Meter
 */
class MeterController
{
    private $meter;
    private $manufacturer;
    private $meterType;

    public function __construct(Meter $meter, Manufacturer $manufacturer, MeterType $meterType)
    {
        $this->meter = $meter;
        $this->manufacturer = $manufacturer;
        $this->meterType = $meterType;
    }

    /**
     * list all meters
     */
    public function index()
    {
        $meters = $this->meter::with('manufacturer')->get();
        //return new ApiResource($meters);
        $title = 'registered Meters';
        return view('layouts.meters.list', compact('meters', 'title'));
    }


    /**
     * Shows the form where to add a new meter
     *
     */
    public function create()
    {
        $title = 'Meter';
        //get list of manufacturers
        $manufacturers = $this->manufacturer::all();
        //get list of meter types
        $meterTypes = $this->meterType::all();

        return view('layouts.meters.add', compact('title', 'manufacturers', 'meterTypes'));
    }

    public function store(MeterRequest $request)
    {
        $this->meter->serial_number = $request->get('serial_number');
        $this->meter->manufacturer()->associate(
            $this->manufacturer->find($request->get('manufacturer'))
        );
        $this->meter->meterType()->associate(
            $this->meterType->find($request->get('meter_type'))
        );
        $this->meter->save();

        return $this->index();
    }
}
