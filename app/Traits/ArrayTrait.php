<?php

namespace App\Traits;


trait ArrayTrait
{
    protected function cleanArray($array)
    {
        return array_filter($array, function ($value) {
            return !empty($value);
        });
    }
}