<?php

return new class extends clsCadastro {
    public $pessoa_logada;

    public $cod_servidor;
    public $ref_cod_instituicao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_nivel;
    public $ref_cod_subnivel;
    public $ref_cod_categoria;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_servidor        = $_GET['ref_cod_servidor'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, 'educar_servidor_lst.php');

        if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
            $obj = new clsPmieducarServidor(
                $this->cod_servidor,
                null,
                null,
                null,
                null,
                null,
                null,
                $this->ref_cod_instituicao
            );

            $registro  = $obj->detalhe();
            if ($registro) {
                $this->ref_cod_subnivel = $registro['ref_cod_subnivel'];
                $obj_subnivel = new clsPmieducarSubnivel($this->ref_cod_subnivel);
                $det_subnivel = $obj_subnivel->detalhe();

                if ($det_subnivel) {
                    $this->ref_cod_nivel = $det_subnivel['ref_cod_nivel'];
                }

                if ($this->ref_cod_nivel) {
                    $obj_nivel = new clsPmieducarNivel($this->ref_cod_nivel);
                    $det_nivel = $obj_nivel->detalhe();
                    $this->ref_cod_categoria = $det_nivel['ref_cod_categoria_nivel'];
                }

                $retorno = 'Editar';
            }
        } else {
            echo sprintf('<script>window.parent.fechaExpansivel("%s");</script>', $_GET['div']);
            die();
        }

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('cod_servidor', $this->cod_servidor);
        $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);

        $obj_categoria = new clsPmieducarCategoriaNivel();
        $lst_categoria = $obj_categoria->lista(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );

        $opcoes = ['' => 'Selecione uma categoria'];

        if ($lst_categoria) {
            foreach ($lst_categoria as $categoria) {
                $opcoes[$categoria['cod_categoria_nivel']] = $categoria['nm_categoria_nivel'];
            }
        }

        $this->campoLista('ref_cod_categoria', 'Categoria', $opcoes, $this->ref_cod_categoria);

        $opcoes = ['' => 'Selecione uma categoria'];

        if ($this->ref_cod_categoria) {
            $obj_nivel = new clsPmieducarNivel();
            $lst_nivel = $obj_nivel->buscaSequenciaNivel($this->ref_cod_categoria);

            if ($lst_nivel) {
                foreach ($lst_nivel as $nivel) {
                    $opcoes[$nivel['cod_nivel']] = $nivel['nm_nivel'];
                }
            }
        }

        $this->campoLista('ref_cod_nivel', 'Nível', $opcoes, $this->ref_cod_nivel, '', false);

        $opcoes = ['' => 'Selecione um nível'];

        if ($this->ref_cod_nivel) {
            $obj_nivel = new clsPmieducarSubnivel();
            $lst_nivel = $obj_nivel->buscaSequenciaSubniveis($this->ref_cod_nivel);
            if ($lst_nivel) {
                foreach ($lst_nivel as $subnivel) {
                    $opcoes[$subnivel['cod_subnivel']] = $subnivel['nm_subnivel'];
                }
            }
        }

        $this->campoLista(
            'ref_cod_subnivel',
            'Subnível',
            $opcoes,
            $this->ref_cod_subnivel,
            '',
            false
        );
    }

    public function Novo()
    {
        echo sprintf('<script>window.parent.fechaExpansivel("%s");</script>', $_GET['div']);
        die();
    }

    public function Editar()
    {
        $obj_servidor = new clsPmieducarServidor(
            $this->cod_servidor,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ref_cod_instituicao,
            $this->ref_cod_subnivel
        );

        $obj_servidor->edita();

        echo sprintf('<script>parent.fechaExpansivel("%s");window.parent.location.reload(true);</script>', $_GET['div']);
        die;
    }

    public function Excluir()
    {
        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-servidor-nivel-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor Nível';
        $this->processoAp         = 0;
        $this->renderMenu         = false;
        $this->renderMenuSuspenso = false;
    }
};
