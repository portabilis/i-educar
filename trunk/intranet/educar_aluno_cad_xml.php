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

header('Content-type: text/xml');

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';
require_once 'include/pmieducar/geral.inc.php';

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

if ($_GET['cpf'] || $_GET['idpes']) {
  $xml  = '<?xml version="1.0" encoding="ISO-8859-15"?>' . PHP_EOL;
  $xml .= '<query xmlns="sugestoes">' . PHP_EOL;
  $xml .= '<dados>' . PHP_EOL;

  $cpf = $_GET['cpf'];

  if ($_GET['idpes']) {
    $ref_idpes = $_GET['idpes'];
  }
  else {
    $cpf = idFederal2int($_GET['cpf']);

    $obj_pessoa_fisica = new clsPessoaFisica(NULL, $cpf);
    $lst_pessoa_fisica = $obj_pessoa_fisica->lista(NULL, $cpf);

    if (! $lst_pessoa_fisica) {
      echo $xml . '</dados></query>';
      die();
    }

    $ref_idpes = $lst_pessoa_fisica[0]['idpes'];

    $xml .= sprintf('<ref_idpes>%d</ref_idpes>', $ref_idpes) . PHP_EOL;
    $xml .= sprintf('<cpf>%s</cpf>', $cpf) . PHP_EOL;
  }

  if ($cod_aluno) {
    $obj_matricula = new clsPmieducarMatricula();
    $lst_matricula = $obj_matricula->lista(NULL, NULL, NULL, NULL, NULL,
      NULL, $cod_aluno);
  }

  if (! empty($ref_idpes)) {
    $obj_aluno   = new clsPmieducarAluno();
    $lista_aluno = $obj_aluno->lista(NULL, NULL, NULL, NULL, NULL, $ref_idpes,
      NULL, NULL, NULL, NULL);

    if ($lista_aluno) {
      $det_aluno = array_shift($lista_aluno);
    }
  }

  if ($det_aluno['cod_aluno']) {
    $cod_aluno               = $det_aluno['cod_aluno'];
    $ref_cod_aluno_beneficio = $det_aluno['ref_cod_aluno_beneficio'];
    $ref_cod_religiao        = $det_aluno['ref_cod_religiao'];
    $caminho_foto            = $det_aluno['caminho_foto'];
  }

  $xml .= sprintf('<cod_aluno>%d</cod_aluno>', $cod_aluno) . PHP_EOL;
  $xml .= sprintf('<ref_cod_aluno_beneficio>%d</ref_cod_aluno_beneficio>', $ref_cod_aluno_beneficio) . PHP_EOL;
  $xml .= sprintf('<ref_cod_religiao>%d</ref_cod_religiao>', $ref_cod_religiao) . PHP_EOL;
  $xml .= sprintf('<caminho_foto>%s</caminho_foto>', $caminho_foto) . PHP_EOL;
  $xml .= sprintf('<idpes>%d</idpes>', $ref_idpes) . PHP_EOL;

  if ($ref_idpes != 'NULL') {
    if ($ref_idpes) {
      $obj_pessoa = new clsPessoaFj($ref_idpes);
      $det_pessoa = $obj_pessoa->detalhe();

      $obj_fisica = new clsFisica($ref_idpes);
      $det_fisica = $obj_fisica->detalhe();

      $obj_fisica_raca = new clsCadastroFisicaRaca($ref_idpes);
      $det_fisica_raca = $obj_fisica_raca->detalhe();
      $ref_cod_raca    = $det_fisica_raca['ref_cod_raca'];

      $nome   = $det_pessoa['nome'];
      $email  = $det_pessoa['email'];
      $ideciv = $det_fisica['ideciv']->ideciv;

      $data_nascimento = dataToBrasil($det_fisica['data_nasc']);

      $cpf = $det_fisica['cpf'];

      $xml .= sprintf('<ref_cod_raca>%d</ref_cod_raca>', $ref_cod_raca) . PHP_EOL;
      $xml .= sprintf('<nome>%s</nome>', $nome) . PHP_EOL;
      $xml .= sprintf('<email>%s</email>', $email) . PHP_EOL;
      $xml .= sprintf('<ideciv>%d</ideciv>', $ideciv) . PHP_EOL;
      $xml .= sprintf('<data_nascimento>%s</data_nascimento>', $data_nascimento) . PHP_EOL;
      $xml .= sprintf('<cpf>%s</cpf>', $cpf) . PHP_EOL;

      $cpf2 = int2CPF($cpf);
      $xml .= sprintf('<cpf_2>%s</cpf_2>', $cpf2) . PHP_EOL;

      $obj_documento     = new clsDocumento($ref_idpes);
      $obj_documento_det = $obj_documento->detalhe();

      $ddd_fone_1 = $det_pessoa['ddd_1'];
      $fone_1     = $det_pessoa['fone_1'];

      $ddd_mov  = $det_pessoa['ddd_mov'];
      $fone_mov = $det_pessoa['fone_mov'];

      $email = $det_pessoa['email'];
      $url   = $det_pessoa['url'];

      $sexo = $det_fisica['sexo'];

      $nacionalidade    = $det_fisica['nacionalidade'];
      $idmun_nascimento = $det_fisica['idmun_nascimento']->idmun;

      $xml .= sprintf('<ddd_fone_1>%s</ddd_fone_1>', $ddd_fone_1) . PHP_EOL;
      $xml .= sprintf('<fone_1>%s</fone_1>', $fone_1) . PHP_EOL;
      $xml .= sprintf('<ddd_mov>%s</ddd_mov>', $ddd_mov) . PHP_EOL;
      $xml .= sprintf('<fone_mov>%s</fone_mov>', $fone_mov) . PHP_EOL;
      $xml .= sprintf('<email>%s</email>', $email) . PHP_EOL;
      $xml .= sprintf('<url>%s</url>', $url) . PHP_EOL;
      $xml .= sprintf('<sexo>%s</sexo>', $sexo) . PHP_EOL;
      $xml .= sprintf('<nacionalidade>%d</nacionalidade>', $nacionalidade) . PHP_EOL;
      $xml .= sprintf('<idmun_nascimento>%d</idmun_nascimento>', $idmun_nascimento) . PHP_EOL;

      $detalhe_pais_origem = $det_fisica['idpais_estrangeiro']->detalhe();
      $pais_origem         = $detalhe_pais_origem['idpais'];

      $ref_idpes_responsavel = $det_fisica['idpes_responsavel'];
      $idpes_pai             = $det_fisica['idpes_pai'];
      $idpes_mae             = $det_fisica['idpes_mae'];

      $xml .= sprintf('<idpes_pai>%d</idpes_pai>', $idpes_pai) . PHP_EOL;
      $xml .= sprintf('<idpes_mae>%d</idpes_mae>', $idpes_mae) . PHP_EOL;

      $obj_aluno = new clsPmieducarAluno(NULL, NULL, NULL, NULL, NULL, $ref_idpes);

      $detalhe_aluno = $obj_aluno->detalhe();

      if ($detalhe_aluno) {
        $nm_pai = $detalhe_aluno['nm_pai'];
        $nm_mae = $detalhe_aluno['nm_mae'];

        $xml .= sprintf('<nm_pai>%s</nm_pai>', $nm_pai) . PHP_EOL;
        $xml .= sprintf('<nm_mae>%s</nm_mae>', $nm_mae) . PHP_EOL;
      }

      $obj_endereco = new clsPessoaEndereco($ref_idpes);

      $zona = NULL;

      if ($obj_endereco_det = $obj_endereco->detalhe()) {
        $isEnderecoExterno = 0;

        $id_cep         = $obj_endereco_det['cep']->cep;
        $id_bairro      = $obj_endereco_det['idbai']->idbai;
        $id_logradouro  = $obj_endereco_det['idlog']->idlog;
        $numero         = $obj_endereco_det['numero'];
        $letra          = $obj_endereco_det['letra'];
        $complemento    = $obj_endereco_det['complemento'];
        $andar          = $obj_endereco_det['andar'];
        $apartamento    = $obj_endereco_det['apartamento'];
        $bloco          = $obj_endereco_det['bloco'];
        $ref_idtlog     = $obj_endereco_det['idtlog'];
        $nm_bairro      = $obj_endereco_det['bairro'];
        $nm_logradouro  = $obj_endereco_det['logradouro'];
        $zona           = $obj_endereco_det['zona_localizacao'];

        $cep_ = int2CEP($id_cep);

        $xml .= sprintf('<id_cep>%d</id_cep>', $id_cep) . PHP_EOL;
        $xml .= sprintf('<id_bairro>%d</id_bairro>', $id_bairro) . PHP_EOL;
        $xml .= sprintf('<id_logradouro>%d</id_logradouro>', $id_logradouro) . PHP_EOL;
        $xml .= sprintf('<numero>%s</numero>', $numero) . PHP_EOL;
        $xml .= sprintf('<letra>%s</letra>', $letra) . PHP_EOL;
        $xml .= sprintf('<complemento>%s</complemento>', $complemento) . PHP_EOL;
        $xml .= sprintf('<andar>%s</andar>', $andar) . PHP_EOL;
        $xml .= sprintf('<apartamento>%s</apartamento>', $apartamento) . PHP_EOL;
        $xml .= sprintf('<bloco>%s</bloco>', $bloco) . PHP_EOL;
        $xml .= sprintf('<ref_idtlog>%s</ref_idtlog>', $ref_idtlog) . PHP_EOL;
        $xml .= sprintf('<nm_bairro>%s</nm_bairro>', $nm_bairro) . PHP_EOL;
        $xml .= sprintf('<nm_logradouro>%s</nm_logradouro>', $nm_logradouro) . PHP_EOL;
      }
      else {
        $obj_endereco = new clsEnderecoExterno($ref_idpes);

        if ($obj_endereco_det = $obj_endereco->detalhe()) {
          $isEnderecoExterno = 1;

          $id_cep        = $obj_endereco_det['cep'];
          $cidade        = $obj_endereco_det['cidade'];
          $nm_bairro     = $obj_endereco_det['bairro'];
          $nm_logradouro = $obj_endereco_det['logradouro'];

          $id_bairro     = NULL;
          $id_logradouro = NULL;
          $numero        = $obj_endereco_det['numero'];
          $letra         = $obj_endereco_det['letra'];
          $complemento   = $obj_endereco_det['complemento'];
          $andar         = $obj_endereco_det['andar'];
          $apartamento   = $obj_endereco_det['apartamento'];
          $bloco         = $obj_endereco_det['bloco'];
          $zona          = $obj_endereco_det['zona_localizacao'];

          $ref_idtlog   = $idtlog        = $obj_endereco_det['idtlog']->idtlog;
          $ref_sigla_uf = $ref_sigla_uf_ = $obj_endereco_det['sigla_uf']->sigla_uf;
          $cep_         = int2CEP($id_cep);

          $xml .= sprintf('<id_cep>%s</id_cep>', $id_cep) . PHP_EOL;
          $xml .= sprintf('<cidade>%s</cidade>', $cidade) . PHP_EOL;
          $xml .= sprintf('<nm_bairro>%s</nm_bairro>', $nm_bairro) . PHP_EOL;
          $xml .= sprintf('<nm_logradouro>%s</nm_logradouro>', $nm_logradouro) . PHP_EOL;
          $xml .= sprintf('<numero>%s</numero>', $numero) . PHP_EOL;
          $xml .= sprintf('<letra>%s</letra>', $letra) . PHP_EOL;
          $xml .= sprintf('<complemento>%s</complemento>', $complemento) . PHP_EOL;
          $xml .= sprintf('<andar>%s</andar>', $andar) . PHP_EOL;
          $xml .= sprintf('<apartamento>%s</apartamento>', $apartamento) . PHP_EOL;
          $xml .= sprintf('<bloco>%s</bloco>', $bloco) . PHP_EOL;
          $xml .= sprintf('<ref_idtlog>%s</ref_idtlog>', $ref_idtlog) . PHP_EOL;
          $xml .= sprintf('<idtlog>%s</idtlog>', $idtlog) . PHP_EOL;
          $xml .= sprintf('<ref_sigla_uf>%s</ref_sigla_uf>', $ref_sigla_uf) . PHP_EOL;
          $xml .= sprintf('<ref_sigla_uf_>%s</ref_sigla_uf_>', $ref_sigla_uf_) . PHP_EOL;
          $xml .= sprintf('<cep_>%s</cep_>', $cep_) . PHP_EOL;
        }
      }

      if (isset($zona)) {
        $xml .= sprintf('<zona_localizacao>%s</zona_localizacao>', $zona) . PHP_EOL;
      }
    }
  }

  if ($isEnderecoExterno == 0) {
    $obj_bairro = new clsBairro($id_bairro);
    $cep_       = int2CEP($id_cep);

    $xml .= sprintf('<cep_>%s</cep_>', $cep_) . PHP_EOL;

    $obj_bairro_det = $obj_bairro->detalhe();

    if ($obj_bairro_det) {
      $nm_bairro = $obj_bairro_det['nome'];
      $xml      .= sprintf('<nm_bairro>%s</nm_bairro>', $nm_bairro) . PHP_EOL;
    }

    $obj_log     = new clsLogradouro($id_logradouro);
    $obj_log_det = $obj_log->detalhe();

    if ($obj_log_det) {
      $nm_logradouro = $obj_log_det['nome'];
      $ref_idtlog    = $obj_log_det['idtlog']->idtlog;

      $xml .= sprintf('<nm_logradouro>%s</nm_logradouro>', $nm_logradouro) . PHP_EOL;
      $xml .= sprintf('<ref_idtlog>%s</ref_idtlog>', $ref_idtlog) . PHP_EOL;

      $obj_mun = new clsMunicipio($obj_log_det['idmun']);
      $det_mun = $obj_mun->detalhe();

      if ($det_mun) {
        $cidade = ucfirst(strtolower($det_mun['nome']));
        $xml   .= sprintf('<cidade>%s</cidade>', $cidade) . PHP_EOL;
      }

      $ref_sigla_uf = $ref_sigla_uf_ = $det_mun['sigla_uf']->sigla_uf;

      $xml .= sprintf('<ref_sigla_uf>%s</ref_sigla_uf>', $ref_sigla_uf) . PHP_EOL;
      $xml .= sprintf('<ref_sigla_uf_>%s</ref_sigla_uf_>', $ref_sigla_uf_) . PHP_EOL;
    }

    $obj_bairro     = new clsBairro($obj_endereco_det['ref_idbai']);
    $obj_bairro_det = $obj_bairro->detalhe();

    if ($obj_bairro_det) {
      $nm_bairro = $obj_bairro_det['nome'];
      $xml      .= sprintf('<nm_bairro>%s</nm_bairro>', $nm_bairro) . PHP_EOL;
    }
  }

  if ($idpes_pai) {
    $obj_pessoa_pai = new clsPessoaFj($idpes_pai);
    $det_pessoa_pai = $obj_pessoa_pai->detalhe();

    if ($det_pessoa_pai) {
      $nm_pai = $det_pessoa_pai['nome'];
      $xml   .= sprintf('<nm_pai>%s</nm_pai>', $nm_pai) . PHP_EOL;

      $obj_cpf = new clsFisica($idpes_pai);
      $det_cpf = $obj_cpf->detalhe();

      if ($det_cpf['cpf']) {
        $cpf_pai = int2CPF($det_cpf['cpf']);
        $xml    .= sprintf('<cpf_pai>%s</cpf_pai>', $cpf_pai) . PHP_EOL;
      }
    }
  }

  if ($idpes_mae) {
    $obj_pessoa_mae = new clsPessoaFj($idpes_mae);
    $det_pessoa_mae = $obj_pessoa_mae->detalhe();

    if ($det_pessoa_mae) {
      $nm_mae = $det_pessoa_mae['nome'];
      $xml   .= sprintf('<nm_mae>%s</nm_mae>', $nm_mae) . PHP_EOL;

      // CPF
      $obj_cpf = new clsFisica($idpes_mae);
      $det_cpf = $obj_cpf->detalhe();

      if ($det_cpf['cpf']) {
        $cpf_mae = int2CPF($det_cpf['cpf']);
        $xml .= sprintf('<cpf_mae>%s</cpf_mae>', $cpf_mae) . PHP_EOL;
      }
    }
  }

  if (! $tipo_responsavel) {
    if ($nm_pai) {
      $tipo_responsavel = 'p';
    }
    elseif ($nm_mae) {
      $tipo_responsavel = 'm';
    }
    elseif ($ref_idpes_responsavel) {
      $tipo_responsavel = 'r';
    }

    $xml .= sprintf('<tipo_responsavel>%s</tipo_responsavel>', $tipo_responsavel) . PHP_EOL;
  }

  if ($ref_idpes) {
    $ObjDocumento     = new clsDocumento($ref_idpes);
    $detalheDocumento = $ObjDocumento->detalhe();

    $rg = $detalheDocumento['rg'];

    $xml .= sprintf('<rg>%s</rg>', $rg);

    if ($detalheDocumento['data_exp_rg']) {
      $data_exp_rg = date(
        'd/m/Y', strtotime(substr($detalheDocumento['data_exp_rg'], 0, 19))
      );

      $xml .= sprintf('<data_exp_rg>%s</data_exp_rg>', $data_exp_rg) . PHP_EOL;
    }

    $sigla_uf_exp_rg = $detalheDocumento['sigla_uf_exp_rg'];
    $tipo_cert_civil = $detalheDocumento['tipo_cert_civil'];
    $num_termo       = $detalheDocumento['num_termo'];
    $num_livro       = $detalheDocumento['num_livro'];
    $num_folha       = $detalheDocumento['num_folha'];

    $xml .= sprintf('<sigla_uf_exp_rg>%s</sigla_uf_exp_rg>', $sigla_uf_exp_rg) . PHP_EOL;
    $xml .= sprintf('<tipo_cert_civil>%s</tipo_cert_civil>', $tipo_cert_civil) . PHP_EOL;
    $xml .= sprintf('<num_termo>%s</num_termo>', $num_termo) . PHP_EOL;
    $xml .= sprintf('<num_livro>%s</num_livro>', $num_livro) . PHP_EOL;
    $xml .= sprintf('<num_folha>%s</num_folha>', $num_folha) . PHP_EOL;

    if ($detalheDocumento['data_emissao_cert_civil']) {
      $data_emissao_cert_civil = date(
        'd/m/Y', strtotime(
          substr($detalheDocumento['data_emissao_cert_civil'], 0, 19)
        )
      );

      $xml .= sprintf('<data_emissao_cert_civil>%s</data_emissao_cert_civil>',
        $data_emissao_cert_civil) . PHP_EOL;
    }

    $sigla_uf_cert_civil = $detalheDocumento['sigla_uf_cert_civil'];
    $cartorio_cert_civil = $detalheDocumento['cartorio_cert_civil'];
    $num_cart_trabalho   = $detalheDocumento['num_cart_trabalho'];
    $serie_cart_trabalho = $detalheDocumento['serie_cart_trabalho'];

    $xml .= sprintf(
      '<sigla_uf_cert_civil>%s</sigla_uf_cert_civil>',
      $sigla_uf_cert_civil
    ) . PHP_EOL;

    $xml .= sprintf(
      '<cartorio_cert_civil>%s</cartorio_cert_civil>',
      $cartorio_cert_civil
    ) . PHP_EOL;

    $xml .= sprintf(
      '<num_cart_trabalho>%s</num_cart_trabalho>',
      $num_cart_trabalho
    ) . PHP_EOL;

    $xml .= sprintf(
      '<serie_cart_trabalho>%s</serie_cart_trabalho>',
      $serie_cart_trabalho
    ) . PHP_EOL;

    if ($detalheDocumento['data_emissao_cart_trabalho']) {
      $data_emissao_cart_trabalho = date('d/m/Y',
        strtotime(substr($detalheDocumento['data_emissao_cart_trabalho'], 0, 19))
      );

      $xml .= sprintf(
        '<data_emissao_cart_trabalho>%s</data_emissao_cart_trabalho>',
        $data_emissao_cart_trabalho
      ) . PHP_EOL;
    }

    $sigla_uf_cart_trabalho = $detalheDocumento['sigla_uf_cart_trabalho'];
    $num_tit_eleitor        = $detalheDocumento['num_tit_eleitor'];
    $zona_tit_eleitor       = $detalheDocumento['zona_tit_eleitor'];
    $secao_tit_eleitor      = $detalheDocumento['secao_tit_eleitor'];
    $idorg_exp_rg           = $detalheDocumento['idorg_exp_rg'];

    $xml .= sprintf(
      '<sigla_uf_cart_trabalho>%s</sigla_uf_cart_trabalho>',
      $sigla_uf_cart_trabalho
    ) . PHP_EOL;

    $xml .= sprintf(
      '<num_tit_eleitor>%s</num_tit_eleitor>',
      $num_tit_eleitor
    ) . PHP_EOL;

    $xml .= sprintf(
      '<zona_tit_eleitor>%s</zona_tit_eleitor>',
      $zona_tit_eleitor
    ) . PHP_EOL;

    $xml .= sprintf(
      '<secao_tit_eleitor>%s</secao_tit_eleitor>',
      $secao_tit_eleitor
    ) . PHP_EOL;

    $xml .= sprintf('<idorg_exp_rg>%s</idorg_exp_rg>', $idorg_exp_rg) . PHP_EOL;
  }

  $xml .= '</dados>' . PHP_EOL;
  $xml .= '</query>';

  echo $xml;
}