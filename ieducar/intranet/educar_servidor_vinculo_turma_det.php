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
 * @author    Lucas Schmoeller das Silva <lucas@portabilis.com.br>
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
require_once 'include/modules/clsModulesProfessorTurma.inc.php';

/**
 * clsIndexBase class.
 *
 * @author    Lucas Schmoeller das Silva <lucas@portabilis.com.br>
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
    $this->SetTitulo($this->_instituicao . ' Servidores - Servidor Formação');
    $this->processoAp = 635;
  }
}

/**
 * indice class.
 *
 * @author    Lucas Schmoeller das Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  var $titulo;

  var $id;
  var $ano;
  var $servidor_id;
  var $funcao_exercida;
  var $tipo_vinculo;

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_curso;
  var $ref_cod_serie;
  var $ref_cod_turma;

  function Gerar()
  {
    $this->titulo = 'Servidor Vínculo Turma - Detalhe';


    $this->id = $_GET['id'];

    $tmp_obj = new clsModulesProfessorTurma($this->id);
    $registro = $tmp_obj->detalhe();

    if (! $registro) {
        $this->simpleRedirect('educar_servidor_professor_vinculo_lst.php');
    }

    $resources_funcao = array(  null => 'Selecione',
                                1    => 'Docente',
                                2    => 'Auxiliar/Assistente educacional',
                                3    => 'Profissional/Monitor de atividade complementar',
                                4    => 'Tradutor Intérprete de LIBRAS',
                                5    => 'Docente titular - Coordenador de tutoria (de módulo ou disciplina) - EAD',
                                6    => 'Docente tutor - Auxiliar (de módulo ou disciplina) - EAD');

    $resources_tipo = array(  null => 'Selecione',
                              1    => 'Concursado/efetivo/estável',
                              2    => 'Contrato temporário',
                              3    => 'Contrato terceirizado',
                              4    => 'Contrato CLT');

    if ($registro['nm_escola']) {
      $this->addDetalhe(array('Escola', $registro['nm_escola']));
    }

    if ($registro['nm_curso']) {
      $this->addDetalhe(array('Curso', $registro['nm_curso']));
    }

    if ($registro['nm_serie']) {
      $this->addDetalhe(array('Série', $registro['nm_serie']));
    }

    if ($registro['nm_turma']) {
      $this->addDetalhe(array('Turma', $registro['nm_turma']));
    }

    if ($registro['funcao_exercida']) {
      $this->addDetalhe(array('Função exercida', $resources_funcao[$registro['funcao_exercida']]));
    }

    if ($registro['tipo_vinculo']) {
      $this->addDetalhe(array('Tipo de vínculo', $resources_tipo[$registro['tipo_vinculo']]));
    }

    $sql = 'SELECT nome
            FROM modules.professor_turma_disciplina
            INNER JOIN modules.componente_curricular cc ON (cc.id = componente_curricular_id)
            WHERE professor_turma_id = $1
            ORDER BY nome';

    $disciplinas = '';

    $resources = Portabilis_Utils_Database::fetchPreparedQuery($sql, array( 'params' => array($this->id) ));

    foreach ($resources as $reg) {
        $disciplinas .= '<span style="background-color: #ccdce6; padding: 2px; border-radius: 3px;"><b>'.$reg['nome'].'</b></span> ';
    }

    if ($disciplinas != '') {
      $this->addDetalhe(array('Disciplinas', $disciplinas));
    }

    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
      $this->url_novo = sprintf(
        'educar_servidor_vinculo_turma_cad.php?ref_cod_instituicao=%d&ref_cod_servidor=%d',
        $registro['instituicao_id'], $registro['servidor_id']
      );

      $this->url_editar = sprintf(
        'educar_servidor_vinculo_turma_cad.php?id=%d&ref_cod_instituicao=%d&ref_cod_servidor=%d',
        $registro['id'], $registro['instituicao_id'], $registro['servidor_id']
      );

      $this->array_botao[] = 'Copiar vínculo';
      $this->array_botao_url_script[] = sprintf(
        'go("educar_servidor_vinculo_turma_cad.php?id=%d&ref_cod_instituicao=%d&ref_cod_servidor=%d&copia");',
        $registro['id'], $registro['instituicao_id'], $registro['servidor_id']
      );

      "go(\"educar_servidor_vinculo_turma_copia_cad.php?{$get_padrao}\");";
    }

    $this->url_cancelar = sprintf(
      'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
      $registro['servidor_id'], $registro['instituicao_id']
    );

    $this->largura = '100%';

    $this->breadcrumb('Detalhe do vínculo', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);
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
