<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

/**
 * clsIndexBase class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor Formação');
    $this->processoAp = 635;
  }
}

/**
 * indice class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $cod_formacao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_servidor;
  var $nm_formacao;
  var $tipo;
  var $descricao;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $passo;
  var $data_conclusao;
  var $data_registro;
  var $diplomas_registros;
  var $ref_cod_instituicao;
  var $data_vigencia_homolog;
  var $data_publicacao;
  var $cod_servidor_curso;
  var $cod_servidor_titulo;

  function Inicializar()
  {
    $retorno = '';

    

    $this->cod_formacao        = $_GET['cod_formacao'];
    $this->ref_cod_servidor    = $_GET['ref_cod_servidor'];
    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
    $this->passo               = $_POST['passo'];
    $this->tipo                = $_POST['tipo'];

    // URL para redirecionamento
    $backUrl = sprintf(
      'educar_servidor_formacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_cod_instituicao
    );

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $backUrl);

    if (is_string($this->passo) && $this->passo == 1) {
      $retorno = 'Novo';
    }

    if (is_numeric($this->cod_formacao)) {
      $obj = new clsPmieducarServidorFormacao($this->cod_formacao, NULL, NULL,
        $this->ref_cod_servidor, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

      $registro  = $obj->detalhe();

      if ($registro) {
        $this->nm_formacao = $registro['nm_formacao'];
        $this->tipo        = $registro['tipo'];
        $this->descricao   = $registro['descricao'];

        if ( $this->tipo == 'C' ) {
          $obj_curso                = new clsPmieducarServidorCurso(NULL, $this->cod_formacao);
          $det_curso                = $obj_curso->detalhe();
          $this->data_conclusao     = dataFromPgToBr($det_curso['data_conclusao']);
          $this->data_registro      = dataFromPgToBr($det_curso['data_registro']);
          $this->diplomas_registros = $det_curso['diplomas_registros'];
          $this->cod_servidor_curso = $det_curso['cod_servidor_curso'];
        }
        else {
          $obj_outros = new clsPmieducarServidorTituloConcurso(NULL, $this->cod_formacao);
          $det_outros = $obj_outros->detalhe();
          $this->data_vigencia_homolog = dataFromPgToBr($det_outros['data_vigencia_homolog']);
          $this->data_publicacao       = dataFromPgToBr($det_outros['data_publicacao']);
          $this->cod_servidor_titulo   = $det_outros['cod_servidor_titulo'];
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
          $this->fexcluir = TRUE;
        }

        $retorno     = 'Editar';
        $this->passo = 1;
      }
    }

    $this->url_cancelar = ($retorno == 'Editar') ?
      'educar_servidor_formacao_det.php?cod_formacao=' . $registro['cod_formacao'] :
      $backUrl;

    $this->nome_url_cancelar = 'Cancelar';

    return $retorno;
  }

  function Gerar()
  {
    if (! is_numeric($this->passo)) {
      $this->passo = 1;
      $this->campoOculto('passo', $this->passo);

      $opcoes = array(
        'C' => 'Cursos',
        'T' => 'Títulos',
        'O' => 'Concursos'
      );

      $this->campoLista('tipo', 'Tipo de Formação', $opcoes, $this->tipo);

      $this->acao_enviar = FALSE;

      $this->array_botao[] = 'Continuar';
      $this->array_botao_url_script[] = 'acao();';

      $this->url_cancelar = FALSE;

      $this->array_botao[] = 'Cancelar';
      $this->array_botao_url_script[] = sprintf(
        'go("educar_servidor_formacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d")',
        $this->ref_cod_servidor, $this->ref_cod_instituicao
      );
    }
    elseif (is_numeric($this->passo) && $this->passo == 1) {
      if ($this->tipo == 'C') {
        // Primary keys
        $this->campoOculto('cod_formacao', $this->cod_formacao);
        $this->campoOculto('tipo', $this->tipo);
        $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
        $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        $this->campoOculto('cod_servidor_curso', $this->cod_servidor_curso);

        $obrigatorio     = TRUE;
        $get_instituicao = TRUE;

        include 'include/pmieducar/educar_campo_lista.php';

        $this->campoRotulo('nm_tipo', 'Tipo de Formação', ($this->tipo == 'C') ? 'Curso' : 'Error');
        $this->campoTexto('nm_formacao', 'Nome do Curso', $this->nm_formacao, 30, 255, TRUE );

        // Foreign keys
        $nm_servidor = '';
        $objTemp    = new clsFuncionario( $this->ref_cod_servidor );
        $detalhe    = $objTemp->detalhe();

        if ($detalhe) {
          $objTmp = new clsPessoa_($detalhe['ref_cod_pessoa_fj']);
          $det    = $objTmp->detalhe();

          if ($det) {
            $nm_servidor = $det['nome'];
          }
        }

        $this->campoMemo('descricao', 'Descricão', $this->descricao, 60, 5, FALSE);

        $this->campoRotulo('nm_servidor', 'Nome do Servidor', $nm_servidor);

        $this->campoData('data_conclusao', 'Data de Conclusão', $this->data_conclusao, TRUE);

        $this->campoData('data_registro', 'Data de Registro', $this->data_registro);

        $this->campoMemo('diplomas_registros', 'Diplomas e Registros',
          $this->diplomas_registros, 60, 5, FALSE);
      }
      elseif ( $this->tipo == 'T' ) {
        // Primary keys
        $this->campoOculto('cod_formacao', $this->cod_formacao);
        $this->campoOculto('tipo', $this->tipo);
        $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
        $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        $this->campoOculto('cod_servidor_titulo', $this->cod_servidor_titulo);

        $obrigatorio     = TRUE;
        $get_instituicao = TRUE;

        include 'include/pmieducar/educar_campo_lista.php';

        $this->campoRotulo('nm_tipo', 'Tipo de Formação', ($this->tipo == 'T') ? 'Título' : 'Error');
        $this->campoTexto('nm_formacao', 'Nome do Título', $this->nm_formacao, 30, 255, TRUE);

        // Foreign keys
        $nm_servidor = '';
        $objTemp     = new clsFuncionario($this->ref_cod_servidor);
        $detalhe     = $objTemp->detalhe();

        if ($detalhe) {
          $objTmp = new clsPessoa_($detalhe['ref_cod_pessoa_fj']);
          $det    = $objTmp->detalhe();

          if ($det) {
            $nm_servidor = $det['nome'];
          }
        }

        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, FALSE);

        $this->campoRotulo('nm_servidor', 'Nome do Servidor', $nm_servidor);

        $this->campoData('data_vigencia_homolog', 'Data de Vigência', $this->data_vigencia_homolog, TRUE);

        $this->campoData('data_publicacao', 'Data de Publicação', $this->data_publicacao, TRUE);
      }
      elseif ( $this->tipo == 'O' ) {
        // Primary keys
        $this->campoOculto('cod_formacao', $this->cod_formacao);
        $this->campoOculto('tipo', $this->tipo);
        $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
        $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        $this->campoOculto('cod_servidor_titulo', $this->cod_servidor_titulo);

        $obrigatorio     = TRUE;
        $get_instituicao = TRUE;

        include 'include/pmieducar/educar_campo_lista.php';

        $this->campoRotulo('nm_tipo', 'Tipo de Formação', ($this->tipo == 'O') ? 'Formação' : 'Error');
        $this->campoTexto('nm_formacao', 'Nome do Concurso', $this->nm_formacao, 30, 255, TRUE);

        // Foreign keys
        $nm_servidor = '';
        $objTemp     = new clsFuncionario($this->ref_cod_servidor);
        $detalhe     = $objTemp->detalhe();

        if ($detalhe) {
          $objTmp = new clsPessoa_($detalhe['ref_cod_pessoa_fj']);
          $det    = $objTmp->detalhe();

          if ($det) {
            $nm_servidor = $det['nome'];
          }
        }
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, FALSE);

        $this->campoRotulo('nm_servidor', 'Nome do Servidor', $nm_servidor);

        $this->campoData('data_vigencia_homolog', 'Data de Homologação', $this->data_vigencia_homolog, TRUE);

        $this->campoData('data_publicacao', 'Data de Publicação', $this->data_publicacao, TRUE);
      }
    }
  }

  function Novo()
  {
    

    $backUrl = sprintf(
      'educar_servidor_formacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_cod_instituicao
    );

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $backUrl);

    $obj = new clsPmieducarServidorFormacao(NULL, NULL, $this->pessoa_logada,
      $this->ref_cod_servidor, $this->nm_formacao, $this->tipo, $this->descricao,
      NULL, NULL, $this->ativo, $this->ref_cod_instituicao);

    $cadastrou = $obj->cadastra();
    if ($cadastrou) {
      if ($this->tipo == 'C') {

        $obj = new clsPmieducarServidorCurso(NULL, $cadastrou, dataToBanco($this->data_conclusao),
          dataToBanco($this->data_registro), $this->diplomas_registros);

        if ($obj->cadastra()) {
          $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
          $this->simpleRedirect($backUrl);
        }
      }
      elseif ( $this->tipo == 'T' || $this->tipo == 'O' ) {
        $obj = new clsPmieducarServidorTituloConcurso(NULL, $cadastrou,
          dataToBanco($this->data_vigencia_homolog), dataToBanco($this->data_publicacao));

        if ($obj->cadastra()) {
          $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
          $this->simpleRedirect($backUrl);
        }
      }
    }

    $this->mensagem = 'Cadastro não realizado.<br>';
    return FALSE;
  }

  function Editar()
  {
    

    $backUrl = sprintf(
      'educar_servidor_formacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_cod_instituicao
    );

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $backUrl);

    $obj = new clsPmieducarServidorFormacao($this->cod_formacao, $this->pessoa_logada,
      NULL, $this->ref_cod_servidor, $this->nm_formacao, $this->tipo, $this->descricao,
      NULL, NULL, 1);

    $editou = $obj->edita();

    if ($editou) {
      if ($this->tipo == 'C') {
        $obj_curso  = new clsPmieducarServidorCurso($this->cod_servidor_curso,
          $this->cod_formacao, dataToBanco($this->data_conclusao),
          dataToBanco($this->data_registro), $this->diplomas_registros
        );

        $editou_cur = $obj_curso->edita();

        if ($editou_cur) {
          $this->mensagem .= 'Edição efetuada com sucesso.<br>';
          $this->simpleRedirect($backUrl);
        }
      }
      else {
        $obj_titulo = new clsPmieducarServidorTituloConcurso($this->cod_servidor_titulo,
          $this->cod_formacao, dataToBanco($this->data_vigencia_homolog),
          dataToBanco($this->data_publicacao));

        $editou_tit = $obj_titulo->edita();

        if ($editou_tit) {
          $this->mensagem .= 'Edição efetuada com sucesso.<br>';
          $this->simpleRedirect($backUrl);
        }
      }
    }

    $this->mensagem = "Edição não realizada.<br>";
    return FALSE;
  }

  function Excluir()
  {
    

    $backUrl = sprintf(
      'educar_servidor_formacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_cod_instituicao
    );

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7, $backUrl);

    $obj = new clsPmieducarServidorFormacao($this->cod_formacao, $this->pessoa_logada,
      NULL, $this->ref_cod_servidor, $this->nm_formacao, $this->tipo, $this->descricao,
      NULL, NULL, 0, $this->ref_cod_instituicao);

    $excluiu = $obj->excluir();

    if ($excluiu) {
      $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
      $this->simpleRedirect($backUrl);
    }

    $this->mensagem = 'Exclusão não realizada.<br>';
    return FALSE;
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
