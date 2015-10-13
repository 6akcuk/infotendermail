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
        $criterias['match_org'] = $request->match_org;
        $criterias['exclude_org'] = $request->exclude_org;

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
        $must_not_org = [];
        $should_org = [];

        $match = explode(',', $criterias['match']);
        $not = explode(',', $criterias['exclude']);
        $match_org = explode(',', $criterias['match_org']);
        $not_org = explode(',', $criterias['exclude_org']);

        foreach ($match as $m) {
            if (!$m) continue;

            $should[] = [
                'match' => [
                    'name.russian' => [
                        'query' => $m,
                        'operator' => 'and'
                    ]
                ]
            ];
        }
        foreach ($not as $n) {
            if (!$n) continue;

            $must_not[] = [
                'match' => [
                    'name.russian' => [
                        'query' => $n,
                        'operator' => 'and'
                    ]
                ]
            ];
        }

        foreach ($match_org as $m) {
            if (!$m) continue;

            $should[] = [
                'match' => [
                    'organization.russian' => [
                        'query' => $m,
                        'operator' => 'and'
                    ]
                ]
            ];
        }
        foreach ($not_org as $n) {
            if (!$n) continue;

            $must_not[] = [
                'match' => [
                    'organization.russian' => [
                        'query' => $n,
                        'operator' => 'and'
                    ]
                ]
            ];
        }

        if (is_array($criterias['regions']) && sizeof($criterias['regions']) > 0) {
            $filtered['filter'] = [
                    'terms' => [
                            'region_id' => $criterias['regions']
                    ]
            ];
        }

        $filtered['query'] = [
            'bool' => [
                'should' => $should,
                'must_not' => $must_not,
            ]
        ];

        $results = $client->search([
            'index' => 'tenders',
            'type' => 'contract',
            'body' => [
                'query' => [
                    'filtered' => $filtered
                ]
            ],
            'size' => 500
        ]);

        $contract_ids = [];
        foreach ($results['hits']['hits'] as $contract) {
            $contract_ids[] = $contract['_id'];
        }

        $list = Contract::whereIn('id', $contract_ids)
                ->whereRaw('(finished_at > NOW() OR finished_at IS NULL)')
                ->with('organization')->get();

        return view('admin.contracts.view', compact('list'));
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
