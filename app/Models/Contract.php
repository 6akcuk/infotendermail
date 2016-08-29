<?php

namespace App\Models;

use Elasticsearch\ClientBuilder;
use App\Models\ContractSearchCriteria;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    //
    protected $fillable = [
        'organization_id',
        'system_id',
        'name',
        'link',
        'status',
        'type',
        'price',
        'finished_at',
        'results_at'
    ];

    protected $dates = ['finished_at', 'results_at', 'created_at', 'updated_at'];

    public function organization()
    {
        return $this->belongsTo('App\Models\Organization');
    }

    public static function elasticSearch(ContractSearchCriteria $options, $max_id = null) 
    {
        $criterias = $options ? json_decode($options->criterias, true) : [];

        $client = ClientBuilder::create()->build();

        $must = [];
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

            $must[] = [
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
                'must' => [
                    'bool' => [
                        'should' => $must
                    ]
                ],
                'should' => $should,
                'must_not' => $must_not
            ]
        ];

        if ($max_id) {
            $filtered['filter']['bool']['must'][] = [
                'range' => [
                    'id' => [
                        'gt' => $max_id
                    ]
                ]
            ];
        }
        //dd($filtered);

        $results = $client->search([
            'index' => 'tenders',
            'type' => 'contract',
            'body' => [
                'query' => [
                    'filtered' => $filtered
                ]
            ],
            'size' => 2000
        ]);

        //dd($results);

        $contract_ids = [];
        foreach ($results['hits']['hits'] as $contract) {
            $contract_ids[] = $contract['_id'];
        }

        return $contract_ids;
    }
}
