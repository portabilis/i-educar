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
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Include
 *
 * @since     Arquivo disponível desde a versão 1.0.0
 *
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

require_once 'App/Model/SimNao.php';
require_once 'App/Model/ZonaLocalizacao.php';
require_once 'Educacenso/Model/AlunoDataMapper.php';
require_once 'Transporte/Model/AlunoDataMapper.php';
require_once 'Transporte/Model/Responsavel.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Pmieducar
 *
 * @since     Classe disponível desde a versão 1.0.0
 *
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Aluno');
        $this->processoAp = 578;
    }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Pmieducar
 *
 * @since     Classe disponível desde a versão 1.0.0
 *
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
    public $pessoa_logada;

    public $cod_aluno;
    public $ref_idpes_responsavel;
    public $idpes_pai;
    public $idpes_mae;
    public $ref_cod_aluno_beneficio;
    public $ref_cod_religiao;
    public $ref_idpes;
    public $nm_mae;
    public $cpf_mae;
    public $nm_pai;
    public $cpf_pai;

    public $ref_cod_raca;

    public $foto_excluida = 0;
    public $analfabeto;
    public $foto_antiga;

    public $passo;
    public $nome;

    public $caminho_foto;

    public $email;
    public $ddd_fone_1;
    public $fone_1;
    public $ddd_fone_2;
    public $fone_2;
    public $ddd_mov;
    public $fone_mov;
    public $ddd_fax;
    public $fone_fax;

    //fisica
    public $data_nascimento;
    public $sexo;
    public $cpf;

    //juridica
    public $cnpj;
    public $nm_fantasia;
    public $insc_estadual;
    public $nome_contato;

    public $foto;
    public $tipo_responsavel;

    public $id_bairro;
    public $id_logradouro;

    public $idtlog;

    public $numero;
    public $letra;
    public $complemento;
    public $andar;
    public $apartamento;
    public $bloco;
    public $deficiencia;
    public $deficiencia_exc;

    //endereco_externo
    public $ref_sigla_uf;
    public $ref_sigla_uf_;
    public $ref_idtlog;
    public $id_cidade;
    public $id_cep;
    public $ref_idtlog_;
    public $id_log;

    public $pais_origem;

    public $cep_;
    public $nm_bairro;
    public $nm_logradouro;

    public $ref_cod_pessoa_deficiencia;

    public $isEnderecoExterno  = 0;

    public $endereco_original_is_externo;

    public $cpf_;
    public $ideciv;

    public $inc;
    public $exc;
    public $back;

    public $url;

    public $nacionalidade;
    public $idmun_nascimento;

    public $rg;
    public $data_exp_rg;
    public $sigla_uf_exp_rg;
    public $tipo_cert_civil;
    public $num_termo;
    public $num_livro;
    public $num_folha;
    public $data_emissao_cert_civil;
    public $sigla_uf_cert_civil;
    public $cartorio_cert_civil;
    public $num_cart_trabalho;
    public $serie_cart_trabalho;
    public $data_emissao_cart_trabalho;
    public $sigla_uf_cart_trabalho;
    public $num_tit_eleitor;
    public $zona_tit_eleitor;
    public $secao_tit_eleitor;
    public $idorg_exp_rg;

    public $sem_cpf;

    public $ref_cod_sistema;

    public $retorno;
    public $tab_habilitado;

    public function Inicializar()
    {
        session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->tab_habilitado = true;

        $this->cod_aluno= $_GET['cod_aluno'];

        $this->retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, 'educar_aluno_lst.php');

        if (is_numeric($this->cod_aluno)) {
            $ref_cod_aluno = $this->cod_aluno;
            $obj = new clsPmieducarAluno($this->cod_aluno);
            $registro = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $obj_matricula = new clsPmieducarMatricula();
                $lst_matricula = $obj_matricula->lista(null, null, null, null, null, null, $this->cod_aluno);
                if ($lst_matricula) {
                    //** verificao de permissao para exclusao
                    $this->fexcluir = $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7);
                }

                $this->retorno = 'Editar';
                $this->tab_habilitado = false;
            }
        }

        $this->url_cancelar = $this->cod_aluno ? 'educar_aluno_det.php?cod_aluno=' . $this->cod_aluno : 'educar_aluno_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        return $this->retorno;
    }

    public function Gerar()
    {
        $this->fexcluir = false;
        $this->campoTabInicio('educar_cad', '', true);
        $this->campoOculto('retorno', $this->retorno);
        $this->campoOculto('bloqueado', 1);

        if (is_int((int)$this->cpf) && !empty($this->cpf)) {
            $cpf = int2CPF($this->cpf);
        } else {
            $cpf = $this->cpf;
        }

        if (!$this->cod_aluno) {
            $this->campoAdicionaTab('CPF', $this->tab_habilitado);
            $opcoes = ['' => 'Pesquise a pessoa clicando na lupa ao lado'];
            $this->campoCpf(
          'cpf_',
          'CPF',
          $cpf,
          false,
        '<img border="0" onclick="pesquisa_valores_popless(\'educar_pesquisa_aluno_lst2.php?campo1=ref_idpes&campo3=cpf&campo4=cpf_\', \'nome\')" src="imagens/lupa.png">'
      );
        }

        $this->campoOculto('ref_idpes', $this->ref_idpes);
        $this->campoAdicionaTab('Dados Pessoais', $this->tab_habilitado);

        if (!empty($this->ref_idpes)) {
            $obj_aluno = new clsPmieducarAluno();
            $lista_aluno = $obj_aluno->lista(
          null,
          null,
          null,
          null,
          null,
        $this->ref_idpes,
          null,
          null,
          null,
          null
      );

            if ($lista_aluno) {
                $det_aluno = array_shift($lista_aluno);
            }
        }

        if ($det_aluno['cod_aluno']) {
            $this->cod_aluno               = $det_aluno['cod_aluno'];
            $this->ref_cod_aluno_beneficio = $det_aluno['ref_cod_aluno_beneficio'];
            $this->ref_cod_religiao        = $det_aluno['ref_cod_religiao'];
            $this->caminho_foto            = $det_aluno['caminho_foto'];
        }

        $this->campoOculto('cod_aluno', $this->cod_aluno);
        $this->campoOculto('ref_idpes', $this->ref_idpes);

        if ($this->ref_idpes != 'NULL') {
            if ($this->ref_idpes) {
                $obj_pessoa = new clsPessoaFj($this->ref_idpes);
                $det_pessoa = $obj_pessoa->detalhe();

                $obj_fisica = new clsFisica($this->ref_idpes);
                $det_fisica = $obj_fisica->detalhe();

                $obj_fisica_raca = new clsCadastroFisicaRaca($this->ref_idpes);
                $det_fisica_raca = $obj_fisica_raca->detalhe();
                $this->ref_cod_raca = $det_fisica_raca['ref_cod_raca'];

                $this->nome  = $det_pessoa['nome'];

                $this->email =  $det_pessoa['email'];

                $this->ideciv = $det_fisica['ideciv']->ideciv;

                $this->data_nascimento = dataToBrasil($det_fisica['data_nasc']);

                $this->cpf = $det_fisica['cpf'];

                $obj_documento = new clsDocumento($this->ref_idpes);
                $obj_documento_det = $obj_documento->detalhe();

                $this->ddd_fone_1 = $det_pessoa['ddd_1'];
                $this->fone_1     = $det_pessoa['fone_1'];

                $this->ddd_fone_2 = $det_pessoa['ddd_2'];
                $this->fone_2     = $det_pessoa['fone_2'];

                $this->ddd_fax  = $det_pessoa['ddd_fax'];
                $this->fone_fax = $det_pessoa['fone_fax'];

                $this->ddd_mov  = $det_pessoa['ddd_mov'];
                $this->fone_mov = $det_pessoa['fone_mov'];

                $this->email = $det_pessoa['email'];
                $this->url   = $det_pessoa['url'];

                $this->sexo       = $det_fisica['sexo'];

                $this->nacionalidade    = $det_fisica['nacionalidade'];
                $this->idmun_nascimento = $det_fisica['idmun_nascimento']->idmun;

                $detalhe_pais_origem = $det_fisica['idpais_estrangeiro']->detalhe();
                $this->pais_origem   = $detalhe_pais_origem['idpais'];

                $this->ref_idpes_responsavel = $det_fisica['idpes_responsavel'];

                $this->idpes_pai = $det_fisica['idpes_pai'];
                $this->idpes_mae = $det_fisica['idpes_mae'];

                $obj_aluno = new clsPmieducarAluno(null, null, null, null, null, $this->ref_idpes);
                $detalhe_aluno = $obj_aluno->detalhe();

                if ($detalhe_aluno) {
                    $this->nm_pai = $detalhe_aluno['nm_pai'];
                    $this->nm_mae = $detalhe_aluno['nm_mae'];
                }

                $obj_endereco = new clsPessoaEndereco($this->ref_idpes);

                if ($obj_endereco_det = $obj_endereco->detalhe()) {
                    $this->isEnderecoExterno = 0;

                    $this->id_cep        = $obj_endereco_det['cep']->cep;
                    $this->id_bairro     = $obj_endereco_det['idbai']->idbai;
                    $this->id_logradouro = $obj_endereco_det['idlog']->idlog;
                    $this->numero        = $obj_endereco_det['numero'];
                    $this->letra         = $obj_endereco_det['letra'];
                    $this->complemento   = $obj_endereco_det['complemento'];
                    $this->andar         = $obj_endereco_det['andar'];
                    $this->apartamento   = $obj_endereco_det['apartamento'];
                    $this->bloco         = $obj_endereco_det['bloco'];

                    $this->ref_idtlog    = $obj_endereco_det['idtlog'];
                    $this->nm_bairro     = $obj_endereco_det['bairro'];
                    $this->nm_logradouro = $obj_endereco_det['logradouro'];

                    $this->cep_ = int2CEP($this->id_cep);
                } else {
                    $obj_endereco = new clsEnderecoExterno($this->ref_idpes);

                    if ($obj_endereco_det = $obj_endereco->detalhe()) {
                        $this->isEnderecoExterno = 1;

                        $this->id_cep        = $obj_endereco_det['cep'];
                        $this->cidade        = $obj_endereco_det['cidade'];
                        $this->nm_bairro     = $obj_endereco_det['bairro'];
                        $this->nm_logradouro = $obj_endereco_det['logradouro'];

                        $this->id_bairro     = null;
                        $this->id_logradouro = null;
                        $this->numero        = $obj_endereco_det['numero'];
                        $this->letra         = $obj_endereco_det['letra'];
                        $this->complemento   = $obj_endereco_det['complemento'];
                        $this->andar         = $obj_endereco_det['andar'];
                        $this->apartamento   = $obj_endereco_det['apartamento'];
                        $this->bloco         = $obj_endereco_det['bloco'];

                        $this->ref_idtlog = $this->idtlog          = $obj_endereco_det['idtlog']->idtlog;
                        $this->ref_sigla_uf = $this->ref_sigla_uf_ = $obj_endereco_det['sigla_uf']->sigla_uf;
                        $this->cep_ = int2CEP($this->id_cep);
                    }
                }

                $this->zona_localizacao = $obj_endereco_det['zona_localizacao'];
            }
        }

        if ($this->isEnderecoExterno == 0) {
            $obj_bairro = new clsBairro($this->id_bairro);
            $this->cep_ = int2CEP($this->id_cep);

            $obj_bairro_det = $obj_bairro->detalhe();

            if ($obj_bairro_det) {
                $this->nm_bairro = $obj_bairro_det['nome'];
            }

            $obj_log = new clsLogradouro($this->id_logradouro);
            $obj_log_det = $obj_log->detalhe();

            if ($obj_log_det) {
                $this->nm_logradouro = $obj_log_det['nome'];
                $this->ref_idtlog    = $obj_log_det['idtlog']->idtlog;

                $obj_mun = new clsMunicipio($obj_log_det['idmun']);
                $det_mun = $obj_mun->detalhe();

                if ($det_mun) {
                    $this->cidade = ucfirst(strtolower($det_mun['nome']));
                }

                $this->ref_sigla_uf = $this->ref_sigla_uf_ =  $det_mun['sigla_uf']->sigla_uf;
            }

            $obj_bairro = new clsBairro($obj_endereco_det['ref_idbai']);
            $obj_bairro_det = $obj_bairro->detalhe();

            if ($obj_bairro_det) {
                $this->nm_bairro = $obj_bairro_det['nome'];
            }
        }

        $this->campoTexto('nome', 'Nome', $this->nome, 30, 100, true);

        if ($this->cpf && $this->ref_idpes) {
            if (!$this->cpf) {
                $this->campoRotulo('cpf_2', 'CPF', $this->cpf);
            } else {
                $this->campoRotulo('cpf_2', 'CPF', int2CPF($this->cpf));
            }
        } else {
            if (!$this->cpf) {
                $this->campoCpf('cpf_2', 'CPF', $this->cpf);
                $this->campoOculto('sem_cpf', 1);
            } else {
                $this->campoCpf('cpf_2', 'CPF', int2CPF($this->cpf), false);
            }
        }

        $this->campoData('data_nascimento', 'Data de Nascimento', $this->data_nascimento, true);

        $lista = [
      ''  => 'Selecione',
      'F' => 'Feminino',
      'M' => 'Masculino'
    ];

        $this->campoLista('sexo', 'Sexo', $lista, $this->sexo);

        $obj_estado_civil = new clsEstadoCivil();
        $obj_estado_civil_lista = $obj_estado_civil->lista();

        $lista_estado_civil = ['' => 'Selecione'];

        if ($obj_estado_civil_lista) {
            foreach ($obj_estado_civil_lista as $estado_civil) {
                $lista_estado_civil[$estado_civil['ideciv']] = $estado_civil['descricao'];
            }
        }

        $this->campoLista('ideciv', 'Estado Civil', $lista_estado_civil, $this->ideciv);

        $obj_religiao = new clsPmieducarReligiao();
        $obj_religia_lista = $obj_religiao->lista(
        null,
        null,
        null,
        null,
        null,
      null,
        null,
        null,
        1
    );

        $lista_religiao = ['NULL' => 'Selecione'];
        if ($obj_religia_lista) {
            foreach ($obj_religia_lista as $religiao) {
                $lista_religiao[$religiao['cod_religiao']] = $religiao['nm_religiao'];
            }
        }

        $this->campoLista(
        'ref_cod_religiao',
        'Religi&atilde;o',
        $lista_religiao,
      $this->ref_cod_religiao,
        '',
        false,
        '',
        '',
        '',
        false
    );

        $opcoes_raca = ['' => 'Selecione'];
        $obj_raca = new clsCadastroRaca();
        $lst_raca = $obj_raca->lista(null, null, null, null, null, null, null, true);

        if ($lst_raca) {
            foreach ($lst_raca as $raca) {
                $opcoes_raca[$raca['cod_raca']] = $raca['nm_raca'];
            }
        }

        $this->campoLista(
        'ref_cod_raca',
        'Ra&ccedil;a',
        $opcoes_raca,
      $this->ref_cod_raca,
        '',
        false,
        '',
        '',
        '',
        false
    );

        $this->campoQuebra2('#224488');

        if ($this->idpes_pai) {
            $obj_pessoa_pai = new clsPessoaFj($this->idpes_pai);
            $det_pessoa_pai = $obj_pessoa_pai->detalhe();

            if ($det_pessoa_pai) {
                $this->nm_pai = $det_pessoa_pai['nome'];

                // cpf
                $obj_cpf = new clsFisica($this->idpes_pai);
                $det_cpf = $obj_cpf->detalhe();

                if ($det_cpf['cpf']) {
                    $this->cpf_pai = int2CPF($det_cpf['cpf']);
                }
            }
        }

        if ($this->idpes_mae) {
            $obj_pessoa_mae = new clsPessoaFj($this->idpes_mae);
            $det_pessoa_mae = $obj_pessoa_mae->detalhe();

            if ($det_pessoa_mae) {
                $this->nm_mae = $det_pessoa_mae['nome'];

                //cpf
                $obj_cpf = new clsFisica($this->idpes_mae);
                $det_cpf = $obj_cpf->detalhe();

                if ($det_cpf['cpf']) {
                    $this->cpf_mae = int2CPF($det_cpf['cpf']);
                }
            }
        }

        $this->campoTexto('nm_pai', 'Nome do Pai', $this->nm_pai, 30, 255, false);

        $this->campoCpf(
        'cpf_pai',
        'CPF pai',
        $this->cpf_pai,
        false,
      $this->cpf_pai ? '': ' &nbsp; &nbsp; (preencher sempre que possível)'
    );

        $this->campoTexto('nm_mae', 'Nome da M&atilde;e', $this->nm_mae, 30, 255, false);
        $this->campoCpf(
        'cpf_mae',
        'CPF mãe',
        $this->cpf_mae,
        false,
      $this->cpf_mae ? '': ' &nbsp; &nbsp; (preencher sempre que possível)'
    );

        $lista = ['' => 'Responsável'];

        if ($this->ref_idpes_responsavel != 'NULL') {
            $obj_pessoa_resp = new clsPessoaFj($this->ref_idpes_responsavel);
            $det_pessoa_resp = $obj_pessoa_resp->detalhe();

            if ($det_pessoa_resp) {
                $lista[$det_pessoa_resp['idpes']] = $det_pessoa_resp['nome'];
            }
        }

        $parametros = new clsParametrosPesquisas();
        $parametros->setSubmit(0);
        $parametros->adicionaCampoSelect('ref_idpes_responsavel', 'idpes', 'nome');
        $parametros->setPessoa('F');
        $parametros->setPessoaNovo('S');
        $parametros->setPessoaCPF('N');
        $parametros->setPessoaTela('frame');
        $parametros->setCodSistema(1);
        $this->campoListaPesq(
        'ref_idpes_responsavel',
        'Responsavel',
        $lista,
      $this->ref_idpes_responsavel,
        'pesquisa_pessoa_lst.php',
        '',
        false,
        '',
        '',
      null,
        null,
        '',
        false,
        $parametros->serializaCampos(),
        false
    );

        $this->campoQuebra2('#224488');

        if ($this->tipo_responsavel) {
            if ($this->nm_pai) {
                $this->tipo_responsavel = 'p';
            } elseif ($this->nm_mae) {
                $this->tipo_responsavel = 'm';
            } elseif ($this->ref_idpes_responsavel) {
                $this->tipo_responsavel = 'r';
            }
        }

        $opcoes = [
      'p' => 'Pai',
      'm' => 'M&atilde;e',
      'r' => 'Respons&aacute;vel'
    ];
        $this->campoRadio('tipo_responsavel', 'Respons&aacute;vel', $opcoes, $this->tipo_responsavel);

        $this->campoQuebra2('#224488');

        $disabled = $this->isEnderecoExterno ? false : true;

        $this->campoOculto('isEnderecoExterno', $this->isEnderecoExterno);

        $urlPesquisaCep = 'educar_pesquisa_cep_log_bairro.php?' .
      'campo1=nm_bairro&campo2=id_bairro&campo3=id_cep&campo4=nm_logradouro&' .
      'campo5=id_logradouro&campo6=ref_sigla_uf&campo7=cidade&' .
      'campo8=ref_idtlog_&campo9=isEnderecoExterno&campo10=cep_&' .
      'campo11=ref_sigla_uf_&campo12=ref_idtlog&campo13=id_cidade&' .
      'campo14=zona_localizacao';

        $urlPesquisaCep = sprintf(
      "<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'%s\'></iframe>');\">",
      $urlPesquisaCep
    );

        $this->campoCep('cep_', 'CEP', $this->cep_, true, '-', $urlPesquisaCep, $disabled);

        $this->campoTexto(
        'cidade',
        'Cidade',
        $this->cidade,
        30,
        255,
        true,
        false,
      true,
        '',
        '',
        '',
        '',
        $disabled
    );

        $obj_uf = new clsUf(false, false, 1);
        $lst_uf = $obj_uf->lista(false, false, false, false, false, 'sigla_uf');
        $array_uf = ['' => 'Selecione um estado'];

        foreach ($lst_uf as $uf) {
            $array_uf[$uf['sigla_uf']] = $uf['nome'];
        }

        $this->campoLista(
        'ref_sigla_uf_',
        ' &nbsp; Estado',
        $array_uf,
      $this->ref_sigla_uf,
        '',
        false,
        '',
        '',
        $disabled
    );

        $this->campoTexto(
        'nm_bairro',
        'Bairro',
        $this->nm_bairro,
        30,
        255,
        true,
      false,
        false,
        '',
        '',
        '',
        '',
        $disabled
    );

        $tipo_logradouro_array = ['' => 'Tipo de Logradouro'];

        $obj_tipo_logradouro = new clsTipoLogradouro();
        $obj_tipo_logradouro_lista = $obj_tipo_logradouro->lista();

        if ($obj_tipo_logradouro_lista) {
            foreach ($obj_tipo_logradouro_lista as $key => $tipo_log) {
                $tipo_logradouro_array[$tipo_log['idtlog']] = $tipo_log['descricao'];
            }
        }

        $this->campoLista(
        'ref_idtlog',
        'Logradouro',
        $tipo_logradouro_array,
      $this->ref_idtlog,
        '',
        true,
        '',
        '',
        $this->isEnderecoExterno ? false : true,
        true
    );

        $this->campoTexto(
        'nm_logradouro',
        'Logradouro',
        $this->nm_logradouro,
        30,
        255,
      true,
        false,
        false,
        '',
        '',
        '',
        '',
        $disabled
    );

        $zl = App_Model_ZonaLocalizacao::getInstance();
        $this->campoLista(
        'zona_localizacao',
        'Zona Localização',
        $zl->getEnums(),
      $this->zona_localizacao,
        false,
        false,
        false,
        false,
        $disabled
    );

        $this->campoNumero(
        'numero',
        'N&uacute;mero',
        $this->numero,
        4,
        6,
        false,
        '',
      '',
        false,
        false,
        true
    );
        $this->campoTexto('letra', ' &nbsp; Letra', $this->letra, 4, 1, false);
        $this->campoTexto('complemento', 'Complemento', $this->complemento, 30, 50, false);
        $this->campoTexto('bloco', 'Bloco', $this->bloco, 30, 50, false);
        $this->campoNumero(
        'andar',
        'Andar',
        $this->andar,
        4,
        2,
        false,
        '',
        '',
      false,
        false,
        true
    );
        $this->campoNumero(
        'apartamento',
        ' &nbsp; Apartamento',
        $this->apartamento,
      4,
        6,
        false
    );

        $this->campoLista(
        'nacionalidade',
        'Nacionalidade',
        $lista_nacionalidade,
      $this->nacionalidade,
        'tmpObj = document.getElementById("pais_origem"); if(this.value != 1) { tmpObj.disabled = false; } else { tmpObj.selectedIndex = 27; tmpObj.disabled = true; }',
      true,
        '',
        '',
        '',
        false
    );

        $lista_mun_nasc = ['NULL' => 'Selecione a cidade'];

        $obj_mun_nasc = new clsMunicipio($this->idmun_nascimento);
        $det_mun_nasc = $obj_mun_nasc->detalhe();

        if ($det_mun_nasc['nome']) {
            $lista_mun_nasc[$det_mun_nasc['idmun']] = $det_mun_nasc['nome'];
        }

        $this->campoListaPesq(
        'idmun_nascimento',
        'Naturalidade',
        $lista_mun_nasc,
      $this->idmun_nascimento,
        'educar_pesquisa_municipio_lst.php?campo1=idmun_nascimento',
        '',
        false,
        '',
        '',
        null,
        null,
        '',
        true
    );

        $this->nacionalidade = $this->nacionalidade ? $this->nacionalidade : 1;

        $lista_nacionalidade = [
      'NULL' => 'Selecione',
      '1'    => 'Brasileiro',
      '2'    => 'Naturalizado Brasileiro',
      '3'    => 'Estrangeiro'
    ];

        $this->campoLista(
        'nacionalidade',
        'Nacionalidade',
        $lista_nacionalidade,
      $this->nacionalidade,
        'tmpObj = document.getElementById("pais_origem"); if(this.value != 1) { tmpObj.disabled = false; } else { tmpObj.selectedIndex = 27; tmpObj.disabled = true; }',
      true,
        '',
        '',
        '',
        false
    );

        $lista_pais_origem = ['NULL' => 'País de origem'];
        $obj_pais          = new clsPais();
        $obj_pais_lista    = $obj_pais->lista(null, null, null, '', '', 'nome asc');

        if ($obj_pais_lista) {
            foreach ($obj_pais_lista as $key => $pais) {
                $lista_pais_origem[$pais['idpais']] = $pais['nome'];
            }
        }

        // se a nacionalidade for "BRASILEIRO" seleciona o brasil e deixa inativo
        $this->pais_origem = $this->nacionalidade == 1 ? 1 : $this->pais_origem;

        $this->campoLista(
        'pais_origem',
        ' &nbsp; País de Origem',
        $lista_pais_origem,
      $this->pais_origem,
        '',
        '',
        '',
        '',
        $this->nacionalidade == 1,
        false
    );

        $this->campoQuebra2('#224488');

        // Transporte escolar
        $transporteMapper = new Transporte_Model_AlunoDataMapper();
        $transporte       = null;

        try {
            $transporte = $transporteMapper->find([$this->cod_aluno]);
        } catch (Exception $e) {
        }

        $bit = App_Model_SimNao::getInstance();
        $this->campoLista(
        'transporte_aluno',
        'Transporte',
        $bit->getEnums(),
      !is_null($transporte) ? 1 : 0,
        'transporteResponsavel();'
    );

        $responsavel = Transporte_Model_Responsavel::getInstance();
        $this->campoLista(
        'transporte_responsavel',
        'Responsável',
        $responsavel->getEnums(),
      !is_null($transporte) ? $transporte->get('responsavel') : 0
    );

        $this->campoQuebra2('#224488');

        $obj_beneficio = new clsPmieducarAlunoBeneficio();
        $obj_beneficio_lista = $obj_beneficio->lista(
        null,
        null,
        null,
        null,
        null,
      null,
        null,
        null,
        null,
        1
    );

        $lista_beneficio = ['NULL' => 'Selecione'];

        if ($obj_beneficio_lista) {
            foreach ($obj_beneficio_lista as $beneficio) {
                $lista_beneficio[$beneficio['cod_aluno_beneficio']] = $beneficio['nm_beneficio'];
            }
        }

        $this->campoLista(
        'ref_cod_aluno_beneficio',
        'Benef&iacute;cio',
      $lista_beneficio,
        $this->ref_cod_aluno_beneficio,
        '',
        false,
        '',
        '',
        false,
        false
    );

        $lista_analfabeto = [
      1 => 'N&atilde;o',
      0 => 'Sim'
    ];

        $this->campoLista(
        'analfabeto',
        'Alfabetizado',
        $lista_analfabeto,
      $this->analfabeto,
        '',
        false,
        '',
        '',
        false,
        false
    );

        $this->campoNumero(
        'ddd_fone_1',
        'Telefone',
        $this->ddd_fone_1,
        1,
        3,
        false,
      '',
        '',
        false,
        false,
        true
    );

        $this->campoNumero('fone_1', 'Telefone', $this->fone_1, 11, 11);

        $this->campoNumero(
        'ddd_mov',
        'Celular',
        $this->ddd_mov,
        1,
        3,
        false,
        '',
        '',
      false,
        false,
        true
    );

        $this->campoNumero('fone_mov', 'Celular', $this->fone_mov, 11, 11);

        $this->campoEmail('email', 'Email', $this->email, 30, 255, false);

        if (!empty($this->caminho_foto)) {
            $this->campoRotulo(
          'foto_antiga_',
          'Arquivo',
        sprintf('
          <img src="arquivos/educar/aluno/small/%s" border="0">
            <a href="javascript:void(0);" onclick="document.getElementById(\'foto_excluida\').value=1; setVisibility(\'tr_foto_antiga_\', false); setVisibility(\'tr_foto\', true);"><img src="imagens/nvp_bola_xis.gif" border="0"></a>', $this->caminho_foto)
      );
        }

        $this->campoOculto('foto_excluida', 0);
        $this->campoArquivo('foto', 'Foto', '', '20', '', false);

        $this->campoOculto('id_bairro', $this->id_bairro);
        $this->campoOculto('id_cep', $this->id_cep);
        $this->campoOculto('id_logradouro', $this->id_logradouro);
        $this->campoOculto('id_cidade', $this->id_cidade);
        $this->campoOculto('ref_idtlog_', $this->ref_idtlog);
        $this->campoOculto('ref_sigla_uf', $this->ref_sigla_uf);

        $this->campoTexto('nome', 'Nome', $this->nome, 30, 100, true);

        if ($this->cpf && $this->ref_idpes) {
            if (!$this->cpf) {
                $this->campoRotulo('cpf_2', 'CPF', $this->cpf);
            } else {
                $this->campoRotulo('cpf_2', 'CPF', int2CPF($this->cpf));
            }
        } else {
            if (!$this->cpf) {
                $this->campoOculto('sem_cpf', 1);
            }
        }

        $this->campoData('data_nascimento', 'Data de Nascimento', $this->data_nascimento, true);

        $lista = [
      ''  => 'Selecione',
      'F' => 'Feminino',
      'M' => 'Masculino'
    ];
        $this->campoLista('sexo', 'Sexo', $lista, $this->sexo);

        $obj_estado_civil = new clsEstadoCivil();
        $obj_estado_civil_lista = $obj_estado_civil->lista();

        $lista_estado_civil = ['' => 'Selecione'];

        if ($obj_estado_civil_lista) {
            foreach ($obj_estado_civil_lista as $estado_civil) {
                $lista_estado_civil[$estado_civil['ideciv']] = $estado_civil['descricao'];
            }
        }

        $this->campoLista('ideciv', 'Estado Civil', $lista_estado_civil, $this->ideciv);

        $obj_religiao = new clsPmieducarReligiao();
        $obj_religia_lista = $obj_religiao->lista(
        null,
        null,
        null,
        null,
        null,
        null,
      null,
        null,
        1
    );

        $lista_religiao = ['NULL' => 'Selecione'];
        if ($obj_religia_lista) {
            foreach ($obj_religia_lista as $religiao) {
                $lista_religiao[$religiao['cod_religiao']] = $religiao['nm_religiao'];
            }
        }

        $this->campoLista(
        'ref_cod_religiao',
        'Religi&atilde;o',
        $lista_religiao,
      $this->ref_cod_religiao,
        '',
        false,
        '',
        '',
        '',
        false
    );

        $opcoes_raca = [ '' => 'Selecione' ];
        $obj_raca = new clsCadastroRaca();
        $lst_raca = $obj_raca->lista(null, null, null, null, null, null, null, true);

        if ($lst_raca) {
            foreach ($lst_raca as $raca) {
                $opcoes_raca[$raca['cod_raca']] = $raca['nm_raca'];
            }
        }

        $this->campoLista(
        'ref_cod_raca',
        'Ra&ccedil;a',
        $opcoes_raca,
      $this->ref_cod_raca,
        '',
        false,
        '',
        '',
        '',
        false
    );

        $this->campoQuebra2('#224488');

        if ($this->idpes_pai) {
            $obj_pessoa_pai = new clsPessoaFj($this->idpes_pai);
            $det_pessoa_pai = $obj_pessoa_pai->detalhe();

            if ($det_pessoa_pai) {
                $this->nm_pai = $det_pessoa_pai['nome'];

                // cpf
                $obj_cpf = new clsFisica($this->idpes_pai);
                $det_cpf = $obj_cpf->detalhe();
                if ($det_cpf['cpf']) {
                    $this->cpf_pai = int2CPF($det_cpf['cpf']);
                }
            }
        }

        if ($this->idpes_mae) {
            $obj_pessoa_mae = new clsPessoaFj($this->idpes_mae);
            $det_pessoa_mae = $obj_pessoa_mae->detalhe();
            if ($det_pessoa_mae) {
                $this->nm_mae = $det_pessoa_mae['nome'];

                // cpf
                $obj_cpf = new clsFisica($this->idpes_mae);
                $det_cpf = $obj_cpf->detalhe();

                if ($det_cpf['cpf']) {
                    $this->cpf_mae = int2CPF($det_cpf['cpf']);
                }
            }
        }

        $this->campoTexto('nm_pai', 'Nome do Pai', $this->nm_pai, 30, 255, false);
        $this->campoCpf(
        'cpf_pai',
        'CPF pai',
        $this->cpf_pai,
        false,
      $this->cpf_pai ? '': ' &nbsp; &nbsp; (preencher sempre que possível)'
    );

        $this->campoTexto('nm_mae', 'Nome da M&atilde;e', $this->nm_mae, 30, 255, false);
        $this->campoCpf(
        'cpf_mae',
        'CPF mãe',
        $this->cpf_mae,
        false,
      $this->cpf_mae ? '': ' &nbsp; &nbsp; (preencher sempre que possível)'
    );

        $lista = ['' => 'Responsável'];

        if ($this->ref_idpes_responsavel != 'NULL') {
            $obj_pessoa_resp = new clsPessoaFj($this->ref_idpes_responsavel);
            $det_pessoa_resp = $obj_pessoa_resp->detalhe();
            if ($det_pessoa_resp) {
                $lista[$det_pessoa_resp['idpes']] = $det_pessoa_resp['nome'];
            }
        }

        $parametros = new clsParametrosPesquisas();
        $parametros->setSubmit(0);
        $parametros->adicionaCampoSelect('ref_idpes_responsavel', 'idpes', 'nome');
        $parametros->setPessoa('F');
        $parametros->setPessoaNovo('S');
        $parametros->setPessoaCPF('N');
        $parametros->setPessoaTela('frame');
        $parametros->setCodSistema(1);
        $this->campoListaPesq(
        'ref_idpes_responsavel',
        'Responsavel',
        $lista,
      $this->ref_idpes_responsavel,
        'pesquisa_pessoa_lst.php',
        '',
        false,
        '',
      '',
        null,
        null,
        '',
        false,
        $parametros->serializaCampos(),
        false
    );

        $this->campoQuebra2('#224488');
        if (!$this->tipo_responsavel) {
            if ($this->nm_pai) {
                $this->tipo_responsavel = 'p';
            } elseif ($this->nm_mae) {
                $this->tipo_responsavel = 'm';
            } elseif ($this->ref_idpes_responsavel) {
                $this->tipo_responsavel = 'r';
            }
        }

        $opcoes = [
      ''  => 'Sem Responsável',
      'p' => 'Pai',
      'm' => 'M&atilde;e',
      'r' => 'Respons&aacute;vel'
    ];
        $this->campoRadio('tipo_responsavel', 'Respons&aacute;vel', $opcoes, $this->tipo_responsavel);

        $this->campoQuebra2('#224488');

        $disabled = $this->isEnderecoExterno ? false : true;

        $this->campoOculto('isEnderecoExterno', $this->isEnderecoExterno);

        $this->campoCep(
        'cep_',
        'CEP',
        $this->cep_,
        true,
        '-',
      //"<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=nm_bairro&campo2=id_bairro&campo3=id_cep&campo4=nm_logradouro&campo5=id_logradouro&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog_&campo9=isEnderecoExterno&campo10=cep_&campo11=ref_sigla_uf_&campo12=ref_idtlog&campo13=id_cidade\'></iframe>');\">",
      $urlPesquisaCep,
      $disabled
    );

        $this->campoTexto(
        'cidade',
        'Cidade',
        $this->cidade,
        30,
        255,
        true,
        false,
      true,
        '',
        '',
        '',
        '',
        $disabled
    );

        $obj_uf = new clsUf(false, false, 1);
        $lst_uf = $obj_uf->lista(false, false, false, false, false, 'sigla_uf');
        $array_uf = ['' => 'Selecione um estado'];
        foreach ($lst_uf as $uf) {
            $array_uf[$uf['sigla_uf']] = $uf['nome'];
        }

        $this->campoLista(
        'ref_sigla_uf_',
        ' &nbsp; Estado',
        $array_uf,
      $this->ref_sigla_uf,
        '',
        false,
        '',
        '',
        $disabled
    );

        $this->campoTexto(
        'nm_bairro',
        'Bairro',
        $this->nm_bairro,
        30,
        255,
        true,
      false,
        false,
        '',
        '',
        '',
        '',
        $disabled
    );

        $tipo_logradouro_array = ['' => 'Tipo de Logradouro'];

        $obj_tipo_logradouro = new clsTipoLogradouro();
        $obj_tipo_logradouro_lista = $obj_tipo_logradouro->lista();

        if ($obj_tipo_logradouro_lista) {
            foreach ($obj_tipo_logradouro_lista as $key => $tipo_log) {
                $tipo_logradouro_array[$tipo_log['idtlog']] = $tipo_log['descricao'];
            }
        }

        $this->campoLista(
        'ref_idtlog',
        'Logradouro',
        $tipo_logradouro_array,
      $this->ref_idtlog,
        '',
        true,
        '',
        '',
        $this->isEnderecoExterno ? false : true,
        true
    );

        $this->campoTexto(
        'nm_logradouro',
        'Logradouro',
        $this->nm_logradouro,
        30,
      255,
        true,
        false,
        false,
        '',
        '',
        '',
        '',
        $disabled
    );

        $this->campoNumero(
        'numero',
        'N&uacute;mero',
        $this->numero,
        4,
        6,
        false,
      '',
        '',
        false,
        false,
        true
    );

        $this->campoTexto('letra', ' &nbsp; Letra', $this->letra, 4, 1, false);

        $this->campoTexto('complemento', 'Complemento', $this->complemento, 30, 50, false);

        $this->campoTexto('bloco', 'Bloco', $this->bloco, 30, 50, false);

        $this->campoNumero(
        'andar',
        'Andar',
        $this->andar,
        4,
        2,
        false,
        '',
        '',
      false,
        false,
        true
    );

        $this->campoNumero(
        'apartamento',
        ' &nbsp; Apartamento',
        $this->apartamento,
      4,
        6,
        false
    );

        $lista_mun_nasc = ['NULL' => 'Selecione a cidade'];

        $obj_mun_nasc = new clsMunicipio($this->idmun_nascimento);
        $det_mun_nasc = $obj_mun_nasc->detalhe();

        if ($det_mun_nasc['nome']) {
            $lista_mun_nasc[$det_mun_nasc['idmun']] = $det_mun_nasc['nome'];
        }

        $this->campoListaPesq(
        'idmun_nascimento',
        'Naturalidade',
        $lista_mun_nasc,
      $this->idmun_nascimento,
        'educar_pesquisa_municipio_lst.php?campo1=idmun_nascimento',
      '',
        false,
        '',
        '',
        null,
        null,
        '',
        true
    );

        $this->nacionalidade = $this->nacionalidade ? $this->nacionalidade : 1;

        $lista_nacionalidade = [
      'NULL' => 'Selecione',
      '1'    => 'Brasileiro',
      '2'    => 'Naturalizado Brasileiro',
      '3'    => 'Estrangeiro'
    ];

        $this->campoLista(
        'nacionalidade',
        'Nacionalidade',
        $lista_nacionalidade,
      $this->nacionalidade,
        'tmpObj = document.getElementById(\'pais_origem\'); if (this.value!=1) { tmpObj.disabled = false; } else { tmpObj.selectedIndex = 27; tmpObj.disabled = true; }',
      true,
        '',
        '',
        '',
        false
    );

        $lista_pais_origem = ['NULL' => 'País de origem'];
        $obj_pais = new clsPais();
        $obj_pais_lista = $obj_pais->lista(null, null, null, '', '', 'nome asc');

        if ($obj_pais_lista) {
            foreach ($obj_pais_lista as $key => $pais) {
                $lista_pais_origem[$pais['idpais']] = $pais['nome'];
            }
        }

        // se a nacionalidade for "BRASILEIRO" seleciona o brasil e deixa inativo
        $this->pais_origem = $this->nacionalidade == 1 ? 1 : $this->pais_origem;
        $this->campoLista(
        'pais_origem',
        ' &nbsp; País de Origem',
        $lista_pais_origem,
      $this->pais_origem,
        '',
        '',
        '',
        '',
        $this->nacionalidade == 1,
        false
    );

        $this->campoQuebra2('#224488');

        $obj_beneficio = new clsPmieducarAlunoBeneficio();
        $obj_beneficio_lista = $obj_beneficio->lista(
        null,
        null,
        null,
        null,
        null,
      null,
        null,
        null,
        null,
        1
    );

        $lista_beneficio = ['NULL' => 'Selecione'];

        if ($obj_beneficio_lista) {
            foreach ($obj_beneficio_lista as $beneficio) {
                $lista_beneficio[$beneficio['cod_aluno_beneficio']] = $beneficio['nm_beneficio'];
            }
        }

        $this->campoLista(
        'ref_cod_aluno_beneficio',
        'Benef&iacute;cio',
        $lista_beneficio,
      $this->ref_cod_aluno_beneficio,
        '',
        false,
        '',
        '',
        false,
        false
    );

        $lista_analfabeto = [
      1 => 'N&atilde;o',
      0 => 'Sim'
    ];
        $this->campoLista(
        'analfabeto',
        'Alfabetizado',
        $lista_analfabeto,
      $this->analfabeto,
        '',
        false,
        '',
        '',
        false,
        false
    );

        $this->campoNumero(
        'ddd_fone_1',
        'Telefone',
        $this->ddd_fone_1,
        1,
        3,
      false,
        '',
        '',
        false,
        false,
        true
    );
        $this->campoNumero('fone_1', 'Telefone', $this->fone_1, 11, 11);
        $this->campoNumero(
        'ddd_mov',
        'Celular',
        $this->ddd_mov,
        1,
        3,
        false,
        '',
        '',
      false,
        false,
        true
    );
        $this->campoNumero('fone_mov', 'Celular', $this->fone_mov, 11, 11);

        $this->campoEmail('email', 'Email', $this->email, 30, 255, false);

        if (!empty($this->caminho_foto)) {
            $this->campoRotulo('foto_antiga_', 'Arquivo', sprintf('<img src="arquivos/educar/aluno/small/%s" border="0"> <a href="javascript:void(0);" onclick="document.getElementById(\'foto_excluida\').value=1; setVisibility(\'tr_foto_antiga_\', false); setVisibility(\'tr_foto\',true);"> <img src="imagens/nvp_bola_xis.gif" border="0"></a>', $this->caminho_foto));
        }

        $this->campoArquivo('foto', 'Foto', '', '20', '', false);

        $this->campoOculto('id_bairro', $this->id_bairro);
        $this->campoOculto('id_cep', $this->id_cep);
        $this->campoOculto('id_logradouro', $this->id_logradouro);
        $this->campoOculto('id_cidade', $this->id_cidade);
        $this->campoOculto('ref_idtlog_', $this->ref_idtlog);
        $this->campoOculto('ref_sigla_uf', $this->ref_sigla_uf);
        $this->campoOculto('cpf', $this->cpf);

        $this->campoAdicionaTab('Deficiência', $this->tab_habilitado);

        if ($this->ref_idpes) {
            $obj_deficiencia_pessoa = new clsCadastroFisicaDeficiencia();
            $obj_deficiencia_pessoa_lista = $obj_deficiencia_pessoa->lista($this->ref_idpes);
        }

        if ($this->inc != 2 && !$this->exc) {
            if ($obj_deficiencia_pessoa_lista) {
                $deficiencia_pessoa = [];

                foreach ($obj_deficiencia_pessoa_lista as $deficiencia) {
                    $obj_def = new clsCadastroDeficiencia($deficiencia['ref_cod_deficiencia']);
                    $det_def =  $obj_def->detalhe();
                    $deficiencia_pessoa[$deficiencia['ref_cod_deficiencia']] = $det_def['nm_deficiencia'];
                }

                $deficiencia_aluno = [];
                $deficiencia_aluno = $deficiencia_pessoa;
            }
        }

        $obj_deficiencias = new clsCadastroDeficiencia();
        $lista_deficiencias = $obj_deficiencias->lista();

        $lista = ['' => 'Selecione'];

        if ($lista_deficiencias) {
            foreach ($lista_deficiencias as $deficiencia) {
                $lista[$deficiencia['cod_deficiencia']] = $deficiencia['nm_deficiencia'];
            }
        }

        $oculto = $tabela = '';
        if ($deficiencia_aluno) {
            foreach ($deficiencia_aluno as $indice => $valor) {
                $cor_fundo = $cor_fundo == '#D1DADF' ? '#f5f9fd' : '#D1DADF';
                $tabela .= "<tr id=\"tr_{$indice}\" bgcolor=\"{$cor_fundo}\" style=\"padding-right: 10px;\">";
                $tabela .= "<td>{$valor}</td>";
                $tabela .= '<td align="right" style="padding-right: 10px;">';
                $tabela .= "<img border=\"0\" onclick=\"excluirLinhaDeficiencia({$indice})\"
                      style=\"cursor: pointer;\" src=\"imagens/banco_imagens/excluirrr.png\"
                      title=\"Excluir\">";
                $tabela .= '</td></tr>';
                $oculto .= "<input type=\"hidden\" id=\"oc_defic[{$indice}]\" name=\"oc_defic[{$indice}]\" value=\"{$indice}\">";
            }
        }

        $this->campoLista(
        'ref_cod_pessoa_deficiencia',
        'Defici&ecirc;ncia',
        $lista,
      $this->ref_cod_pessoa_deficiencia,
        '',
        false,
        '',
        '',
        false,
        $obrigatorio
    );

        $this->campoRotulo(
        'incluir2',
        'Incluir defici&ecirc;ncia',
      '<a href=\'#\' onclick="adicionaDeficiencia();"><img src=\'imagens/banco_imagens/entrada2.gif\' title=\'Incluir\' border=0></a>'
    );

        $this->campoRotulo('tab_defic', 'Deficiências', "<table id='tabela_deficiencia' cellspacing='0' cellpadding='2'><tbody>{$tabela}</tbody></table><div id='ocultos_defic'>{$oculto}</div>");

        $this->campoOculto('inc', '');
        $this->campoOculto('exc', '');

        $this->campoAdicionaTab('Outros Dados', $this->tab_habilitado);

        if ($this->ref_idpes) {
            $ObjDocumento = new clsDocumento($this->ref_idpes);
            $detalheDocumento = $ObjDocumento->detalhe();

            $this->rg = $detalheDocumento['rg'];

            if ($detalheDocumento['data_exp_rg']) {
                $this->data_exp_rg = date(
            'd/m/Y',
          strtotime(substr($detalheDocumento['data_exp_rg'], 0, 19))
        );
            }

            $this->sigla_uf_exp_rg = $detalheDocumento['sigla_uf_exp_rg'];
            $this->tipo_cert_civil = $detalheDocumento['tipo_cert_civil'];
            $this->num_termo = $detalheDocumento['num_termo'];
            $this->num_livro = $detalheDocumento['num_livro'];
            $this->num_folha = $detalheDocumento['num_folha'];

            if ($detalheDocumento['data_emissao_cert_civil']) {
                $this->data_emissao_cert_civil = date(
            'd/m/Y',
          strtotime(substr($detalheDocumento['data_emissao_cert_civil'], 0, 19))
        );
            }

            $this->sigla_uf_cert_civil = $detalheDocumento['sigla_uf_cert_civil'];

            $this->cartorio_cert_civil = $detalheDocumento['cartorio_cert_civil'];
            $this->num_cart_trabalho   = $detalheDocumento['num_cart_trabalho'];
            $this->serie_cart_trabalho = $detalheDocumento['serie_cart_trabalho'];

            if ($detalheDocumento['data_emissao_cart_trabalho']) {
                $this->data_emissao_cart_trabalho = date('d/m/Y', strtotime(substr($detalheDocumento['data_emissao_cart_trabalho'], 0, 19)));
            }

            $this->sigla_uf_cart_trabalho = $detalheDocumento['sigla_uf_cart_trabalho'];
            $this->num_tit_eleitor        = $detalheDocumento['num_tit_eleitor'];
            $this->zona_tit_eleitor       = $detalheDocumento['zona_tit_eleitor'];
            $this->secao_tit_eleitor      = $detalheDocumento['secao_tit_eleitor'];
            $this->idorg_exp_rg           = $detalheDocumento['idorg_exp_rg'];
        }

        $objUf = new clsUf();
        $listauf = $objUf->lista();
        $listaEstado = ['0' => 'Selecione'];

        if ($listauf) {
            foreach ($listauf as $uf) {
                $listaEstado[$uf['sigla_uf']] = $uf['sigla_uf'];
            }
        }

        $objOrgaoEmissorRg = new clsOrgaoEmissorRg();
        $listaOrgaoEmissorRg = $objOrgaoEmissorRg->lista();
        $listaOrgao = ['0' => 'Selecione'];

        if ($listaOrgaoEmissorRg) {
            foreach ($listaOrgaoEmissorRg as $orgaoemissor) {
                $listaOrgao[$orgaoemissor['idorg_rg']] = $orgaoemissor['sigla'];
            }
        }

        $this->campoOculto('idpes', $this->idpes);

        $this->campoTexto('rg', 'Rg', $this->rg, '10', '10', false);
        $this->campoData('data_exp_rg', 'Data Expedição RG', $this->data_exp_rg, false);

        $this->campoLista(
        'idorg_exp_rg',
        'Órgão Expedição RG',
        $listaOrgao,
      $this->idorg_exp_rg,
        false,
        false,
        false,
        false,
        false,
        false
    );

        $this->campoLista(
        'sigla_uf_exp_rg',
        'Estado Expedidor',
        $listaEstado,
      $this->sigla_uf_exp_rg,
        false,
        false,
        false,
        false,
        false,
        false
    );

        $lista_tipo_cert_civil = [];
        $lista_tipo_cert_civil['0'] = 'Selecione';
        $lista_tipo_cert_civil[91]  = 'Nascimento';
        $lista_tipo_cert_civil[92]  = 'Casamento';

        $this->campoLista(
        'tipo_cert_civil',
        'Tipo Certificado Civil',
      $lista_tipo_cert_civil,
        $this->tipo_cert_civil,
        null,
        null,
        null,
        null,
      null,
        false
    );

        $this->campoTexto('num_termo', 'Termo', $this->num_termo, '8', '8', false);
        $this->campoTexto('num_livro', 'Livro', $this->num_livro, '8', '8', false);
        $this->campoTexto('num_folha', 'Folha', $this->num_folha, '4', '4', false);

        $this->campoData(
        'data_emissao_cert_civil',
        'Emissão Certidão Civil',
      $this->data_emissao_cert_civil,
        false
    );

        $this->campoLista(
        'sigla_uf_cert_civil',
        'Sigla Certidão Civil',
      $listaEstado,
        $this->sigla_uf_cert_civil,
        false,
        false,
        false,
        false,
      false,
        false
    );

        $this->campoMemo(
        'cartorio_cert_civil',
        'Cartório',
        $this->cartorio_cert_civil,
      '35',
        '4',
        false,
        false
    );

        $this->campoTexto(
        'num_tit_eleitor',
        'Título de Eleitor',
        $this->num_tit_eleitor,
      '13',
        '13',
        false
    );

        $this->campoTexto(
        'zona_tit_eleitor',
        'Zona',
        $this->zona_tit_eleitor,
        '4',
      '4',
        false
    );

        $this->campoTexto(
        'secao_tit_eleitor',
        'Seção',
        $this->secao_tit_eleitor,
      '10',
        '10',
        false
    );

        // Adiciona uma aba com dados do Inep/Educacenso caso aluno tenha código Inep.
        if (isset($this->cod_aluno)) {
            $alunoMapper = new Educacenso_Model_AlunoDataMapper();

            $alunoInep = null;
            try {
                $alunoInep = $alunoMapper->find(['aluno' => $this->cod_aluno]);
            } catch (Exception $e) {
            }

            if ($alunoInep) {
                $this->campoAdicionaTab('Educacenso/Inep', $this->tab_habilitado);

                $this->campoRotulo(
            '_inep_cod_aluno',
            'Código do aluno no Educacenso/Inep',
          $alunoInep->alunoInep
        );

                if (isset($alunoInep->nomeInep)) {
                    $this->campoRotulo(
              '_inep_nome_aluno',
              'Nome do aluno no Educacenso/Inep',
            $alunoInep->nomeInep
          );
                }
            }
        }

        $this->campoTabFim();
    }

    public function Novo()
    {
        session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        if (!$this->cpf && $this->cpf_2) {
            $cpf = idFederal2int($this->cpf_2);
            $obj_pessoa_fisica = new clsPessoaFisica();
            $lst_pessoa_fisica = $obj_pessoa_fisica->lista(null, $cpf);

            if ($lst_pessoa_fisica) {
                $this->mensagem = 'CPF Já Cadastrado';

                return false;
            } else {
                die('CPF Não Existente');
            }
        }

        $obj_pessoa = new clsPessoa_($this->ref_idpes);

        if ($obj_pessoa->detalhe()) {
            $obj_pessoa = new clsPessoa_(
          $this->ref_idpes,
          $this->nome,
          null,
          $this->url,
        null,
          $this->pessoa_logada,
          null,
          $this->email
      );

            if (!$obj_pessoa->edita()) {
                return false;
            }
        } else {
            $obj_pessoa = new clsPessoa_(
          $this->ref_idpes,
          $this->nome,
          null,
          $this->url,
        'F',
          $this->pessoa_logada,
          null,
          $this->email
      );

            if (!$this->ref_idpes = $obj_pessoa->cadastra()) {
                return false;
            }
        }

        if (!$this->cpf) {
            $this->ref_cod_sistema = 1;
        } else {
            $this->ref_cod_sistema = 'NULL';
        }

        if (is_string($this->cpf_pai) && $this->cpf_pai != '') {
            $this->cpf_pai = idFederal2int($this->cpf_pai);
            $obj_cpf = new clsFisica(
          false,
          false,
          false,
          false,
          false,
          false,
          false,
        false,
          false,
          false,
          false,
          false,
          false,
          false,
          false,
          false,
          false,
        false,
          false,
          false,
          false,
          false,
          false,
          false,
          false,
          $this->cpf_pai
      );

            $detalhe_cpf = $obj_cpf->detalheCPF();

            if ($detalhe_cpf) {
                $this->idpes_pai = $detalhe_cpf['idpes'];
                $obj_pessoa = new clsPessoa_($this->idpes_pai);
                $det_pessoa = $obj_pessoa->detalhe();

                if ($det_pessoa) {
                    if ($this->nm_pai) {
                        $obj_pessoa = new clsPessoa_($this->idpes_pai, $this->nm_pai);
                        $obj_pessoa->edita();
                    } else {
                        $this->nm_pai = $det_pessoa['nome'];
                    }
                }
            } else {
                // cria uma pessoa para o pai
                $obj_pessoa = new clsPessoa_(
            false,
            $this->nm_pai,
            $this->pessoa_logada,
          false,
            'F'
        );

                $idpes = $obj_pessoa->cadastra();

                if ($idpes) {
                    $this->idpes_pai = $idpes;
                    //cadastra como pesso Fisica
                    $obj_fisica = new clsFisica(
              $idpes,
              false,
              'M',
              false,
              false,
              false,
            false,
              false,
              false,
              false,
              false,
              false,
              false,
              false,
              false,
              false,
            false,
              false,
              false,
              false,
              false,
              false,
              $this->pessoa_logada,
             null,
              null,
              $this->cpf_pai
          );

                    $obj_fisica->cadastra();
                }
            }
        } else {
            $this->idpes_pai = 'NULL';
        }

        if (is_string($this->cpf_mae) && $this->cpf_mae != '') {
            $this->cpf_mae = idFederal2int($this->cpf_mae);
            $obj_cpf = new clsFisica(
          false,
          false,
          false,
          false,
          false,
          false,
          false,
        false,
          false,
          false,
          false,
          false,
          false,
          false,
          false,
          false,
          false,
        false,
          false,
          false,
          false,
          false,
          false,
          false,
          false,
          $this->cpf_mae
      );

            $detalhe_cpf = $obj_cpf->detalheCPF();

            if ($detalhe_cpf) {
                $this->idpes_mae = $detalhe_cpf['idpes'];
                $obj_pessoa = new clsPessoa_($this->idpes_mae);
                $det_pessoa = $obj_pessoa->detalhe();

                if ($det_pessoa) {
                    if ($this->nm_mae) {
                        $obj_pessoa = new clsPessoa_($this->idpes_mae, $this->nm_mae);
                        $obj_pessoa->edita();
                    } else {
                        $this->nm_mae = $det_pessoa['nome'];
                    }
                }
            } else {
                // cria uma pessoa para a mae
                $obj_pessoa = new clsPessoa_(
            false,
            $this->nm_mae,
            $this->pessoa_logada,
          false,
            'F'
        );

                $idpes = $obj_pessoa->cadastra();

                if ($idpes) {
                    $this->idpes_mae = $idpes;
                    //cadastra como pesso Fisica
                    $obj_fisica = new clsFisica(
              $idpes,
              false,
              'F',
              false,
              false,
              false,
            false,
              false,
              false,
              false,
              false,
              false,
              false,
              false,
              false,
            false,
              false,
              false,
              false,
              false,
              false,
              false,
              $this->pessoa_logada,
             null,
              null,
              $this->cpf_mae
          );

                    $obj_fisica->cadastra();
                }
            }
        } else {
            $this->idpes_mae = 'NULL';
        }

        $obj_fisica = new clsFisica(
        $this->ref_idpes,
        dataToBanco($this->data_nascimento),
      $this->sexo,
        $this->idpes_mae,
        $this->idpes_pai,
        $this->ref_idpes_responsavel,
      null,
        $this->ideciv,
        null,
        null,
        null,
        $this->nacionalidade,
      $this->pais_origem,
        null,
        $this->idmun_nascimento,
        null,
        null,
        null,
        null,
      null,
        null,
        null,
        null,
        $this->pessoa_logada,
        $this->ref_cod_sistema,
        $this->cpf
    );

        if ($obj_fisica->detalhe()) {
            if (!$this->ref_idpes_responsavel) {
                $this->ref_idpes_responsavel = 'NULL';
            }

            $obj_fisica = new clsFisica(
          $this->ref_idpes,
          dataToBanco($this->data_nascimento),
        $this->sexo,
          $this->idpes_mae,
          $this->idpes_pai,
          $this->ref_idpes_responsavel,
        null,
          $this->ideciv,
          null,
          null,
          null,
          $this->nacionalidade,
          $this->pais_origem,
        null,
          $this->idmun_nascimento,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
        null,
          $this->pessoa_logada,
          $this->ref_cod_sistema
      );

            if (!$obj_fisica->edita()) {
                return false;
            }
        } else {
            $obj_fisica = new clsFisica(
          $this->ref_idpes,
          dataToBanco($this->data_nascimento),
        $this->sexo,
          $this->idpes_mae,
          $this->idpes_pai,
          $this->ref_idpes_responsavel,
         null,
          $this->ideciv,
          null,
          null,
          null,
          $this->nacionalidade,
         $this->pais_origem,
          null,
          $this->idmun_nascimento,
          null,
          null,
          null,
         null,
          null,
          null,
          null,
          null,
          $this->pessoa_logada,
          $this->ref_cod_sistema,
         $this->cpf
      );

            if (!$obj_fisica->cadastra()) {
                return false;
            }
        }

        if (is_numeric($this->ref_cod_raca)) {
            $obj_fisica_raca = new clsCadastroFisicaRaca($this->ref_idpes);
            if ($obj_fisica_raca->existe()) {
                $obj_fisica_raca = new clsCadastroFisicaRaca($this->ref_idpes, $this->ref_cod_raca);
                $obj_fisica_raca->edita();
            } else {
                $obj_fisica_raca = new clsCadastroFisicaRaca($this->ref_idpes, $this->ref_cod_raca);
                $obj_fisica_raca->cadastra();
            }
        } else {
            $obj_fisica_raca = new clsCadastroFisicaRaca($this->ref_idpes, $this->ref_cod_raca);
            $obj_fisica_raca->excluir();
        }

        $objTelefone = new clsPessoaTelefone($this->ref_idpes, 1, $this->fone_1, $this->ddd_fone_1);
        if ($objTelefone->detalhe()) {
            $objTelefone->edita();
        } else {
            $objTelefone->cadastra();
        }

        $objTelefone = new clsPessoaTelefone($this->ref_idpes, 2, $this->fone_2, $this->ddd_fone_2);
        if ($objTelefone->detalhe()) {
            $objTelefone->edita();
        } else {
            $objTelefone->cadastra();
        }

        $objTelefone = new clsPessoaTelefone($this->ref_idpes, 3, $this->fone_mov, $this->ddd_mov);
        if ($objTelefone->detalhe()) {
            $objTelefone->edita();
        } else {
            $objTelefone->cadastra();
        }

        $objTelefone = new clsPessoaTelefone($this->ref_idpes, 4, $this->fone_fax, $this->ddd_fax);
        if ($objTelefone->detalhe()) {
            $objTelefone->edita();
        } else {
            $objTelefone->cadastra();
        }

        if ($this->isEnderecoExterno) {
            $this->cep_ = str_replace('-', '', $this->cep_);
            $obj_endereco = new clsEnderecoExterno(
          $this->ref_idpes,
          1,
        $this->ref_idtlog,
          $this->nm_logradouro,
          $this->numero,
          $this->letra,
        $this->complemento,
          $this->nm_bairro,
          $this->cep_,
          $this->cidade,
        $this->ref_sigla_uf_,
          null,
          $this->bloco,
          $this->apartamento,
          $this->andar,
        null,
          $this->pessoa_logada,
          $this->zona_localizacao
      );

            if ($obj_endereco->existe()) {
                if (!$obj_endereco->edita()) {
                    return false;
                }
            } else {
                if (!$obj_endereco->cadastra()) {
                    return false;
                }
            }

            if ($this->endereco_original_is_externo != $this->isEnderecoExterno) {
                $obj_endereco = new clsPessoaEndereco($this->ref_idpes);
                $obj_endereco->exclui();
            }
        } else {
            $obj_endereco = new clsPessoaEndereco(
          $this->ref_idpes,
          $this->id_cep,
        $this->id_logradouro,
          $this->id_bairro,
          $this->numero,
          $this->complemento,
        null,
          $this->letra,
          $this->bloco,
          $this->apartamento,
          $this->andar,
          null,
        $this->pessoa_logada
      );

            if ($obj_endereco->existe()) {
                if (!$obj_endereco->edita()) {
                    return false;
                }
            } else {
                if (!$obj_endereco->cadastra()) {
                    return false;
                }
            }

            if ($this->endereco_original_is_externo != $this->isEnderecoExterno) {
                $obj_endereco = new clsEnderecoExterno($this->ref_idpes);
                $obj_endereco->exclui();
            }
        }

        $this->deficiencia_exc = $_POST['oc_defic_exc'];
        if ($this->deficiencia_exc) {
            foreach ($this->deficiencia_exc as $key => $deficiencia) {
                $obj_deficiencia_pessoa = new clsCadastroFisicaDeficiencia($this->ref_idpes, $deficiencia);

                if ($obj_deficiencia_pessoa->detalhe()) {
                    $obj_deficiencia_pessoa->excluir();
                }
            }
        }

        $this->deficiencia = $_POST['oc_defic'];

        if ($this->deficiencia) {
            foreach ($this->deficiencia as $key => $deficiencia) {
                $obj_deficiencia_pessoa = new clsCadastroFisicaDeficiencia($this->ref_idpes, $key);

                if (!$obj_deficiencia_pessoa->detalhe()) {
                    $obj_deficiencia_pessoa->cadastra();
                }
            }
        }

        if ($this->data_emissao_cart_trabalho) {
            $this->data_emissao_cart_trabalho = explode('/', $this->data_emissao_cart_trabalho);
            $this->data_emissao_cart_trabalho = "{$this->data_emissao_cart_trabalho[2]}/{$this->data_emissao_cart_trabalho[1]}/{$this->data_emissao_cart_trabalho[0]}";
        }

        if ($this->data_emissao_cert_civil) {
            $this->data_emissao_cert_civil = explode('/', $this->data_emissao_cert_civil);
            $this->data_emissao_cert_civil = "{$this->data_emissao_cert_civil[2]}/{$this->data_emissao_cert_civil[1]}/{$this->data_emissao_cert_civil[0]}";
        }

        if ($this->data_exp_rg) {
            $this->data_exp_rg = explode('/', $this->data_exp_rg);
            $this->data_exp_rg = "{$this->data_exp_rg[2]}/{$this->data_exp_rg[1]}/{$this->data_exp_rg[0]}";
        }

        $ObjDocumento = new clsDocumento(
        $this->ref_idpes,
        $this->rg,
        $this->data_exp_rg,
      $this->sigla_uf_exp_rg,
        $this->tipo_cert_civil,
        $this->num_termo,
        $this->num_livro,
      $this->num_folha,
        $this->data_emissao_cert_civil,
        $this->sigla_uf_cert_civil,
      $this->cartorio_cert_civil,
        $this->num_cart_trabalho,
        $this->serie_cart_trabalho,
      $this->data_emissao_cart_trabalho,
        $this->sigla_uf_cart_trabalho,
      $this->num_tit_eleitor,
        $this->zona_tit_eleitor,
        $this->secao_tit_eleitor,
      $this->idorg_exp_rg
    );

        if ($ObjDocumento->detalhe()) {
            $ObjDocumento = new clsDocumento(
          $this->ref_idpes,
          $this->rg,
          $this->data_exp_rg,
        $this->sigla_uf_exp_rg,
          $this->tipo_cert_civil,
          $this->num_termo,
        $this->num_livro,
          $this->num_folha,
          $this->data_emissao_cert_civil,
        $this->sigla_uf_cert_civil,
          $this->cartorio_cert_civil,
          $this->num_cart_trabalho,
        $this->serie_cart_trabalho,
          $this->data_emissao_cart_trabalho,
        $this->sigla_uf_cart_trabalho,
          $this->num_tit_eleitor,
          $this->zona_tit_eleitor,
        $this->secao_tit_eleitor,
          $this->idorg_exp_rg
      );

            if (!$ObjDocumento->edita()) {
                return false;
            }
        } else {
            $ObjDocumento = new clsDocumento(
          $this->ref_idpes,
          $this->rg,
          $this->data_exp_rg,
        $this->sigla_uf_exp_rg,
          $this->tipo_cert_civil,
          $this->num_termo,
        $this->num_livro,
          $this->num_folha,
          $this->data_emissao_cert_civil,
        $this->sigla_uf_cert_civil,
          $this->cartorio_cert_civil,
          $this->num_cart_trabalho,
        $this->serie_cart_trabalho,
          $this->data_emissao_cart_trabalho,
        $this->sigla_uf_cart_trabalho,
          $this->num_tit_eleitor,
        $this->zona_tit_eleitor,
          $this->secao_tit_eleitor,
          $this->idorg_exp_rg
      );

            if (!$ObjDocumento->cadastra()) {
                return false;
            }
        }

        if ($this->foto && $this->foto['error'] == 0) {
            $this->foto = $this->geraFotos($this->foto['tmp_name']);
            $obj = new clsPmieducarAluno(
          null,
          $this->ref_cod_aluno_beneficio,
        $this->ref_cod_religiao,
          $this->pessoa_logada,
          $this->pessoa_logada,
        $this->ref_idpes,
          null,
          null,
          1,
          $this->foto,
          $this->analfabeto,
        $this->nm_pai,
          $this->nm_mae
      );

            $obj_det = $obj->detalhe();
            if ($obj_det) {
                if ($obj_det['caminho_foto']) {
                    $this->caminho_foto = $obj_det['caminho_foto'];
                }

                $this->foto_excluida = 1;
            }
        } elseif ($this->foto_excluida == 1) {
            $this->foto = 'NULL';
        } elseif (!$this->foto_excluida) {
            $this->foto = $this->foto_antiga;
        }

        if ($this->foto_excluida) {
            if (file_exists('arquivos/educar/aluno/big/' . $this->caminho_foto)) {
                unlink('arquivos/educar/aluno/big/' . $this->caminho_foto);
            }

            if (file_exists('arquivos/educar/aluno/small/' . $this->caminho_foto)) {
                unlink('arquivos/educar/aluno/small/' . $this->caminho_foto);
            }

            if (file_exists('arquivos/educar/aluno/original/' . $this->caminho_foto)) {
                unlink('arquivos/educar/aluno/original/'  . $this->caminho_foto);
            }
        }

        if (is_numeric($this->idpes_mae) && $this->idpes_mae != 'NULL') {
            $this->nm_mae = 'NULL';
        }

        if (is_numeric($this->idpes_pai) && $this->idpes_pai != 'NULL') {
            $this->nm_pai = 'NULL';
        }

        if (!$this->cod_aluno) {
            $obj = new clsPmieducarAluno(
          null,
          $this->ref_cod_aluno_beneficio,
        $this->ref_cod_religiao,
          $this->pessoa_logada,
          $this->pessoa_logada,
        $this->ref_idpes,
          null,
          null,
          1,
          $this->foto,
          $this->analfabeto,
        $this->nm_pai,
          $this->nm_mae,
          $this->tipo_responsavel
      );

            if ($this->ref_idpes) {
                if ($obj->existePessoa()) {
                    $aluno = $obj->edita();
                    $this->cod_aluno = $aluno['cod_aluno'];
                } else {
                    $this->cod_aluno = $obj->cadastra();
                }
            }
        } else {
            $obj = new clsPmieducarAluno(
          $this->cod_aluno,
          $this->ref_cod_aluno_beneficio,
        $this->ref_cod_religiao,
          $this->pessoa_logada,
          $this->pessoa_logada,
        $this->ref_idpes,
          null,
          null,
          1,
          $this->foto,
          $this->analfabeto,
        $this->nm_pai,
          $this->nm_mae,
          $this->tipo_responsavel
      );

            if ($this->ref_idpes) {
                if ($obj->existePessoa()) {
                    $obj->edita();
                } else {
                    $obj->cadastra();
                }
            }
        }

        // Atualiza a informação de uso de transporte escolar.
        $this->_cadastraTransporte(
        $this->cod_aluno,
        $this->transporte_aluno,
      $this->transporte_responsavel,
        $this->pessoa_logada
    );

        header('Location: educar_aluno_det.php?cod_aluno=' . $this->cod_aluno);
        die();
    }

    public function Editar()
    {
        $this->Novo();
    }

    public function Excluir()
    {
        session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $obj = new clsPmieducarAluno(
        $this->cod_aluno,
        $this->ref_cod_aluno_beneficio,
      $this->ref_cod_religiao,
        $this->pessoa_logada,
        $this->pessoa_logada,
      $this->ref_idpes,
        $this->data_cadastro,
        $this->data_exclusao,
        0
    );

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            header('Location: educar_aluno_lst.php');
            die();
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';
        echo "<!--\nErro ao excluir clsPmieducarAluno\nvalores obrigatorios\nif( is_numeric( $this->cod_aluno ) && is_numeric( $this->ref_usuario_exc ) )\n-->";

        return false;
    }

    public function geraFotos($fotoOriginal)
    {
        if (!file_exists($fotoOriginal)) {
            return;
        }

        list($imagewidth, $imageheight, $img_type) = @GetImageSize($fotoOriginal);
        $src_img_original = '';

        $fim_largura = $imagewidth;
        $fim_altura  = $imageheight;

        $extensao = $img_type == 2 ? '.jpg' : (($img_type == 3) ? '.png' : '');

        $nome_do_arquivo       = array_pop(explode('/', $fotoOriginal)) . $extensao;
        $caminhoDaBig          = 'arquivos/educar/aluno/big/' . $nome_do_arquivo;
        $caminhoDaFotoOriginal = 'arquivos/educar/aluno/original/' . $nome_do_arquivo;

        if ($imagewidth > 700) {
            $new_w = 700;
            $ratio = ($imagewidth / $new_w);
            $new_h = ceil($imageheight / $ratio);

            $fim_largura = $new_w;
            $fim_altura = $new_h;

            if (!file_exists($caminhoDaBig)) {
                if ($img_type == 2) {
                    $src_img_original = @imagecreatefromjpeg($fotoOriginal);
                    $dst_img = @imagecreatetruecolor($new_w, $new_h);
                    imagecopyresized(
              $dst_img,
              $src_img_original,
              0,
              0,
              0,
              0,
              $new_w,
            $new_h,
              imagesx($src_img_original),
              imagesy($src_img_original)
          );
                    imagejpeg($dst_img, $caminhoDaBig);
                } elseif ($img_type == 3) {
                    $src_img_original = @ImageCreateFrompng($fotoOriginal);

                    $dst_img = @imagecreatetruecolor($new_w, $new_h);
                    ImageCopyResized(
              $dst_img,
              $src_img_original,
              0,
              0,
              0,
              0,
              $new_w,
            $new_h,
              ImageSX($src_img_original),
              ImageSY($src_img_original)
          );
                    Imagepng($dst_img, $caminhoDaBig);
                }
            }
        } else {
            if (!file_exists($caminhoDaBig)) {
                copy($fotoOriginal, $caminhoDaBig);

                if ($img_type == 2) {
                    $src_img_original = @imagecreatefromjpeg($fotoOriginal);
                } elseif ($img_type == 3) {
                    $src_img_original = @imagecreatefrompng($fotoOriginal);
                }
            }
        }

        $new_w = 100;
        $ratio = ($imagewidth / $new_w);
        $new_h = round($imageheight / $ratio);

        $caminhoDaSmall = 'arquivos/educar/aluno/small/' . $nome_do_arquivo;

        if (file_exists($caminhoDaBig)) {
            if ($img_type == 2) {
                $dst_img = @imagecreatetruecolor($new_w, $new_h);
                @imagecopyresized(
            $dst_img,
            $src_img_original,
            0,
            0,
            0,
            0,
            $new_w,
          $new_h,
            imagesx($src_img_original),
            imagesy($src_img_original)
        );

                @imagejpeg($dst_img, $caminhoDaSmall);
            } elseif ($img_type == 3) {
                $dst_img = @imagecreatetruecolor($new_w, $new_h);
                @imageCopyResized(
            $dst_img,
            $src_img_original,
            0,
            0,
            0,
            0,
            $new_w,
            $new_h,
          ImageSX($src_img_original),
            imageSY($src_img_original)
        );

                @imagepng($dst_img, $caminhoDaSmall);
            } elseif ($img_type == 1) {
                $dst_img=@imagecreatefromgif($src_img_original);
                @imageCopyResized(
            $dst_img,
            $src_img_original,
            0,
            0,
            0,
            0,
            $new_w,
          $new_h,
            ImageSX($src_img_original),
            imageSY($src_img_original)
        );

                @imagegif($dst_img, $caminhoDaSmall);
            }
        }

        copy($fotoOriginal, $caminhoDaFotoOriginal);
        if (! (file_exists($fotoOriginal) && file_exists($caminhoDaSmall) &&
      file_exists($caminhoDaBig))) {
            die('<center><br>Um erro ocorreu ao inserir a foto.<br>Por favor tente novamente.</center>');
        }

        if (file_exists($fotoOriginal)) {
            unlink($fotoOriginal);
        }

        return $nome_do_arquivo;
    }

    /**
     * Cadastra ou atualiza a informação de uso de transporte escolar.
     *
     * @access protected
     *
     * @param int  $codAluno    Código do aluno
     * @param bool $transporte  [Opcional] TRUE para cadastrar/atualizar e FALSE
     *                          para remover a informação de uso de transporte escolar
     * @param int  $responsavel [Opcional] Código do responsável pelo transporte
     *                          escolar, valor mapeado para o enum Transporte_Model_Responsavel. Apenas
     *                          obrigatório caso $transporte = TRUE
     * @param int  $user        Código do usuário a alterar o registroo
     *
     * @return bool TRUE caso tenha criado/editado/apagado o registro com sucesso
     *
     * @since  Método disponível desde a versão 1.2.0
     */
    public function _cadastraTransporte(
      $codAluno,
      $transporte = true,
      $responsavel = null,
    $user
  ) {
        $data = [
      'aluno'       => $codAluno,
      'responsavel' => $responsavel,
      'user'        => $user,
      'created_at'  => 'NOW()'
    ];

        $transporteMapper = new Transporte_Model_AlunoDataMapper();

        if ($transporte) {
            if (is_null($responsavel)) {
                return false;
            }

            try {
                $transporteMapper->find(['aluno' => $codAluno]);
            } catch (Exception $e) {
                $transporteMapper->save(
          $transporteMapper->createNewEntityInstance($data)
        );
            }
        } else {
            $transporteMapper->delete(['aluno' => $codAluno]);
        }

        return true;
    }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
  var campos = document.getElementsByName('tipo_responsavel');

  for(var i = 1; i < campos.length; i++) {
    campos[i].onclick = function() {
      analizador();
    }
  }

  function analizador()
  {
    var id_check = 0;
    for(var i = 1; i < campos.length; i++) {
      if(campos[i].checked)
      {
        switch(campos[i].value) {
          case 'p':
            id_check = 1;
            if (!campo_pai.value) {
              alert("Preencha o campo 'Nome do Pai' para poder seleciona-lo!");
              campo_pai.focus();
              id_check = 0;
            }
            break;

          case 'm':
            id_check = 2;
            if (!campo_mae.value) {
              alert("Preencha o campo 'Nome da Mãe' para poder seleciona-lo!");
              campo_mae.focus();
              id_check = 0;
            }
            break;

          case 'r':
            id_check = 3;
            if (!campo_resp.value) {
              alert("Preencha o campo 'Responsável' para poder seleciona-lo!");
              campo_resp.focus();
              id_check = 0;
            }
            break;
        }
      }
    }

    if (id_check) {
      campos[id_check].checked = true;
    }
    else {
      analizador2();
    }
  }

  var campo_pai = $('nm_pai');
  campo_pai.onkeyup = function()
  {
    if(campo_pai.value) {
      campos[1].checked = true;
    }
    else {
      analizador2();
    }
  }

  var campo_mae = $('nm_mae');
  campo_mae.onkeyup = function()
  {
    if (campo_pai.value) {
      campos[1].checked = true;
      return;
    }
    if (campo_mae.value) {
      campos[2].checked = true;
    }
    else {
      analizador2();
    }
  }

  var campo_resp = $('ref_idpes_responsavel');
  campo_resp.onchange = function()
  {
    if (campo_resp.value) {
      campos[3].checked = true;
    }
    else if(campo_mae.value) {
      campos[2].checked = true;
      return;
    }
    else {
      analizador2();
    }
  }

  function analizador2()
  {
    var id_check = 0;

    if (campo_pai.value) {
      campo_pai.onkeyup();
    }
    else if(campo_mae.value) {
      campo_mae.onkeyup();
    }
    else if(campo_resp.value) {
      campo_resp.onchange();
    }
    else {
      campos[0].checked = true;
    }
  }

  var cor_fundo;
  function adicionaDeficiencia()
  {
    if ($F('ref_cod_pessoa_deficiencia') == '') {
      alert('Selecione uma deficiência para adicionar');
    }
    else {
      var tabela = $('tabela_deficiencia');
      var cod_deficiencia;
      var nm_deficiencia;

      cod_deficiencia = $F('ref_cod_pessoa_deficiencia');
      nm_deficiencia  = $('ref_cod_pessoa_deficiencia').options[$('ref_cod_pessoa_deficiencia').selectedIndex].text;

      if (!$('tr_'+cod_deficiencia)) {
        cor_fundo = cor_fundo == '#D1DADF' ? '#f5f9fd' : '#D1DADF';
        var row = document.createElement('tr');
        row.setAttribute('id', 'tr_'+cod_deficiencia);
        row.setAttribute('align', 'center');
        row.style.backgroundColor = cor_fundo;

        var cell1 = document.createElement('td');
        cell1.setAttribute('align', 'right');
        cell1.setAttribute('style', 'padding-right:10px;');

        var cell2 = document.createElement('td');

        var img = "<img border='0' title='Excluir' src='imagens/banco_imagens/excluirrr.png' style='cursor:pointer' onclick='excluirLinhaDeficiencia(" + cod_deficiencia + ")'>";

        var text = document.createTextNode(nm_deficiencia);

        cell1.innerHTML = img;
        cell2.appendChild(text);
        row.appendChild(cell2);
        row.appendChild(cell1);
        tabela.firstChild.appendChild(row);

        var area = document.getElementById('ocultos_defic');
        var input = document.createElement('input');
        input.setAttribute('type', 'hidden');
        input.setAttribute('id', 'oc_defic['+cod_deficiencia+']');
        input.setAttribute('name', 'oc_defic['+cod_deficiencia+']');
        input.setAttribute('value', cod_deficiencia);
        area.appendChild(input);
      }
      else {
        alert('Deficiência já selecionada');
      }
    }
  }

  function excluirLinhaDeficiencia(cod_deficiencia)
  {
    var cor = '';
    var tabela = $('tabela_deficiencia').firstChild;
    var deficiencia = 'tr_'+cod_deficiencia;

    for(var i=0; i<tabela.childNodes.length; i++) {
      if (tabela.childNodes[i]) {
        if(tabela.childNodes[i].id == deficiencia) {
          cor = tabela.childNodes[i].bgColor;
          tabela.removeChild(tabela.childNodes[i]);
        }

        if(cor != '' && tabela.childNodes[i] && tabela.childNodes[i].tagName == 'TR') {
          tabela.childNodes[i].bgColor = cor;
          cor = (cor == '#d1dadf') ? '#f5f9fd' : '#d1dadf';
        }
      }
    }

    var area = document.getElementById('ocultos_defic');
    deficiencia = 'oc_defic['+cod_deficiencia+']';

    for (var i = 0; i < area.childNodes.length; i++) {
      if (area.childNodes[i]) {
        if (area.childNodes[i].id == deficiencia) {
          area.removeChild(area.childNodes[i]);
          var areaExc = document.getElementById('ocultos_defic');
          var inputExc = document.createElement('input');
          inputExc.setAttribute('type', 'hidden');
          inputExc.setAttribute('id', 'oc_defic_exc['+cod_deficiencia+']');
          inputExc.setAttribute('name', 'oc_defic_exc['+cod_deficiencia+']');
          inputExc.setAttribute('value', cod_deficiencia);
          areaExc.appendChild(inputExc);
        }
      }
    }
  }

<?php
/** Javascript condicional */
if (!$_GET['cod_aluno']) {
    ?>
  Event.observe(window, 'load', Init, false);

  function Init()
  {
    elemento = $$('div#content1 img');
    elemento[1].setAttribute('onClick', 'bloqueia();');
    $('btn_enviar').disabled  = true;
    $('btn_enviar').className = 'botaolistagemdisabled';
  }

  function passaPagina()
  {
    LTb0('0', '2');
  }

  function bloqueia()
  {
    if (($F('cpf_') != '' && $F('cpf') == '' && $F('bloqueado') == 1) ||
         $F('cpf_') != $F('cpf_2')
    ) {
      $('btn_enviar').disabled  = false;
      $('btn_enviar').className = 'botaolistagemdisabled';
      $('btn_enviar').value     = 'Aguarde...';
      var cpf = $('cpf_').value;
      var xml_dados_pessoa = new ajax(getDados);
      xml_dados_pessoa.envia('educar_aluno_cad_xml.php?cpf=' + cpf);
    }
    else if ($F('cpf') != '' || $F('bloqueado') == 0) {
      validaTab(1);
      LTb0('0', '2');
      $('btn_enviar').disabled = false;
      $('btn_enviar').className = 'botaolistagem';
    }
    else {
      alert('Você deve preencher o campo CPF');
    }
  }

  function getDados(xml_dados)
  {
    var DOM_array = xml_dados.getElementsByTagName('dados');

    if (DOM_array.length) {
      var elementos;

      for (var i = 1; i <5; i++) {
        elementos = $$('div#content' + i + ' input');
        for (var j = 0; j < elementos.length; j++) {
          if (elementos[j].id != 'cpf' && elementos[j].id != 'cpf_') {
            elementos[j].value = '';
          }
        }
      }

      var libera = false;

      for (var i = 0; i < DOM_array[0].childNodes.length; i++) {
        if (DOM_array[0].childNodes[i].nodeType == 1) {
          try {
            libera = true;
            if (DOM_array[0].childNodes[i].firstChild.nodeValue != '')
              document.getElementById(DOM_array[0].childNodes[i].nodeName).value =
                DOM_array[0].childNodes[i].firstChild.nodeValue;
          }
          catch(e) {
            continue;
          }
        }
      }

      $('cpf_2').disabled = true;

      if (libera) {
        validaTab(1);
        LTb0('0', '2');
        $('btn_enviar').disabled = false;
        $('bloqueado').value = 0;
        $('btn_enviar').className = 'botaolistagem';
      }
      else {
        validaTab(1);
        LTb0('0', '2');
        $('btn_enviar').disabled = false;
        $('bloqueado').value = 0;
        $('btn_enviar').className = 'botaolistagem';
        $('cpf_2').value = $F('cpf_');
      }

      $('btn_enviar').value = 'Salvar';
    }

    $('btn_enviar').value = 'Salvar';
  }
<?php
}
/** Javascript condicional */
?>
Event.observe(window, 'load', transporteResponsavel, false);

function transporteResponsavel()
{
  obj1 = document.getElementById('transporte_aluno');
  obj2 = document.getElementById('transporte_responsavel');

  if (obj1.value == 1) {
    obj2.disabled = false;
  }
  else {
    obj2.disabled = true;
  }
}
</script>
