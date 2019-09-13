<?php

/*
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
 */

/**
 * Pop-up de troca de senha.
 *
 * @author   Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo disponível desde a versão 1.0.0
 * @version  $Id$
 */

use Illuminate\Support\Facades\Session;

$desvio_diretorio = "";
require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';


class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . 'Usu&aacute;rios');
    $this->processoAp   = "0";
    $this->renderBanner = FALSE;
    $this->renderMenu   = FALSE;
    $this->renderMenuSuspenso = FALSE;
  }
}

class indice extends clsCadastro
{

  public $p_cod_pessoa_fj;
  public $f_senha;
  public $f_senha2;


  public function Inicializar()
  {
    $retorno = "Novo";

    $this->p_cod_pessoa_fj = $this->pessoa_logada;

    $objPessoa = new clsPessoaFj();

    $db = new clsBanco();
    $db->Consulta("SELECT f.senha FROM portal.funcionario f WHERE f.ref_cod_pessoa_fj={$this->p_cod_pessoa_fj}");

    if ($db->ProximoRegistro()) {
      list($this->f_senha) = $db->Tupla();
    }

    $this->acao_enviar = "acao2()";
    return $retorno;
  }


  public function null2empityStr( $vars )
  {
    foreach ($vars as $key => $valor) {
      $valor .= "";
      if ($valor == "NULL") {
        $vars[$key] = "";
      }
    }

    return $vars;
  }


  public function Gerar()
  {
    $this->campoOculto("p_cod_pessoa_fj", $this->p_cod_pessoa_fj);
    $this->cod_pessoa_fj = $this->p_cod_pessoa_fj;

    $this->campoRotulo("", "<strong>Informações</strong>", "<strong>Sua senha expirará em alguns dias, por favor cadastre uma nova senha com no mínimo 8 caracteres e diferente da senha anterior</strong>");
    $this->campoSenha("f_senha", "Senha", "", TRUE, "A sua nova senha deverá conter pelo menos oito caracteres");
    $this->campoSenha("f_senha2", "Redigite a Senha", $this->f_senha2, TRUE);
  }


  public function Novo()
  {
    $sql = "SELECT ref_cod_pessoa_fj FROM portal.funcionario WHERE md5('{$this->f_senha}') = senha AND ref_cod_pessoa_fj = {$this->p_cod_pessoa_fj}";
    $db = new clsBanco();
    $senha_igual = $db->CampoUnico($sql);

    if ($this->f_senha && !$senha_igual) {
      $sql_funcionario = "UPDATE funcionario SET senha=md5('{$this->f_senha}'), data_troca_senha = NOW(), tempo_expira_senha = 30 WHERE ref_cod_pessoa_fj={$this->p_cod_pessoa_fj}";
      $db->Consulta( $sql_funcionario );
      echo "
        <script>
          window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
          window.parent.location = 'index.php';
        </script>";

      return TRUE;
    }

    $this->mensagem .= "A sua nova senha deverá ser diferente da anterior";

    return FALSE;
  }


  public function Editar() {}
}

$pagina = new clsIndex();
$miolo  = new indice();
$pagina->addForm($miolo);
$pagina->MakeAll();
?>

<script type="text/javascript">
function acao2()
{
  if ($F('f_senha').length > 7) {
    if ($F('f_senha') == $F('f_senha2')) {
      acao();
    }
    else {
      alert('As senhas devem ser iguais');
    }
  }
  else {
    alert('A sua nova senha deverá conter pelo menos oito caracteres');
  }
}
</script>
