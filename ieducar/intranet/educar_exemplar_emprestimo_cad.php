<?php

use Illuminate\Support\Facades\Session;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_emprestimo;
    public $ref_usuario_devolucao;
    public $ref_usuario_cad;
    public $ref_cod_cliente;
    public $ref_cod_exemplar;
    public $data_retirada;
    public $data_devolucao;
    public $valor_multa;

    public $ref_cod_biblioteca;

    public $exemplar_emprestimo;
    public $incluir_tombo;
    public $excluir_tombo;

    public $confirmado;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_cliente = Session::get('emprestimo.cod_cliente');
        $this->ref_cod_biblioteca = Session::get('emprestimo.ref_cod_biblioteca');

        $this->cod_emprestimo=$_GET['cod_emprestimo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(610, $this->pessoa_logada, 11, 'educar_exemplar_emprestimo_lst.php');

        if (is_numeric($this->cod_emprestimo)) {
            $obj = new clsPmieducarExemplarEmprestimo($this->cod_emprestimo);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
            }
        }
        $this->url_cancelar = 'educar_exemplar_emprestimo_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('ref_cod_biblioteca', $this->ref_cod_biblioteca);

        $obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
        $det_biblioteca = $obj_biblioteca->detalhe();
        $max_emprestimo = $det_biblioteca['max_emprestimo'];
        $dias_espera = $det_biblioteca['dias_espera'];
        $valor_maximo_multa = $det_biblioteca['valor_maximo_multa'];
        $valor_maximo_multa = number_format($valor_maximo_multa, 2, ',', '.');

        $obj_cliente_suspenso = new clsPmieducarCliente();
        $lst_cliente_suspenso = $obj_cliente_suspenso->lista($this->ref_cod_cliente, null, null, null, null, null, null, null, null, null, 1, null, 'suspenso');
//      echo "<pre>";print_r($lst_cliente_suspenso);
        if (is_array($lst_cliente_suspenso)) {
            echo '<script> alert(\'Cliente atualmente suspenso!\\nNão é possivel realizar o empréstimo.\'); window.location = \'educar_exemplar_emprestimo_lst.php\';</script>';
            die();
        }

        $obj_exemplar_emprestimo = new clsPmieducarExemplarEmprestimo();
        $lst_exemplar_emprestimo = $obj_exemplar_emprestimo->lista(null, null, null, $this->ref_cod_cliente, null, null, null, null, null, null, false, $this->ref_cod_biblioteca);

        if (count($lst_exemplar_emprestimo) >= $max_emprestimo) {
            echo '<script> alert(\'Excedido o número máximo de empréstimos do cliente!\\nNão é possivel realizar o empréstimo.\'); window.location = \'educar_exemplar_emprestimo_lst.php\';</script>';
            die();
        }

        $lst_cliente_divida = $obj_exemplar_emprestimo->clienteDividaTotal(null, $this->ref_cod_cliente);
        if (is_array($lst_cliente_divida) && count($lst_cliente_divida)) {// calcula o valor total das multas do cliente em todas as bibliotecas
            foreach ($lst_cliente_divida as $divida) {
                $valor_total_multa =  $divida['valor_multa'];
                $valor_total_pago = $divida['valor_pago'];
            }

            $valor_total_divida = $valor_total_multa - $valor_total_pago;
        }

        //$lst_cliente_divida = $obj_exemplar_emprestimo->clienteDividaTotal( null,$this->ref_cod_cliente,null,$this->ref_cod_biblioteca );
        $lst_cliente_divida = $obj_exemplar_emprestimo->listaDividaPagamentoCliente($this->ref_cod_cliente, null, null, null, $this->ref_cod_biblioteca);
        if (is_array($lst_cliente_divida) && count($lst_cliente_divida)) {// calcula o valor das multas do cliente na biblioteca em que esta realizando o emprestimo
            foreach ($lst_cliente_divida as $divida) {
                $valor_multa =  $divida['valor_multa'];
                $valor_pago = $divida['valor_pago'];
            }

            $valor_divida = $valor_multa - $valor_pago;
        }
        if (!$valor_total_divida) {
            $valor_total_divida = 0;
        }
        if (!$valor_divida) {
            $valor_divida = 0;
        }

        $valor_total_divida = number_format($valor_total_divida, 2, ',', '.');
        $valor_divida = number_format($valor_divida, 2, ',', '.');
        // verifica se o valor da divida ultrapassou o valor maximo permitido da multa pela biblioteca
        if (($valor_maximo_multa <= $valor_total_divida) && ($this->confirmado != true)) {
            echo "<script> if(!confirm('Excedido o valor total das multas do cliente! \\n Valor total das multas: R$$valor_total_divida \\n Valor total das multas nessa biblioteca: R$$valor_divida \\n Valor máximo da multa permitido nessa biblioteca: R$$valor_maximo_multa \\n Deseja mesmo assim realizar o empréstimo?')) window.location = 'educar_exemplar_emprestimo_lst.php';</script>";
            $this->confirmado = true;
            $this->campoOculto('confirmado', $this->confirmado);
        }

        // primary keys
        $this->campoOculto('cod_emprestimo', $this->cod_emprestimo);

        // foreign keys
        $obj_cliente = new clsPmieducarCliente($this->ref_cod_cliente);
        $det_cliente = $obj_cliente->detalhe();
        $ref_idpes = $det_cliente['ref_idpes'];
        $obj_pessoa = new clsPessoa_($ref_idpes);
        $det_pessoa = $obj_pessoa->detalhe();
        $nm_pessoa = $det_pessoa['nome'];

        $this->campoTextoInv('nm_pessoa', 'Cliente', $nm_pessoa, 30, 255);

        //-----------------------INCLUI TOMBO------------------------//
        $this->campoQuebra();

        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        if ($_POST['exemplar_emprestimo']) {
            $this->exemplar_emprestimo = unserialize(urldecode($_POST['exemplar_emprestimo']));
        }

        if ($_POST['ref_cod_exemplar']) {
            $this->exemplar_emprestimo['ref_cod_exemplar_'][] = $_POST['ref_cod_exemplar'];
            unset($this->ref_cod_exemplar);
        }

        $this->campoOculto('excluir_tombo', '');
        unset($aux);

        if (isset($this->exemplar_emprestimo)) {
            foreach ($this->exemplar_emprestimo as $key => $campo) {
                if ($campo) {
                    foreach ($campo as $chave => $exemplar) {
                        if ($this->excluir_tombo == $exemplar) {
                            unset($this->exemplar_emprestimo[$key][$chave]);
                            unset($this->excluir_tombo);
                        } else {
                            $obj_exemplar = new clsPmieducarExemplar();
//                          $lst_exemplar = $obj_exemplar->lista($exemplar,null,null,null,null,null,null,2,null,null,null,null,null,1,null,null,null,null,$this->ref_cod_biblioteca);
                            $lst_exemplar = $obj_exemplar->lista(null, null, null, null, null, null, null, 2, null, null, null, null, null, 1, null, null, null, null, $this->ref_cod_biblioteca, null, null, null, $exemplar);

                            //verifica se o exemplar é disponibilizado para empréstimo
                            if (is_array($lst_exemplar) && count($lst_exemplar)) {
                                $det_exemplar = array_shift($lst_exemplar);
                                $cod_situacao = $det_exemplar['ref_cod_situacao'];

                                $obj_situacao = new clsPmieducarSituacao($cod_situacao);
                                $det_situacao = $obj_situacao->detalhe();
                                $situacao_padrao = $det_situacao['situacao_padrao'];
                                $permite_emprestimo = $det_situacao['permite_emprestimo'];
//                          echo "<pre>"; print_r($det_situacao); die();
                                // verifica se a situacao do exemplar é padrao (disponivel)
                                if ($situacao_padrao == 1 && $permite_emprestimo == 2) {
                                    $obj_reservas = new clsPmieducarReservas();
                                    $lst_reservas = $obj_reservas->lista(null, null, null, $this->ref_cod_cliente, null, null, null, null, null, null, $exemplar, 1);

                                    // verifica se o cliente reservou o exemplar
                                    if (is_array($lst_reservas) && count($lst_reservas)) {
                                        $reservas = array_shift($lst_reservas);
                                        // registra a retirada do exemplar pelo cliente
                                        $obj = new clsPmieducarReservas($reservas['cod_reserva'], $this->pessoa_logada, null, null, null, null, date('Y-m-d'), null, 0);
                                        $editou = $obj->edita();
                                        if ($editou) {
                                            // adiciona exemplar para empréstimo
                                            $obj_exemplar = new clsPmieducarExemplar($exemplar);
                                            $det_exemplar = $obj_exemplar->detalhe();
                                            $acervo = $det_exemplar['ref_cod_acervo'];
                                            $obj_acervo = new clsPmieducarAcervo($acervo);
                                            $det_acervo = $obj_acervo->detalhe();
                                            $titulo = $det_acervo['titulo'];
                                            $this->campoTextoInv("ref_cod_exemplar_{$exemplar}", '', $exemplar, 8, 255, false, false, true);
                                            $this->campoTextoInv("titulo_{$exemplar}", '', $titulo, 30, 255, false, false, false, '', "<a href='#' onclick=\"getElementById('excluir_tombo').value = '{$exemplar}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>");
                                            $aux['ref_cod_exemplar_'][] = $exemplar;
                                        } else {
                                            echo '<script> alert(\'ERRO !!!\\nNão foi possível registrar a retirada do exemplar.\'); </script>';
                                        }
                                    } else {
                                        $lst_reservas = $obj_reservas->lista(null, null, null, null, null, null, null, null, null, null, $exemplar, 1);

                                        // verifica se existem reservas do exemplar
                                        if (is_array($lst_reservas) && count($lst_reservas)) {
                                            $reservas = $obj_reservas->getUltimaReserva($exemplar);

                                            // verifica se a ultima reserva expirou
                                            if (is_array($reservas) && count($reservas)) {
                                                $dias_da_semana = [ 'Sun' => 1, 'Mon' => 2, 'Tue' => 3, 'Wed' => 4, 'Thu' => 5, 'Fri' => 6, 'Sat' => 7 ];

                                                $det_reserva = array_shift($reservas);
                                                $data_disponivel = $reservas['data_prevista_disponivel'];
                                                if ($dias_espera == 1) {
                                                    $data_disponivel = date('D Y-m-d', strtotime("$data_disponivel +".$dias_espera.' day'));
                                                } elseif ($dias_espera > 1) {
                                                    $data_disponivel = date('D Y-m-d', strtotime("$data_disponivel +".$dias_espera.' days'));
                                                }

                                                //---------------------DIAS FUNCIONAMENTO----------------------//
                                                $obj_biblioteca_dia = new clsPmieducarBibliotecaDia();
                                                $lst_biblioteca_dia = $obj_biblioteca_dia->lista($this->ref_cod_biblioteca);
                                                if (is_array($lst_biblioteca_dia) && count($lst_biblioteca_dia)) {
                                                    foreach ($lst_biblioteca_dia as $dia_semana) {
                                                        // dias de funcionamento da biblioteca
                                                        $biblioteca_dias_semana[] = $dia_semana['dia'];
                                                    }
                                                }
                                                // salva somente os dias que n se repetem ( dias de n funcionamento)
                                                $biblioteca_dias_folga = array_diff($dias_da_semana, $biblioteca_dias_semana);
                                                // inverte as relacoes entre chaves e valores ( de $variavel["Sun"] => 1, para $variavel[1] => "Sun")
                                                $biblioteca_dias_folga = array_flip($biblioteca_dias_folga);

                                                //---------------------DIAS FERIADO----------------------//
                                                $obj_biblioteca_feriado = new clsPmieducarBibliotecaFeriados();
                                                $lst_biblioteca_feriado = $obj_biblioteca_feriado->lista(null, $this->ref_cod_biblioteca);
                                                if (is_array($lst_biblioteca_feriado) && count($lst_biblioteca_feriado)) {
                                                    foreach ($lst_biblioteca_feriado as $dia_feriado) {
                                                        // dias de feriado da biblioteca
                                                        $biblioteca_dias_feriado[] = dataFromPgToBr($dia_feriado['data_feriado'], 'D Y-m-d');
                                                    }
                                                }

                                                // devido a comparacao das datas, é necessario mudar o formato da data
                                                $data_disponivel = dataFromPgToBr($data_disponivel, 'D Y-m-d');

                                                // verifica se a data cai em algum dia que a biblioteca n funciona
                                                while (in_array(substr($data_disponivel, 0, 3), $biblioteca_dias_folga) || in_array($data_disponivel, $biblioteca_dias_feriado)) {
                                                    $data_disponivel = date('D Y-m-d ', strtotime("$data_disponivel +1 day"));
                                                    $data_disponivel = dataFromPgToBr($data_disponivel, 'D Y-m-d');
                                                }

                                                $data_disponivel = dataFromPgToBr($data_disponivel, 'Y-m-d');

                                                if ($data_disponivel < date('Y-m-d')) {
                                                    // desativa reserva desatualizada
                                                    $obj = new clsPmieducarReservas();
                                                    $lst = $obj->lista(null, null, null, null, null, null, $reservas['data_prevista_disponivel'], $reservas['data_prevista_disponivel'], null, null, $reservas['ref_cod_exemplar'], 1, $this->ref_cod_biblioteca);
                                                    if (is_array($lst) && count($lst)) {
                                                        $det = array_shift($lst);
                                                        $cod_reserva = $det['cod_reserva'];

                                                        $obj = new clsPmieducarReservas($cod_reserva, $this->pessoa_logada, null, null, null, null, null, null, 0);
                                                        $excluiu = $obj->excluir();
                                                        if ($excluiu) {
                                                            // adiciona exemplar para empréstimo
                                                            $obj_exemplar = new clsPmieducarExemplar($exemplar);
                                                            $det_exemplar = $obj_exemplar->detalhe();
                                                            $acervo = $det_exemplar['ref_cod_acervo'];
                                                            $obj_acervo = new clsPmieducarAcervo($acervo);
                                                            $det_acervo = $obj_acervo->detalhe();
                                                            $titulo = $det_acervo['titulo'];
                                                            $this->campoTextoInv("ref_cod_exemplar_{$exemplar}", '', $exemplar, 8, 255, false, false, true);
                                                            $this->campoTextoInv("titulo_{$exemplar}", '', $titulo, 30, 255, false, false, false, '', "<a href='#' onclick=\"getElementById('excluir_tombo').value = '{$exemplar}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>");
                                                            $aux['ref_cod_exemplar_'][] = $exemplar;
                                                        } else {
                                                            echo '<script> alert(\'ERRO - Não foi possível desativar reserva desatualizada!\'); </script>';
                                                        }
                                                    } else {
                                                        echo '<script> alert(\'ERRO - Não foi possível encontrar a reserva!\'); </script>';
                                                    }
                                                } else {
                                                    echo '<script> alert(\'Exemplar reservado!\\nNo momento, não disponível para empréstimo.\'); </script>';
                                                }
                                            }
                                        }// adiciona exemplar para empréstimo
                                        else {
                                            $obj_exemplar = new clsPmieducarExemplar($exemplar);
                                            $det_exemplar = $obj_exemplar->detalhe();
                                            $acervo = $det_exemplar['ref_cod_acervo'];
                                            $obj_acervo = new clsPmieducarAcervo($acervo);
                                            $det_acervo = $obj_acervo->detalhe();
                                            $titulo = $det_acervo['titulo'];
                                            $this->campoTextoInv("ref_cod_exemplar_{$exemplar}", '', $exemplar, 8, 255, false, false, true);
                                            $this->campoTextoInv("titulo_{$exemplar}", '', $titulo, 30, 255, false, false, false, '', "<a href='#' onclick=\"getElementById('excluir_tombo').value = '{$exemplar}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>");
                                            $aux['ref_cod_exemplar_'][] = $exemplar;
                                        }
                                    }
                                } else {
                                    echo '<script> alert(\'Situação atual do exemplar não permite empréstimo!\'); </script>';
                                }
                            } else {
                                echo '<script> alert(\'Exemplar não disponível para empréstimo!\'); </script>';
                            }
                        }
                    }
                }
            }
            unset($this->exemplar_emprestimo);
            $this->exemplar_emprestimo = $aux;
        }

        $this->campoOculto('exemplar_emprestimo', serialize($this->exemplar_emprestimo));

        if ($aux) {
            $this->campoNumero('ref_cod_exemplar', 'Tombo', $this->ref_cod_exemplar, 15, 50, false, '', '<a href=\'#\' onclick="getElementById(\'incluir_tombo\').value = \'S\'; getElementById(\'tipoacao\').value = \'\'; acao();"><img src=\'imagens/nvp_bot_adiciona.gif\' title=\'Incluir\' border=0></a>');
        } else {
            $this->campoNumero('ref_cod_exemplar', 'Tombo', $this->ref_cod_exemplar, 15, 50, true, '', '<a href=\'#\' onclick="getElementById(\'incluir_tombo\').value = \'S\'; getElementById(\'tipoacao\').value = \'\'; acao();"><img src=\'imagens/nvp_bot_adiciona.gif\' title=\'Incluir\' border=0></a>');
        }

        $this->campoOculto('incluir_tombo', '');
//      $this->campoRotulo( "bt_incluir_tombo", "Tombo", "<a href='#' onclick=\"getElementById('incluir_tombo').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_incluir2.gif' title='Incluir' border=0></a>" );

        $this->campoQuebra();
        //-----------------------FIM INCLUI TOMBO------------------------//
    }

    public function Novo()
    {
        $this->ref_cod_cliente = Session::get('emprestimo.cod_cliente');

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(610, $this->pessoa_logada, 11, 'educar_exemplar_emprestimo_lst.php');

        $this->exemplar_emprestimo = unserialize(urldecode($this->exemplar_emprestimo));
        if ($this->exemplar_emprestimo) {
            $this->exemplar_emprestimo = $this->exemplar_emprestimo['ref_cod_exemplar_'];
            foreach ($this->exemplar_emprestimo as $campo) {
                $obj = new clsPmieducarExemplarEmprestimo(null, null, $this->pessoa_logada, $this->ref_cod_cliente, $campo);
                $cadastrou = $obj->cadastra();
                if ($cadastrou) {
                    $obj_situacao = new clsPmieducarSituacao();
                    $lst_situacao = $obj_situacao->lista(null, null, null, null, 1, null, 0, 1, null, null, null, null, 1, $this->ref_cod_biblioteca);
                    if (is_array($lst_situacao) && count($lst_situacao)) {
                        $det_situacao = array_shift($lst_situacao);
                        $cod_situacao = $det_situacao['cod_situacao'];
                        $obj = new clsPmieducarExemplar($campo, null, null, null, $cod_situacao, $this->pessoa_logada, null, null, null, null, null, 1);
                        $editou = $obj->edita();
                        if (!$editou) {
                            $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

                            return false;
                        }
                    } else {
                        echo '<script> alert(\'ERRO - Não foi possível encontrar a situação EMPRESTADO da biblioteca utilizada!\'); </script>';
                    }
                } else {
                    $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

                    return false;
                }
            }
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_exemplar_devolucao_lst.php');
        }
        echo '<script> alert(\'É necessário adicionar pelo menos 1 Tombo!\') </script>';
        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Exemplar Empr&eacute;stimo';
        $this->processoAp = '610';
    }
};
