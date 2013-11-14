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
 * Meus dados.
 *
 * @author   Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo disponível desde a versão 1.0.0
 * @version  $Id$
 */

$desvio_diretorio = '';
require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';


class clsIndex extends clsBase
{
  public function Formular() {
    $this->SetTitulo($this->_instituicao . 'Usu&aacute;rios');
    $this->processoAp = '0';
  }
}


class indice extends clsCadastro
{

  public
    $p_cod_pessoa_fj,
    $p_nm_pessoa,
    $p_id_federal,
    $idtlog,
    $p_endereco,
    $p_cep,
    $p_ref_bairro,
    $p_ddd_telefone_1,
    $p_telefone_1,
    $p_ddd_telefone_2,
    $p_telefone_2,
    $p_ddd_telefone_mov,
    $p_telefone_mov,
    $p_ddd_telefone_fax,
    $p_telefone_fax,
    $p_email,
    $p_http,
    $p_tipo_pessoa,
    $p_sexo,
    $f_matricula,
    $f_senha,
    $f_ativo,
    $f_ref_sec,
    $f_ramal,
    $f_ref_dept,
    $f_ref_setor,
    $ref_cod_funcionario_vinculo,
    $bloco,
    $apartamento,
    $andar,
    $ref_cod_setor = NULL;

  public $confere_senha;

  public function Inicializar()
  {
    $retorno = "Novo";
    session_start();

    if (isset($_SESSION['id_pessoa'])) {
      $this->p_cod_pessoa_fj = $_SESSION['id_pessoa'];
      $objPessoa = new clsPessoaFj();
      $db = new clsBanco();
      $db->Consulta("SELECT f.matricula, f.senha, f.ativo, f.ramal, f.ref_cod_setor, f.ref_cod_funcionario_vinculo, f.ref_cod_setor_new, email FROM funcionario f WHERE f.ref_cod_pessoa_fj={$this->p_cod_pessoa_fj}");

      if ($db->ProximoRegistro()) {
        list($this->f_matricula, $this->f_senha, $this->f_ativo, $this->f_ramal,
          $this->f_ref_setor, $this->ref_cod_funcionario_vinculo, $this->ref_cod_setor, $this->email) = $db->Tupla();

        list($this->p_nm_pessoa, $this->p_id_federal, $this->p_endereco, $this->p_cep,
          $this->p_ref_bairro, $this->p_ddd_telefone_1, $this->p_telefone_1,
          $this->p_ddd_telefone_2, $this->p_telefone_2, $this->p_ddd_telefone_mov,
          $this->p_telefone_mov, $this->p_ddd_telefone_fax, $this->p_telefone_fax,
          $this->p_email, $this->p_http, $this->p_tipo_pessoa, $this->cidade,
          $this->bairro, $this->logradouro, $this->cep, $this->idlog, $this->idbai,
          $this->idtlog, $this->sigla_uf, $this->complemento, $this->numero, $this->letra,
          $this->bloco, $this->apartamento, $this->andar) = $objPessoa->queryRapida($this->p_cod_pessoa_fj, "nome", "cpf", "endereco", "cep", "bairro", "ddd_1", "fone_1", "ddd_2", "fone_2", "ddd_mov", "fone_mov", "ddd_fax", "fone_fax", "email", "url", "tipo", "cidade", "bairro", "logradouro", "cep", "idlog", "idbai", "idtlog", "sigla_uf", "complemento", "numero", "letra", "bloco", "apartamento", "andar");

        $objFisica = new clsPessoaFisica();
        list($this->p_sexo) = $objFisica->queryRapida($this->p_cod_pessoa_fj, "sexo");

        $this->fexcluir = FALSE;
        $retorno        = "Editar";

        // define os niveis ate o setor escolhido (para que os campos ja venham preenchidos corretamente)
        if ($this->ref_cod_setor) {
          $objSetor = new clsSetor();
          $niveis = $objSetor->getNiveis($this->ref_cod_setor);

          for ($i = 0; $i < count($niveis); $i++) {
            $nm_var = "setor_$i";
            $this->$nm_var = $niveis[$i];
          }
        }
      }
    }

    $this->url_cancelar      = 'index.php';
    $this->nome_url_cancelar = 'Cancelar';

    return $retorno;
  }

  public function null2empityStr($vars)
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
    session_start();
    $this->campoOculto('p_cod_pessoa_fj', $this->p_cod_pessoa_fj);
    $this->cod_pessoa_fj = $this->p_cod_pessoa_fj;

    list ($this->p_ddd_telefone_1, $this->p_ddd_telefone_2,
      $this->p_ddd_telefone_fax, $this->p_ddd_telefone_mov) =
        $this->null2empityStr(array($this->p_ddd_telefone_1, $this->p_ddd_telefone_2, $this->p_ddd_telefone_fax, $this->p_ddd_telefone_mov));

    $this->p_ddd_telefone_1 = ($this->p_ddd_telefone_1 == NULL) ? '' : $this->p_ddd_telefone_1;
    $this->p_ddd_telefone_2 = ($this->p_ddd_telefone_2 == NULL) ? '' : $this->p_ddd_telefone_2;
    $this->p_ddd_telefone_3 = ($this->p_ddd_telefone_3 == NULL) ? '' : $this->p_ddd_telefone_3;

    $this->campoRotulo("nome", "Nome", $this->p_nm_pessoa);

    // Detalhes do endereço
    $objTipoLog = new clsTipoLogradouro();
    $listaTipoLog = $objTipoLog->lista();
    $listaTLog = array(""=>"Selecione");

    if ($listaTipoLog) {
      foreach ($listaTipoLog as $tipoLog) {
        $listaTLog[$tipoLog['idtlog']] = $tipoLog['descricao'];
      }
    }

    $objUf = new clsUf();
    $listauf = $objUf->lista();
    $listaEstado = array('' => "Selecione");
    if ($listauf) {
      foreach ($listauf as $uf) {
        $listaEstado[$uf['sigla_uf']] = $uf['sigla_uf'];
      }
    }

    $this->campoOculto('idbai', $this->idbai);
    $this->campoOculto('idlog', $this->idlog);

    if (is_numeric($this->cep)) {
      $this->cep = int2CEP($this->cep);
    }

    $this->campoOculto('cep', $this->cep);
    $this->campoOculto('ref_sigla_uf', $this->sigla_uf);
    $this->campoOculto('ref_idtlog', $this->idtlog);
    $this->campoOculto('id_cidade', $this->cidade);

    if ($this->idlog && $this->idbai && $this->cep && $this->cod_pessoa_fj)
    {
      $this->campoCep("cep_", "CEP", $this->cep, TRUE, "-", "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade\'></iframe>');\">", $disabled);

      $this->campoLista("sigla_uf", "Estado", $listaEstado, $this->sigla_uf,
        FALSE, FALSE, FALSE, FALSE, TRUE);

      $this->campoTextoInv("cidade", "Cidade", $this->cidade, "50", "255", FALSE);

      $this->campoTextoInv("bairro", "Bairro", $this->bairro, "50", "255", FALSE);

      $this->campoLista("idtlog","Tipo Logradouro", $listaTLog,$this->idtlog,
        FALSE, FALSE, FALSE, FALSE, TRUE);

      $this->campoTextoInv("logradouro", "Logradouro", $this->logradouro, "50", "255", FALSE);

      $this->campoTexto("complemento", "Complemento", $this->complemento, "22", "20", FALSE);

      $this->campoTexto("numero", "N&uacute;mero", $this->numero, "10", "10", TRUE);

      $this->campoTexto("letra", "Letra", $this->letra, "1", "1", FALSE);

      $this->campoTexto("bloco", "Bloco", $this->bloco, "20","20", FALSE);

      $this->campoTexto("apartamento", "Apartamento", $this->apartamento, "6","6", FALSE);

      $this->campoTexto("andar", "Andar", $this->andar, "2","2", FALSE);
    }
    elseif ($this->cod_pessoa_fj && $this->cep)
    {
      $this->campoCep("cep_", "CEP", $this->cep, TRUE, "-", "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade\'></iframe>');\">", $disabled);

      $this->campoLista("sigla_uf","Estado",$listaEstado,$this->sigla_uf);

      $this->campoTexto("cidade", "Cidade",  $this->cidade, "50", "255", FALSE);

      $this->campoTexto("bairro", "Bairro",  $this->bairro, "50", "255", FALSE);

      $this->campoLista("idtlog","Tipo Logradouro",$listaTLog,$this->idtlog);

      $this->campoTexto("logradouro", "Logradouro",  $this->logradouro, "50", "255", FALSE);

      $this->campoTexto("complemento", "Complemento",  $this->complemento, "22", "20", FALSE);

      $this->campoTexto("numero", "N&uacute;mero",  $this->numero, "10", "10", FALSE);

      $this->campoTexto("letra", "Letra",  $this->letra, "1", "1", FALSE);

      $this->campoTexto("bloco", "Bloco", $this->bloco, "20","20", FALSE);

      $this->campoTexto("apartamento", "Apartamento", $this->apartamento, "6","6", FALSE);

      $this->campoTexto("andar", "Andar", $this->andar, "2","2", FALSE);
    }
    else
    {
      $this->campoCep("cep_", "CEP", $this->cep, TRUE, "-", "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade\'></iframe>');\">", $disabled);

      $this->campoLista("sigla_uf", "Estado", $listaEstado, $this->sigla_uf, FALSE,
        FALSE, FALSE, FALSE, TRUE);

      $this->campoTextoInv("cidade", "Cidade", $this->cidade, "50", "255", TRUE);

      $this->campoTextoInv("bairro", "Bairro",  $this->bairro, "50", "20", TRUE);

      $this->campoLista("idtlog", "Tipo Logradouro", $listaTLog, $this->idtlog,
        FALSE, FALSE, FALSE, FALSE, TRUE);

      $this->campoTextoInv("logradouro", "Logradouro", $this->logradouro, "50", "255", TRUE);

      $this->campoTextoInv("complemento", "Complemento",  $this->complemento, "22", "20", FALSE);

      $this->campoTextoInv("numero", "Número",  $this->numero, "10", "10", FALSE);

      $this->campoTextoInv("letra", "Letra",  $this->letra, "1", "1", FALSE);

      $this->campoTexto("bloco", "Bloco", $this->bloco, "20","20", FALSE);

      $this->campoTexto("apartamento", "Apartamento", $this->apartamento, "6","6", FALSE);

      $this->campoTexto("andar", "Andar", $this->andar, "2","2", FALSE);
    }

    $this->inputTelefone('1', 'Telefone 1');
    $this->inputTelefone('2', 'Telefone 2');
    $this->inputTelefone('mov', 'Celular');
    $this->inputTelefone('fax', 'Fax');

    $this->campoTexto("p_http", "Site", $this->p_http, "50", "255", FALSE);

    // exibe o email definido pelo usuário ($this->email) no lugar do email da pessoa ($this->p_email)
    $this->campoRotulo('email', 'E-mail', $this->email . " <a href='/module/Usuario/AlterarEmail' class='decorated'>alterar e-mail</a>");

    if (empty($_SESSION['convidado'])) {
      $this->campoRotulo('senha', 'Senha', '********' . " <a href='/module/Usuario/AlterarSenha' class='decorated'>alterar senha</a>");

      //$this->campoSenha("f_senha", "Senha",  $this->f_senha, FALSE);
      //$this->campoOculto("confere_senha", $this->f_senha);
    }

    $lista_sexos = array();
    $lista_sexos['']  = 'Escolha uma op&ccedil;&atilde;o...';
    $lista_sexos['M'] = 'Masculino';
    $lista_sexos['F'] = 'Feminino';
    $this->campoLista("p_sexo", "Sexo", $lista_sexos, $this->p_sexo);

    $dba = new clsBanco();
    $opcoes = array();
    $dba->Consulta("SELECT cod_funcionario_vinculo, nm_vinculo FROM funcionario_vinculo ORDER BY nm_vinculo ASC");

    while ($dba->ProximoRegistro()) {
      list($cod, $nome) = $dba->Tupla();
      $opcoes[$cod] = $nome;
    }

    $this->campoLista("ref_cod_funcionario_vinculo", "V&iacute;nculo", $opcoes,
      $this->ref_cod_funcionario_vinculo);

    $this->campoTexto("f_ramal", "Ramal", $this->f_ramal, "10", "20", FALSE);

    $this->campoRotulo("documentos", "Documentos", "<a href='#' onclick=\" openPage('adicionar_documentos_cad.php?idpes={$this->p_cod_pessoa_fj}','400','400','yes', '10','10'); \"><img src='imagens/nvp_bot_ad_doc.png' border='0'></a>");
  }

  public function Editar()
  {

    session_start();
    $pessoaFj = $_SESSION['id_pessoa'];
    session_write_close();

    $objPessoa = new clsPessoa_($pessoaFj, FALSE, FALSE, $this->p_http, FALSE,
      $pessoaFj, date("Y-m-d H:i:s", time()), $this->p_email);

    $objPessoa->edita();

    $objFisica = new clsFisica($pessoaFj, FALSE, $this->p_sexo);
    $objFisica->edita();

    $objTelefone = new clsPessoaTelefone($pessoaFj);
    $objTelefone->excluiTodos();

    $objTelefone = new clsPessoaTelefone($pessoaFj, 1, str_replace("-", "", $this->p_telefone_1), $this->p_ddd_telefone_1);
    $objTelefone->cadastra();

    $objTelefone = new clsPessoaTelefone($pessoaFj, 2, str_replace("-", "", $this->p_telefone_2), $this->p_ddd_telefone_2);
    $objTelefone->cadastra();

    $objTelefone = new clsPessoaTelefone($pessoaFj, 3, str_replace("-", "", $this->p_telefone_mov), $this->p_ddd_telefone_mov);
    $objTelefone->cadastra();

    $objTelefone = new clsPessoaTelefone($pessoaFj, 4, str_replace("-", "", $this->p_telefone_fax), $this->p_ddd_telefone_fax);
    $objTelefone->cadastra();

    if ($this->cep && $this->idbai && $this->idlog) {
      $objEndereco = new clsPessoaEndereco( $pessoaFj );
      $objEndereco2 = new clsPessoaEndereco($pessoaFj,$this->cep,$this->idlog,$this->idbai,$this->numero,$this->complemento,FALSE,$this->letra, $this->bloco, $this->apartamento, $this->andar);
      if( $objEndereco->detalhe() )
      {
        $objEndereco2->edita();
      }
      else {
        $objEndereco2->cadastra();
      }

      $objPessoa = new clsPessoaFj();
      list($this->cidade, $this->bairro, $this->logradouro, $this->cep,
        $this->idtlog, $this->sigla_uf, $this->bloco, $this->apartamento, $this->andar) =
          $objPessoa->queryRapida($pessoaFj, "cidade", "bairro", "logradouro",
            "cep", "idtlog", "sigla_uf", "bloco", "apartamento", "andar");
    }
    else {
      $this->cep_ = idFederal2int($this->cep_);
      $objEnderecoExterno  = new clsEnderecoExterno($pessoaFj);
      $objEnderecoExterno2 = new clsEnderecoExterno($pessoaFj, "1", $this->idtlog,
        $this->logradouro, $this->numero, $this->letra, $this->complemento,
        $this->bairro, $this->cep_, $this->cidade, $this->sigla_uf, FALSE,
        $this->bloco, $this->apartamento, $this->andar);

      if ($objEnderecoExterno->detalhe()) {
        $objEnderecoExterno2->edita();
      }
      else {
        $objEnderecoExterno2->cadastra();
      }
    }

    // Verifica o maior setor selecionado
    for ($i = 0; $i < 5; $i++) {
      $varNm = "setor_$i";
      if ($this->$varNm) {
        $setor = $this->$varNm;
      }
    }
    if ($setor) {
      $sql = " ref_cod_setor_new = '{$setor}', ";
    }

    if (empty($_SESSION['convidado']))
      $sql_funcionario = "UPDATE funcionario SET $sql ramal='{$this->f_ramal}', ref_cod_funcionario_vinculo='{$this->ref_cod_funcionario_vinculo}', ref_ref_cod_pessoa_fj='{$pessoaFj}' WHERE ref_cod_pessoa_fj={$this->p_cod_pessoa_fj}";
    else
      $sql_funcionario = "UPDATE funcionario SET $sql ramal='{$this->f_ramal}', ref_ref_cod_pessoa_fj='{$pessoaFj}' WHERE ref_cod_pessoa_fj={$this->p_cod_pessoa_fj}";

    $db = new clsBanco();

    $db->Consulta($sql_funcionario);

    if (empty($_SESSION['convidado'])) {
      if (! $_POST["reloading"]) {
      }
    }
    else {
      if ($_SESSION['motivo_visita'] == 'atualizar_cadastro_e_email') {
        echo "<script>document.location='solicita_email.php';</script>";
      }
      else {
        echo "<script>document.location='insmess_cad.php';</script>";
      }
    }

    header('Location: index.php');
  }

  protected function inputTelefone($type, $typeLabel = '') {
     if (! $typeLabel)
       $typeLabel = "Telefone {$type}";
 
     // ddd
 
     $options = array(
       'required'    => false,
       'label'       => "(ddd) / {$typeLabel}",
       'placeholder' => 'ddd',
       'value'       => $this->{"p_ddd_telefone_{$type}"},
       'max_length'  => 3,
       'size'        => 3,
       'inline'      => true
     );
 
     $this->inputsHelper()->integer("p_ddd_telefone_{$type}", $options);
 
 
    // telefone
 
     $options = array(
       'required'    => false,
       'label'       => '',
       'placeholder' => $typeLabel,
       'value'       => $this->{"p_telefone_{$type}"},
       'max_length'  => 11
     );
 
     $this->inputsHelper()->integer("p_telefone_{$type}", $options);
   }  
}


// Instancia objeto de página
$pagina = new clsIndex();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
