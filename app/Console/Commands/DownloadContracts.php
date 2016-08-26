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
                'User-Agent' => 'Mozilla'
            ]
        ]);

        Log::info('Connecting..');

        $response = $this->makeRequest($client, 1);
        $crawler = new Crawler($response->getBody()->getContents(), 'http://zakupki.gov.ru/');

        $this->parseTenders($client, $crawler);

        for ($p = 2; $p <= 40; $p++) {
            $response = $this->makeRequest($client, $p);
            Log::info('Going to page'. $p);

            $repeatSensor = $this->parseTenders($client, new Crawler($response->getBody()->getContents()));

            if ($repeatSensor >= 10) break;
        }
    }

    protected function makeRequest(Client $client, $pageNumber = 1)
    {
        return $client->request('GET', 'epz/order/quicksearch/search.html', [
            'query' => [
                'recordsPerPage' => '_50',
                'fz44' => 'on',
                'fz223' => 'on',
                'af' => 'on',
                'ca' => 'on',
                'pageNumber' => $pageNumber,
                'sortBy' => 'PUBLISH_DATE'
            ]
        ]);
    }

    protected function parseTenders(Client $client, Crawler $crawler)
    {
        $repeatSensor = 0;

        $crawler->filter('div.registerBox')->each(function(Crawler $node, $i) use ($client, &$repeatSensor) {
            $systemId = str_replace('№ ', '', trim($node->filter('td.descriptTenderTd > dl > dt > a')->text()));

            $organizationNode = $node->filter('dd.nameOrganization > a');
            $organizationName = trim($organizationNode->text());
            $organizationUrl = trim($organizationNode->attr('href'));

            //Log::info('Node data', [$node->html()]);

            $contractName = trim($node->filter('td.descriptTenderTd > dl > dd')->eq(1)->text());
            $contractUrl = $node->filter('td.descriptTenderTd > dl > dt > a')->attr('href');
            $contractType = trim($node->filter('td.tenderTd > dl > dt')->text());
            $contractStatus = ($node->filter('td.tenderTd > dl > dd')->count())
                                ? trim($node->filter('td.tenderTd > dl > dd')->text())
                                : '';

            /*Log::info('Обработка нового контракта', [
                'org_name' => $organizationName,
                'org_url' => $organizationUrl,
                'name' => $contractName,
                'url' => $contractUrl
            ]);*/

            $contract = Contract::where('system_id', $systemId)->first();
            if (!$contract) {
                $repeatSensor = 0;

                // Search organization in database
                $organization = Organization::where('url', $organizationUrl)->first();
                if (!$organization) {
                    //Log::info('Организация не найдена, добавляем в базу.');

                    $organization = new Organization();
                    $organization->name = $organizationName;
                    $organization->url = $organizationUrl;

                    $this->info($organizationUrl);

                    $organizationResponse = $client->get($organizationUrl);

                    $organizationCrawler = new Crawler($organizationResponse->getBody()->getContents());

                    //Log::info('Информация по организации загружена.');

                    // Federal Law 223
                    if (preg_match("/223\/ppa/", $organizationUrl)) {
                        $organizationCrawler->filter('div.noticeTabBoxWrapper > table tr')->each(function(Crawler $row, $j) use (&$organization) {
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
                                            $addresses = array_map(function ($value) {
                                                return trim($value);
                                            }, explode(',', $valueColumn));

                                            $address = collect($addresses);
                                            $address->forget('Российская Федерация');

                                            $organization->postal_code = $address[0];
                                            $organization->country_id = 1;

                                            $country = Country::find(1);

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
                    else {
                        $organizationCrawler->filter('td.icePnlTbSetCnt table tr')->each(function(Crawler $row, $j) use ($organization) {
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

                                            $this->info($valueColumn);

                                            $organization->postal_code = $address[1];
                                            $country = Country::where('name', 'Российская Федерация')->first();
                                            
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

                    //dd($organization);

                    $organization->save();

                    //Log::info('Организация добавлена в базу.');

                    $this->info('Organization '. $organizationName);
                }
                else {
                    //Log::info('Организация найдена в базе.');
                }

                //Log::info('Переходим на страницу контракта.');

                $contractResponse = $client->get($contractUrl);
                $contractCrawler = new Crawler($contractResponse->getBody()->getContents());

                //Log::info('Страница контракта загружена.');

                $contract = new Contract();
                $contract->organization_id = $organization->id;
                $contract->system_id = $systemId;
                $contract->name = $contractName;
                $contract->link = $contractUrl;
                $contract->status = $contractStatus;
                $contract->type = $contractType;

                $price = str_replace(
                    ',', '.', preg_replace(
                        "/([^0-9\.\,]*)/", '', trim($node->filter('td.tenderTd > dd')->eq(1)->text())
                    )
                );

                $contract->price = $price;

                // Federal Law 223
                $contractCrawler->filter('div.noticeTabBoxWrapper > table tr')->each(function (Crawler $row, $j) use ($contract, $contractUrl) {
                    if ($row->filter('td')->count() > 1) {
                        $nameColumn = trim($row->filter('td')->eq(0)->text());
                        $valueColumn = trim($row->filter('td')->eq(1)->text());

                        if (!$valueColumn) return;

                        if (preg_match("/223\/purchase/", $contractUrl)) {
                            if (preg_match('/подачи заявок/i', $nameColumn)) {
                                preg_match("/(\d{2}\.\d{2}\.\d{4}\sв\s\d{2}:\d{2})/ui", $valueColumn, $date);

                                if (!isset($date[1])) return; 

                                $valueColumn = str_replace('в', '', $date[1]);

                                $finishDate = new Carbon($valueColumn);
                                $contract->finished_at = $finishDate;
                            } elseif (preg_match('/подведения итогов/i', $nameColumn)) {
                                preg_match("/(\d{2}[\.]{1}\d{2}[\.]{1}\d{4}[ ]{1}[в]{1}[ ]{1}\d{2}:\d{2})/ui", $valueColumn, $date);

                                if (!isset($date[1])) return;

                                $valueColumn = str_replace('в', '', $date[1]);

                                $resultDate = new Carbon($valueColumn);
                                $contract->results_at = $resultDate;
                            }
                        }
                        else {
                            if (
                                preg_match('/Дата и время окончания подачи заявок/i', $nameColumn) ||
                                preg_match('/Дата и время окончания подачи котировочных заявок/i', $nameColumn)
                            ) {
                                $valueColumn = str_replace('в', '', $valueColumn);

                                $finishDate = new Carbon($valueColumn);
                                $contract->finished_at = $finishDate;
                            } elseif (
                                preg_match('/Дата проведения аукциона в электронной форме/i', $nameColumn) ||
                                preg_match('/Дата и время вскрытия конвертов с заявками/i', $nameColumn)
                            ) {
                                // Не ставим точную дату, так как скрипт проверять будет на следующий день
                                $valueColumn = str_replace('в', '', $valueColumn);

                                $resultDate = new Carbon($valueColumn);
                                $contract->results_at = $resultDate;
                            }
                        }
                    }
                });

                $contract->save();

                $this->info('Контракт '. $systemId .' '. $contractName);

                Log::info('Контракт сохранен в базу.');
            } else {
                $repeatSensor++;

                Log::info('Контракт найден в базе.');
            }

            usleep(rand(200, 2000) * 1000); // sleep for random time
        });

        return $repeatSensor;
    }
}
