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

        $tmp_obj = new clsModulesPlanejamentoPedagogico($this->id);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('educar_plano_de_aula_lst.php');
        }


        if ($registro['detalhes']['data_inicial']) {
            $this->addDetalhe(
                [
                    'Data Inicial',
                    dataToBrasil($registro['detalhes']['data_inicial'])
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

function montaListaBNCC ($codigos, $descricoes) {
    
    $this->tabela .= ' <div style="margin-bottom: 10px;">';
    $this->tabela .= ' <span style="display: block; float: left; width: 100px; font-weight: bold">Código</span>';
    $this->tabela .= ' <span style="display: block; float: left; width: 700px; font-weight: bold">Habilidade</span>';
    $this->tabela .= ' </div>';
    $this->tabela .= ' <br style="clear: left" />';

    for ($i=0; $i < count($codigos); $i++) { 
        $checked = !$aluno['presenca'] ? "checked='true'" : '';

        $this->tabela .= '  <div style="margin-bottom: 10px; float: left" class="linha-disciplina" >';
        
        $this->tabela .= "  <span style='display: block; float: left; width: 100px'>{$codigos[$i]}</span>";

        $this->tabela .= "  <span style='display: block; float: left; width: 700px'>{$descricoes[$i]}</span>";

        $this->tabela .= '  </div>';
        $this->tabela .= '  <br style="clear: left" />';
    }

    $bncc  = '<table cellspacing="0" cellpadding="0" border="0">';
    $bncc .= sprintf('<tr align="left"><td>%s</td></tr>', $this->tabela);
    $bncc .= '</table>';

    $this->addDetalhe(
        [
            'BNCC',
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
