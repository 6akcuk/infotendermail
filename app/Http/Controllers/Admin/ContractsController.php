<?php

namespace App\Http\Controllers\Admin;

use App\Models\Contract;
use App\Models\ContractSearchCriteria;
use App\Models\Region;
use App\Models\UserSendedContract;
use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ContractsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $contracts = Contract::latest()->paginate(10);

        return view('admin.contracts.index', compact('contracts'));
    }

    public function setup()
    {
        $options = ContractSearchCriteria::where('user_id', Auth::user()->id)->first();
        $criterias = $options ? json_decode($options->criterias, true) : [];

        $regions = Region::orderBy('name')->lists('name', 'id');

        return view('admin.contracts.setup', compact('criterias', 'regions'));
    }

    public function save(Request $request)
    {
        $options = ContractSearchCriteria::where('user_id', Auth::user()->id)->first();
        $criterias = $options ? json_decode($options->criterias, true) : [];

        $criterias['regions'] = $request->regions;
        $criterias['match'] = $request->match;
        $criterias['exclude'] = $request->exclude;

        if (!$options) {
            $options = new ContractSearchCriteria();
            $options->user_id = Auth::user()->id;
        }
        $options->criterias = json_encode($criterias);
        $options->save();

        flash()->success('Изменения сохранены');
        return redirect()->route('admin.contracts.setup');
    }

    public function match()
    {
        $options = ContractSearchCriteria::where('user_id', Auth::user()->id)->first();
        if (!$options) {
            flash()->error('Настройте критерии поиска.');

            return redirect()->route('admin.contracts.index');
        }

        $criterias = $options ? json_decode($options->criterias, true) : [];

        $client = ClientBuilder::create()->build();

        $must_not = [];
        $should = [];

        $match = explode(',', $criterias['match']);
        $not = explode(',', $criterias['exclude']);

        foreach ($match as $m) {
            $should[] = [
                'match' => [
                    'name'  => [
                        'query' => $m,
                        'operator' => 'and'
                    ]
                ]
            ];
        }
        foreach ($not as $n) {
            $must_not[] = [
                'match' => [
                    'name' => [
                        'query' => $n,
                        'operator' => 'and'
                    ]
                ]
            ];
        }

        $results = $client->search([
            'index' => 'tenders',
            'type' => 'contract',
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => $should,
                        'must_not' => $must_not
                    ]
                ]
            ],
            'size' => 500
        ]);

        var_dump($results['hits']['hits']);
    }

    public function view(Request $request)
    {
        if (!$request->date)
            abort(500, 'Запрос неверный');

        $contract_ids = UserSendedContract::where('user_id', Auth::user()->id)
                ->whereBetween('created_at', [$request->date .' 00:00:00', $request->date .' 23:59:59'])
                ->lists('contract_id');

        $list = Contract::whereIn('id', $contract_ids)->with('organization')->get();

        return view('admin.contracts.view', compact('list'));
    }
}
