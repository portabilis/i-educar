<?php

use App\Models\LegacyDocument;
use App\Models\LegacyPhone;
use App\Models\PersonHasPlace;
use iEducar\Legacy\Model;

class clsPessoaFj extends Model
{
    public $idpes;

    public $nome;

    public $idpes_cad;

    public $data_cad;

    public $url;

    public $tipo;

    public $idpes_rev;

    public $data_rev;

    public $situacao;

    public $origem_gravacao;

    public $email;

    public $data_nasc;

    public $bairro;

    public $idbai;

    public $logradouro;

    public $idlog;

    public $idtlog;

    public $cidade;

    public $idmun;

    public $sigla_uf;

    public $pais;

    public $complemento;

    public $reside_desde;

    public $letra;

    public $numero;

    public $cep;

    public $bloco;

    public $apartamento;

    public $andar;

    public $ddd_1;

    public $fone_1;

    public $ddd_2;

    public $fone_2;

    public $ddd_fax;

    public $fone_fax;

    public $ddd_mov;

    public $fone_mov;

    public $rg;

    public $cpf;

    public $banco = 'gestao_homolog';

    public $schema_cadastro = 'cadastro';

    public $tabela_pessoa = 'pessoa';

    public function __construct($int_idpes = false)
    {
        $this->idpes = $int_idpes;
    }

    public function lista(
        $str_nome = false,
        $inicio_limite = false,
        $qtd_registros = false,
        $str_orderBy = false,
        $arrayint_idisin = false,
        $arrayint_idnotin = false,
        $str_tipo_pessoa = false
    ) {
        $objPessoa = new clsPessoa_;

        $listaPessoa = $objPessoa->lista(
            $str_nome = pg_escape_string($str_nome),
            $inicio_limite,
            $qtd_registros,
            $str_orderBy,
            $arrayint_idisin,
            $arrayint_idnotin,
            $str_tipo_pessoa
        );

        if ($listaPessoa && count($listaPessoa) > 0) {
            return $listaPessoa;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array|false
     */
    public function detalhe()
    {
        if ($this->idpes && is_numeric($this->idpes)) {
            $objPessoa = new clsPessoa_($this->idpes);
            $detalhePessoa = $objPessoa->detalhe();

            $has = PersonHasPlace::query()->with('place.city.state')->where('person_id', $this->idpes)->first();

            if ($has) {
                $place = $has->place;

                $this->bairro = $place->neighborhood;
                $this->logradouro = $place->address;
                $this->sigla_uf = $place->city->state->abbreviation;
                $this->cidade = $place->city->name;
                $this->reside_desde = null;
                $this->idtlog = $place->id;
                $this->complemento = $place->complement;
                $this->numero = $place->number;
                $this->letra = null;
                $this->idlog = $place->id;
                $this->idbai = $place->id;
                $this->cep = $place->postal_code;
                $this->apartamento = null;
                $this->bloco = null;
                $this->andar = null;
                $this->zona_localizacao = null;

                $detalhePessoa['bairro'] = $this->bairro;
                $detalhePessoa['logradouro'] = $this->logradouro;
                $detalhePessoa['sigla_uf'] = $this->sigla_uf;
                $detalhePessoa['cidade'] = $this->cidade;
                $detalhePessoa['reside_desde'] = $this->reside_desde;
                $detalhePessoa['idtlog'] = $this->idtlog;
                $detalhePessoa['complemento'] = $this->complemento;
                $detalhePessoa['numero'] = $this->numero;
                $detalhePessoa['letra'] = $this->letra;
                $detalhePessoa['idbai'] = $this->idbai;
                $detalhePessoa['cep'] = $this->cep;
                $detalhePessoa['idlog'] = $this->idlog;
            }

            $obj_fisica = new clsFisica($this->idpes);
            $detalhe_fisica = $obj_fisica->detalhe();

            if ($detalhe_fisica) {
                $detalhePessoa['cpf'] = $detalhe_fisica['cpf'];

                $this->cpf = $detalhe_fisica['cpf'];
                $this->data_nasc = $detalhe_fisica['data_nasc'];

                if ($this->data_nasc) {
                    $detalhePessoa['data_nasc'] = $this->data_nasc;
                }
            }

            $listaFone = LegacyPhone::query()
                ->where('idpes', $this->idpes)
                ->get();

            foreach ($listaFone as $fone) {
                $sufixo = match ((int) $fone->tipo) {
                    LegacyPhone::TYPE_LANDLINE => '1',
                    LegacyPhone::TYPE_MOBILE => '2',
                    LegacyPhone::TYPE_MOBILE_ALT => 'mov',
                    LegacyPhone::TYPE_FAX => 'fax',
                    default => null,
                };

                if ($sufixo === null) {
                    continue;
                }

                $detalhePessoa["ddd_{$sufixo}"] = $fone->ddd;
                $detalhePessoa[] = &$detalhePessoa["ddd_{$sufixo}"];
                $detalhePessoa["fone_{$sufixo}"] = $fone->fone;
                $detalhePessoa[] = &$detalhePessoa["fone_{$sufixo}"];

                $this->{"ddd_{$sufixo}"} = $fone->ddd;
                $this->{"fone_{$sufixo}"} = $fone->fone;
            }

            $documentos = LegacyDocument::find($this->idpes)?->getAttributes();

            if (is_array($documentos)) {
                if ($documentos['rg']) {
                    $detalhePessoa['rg'] = $documentos['rg'];
                    $detalhePessoa[] = &$detalhePessoa['rg'];

                    $this->rg = $documentos['rg'];
                }
            }

            $this->idpes = $detalhePessoa['idpes'];
            $this->nome = $detalhePessoa['nome'];
            $this->idpes_cad = $detalhePessoa['idpes_cad'];
            $this->data_cad = $detalhePessoa['data_cad'];
            $this->url = $detalhePessoa['url'];
            $this->tipo = $detalhePessoa['tipo'];
            $this->idpes_rev = $detalhePessoa['idpes_rev'];
            $this->data_rev = $detalhePessoa['data_rev'];
            $this->situacao = $detalhePessoa['situacao'];
            $this->origem_gravacao = $detalhePessoa['origem_gravacao'];
            $this->email = $detalhePessoa['email'];

            return $detalhePessoa;
        }

        return false;
    }

    public function queryRapida($int_idpes)
    {
        $this->idpes = $int_idpes;

        $this->detalhe();

        $resultado = [];
        $pos = 0;

        for ($i = 1; $i < func_num_args(); $i++) {
            $campo = func_get_arg($i);
            $resultado[$pos] = ($this->$campo) ? $this->$campo : '';
            $resultado[$campo] = &$resultado[$pos];

            $pos++;
        }

        if (count($resultado) > 0) {
            return $resultado;
        }

        return false;
    }
}
