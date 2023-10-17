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

    protected function saveAddress($person, $optionalFields = false)
    {
        $person = Person::query()->find($person);

        if (empty($person)) {
            return;
        }

        if (!$optionalFields) {
            $validation_fields = [$this->city_id, $this->address, $this->neighborhood, $this->postal_code];
            $original_count = count($validation_fields);
            $hasEmpty = array_filter($validation_fields);

            if (count($hasEmpty) < $original_count) {
                return;
            }
        }

        $place = Place::query()->updateOrCreate([
            'id' => $person->place->id ?? 0,
        ], [
            'address' => $this->address ?: null,
            'number' => $this->number ?: null,
            'complement' => $this->complement ?: null,
            'neighborhood' => $this->neighborhood ?: null,
            'city_id' => $this->city_id ?: null,
            'postal_code' => empty($this->postal_code) ? null : idFederal2int($this->postal_code),
        ]);

        PersonHasPlace::query()->updateOrCreate([
            'person_id' => $person->getKey(),
            'type' => 1,
        ], [
            'place_id' => $place->getKey(),
        ]);
    }

    protected function viewAddress($optionalFields = false, $complementMaxLength = 20)
    {
        $enderecamentoObrigatorio = false;

        $this->campoRotulo('enderecamento', '<b>Endereçamento</b>', '', '', 'Digite um CEP para buscar <br>o endereço completo');

        $searchPostalCode = '<a id="search-postal-code" data-optional="'.$optionalFields.'" href="javascript:void(0)" class="span-busca-cep" style="color: blue; margin-left: 10px;">Preencher automaticamente usando o CEP</a>';
        $notKnowMyPostalCode = '<a href="http://www.buscacep.correios.com.br/sistemas/buscacep/" target="_blank" class="span-busca-cep" style="color: blue; margin-left: 10px;">Não sei meu CEP</a>';
        $loading = '<img id="postal_code_search_loading" src="/intranet/imagens/indicator.gif" style="margin-left: 10px; visibility: hidden">';

        $disabled = !$optionalFields && empty($this->postal_code);

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
            'required' => $enderecamentoObrigatorio,
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
            'max_length' => $complementMaxLength,
        ]);

        $this->inputsHelper()->text('neighborhood', [
            'label' => 'Bairro',
            'disabled' => $disabled,
            'value' => $this->neighborhood,
            'required' => $enderecamentoObrigatorio,
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
