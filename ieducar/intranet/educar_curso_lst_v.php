<?php

use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;

return new class extends clsListagem
{
    public $__pessoa_logada;

    public $__titulo;

    public $__limite;

    public $__offset;

    public $cod_curso;

    public $ref_usuario_cad;

    public $ref_cod_tipo_regime;

    public $ref_cod_nivel_ensino;

    public $ref_cod_tipo_ensino;

    public $ref_cod_tipo_avaliacao;

    public $nm_curso;

    public $sgl_curso;

    public $qtd_etapas;

    public $frequencia_minima;

    public $media;

    public $media_exame;

    public $falta_ch_globalizada;

    public $carga_horaria;

    public $ato_poder_publico;

    public $edicao_final;

    public $objetivo_curso;

    public $publico_alvo;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_usuario_exc;

    public $ref_cod_instituicao;

    public $padrao_ano_escolar;

    public $hora_falta;

    public function Gerar()
    {
        $this->__pessoa_logada = $this->pessoa_logada;
        $this->__titulo = 'Curso - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Curso',
            'Nivel Ensino',
            'Tipo Ensino',
            'Instituicão',
        ]);

        $this->campoTexto(nome: 'nm_curso', campo: 'Curso', valor: $this->nm_curso, tamanhovisivel: 30, tamanhomaximo: 255);

        $opcoes = LegacyEducationLevel::query()
            ->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_nivel', direction: 'ASC')
            ->pluck(column: 'nm_nivel', key: 'cod_nivel_ensino')
            ->prepend(value: 'Selecione', key: '');

        $this->campoLista(nome: 'ref_cod_nivel_ensino', campo: 'Nivel Ensino', valor: $opcoes, default: $this->ref_cod_nivel_ensino);

        $opcoes = LegacyEducationType::query()
            ->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_tipo', direction: 'ASC')
            ->pluck(column: 'nm_tipo', key: 'cod_tipo_ensino')
            ->prepend(value: 'Selecione', key: '');

        $this->campoLista(nome: 'ref_cod_tipo_ensino', campo: 'Tipo Ensino', valor: $opcoes, default: $this->ref_cod_tipo_ensino);

        // Paginador
        $this->__limite = 20;
        $this->__offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->__limite - $this->__limite : 0;

        $obj_curso = new clsPmieducarCurso();
        $obj_curso->setOrderby('nm_curso ASC');
        $obj_curso->setLimite(intLimiteQtd: $this->__limite, intLimiteOffset: $this->__offset);

        $lista = $obj_curso->lista(
            int_ref_cod_tipo_regime: $this->ref_cod_nivel_ensino,
            int_ref_cod_nivel_ensino: $this->ref_cod_tipo_ensino,
            int_ref_cod_tipo_avaliacao: $this->nm_curso,
            date_data_cadastro_fim: 1
        );

        $total = $obj_curso->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // muda os campos data
                $registro['data_cadastro_time'] = strtotime(substr(string: $registro['data_cadastro'], offset: 0, length: 16));
                $registro['data_cadastro_br'] = date(format: 'd/m/Y H:i', timestamp: $registro['data_cadastro_time']);

                $registro['data_exclusao_time'] = strtotime(substr(string: $registro['data_exclusao'], offset: 0, length: 16));
                $registro['data_exclusao_br'] = date(format: 'd/m/Y H:i', timestamp: $registro['data_exclusao_time']);

                $det_ref_cod_nivel_ensino = LegacyEducationLevel::find($registro['ref_cod_nivel_ensino'])?->getAttributes();
                $registro['ref_cod_nivel_ensino'] = $det_ref_cod_nivel_ensino['nm_nivel'];

                $det_ref_cod_tipo_ensino = LegacyEducationType::find($registro['ref_cod_tipo_ensino'])?->getAttributes();
                $registro['ref_cod_tipo_ensino'] = $det_ref_cod_tipo_ensino['nm_tipo'];

                $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

                $this->addLinhas([
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['nm_curso']}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['ref_cod_nivel_ensino']}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['ref_cod_tipo_ensino']}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['ref_cod_instituicao']}</a>",
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_curso_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->__limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 0, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 0)) {
            $this->acao = 'go("educar_curso_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Curso';
        $this->processoAp = '0';
    }
};
