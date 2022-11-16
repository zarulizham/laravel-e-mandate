<?php

namespace ZarulIzham\EMandate\Messages;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use ZarulIzham\EMandate\Contracts\Message as Contract;

class BankEnquiry extends Message implements Contract
{

    /**
     * Message code on the FPX side
     */
    public const CODE = 'BE';

    /**
     * Message Url
     */
    public $url;

    public function __construct()
    {
        parent::__construct();

        $this->type = self::CODE;
        $this->url = App::environment('production') ?
            Config::get('fpx.urls.production.bank_enquiry') :
            Config::get('fpx.urls.uat.bank_enquiry');
    }

    /**
     * handle a message
     *
     * @param array $options
     * @return mixed
     */
    public function handle(array $options)
    {
        # code...
    }

    /**
     * get request data from
     *
     */
    public function getData()
    {
        return collect([
            'fpx_msgType' => urlencode($this->type),
            'fpx_msgToken' => urlencode($this->flow),
            'fpx_sellerExId' => urlencode($this->exchangeId),
            'fpx_version' => urlencode($this->version),
            'fpx_checkSum' => $this->getCheckSum($this->format()),
        ]);
    }

    /**
     * connect and excute the request to FPX server
     *
     */
    public function connect(Collection $dataList)
    {
        $client = new Client();
        $response = $client->request('POST', $this->url, [
            'form_params' => $dataList->toArray()
        ]);

        return Str::replaceArray("\n", [''], $response->getBody());
    }

    /**
     * Parse the bank list response
     *
     */
    public function parseBanksList($response)
    {
        if ($response == 'ERROR' || !$response) {
            return false;
        }

        while ($response !== false) {
            list($key1, $value1) = explode("=", $response);
            $value1 = urldecode($value1);
            $response_value[$key1] = $value1;
            $response = strtok("&");
        }


        $data = $response_value['fpx_bankList'] . "|" .
            $response_value['fpx_msgToken'] . "|" .
            $response_value['fpx_msgType']  . "|" .
            $response_value['fpx_sellerExId'];

        $checksum = $response_value['fpx_checkSum'];

        if (Config::get('fpx.should_verify_response')) {
            $this->verifySign($checksum, $data);
        }

        $bankListToken = strtok($response_value['fpx_bankList'], ",");

        $i = 1;
        while ($bankListToken !== false) {
            list($key1, $value1) = explode("~", $bankListToken);
            $value1 = urldecode($value1);
            $bankList[$i  . ' - ' . $key1] = $value1;
            $i++;
            $bankListToken = strtok(",");
        }

        return $bankList;
    }


    /**
     * Banks List
     */
    public function getBanks($id = null)
    {
        $banks = [
            [
                "bank_id" => "ABB0234",
                "status" => "offline",
                "name" => "Affin Bank Berhad",
                "short_name" => "Affin Bank Berhad",
                "type" => ["B2C"],
            ], [
                "bank_id" => "ABMB0212",
                "status" => "offline",
                "name" => "Alliance Bank Malaysia Berhad",
                "short_name" => "Alliance Bank (Personal)",
                "type" => ["B2C"],
            ], [
                "bank_id" => "AGRO01",
                "status" => "offline",
                "name" => "BANK PERTANIAN MALAYSIA BERHAD (AGROBANK)",
                "short_name" => "AGRONet",
                "type" => ["B2C"],
            ], [
                "bank_id" => "AMBB0209",
                "status" => "offline",
                "name" => "AmBank Malaysia Berhad",
                "short_name" => "AmBank",
                "type" => ["B2C"],
            ], [
                "bank_id" => "BIMB0340",
                "status" => "offline",
                "name" => "Bank Islam Malaysia Berhad",
                "short_name" => "Bank Islam",
                "type" => ["B2C", "B2B"],
            ], [
                "bank_id" => "BMMB0341",
                "status" => "offline",
                "name" => "Bank Muamalat Malaysia Berhad",
                "short_name" => "Bank Muamalat",
                "type" => ["B2C"],
            ], [
                "bank_id" => "BKRM0602",
                "status" => "offline",
                "name" => "Bank Kerjasama Rakyat Malaysia Berhad ",
                "short_name" => "Bank Rakyat",
                "type" => ["B2C", "B2B"],
            ], [
                "bank_id" => "BSN0601",
                "status" => "offline",
                "name" => "Bank Simpanan Nasional",
                "short_name" => "BSN",
                "type" => ["B2C"],
            ], [
                "bank_id" => "BCBB0235",
                "status" => "offline",
                "name" => "CIMB Bank Berhad",
                "short_name" => "CIMB Clicks",
                "type" => ["B2C", "B2B"],
            ], [
                "bank_id" => "HLB0224",
                "status" => "offline",
                "name" => "Hong Leong Bank Berhad",
                "short_name" => "Hong Leong Bank",
                "type" => ["B2C", "B2B"],
            ], [
                "bank_id" => "HSBC0223",
                "status" => "offline",
                "name" => "HSBC Bank Malaysia Berhad",
                "short_name" => "HSBC Bank",
                "type" => ["B2C", "B2B"],
            ], [
                "bank_id" => "KFH0346",
                "status" => "offline",
                "name" => "Kuwait Finance House (Malaysia) Berhad",
                "short_name" => "KFH",
                "type" => ["B2C", "B2B"],
            ], [
                "bank_id" => "MBB0228",
                "status" => "offline",
                "name" => "Malayan Banking Berhad (M2E)",
                "short_name" => "Maybank2E",
                "type" => ["B2C", "B2B"],
            ], [
                "bank_id" => "MB2U0227",
                "status" => "offline",
                "name" => "Malayan Banking Berhad (M2U)",
                "short_name" => "Maybank2U",
                "type" => ["B2C"],
            ], [
                "bank_id" => "OCBC0229",
                "status" => "offline",
                "name" => "OCBC Bank Malaysia Berhad",
                "short_name" => "OCBC Bank",
                "type" => ["B2C", "B2B"],
            ], [
                "bank_id" => "PBB0233",
                "status" => "offline",
                "name" => "Public Bank Berhad",
                "short_name" => "Public Bank",
                "type" => ["B2C", "B2B"],
            ], [
                "bank_id" => "RHB0218",
                "status" => "offline",
                "name" => "RHB Bank Berhad",
                "short_name" => "RHB Bank",
                "type" => ["B2C", "B2B"],
            ], [
                "bank_id" => "SCB0216",
                "status" => "offline",
                "name" => "Standard Chartered Bank",
                "short_name" => "Standard Chartered",
                "type" => ["B2C"],
            ], [
                "bank_id" => "UOB0229",
                "status" => "offline",
                "name" => "United Overseas Bank - B2C Test",
                "short_name" => "UOB Bank",
                "type" => ["B2C"],
            ], [
                "bank_id" => "ABB0235",
                "status" => "offline",
                "name" => "Affin Bank Berhad",
                "short_name" => "AFFINMAX",
                "type" => ["B2B"],
            ], [
                "bank_id" => "ABMB0213",
                "status" => "offline",
                "name" => "Alliance Bank Malaysia Berhad",
                "short_name" => "Alliance Bank (Business)",
                "type" => ["B2B"],
            ], [
                "bank_id" => "AGRO02",
                "status" => "offline",
                "name" => "BANK PERTANIAN MALAYSIA BERHAD (AGROBANK)",
                "short_name" => "AGRONetBIZ",
                "type" => ["B2B"],
            ], [
                "bank_id" => "AMBB0208",
                "status" => "offline",
                "name" => "AmBank Malaysia Berhad",
                "short_name" => "AmBank",
                "type" => ["B2B"],
            ], [
                "bank_id" => "BMMB0342",
                "status" => "offline",
                "name" => "Bank Muamalat Malaysia Berhad",
                "short_name" => "Bank Muamalat",
                "type" => ["B2B"],
            ], [
                "bank_id" => "BNP003",
                "status" => "offline",
                "name" => "BNP Paribas Malaysian Berhad",
                "short_name" => "BNP Paribas",
                "type" => ["B2B"],
            ], [
                "bank_id" => "CIT0218",
                "status" => "offline",
                "name" => "CITI Bank Berhad",
                "short_name" => "Citibank Corporate Banking",
                "type" => ["B2B"],
            ], [
                "bank_id" => "DBB0199",
                "status" => "offline",
                "name" => "Deutsche Bank Berhad",
                "short_name" => "Deutsche Bank",
                "type" => ["B2B"],
            ], [
                "bank_id" => "PBB0234",
                "status" => "offline",
                "name" => "Public Bank Enterprise",
                "short_name" => "Public Bank PB enterprise",
                "type" => ["B2B"],
            ], [
                "bank_id" => "SCB0215",
                "status" => "offline",
                "name" => "Standard Chartered Bank",
                "short_name" => "Standard Chartered",
                "type" => ["B2B"],
            ], [
                "bank_id" => "UOB0228",
                "status" => "offline",
                "name" => "United Overseas Bank - B2B Regional",
                "short_name" => "UOB Regional",
                "type" => ["B2B"],
            ], [
                "bank_id" => "BOCM01",
                "status" => "offline",
                "name" => "Bank Of China (M) Berhad",
                "short_name" => "Bank Of China",
                "type" => ["B2C"],
            ],  [
                "bank_id" => "ABB0233",
                "status" => "offline",
                "name" => "Affin Bank Berhad",
                "short_name" => "Affin Bank",
                "type" => ["B2C"],
            ],  [
                "bank_id" => "UOB0226",
                "status" => "offline",
                "name" => "United Overseas Bank",
                "short_name" => "UOB Bank",
                "type" => ["B2C"],
            ],
       ];

        $banks = collect($banks)->merge($this->getTestingBanks());

        if (is_null($id)) {
            return $banks;
        }

        return $banks->firstWhere('bank_id', $id);
    }

    public function getTestingBanks()
    {
        if (App::environment('production')) {
            return [];
        }

        return [
            [
                "bank_id" => "LOAD001",
                "status" => "offline",
                "name" => "LOAD001",
                "short_name" => "LOAD001",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "ABB0234",
                "status" => "offline",
                "name" => "Affin Bank Berhad B2C - Test ID",
                "short_name" => "Affin B2C - Test ID",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "ABB0233",
                "status" => "offline",
                "name" => "Affin Bank Berhad",
                "short_name" => "Affin Bank",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "ABMB0212",
                "status" => "offline",
                "name" => "Alliance Bank Malaysia Berhad",
                "short_name" => "Alliance Bank (Personal)",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "AGRO01",
                "status" => "offline",
                "name" => "BANK PERTANIAN MALAYSIA BERHAD (AGROBANK)",
                "short_name" => "AGRONet",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "AMBB0209",
                "status" => "offline",
                "name" => "AmBank Malaysia Berhad",
                "short_name" => "AmBank",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "BIMB0340",
                "status" => "offline",
                "name" => "Bank Islam Malaysia Berhad",
                "short_name" => "Bank Islam",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "BMMB0341",
                "status" => "offline",
                "name" => "Bank Muamalat Malaysia Berhad",
                "short_name" => "Bank Muamalat ",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "BKRM0602",
                "status" => "offline",
                "name" => "Bank Kerjasama Rakyat Malaysia Berhad ",
                "short_name" => "Bank Rakyat",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "BOCM01",
                "status" => "offline",
                "name" => "Bank Of China (M) Berhad",
                "short_name" => "Bank Of China",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "BSN0601",
                "status" => "offline",
                "name" => "Bank Simpanan Nasional",
                "short_name" => "BSN",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "BCBB0235",
                "status" => "offline",
                "name" => "CIMB Bank Berhad",
                "short_name" => "CIMB Clicks",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "CIT0219",
                "status" => "offline",
                "name" => "CITI Bank Berhad",
                "short_name" => "Citibank",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "HLB0224",
                "status" => "offline",
                "name" => "Hong Leong Bank Berhad",
                "short_name" => "Hong Leong Bank",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "HSBC0223",
                "status" => "offline",
                "name" => "HSBC Bank Malaysia Berhad",
                "short_name" => "HSBC Bank",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "KFH0346",
                "status" => "offline",
                "name" => "Kuwait Finance House (Malaysia) Berhad",
                "short_name" => "KFH",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "MBB0228",
                "status" => "offline",
                "name" => "Malayan Banking Berhad (M2E)",
                "short_name" => "Maybank2E",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "MB2U0227",
                "status" => "offline",
                "name" => "Malayan Banking Berhad (M2U)",
                "short_name" => "Maybank2U",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "OCBC0229",
                "status" => "offline",
                "name" => "OCBC Bank Malaysia Berhad",
                "short_name" => "OCBC Bank",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "PBB0233",
                "status" => "offline",
                "name" => "Public Bank Berhad",
                "short_name" => "Public Bank",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "RHB0218",
                "status" => "offline",
                "name" => "RHB Bank Berhad",
                "short_name" => "RHB Bank",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "TEST0021",
                "status" => "offline",
                "name" => "SBI Bank A",
                "short_name" => "SBI Bank A",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "TEST0022",
                "status" => "offline",
                "name" => "SBI Bank B",
                "short_name" => "SBI Bank B",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "TEST0023",
                "status" => "offline",
                "name" => "SBI Bank C",
                "short_name" => "SBI Bank C",
                "type" => ["B2C", "B2B"],
            ],
            [
                "bank_id" => "SCB0216",
                "status" => "offline",
                "name" => "Standard Chartered Bank",
                "short_name" => "Standard Chartered",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "UOB0226",
                "status" => "offline",
                "name" => "United Overseas Bank",
                "short_name" => "UOB Bank",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "UOB0229",
                "status" => "offline",
                "name" => "United Overseas Bank - B2C Test",
                "short_name" => "UOB Bank - Test ID",
                "type" => ["B2C"],
            ],
            [
                "bank_id" => "ABB0232",
                "status" => "offline",
                "name" => "Affin Bank Berhad",
                "short_name" => "Affin Bank Berhad",
                "type" => ["B2B"],
            ],
            [
                "bank_id" => "ABB0235",
                "status" => "offline",
                "name" => "Affin Bank Berhad B2B",
                "short_name" => "AFFINMAX",
                "type" => ["B2B"],
            ],
            [
                "bank_id" => "ABMB0213",
                "status" => "offline",
                "name" => "Alliance Bank Malaysia Berhad",
                "short_name" => "Alliance Bank (Business)",
                "type" => ["B2B"],
            ],
            [
                "bank_id" => "AGRO02",
                "status" => "offline",
                "name" => "BANK PERTANIAN MALAYSIA BERHAD (AGROBANK)",
                "short_name" => "AGRONetBIZ",
                "type" => ["B2B"],
            ],
            [
                "bank_id" => "AMBB0208",
                "status" => "offline",
                "name" => "AmBank Malaysia Berhad",
                "short_name" => "AmBank",
                "type" => ["B2B"],
            ],
            [
                "bank_id" => "BMMB0342",
                "status" => "offline",
                "name" => "Bank Muamalat Malaysia Berhad",
                "short_name" => "Bank Muamalat ",
                "type" => ["B2B"],
            ],
            [
                "bank_id" => "BNP003",
                "status" => "offline",
                "name" => "BNP Paribas Malaysian Berhad",
                "short_name" => "BNP Paribas",
                "type" => ["B2B"],
            ],
            [
                "bank_id" => "CIT0218",
                "status" => "offline",
                "name" => "CITI Bank Berhad",
                "short_name" => "Citibank Corporate Banking",
                "type" => ["B2B"],
            ],
            [
                "bank_id" => "DBB0199",
                "status" => "offline",
                "name" => "Deutsche Bank Berhad",
                "short_name" => "Deutsche Bank",
                "type" => ["B2B"],
            ],
            // [
            // 	"bank_id" => "BKRM0602",
            // 	"status" => "offline",
            // 	"name" => "Bank Kerjasama Rakyat Malaysia Berhad",
            // 	"short_name" => "i-bizRAKYAT",
            // 	"type" => ["B2B"],
            // ],
            [
                "bank_id" => "PBB0234",
                "status" => "offline",
                "name" => "Public Bank Enterprise",
                "short_name" => "Public Bank PB enterprise",
                "type" => ["B2B"],
            ],
            [
                "bank_id" => "SCB0215",
                "status" => "offline",
                "name" => "Standard Chartered Bank",
                "short_name" => "Standard Chartered",
                "type" => ["B2B"],
            ],
            [
                "bank_id" => "UOB0228",
                "status" => "offline",
                "name" => "United Overseas Bank - B2B Regional",
                "short_name" => "UOB Regional",
                "type" => ["B2B"],
            ],
        ];
    }

    /**
     * Format data for checksum
     * @return string
     */
    public function format()
    {
        $list = collect([
            $this->flow ?? '',
            $this->type ?? '',
            $this->exchangeId ?? '',
            $this->version ?? '',
        ]);

        return $list->join('|');
    }
}
