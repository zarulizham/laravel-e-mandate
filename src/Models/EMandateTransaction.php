<?php

namespace ZarulIzham\EMandate\Models;

use ZarulIzham\EMandate\Models\Bank;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EMandateTransaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public function getTable()
    {
        return config('e-mandate.transaction_table_name');
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'request_payload' => 'object',
        'response_payload' => 'object',
    ];

    public function getAttribute($key)
    {
        [$key, $path] = preg_split('/(->|\.)/', $key, 2) + [null, null];

        return data_get(parent::getAttribute($key), $path);
    }

    /**
     * Get the bank that owns the FpxTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'request_payload->targetBankId', 'bank_id');
    }
}
