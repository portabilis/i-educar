<?php
/**
 *
 * @author  Prefeitura Municipal de Itajaí
 * @version $Id$
 *
 * Pacote: i-PLB Software Público Livre e Brasileiro
 *
 * Copyright (C) 2006 PMI - Prefeitura Municipal de Itajaí
 *            ctima@itajai.sc.gov.br
 *
 * Este  programa  é  software livre, você pode redistribuí-lo e/ou
 * modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 * publicada pela Free  Software  Foundation,  tanto  a versão 2 da
 * Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.
 *
 * Este programa  é distribuído na expectativa de ser útil, mas SEM
 * QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-
 * ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-
 * sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.
 *
 * Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU
 * junto  com  este  programa. Se não, escreva para a Free Software
 * Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA
 * 02111-1307, USA.
 *
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';


class clsIndexBase extends clsBase {

  public function Formular() {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor Afastamento');
    $this->processoAp = '635';
  }

}


class indice extends clsCadastro {

  /**
   * Referência a usuário da sessão
   *
   * @var int
   */
  public $pessoa_logada = NULL;

  public
    $ref_cod_servidor           = NULL,
    $sequencial                 = NULL,
    $ref_cod_instituicao        = NULL,
    $ref_cod_motivo_afastamento = NULL,
    $ref_usuario_exc            = NULL,
    $ref_usuario_cad            = NULL,
    $data_cadastro              = NULL,
    $data_exclusao              = NULL,
    $data_retorno               = NULL,
    $data_saida                 = NULL,
    $ativo                      = NULL,
    $status                     = NULL,
    $alocacao_array             = NULL,
    $parametros                 = NULL;

  /**
   * Array dos dias da semana
   *
   * @var Array
   */
  public $dias_da_semana = array(
    '' => 'Selecione',
    1  => 'Domingo',
    2  => 'Segunda',
    3  => 'Ter&ccedil;a',
    4  => 'Quarta',
    5  => 'Quinta',
    6  => 'Sexta',
    7  => 'S&aacute;bado'
    );



  public function Inicializar() {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $retorno = "Novo";
    $this->status = "N";

    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
    $this->ref_cod_servidor    = $_GET['ref_cod_servidor'];
    $this->sequencial          = $_GET['sequencial'];

    $urlPermite = sprintf('educar_servidor_det.php?cod_servidor=%s&ref_cod_instituicao=%s',
      $this->ref_cod_servidor, $this->ref_cod_instituicao);

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $urlPemite);

    if (is_numeric($this->ref_cod_servidor) && is_numeric($this->sequencial) &&
        is_numeric($this->ref_cod_instituicao)) {

      $obj = new clsPmieducarServidorAfastamento(
        $this->ref_cod_servidor, $this->sequencial, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

      $registro = $obj->detalhe();

      if ($registro) {

        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

        if ($this->data_retorno) {
          $this->data_retorno = dataFromPgToBr($this->data_retorno);
        }

        if ($this->data_saida) {
          $this->data_saida   = dataFromPgToBr($this->data_saida);
        }

        $retorno = "Editar";
        $this->status = "E";
      }
    }

    $this->url_cancelar = sprintf(
      'educar_servidor_det.php?cod_servidor=%s&ref_cod_instituicao=%s',
      $this->ref_cod_servidor, $this->ref_cod_instituicao);

    $this->nome_url_cancelar = "Cancelar";

    return $retorno;
  }



  public function Gerar() {
    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
    $this->campoOculto('sequencial', $this->sequencial);
    $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);

    $opcoes = array('' => 'Selecione');

    $objTemp = new clsPmieducarMotivoAfastamento();
    $lista = $objTemp->lista();

    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $opcoes[$registro['cod_motivo_afastamento']] = $registro['nm_motivo'];
      }
    }
    else {
      $opcoes = array('' => 'Erro na geracao');
    }

    if ($this->status == 'N') {
      $this->campoLista('ref_cod_motivo_afastamento', 'Motivo Afastamento',
        $opcoes, $this->ref_cod_motivo_afastamento);
    }
    elseif ($this->status == 'E') {
      $this->campoLista('ref_cod_motivo_afastamento', 'Motivo Afastamento',
        $opcoes, $this->ref_cod_motivo_afastamento, '', FALSE, '', '', TRUE);
    }

    // data
    if ($this->status == 'N') {
      $this->campoData('data_saida', 'Data de Afastamento', $this->data_saida, TRUE);
    }
    elseif ($this->status == 'E') {
      $this->campoRotulo('data_saida', 'Data de Afastamento', $this->data_saida);
    }

    if ($this->status == 'E') {
      $this->campoData('data_retorno', 'Data de Retorno', $this->data_retorno, FALSE);
    }

    if (class_exists('clsPmieducarServidor')) {
      $obj_servidor = new clsPmieducarServidor($this->ref_cod_servidor,
        NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

      $det_servidor = $obj_servidor->detalhe();

      if ($det_servidor) {

        if (class_exists('clsPmieducarFuncao')) {
          $obj_funcao = new clsPmieducarFuncao($det_servidor['ref_cod_funcao'],
            NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

          $det_funcao = $obj_funcao->detalhe();

          if ($det_funcao['professor'] == 1) {
            $obj = new clsPmieducarQuadroHorarioHorarios();
            $lista = $obj->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL,
              $this->ref_cod_instituicao, NULL, $this->ref_cod_servidor, NULL,
              NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL);

            if ($lista) {

              // Passa todos os valores obtidos no registro para atributos do objeto
              foreach ($lista as $campo => $val) {
                $temp = array();
                $temp['hora_inicial']       = $val['hora_inicial'];
                $temp['hora_final']         = $val['hora_final'];
                $temp['dia_semana']         = $val['dia_semana'];
                $temp['ref_cod_escola']     = $val['ref_cod_escola'];
                $temp['ref_cod_substituto'] = $val['ref_servidor_substituto'];
                $this->alocacao_array[]     = $temp;
              }

              if ($this->alocacao_array) {
                $tamanho = sizeof($alocacao);
                $script  = "<script>\nvar num_alocacao = {$tamanho};\n";
                $script .= "var array_servidores = Array();\n";

                foreach ($this->alocacao_array as $key => $alocacao) {
                  $script .= "array_servidores[{$key}] = new Array();\n";

                  $hora_ini = explode(":", $alocacao['hora_inicial']);
                  $hora_fim = explode(":", $alocacao['hora_final']);

                  $horas_utilizadas   = ($hora_fim[0] - $hora_ini[0]);
                  $minutos_utilizados = ($hora_fim[1] - $hora_ini[1]);

                  $horas   = sprintf('%02d', (int) $horas_utilizadas);
                  $minutos = sprintf('%02d', (int) $minutos_utilizados);

                  $str_horas_utilizadas = "{$horas}:{$minutos}";

                  $script .= "array_servidores[{$key}][0] = '{$str_horas_utilizadas}';\n";
                  $script .= "array_servidores[{$key}][1] = '';\n\n";

                  $obj_escola    = new clsPmieducarEscola($alocacao['ref_cod_escola']);
                  $det_escola    = $obj_escola->detalhe();
                  $det_escola    = $det_escola['nome'];
                  $nm_dia_semana = $this->dias_da_semana[$alocacao['dia_semana']];

                  $obj_subst = new clsPessoa_($alocacao['ref_cod_substituto']);
                  $det_subst = $obj_subst->detalhe();

                  if ($this->status == 'N') {
                    $this->campoTextoInv("dia_semana_{$key}_", '', $nm_dia_semana,
                      8, 8, FALSE, FALSE, TRUE, '', '', '', '', 'dia_semana');

                    $this->campoTextoInv("hora_inicial_{$key}_", '', $alocacao['hora_inicial'],
                      5, 5, FALSE, FALSE, TRUE, '', '', '', '', 'ds_hora_inicial_');

                    $this->campoTextoInv("hora_final_{$key}_", '', $alocacao['hora_final'],
                      5, 5, FALSE, FALSE, TRUE, '', '', '', '', 'ds_hora_final_');

                    $this->campoTextoInv("ref_cod_escola_{$key}", '', $det_escola,
                      30, 255, FALSE, FALSE, TRUE, '', '', '', '', 'ref_cod_escola_');

                    $this->campoTextoInv("ref_cod_servidor_substituto_{$key}_",
                      '', $det_subst['nome'], 30, 255, FALSE, FALSE, FALSE, '',
                      "<span name=\"ref_cod_servidor_substituto\" id=\"ref_cod_servidor_substituicao_{$key}\"><img border='0'  onclick=\"pesquisa_valores_popless('educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor_substituto[{$key}]&campo2=ref_cod_servidor_substituto_{$key}_&ref_cod_instituicao={$this->ref_cod_instituicao}&dia_semana={$alocacao["dia_semana"]}&hora_inicial={$alocacao["hora_inicial"]}&hora_final={$alocacao["hora_final"]}&ref_cod_servidor={$this->ref_cod_servidor}&professor=1&ref_cod_escola={$alocacao['ref_cod_escola']}&horario=S', 'nome')\" src=\"imagens/lupa.png\" ></span>",
                      '', '', 'ref_cod_servidor_substituto');
                  }

                  $this->campoOculto("dia_semana_{$key}", $alocacao['dia_semana']);
                  $this->campoOculto("hora_inicial_{$key}", $alocacao['hora_inicial']);
                  $this->campoOculto("hora_final_{$key}", $alocacao['hora_final']);
                  $this->campoOculto("ref_cod_escola_{$key}", $alocacao['ref_cod_escola']);
                  $this->campoOculto("ref_cod_servidor_substituto[{$key}]", $alocacao['ref_cod_substituto']);

                }

                $script .= "\n</script>";

                // Print do Javascript
                print $script;
              }
            }
          }
        }

      }
    }
  }



  public function Novo() {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->ref_cod_servidor = isset($_POST['ref_cod_servidor']) ?
      $_POST['ref_cod_servidor'] : NULL;

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}");

    $obj = new clsPmieducarServidorAfastamento($this->ref_cod_servidor, NULL,
      $this->ref_cod_motivo_afastamento, NULL, $this->pessoa_logada, NULL, NULL,
      $this->data_retorno, $this->data_saida, 1, $this->ref_cod_instituicao);

    $cadastrou = $obj->cadastra();
    if ($cadastrou) {

      if(is_array($_POST['ref_cod_servidor_substituto']))
      foreach ( $_POST['ref_cod_servidor_substituto'] as $key => $valor )
      {
        //if ( substr( $campo, 0, 28 ) == 'ref_cod_servidor_substituto_' )
        //	{
        $ref_cod_servidor_substituto = $valor;
        //	}
        //if ( substr( $campo, 0, 15 ) == 'ref_cod_escola_' )
        $ref_cod_escola = $_POST["ref_cod_escola_{$key}"];
        //if ( substr( $campo, 0, 11 ) == 'dia_semana_' )
        $dia_semana = $_POST["dia_semana_{$key}"];
        //if ( substr( $campo, 0, 13 ) == 'hora_inicial_' )
        $hora_inicial = urldecode( $_POST["hora_inicial_{$key}"] );
        //	if ( substr( $campo, 0, 11 ) == 'hora_final_' )
        $hora_final = urldecode( $_POST["hora_final_{$key}"] );

        if ( is_numeric( $ref_cod_servidor_substituto ) && is_numeric( $ref_cod_escola ) && is_numeric( $dia_semana ) && is_string( $hora_inicial ) && is_string( $hora_final ) )
        {

          //if ( substr( $campo, 0, 28 ) == 'ref_cod_servidor_substituto_' )
          //{die;
          $obj_horarios = new clsPmieducarQuadroHorarioHorarios( null, null, $ref_cod_escola,null, null,null, $this->ref_cod_instituicao,$ref_cod_servidor_substituto, $this->ref_cod_servidor, $hora_inicial, $hora_final, null, null, 1, $dia_semana );
          $det_horarios = $obj_horarios->detalhe($ref_cod_escola);
          //echo " = new clsPmieducarQuadroHorarioHorarios( {$det_horarios["ref_cod_quadro_horario"]}, {$det_horarios["ref_cod_serie"]}, {$det_horarios["ref_cod_escola"]}, {$det_horarios["ref_cod_disciplina"]}, {$det_horarios["ref_ref_cod_turma"]}, {$det_horarios["sequencial"]}, {$det_horarios["ref_cod_instituicao_servidor"]}, null, {$ref_cod_servidor_substituto}, null, null, null, null, null, null, null );";die;
          $obj_horario = new clsPmieducarQuadroHorarioHorarios( $det_horarios["ref_cod_quadro_horario"], $det_horarios["ref_cod_serie"], $det_horarios["ref_cod_escola"], $det_horarios["ref_cod_disciplina"], $det_horarios["sequencial"], $det_horarios["ref_cod_instituicao_servidor"],$det_horarios["ref_cod_instituicao_servidor"], $ref_cod_servidor_substituto, $this->ref_cod_servidor, null, null, null, null, null, null );
          if( !$obj_horario->edita() )
          {
            $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
            return false;
          }
          //}
          }
        }
        $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
        header( "Location: educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
        die();
        return true;
        }
        else
        {
          $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
          return false;
        }
        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar clsPmieducarServidorAfastamento\nvalores obrigatorios\nis_numeric( $this->ref_cod_servidor ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_cod_motivo_afastamento ) && is_numeric( $this->ref_usuario_cad ) && is_string( $this->data_saida )\n-->";
        return false;
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7,  "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );

    $obj = new clsPmieducarServidorAfastamento( $this->ref_cod_servidor, $this->sequencial, $this->ref_cod_motivo_afastamento, $this->pessoa_logada, null, null, null, $this->data_retorno, unserialize( $this->data_saida ), 1, $this->ref_cod_instituicao );
    $editou = $obj->edita();
    if( $editou )
    {
      if(is_array($_POST['ref_cod_servidor_substituto']))
      foreach ( $_POST['ref_cod_servidor_substituto'] as $key => $valor )
      {
        $ref_cod_servidor_substituto = $valor;

        //if ( substr( $campo, 0, 15 ) == 'ref_cod_escola_' )
        $ref_cod_escola = $_POST["ref_cod_escola_{$key}"];
        //if ( substr( $campo, 0, 11 ) == 'dia_semana_' )
        $dia_semana = $_POST["dia_semana_{$key}"];
        //if ( substr( $campo, 0, 13 ) == 'hora_inicial_' )
        $hora_inicial = urldecode( $_POST["hora_inicial_{$key}"] );
        //	if ( substr( $campo, 0, 11 ) == 'hora_final_' )
        $hora_final = urldecode( $_POST["hora_final_{$key}"] );

        if ( is_numeric( $ref_cod_servidor_substituto ) && is_numeric( $ref_cod_escola ) && is_numeric( $dia_semana ) && is_string( $hora_inicial ) && is_string( $hora_final ) )
        {
          //if ( substr( $campo, 0, 28 ) == 'ref_cod_servidor_substituto_' )
          //{
          $obj_horarios = new clsPmieducarQuadroHorarioHorarios( null, null, $ref_cod_escola,null, null,null, $this->ref_cod_instituicao,$ref_cod_servidor_substituto, $this->ref_cod_servidor, $hora_inicial, $hora_final, null, null, 1, $dia_semana );
          $det_horarios = $obj_horarios->detalhe($ref_cod_escola);
          //if ( is_string( $this->data_retorno ) && $this->data_retorno != '' )
          //{
          //$obj_horario = new clsPmieducarQuadroHorarioHorarios( $det_horarios["ref_cod_quadro_horario"], $det_horarios["ref_ref_cod_serie"], $det_horarios["ref_ref_cod_escola"], $det_horarios["ref_ref_cod_disciplina"], $det_horarios["ref_ref_cod_turma"], $det_horarios["sequencial"], null, null, null, null, null, null, null, null, null, null );
            $obj_horario = new clsPmieducarQuadroHorarioHorarios( $det_horarios["ref_cod_quadro_horario"], $det_horarios["ref_cod_serie"], $det_horarios["ref_cod_escola"], $det_horarios["ref_cod_disciplina"], $det_horarios["sequencial"], null,$det_horarios["ref_cod_instituicao_servidor"], null, $this->ref_cod_servidor, null, null, null, null, null, null );
            //}
            /*	else
            {
            //							$obj_horario = new clsPmieducarQuadroHorarioHorarios( $det_horarios["ref_cod_quadro_horario"], $det_horarios["ref_ref_cod_serie"], $det_horarios["ref_ref_cod_escola"], $det_horarios["ref_ref_cod_disciplina"], $det_horarios["ref_ref_cod_turma"], $det_horarios["sequencial"], $det_horarios["ref_cod_instituicao_servidor"], null, $ref_cod_servidor_substituto, null, null, null, null, null, null, null );
            $obj_horario = new clsPmieducarQuadroHorarioHorarios( $det_horarios["ref_cod_quadro_horario"], $det_horarios["ref_cod_serie"], $det_horarios["ref_cod_escola"], $det_horarios["ref_cod_disciplina"], $det_horarios["sequencial"], $det_horarios["ref_cod_instituicao_servidor"],$det_horarios["ref_cod_instituicao_servidor"], $ref_cod_servidor_substituto, $this->ref_cod_servidor, null, null, null, null, null, null );
            }*/
            if( !$obj_horario->edita() )
            {
              $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
              return false;
            }
            //}
        }
      }
      $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
      header( "Location: educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
      die();
      return true;
    }

    $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
    echo "<!--\nErro ao editar clsPmieducarServidorAfastamento\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
    return false;
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 7,  "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );


    $obj = new clsPmieducarServidorAfastamento($this->ref_cod_servidor, $this->sequencial, $this->ref_ref_cod_instituicao, $this->ref_cod_motivo_afastamento, $this->pessoa_logada, $this->pessoa_logada, $this->data_cadastro, $this->data_exclusao, $this->data_retorno, $this->data_saida, 0);
    $excluiu = $obj->excluir();
    if( $excluiu )
    {
      $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
      header( "Location: educar_servidor_afastamento_lst.php" );
      die();
      return true;
    }

    $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
    echo "<!--\nErro ao excluir clsPmieducarServidorAfastamento\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
    return false;
  }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
<script>
	if ( document.getElementById( 'btn_enviar' ) )
	{
		document.getElementById( 'btn_enviar' ).onclick = function() { validaFormulario(); }
	}
	function validaFormulario()
	{
		var c    = 0;
		var loop = true;
		do
		{
			if ( document.getElementById( 'ref_cod_servidor_substituto_' + c + '_' ) && document.getElementById( 'ref_cod_servidor_substituto_' + c ) )
			{
				if ( document.getElementById( 'ref_cod_servidor_substituto_' + c + '_' ).value == '' && document.getElementById( 'ref_cod_servidor_substituto_' + c ).value == '' )
				{
					alert( 'Você deve informar um substituto para cada horário.' );
					return;
				}
			}
			else
			{
				loop = false;
			}
			c++;
		} while ( loop );
		acao();
	}
</script>