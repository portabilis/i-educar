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
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   Ied_Cadastro
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';

/**
 * clsIndex class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Pessoas Físicas - Cadastro');
    $this->processoAp = 43;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $cod_pessoa_fj;
  var $nm_pessoa;
  var $id_federal;
  var $data_nasc;
  var $endereco;
  var $cep;
  var $idlog;
  var $idbai;
  var $sigla_uf;
  var $ddd_telefone_1;
  var $telefone_1;
  var $ddd_telefone_2;
  var $telefone_2;
  var $ddd_telefone_mov;
  var $telefone_mov;
  var $ddd_telefone_fax;
  var $telefone_fax;
  var $email;
  var $http;
  var $tipo_pessoa;
  var $sexo;
  var $busca_pessoa;
  var $complemento;
  var $apartamento;
  var $bloco;
  var $andar;
  var $numero;
  var $retorno;
  var $zona_localizacao;
  var $cor_raca;

  var $caminho_det;
  var $caminho_lst;

  var $alterado;

  function Inicializar()
  {
    if ($_REQUEST['busca_pessoa']) {
      $this->retorno = 'Novo';

      $cpf = idFederal2int($_REQUEST['busca_pessoa']);

      $this->busca_pessoa = $cpf;
      $this->id_federal   = $cpf;

      $objPessoa     = new clsPessoaFisica(FALSE, $cpf);
      $detalhePessoa = $objPessoa->detalhe();

      $this->cod_pessoa_fj = $detalhePessoa["idpes"];
    }
    elseif ($_REQUEST['cod_pessoa_fj'] != '') {
      $this->busca_pessoa = TRUE;

      if ($_REQUEST['cod_pessoa_fj'] != 0) {
        $this->cod_pessoa_fj = $_REQUEST['cod_pessoa_fj'];
      }
      else {
        $this->retorno = 'Novo';
      }
    }

    if ($this->cod_pessoa_fj) {
      $this->cod_pessoa_fj = @$_GET['cod_pessoa'] ?
        @$_GET['cod_pessoa'] : $this->cod_pessoa_fj;

      $db = new clsBanco();
      $objPessoa = new clsPessoaFisica();

      list($this->nm_pessoa, $this->id_federal, $this->data_nasc,
        $this->ddd_telefone_1, $this->telefone_1, $this->ddd_telefone_2,
        $this->telefone_2, $this->ddd_telefone_mov, $this->telefone_mov,
        $this->ddd_telefone_fax, $this->telefone_fax, $this->email,
        $this->http, $this->tipo_pessoa, $this->sexo, $this->cidade,
        $this->bairro, $this->logradouro, $this->cep, $this->idlog, $this->idbai,
        $this->idtlog, $this->sigla_uf, $this->complemento, $this->numero,
        $this->bloco, $this->apartamento, $this->andar, $this->zona_localizacao
      ) =
      $objPessoa->queryRapida(
        $this->cod_pessoa_fj, 'nome', 'cpf', 'data_nasc',  'ddd_1', 'fone_1',
        'ddd_2', 'fone_2', 'ddd_mov', 'fone_mov', 'ddd_fax', 'fone_fax', 'email',
        'url', 'tipo', 'sexo', 'cidade', 'bairro', 'logradouro', 'cep', 'idlog',
        'idbai', 'idtlog', 'sigla_uf', 'complemento', 'numero', 'bloco', 'apartamento',
        'andar', 'zona_localizacao'
      );

      // Cor/Raça.
      $raca = new clsCadastroFisicaRaca($this->cod_pessoa_fj);
      $raca = $raca->detalhe();
      if (is_array($raca)) {
        $this->cod_raca = $raca['ref_cod_raca'];
      }

      $this->cep     = int2Cep($this->cep);
      $this->retorno = 'Editar';
    }

    $this->nome_url_cancelar = 'Cancelar';

    return $this->retorno;
  }

  function Gerar()
  {
    $this->addBanner('imagens/nvp_top_intranet.jpg',
      'imagens/nvp_vert_intranet.jpg', 'Intranet', FALSE);

    if (! $this->busca_pessoa) {
      $this->campoOculto('cod_pessoa_fj', '');

      $parametros = new clsParametrosPesquisas();
      $parametros->setSubmit(1);
      $parametros->adicionaCampoTexto('busca_pessoa', 'id_federal');
      $parametros->adicionaCampoTexto('cod_pessoa_fj', 'idpes');
      $parametros->setPessoa('F');
      $parametros->setPessoaCampo('cod_pessoa_fj');
      $parametros->setPessoaNovo('S');
      $parametros->setPessoaTela('window');

      $html = sprintf(
        '<img id="lupa" src="imagens/lupa.png" border="0" ' .
        "onclick=\"showExpansivel(500, 500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'pesquisa_pessoa_lst.php?campos=%s\'></iframe>');\"".
        '>',
        $parametros->serializaCampos()
      );

      $this->campoCpf('busca_pessoa', 'CPF', $this->ref_cod_pessoa_fj, TRUE,
        $html, FALSE, TRUE);
    }
    else {
      $this->campoOculto('busca_pessoa', $this->busca_pessoa);

      $this->url_cancelar = $this->retorno == 'Editar' ?
        'atendidos_det.php?cod_pessoa=' . $this->cod_pessoa_fj : 'atendidos_lst.php';

      $this->campoOculto('cod_pessoa_fj', $this->cod_pessoa_fj);
      $this->campoTexto('nm_pessoa', 'Nome', $this->nm_pessoa, '50', '255', TRUE);

      if ($this->id_federal) {
        $this->campoRotulo('id_federal', 'CPF', int2CPF($this->id_federal));
      }
      else {
        $this->campoCpf('id_federal', 'CPF', '', FALSE);
      }

      if ($this->data_nasc) {
        $this->data_nasc = dataFromPgToBr($this->data_nasc);
      }

      $this->campoData('data_nasc', 'Data de Nascimento', $this->data_nasc);

      $lista_sexos      = array();
      $lista_sexos['']  = 'Escolha uma opção...';
      $lista_sexos['M'] = 'Masculino';
      $lista_sexos['F'] = 'Feminino';
      $this->campoLista('sexo', 'Sexo', $lista_sexos, $this->sexo);

      // Cor/raça.
      $opcoes_raca = array('' => 'Selecione');
      $obj_raca = new clsCadastroRaca();
      $lst_raca = $obj_raca->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, TRUE);

      if ($lst_raca) {
        foreach ($lst_raca as $raca) {
          $opcoes_raca[$raca['cod_raca']] = $raca['nm_raca'];
        }
      }

      $this->campoLista('cor_raca', 'Raça', $opcoes_raca,
        $this->cod_raca, '', FALSE, '', '', '', FALSE);

      // Detalhes do Endereço
      $objTipoLog   = new clsTipoLogradouro();
      $listaTipoLog = $objTipoLog->lista();
      $listaTLog    = array('0' => 'Selecione');

      if ($listaTipoLog) {
        foreach ($listaTipoLog as $tipoLog) {
          $listaTLog[$tipoLog['idtlog']] = $tipoLog['descricao'];
        }
      }

      $objUf       = new clsUf();
      $listauf     = $objUf->lista();
      $listaEstado = array('0' => 'Selecione');

      if ($listauf) {
        foreach ($listauf as $uf) {
          $listaEstado[$uf['sigla_uf']] = $uf['sigla_uf'];
        }
      }

      $this->campoOculto('idbai', $this->idbai);
      $this->campoOculto('idlog', $this->idlog);
      $this->campoOculto('cep', $this->cep);
      $this->campoOculto('ref_sigla_uf', $this->sigla_uf);
      $this->campoOculto('ref_idtlog', $this->idtlog);
      $this->campoOculto('id_cidade', $this->cidade);

      $zona = App_Model_ZonaLocalizacao::getInstance();

      if ($this->idlog && $this->idbai && $this->cep && $this->cod_pessoa_fj) {
        $this->campoCep('cep_', 'CEP', $this->cep, true, '-',
          "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade\'></iframe>');\">",
          TRUE);

        $this->campoLista('idtlog', 'Tipo Logradouro', $listaTLog, $this->idtlog,
          FALSE, FALSE, FALSE, FALSE, TRUE);

        $this->campoTextoInv('logradouro', 'Logradouro', $this->logradouro,
          '50', '255', FALSE);

        $this->campoTextoInv('cidade', 'Cidade', $this->cidade, '50', '255',
          FALSE);

        $this->campoTextoInv('bairro', 'Bairro', $this->bairro, '50', '255', FALSE);

        $this->campoTexto('complemento', 'Complemento',  $this->complemento, '50', '255',
          FALSE);

        $this->campoTexto('numero', 'Número', $this->numero, '10', '10');

        $this->campoTexto('letra', 'Letra', $this->letra, '1', '1', FALSE);

        $this->campoTexto('apartamento', 'Número Apartamento', $this->apartamento, '6', '6',
          FALSE);

        $this->campoTexto('bloco', 'Bloco', $this->bloco, '20', '20', FALSE);
        $this->campoTexto('andar', 'Andar', $this->andar, '2', '2', FALSE);

        $this->campoLista('sigla_uf', 'Estado', $listaEstado, $this->sigla_uf,
          FALSE, FALSE, FALSE, FALSE, TRUE);
      }
      elseif($this->cod_pessoa_fj && $this->cep) {
        $this->campoCep('cep_', 'CEP', $this->cep, true, '-',
          "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade\'></iframe>');\">",
          $disabled);

        $this->campoLista('idtlog', 'Tipo Logradouro', $listaTLog, $this->idtlog);

        $this->campoTexto('logradouro', 'Logradouro',  $this->logradouro, '50',
          '255', FALSE);

        $this->campoTexto('cidade', 'Cidade', $this->cidade, '50', '255', FALSE);

        $this->campoTexto('bairro', 'Bairro',  $this->bairro, '50', '255', FALSE);

        $this->campoTexto('complemento', 'Complemento',  $this->complemento, '50',
          '255', FALSE);

        $this->campoTexto('numero', 'Número', $this->numero, '10', '10');

        $this->campoTexto('letra', 'Letra', $this->letra, '1', '1', FALSE);

        $this->campoTexto('apartamento', 'Número Apartamento', $this->apartamento,
          '6', '6', FALSE);

        $this->campoTexto('bloco', 'Bloco', $this->bloco, '20', '20', FALSE);

        $this->campoTexto('andar', 'Andar', $this->andar, '2', '2', FALSE);

        $this->campoLista('sigla_uf', 'Estado', $listaEstado, $this->sigla_uf);
      }
      else {
        $this->campoCep('cep_', 'CEP', $this->cep, TRUE, '-',
          "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\"
          onclick=\"showExpansivel(500, 500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade&campo14=zona_localizacao\'></iframe>');\">",
          false
        );

        $this->campoLista('idtlog', 'Tipo Logradouro', $listaTLog, $this->idtlog,
          FALSE, FALSE, FALSE, FALSE, FALSE);

        $this->campoTexto('logradouro', 'Logradouro', $this->logradouro,
          '50', '255');

        $this->campoTexto('cidade', 'Cidade', $this->cidade, '50', '255');

        $this->campoTexto('bairro', 'Bairro', $this->bairro, '50', '255');

        $this->campoTexto('complemento', 'Complemento', $this->complemento,
          '50', '255', FALSE);

        $this->campoTexto('numero', 'Número', $this->numero, '10', '10');

        $this->campoTexto('letra', 'Letra', $this->letra, '1', '1', FALSE);

        $this->campoTexto('apartamento', 'Número Apartamento', $this->apartamento,
          '6', '6', FALSE);

        $this->campoTexto('bloco', 'Bloco', $this->bloco, '20', '20', FALSE);

        $this->campoTexto('andar', 'Andar', $this->andar, '2', '2', FALSE);

        $this->campoLista('sigla_uf', 'Estado', $listaEstado, $this->sigla_uf,
          FALSE, FALSE, FALSE, FALSE, FALSE);
      }

      $this->campoLista('zona_localizacao', 'Zona Localização', $zona->getEnums(),
        $this->zona_localizacao, FALSE, FALSE, FALSE, FALSE,
        ($this->idbai ? TRUE : FALSE)
      );

      $this->campoTexto('ddd_telefone_1', 'DDD Telefone 1', $this->ddd_telefone_1,
        '3', '2', FALSE);

      $this->campoTexto('telefone_1', 'Telefone 1',  $this->telefone_1, '10',
        '15', FALSE);

      $this->campoTexto('ddd_telefone_2', 'DDD Telefone 2', $this->ddd_telefone_2,
        '3', '2', FALSE);

      $this->campoTexto('telefone_2', 'Telefone 2', $this->telefone_2, '10',
        '15', FALSE);

      $this->campoTexto('ddd_telefone_mov', 'DDD Celular',
        $this->ddd_telefone_mov, '3', '2', FALSE);

      $this->campoTexto('telefone_mov', 'Celular',  $this->telefone_mov, '10',
        '15', FALSE);

      $this->campoTexto('ddd_telefone_fax', 'DDD Fax',  $this->ddd_telefone_fax,
        '3', '2', FALSE);

      $this->campoTexto('telefone_fax', 'Fax',  $this->telefone_fax, '10', '15',
        FALSE);

      $this->campoTexto('http', 'Site', $this->http, '50', '255', FALSE);

      $this->campoTexto('email', 'E-mail', $this->email, '50', '255', FALSE);

      if ($this->cod_pessoa_fj) {
        $this->campoRotulo('documentos', '<b><i>Documentos</i></b>',
          "<a href='#' onclick=\"openPage('adicionar_documentos_cad.php?id_pessoa={$this->cod_pessoa_fj}', '400', '400', 'yes', '10', '10'); \"><img src='imagens/nvp_bot_ad_doc.png' border='0'></a>");

        $this->campoCheck('alterado', 'Alterado', $this->alterado);
      }
    }
  }

  function Novo()
  {
    @session_start();
    $pessoaFj = $_SESSION['id_pessoa'];
    session_write_close();

    $db  = new clsBanco();
    $db2 = new clsBanco();

    $ref_cod_sistema = FALSE;

    if ($this->id_federal) {
      $this->id_federal = idFederal2int($this->id_federal);

      $objCPF = new clsFisica(FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, $this->id_federal);

      $detalhe_fisica = $objCPF->detalhe();
      if ($detalhe_fisica['cpf']) {
        $this->erros['id_federal'] = 'CPF já cadastrado.';
        return FALSE;
      }
    }

    $objPessoa = new clsPessoa_(FALSE, $this->nm_pessoa, $pessoaFj, $this->http,
      'F', FALSE, FALSE, $this->email);

    $idpes = $objPessoa->cadastra();

    $this->data_nasc = dataToBanco($this->data_nasc);

    if ($this->id_federal) {
      $objFisica = new clsFisica($idpes, $this->data_nasc, $this->sexo, FALSE,
        FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        $ref_cod_sistema, $this->id_federal);
    }
    else {
      $objFisica = new clsFisica($idpes, $this->data_nasc, $this->sexo, FALSE,
        FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        $ref_cod_sistema);
    }

    $objFisica->cadastra();

    $objTelefone = new clsPessoaTelefone($idpes, 1, $this->telefone_1, $this->ddd_telefone_1);
    $objTelefone->cadastra();

    $objTelefone = new clsPessoaTelefone($idpes, 2, $this->telefone_2, $this->ddd_telefone_2);
    $objTelefone->cadastra();

    $objTelefone = new clsPessoaTelefone($idpes, 3, $this->telefone_mov, $this->ddd_telefone_mov);
    $objTelefone->cadastra();

    $objTelefone = new clsPessoaTelefone($idpes, 4, $this->telefone_fax, $this->ddd_telefone_fax);
    $objTelefone->cadastra();

    if ($this->cep && $this->idbai && $this->idlog) {
      $this->cep    = idFederal2Int($this->cep);
      $objEndereco  = new clsPessoaEndereco($idpes);
      $objEndereco2 = new clsPessoaEndereco($idpes, $this->cep, $this->idlog,
        $this->idbai, $this->numero, $this->complemento, FALSE, $this->letra,
        $this->bloco, $this->apartamento, $this->andar);

      if ($objEndereco->detalhe()) {
        $objEndereco2->edita();
      }
      else {
        $objEndereco2->cadastra();
      }
    }
    elseif($this->cep_) {
      $this->cep_  = idFederal2int($this->cep_);

      $objEnderecoExterno  = new clsEnderecoExterno($idpes);
      $objEnderecoExterno2 = new clsEnderecoExterno($idpes, '1', $this->idtlog,
        $this->logradouro, $this->numero, $this->letra, $this->complemento,
        $this->bairro, $this->cep_, $this->cidade, $this->sigla_uf, FALSE,
        $this->bloco, $this->apartamento, $this->andar, FALSE, FALSE,
        $this->zona_localizacao);

      if ($objEnderecoExterno->detalhe()) {
        $objEnderecoExterno2->edita();
      }
      else {
        $objEnderecoExterno2->cadastra();
      }
    }

    // Cadastra raça.
    $this->_cadastraRaca($idpes, $this->cor_raca);

    echo '<script>document.location="atendidos_lst.php";</script>';
    return TRUE;
  }

  function Editar()
  {
    @session_start();
    $pessoaFj = $_SESSION['id_pessoa'];
    session_write_close();

    if ($this->id_federal) {
      $ref_cod_sistema  = 'null';
      $this->id_federal = idFederal2int($this->id_federal);

      $objFisicaCpf   = new clsFisica($this->cod_pessoa_fj);
      $detalhe_fisica = $objFisicaCpf->detalhe();

      if (! $detalhe_fisica['cpf']) {
        $objCPF = new clsFisica(FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
          FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
          FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, $this->id_federal);

        if ($objCPF->detalhe()) {
          $this->erros['id_federal'] = 'CPF já cadastrado.';
          return FALSE;
        }
      }
    }

    $objPessoa = new clsPessoa_($this->cod_pessoa_fj, $this->nm_pessoa, FALSE,
      $this->p_http, FALSE, $pessoaFj, date('Y-m-d H:i:s', time()), $this->email);

    $objPessoa->edita();

    $this->data_nasc = dataToBanco($this->data_nasc);

    if ($this->id_federal) {
      $this->id_federal = idFederal2Int($this->id_federal);
      $objFisica = new clsFisica($this->cod_pessoa_fj, $this->data_nasc,
        $this->sexo, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        FALSE, FALSE, FALSE, $ref_cod_sistema, $this->id_federal);
    }
    else {
      $objFisica = new clsFisica($this->cod_pessoa_fj, $this->data_nasc,
        $this->sexo, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        FALSE, FALSE, FALSE, $ref_cod_sistema);
    }

    $objFisica->edita();

    if ($this->alterado) {
      $db = new clsBanco();
      $db->Consulta("UPDATE cadastro.fisica SET alterado = 'TRUE' WHERE idpes = '$this->cod_pessoa_fj'");
    }

    $objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj, 1,
      $this->telefone_1, $this->ddd_telefone_1);
    $objTelefone->cadastra();

    $objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj, 2,
      $this->telefone_2, $this->ddd_telefone_2);
    $objTelefone->cadastra();

    $objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj, 3,
      $this->telefone_mov, $this->ddd_telefone_mov);
    $objTelefone->cadastra();

    $objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj, 4,
      $this->telefone_fax, $this->ddd_telefone_fax);
    $objTelefone->cadastra();

    $objEndereco = new clsPessoaEndereco($this->cod_pessoa_fj);

    $this->cep = idFederal2Int($this->cep);

    $objEndereco2 = new clsPessoaEndereco($this->cod_pessoa_fj, $this->cep,
      $this->idlog, $this->idbai, $this->numero, $this->complemento, FALSE,
      $this->letra, $this->bloco, $this->apartamento,$this->andar);

    if ($objEndereco->detalhe() && $this->cep && $this->idlog && $this->idbai) {
      $objEndereco2->edita();
    }
    elseif ($this->cep && $this->idlog && $this->idbai) {
      $objEndereco2->cadastra();
    }
    elseif ($objEndereco->detalhe()) {
      $objEndereco2->exclui();
    }
    else {
      $this->cep_ = idFederal2int($this->cep_);
      $objEnderecoExterno = new clsEnderecoExterno($this->cod_pessoa_fj);

      $objEnderecoExterno2 = new clsEnderecoExterno($this->cod_pessoa_fj, '1',
        $this->idtlog, $this->logradouro, $this->numero, $this->letra,
        $this->complemento, $this->bairro, $this->cep_, $this->cidade,
        $this->sigla_uf, FALSE, $this->bloco, $this->apartamento, $this->andar,
        FALSE, FALSE, $this->zona_localizacao);

      if ($objEnderecoExterno->detalhe()) {
        $objEnderecoExterno2->edita();
      }
      else {
        $objEnderecoExterno2->cadastra();
      }
    }

    // Atualizada raça.
    $this->_cadastraRaca($this->cod_pessoa_fj, $this->cor_raca);

    echo '<script>document.location="atendidos_lst.php";</script>';
    return TRUE;
  }

  function Excluir()
  {
    echo '<script>document.location="atendidos_lst.php";</script>';
    return TRUE;
  }

  /**
   * Cadastra ou atualiza a raça de uma pessoa.
   *
   * @access protected
   * @param  int $pessoaId
   * @param  int $corRaca
   * @return bool
   * @since  Método disponível desde a versão 1.2.0
   */
  function _cadastraRaca($pessoaId, $corRaca)
  {
    $pessoaId = (int) $pessoaId;
    $corRaca  = (int) $corRaca;

    $raca = new clsCadastroFisicaRaca($pessoaId, $corRaca);
    if ($raca->existe()) {
      return $raca->edita();
    }

    return $raca->cadastra();
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