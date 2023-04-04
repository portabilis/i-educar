<?php

return new class extends clsDetalhe {
    public $titulo;

    public $cod_serie;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_curso;
    public $nm_serie;
    public $etapa_curso;
    public $concluinte;
    public $carga_horaria;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $regra_avaliacao_id;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Série - Detalhe';

        $this->cod_serie=$_GET['cod_serie'];

        $tmp_obj = new clsPmieducarSerie($this->cod_serie);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('educar_serie_lst.php');
        }

        $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
        $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
        $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];

        $registro['ref_cod_instituicao'] = $det_ref_cod_curso['ref_cod_instituicao'];
        $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissoes = new clsPermissoes();

        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe(['Instituição',
          $registro['ref_cod_instituicao']]);
            }
        }

        if ($registro['ref_cod_curso']) {
            $this->addDetalhe(['Curso', $registro['ref_cod_curso']]);
        }

        if ($registro['nm_serie']) {
            $this->addDetalhe(['Série', $registro['nm_serie']]);
        }

        if ($registro['etapa_curso']) {
            $this->addDetalhe(['Etapa Curso', $registro['etapa_curso']]);
        }

        $regras = $this->getRegrasAvaliacao();

        if ($regras) {
            $this->addDetalhe(['Regras de avaliação', $regras]);
        }

        if ($registro['concluinte']) {
            if ($registro['concluinte'] == 1) {
                $registro['concluinte'] = 'não';
            } elseif ($registro['concluinte'] == 2) {
                $registro['concluinte'] = 'sim';
            }

            $this->addDetalhe(['Concluinte', $registro['concluinte']]);
        }

        if ($registro['carga_horaria']) {
            $this->addDetalhe(['Carga Horária', $registro['carga_horaria']]);
        }

        $this->addDetalhe(['Dias letivos', $registro['dias_letivos']]);

        $this->addDetalhe(['Idade padrão', $registro['idade_ideal']]);

        if ($registro['observacao_historico']) {
            $this->addDetalhe(['Observação histórico', $registro['observacao_historico']]);
        }

        if ($obj_permissoes->permissao_cadastra(583, $this->pessoa_logada, 3)) {
            $this->url_novo = 'educar_serie_cad.php';
            $this->url_editar = "educar_serie_cad.php?cod_serie={$registro['cod_serie']}";
        }

        $this->url_cancelar = 'educar_serie_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da série', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
    }

    public function getRegrasAvaliacao()
    {
        $query = <<<SQL
            SELECT
                ra.id,
                ra.nome,
                rasa.ano_letivo
            FROM
                modules.regra_avaliacao AS ra
            INNER JOIN
                modules.regra_avaliacao_serie_ano AS rasa ON rasa.regra_avaliacao_id = ra.id
            INNER JOIN
                pmieducar.serie AS s ON s.cod_serie = rasa.serie_id
            WHERE TRUE
                AND s.cod_serie = $1
            ORDER BY
                ra.id, rasa.ano_letivo
SQL;

        $regras = Portabilis_Utils_Database::fetchPreparedQuery($query, [
            'params' => [$this->cod_serie]
        ]);

        if (empty($regras)) {
            return '';
        }

        $retorno = [];

        foreach ($regras as $regra) {
            $regra['id'] = (int) $regra['id'];
            if (!isset($retorno[$regra['id']])) {
                $retorno[$regra['id']] = [
                    'nome' => $regra['nome'],
                    'anos' => [(int) $regra['ano_letivo']]
                ];
            } else {
                $retorno[$regra['id']]['anos'][] = (int) $regra['ano_letivo'];
            }
        }

        $html = [];

        foreach ($retorno as $r) {
            $html[] = sprintf('%s (%s)', $r['nome'], join(', ', $r['anos']));
        }

        return join('<br>', $html);
    }

    public function Formular()
    {
        $this->title = 'Série';
        $this->processoAp = '583';
    }
};
