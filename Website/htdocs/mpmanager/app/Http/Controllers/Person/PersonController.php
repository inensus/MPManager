<?php

namespace App\Http\Controllers\Person;

use App;
use App\Http\Services\PersonService;
use App\Models\Person\Person;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Class PersonController
 * @package App\Http\Controllers\Person
 *

 */
class PersonController extends Controller
{

    public function index(): View
    {
        return view('layouts.people.list', [
            'title' => 'registered People',
            'people' => Person::with('addresses', 'addresses.city')->get(),
        ]);
    }


    public function show($id)
    {
        $personService = App::make(PersonService::class);
        $person = $personService->getDetails($id, true);

        $lastTransactions = $person->transactions()->latest()->take(10)->get();
        $difference = 'no data available';
        $lastTransactionDate = null;
        if (count($lastTransactions)) {
            $lastTransactionDate = $newest = $lastTransactions[0]->created_at;
            $newest = new Carbon($newest);
            $lastTransactionDate = $newest->diffInDays(Carbon::now()) . ' days ago';
            $eldest = new Carbon($lastTransactions[count($lastTransactions) - 1]->created_at);
            $difference = $eldest->diffInDays($newest) . ' days';
        }


        return view('layouts.people.detail', compact('person', 'lastTransactionDate', 'difference'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
