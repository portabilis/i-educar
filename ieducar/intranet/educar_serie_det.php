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
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
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
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - S&eacute;rie');
    $this->processoAp = '583';
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  var $titulo;

  var $cod_serie;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_curso;
  var $nm_serie;
  var $etapa_curso;
  var $concluinte;
  var $carga_horaria;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $regra_avaliacao_id;

  var $ref_cod_instituicao;

  function Gerar()
  {
    $this->titulo = 'S&eacute;rie - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg',
      'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $this->cod_serie=$_GET["cod_serie"];

    $tmp_obj = new clsPmieducarSerie( $this->cod_serie );
    $registro = $tmp_obj->detalhe();

    if (!$registro) {
        $this->simpleRedirect('educar_serie_lst.php');
    }

    $obj_ref_cod_curso = new clsPmieducarCurso( $registro['ref_cod_curso'] );
    $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
    $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];

    $registro['ref_cod_instituicao'] = $det_ref_cod_curso['ref_cod_instituicao'];
    $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
    $obj_instituicao_det = $obj_instituicao->detalhe();
    $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

    $obj_permissoes = new clsPermissoes();

    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if ($nivel_usuario == 1) {
      if ($registro['ref_cod_instituicao']) {
        $this->addDetalhe(array('Institui&ccedil;&atilde;o',
          $registro['ref_cod_instituicao']));
      }
    }

    if ( $registro['ref_cod_curso'] ) {
      $this->addDetalhe(array('Curso', $registro['ref_cod_curso']));
    }

    if ($registro['nm_serie']) {
      $this->addDetalhe(array('S&eacute;rie', $registro['nm_serie']));
    }

    if ($registro['etapa_curso']) {
      $this->addDetalhe(array('Etapa Curso', $registro['etapa_curso']));
    }

    $regras = $this->getRegrasAvaliacao();

    if ($regras) {
        $this->addDetalhe(['Regras de avaliação', $regras]);
    }

    if ($registro['concluinte']) {
      if ($registro['concluinte'] == 1) {
        $registro['concluinte'] = 'n&atilde;o';
      }
      else if ($registro['concluinte'] == 2) {
        $registro['concluinte'] = 'sim';
      }

      $this->addDetalhe(array('Concluinte', $registro['concluinte']));
    }

    if ($registro['carga_horaria']) {
      $this->addDetalhe(array('Carga Hor&aacute;ria', $registro['carga_horaria']));
    }

    $this->addDetalhe(array('Dias letivos', $registro['dias_letivos']));

    $this->addDetalhe(array('Idade padrão', $registro['idade_ideal']));

    if ($registro['observacao_historico']) {
      $this->addDetalhe(array('Observação histórico', $registro['observacao_historico']));
    }

    if ($obj_permissoes->permissao_cadastra(583, $this->pessoa_logada, 3)) {
      $this->url_novo = 'educar_serie_cad.php';
      $this->url_editar = "educar_serie_cad.php?cod_serie={$registro['cod_serie']}";
    }

    $this->url_cancelar = 'educar_serie_lst.php';
    $this->largura = '100%';

    $this->breadcrumb('Detalhe da série', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
  }

    public function getRegrasAvaliacao()
    {
        $query = <<<SQL
            SELECT
                ra.id,
                ra.nome,
                rasa.ano_letivo
            FROM
                modules.regra_avaliacao AS ra
            INNER JOIN
                modules.regra_avaliacao_serie_ano AS rasa ON rasa.regra_avaliacao_id = ra.id
            INNER JOIN
                pmieducar.serie AS s ON s.cod_serie = rasa.serie_id
            WHERE TRUE
                AND s.cod_serie = $1
            ORDER BY
                ra.id, rasa.ano_letivo
SQL;

        $regras = Portabilis_Utils_Database::fetchPreparedQuery($query, [
            'params' => [$this->cod_serie]
        ]);

        if (empty($regras)) {
            return '';
        }

        $retorno = [];

        foreach ($regras as $regra) {
            $regra['id'] = (int) $regra['id'];
            if (!isset($retorno[$regra['id']])) {
                $retorno[$regra['id']] = [
                    'nome' => $regra['nome'],
                    'anos' => [(int) $regra['ano_letivo']]
                ];
            } else {
                $retorno[$regra['id']]['anos'][] = (int) $regra['ano_letivo'];
            }
        }

        $html = [];

        foreach ($retorno as $r) {
            $html[] = sprintf('%s (%s)', $r['nome'], join(', ', $r['anos']));
        }

        return join('<br>', $html);
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
