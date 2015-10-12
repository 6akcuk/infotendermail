<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\ContractSearchCriteria;
use App\Models\UserSendedContract;
use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mail';

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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $elastic = ClientBuilder::create()->build();

        $searches = ContractSearchCriteria::with('user')->all();
        foreach ($searches as $search) {
            $criteria = json_decode($search->criterias, true);

            $this->info('Обработка критерия для пользователя '. $search->user_id);

            if (is_array($criteria) && sizeof($criteria) > 0) {
                $max_id = (int) UserSendedContract::where('user_id', $search->user_id)->max('contract_id');

                $must_not = [];
                $should = [];

                $match = explode(',', $criteria['match']);
                $not = explode(',', $criteria['exclude']);

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

                $this->info('Поиск контрактов');

                $contracts = $elastic->search([
                    'index' => 'tenders',
                    'type' => 'contract',
                    'body' => [
                        'query' => [
                            'filtered' => [
                                'query' => [
                                    'bool' => [
                                        'should' => $should,
                                        'must_not' => $must_not
                                    ]
                                ],
                                'filter' => [
                                    'range' => [
                                        'id' => [
                                            'gt' => $max_id
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'size' => 500
                ]);

                $contract_ids = [];
                foreach ($contracts['hits']['hits'] as $contract) {
                    $contract_ids[] = $contract['_id'];

                    UserSendedContract::create([
                        'user_id' => $search->user_id,
                        'contract_id' => $contract['_id']
                    ]);
                }

                $list = Contract::whereIn('id', $contract_ids)->with('organization')->get();

                Mail::send('emails.contracts', ['list' => $list], function ($m) use ($list, $search, $contract_ids) {
                    $m->from('noreply@infotendermail.ru', 'infotendermail');
                    $m->to($search->user->email, $search->user->name)->subject('Контракты с infotendermail.ru #'. sizeof($contract_ids));
                });
            }
        }
    }
}
