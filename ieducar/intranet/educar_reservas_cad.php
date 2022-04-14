<?php

use Illuminate\Support\Facades\Session;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_reserva;
    public $ref_usuario_libera;
    public $ref_usuario_cad;
    public $ref_cod_cliente;
    public $data_reserva;
    public $data_disponivel;
    public $data_retirada;
    public $ref_cod_exemplar;

    public $dias_espera;
    public $ref_cod_biblioteca;
    public $ref_cod_acervo;
    public $titulo_obra;

    public $passo;
    public $existe_reserva = 0;
    public $ref_cod_reserva;

    public $dias_da_semana = [ 'Sun' => 1, 'Mon' => 2, 'Tue' => 3, 'Wed' => 4, 'Thu' => 5, 'Fri' => 6, 'Sat' => 7 ];

    public $confirmado;

    public function Inicializar()
    {
        $retorno = 'Novo';
        $this->ref_cod_cliente = Session::get('reservas.cod_cliente');
        $this->ref_cod_biblioteca = Session::get('reservas.ref_cod_biblioteca');

        $this->cod_reserva=$_GET['cod_reserva'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(609, $this->pessoa_logada, 11, 'educar_reservas_lst.php');

        if (!isset($this->ref_cod_cliente)) {
            $this->simpleRedirect('educar_reserva_vaga_lst.php');
        }

        if (is_numeric($this->cod_reserva)) {
            $obj = new clsPmieducarReservas($this->cod_reserva);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
            }
        }

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('ref_cod_biblioteca', $this->ref_cod_biblioteca);

        if ($this->ref_cod_acervo) {
            $obj_acervo = new clsPmieducarAcervo($this->ref_cod_acervo);
            $det_acervo = $obj_acervo->detalhe();
            $this->titulo_obra = $det_acervo['titulo'];
        }

        $this->cod_biblioteca = $this->ref_cod_biblioteca;
        $this->campoOculto('cod_biblioteca', $this->cod_biblioteca);

        $obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
        $det_biblioteca = $obj_biblioteca->detalhe();
        $max_emprestimo = $det_biblioteca['max_emprestimo'];
        $valor_maximo_multa = $det_biblioteca['valor_maximo_multa'];
        $this->dias_espera = $det_biblioteca['dias_espera'];
        $this->campoOculto('dias_espera', $this->dias_espera);

        $obj_cliente_suspenso = new clsPmieducarCliente();
        $lst_cliente_suspenso = $obj_cliente_suspenso->lista($this->ref_cod_cliente, null, null, null, null, null, null, null, null, null, 1, null, 'suspenso');
        if (is_array($lst_cliente_suspenso)) {
            echo '<script> alert(\'Cliente atualmente suspenso!\\nNão é possivel realizar a reserva.\'); window.location = \'educar_reservas_lst.php\';</script>';
            die();
        }

        $obj_reservas = new clsPmieducarReservas();
        $lst_reservas = $obj_reservas->lista(null, null, null, $this->ref_cod_cliente, null, null, null, null, null, null, null, 1, $this->ref_cod_biblioteca);

        // verifica se o cliente excedeu a qntde de reservas permitidas pela biblioteca
        if (count($lst_reservas) >= $max_emprestimo) {
            echo '<script> alert(\'Excedido o número máximo de reservas do cliente!\\nNão é possivel realizar a reserva.\'); window.location = \'educar_reservas_lst.php\';</script>';
            die();
        }

        $obj_exemplar_emprestimo = new clsPmieducarExemplarEmprestimo();
        $lst_cliente_divida = $obj_exemplar_emprestimo->clienteDividaTotal(null, $this->ref_cod_cliente);
        if (is_array($lst_cliente_divida) && count($lst_cliente_divida)) {// calcula o valor total das multas do cliente em todas as bibliotecas
            foreach ($lst_cliente_divida as $divida) {
                $valor_total_multa =  $divida['valor_multa'];
                $valor_total_pago = $divida['valor_pago'];
            }

            $valor_total_divida = $valor_total_multa - $valor_total_pago;
        }

        $lst_cliente_divida = $obj_exemplar_emprestimo->clienteDividaTotal(null, $this->ref_cod_cliente, null, $this->ref_cod_biblioteca);
        if (is_array($lst_cliente_divida) && count($lst_cliente_divida)) {// calcula o valor das multas do cliente na biblioteca em que esta realizando o emprestimo
            foreach ($lst_cliente_divida as $divida) {
                $valor_multa =  $divida['valor_multa'];
                $valor_pago = $divida['valor_pago'];
            }

            $valor_divida = $valor_multa - $valor_pago;
        }
        // verifica se o valor da divida ultrapassou o valor maximo permitido da multa pela biblioteca
        if (($valor_maximo_multa <= $valor_total_divida) && ($this->confirmado != true)) {
            echo "<script> if(!confirm('Excedido o valor total das multas do cliente! \\n Valor total das multas: R$$valor_total_divida,00 \\n Valor total das multas nessa biblioteca: R$$valor_divida,00 \\n Valor máximo da multa permitido nessa biblioteca: R$$valor_maximo_multa,00 \\n Deseja mesmo assim realizar a reserva?')) window.location = 'educar_reservas_lst.php';</script>";
            $this->confirmado = true;
            $this->campoOculto('confirmado', $this->confirmado);
        }

        if ($this->titulo_obra) {
            $obj_exemplar = new clsPmieducarExemplar();
            $lst_exemplar = $obj_exemplar->lista(null, null, null, $this->ref_cod_acervo, null, null, null, 2, null, null, null, null, null, 1, null, null, null, null, $this->ref_cod_biblioteca);

            // verifica se o exemplar pode ser emprestado
            if (is_array($lst_exemplar) && count($lst_exemplar)) {
                $obj_reservas = new clsPmieducarReservas();
                foreach ($lst_exemplar as $exemplar) {
                    $lst_reservas = $obj_reservas->lista(null, null, null, $this->ref_cod_cliente, null, null, null, null, null, null, $exemplar['cod_exemplar'], 1);

                    if (is_array($lst_reservas) && count($lst_reservas)) {
                        // Já existe uma reserva do exemplar feita pelo cliente
                        $reservou = true;
                    }
                }
                if (!$reservou) {
                    // volta para o inicio da lista
                    reset($lst_exemplar);

                    $reservas = [];
                    foreach ($lst_exemplar as $exemplar) {
                        $obj_situacao = new clsPmieducarSituacao($exemplar['ref_cod_situacao']);
                        $det_situacao = $obj_situacao->detalhe();
                        $situacao_permite_emprestimo = $det_situacao['permite_emprestimo'];
                        $situacao_emprestada = $det_situacao['situacao_emprestada'];
                        $situacao_padrao = $det_situacao['situacao_padrao'];

                        // verifica se a situacao do exemplar permite emprestimo
                        if ($situacao_permite_emprestimo == 2 && $situacao_emprestada == 0 && $situacao_padrao == 1) {
                            unset($this->titulo_obra);
                            unset($this->ref_cod_acervo);
                            echo "<script> alert('Exemplar disponível para empréstimo!\\nNão é possivel realizar a reserva.\\n  TOMBO #{$exemplar['cod_exemplar']}'); </script>";
                        }// verifica se a situacao do exemplar esta como 'emprestado'
                        elseif ($situacao_permite_emprestimo == 1 && $situacao_emprestada == 1 && $situacao_padrao == 0) {
                            $lst_reservas = $obj_reservas->lista(null, null, null, null, null, null, null, null, null, null, $exemplar['cod_exemplar'], 1);

                            // verifica se existem reservas do exemplar
                            if (is_array($lst_reservas) && count($lst_reservas)) {
                                $this->existe_reserva = 2;
                            } else {
                                // encontrado exemplar sem reservas
                                $this->ref_cod_exemplar = $exemplar['cod_exemplar'];
                                break;
                            }
                        } else {
                            unset($this->titulo_obra);
                            unset($this->ref_cod_acervo);
                            echo '<script> alert(\'Situação atual do exemplar não permite reserva!\'); </script>';
                        }
                    }
                } else {
                    unset($this->titulo_obra);
                    unset($this->ref_cod_acervo);
                    echo '<script> alert(\'Já existe uma reserva do exemplar feita pelo cliente!\'); </script>';
                }
            } else {
                unset($this->titulo_obra);
                unset($this->ref_cod_acervo);
                echo '<script> alert(\'Exemplar não disponível para reserva/empréstimo!\'); </script>';
            }
        }

        // foreign keys
        $obj_cliente = new clsPmieducarCliente($this->ref_cod_cliente);
        $det_cliente = $obj_cliente->detalhe();
        $ref_idpes = $det_cliente['ref_idpes'];
        $obj_pessoa = new clsPessoa_($ref_idpes);
        $det_pessoa = $obj_pessoa->detalhe();
        $nm_pessoa = $det_pessoa['nome'];

        $this->campoTextoInv('nm_pessoa', 'Cliente', $nm_pessoa, 30, 255);

        // outros Filtros
        $this->campoTexto('titulo_obra', 'Obra', $this->titulo_obra, 30, 255, true, false, false, '', "<img border=\"0\" class=\"btn\" onclick=\"pesquisa_obra();\" id=\"ref_cod_acervo_lupa\" name=\"ref_cod_acervo_lupa\" src=\"imagens/lupaT.png\"\/>", '', '', true);
        $this->campoOculto('ref_cod_acervo', $this->ref_cod_acervo);

        // caso exemplar esteja emprestado, mas ainda nao exista reserva
        if (isset($this->ref_cod_exemplar)) {
//          die("emprestado, sem reserva");
            $this->campoOculto('ref_cod_exemplar', $this->ref_cod_exemplar);
            $this->existe_reserva = 1;
            $this->campoOculto('existe_reserva', $this->existe_reserva);
        }// caso já exista(m) reserva(s) para o exemplar
        elseif ($this->existe_reserva == 2) {
//          die("emprestado, com reserva");
            $lst_reserva = $obj_reservas->getUltimasReservas($this->ref_cod_acervo, 1);
            if (is_array($lst_reserva) && count($lst_reserva)) {
                $det_reserva = array_shift($lst_reserva);
                $this->ref_cod_exemplar = $det_reserva['ref_cod_exemplar'];
                $this->data_disponivel = $det_reserva['data_prevista_disponivel'];

                $this->data_disponivel = dataFromPgToBr($this->data_disponivel, 'Y-m-d');

                $this->campoOculto('data_disponivel', $this->data_disponivel);
                $this->campoOculto('ref_cod_exemplar', $this->ref_cod_exemplar);
                $this->campoOculto('existe_reserva', $this->existe_reserva);
            }
        }
        $this->campoOculto('passo', 1);
        $this->url_cancelar = 'educar_reservas_lst.php';
        $this->nome_url_cancelar = 'Cancelar';
    }

    public function Novo()
    {
        $this->ref_cod_cliente = Session::get('reservas.cod_cliente');

        if ($this->passo == 2) {
            return true;
        }

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(609, $this->pessoa_logada, 11, 'educar_reservas_lst.php');

        $obj_acervo = new clsPmieducarAcervo($this->ref_cod_acervo);
        $det_acervo = $obj_acervo->detalhe();
        // tipo de exemplar
        $cod_exemplar_tipo = $det_acervo['ref_cod_exemplar_tipo'];

        if ($this->existe_reserva == 1) {
//          die("1");
//          echo "EMPRESTIMO <br>";
            // ------------------- DADOS DO CLIENTE (EMPRESTIMO) ------------------ //
            $obj_exemplar_emprestimo = new clsPmieducarExemplarEmprestimo();
            $lst_exemplar_emprestimo = $obj_exemplar_emprestimo->lista(null, null, null, null, $this->ref_cod_exemplar, null, null, null, null, null, false, $this->ref_cod_biblioteca);
            if (is_array($lst_exemplar_emprestimo) && count($lst_exemplar_emprestimo)) {
                $det_exemplar_emprestimo = array_shift($lst_exemplar_emprestimo);
                $cod_cliente = $det_exemplar_emprestimo['ref_cod_cliente'];
                // data do emprestimo
                $data_retirada = $det_exemplar_emprestimo['data_retirada'];
                $data_prevista_disponivel = dataFromPgToBr($data_retirada, 'Y-m-d');
            } else {
                echo '<script> alert(\'ERRO - Não foi possível encontrar o registro do empréstimo!\'); </script>';
            }
        } elseif ($this->existe_reserva == 2) {
//          die("2");
//          echo "RESERVA <br>";
            // ------------------- DADOS DO CLIENTE (RESERVA) ------------------ //
            $obj_reservas = new clsPmieducarReservas();
            $lst_reservas = $obj_reservas->lista(null, null, null, null, null, null, $this->data_disponivel, $this->data_disponivel, null, null, $this->ref_cod_exemplar, 1, $this->ref_cod_biblioteca);

            if (is_array($lst_reservas) && count($lst_reservas)) {
                $det_reservas = array_shift($lst_reservas);
                $cod_cliente = $det_reservas['ref_cod_cliente'];
                // data da reserva
                $data_prevista_disponivel = $this->data_disponivel;
            } else {
                echo '<script> alert(\'ERRO - Não foi possível encontrar a reserva!\'); </script>';
            }
        }
//      echo "data_prevista_disponivel 1: ".$data_prevista_disponivel."<br>";

        $obj_cliente_tipo_cliente = new clsPmieducarClienteTipoCliente();
        $lst_cliente_tipo_cliente = $obj_cliente_tipo_cliente->lista(null, $cod_cliente, null, null, null, null, null, null, $this->ref_cod_biblioteca);
        $det_cliente_tipo_cliente = array_shift($lst_cliente_tipo_cliente);
        // tipo do cliente
        $cod_cliente_tipo = $det_cliente_tipo_cliente['ref_cod_cliente_tipo'];

        $obj_cliente_tipo_exemplar_tipo = new clsPmieducarClienteTipoExemplarTipo($cod_cliente_tipo, $cod_exemplar_tipo);
        $det_cliente_tipo_exemplar_tipo = $obj_cliente_tipo_exemplar_tipo->detalhe();
        // qtde de dias disponiveis para emprestimo
        $dias_emprestimo = $det_cliente_tipo_exemplar_tipo['dias_emprestimo'];

//      echo "dias_espera: ".$this->dias_espera."<br>";

        if ($this->existe_reserva == 2) {
            // Dias que o cliente tem pra pegar o exemplar. Calculo feito levando em consideracao a pior situacao.
            $data_prevista_disponivel = date('D Y-m-d', strtotime("$data_prevista_disponivel +".$this->dias_espera.' days'));
        }

//      echo "data_prevista_disponivel 2: ".$data_prevista_disponivel."<br>";

        $data_prevista_disponivel = date('D Y-m-d', strtotime("$data_prevista_disponivel +".$dias_emprestimo.' days'));

//      echo "data_prevista_disponivel 3: ".$data_prevista_disponivel."<br>";

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
        $biblioteca_dias_folga = array_diff($this->dias_da_semana, $biblioteca_dias_semana);
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

//      echo "<pre>"; print_r($biblioteca_dias_feriado)."<br>";

        // Cliente tem o dia inteiro para entregar o exemplar. Exemplar somente disponivel para a proxima reserva no dia seguinte.
        $data_prevista_disponivel = date('D Y-m-d ', strtotime("$data_prevista_disponivel +1 day"));
        // devido a comparacao das datas, é necessario mudar o formato da data
        $data_prevista_disponivel = dataFromPgToBr($data_prevista_disponivel, 'D Y-m-d');

//      echo "data_prevista_disponivel 4: ".$data_prevista_disponivel."<br>";

        // verifica se a data cai em algum dia que a biblioteca n funciona
        while (in_array(substr($data_prevista_disponivel, 0, 3), $biblioteca_dias_folga) || in_array($data_prevista_disponivel, $biblioteca_dias_feriado)) {
//          echo "data_prevista_disponivel ASDFG = ".$data_prevista_disponivel."<br>";
            $data_prevista_disponivel = date('D Y-m-d ', strtotime("$data_prevista_disponivel +1 day"));
            $data_prevista_disponivel = dataFromPgToBr($data_prevista_disponivel, 'D Y-m-d');
//          echo "data_prevista_disponivel ASDFG = ".$data_prevista_disponivel."<br>";
        }

//      echo "data_prevista_disponivel 5: ".$data_prevista_disponivel."<br>";die;

        $data_prevista_disponivel = dataFromPgToBr($data_prevista_disponivel, 'Y-m-d');

        $obj = new clsPmieducarReservas(null, null, $this->pessoa_logada, $this->ref_cod_cliente, null, $data_prevista_disponivel, null, $this->ref_cod_exemplar, 1);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_reservas_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-reservas-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Reservas';
        $this->processoAp = '609';
    }
};
