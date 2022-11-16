<?php

namespace ZarulIzham\EMandate;

use Exception;
use ZarulIzham\EMandate\Models\Bank;
use ZarulIzham\EMandate\Messages\BankEnquiry;

class EMandate
{
    public static function getBankList(bool $getLatest = false)
    {
        if ($getLatest) {
            try {
                $bankEnquiry = new BankEnquiry;
                $dataList = $bankEnquiry->getData();
                $response = $bankEnquiry->connect($dataList);
                $token = strtok($response, "&");
                $bankList = $bankEnquiry->parseBanksList($token);

                if ($bankList === false) {
                    throw new Exception('We could not find any data');
                }

                foreach ($bankList as $key => $status) {
                    $bankId = explode(" - ", $key)[1];
                    $bank = $bankEnquiry->getBanks($bankId);

                    if (empty($bank)) {
                        logger("Bank Not Found: ", [$bankId]);
                        continue;
                    }

                    Bank::updateOrCreate(['bank_id' => $bankId], [
                        'status' => $status == 'A' ? 'Online' : 'Offline',
                        'name' => $bank['name'],
                        'short_name' => $bank['short_name'],
                        'type' => $bank['type'] ?? [],
                    ]);
                }
            } catch (Exception $e) {
                throw $e;
            }
        }
        return Bank::select('name', 'bank_id', 'short_name', 'status')->orderBy('short_name', 'ASC')->get()->toArray();
    }
}
