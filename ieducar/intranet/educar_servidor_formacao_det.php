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
require_once 'include/clsDetalhe.inc.php';
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
class indice extends clsDetalhe
{
  var $titulo;

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

  function Gerar()
  {
    $this->titulo = 'Servidor Formacao - Detalhe';


    $this->cod_formacao = $_GET['cod_formacao'];

    $tmp_obj = new clsPmieducarServidorFormacao($this->cod_formacao);
    $registro = $tmp_obj->detalhe();

    if (! $registro) {
        $this->simpleRedirect('educar_servidor_formacao_lst.php');
    }

      $obj_ref_cod_servidor = new clsPmieducarServidor($registro['ref_cod_servidor'],
        null, null, null, null, null, 1, $registro['ref_ref_cod_instituicao']
      );

      $det_ref_cod_servidor = $obj_ref_cod_servidor->detalhe();
      $registro['ref_cod_servidor'] = $det_ref_cod_servidor['cod_servidor'];

    if ($registro['nm_formacao']) {
      $this->addDetalhe(array('Nome Formação', $registro['nm_formacao']));
    }

    if ($registro['tipo'] == 'C') {
      $obj_curso = new clsPmieducarServidorCurso( null, $this->cod_formacao );
      $det_curso = $obj_curso->detalhe();
    }
    elseif ($registro['tipo'] == 'T' || $registro['tipo'] == 'O') {
      $obj_titulo = new clsPmieducarServidorTituloConcurso(NULL, $this->cod_formacao);
      $det_titulo = $obj_titulo->detalhe();
    }

    if ($registro['tipo']) {
      if ($registro['tipo'] == 'C') {
        $registro['tipo'] = 'Curso';
      }
      elseif ($registro['tipo'] == 'T') {
        $registro['tipo'] = 'T&iacute;tulo';
      }
      else {
        $registro['tipo'] = 'Concurso';
      }

      $this->addDetalhe(array('Tipo', $registro['tipo']));
    }

    if ($registro['descricao']) {
      $this->addDetalhe(array('Descricção', $registro['descricao']));
    }

    if ($det_curso['data_conclusao']) {
      $this->addDetalhe(array('Data de Conclusão', dataFromPgToBr($det_curso['data_conclusao'])));
    }

    if ($det_curso['data_registro']) {
      $this->addDetalhe(array('Data de Registro', dataFromPgToBr($det_curso['data_registro'])));
    }

    if ($det_curso['diplomas_registros']) {
      $this->addDetalhe(array('Diplomas e Registros', $det_curso['diplomas_registros']));
    }

    if ($det_titulo['data_vigencia_homolog'] && $registro['tipo'] == 'Título') {
      $this->addDetalhe(array('Data de Vigência', dataFromPgToBr($det_titulo['data_vigencia_homolog'])));
    }
    elseif ($det_titulo['data_vigencia_homolog'] && $registro['tipo'] == 'Concurso') {
      $this->addDetalhe(array('Data de Homologação', dataFromPgToBr($det_titulo['data_vigencia_homolog'])));
    }

    if ($det_titulo['data_publicacao']) {
      $this->addDetalhe(array('Data de Publicação', dataFromPgToBr($det_titulo['data_publicacao'])));
    }

    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
      $this->url_novo = 'educar_servidor_formacao_cad.php';

      $this->url_editar = sprintf(
        'educar_servidor_formacao_cad.php?cod_formacao=%d&ref_cod_instituicao=%d&ref_cod_servidor=%d',
        $registro['cod_formacao'], $registro['ref_ref_cod_instituicao'], $registro['ref_cod_servidor']
      );
    }

    $this->url_cancelar = sprintf(
      'educar_servidor_formacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
      $registro['ref_cod_servidor'], $registro['ref_ref_cod_instituicao']
    );

    $this->largura = '100%';
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
