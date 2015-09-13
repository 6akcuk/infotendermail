<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\Country;
use App\Models\Organization;
use App\Models\Region;
use App\Models\Town;
use Carbon\Carbon;
use Elasticsearch\ClientBuilder;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;

class DownloadContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses contracts from sites.';

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
        $client = new Client([
            'base_uri' => 'http://zakupki.gov.ru/',
            'cookies' => true,
            'headers' => [
                'User-Agent' => 'testing/1.0'
            ]
        ]);

        Log::info('Начало загрузки контрактов: подключаемся к zakupki.gov.ru');

        $response = $client->get('epz/order/quicksearch/search.html?searchString=');

        $crawler = new Crawler($response->getBody()->getContents(), 'http://zakupki.gov.ru/');

        $this->zakupki($client, $crawler);

        $form = $crawler->selectButton('Обновить результаты поиска')->form();

        foreach (['FZ_44', 'FZ_223'] as $fz) {
            for ($p = 2; $p <= 10; $p++) {
                //$this->info($form['placeOfSearch']);

                $form->disableValidation();

                $form->setValues([
                        'placeOfSearch' => $fz,
                        'pageNo' => $p,
                        'recordsPerPage' => '_100',
                        'isPaging' => true
                ]);

                Log::info('Переходим на страницу '. $p);

                $nextPage = $client->get($form->getUri());

                $repeatSensor = $this->zakupki($client, new Crawler($nextPage->getBody()->getContents()));

                if ($repeatSensor >= 10) break;
            }
        }
    }

    protected function zakupki(Client $client, Crawler $crawler)
    {
        $repeatSensor = 0;
        $self = $this;
        //$elastic = ClientBuilder::create()->build();

        //$indices = $elastic->indices();
        /*if (!$indices->exists(['index' => 'tenders']))
            $elastic->indices()->create([
                'index' => 'tenders',
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0
                    ]
                ]
            ]);*/

        $crawler->filter('#exceedSphinxPageSizeDiv div.registerBox')->each(function(Crawler $node, $i) use ($client, &$repeatSensor, $self) {
            $systemId = str_replace('№ ', '', trim($node->filter('td.descriptTenderTd > dl > dt > a')->text()));

            $organizationNode = $node->filter('dd.nameOrganization > a');
            $organizationName = trim($organizationNode->text());
            $organizationUrl = trim($organizationNode->attr('href'));

            Log::info('Node data', [$node->html()]);

            $contractNode = $node->filter('td.descriptTenderTd > dl > dd')->eq(1)->filter('a');
            $contractName = trim($contractNode->text());
            $contractUrl = trim($contractNode->attr('href'));
            $contractType = trim($node->filter('td.tenderTd > dl > dt')->text());
            $contractStatus = ($node->filter('td.tenderTd > dl > dd')->count())
                                ? trim($node->filter('td.tenderTd > dl > dd')->text())
                                : '';

            Log::info('Обработка нового контракта', [
                'org_name' => $organizationName,
                'org_url' => $organizationUrl,
                'name' => $contractName,
                'url' => $contractUrl
            ]);

            $contract = Contract::where('system_id', $systemId)->first();
            if (!$contract) {
                $repeatSensor = 0;

                // Search organization in database
                $organization = Organization::where('url', $organizationUrl)->first();
                if (!$organization) {
                    Log::info('Организация не найдена, добавляем в базу.');

                    $organization = new Organization();
                    $organization->name = $organizationName;
                    $organization->url = $organizationUrl;

                    $self->info($organizationUrl);

                    $organizationResponse = $client->get($organizationUrl);

                    $organizationCrawler = new Crawler($organizationResponse->getBody()->getContents());

                    Log::info('Информация по организации загружена.');

                    // Federal Law 223
                    if ($node->filter('td.amountTenderTd > p > span.fz223')->count()) {
                        $organizationCrawler->filter('div.noticeTabBoxWrapper > table tr')->each(function(Crawler $row, $j) use (&$organization, $self) {
                            if ($row->children('td')->count() > 1) {
                                $nameColumn = trim($row->children('td')->eq(0)->text());
                                $valueColumn = trim($row->children('td')->eq(1)->text());

                                if ($valueColumn) {
                                    switch ($nameColumn) {
                                        case 'Уровень организации':
                                            $organization->level = $valueColumn;
                                            break;
                                        case 'ИНН':
                                            $organization->inn = $valueColumn;
                                            break;
                                        case 'КПП':
                                            $organization->kpp = $valueColumn;
                                            break;
                                        case 'ОГРН':
                                            $organization->ogrn = $valueColumn;
                                            break;
                                        case 'ОКАТО':
                                            $organization->okato = $valueColumn;
                                            break;
                                        case 'Адрес (место нахождения)':
                                            $address = array_map(function ($value) {
                                                return trim($value);
                                            }, explode(',', $valueColumn));

                                            if ($address[0] > 0) {
                                                array_unshift($address, 'Российская Федерация');
                                            }

                                            $self->info(implode(',', $address));

                                            $organization->postal_code = $address[1];

                                            $country = Country::where('name', $address[0])->first();
                                            if (!$country) {
                                                $country = Country::create([
                                                        'name' => $address[0]
                                                ]);
                                            }

                                            $organization->country_id = $country->id;

                                            if (isset($address[3])) {
                                                $region = Region::where('name', $address[2])->where('country_id', $country->id)->first();
                                                if (!$region) {
                                                    $region = Region::create([
                                                            'country_id' => $country->id,
                                                            'name' => $address[2]
                                                    ]);
                                                }

                                                $town = Town::where('name', $address[3])->where('region_id', $region->id)->first();
                                                if (!$town) {
                                                    $town = Town::create([
                                                            'region_id' => $region->id,
                                                            'name' => $address[3]
                                                    ]);
                                                }

                                                $organization->region_id = $region->id;
                                                $organization->town_id = $town->id;
                                            }

                                            $organization->address = $valueColumn;

                                            break;
                                        case 'Телефон':
                                            $organization->contact_phone = $valueColumn;
                                            break;
                                        case 'Факс':
                                            $organization->contact_fax = $valueColumn;
                                            break;
                                        case 'Почтовый адрес':
                                            $organization->contact_address = $valueColumn;
                                            break;
                                        case 'Контактное лицо':
                                            $organization->contact_name = $valueColumn;
                                            break;
                                        case 'Адрес электронной почты для системных уведомлений':
                                            $organization->contact_email = $valueColumn;
                                            break;
                                    }
                                }
                            }
                        });
                    }
                    // Federal Law 44
                    elseif ($node->filter('td.amountTenderTd > p > span.fz44')->count()) {
                        $organizationCrawler->filter('td.icePnlGrdCol > table tr')->each(function(Crawler $row, $j) use (&$organization, $self) {
                            if ($row->children('td')->count() > 1) {
                                $nameColumn = trim($row->children('td')->eq(0)->text());
                                $valueColumn = trim($row->children('td')->eq(1)->text());

                                if ($valueColumn) {
                                    switch ($nameColumn) {
                                        case 'Уровень организации':
                                            $organization->level = $valueColumn;
                                            break;
                                        case 'ИНН':
                                            $organization->inn = $valueColumn;
                                            break;
                                        case 'КПП':
                                            $organization->kpp = $valueColumn;
                                            break;
                                        case 'ОГРН':
                                            $organization->ogrn = $valueColumn;
                                            break;
                                        case 'ОКАТО':
                                            $organization->okato = $valueColumn;
                                            break;
                                        case 'Место нахождения':
                                            $address = array_map(function ($value) {
                                                return trim($value);
                                            }, explode(',', $valueColumn));

                                            $self->info($valueColumn);

                                            $organization->postal_code = $address[1];

                                            $country = Country::where('name', $address[0])->first();
                                            if (!$country) {
                                                $country = Country::create([
                                                        'name' => $address[0]
                                                ]);
                                            }

                                            $region = Region::where('name', $address[2])->where('country_id', $country->id)->first();
                                            if (!$region) {
                                                $region = Region::create([
                                                        'country_id' => $country->id,
                                                        'name' => $address[2]
                                                ]);
                                            }

                                            $town = Town::where('name', $address[3])->where('region_id', $region->id)->first();
                                            if (!$town) {
                                                $town = Town::create([
                                                        'region_id' => $region->id,
                                                        'name' => $address[3]
                                                ]);
                                            }

                                            $organization->country_id = $country->id;
                                            $organization->region_id = $region->id;
                                            $organization->town_id = $town->id;
                                            $organization->address = $valueColumn;

                                            break;
                                        case 'Телефон':
                                            $organization->contact_phone = $valueColumn;
                                            break;
                                        case 'Факс':
                                            $organization->contact_fax = $valueColumn;
                                            break;
                                        case 'Почтовый адрес':
                                            $organization->contact_address = $valueColumn;
                                            break;
                                        case 'Контактное лицо':
                                            $organization->contact_name = $valueColumn;
                                            break;
                                        case 'Контактный адрес электронной почты':
                                            $organization->contact_email = $valueColumn;
                                            break;
                                    }
                                }
                            }
                        });
                    }

                    $organization->save();

                    Log::info('Организация добавлена в базу.');

                    $self->info('Организация '. $organizationName);
                }
                else {
                    Log::info('Организация найдена в базе.');
                }

                Log::info('Переходим на страницу контракта.');

                $contractResponse = $client->get($contractUrl);
                $contractCrawler = new Crawler($contractResponse->getBody()->getContents());

                Log::info('Страница контракта загружена.');

                $contract = new Contract();
                $contract->organization_id = $organization->id;
                $contract->system_id = $systemId;
                $contract->name = $contractName;
                $contract->link = $contractUrl;
                $contract->status = $contractStatus;
                $contract->type = $contractType;

                $price = str_replace(
                    ',', '.', preg_replace(
                        "/([^0-9\.\,]*)/", '', trim($node->filter('td.amountTenderTd > dl > dt')->text())
                    )
                );

                $contract->price = $price;

                // Federal Law 223
                if ($node->filter('td.amountTenderTd > p > span.fz223')->count()) {
                    $contractCrawler->filter('div.noticeTabBoxWrapper > table tr')->each(function (Crawler $row, $j) use (&$contract) {
                        if ($row->filter('td')->count() > 1) {
                            $nameColumn = trim($row->filter('td')->eq(0)->text());
                            $valueColumn = trim($row->filter('td')->eq(1)->text());

                            if ($valueColumn) {
                                if (stristr('подачи заявок', $nameColumn)) {
                                    preg_match("/(\d{2}[\.]{1}\d{2}[\.]{1}\d{4}[.*]{1}[в]{1}[.*]{1}\d{2}:\d{2})/ui", $valueColumn, $date);

                                    $this->info($valueColumn);
                                    $this->info(var_export($date, true));
                                    exit;

                                    $valueColumn = str_replace('в', '', $date[1]);

                                    $finishDate = new Carbon($valueColumn);
                                    $contract->finished_at = $finishDate;
                                } elseif (stristr('подведения итогов', $nameColumn)) {
                                    preg_match("/(\d{2}[\.]{1}\d{2}[\.]{1}\d{4}[ ]{1}[в]{1}[ ]{1}\d{2}:\d{2})/ui", $valueColumn, $date);

                                    $valueColumn = str_replace('в', '', $date[1]);

                                    $resultDate = new Carbon($valueColumn);
                                    $contract->results_at = $resultDate;
                                }
                            }
                        }
                    });
                }
                // Federal Law 44
                elseif ($node->filter('td.amountTenderTd > p > span.fz44')->count()) {
                    $contractCrawler->filter('div.noticeTabBoxWrapper > table tr')->each(function (Crawler $row, $j) use (&$contract) {
                        if ($row->filter('td')->count() > 1) {
                            $nameColumn = trim($row->filter('td')->eq(0)->text());
                            $valueColumn = trim($row->filter('td')->eq(1)->text());

                            if ($valueColumn) {
                                if (
                                    stristr('Дата и время окончания подачи заявок', $nameColumn) ||
                                    stristr('Дата и время окончания подачи котировочных заявок', $nameColumn)
                                ) {
                                    $valueColumn = str_replace('в', '', $valueColumn);

                                    $finishDate = new Carbon($valueColumn);
                                    $contract->finished_at = $finishDate;
                                } elseif (
                                    stristr('Дата проведения аукциона в электронной форме', $nameColumn) ||
                                    stristr('Дата и время вскрытия конвертов с заявками ', $nameColumn)
                                ) {
                                    // Не ставим точную дату, так как скрипт проверять будет на следующий день
                                    $valueColumn = str_replace('в', '', $valueColumn);

                                    $resultDate = new Carbon($valueColumn);
                                    $contract->results_at = $resultDate;
                                }
                            }
                        }
                    });
                }

                $contract->save();

                $self->info('Контракт '. $systemId .' '. $contractName);

                Log::info('Контракт сохранен в базу.');
            } else {
                $repeatSensor++;

                Log::info('Контракт найден в базе.');
            }
        });

        return $repeatSensor;
    }
}
