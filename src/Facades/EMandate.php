<?php

namespace ZarulIzham\EMandate\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ZarulIzham\EMandate\EMandate
 */
class EMandate extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \ZarulIzham\EMandate\EMandate::class;
    }
}
