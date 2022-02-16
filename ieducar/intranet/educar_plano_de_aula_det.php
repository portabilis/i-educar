<?php

return new class extends clsDetalhe {
    public $id;
    public $turma_id;
    public $data_inicial;
    public $data_final;
    public $ddp;
    public $atividades;
    public $bncc;
    public $conteudos;

    public function Gerar()
    {
        $this->titulo = 'Planejamento de Aula - Detalhe';
        $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();

        $tmp_obj = new clsModulesPlanejamentoPedagogico($this->id_freq);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('educar_plano_de_aula_lst.php');
        }


        if ($registro['detalhes']['data_inicial']) {
            $this->addDetalhe(
                [
                    'Data inicial',
                    dataToBrasil($registro['detalhes']['data_inicial'])
                ]
            );
        }

        if ($registro['detalhes']['data_final']) {
            $this->addDetalhe(
                [
                    'Data final',
                    dataToBrasil($registro['detalhes']['data_final'])
                ]
            );
        }

        if ($registro['detalhes']['data_final']) {
            $this->addDetalhe(
                [
                    'Data Final',
                    dataToBrasil($registro['detalhes']['data_final'])
                ]
            );
        }

        if ($registro['detalhes']['turma_id']) {
            $this->addDetalhe(
                [
                    'Turma',
                    $registro['detalhes']['turma_id']
                ]
            );
        }

        if ($registro['detalhes']['ddp']) {
            $this->addDetalhe(
                [
                    'DDP',
                    $registro['detalhes']['ddp']
                ]
            );
        } 

        if ($registro['detalhes']['atividades']) {
            $this->addDetalhe(
                [
                    'Atividades',
                    $registro['detalhes']['atividades']
                ]
            );
        }

        if ($registro['detalhes']['conteudos']) {
            $this->addDetalhe(
                [
                    'Conteudos',
                    $registro['detalhes']['conteudos']
                ]
            );
        
        }
        
        if (is_array($registro['bnccs']) && $registro['bnccs'] != null) {
            $this->montaListaBNCC($registro['bnccs']);
        }

        if (is_array($registro['conteudos']) && $registro['conteudos'] != null) {
            $this->montaListaConteudos($registro['conteudos']);
        }

        if ($registro['detalhes']['ddp']) {
            $this->addDetalhe(
                [
                    'Desdobramento didático pedagógico',
                    $registro['detalhes']['ddp']
                ]
            );
        }

        if ($registro['detalhes']['atividades']) {
            $this->addDetalhe(
                [
                    'Atividades',
                    $registro['detalhes']['atividades']
                ]
            );
        }

        if ($obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_plano_de_aula_cad.php';

            $data_agora = new DateTime('now');
            $data_agora = new \DateTime($data_agora->format('Y-m-d'));

        $this->url_cancelar = 'educar_plano_de_aula_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da frequência', [
            url('intranet/educar_professores_index.php') => 'Professores',
        ]);
    }
    $bncc = $registro['detalhes']['bncc'];
}

    function montaListaBNCC ($bnccs) {
        $this->tabela .= ' <div style="margin-bottom: 10px;">';
        $this->tabela .= ' <span style="display: block; float: left; width: 100px; font-weight: bold">Código</span>';
        $this->tabela .= ' <span style="display: block; float: left; width: 700px; font-weight: bold">Habilidade</span>';
        $this->tabela .= ' </div>';
        $this->tabela .= ' <br style="clear: left" />';

        for ($i=0; $i < count($bnccs); $i++) {
            $this->tabela .= '  <div style="margin-bottom: 10px; float: left" class="linha-disciplina" >';
            
            $this->tabela .= "  <span style='display: block; float: left; width: 100px'>{$bnccs[$i][bncc][codigo]}</span>";

            $this->tabela .= "  <span style='display: block; float: left; width: 700px'>{$bnccs[$i][bncc][habilidade]}</span>";

        $this->tabela .= '  </div>';
        $this->tabela .= '  <br style="clear: left" />';
    }

        $bncc  = '<table cellspacing="0" cellpadding="0" border="0">';
        $bncc .= sprintf('<tr align="left"><td>%s</td></tr>', $this->tabela);
        $bncc .= '</table>';

        $this->addDetalhe(
            [
                'Objetivos de aprendizagem/habilidades',
                $bncc
            ]
        );
    }

    function montaListaConteudos ($conteudos) {
        for ($i=0; $i < count($conteudos); $i++) {
            $this->tabela2 .= '  <div style="margin-bottom: 10px; float: left" class="linha-disciplina" >';
            
            $this->tabela2 .= "  <span style='display: block; float: left; width: 750px'>{$conteudos[$i][conteudo]}</span>";

            $this->tabela2 .= '  </div>';
            $this->tabela2 .= '  <br style="clear: left" />';
        }

        $bncc  = '<table cellspacing="0" cellpadding="0" border="0">';
        $bncc .= sprintf('<tr align="left"><td>%s</td></tr>', $this->tabela2);
        $bncc .= '</table>';

        $this->addDetalhe(
            [
                'Conteúdos',
                $bncc
            ]
        );
    }

    public function Formular()
    {
        $this->title = 'Frequência - Detalhe';
        $this->processoAp = 58;
    }
 };
