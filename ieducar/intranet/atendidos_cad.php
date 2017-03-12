<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestгo escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaн
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa й software livre; vocк pode redistribuн-lo e/ou modificб-lo
 * sob os termos da Licenзa Pъblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versгo 2 da Licenзa, como (a seu critйrio)
 * qualquer versгo posterior.
 *
 * Este programa й distribuн≠do na expectativa de que seja ъtil, porйm, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implн≠cita de COMERCIABILIDADE OU
 * ADEQUA«√O A UMA FINALIDADE ESPECЌFICA. Consulte a Licenзa Pъblica Geral
 * do GNU para mais detalhes.
 *
 * Vocк deve ter recebido uma cуpia da Licenзa Pъblica Geral do GNU junto
 * com este programa; se nгo, escreva para a Free Software Foundation, Inc., no
 * endereзo 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itajaн <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   Ied_Cadastro
 * @since     Arquivo disponнvel desde a versгo 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';
require_once 'include/pmieducar/clsPmieducarAluno.inc.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';

require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Utils/Validation.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'image_check.php';

/**
 * clsIndex class.
 *
 * @author    Prefeitura Municipal de Itajaн <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponнvel desde a versгo 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Pessoas Fнsicas - Cadastro');
    $this->processoAp = 43;
    $this->addEstilo('localizacaoSistema');
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaн <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponнvel desde a versгo 1.0.0
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
  var $sus;
  var $nis_pis_pasep;
  var $municipio_id;
  var $bairro_id;
  var $logradouro_id;

  var $caminho_det;
  var $caminho_lst;

  // Variбveis para controle da foto
  var $objPhoto;
  var $arquivoFoto;

  function Inicializar()
  {
    $this->cod_pessoa_fj = @$_GET['cod_pessoa_fj'];
    $this->retorno       = 'Novo';

    if (is_numeric($this->cod_pessoa_fj)) {
      $this->retorno = 'Editar';
      $objPessoa     = new clsPessoaFisica();

      list($this->nm_pessoa, $this->id_federal, $this->data_nasc,
        $this->ddd_telefone_1, $this->telefone_1, $this->ddd_telefone_2,
        $this->telefone_2, $this->ddd_telefone_mov, $this->telefone_mov,
        $this->ddd_telefone_fax, $this->telefone_fax, $this->email,
        $this->tipo_pessoa, $this->sexo, $this->cidade,
        $this->bairro, $this->logradouro, $this->cep, $this->idlog, $this->idbai,
        $this->idtlog, $this->sigla_uf, $this->complemento, $this->numero,
        $this->bloco, $this->apartamento, $this->andar, $this->zona_localizacao, $this->estado_civil,
        $this->pai_id, $this->mae_id, $this->tipo_nacionalidade, $this->pais_origem, $this->naturalidade,
        $this->letra, $this->sus, $this->nis_pis_pasep
      ) =

      $objPessoa->queryRapida(
        $this->cod_pessoa_fj, 'nome', 'cpf', 'data_nasc',  'ddd_1', 'fone_1',
        'ddd_2', 'fone_2', 'ddd_mov', 'fone_mov', 'ddd_fax', 'fone_fax', 'email',
        'tipo', 'sexo', 'cidade', 'bairro', 'logradouro', 'cep', 'idlog',
        'idbai', 'idtlog', 'sigla_uf', 'complemento', 'numero', 'bloco', 'apartamento',
        'andar', 'zona_localizacao', 'ideciv', 'idpes_pai', 'idpes_mae', 'nacionalidade',
        'idpais_estrangeiro', 'idmun_nascimento', 'letra', 'sus', 'nis_pis_pasep'
      );

      $this->id_federal      = is_numeric($this->id_federal) ? int2CPF($this->id_federal) : '';
      $this->cep             = is_numeric($this->cep)        ? int2Cep($this->cep) : '';
      $this->data_nasc       = $this->data_nasc              ? dataFromPgToBr($this->data_nasc) : '';

      $this->estado_civil_id = $this->estado_civil->ideciv;
      $this->pais_origem_id  = $this->pais_origem->idpais;
      $this->naturalidade_id = $this->naturalidade->idmun;

      $raca           = new clsCadastroFisicaRaca($this->cod_pessoa_fj);
      $raca           = $raca->detalhe();
      $this->cod_raca = is_array($raca) ? $raca['ref_cod_raca'] : null;
    }

    $this->nome_url_cancelar = 'Cancelar';

    $nomeMenu = $this->retorno == "Editar" ? $this->retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         ""                                  => "$nomeMenu pessoa f&iacute;sica"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    return $this->retorno;
  }

  function Gerar()
  {
    $this->url_cancelar = $this->retorno == 'Editar' ?
      'atendidos_det.php?cod_pessoa=' . $this->cod_pessoa_fj : 'atendidos_lst.php';

    $this->campoCpf('id_federal', 'CPF', $this->id_federal, FALSE);

    $this->campoOculto('cod_pessoa_fj', $this->cod_pessoa_fj);
    $this->campoTexto('nm_pessoa', 'Nome', $this->nm_pessoa, '50', '255', TRUE);

    $foto = false;
    if (is_numeric($this->cod_pessoa_fj)){
      $objFoto = new ClsCadastroFisicaFoto($this->cod_pessoa_fj);
      $detalheFoto = $objFoto->detalhe();
      if(count($detalheFoto))
      $foto = $detalheFoto['caminho'];
    } else
      $foto=false;

    if ($foto!=false){
      $this->campoRotulo('fotoAtual_','Foto atual','<img height="117" src="'.$foto.'"/>');
      $this->campoArquivo('file','Trocar foto',$this->arquivoFoto,40,'<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho mбximo: 150KB</span>');
    }else
      $this->campoArquivo('file','Foto',$this->arquivoFoto,40,'<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho mбximo: 150KB</span>');


    // ao cadastrar pessoa do pai ou mгe apartir do cadastro de outra pessoa,
    // й enviado o tipo de cadastro (pai ou mae).
    $parentType = isset($_REQUEST['parent_type']) ? $_REQUEST['parent_type'] : '';
    $naturalidadeObrigatoria = ($parentType == '' ? true : false);


     // sexo

    $sexo = $this->sexo;

    // sugere sexo quando cadastrando o pai ou mгe

    if (! $sexo && $parentType == 'pai')
      $sexo = 'M';
    elseif (! $sexo && $parentType == 'mae')
      $sexo = 'F';


    $options = array(
      'label'       => 'Sexo / Estado civil',
      'value'     => $sexo,
      'resources' => array(
        '' => 'Sexo',
        'M' => 'Masculino',
        'F' => 'Feminino'
      ),
      'inline' => true
    );

    $this->inputsHelper()->select('sexo', $options);

    // estado civil

    $this->inputsHelper()->estadoCivil(array('label' => '', 'required' => empty($parentType)));


    // data nascimento

    $options = array(
      'label'       => 'Data nascimento',
      'value'       => $this->data_nasc,
      'required'    => empty($parentType)
    );

    $this->inputsHelper()->date('data_nasc', $options);


    // pai, mгe

    $this->inputPai();
    $this->inputMae();


    // documentos

    $documentos        = new clsDocumento();
    $documentos->idpes = $this->cod_pessoa_fj;
    $documentos        = $documentos->detalhe();

    // rg

    // o rg й obrigatorio ao cadastrar pai ou mгe, exceto se configurado como opcional.

    $required = (! empty($parentType));

    if ($required && $GLOBALS['coreExt']['Config']->app->rg_pessoa_fisica_pais_opcional) {
      $required = false;
    }

    $options = array(
      'required'    => $required,
      'label'       => 'RG / Data emissгo',
      'placeholder' => 'Documento identidade',
      'value'       => $documentos['rg'],
      'max_length'  => 20,
      'size'        => 27,
      'inline'      => true
    );

    $this->inputsHelper()->integer('rg', $options);


    // data emissгo rg

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Data emissгo',
      'value'       => $documentos['data_exp_rg'],
      'size'        => 19
    );

    $this->inputsHelper()->date('data_emissao_rg', $options);


    // orgгo emissгo rg

    $selectOptions = array( null => 'Orgгo emissor' );
    $orgaos        = new clsOrgaoEmissorRg();
    $orgaos        = $orgaos->lista();

    foreach ($orgaos as $orgao)
      $selectOptions[$orgao['idorg_rg']] = $orgao['sigla'];

    $selectOptions = Portabilis_Array_Utils::sortByValue($selectOptions);

    $options = array(
      'required'  => false,
      'label'     => '',
      'value'     => $documentos['idorg_exp_rg'],
      'resources' => $selectOptions,
      'inline'    => true
    );

    $this->inputsHelper()->select('orgao_emissao_rg', $options);


    // uf emissгo rg

    $options = array(
      'required' => false,
      'label'    => '',
      'value'    => $documentos['sigla_uf_exp_rg']
    );

    $helperOptions = array(
      'attrName' => 'uf_emissao_rg'
    );

    $this->inputsHelper()->uf($options, $helperOptions);

    // Cуdigo NIS (PIS/PASEP)

    $options = array(
      'required'    => false,
      'label'       => 'NIS (PIS/PASEP)',
      'placeholder' => '',
      'value'       => $this->nis_pis_pasep,
      'max_length'  => 11,
      'size'        => 20
    );

    $this->inputsHelper()->integer('nis_pis_pasep', $options);

    // Carteira do SUS

    $options = array(
      'required'    => false,
      'label'       => 'Nъmero da carteira do SUS',
      'placeholder' => '',
      'value'       => $this->sus,
      'max_length'  => 20,
      'size'        => 20
    );

    $this->inputsHelper()->text('sus', $options);

    // tipo de certidao civil

    $selectOptions = array(
      null                               => 'Tipo certidгo civil',
      'certidao_nascimento_novo_formato' => 'Nascimento (novo formato)',
      91                                 => 'Nascimento (antigo formato)',
      92                                 => 'Casamento'
    );


    // caso certidao nascimento novo formato tenha sido informado,
    // considera este o tipo da certidгo
    if (! empty($documentos['certidao_nascimento']))
      $tipoCertidaoCivil = 'certidao_nascimento_novo_formato';
    else
      $tipoCertidaoCivil = $documentos['tipo_cert_civil'];

    $options = array(
      'required'  => false,
      'label'     => 'Tipo certidгo civil',
      'value'     => $tipoCertidaoCivil,
      'resources' => $selectOptions,
      'inline'    => true
    );

    $this->inputsHelper()->select('tipo_certidao_civil', $options);


    // termo certidao civil

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Termo',
      'value'       => $documentos['num_termo'],
      'max_length'  => 8,
      'inline'      => true
    );

    $this->inputsHelper()->integer('termo_certidao_civil', $options);


    // livro certidao civil

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Livro',
      'value'       => $documentos['num_livro'],
      'max_length'  => 8,
      'size'        => 15,
      'inline'      => true
    );

    $this->inputsHelper()->text('livro_certidao_civil', $options);


    // folha certidao civil

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Folha',
      'value'       => $documentos['num_folha'],
      'max_length'  => 4,
      'inline'      => true
    );

    $this->inputsHelper()->integer('folha_certidao_civil', $options);


    // certidao nascimento (novo padrгo)

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Certidгo nascimento',
      'value'       => $documentos['certidao_nascimento'],
      'max_length'  => 50,
      'size'        => 50
    );

    $this->inputsHelper()->text('certidao_nascimento', $options);


    // uf emissгo certidгo civil

    $options = array(
      'required' => false,
      'label'    => 'Estado emissгo / Data emissгo',
      'value'    => $documentos['sigla_uf_cert_civil'],
      'inline'   => true
    );

    $helperOptions = array(
      'attrName' => 'uf_emissao_certidao_civil'
    );

    $this->inputsHelper()->uf($options, $helperOptions);


    // data emissгo certidгo civil

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Data emissгo',
      'value'       => $documentos['data_emissao_cert_civil']
    );

    $this->inputsHelper()->date('data_emissao_certidao_civil', $options);


    // cartуrio emissгo certidгo civil

    $options = array(
      'required'    => false,
      'label'       => 'Cartуrio emissгo',
      'value'       => $documentos['cartorio_cert_civil'],
      'cols'        => 45,
      'max_length'  => 150
    );

    $this->inputsHelper()->textArea('cartorio_emissao_certidao_civil', $options);


    // carteira de trabalho

    $options = array(
      'required'    => false,
      'label'       => 'Carteira de trabalho / Sйrie',
      'placeholder' => 'Carteira de trabalho',
      'value'       => $documentos['num_cart_trabalho'],
      'max_length'  => 7,
      'inline'      => true

    );

    $this->inputsHelper()->integer('carteira_trabalho', $options);

    // serie carteira de trabalho

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Sйrie',
      'value'       => $documentos['serie_cart_trabalho'],
      'max_length'  => 5
    );

    $this->inputsHelper()->integer('serie_carteira_trabalho', $options);


    // uf emissгo carteira de trabalho

    $options = array(
      'required' => false,
      'label'    => 'Estado emissгo / Data emissгo',
      'value'    => $documentos['sigla_uf_cart_trabalho'],
      'inline'   => true
    );

    $helperOptions = array(
      'attrName' => 'uf_emissao_carteira_trabalho'
    );

    $this->inputsHelper()->uf($options, $helperOptions);


    // data emissгo carteira de trabalho

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Data emissгo',
      'value'       => $documentos['data_emissao_cart_trabalho']
    );

    $this->inputsHelper()->date('data_emissao_carteira_trabalho', $options);


    // titulo eleitor

    $options = array(
      'required'    => false,
      'label'       => 'Titulo eleitor / Zona / Seзгo',
      'placeholder' => 'Titulo eleitor',
      'value'       => $documentos['num_tit_eleitor'],
      'max_length'  => 13,
      'inline'      => true
    );

    $this->inputsHelper()->integer('titulo_eleitor', $options);


    // zona titulo eleitor

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Zona',
      'value'       => $documentos['zona_tit_eleitor'],
      'max_length'  => 4,
      'inline'      => true
    );

    $this->inputsHelper()->integer('zona_titulo_eleitor', $options);


    // seзгo titulo eleitor

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Seзгo',
      'value'       => $documentos['secao_tit_eleitor'],
      'max_length'  => 4
    );

    $this->inputsHelper()->integer('secao_titulo_eleitor', $options);


    // Cor/raзa.

    $racas         = new clsCadastroRaca();
    $racas         = $racas->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, TRUE);
    $selectOptions = array('' => 'Raзa');

    foreach ($racas as $raca)
      $selectOptions[$raca['cod_raca']] = $raca['nm_raca'];

    $selectOptions = Portabilis_Array_Utils::sortByValue($selectOptions);

    $this->campoLista('cor_raca', 'Raзa', $selectOptions, $this->cod_raca, '', FALSE, '', '', '', FALSE);


    // nacionalidade

    // tipos
    $tiposNacionalidade = array(null => 'Selecione',
                                '1'  => 'Brasileiro',
                                '2'  => 'Naturalizado brasileiro',
                                '3'  => 'Estrangeiro');

    $options            = array('label'       => 'Nacionalidade',
                                'resources'   => $tiposNacionalidade,
                                'required'    => false,
                                'inline'      => true,
                                'value'       => $this->tipo_nacionalidade);

    $this->inputsHelper()->select('tipo_nacionalidade', $options);


    // pais origem

    $options = array(
      'label'       => '',
      'placeholder' => 'Informe o nome do pais',
      'required'    => true
    );

    $hiddenInputOptions = array(
      'options' => array('value' => $this->pais_origem_id)
    );

    $helperOptions = array(
      'objectName'         => 'pais_origem',
      'hiddenInputOptions' => $hiddenInputOptions
    );

    $this->inputsHelper()->simpleSearchPais('nome', $options, $helperOptions);


    // naturalidade

    //$options       = array('label' => 'Naturalidade', 'required'   => true);
    $options       = array('label' => 'Naturalidade', 'required'   => $naturalidadeObrigatoria);

    $helperOptions = array('objectName'         => 'naturalidade',
                           'hiddenInputOptions' => array('options' => array('value' => $this->naturalidade_id)));

    $this->inputsHelper()->simpleSearchMunicipio('nome', $options, $helperOptions);


    // Detalhes do Endereзo
    if ($this->idlog){

      $objLogradouro = new clsLogradouro($this->idlog);
      $detalheLogradouro = $objLogradouro->detalhe();
      if ($detalheLogradouro)
        $this->municipio_id = $detalheLogradouro['idmun'];

    // Caso seja um endereзo externo, tentamos entгo recuperar a cidade pelo cep
    }elseif($this->cep){

      $numCep = idFederal2int($this->cep);

      $sql = "SELECT idmun, count(idmun) as count_mun FROM public.logradouro l, urbano.cep_logradouro cl
              WHERE cl.idlog = l.idlog AND cl.cep = '{$numCep}' group by idmun order by count_mun desc limit 1";

      $options = array('return_only' => 'first-field');
      $result = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

      if ($result)
        $this->municipio_id = $result;

    }
    if ($this->cod_pessoa_fj){

      $objPE = new clsPessoaEndereco($this->cod_pessoa_fj);
      $det = $objPE->detalhe();

      if($det){

        $this->bairro_id = $det['idbai'];
        $this->logradouro_id = $det['idlog'];
      }
    }

    if (!($this->bairro_id && $this->municipio_id && $this->logradouro_id)){
      $this->bairro_id = null;
      $this->municipio_id = null;
      $this->logradouro_id = null;
    }
    $this->campoOculto('idbai', $this->idbai);
    $this->campoOculto('idlog', $this->idlog);
    $this->campoOculto('cep', $this->cep);
    $this->campoOculto('ref_sigla_uf', $this->sigla_uf);
    $this->campoOculto('ref_idtlog', $this->idtlog);
    $this->campoOculto('id_cidade', $this->cidade);


    // o endereзamento й opcional
    $enderecamentoObrigatorio = false;

    // Caso o cep jб esteja definido, os campos jб vem desbloqueados inicialmente
    $desativarCamposDefinidosViaCep = empty($this->cep);

    $this->campoRotulo('','<b> Endereзamento</b>', '', '', 'Digite um CEP ou clique na lupa para<br/> busca avanзada para comeзar');

    $this->campoCep(
      'cep_',
      'CEP',
      $this->cep,
      $enderecamentoObrigatorio,
      '-',
      "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel(500, 550, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro2.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=municipio_municipio&campo12=idtlog&campo13=municipio_id&campo14=zona_localizacao\'></iframe>');\">",
      false
    );

    $options       = array('label' => Portabilis_String_Utils::toLatin1('Municнpio'), 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

    $helperOptions = array('objectName'         => 'municipio',
                           'hiddenInputOptions' => array('options' => array('value' => $this->municipio_id)));

    $this->inputsHelper()->simpleSearchMunicipio('municipio', $options, $helperOptions);

    $helperOptions = array('hiddenInputOptions' => array('options' => array('value' => $this->bairro_id)));

    $options       = array( 'label' => Portabilis_String_Utils::toLatin1('Bairro / Zona de Localizaзгo - <b>Buscar</b>'), 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

    $this->inputsHelper()->simpleSearchBairro('bairro', $options, $helperOptions);

    $options = array(
      'label'       => 'Bairro / Zona de Localizaзгo - <b>Cadastrar</b>',
      'placeholder' => 'Bairro',
      'value'       => $this->bairro,
      'max_length'  => 40,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'inline'      => true,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->text('bairro', $options);


    // zona localizaзгo

    $zonas = App_Model_ZonaLocalizacao::getInstance();
    $zonas = $zonas->getEnums();
    $zonas = Portabilis_Array_Utils::insertIn(null, 'Zona localizaзгo', $zonas);

    $options = array(
      'label'       => '',
      'placeholder' => 'Zona localizaзгo ',
      'value'       => $this->zona_localizacao,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'resources'   => $zonas,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->select('zona_localizacao', $options);

    $helperOptions = array('hiddenInputOptions' => array('options' => array('value' => $this->logradouro_id)));

    $options       = array('label' => 'Tipo / Logradouro - <b>Buscar</b>', 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

    $this->inputsHelper()->simpleSearchLogradouro('logradouro', $options, $helperOptions);

    // tipo logradouro

    $options = array(
      'label'       => 'Tipo / Logradouro - <b>Cadastrar</b>',
      'value'       => $this->idtlog,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'inline'      => true,
      'required'    => $enderecamentoObrigatorio
    );

    $helperOptions = array(
      'attrName' => 'idtlog'
    );

    $this->inputsHelper()->tipoLogradouro($options, $helperOptions);


    // logradouro

    $options = array(
      'label'       => '',
      'placeholder' => 'Logradouro',
      'value'       => $this->logradouro,
      'max_length'  => 150,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->text('logradouro', $options);


    // complemento

    $options = array(
      'required'    => false,
      'value'       => $this->complemento,
      'max_length'  => 20
    );

    $this->inputsHelper()->text('complemento', $options);


    // numero

    $options = array(
      'required'    => false,
      'label'       => 'Nъmero / Letra',
      'placeholder' => 'Nъmero',
      'value'       => $this->numero,
      'max_length'  => 6,
      'inline'      => true
    );

    $this->inputsHelper()->integer('numero', $options);


    // letra

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Letra',
      'value'       => $this->letra,
      'max_length'  => 1,
      'size'        => 15
    );

    $this->inputsHelper()->text('letra', $options);


    // apartamento

    $options = array(
      'required'    => false,
      'label'       => 'NЇ apartamento / Bloco / Andar',
      'placeholder' => 'NЇ apartamento',
      'value'       => $this->apartamento,
      'max_length'  => 6,
      'inline'      => true
    );

    $this->inputsHelper()->integer('apartamento', $options);


    // bloco

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Bloco',
      'value'       => $this->bloco,
      'max_length'  => 20,
      'size'        => 15,
      'inline'      => true
    );

    $this->inputsHelper()->text('bloco', $options);


    // andar

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Andar',
      'value'       => $this->andar,
      'max_length'  => 2
    );

    $this->inputsHelper()->integer('andar', $options);


    // contato

    $this->inputTelefone('1', 'Telefone residencial');
    $this->inputTelefone('mov', 'Celular');
    $this->inputTelefone('2', 'Telefone adicional');
    $this->inputTelefone('fax', 'Fax');

    $this->campoTexto('email', 'E-mail', $this->email, '50', '255', FALSE);


    // after change pessoa pai / mae

    if ($parentType)
      $this->inputsHelper()->hidden('parent_type', array('value' => $parentType));


    $styles = array(
      '/modules/Portabilis/Assets/Stylesheets/Frontend.css',
      '/modules/Portabilis/Assets/Stylesheets/Frontend/Resource.css',
      '/modules/Cadastro/Assets/Stylesheets/PessoaFisica.css'
    );

    Portabilis_View_Helper_Application::loadStylesheet($this, $styles);

    $script = array('/modules/Cadastro/Assets/Javascripts/PessoaFisica.js',
                    '/modules/Cadastro/Assets/Javascripts/Endereco.js');
    Portabilis_View_Helper_Application::loadJavascript($this, $script);

    $this->campoCep(
      'cep_',
      'CEP',
      $this->cep,
      $enderecamentoObrigatorio,
      '-',
      "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel(500, 550, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro2.php?campo1=bairro_bairro&campo2=bairro_id&campo3=cep&campo4=logradouro_logradouro&campo5=logradouro_id&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=municipio_municipio&campo12=idtlog&campo13=municipio_id&campo14=zona_localizacao\'></iframe>');\">",
       false
    );
  }

  function Novo() {
    return $this->createOrUpdate();
  }

  function Editar() {
    return $this->createOrUpdate($this->cod_pessoa_fj);
  }

  function Excluir() {
    echo '<script>document.location="atendidos_lst.php";</script>';
    return TRUE;
  }

  function afterChangePessoa($id) {
    Portabilis_View_Helper_Application::embedJavascript($this, "

      if(window.opener &&  window.opener.afterChangePessoa) {
        var parentType = \$j('#parent_type').val();

        if (parentType)
          window.opener.afterChangePessoa(self, parentType, $id, \$j('#nm_pessoa').val());
        else
          window.opener.afterChangePessoa(self, null, $id, \$j('#nm_pessoa').val());
      }
      else
        document.location = 'atendidos_lst.php';

    ", $afterReady = true);
  }

  protected function loadAlunoByPessoaId($id) {
    $aluno            = new clsPmieducarAluno();
    $aluno->ref_idpes = $id;

    return $aluno->detalhe();
  }

  protected function inputPai() {
    $this->addParentsInput('pai');
  }

  protected function inputMae() {
    $this->addParentsInput('mae', 'mгe');
  }

  protected function addParentsInput($parentType, $parentTypeLabel = '') {
    if (! $parentTypeLabel)
      $parentTypeLabel = $parentType;

    if (! isset($this->_aluno))
      $this->_aluno = $this->loadAlunoByPessoaId($this->cod_pessoa_fj);

    $parentId = $this->{$parentType . '_id'};


    // mostra uma dica nos casos em que foi informado apenas o nome dos pais,
    //pela antiga interface do cadastro de alunos.

    if (! $parentId && $this->_aluno['nm_' . $parentType]) {
      $nome      = Portabilis_String_Utils::toLatin1($this->_aluno['nm_' . $parentType],
                                                     array('transform' => true, 'escape' => false));

      $inputHint = '<br /><b>Dica:</b> Foi informado o nome "' . $nome .
                   '" no cadastro de aluno,<br />tente pesquisar esta pessoa ' .
                   'pelo CPF ou RG, caso nгo encontre, cadastre uma nova pessoa.';
    }


    $hiddenInputOptions = array('options' => array('value' => $parentId));
    $helperOptions      = array('objectName' => $parentType, 'hiddenInputOptions' => $hiddenInputOptions);

    $options            = array('label'      => 'Pessoa ' . $parentTypeLabel,
                                'size'       => 50,
                                'required'   => false,
                                'input_hint' => $inputHint);

    $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);
  }

  protected function validatesCpf($cpf) {
    $isValid = true;

    if ($cpf && ! Portabilis_Utils_Validation::validatesCpf($cpf)) {
      $this->erros['id_federal'] = 'CPF invбlido.';
      $isValid = false;
    }
    elseif($cpf) {
      $fisica      = new clsFisica();
      $fisica->cpf = idFederal2int($cpf);
      $fisica      = $fisica->detalhe();

      if ($fisica['cpf'] && $this->cod_pessoa_fj != $fisica['idpes']) {
        $link = "<a class='decorated' target='__blank' href='/intranet/atendidos_cad.php?cod_pessoa_fj=" .
                "{$fisica['idpes']}'>{$fisica['idpes']}</a>";

        $this->erros['id_federal'] = "CPF jб utilizado pela pessoa $link.";
        $isValid = false;
      }
    }

    return $isValid;
  }

  protected function createOrUpdate($pessoaIdOrNull = null) {
    if (! $this->validatesCpf($this->id_federal))
      return false;

    if (!$this->validatePhoto())
      return false;

    $pessoaId = $this->createOrUpdatePessoa($pessoaIdOrNull);

    $this->savePhoto($pessoaId);
    $this->createOrUpdatePessoaFisica($pessoaId);
    $this->createOrUpdateDocumentos($pessoaId);
    $this->createOrUpdateTelefones($pessoaId);
    $this->createOrUpdateEndereco($pessoaId);

    $this->afterChangePessoa($pessoaId);
    return true;
  }


  //envia foto e salva caminha no banco
   protected function savePhoto($id){

     if ($this->objPhoto!=null){

       $caminhoFoto = $this->objPhoto->sendPicture($id);
       if ($caminhoFoto!=''){
         //new clsCadastroFisicaFoto($id)->exclui();
         $obj = new clsCadastroFisicaFoto($id,$caminhoFoto);
         $detalheFoto = $obj->detalhe();
         if (is_array($detalheFoto) && count($detalheFoto)>0)
          $obj->edita();
         else
          $obj->cadastra();

         return true;
       } else{
         echo '<script>alert(\'Foto nгo salva.\')</script>';
         return false;
       }
     }
   }

   // Retorna true caso a foto seja vбlida
   protected function validatePhoto(){

     $this->arquivoFoto = $_FILES["file"];
     if (!empty($this->arquivoFoto["name"])){
       $this->objPhoto = new PictureController($this->arquivoFoto);
       if ($this->objPhoto->validatePicture()){
         return TRUE;
       } else {
         $this->mensagem = $this->objPhoto->getErrorMessage();
         return false;
       }
       return false;
     }else{
       $this->objPhoto = null;
       return true;
     }

   }



  protected function createOrUpdatePessoa($pessoaId = null) {
    $pessoa        = new clsPessoa_();
    $pessoa->idpes = $pessoaId;
    $pessoa->nome  = addslashes($this->nm_pessoa);
    $pessoa->email = addslashes($this->email);

    $sql = "select 1 from cadastro.pessoa WHERE idpes = $1 limit 1";

    if (! $pessoaId || Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
      $pessoa->tipo      = 'F';
      $pessoa->idpes_cad = $this->currentUserId();
      $pessoaId          = $pessoa->cadastra();
    }
    else {
      $pessoa->idpes_rev = $this->currentUserId();
      $pessoa->data_rev  = date('Y-m-d H:i:s', time());
      $pessoa->edita();
    }

    return $pessoaId;
  }

  protected function createOrUpdatePessoaFisica($pessoaId) {
    $fisica                     = new clsFisica();
    $fisica->idpes              = $pessoaId;
    $fisica->data_nasc          = Portabilis_Date_Utils::brToPgSQL($this->data_nasc);
    $fisica->sexo               = $this->sexo;
    $fisica->ref_cod_sistema    = 'NULL';
    $fisica->cpf                = $this->id_federal ? idFederal2int($this->id_federal) : 'NULL';
    $fisica->ideciv             = $this->estado_civil_id;
    $fisica->idpes_pai          = $this->pai_id ? $this->pai_id : "NULL";
    $fisica->idpes_mae          = $this->mae_id ? $this->mae_id : "NULL";
    $fisica->nacionalidade      = $_REQUEST['tipo_nacionalidade'];
    $fisica->idpais_estrangeiro = $_REQUEST['pais_origem_id'];
    $fisica->idmun_nascimento   = $_REQUEST['naturalidade_id'];
    $fisica->sus                = $this->sus;
    $fisica->nis_pis_pasep      = $this->nis_pis_pasep;

    $sql = "select 1 from cadastro.fisica WHERE idpes = $1 limit 1";

    if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1)
      $fisica->cadastra();
    else
      $fisica->edita();

    $this->createOrUpdateRaca($pessoaId, $this->cor_raca);
  }

  function createOrUpdateRaca($pessoaId, $corRaca) {
    $pessoaId = (int) $pessoaId;
    $corRaca  = (int) $corRaca;

    $raca = new clsCadastroFisicaRaca($pessoaId, $corRaca);

    if ($raca->existe())
      return $raca->edita();

    return $raca->cadastra();
  }

  protected function createOrUpdateDocumentos($pessoaId) {
    $documentos                             = new clsDocumento();
    $documentos->idpes                      = $pessoaId;


    // rg

    $documentos->rg                         = $_REQUEST['rg'];

    $documentos->data_exp_rg                = Portabilis_Date_Utils::brToPgSQL(
      $_REQUEST['data_emissao_rg']
    );

    $documentos->idorg_exp_rg               = $_REQUEST['orgao_emissao_rg'];
    $documentos->sigla_uf_exp_rg            = $_REQUEST['uf_emissao_rg'];


    // certidгo civil


    // o tipo certidгo novo padrгo й apenas para exibiзгo ao usuбrio,
    // nгo precisa ser gravado no banco
    //
    // quando selecionado um tipo diferente do novo formato,
    // й removido o valor de certidao_nascimento.
    //
    if ($_REQUEST['tipo_certidao_civil'] == 'certidao_nascimento_novo_formato') {
      $documentos->tipo_cert_civil     = null;
      $documentos->certidao_nascimento = $_REQUEST['certidao_nascimento'];
    }
    else {
      $documentos->tipo_cert_civil     = $_REQUEST['tipo_certidao_civil'];
      $documentos->certidao_nascimento = '';
    }

    $documentos->num_termo                  = $_REQUEST['termo_certidao_civil'];
    $documentos->num_livro                  = $_REQUEST['livro_certidao_civil'];
    $documentos->num_folha                  = $_REQUEST['folha_certidao_civil'];

    $documentos->data_emissao_cert_civil    = Portabilis_Date_Utils::brToPgSQL(
      $_REQUEST['data_emissao_certidao_civil']
    );

    $documentos->sigla_uf_cert_civil        = $_REQUEST['uf_emissao_certidao_civil'];
    $documentos->cartorio_cert_civil        = addslashes($_REQUEST['cartorio_emissao_certidao_civil']);


    // carteira de trabalho

    $documentos->num_cart_trabalho          = $_REQUEST['carteira_trabalho'];
    $documentos->serie_cart_trabalho        = $_REQUEST['serie_carteira_trabalho'];

    $documentos->data_emissao_cart_trabalho = Portabilis_Date_Utils::brToPgSQL(
      $_REQUEST['data_emissao_carteira_trabalho']
    );

    $documentos->sigla_uf_cart_trabalho     = $_REQUEST['uf_emissao_carteira_trabalho'];


    // titulo de eleitor

    $documentos->num_tit_eleitor            = $_REQUEST['titulo_eleitor'];
    $documentos->zona_tit_eleitor           = $_REQUEST['zona_titulo_eleitor'];
    $documentos->secao_tit_eleitor          = $_REQUEST['secao_titulo_eleitor'];


    // Alteraзгo de documentos compativel com a versгo anterior do cadastro,
    // onde era possivel criar uma pessoa, nгo informando os documentos,
    // o que nгo criaria o registro do documento, sendo assim, ao editar uma pessoa,
    // o registro do documento serб criado, caso nгo exista.

    $sql = "select 1 from cadastro.documento WHERE idpes = $1 limit 1";

    if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1)
      $documentos->cadastra();
    else
      $documentos->edita();
  }

  protected function _createOrUpdatePessoaEndereco($pessoaId) {

    $cep = idFederal2Int($this->cep_);

    $objCepLogradouro = new ClsCepLogradouro($cep, $this->logradouro_id);

    if (! $objCepLogradouro->existe())
      $objCepLogradouro->cadastra();

    $objCepLogradouroBairro = new ClsCepLogradouroBairro();
    $objCepLogradouroBairro->cep = $cep;
    $objCepLogradouroBairro->idbai = $this->bairro_id;
    $objCepLogradouroBairro->idlog = $this->logradouro_id;


    if (! $objCepLogradouroBairro->existe())
      $objCepLogradouroBairro->cadastra();

    #die("Morram <br> $cep <br> {$this->bairro_id} <br> {$this->logradouro_id}");
    $endereco = new clsPessoaEndereco(
      $pessoaId,
      $cep,
      $this->logradouro_id,
      $this->bairro_id,
      $this->numero,
      addslashes($this->complemento),
      FALSE,
      addslashes($this->letra),
      addslashes($this->bloco),
      $this->apartamento,
      $this->andar
    );

    // forзado exclusгo, assim ao cadastrar endereco_pessoa novamente,
    // serб excluido endereco_externo (por meio da trigger fcn_aft_ins_endereco_pessoa).
    $endereco->exclui();
    $endereco->cadastra();
  }

  protected function _createOrUpdateEnderecoExterno($pessoaId) {
    $endereco = new clsEnderecoExterno(
      $pessoaId,
      '1',
      $this->idtlog,
      addslashes($this->logradouro),
      $this->numero,
      addslashes($this->letra),
      addslashes($this->complemento),
      addslashes($this->bairro),
      idFederal2int($this->cep_),
      addslashes($this->cidade),
      $this->sigla_uf,
      FALSE,
      addslashes($this->bloco),
      $this->apartamento,
      $this->andar,
      FALSE,
      FALSE,
      $this->zona_localizacao
    );

    // forзado exclusгo, assim ao cadastrar endereco_externo novamente,
    // serб excluido endereco_pessoa (por meio da trigger fcn_aft_ins_endereco_externo).
    $endereco->exclui();
    $endereco->cadastra();
  }

  protected function createOrUpdateEndereco($pessoaId) {

    if ($this->cep_ && is_numeric($this->bairro_id) && is_numeric($this->logradouro_id))
      $this->_createOrUpdatePessoaEndereco($pessoaId);
    else if($this->cep_ && is_numeric($this->municipio_id)){

      if (!is_numeric($this->bairro_id)){
        if ($this->canCreateBairro())
          $this->bairro_id = $this->createBairro();
        else
          return;
      }

      if (!is_numeric($this->logradouro_id)){
        if($this->canCreateLogradouro())
          $this->logradouro_id = $this->createLogradouro();
        else
          return;
      }

      $this->_createOrUpdatePessoaEndereco($pessoaId);

    }else{
      $endereco = new clsPessoaEndereco($pessoaId);
      $endereco->exclui();
    }

  }

  protected function canCreateBairro(){
    return !empty($this->bairro) && !empty($this->zona_localizacao);
  }

  protected function canCreateLogradouro(){
    return !empty($this->logradouro) && !empty($this->idtlog);
  }

  protected function createBairro(){
    $objBairro = new clsBairro(null,$this->municipio_id,null,addslashes($this->bairro), $this->currentUserId());
    $objBairro->zona_localizacao = $this->zona_localizacao;

    return $objBairro->cadastra();
  }

  protected function createLogradouro(){
    $objLogradouro = new clsLogradouro(null,$this->idtlog, $this->logradouro, $this->municipio_id,
                                           null, 'S', $this->currentUserId());
    return $objLogradouro->cadastra();
  }

  protected function createOrUpdateTelefones($pessoaId) {
    $telefones   = array();

    $telefones[] = new clsPessoaTelefone($pessoaId, 1, $this->telefone_1,   $this->ddd_telefone_1);
    $telefones[] = new clsPessoaTelefone($pessoaId, 2, $this->telefone_2,   $this->ddd_telefone_2);
    $telefones[] = new clsPessoaTelefone($pessoaId, 3, $this->telefone_mov, $this->ddd_telefone_mov);
    $telefones[] = new clsPessoaTelefone($pessoaId, 4, $this->telefone_fax, $this->ddd_telefone_fax);

    foreach ($telefones as $telefone)
      $telefone->cadastra();
  }

  // inputs usados em Gerar,
  // implementado estes metodos para nгo duplicar cуdigo
  // uma vez que estes campos sгo usados vбrias vezes em Gerar.

  protected function inputTelefone($type, $typeLabel = '') {
    if (! $typeLabel)
      $typeLabel = "Telefone {$type}";

    // ddd

    $options = array(
      'required'    => false,
      'label'       => "(ddd) / {$typeLabel}",
      'placeholder' => 'ddd',
      'value'       => $this->{"ddd_telefone_{$type}"},
      'max_length'  => 3,
      'size'        => 3,
      'inline'      => true
    );

    $this->inputsHelper()->integer("ddd_telefone_{$type}", $options);


   // telefone

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => $typeLabel,
      'value'       => $this->{"telefone_{$type}"},
      'max_length'  => 11
    );

    $this->inputsHelper()->integer("telefone_{$type}", $options);
  }
}

// Instancia objeto de pбgina
$pagina = new clsIndex();

// Instancia objeto de conteъdo
$miolo = new indice();

// Atribui o conteъdo а pбgina
$pagina->addForm($miolo);

// Gera o cуdigo HTML
$pagina->MakeAll();
