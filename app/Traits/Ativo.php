<?php

namespace App\Traits;

trait Ativo
{
    protected static function bootAtivo(): void
    {
        static::creating(function ($model) {
            $model->ativo = 1;
        });
    }
}
