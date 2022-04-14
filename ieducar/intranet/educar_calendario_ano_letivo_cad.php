<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_calendario_ano_letivo;
    public $ref_cod_escola;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ano;
    public $data_cadastra;
    public $data_exclusao;
    public $ativo;
    public $inicio_ano_letivo;
    public $termino_ano_letivo;

    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_calendario_ano_letivo=$_GET['cod_calendario_ano_letivo'];
        $this->ref_cod_escola=$_GET['ref_cod_escola'];
        $this->ref_cod_instituicao=$_GET['ref_cod_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(620, $this->pessoa_logada, 7, 'educar_calendario_ano_letivo_lst.php');
        //  $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
        //$this->ref_cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);

        if (is_numeric($this->cod_calendario_ano_letivo)) {
            $obj = new clsPmieducarCalendarioAnoLetivo($this->cod_calendario_ano_letivo);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
                $obj_det = $obj_escola->detalhe();

                /*
                $this->inicio_ano_letivo = dataFromPgToBr( $this->inicio_ano_letivo );
                $this->termino_ano_letivo = dataFromPgToBr( $this->termino_ano_letivo );
                */
                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(620, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_calendario_ano_letivo_det.php?cod_calendario_ano_letivo={$registro['cod_calendario_ano_letivo']}" : 'educar_calendario_ano_letivo_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' calendário do ano letivo', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_calendario_ano_letivo', $this->cod_calendario_ano_letivo);

        /*$obj_anos = new clsPmieducarEscolaAnoLetivo();
        $lista_ano = $obj_anos->lista(null,null,null,null,2,null,null,null,null,1);
        if($lista_ano)
        {
            $script = "<script>
                      var ar_anos = new Array();
                        ";

            foreach ($lista_ano as $ano) {

                $script .= "ar_anos[ar_anos.length] = new Array('{$ano['ref_cod_escola']}','{$ano['ano']}');\n";
            }

            echo $script .= "</script>";
        }*/

        if ($_GET) {
            $this->ref_cod_escola=$_GET['ref_cod_escola'];
            $this->ref_cod_instituicao=$_GET['ref_cod_instituicao'];
        }
        $this->inputsHelper()->dynamic(['instituicao', 'escola']);

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_calendario_ano_letivo_det.php?cod_calendario_ano_letivo={$registro['cod_calendario_ano_letivo']}" : 'educar_calendario_ano_letivo_lst.php';

//      $ano_array = array();
        $ano_array = [ '' => 'Selecione um ano' ];
        if ($this->ref_cod_escola) {
            $obj_anos = new clsPmieducarEscolaAnoLetivo();
            $lista_ano = $obj_anos->lista($this->ref_cod_escola, null, null, null, 2, null, null, null, null, 1);
            if ($lista_ano) {
                foreach ($lista_ano as $ano) {
                    $ano_array["{$ano['ano']}"] = $ano['ano'];
                }
            }
        } else {
            $ano_array = [ '' => 'Selecione uma escola' ];
        }
        // text
//      $conc = ",";
//      $anos = array( "" => "Selecione" );
//      $ano_atual = date("Y");
//      $lim = 5;
//      for($a = date('Y') ; $a < $ano_atual + $lim ; $a++ )
//          if(!key_exists($a,$ano_array))
//              $anos["{$a}"] = "{$a}";
//          else
//              $lim++;

        $this->campoLista('ano', 'Ano', $ano_array, $this->ano, '', false);

        //  if($this->ref_cod_escola)
    //      $this->campoOculto("ref_cod_escola",$this->ref_cod_escola);

        //if($this->ref_cod_instituicao)
    //  $this->campoOculto("ref_cod_instituicao",$this->ref_cod_instituicao);

        // data
//      $this->campoData( "inicio_ano_letivo", "Inicio Ano Letivo", $this->inicio_ano_letivo, true );
//      $this->campoData( "termino_ano_letivo", "Termino Ano Letivo", $this->termino_ano_letivo, true );
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(620, $this->pessoa_logada, 7, 'educar_calendario_ano_letivo_lst.php');

        $obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
        $data_inicio = $obj_ano_letivo_modulo->menorData($this->ano, $this->ref_cod_escola);
        $data_fim = $obj_ano_letivo_modulo->maiorData($this->ano, $this->ref_cod_escola);

        if ($data_inicio && $data_fim) {
            $obj_calend_ano_letivo = new clsPmieducarCalendarioAnoLetivo();
            $lst_calend_ano_letivo = $obj_calend_ano_letivo->lista(null, $this->ref_cod_escola, null, null, $this->ano);
            if ($lst_calend_ano_letivo) {
                $det_calend_ano_letivo = array_shift($lst_calend_ano_letivo);

                $obj_calend_ano_letivo = new clsPmieducarCalendarioAnoLetivo($det_calend_ano_letivo['cod_calendario_ano_letivo'], $this->ref_cod_escola, $this->pessoa_logada, null, $this->ano, null, null, 1/*, $data_inicio,$data_fim*/);
                if ($obj_calend_ano_letivo->edita()) {
                    $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
                    throw new HttpResponseException(
                        new RedirectResponse('educar_calendario_ano_letivo_lst.php')
                    );
                }

                $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

                return false;
            } else {
                $obj_calend_ano_letivo = new clsPmieducarCalendarioAnoLetivo(null, $this->ref_cod_escola, null, $this->pessoa_logada, $this->ano, null, null, 1);
                if ($cod_calendario_ano_letivo = $obj_calend_ano_letivo->cadastra()) {
                    $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                    throw new HttpResponseException(
                        new RedirectResponse("educar_calendario_ano_letivo_lst.php?ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}")
                    );
                }

                $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

                return false;
            }
        }

        echo '<script> alert( \'Não foi possível definir as datas de início e fim do ano letivo.\' ) </script>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(620, $this->pessoa_logada, 7, 'educar_calendario_ano_letivo_lst.php');

        $obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
        $data_inicio = $obj_ano_letivo_modulo->menorData($this->ano, $this->ref_cod_escola);
        $data_fim = $obj_ano_letivo_modulo->maiorData($this->ano, $this->ref_cod_escola);

        if ($data_inicio && $data_fim) {
            $obj_calend_ano_letivo = new clsPmieducarCalendarioAnoLetivo($this->cod_calendario_ano_letivo, $this->ref_cod_escola, $this->pessoa_logada, null, $this->ano, null, null, 1/*, $data_inicio,$data_fim*/);
            if ($obj_calend_ano_letivo->edita()) {
                $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
                throw new HttpResponseException(
                    new RedirectResponse('educar_calendario_ano_letivo_lst.php')
                );
            }

            $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

            return false;
        }

        echo '<script> alert( \'Não foi possível definir as datas de início e fim do ano letivo.\' ) </script>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(620, $this->pessoa_logada, 7, 'educar_calendario_ano_letivo_lst.php');

        $obj = new clsPmieducarCalendarioAnoLetivo($this->cod_calendario_ano_letivo, $this->ref_cod_escola, $this->pessoa_logada, $this->pessoa_logada, $this->ano, $this->data_cadastra, $this->data_exclusao, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_ano_letivo_lst.php')
            );
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ .'/scripts/extra/educar-calendario-ano-letivo.js');
    }

    public function Formular()
    {
        $this->title = 'Calendario Ano Letivo';
        $this->processoAp = '620';
    }
};
