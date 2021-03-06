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

        $searches = ContractSearchCriteria::with('user')->get();
        foreach ($searches as $search) {
            $criteria = json_decode($search->criterias, true);

            $this->info('Обработка критерия для пользователя '. $search->user_id);

            if (is_array($criteria) && sizeof($criteria) > 0) {
                $max_id = (int) UserSendedContract::where('user_id', $search->user_id)->max('contract_id');

                $this->info('Поиск контрактов');

                $contract_ids = Contract::elasticSearch($search, $max_id);
                foreach ($contract_ids as $contract) {
                    UserSendedContract::create([
                        'user_id' => $search->user_id,
                        'contract_id' => $contract
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
