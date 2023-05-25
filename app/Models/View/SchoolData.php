<?php

namespace App\Models\View;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SchoolData extends Model
{
    protected $primaryKey = 'cod_escola';

    protected $table = 'relatorio.view_dados_escola';

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nome
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->telefone ? '(' . $this->telefone_ddd . ') ' . $this->telefone : '(##) ####-####' ;
            }
        );
    }

    protected function cellphone(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->celular ? '(' . $this->celular_ddd . ') ' . $this->celular : '(##) #####-####' ;
            }
        );
    }

    protected function address(): Attribute
    {
        return Attribute::make(
            get: fn () => implode(', ', [
                $this->logradouro,
                $this->bairro
            ])
        );
    }
}
