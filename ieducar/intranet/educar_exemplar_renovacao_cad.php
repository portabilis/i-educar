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

    public $dias_da_semana = [ 'Sun' => 1, 'Mon' => 2, 'Tue' => 3, 'Wed' => 4, 'Thu' => 5, 'Fri' => 6, 'Sat' => 7 ];

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_emprestimo = $_GET['cod_emprestimo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(628, $this->pessoa_logada, 11, 'educar_exemplar_devolucao_lst.php');

        if (is_numeric($this->cod_emprestimo)) {
            $obj = new clsPmieducarExemplarEmprestimo($this->cod_emprestimo);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
            }
        }
        $this->url_cancelar = 'educar_exemplar_devolucao_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Realizar renovação', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_emprestimo', $this->cod_emprestimo);

        $this->data_retirada = dataFromPgToBr($this->data_retirada, 'Y-m-d');

        $obj_exemplar = new clsPmieducarExemplar($this->ref_cod_exemplar);
        $det_exemplar = $obj_exemplar->detalhe();
        $cod_acervo = $det_exemplar['ref_cod_acervo'];

        $obj_acervo = new clsPmieducarAcervo($cod_acervo);
        $det_acervo = $obj_acervo->detalhe();
        // tipo de exemplar
        $cod_exemplar_tipo = $det_acervo['ref_cod_exemplar_tipo'];
        $titulo_obra = $det_acervo['titulo'];
        $this->ref_cod_biblioteca = $det_acervo['ref_cod_biblioteca'];

        $this->campoOculto('ref_cod_biblioteca', $this->ref_cod_biblioteca);

        $obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
        $det_biblioteca = $obj_biblioteca->detalhe();
        // valor da multa da biblioteca por dia
        $valor_multa_biblioteca = $det_biblioteca['valor_multa'];

        $obj_cliente_tipo_cliente = new clsPmieducarClienteTipoCliente();
        $lst_cliente_tipo_cliente = $obj_cliente_tipo_cliente->lista(null, $this->ref_cod_cliente, null, null, null, null, null, null, $this->ref_cod_biblioteca);
        if (is_array($lst_cliente_tipo_cliente) && count($lst_cliente_tipo_cliente)) {
            $det_cliente_tipo_cliente = array_shift($lst_cliente_tipo_cliente);
            // tipo do cliente
            $cod_cliente_tipo = $det_cliente_tipo_cliente['ref_cod_cliente_tipo'];

            $obj_cliente_tipo_exemplar_tipo = new clsPmieducarClienteTipoExemplarTipo($cod_cliente_tipo, $cod_exemplar_tipo);
            $det_cliente_tipo_exemplar_tipo = $obj_cliente_tipo_exemplar_tipo->detalhe();
            // qtde de dias disponiveis para emprestimo
            $dias_emprestimo = $det_cliente_tipo_exemplar_tipo['dias_emprestimo'];
        }

        $data_entrega = date('Y-m-d', strtotime("$this->data_retirada +".$dias_emprestimo.' days'));

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
        $biblioteca_dias_folga = array_diff($this->dias_da_semana, $biblioteca_dias_semana ?? []);
        // inverte as relacoes entre chaves e valores ( de $variavel["Sun"] => 1, para $variavel[1] => "Sun")
        $biblioteca_dias_folga = array_flip($biblioteca_dias_folga);

        //---------------------DIAS FERIADO----------------------//
        $obj_biblioteca_feriado = new clsPmieducarBibliotecaFeriados();
        $lst_biblioteca_feriado = $obj_biblioteca_feriado->lista(null, $this->ref_cod_biblioteca);
        if (is_array($lst_biblioteca_feriado) && count($lst_biblioteca_feriado)) {
            foreach ($lst_biblioteca_feriado as $dia_feriado) {
                // dias de feriado da biblioteca
                $biblioteca_dias_feriado[] = dataFromPgToBr($dia_feriado['data_feriado'], 'Y-m-d');
            }
        }

        // devido a comparacao das datas, é necessario mudar o formato da data
        $data_entrega = dataFromPgToBr($data_entrega, 'Y-m-d');

        if (!is_array($biblioteca_dias_folga)) {
            $biblioteca_dias_folga = [null];
        }
        if (!is_array($biblioteca_dias_feriado)) {
            $biblioteca_dias_feriado = [null];
        }

        // verifica se a data cai em algum dia que a biblioteca n funciona
        while (in_array(substr($data_entrega, 0, 3), $biblioteca_dias_folga) || in_array($data_entrega, $biblioteca_dias_feriado)) {
            $data_entrega = date('Y-m-d ', strtotime("$data_entrega +1 day"));
            $data_entrega = dataFromPgToBr($data_entrega, 'Y-m-d');
        }

        $data_entrega = dataFromPgToBr($data_entrega, 'Y-m-d');

        // verifica se houve atraso na devolucao do exemplar
        if ($data_entrega < date('Y-m-d')) {
            $dias_atraso = (int)((time() - strtotime($data_entrega)) / 86400);
            $dias_atraso = $dias_atraso > 0 ? $dias_atraso : 0;

            $valor_divida = $dias_atraso * $valor_multa_biblioteca;
            $valor_divida = number_format($valor_divida, 2, ',', '.');
            $data_entrega = dataFromPgToBr($data_entrega, 'd/m/Y');
        }

        // foreign keys
        $obj_cliente = new clsPmieducarCliente($this->ref_cod_cliente);
        $det_cliente = $obj_cliente->detalhe();
        $ref_idpes = $det_cliente['ref_idpes'];
        $obj_pessoa = new clsPessoa_($ref_idpes);
        $det_pessoa = $obj_pessoa->detalhe();
        $nm_pessoa = $det_pessoa['nome'];

        $this->campoTextoInv('nm_pessoa', 'Cliente', $nm_pessoa, 30, 255);
        $ref_cod_exemplar_ = $this->ref_cod_exemplar;
        $this->campoTextoInv('ref_cod_exemplar_', 'Tombo', $ref_cod_exemplar_, 15, 50);
        $this->campoOculto('ref_cod_exemplar', $this->ref_cod_exemplar);
        $this->campoTextoInv('titulo_obra', 'Obra', $titulo_obra, 30, 255);

        $reload = Session::get('reload');

        if ($valor_divida && !$reload) {
            $this->valor_multa = $valor_divida;
            $this->campoMonetario('valor_divida', 'Valor Multa', $valor_divida, 8, 8, false, '', '', '', true);
            $this->campoOculto('valor_multa', $this->valor_multa);

            $reload = 1;

            Session::put('reload', $reload);
            Session::save();
            Session::start();

            echo "<script>
                if(!confirm('Atraso na devolução do exemplar ($dias_atraso dias)! \\n Data prevista para a entrega: $data_entrega \\n Valor total da multa: R$$valor_divida \\n Deseja adicionar a multa?'))
                    window.location = 'educar_exemplar_devolucao_cad.php?cod_emprestimo={$this->cod_emprestimo}';
            </script>";
        } elseif ($valor_divida && $reload) {
            echo '<script> alert(\'Valor da multa ignorado!\'); </script>';
            $valor_divida = '0,00';
            $this->campoMonetario('valor_divida', 'Valor Multa', $valor_divida, 8, 8, false, '', '', '', true);
            $this->campoOculto('valor_multa', $this->valor_multa);
        }
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(628, $this->pessoa_logada, 11, 'educar_exemplar_devolucao_lst.php');

        $this->valor_multa = urldecode($this->valor_multa);
        $this->valor_multa = str_replace('.', '', $this->valor_multa);
        $this->valor_multa = str_replace(',', '.', $this->valor_multa);

        $obj_situacao = new clsPmieducarSituacao();
        $lst_situacao = $obj_situacao->lista(null, null, null, null, 2, null, 1, 0, null, null, null, null, 1, $this->ref_cod_biblioteca);
        if (is_array($lst_situacao) && count($lst_situacao)) {
            $det_situacao = array_shift($lst_situacao);
            $cod_situacao = $det_situacao['cod_situacao'];
        } else {
            echo '<script> alert(\'ERRO - Não foi possível encontrar a situação DISPONÍVEL da biblioteca utilizada!\'); </script>';

            return false;
        }

        $obj = new clsPmieducarExemplarEmprestimo($this->cod_emprestimo, $this->pessoa_logada, null, null, null, date('Y-m-d'), null, $this->valor_multa);
        $editou = $obj->edita();
        if ($editou) {
            $obj = new clsPmieducarExemplar($this->ref_cod_exemplar, null, null, null, $cod_situacao, $this->pessoa_logada, null, null, null, null, null, 1);
            $editou = $obj->edita();
            if (!$editou) {
                $this->mensagem = 'Cadastro não realizado.<br>';

                return false;
            }

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_exemplar_devolucao_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Renovação de empréstimo';
        $this->processoAp = '628';
    }
};
