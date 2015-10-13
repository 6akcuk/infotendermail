<?php

namespace App\Console\Commands;

use App\Models\Contract;
use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class BuildIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build:index {--reset : Reset index}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->elastic = ClientBuilder::create()->build();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $self = $this;

        if ($this->option('reset')) {
            if ($this->elastic->indices()->exists(['index' => 'tenders'])) {
                $this->elastic->indices()->delete(['index' => 'tenders']);
                $this->info('Удален индекс tenders');
            }

            $this->elastic->indices()->create([
                'index' => 'tenders'
            ]);
            $this->info('Создан индекс tenders');

            $this->elastic->indices()->putMapping([
                'index' => 'tenders',
                'type' => 'contract',
                'body' => [
                    'properties' => [
                        'region_id' => [
                            'type' => 'integer'
                        ],
                        'name' => [
                            'type' => 'string',
                            'fields' => [
                                'russian' => [
                                    'type' => 'string',
                                    'analyzer' => 'russian'
                                ]
                            ]
                        ],
                        'organization' => [
                            'type' => 'string',
                            'fields' => [
                                'russian' => [
                                    'type' => 'string',
                                    'analyzer' => 'russian'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            Contract::chunk(100, function($contracts) use ($self) {
                foreach ($contracts as $contract) {
                    $self->put($contract);
                }
            });
        } else {
            $result = $this->elastic->search([
                'index' => 'tenders',
                'type' => 'contract',
                'size' => 1,
                'body' => [
                    'query' => [
                        'match_all' => []
                    ],
                    'sort' => [
                        'id' => 'desc'
                    ]
                ]
            ]);

            $last_id = $result['hits']['hits'][0]['_id'];

            Contract::where('id', '>', $last_id)->chunk(100, function($contracts) use ($self) {
                foreach ($contracts as $contract) {
                    $self->put($contract);
                }
            });
        }
    }

    protected function put(Contract $contract)
    {
        $this->elastic->index([
            'index' => 'tenders',
            'type' => 'contract',
            'id' => $contract->id,
            'body' => [
                'id' => $contract->id,
                'name' => $contract->name,
                'organization' => $contract->organization->name,
                'region_id' => $contract->organization->region_id,
                'status' => $contract->status,
                'type' => $contract->type,
                'price' => $contract->price,
                'created_at' => $contract->created_at->format('Y-m-d H:i:s'),
                'finished_at' => $contract->finished_at ? $contract->finished_at->format('Y-m-d H:i') : null
            ]
        ]);
    }
}
