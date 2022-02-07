<?php

namespace iEducar\Modules\Addressing;

use App\Models\Person;
use App\Models\PersonHasPlace;
use App\Models\Place;

trait LegacyAddressingFields
{
    protected $address;
    protected $number;
    protected $complement;
    protected $neighborhood;
    protected $city_id;
    protected $postal_code;

    protected function loadAddress($person)
    {
        $place = PersonHasPlace::query()
            ->with('place')
            ->where('person_id', $person)
            ->orderBy('type')
            ->first();

        if ($place) {
            $place = $place->place;

            $this->address = $place->address;
            $this->number = $place->number;
            $this->complement = $place->complement;
            $this->neighborhood = $place->neighborhood;
            $this->city_id = $place->city_id;
            $this->postal_code = int2CEP($place->postal_code);
        }
    }

    protected function saveAddress($person)
    {
        $person = Person::query()->find($person);

        if (empty($person)) {
            return;
        }

        $hasEmpty = array_filter([$this->city_id, $this->address, $this->neighborhood, $this->postal_code]);

        if (count($hasEmpty) < 4) {
            return;
        }

        $place = Place::query()->updateOrCreate([
            'id' => $person->place->id ?? 0,
        ], [
            'address' => $this->address,
            'number' => $this->number ?: null,
            'complement' => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city_id' => $this->city_id,
            'postal_code' => idFederal2int($this->postal_code),
        ]);

        PersonHasPlace::query()->updateOrCreate([
            'person_id' => $person->getKey(),
            'type' => 1,
        ], [
            'place_id' => $place->getKey(),
        ]);
    }

    protected function viewAddress()
    {
        $enderecamentoObrigatorio = false;

        $this->campoRotulo('enderecamento', '<b>Endereçamento</b>', '', '', 'Digite um CEP para buscar <br>o endereço completo');

        $searchPostalCode = '<a id="search-postal-code" href="javascript:void(0)" class="span-busca-cep" style="color: blue; margin-left: 10px;">Preencher automaticamente usando o CEP</a>';
        $notKnowMyPostalCode = '<a href="http://www.buscacep.correios.com.br/sistemas/buscacep/" target="_blank" class="span-busca-cep" style="color: blue; margin-left: 10px;">Não sei meu CEP</a>';
        $loading = '<img id="postal_code_search_loading" src="/intranet/imagens/indicator.gif" style="margin-left: 10px; visibility: hidden">';

        $disabled = empty($this->postal_code);

        $this->campoCep(
            'postal_code',
            'CEP',
            $this->postal_code,
            $enderecamentoObrigatorio,
            '-',
            "{$loading} {$searchPostalCode} {$notKnowMyPostalCode}",
            false
        );

        $this->inputsHelper()->text('address', [
            'label' => 'Endereço',
            'disabled' => $disabled,
            'value' => $this->address,
            'required' => $enderecamentoObrigatorio
        ]);

        $this->inputsHelper()->integer('number', [
            'required' => $enderecamentoObrigatorio,
            'label' => 'Número',
            'disabled' => $disabled,
            'placeholder' => 'Número',
            'value' => $this->number,
            'max_length' => 6,
        ]);

        $this->inputsHelper()->text('complement', [
            'required' => $enderecamentoObrigatorio,
            'label' => 'Complemento',
            'disabled' => $disabled,
            'placeholder' => 'Complemento',
            'value' => $this->complement,
            'max_length' => 191
        ]);

        $this->inputsHelper()->text('neighborhood', [
            'label' => 'Bairro',
            'disabled' => $disabled,
            'value' => $this->neighborhood,
            'required' => $enderecamentoObrigatorio
        ]);

        $this->inputsHelper()->simpleSearchMunicipio('city', [
            'required' => $enderecamentoObrigatorio,
            'label' => 'Município',
            'disabled' => $disabled,
        ], [
            'objectName' => 'city',
            'hiddenInputOptions' => [
                'options' => [
                    'value' => $this->city_id,
                ],
            ],
        ]);
    }
}
