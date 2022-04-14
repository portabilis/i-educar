<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_rota;
    public $descricao;

    // INCLUI NOVO
    public $pontos;
    public $ref_cod_ponto_transporte_escolar;
    public $hora;
    public $tipo;
    public $ref_cod_veiculo;

    //------INCLUI DISCIPLINA------//
    public $historico_disciplinas;
    public $nm_disciplina;
    public $nota;
    public $faltas;
    public $excluir_disciplina;
    public $ultimo_sequencial;

    public $aceleracao;

    public function Inicializar()
    {
        $retorno = 'Editar';

        $this->cod_rota=$_GET['cod_rota'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(21238, $this->pessoa_logada, 7, "transporte_rota_det.php?cod_rota={$this->cod_rota}");
        $volta = false;
        if (is_numeric($this->cod_rota)) {
            $obj = new clsModulesRotaTransporteEscolar($this->cod_rota);
            $registro  = $obj->detalhe();
            if ($registro) {
                $this->descricao = $registro['descricao'];
            } else {
                $volta = true;
            }
        } else {
            $volta = true;
        }

        if ($volta) {
            $this->simpleRedirect('transporte_rota_lst.php');
        }
        $this->url_cancelar = "transporte_rota_det.php?cod_rota={$this->cod_rota}";
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Editar itinerário', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = (!$this->$campo) ?  $val : $this->$campo ;
            }
        }

        $this->campoRotulo('cod_rota', 'Código da rota', $this->cod_rota);
        $this->campoRotulo('descricao', 'Rota', $this->descricao);

        //---------------------INCLUI DISCIPLINAS---------------------//
        $this->campoQuebra();

        if (is_numeric($this->cod_rota) && !$_POST) {
            $obj = new clsModulesItinerarioTransporteEscolar();
            $obj->setOrderby(' seq ASC');
            $registros = $obj->lista(null, $this->cod_rota);
            $qtd_pontos = 0;
            if ($registros) {
                foreach ($registros as $campo) {
                    //$this->pontos[$qtd_pontos][] = $campo["cod_itinerario_transporte_escolar"];
                    $this->pontos[$qtd_pontos][] = $campo['ref_cod_ponto_transporte_escolar'].' - '.$campo['descricao'];
                    //$this->pontos[$qtd_pontos][] = $campo["descricao"];
                    $this->pontos[$qtd_pontos][] = $campo['hora'];
                    $this->pontos[$qtd_pontos][] = $campo['tipo'];
                    $this->pontos[$qtd_pontos][] = $campo['ref_cod_veiculo'].' - '.$campo['nome_onibus'];
                    //$this->pontos[$qtd_pontos][] = $campo["seq"];
                    $qtd_pontos++;
                }
            }
        }

        $this->campoTabelaInicio('pontos', 'Itinerário', ['Ponto (Requer pré-cadastro)<br/> <spam style=" font-weight: normal; font-size: 10px;">Digite o código ou nome do ponto e selecione o desejado</spam>','Hora','Tipo','Veículo (Requer pré-cadastro)<br/> <spam style=" font-weight: normal; font-size: 10px;">Digite o código, nome ou placa do veículo e selecione o desejado</spam>' ], $this->pontos);

        $this->campoTexto('ref_cod_ponto_transporte_escolar', 'Ponto (Requer pré-cadastro)', $this->ref_cod_ponto_transporte_escolar, 50, 255, false, true, false, '', '', '', 'onfocus');

        $this->campoHora('hora', 'Hora', $this->hora);
        $this->campoLista('tipo', 'Tipo', [ '' => 'Selecione', 'I' => 'Ida', 'V' => 'Volta'], $this->tipo);
        $this->campoTexto('ref_cod_veiculo', 'Veículo', $this->ref_cod_veiculo, 50, 255, false, false, false, '', '', '', 'onfocus');
        $this->campoTabelaFim();

        $this->campoQuebra();
        //---------------------FIM INCLUI DISCIPLINAS---------------------//

        // carrega estilo para feedback messages, para exibir msg validação frequencia.

        $style = '/modules/Portabilis/Assets/Stylesheets/Frontend.css';
        Portabilis_View_Helper_Application::loadStylesheet($this, $style);

        Portabilis_View_Helper_Application::loadJavascript(
            $this,
            ['/modules/Portabilis/Assets/Javascripts/Utils.js',
                        '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/SimpleSearch.js',
                        '/modules/Portabilis/Assets/Javascripts/Validator.js']
        );
        $this->addBotao('Excluir todos', "transporte_itinerario_del.php?cod_rota={$this->cod_rota}");
    }

    public function Novo()
    {
        return true;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(21238, $this->pessoa_logada, 7, "transporte_rota_det.php?cod_rota={$this->cod_rota}");

        if ($this->ref_cod_ponto_transporte_escolar) {
            $obj  = new clsModulesItinerarioTransporteEscolar();

            $excluiu = $obj->excluirTodos($this->cod_rota);

            if ($excluiu) {
                $sequencial = 1;
                foreach ($this->ref_cod_ponto_transporte_escolar as $key => $ponto) {
                    $obj = new clsModulesItinerarioTransporteEscolar(
                        null,
                        $this->cod_rota,
                        $sequencial,
                        $this->retornaCodigo($ponto),
                        $this->retornaCodigo($this->ref_cod_veiculo[$key]),
                        $this->hora[$key],
                        $this->tipo[$key]
                    );
                    $cadastrou1 = $obj->cadastra();
                    if (!$cadastrou1) {
                        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

                        return false;
                    }
                    $sequencial++;
                }
            }
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect("transporte_rota_det.php?cod_rota={$this->cod_rota}");
        }
    }

    public function Excluir()
    {
        return true;
    }

    protected function retornaCodigo($palavra)
    {
        return substr($palavra, 0, strpos($palavra, ' -'));
    }

    protected function fixupFrequencia($frequencia)
    {
        if (strpos($frequencia, ',')) {
            $frequencia = str_replace('.', '', $frequencia);
            $frequencia = str_replace(',', '.', $frequencia);
        }

        return $frequencia;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/transporte-itinerario-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Itinerário';
        $this->processoAp = '21238';
    }
};
