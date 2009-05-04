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
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase {

  public function Formular() {
    $this->SetTitulo( "{$this->_instituicao} i-Educar - Falta Atraso" );
    $this->processoAp = "635";
  }

}


class indice extends clsListagem
{
  /**
   * Referencia pega da session para o idpes do usuario atual
   *
   * @var int
   */
  public $pessoa_logada = 0;

 /**
   * Titulo no topo da pagina
   *
   * @var int
   */
  public $titulo = '';

  /**
   * Quantidade de registros a ser apresentada em cada pagina
   *
   * @var int
   */
  public $limite = 0;

  /**
   * Inicio dos registros a serem exibidos (limit)
   *
   * @var int
   */
  public $offset = 0;

  public
    $cod_falta_atraso        = NULL,
    $ref_cod_escola          = NULL,
    $ref_ref_cod_instituicao = NULL,
    $ref_usuario_exc         = NULL,
    $ref_usuario_cad         = NULL,
    $ref_cod_servidor        = NULL,
    $tipo                    = NULL,
    $data_falta_atraso       = NULL,
    $qtd_horas               = NULL,
    $qtd_min                 = NULL,
    $justificada             = NULL,
    $data_cadastro           = NULL,
    $data_exclusao           = NULL,
    $ativo                   = NULL;



  public function Gerar() {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->ref_cod_servidor        = isset($_GET['ref_cod_servidor']) ?
      $_GET['ref_cod_servidor'] : NULL;
    $this->ref_ref_cod_instituicao = isset($_GET['ref_cod_instituicao']) ?
      $_GET['ref_cod_instituicao'] : NULL;

    $this->titulo = 'Faltas e atrasos - Listagem';

    foreach ($_GET as $var => $val) {
      $this->$var = ($val === "") ? NULL : $val;
    }

    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $this->addCabecalhos(array(
      'Escola',
      'Instituic&atilde;o',
      'Tipo',
      'Horas',
      'Minutos'
    ));

    // Filtros de Foreign Keys
    $obrigatorio     = FALSE;
    $get_instituicao = TRUE;
    $get_escola      = TRUE;
    include_once 'include/pmieducar/educar_campo_lista.php';

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite-$this->limite : 0;

    $obj_falta_atraso = new clsPmieducarFaltaAtraso(NULL, $this->ref_cod_escola,
      $this->ref_ref_cod_instituicao, NULL, NULL, $this->ref_cod_servidor);

    $obj_falta_atraso->setOrderby('tipo ASC');
    $obj_falta_atraso->setLimite($this->limite, $this->offset);

    // Recupera a lista de faltas/atrasos
    $lista = $obj_falta_atraso->lista(NULL, NULL, NULL, NULL, NULL, $this->ref_cod_servidor);

    $total = $obj_falta_atraso->_total;

    // monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {

        // Recupera o nome da escola
        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $registro['nm_escola'] = $det_ref_cod_escola['nome'];

        $obj_ins = new clsPmieducarInstituicao($registro['ref_ref_cod_instituicao']);
        $det_ins = $obj_ins->detalhe();

        $obj_comp = new clsPmieducarFaltaAtrasoCompensado();
        $horas    = $obj_comp->ServidorHorasCompensadas($this->ref_cod_servidor,
          $registro["ref_cod_escola"], $registro["ref_ref_cod_instituicao"]);

        if ($horas) {
          $horas_aux   = $horas["hora"];
          $minutos_aux = $horas["min"];
        }

        $horas_aux   = $horas_aux - $registro["qtd_horas"];
        $minutos_aux = $minutos_aux - $registro["qtd_min"];

        if ($horas_aux > 0 && $minutos_aux < 0) {
          $horas_aux--;
          $minutos_aux += 60;
        }

        if ($horas_aux < 0 && $minutos_aux > 0) {
          $horas_aux--;
          $minutos_aux -= 60;
        }

        if ($horas_aux < 0) {
          $horas_aux = '('.($horas_aux * -1).')';
        }

        if ($minutos_aux < 0) {
          $minutos_aux = '('.($minutos_aux * -1).')';
        }

        $tipo = $registro['tipo'] == 1 ?
          'Atraso' : 'Falta';

        $this->addLinhas( array(
          "<a href=\"educar_falta_atraso_det.php?cod_falta_atraso={$registro['cod_falta_atraso']}\">{$registro["nm_escola"]}</a>",
          "<a href=\"educar_falta_atraso_det.php?cod_falta_atraso={$registro['cod_falta_atraso']}\">{$det_ins["nm_instituicao"]}</a>",
          "<a href=\"educar_falta_atraso_det.php?cod_falta_atraso={$registro['cod_falta_atraso']}\">{$tipo}</a>",
          "<a href=\"educar_falta_atraso_det.php?cod_falta_atraso={$registro['cod_falta_atraso']}\">{$horas_aux}</a>",
          "<a href=\"educar_falta_atraso_det.php?cod_falta_atraso={$registro['cod_falta_atraso']}\">{$minutos_aux}</a>"
        ));
      }
    }

    $this->addPaginador2('educar_falta_atraso_lst.php', $total, $_GET, $this->nome, $this->limite);
    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
      $this->array_botao[]     = 'Novo';
      $this->array_botao_url[] = "educar_falta_atraso_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
    }

    $this->array_botao[]     = 'Voltar';
    $this->array_botao_url[] = "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
    $this->largura           = "100%";
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