<?php

use App\Models\PersonHasPlace;
use iEducar\Legacy\Model;

require_once 'include/clsBanco.inc.php';

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
        $objPessoa = new clsPessoa_();

        $listaPessoa = $objPessoa->lista(
            $str_nome,
            $inicio_limite,
            $qtd_registros,
            $str_orderBy,
            $arrayint_idisin,
            $arrayint_idnotin,
            $str_tipo_pessoa
        );

        if (count($listaPessoa) > 0) {
            return $listaPessoa;
        }

        return false;
    }

    public function lista_rapida(
        $idpes = null,
        $nome = null,
        $id_federal = null,
        $inicio_limite = null,
        $limite = null,
        $str_tipo_pessoa = null,
        $str_order_by = null,
        $int_ref_cod_sistema = null
    ) {
        $db = new clsBanco();

        $filtros = '';
        $filtroTipo = '';
        $whereAnd = ' WHERE ';
        $outros_filtros = false;
        $filtro_cnpj = false;

        if (is_string($nome) && $nome != '') {
            $nome = pg_escape_string($nome);

            $filtros .= "{$whereAnd} translate(upper(nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$nome}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
            $outros_filtros = true;
        }

        if (is_numeric($idpes)) {
            $filtros .= "{$whereAnd} idpes = '{$idpes}'";
            $whereAnd = ' AND ';
            $outros_filtros = true;
        }

        if (is_numeric($int_ref_cod_sistema)) {
            $filtro_sistema = true;
            $filtros .= "{$whereAnd} (ref_cod_sistema = '{$int_ref_cod_sistema}' OR id_federal is not null)";
            $whereAnd = ' AND ';
        }

        if (is_numeric($id_federal)) {
            $db2 = new clsBanco();

            $sql = sprintf(
                'SELECT idpes FROM cadastro.juridica WHERE cnpj LIKE \'%%%s%%\'',
                $id_federal
            );

            $db2->Consulta($sql);

            while ($db2->ProximoRegistro()) {
                list($id_pes) = $db2->Tupla();
                $array_idpes[] = $id_pes;
            }

            if (is_array($array_idpes)) {
                $array_idpes = implode(', ', $array_idpes);
                $filtros .= "{$whereAnd} idpes IN ($array_idpes)";
                $whereAnd = ' AND ';
                $filtro_idfederal = true;
            } else {
                return false;
            }
        }

        if (is_string($str_tipo_pessoa)) {
            $filtroTipo .= " AND tipo  = '{$str_tipo_pessoa}' ";
            $outros_filtros = true;
        }

        if (is_string($str_order_by)) {
            $order = "ORDER BY $str_order_by";
        }

        $limit = '';

        if (is_numeric($inicio_limite) && is_numeric($limite)) {
            $limit = "LIMIT $limite OFFSET $inicio_limite";
        }

        if ($filtro_idfederal) {
            $this->_total = $db->CampoUnico(
                sprintf('SELECT COUNT(0) FROM cadastro.v_pessoa_fj %s', $filtros)
            );
        } else {
            if ($filtro_sistema && $outros_filtros == false || $filtro_cnpj) {
                $this->_total = $db->CampoUnico(
                    sprintf('SELECT COUNT(0) FROM cadastro.v_pessoafj_count %s', $filtros)
                );
            } else {
                $this->_total = $db->CampoUnico(
                    sprintf('SELECT COUNT(0) FROM cadastro.v_pessoa_fj %s', $filtros)
                );
            }
        }

        $sql = sprintf(
            '
      SELECT
        idpes,
        nome,
        ref_cod_sistema,
        fantasia,
        tipo,
        id_federal AS cpf,
        id_federal AS cnpj,
        id_federal
      FROM
        cadastro.v_pessoa_fj
        %s
        %s
        %s',
            $filtros,
            $order,
            $limit
        );

        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['_total'] = $this->_total;
            $resultado[] = $tupla;
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if ($this->idpes) {
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

            $objFone = new clsPessoaTelefone();
            $listaFone = $objFone->lista($this->idpes);

            if ($listaFone) {
                foreach ($listaFone as $fone) {
                    if ($fone['tipo'] == 1) {
                        $detalhePessoa['ddd_1'] = $fone['ddd'];
                        $detalhePessoa[] = &$detalhePessoa['ddd_1'];
                        $detalhePessoa['fone_1'] = $fone['fone'];
                        $detalhePessoa[] = &$detalhePessoa['fone_1'];

                        $this->ddd_1 = $fone['ddd'];
                        $this->fone_1 = $fone['fone'];
                    }

                    if ($fone['tipo'] == 2) {
                        $detalhePessoa['ddd_2'] = $fone['ddd'];
                        $detalhePessoa[] = &$detalhePessoa['ddd_2'];
                        $detalhePessoa['fone_2'] = $fone['fone'];
                        $detalhePessoa[] = &$detalhePessoa['fone_2'];

                        $this->ddd_2 = $fone['ddd'];
                        $this->fone_2 = $fone['fone'];
                    }

                    if ($fone['tipo'] == 3) {
                        $detalhePessoa['ddd_mov'] = $fone['ddd'];
                        $detalhePessoa[] = &$detalhePessoa['ddd_mov'];
                        $detalhePessoa['fone_mov'] = $fone['fone'];
                        $detalhePessoa[] = &$detalhePessoa['fone_mov'];

                        $this->ddd_mov = $fone['ddd'];
                        $this->fone_mov = $fone['fone'];
                    }

                    if ($fone['tipo'] == 4) {
                        $detalhePessoa['ddd_fax'] = $fone['ddd'];
                        $detalhePessoa[] = &$detalhePessoa['ddd_fax'];
                        $detalhePessoa['fone_fax'] = $fone['fone'];
                        $detalhePessoa[] = &$detalhePessoa['fone_fax'];

                        $this->ddd_fax = $fone['ddd'];
                        $this->fone_fax = $fone['fone'];
                    }
                }
            }

            $obj_documento = new clsDocumento($this->idpes);
            $documentos = $obj_documento->detalhe();

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
