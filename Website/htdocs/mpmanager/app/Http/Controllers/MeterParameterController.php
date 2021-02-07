<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeterParameterRequest;
use App\Http\Resources\ApiResource;
use App\Models\ConnectionType;
use App\Models\GeographicalInformation;
use App\Models\Meter\MeterParameter;
use App\Models\Meter\MeterTariff;
use App\Models\Person\Person;
use App\Models\SubConnectionType;
use Illuminate\Http\Request;

/**
 * @group   MeterParameter
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
     *
     * @responseFile responses/meterparameters/meterparameters.list.json
     *
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource($this->meterParameter->get());
    }


    /**
     * Create
     *
     * @param MeterParameterRequest $request
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
     *
     * @urlParam     meterParameter int required
     * @responseFile responses/meterparameters/meterparameter.detail.json
     * @param        MeterParameter $meterParameter
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
     *
     * @urlParam meterId int required
     *
     * @bodyParam tariffId int
     * @bodyParam personId int
     *
     * @param string $meterId
     *
     * @return ApiResource|null
     */
    public function update(string $meterId)
    {

        $personId = request()->input('personId', -1);
        $tariffId = request()->input('tariffId',-1);
        $connectionId = request()->input('connectionId',-1);
        $parameter = $this->meterParameter->where('meter_id', $meterId)->first();

        if ($personId !== -1) {
            $parameter->owner()->associate(Person::findOrFail($personId));
        } elseif ($connectionId !== -1) {
            $parameter->connectionType()->associate(SubConnectionType::findOrFail($connectionId));
        } elseif ($tariffId !== -1) {
            $tariff = MeterTariff::findOrFail($tariffId);
            $parameter->tariff()->associate($tariff);
            $accessRate = $tariff->accessRate()->first();
            $acP = $parameter->meter()->first()->accessRatePayment()->first();
            if ($acP) {
                $acP->access_rate_id = $accessRate->id;
                $acP->update();
            }
        } else {
            return;
        }
        $person=Person::find($parameter->owner_id);
        if ($person) {
            $person->update(
                [
                'updated_at' => date('Y-m-d h:i:s')]
            );
        }
        $parameter->save();
        return new ApiResource($parameter);
    }

    /**
     * List of connection types
     * A list of connection types and the meters which belong to the connection type
     *
     * @responseFile /responses/meterparameters/meterparameter.connectiontype.list.json
     * @param        Request $request
     * @return       ApiResource
     */
    public function connectionTypes(Request $request): ApiResource
    {
        return new ApiResource($this->connectionType->numberOfConnections());
    }
}
