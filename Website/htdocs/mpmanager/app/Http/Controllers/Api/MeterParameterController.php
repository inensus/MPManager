<?php

namespace App\Http\Controllers;


use App\Http\Requests\MeterParameterRequest;
use App\Http\Resources\ApiResource;
use App\Models\AccessRate\AccessRatePayment;
use App\Models\ConnectionType;
use App\Models\GeographicalInformation;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterParameter;
use App\Models\Meter\MeterTariff;
use App\Models\Person\Person;
use App\PaymentHandler\AccessRate;
use Illuminate\Http\Request;

/**
 * @group MeterParameter
 * Class MeterParameterController
 * @package App\Http\Controllers
 */
class MeterParameterController extends Controller
{
    /**
     * @var MeterParameter
     */
    private $meterParameter;
    /**
     * @var ConnectionType
     */
    private $connectionType;

    /**
     * MeterParameterController constructor.
     *
     * @param MeterParameter $meterParameter
     * @param ConnectionType $connectionType
     */
    public function __construct(MeterParameter $meterParameter, ConnectionType $connectionType)
    {
        $this->meterParameter = $meterParameter;
        $this->connectionType = $connectionType;
    }

    /**
     * List
     * @responseFile responses/meterparameters/meterparameters.list.json
     * @return void
     */
    public function index()
    {
        return new ApiResource($this->meterParameter->get());
    }


    /**
     * Create
     *
     * @param Request $request
     *
     * @return ApiResource
     */
    public function store(MeterParameterRequest $request)
    {
        $geoLocation = new GeographicalInformation();
        $geoLocation->points = request('geo_points');

        $meterParameter = new MeterParameter();
        $meterParameter->meter_id = request('meter_id');
        $meterParameter->tariff_id = request('tariff_id');
        $meterParameter->owner()->associate(Person::find(request('customer_id')));

        $meterParameter->save();
        $meterParameter->geo()->save($geoLocation);
        //initializes a new Access Rate Payment for the next Period
        event('accessRatePayment.initialize', $meterParameter);
        // changes in_use parameter of the meter
        event('meterparameter.saved', $meterParameter->meter_id);

        return new ApiResource(
            $meterParameter
        );
    }

    /**
     * List with meters
     * A list with following relations
     * - Owner
     * - Meter
     * - Tariff
     * @urlParam meterParameter int required
     * @responseFile responses/meterparameters/meterparameter.detail.json
     * @param MeterParameter $meterParameter
     *
     * @return ApiResource
     */
    public function show(MeterParameter $meterParameter): ApiResource
    {
        $m = MeterParameter::with('owner', 'meter', 'tariff')->find($meterParameter);
        return new ApiResource($m);
    }


    /**
     * Update
     * @urlParam meterId int required
     * @bodyParam tariffId int
     * @bodyParam personId int
     *
     * @param int $meterId
     *
     * @return void
     */
    public function update(string $meterId)
    {
        $personId = \request('personId') ?? -1;
        $tariffId = \request('tariffId') ?? -1;
        $connectionId = \request('connectionId') ?? -1;
        $parameter = $this->meterParameter->where('meter_id', $meterId)->first();
        if ($personId !== -1) {
            $parameter->owner()->associate(Person::findOrFail($personId));
        } elseif ($connectionId !== -1) {
            $parameter->connectionType()->associate(ConnectionType::findOrFail($connectionId));
        } elseif ($tariffId !== -1) {
            $parameter->tariff()->associate(MeterTariff::findOrFail($tariffId));

            $tariff = MeterTariff::find($tariffId);
            $accessRate = $tariff->accessRate()->first();

            $acP = $parameter->meter()->first()->accessRatePayment()->first();

            $acP->access_rate_id = $accessRate->id;
            $acP->update();

        } else {
            return;
        }
        $parameter->save();
        return new ApiResource($parameter);
    }

    /**
     * List of connection types
     * A list of connection types and the meters which belong to the connection type
     * @responseFile /responses/meterparameters/meterparameter.connectiontype.list.json
     * @param Request $request
     * @return ApiResource
     */
    public function connectionTypes(Request $request)
    {
        return new ApiResource($this->connectionType->numberOfConnections());
    }

}
