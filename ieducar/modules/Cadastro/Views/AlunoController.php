<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Avaliacao
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'App/Model/ZonaLocalizacao.php';
require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';

class AlunoController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'Cadastro de aluno';

  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_processoAp        = 578;
  protected $_deleteOption      = true;

  protected $_formMap    = array(
    'pessoa' => array(
      'label'  => 'Pessoa',
      'help'   => '',
    ),

    'rg' => array(
      'label'  => 'Documento de identidade (RG)',
      'help'   => '',
    ),

    'pai' => array(
      'label'  => 'Pai',
      'help'   => '',
    ),

    'mae' => array(
      'label'  => 'Mãe',
      'help'   => '',
    ),

    'responsavel' => array(
      'label'  => 'Responsável',
      'help'   => '',
    ),

    'alfabetizado' => array(
      'label'  => 'Alfabetizado',
      'help'   => '',
    ),

    'transporte' => array(
      'label'  => 'Transporte público',
      'help'   => '',
    ),

    'id' => array(
      'label'  => 'Código aluno',
      'help'   => '',
    ),

    'aluno_inep_id' => array(
      'label'  => 'Código INEP',
      'help'   => '',
    ),

    'aluno_estado_id' => array(
      'label'  => 'Código rede estadual',
      'help'   => '',
    ),

    'deficiencias' => array(
      'label'  => 'Deficiências / habilidades especiais',
      'help'   => '',
      ),

      /* *******************
         ** Dados médicos **
         ******************* */

      'altura' => array('label' => 'Altura/Metro'),

      'peso' => array('label' => 'Peso/Kg'),

      'grupo_sanguineo' => array('label' => 'Grupo sanguíneo'),

      'fator_rh' => array('label' => 'Fator RH'),

      'alergia_medicamento' => array('label' => 'O aluno é alérgico a algum medicamento?'),

      'desc_alergia_medicamento' => array('label' => 'Quais?'),

      'alergia_alimento' => array('label' => 'O aluno é alérgico a algum alimento?'),

      'desc_alergia_alimento' => array('label' => 'Quais?'),

      'doenca_congenita' => array('label' => 'O aluno possui doença congênita?'),

      'desc_doenca_congenita' => array('label' => 'Quais?'),

      'fumante' => array('label' => 'O aluno é fumante?'),

      'doenca_caxumba' => array('label' => 'O aluno já contraiu caxumba?'),

      'doenca_sarampo' => array('label' => 'O aluno já contraiu sarampo?'),

      'doenca_rubeola' => array('label' => 'O aluno já contraiu rubeola?'),

      'doenca_catapora' => array('label' => 'O aluno já contraiu catapora?'),

      'doenca_escarlatina' => array('label' => 'O aluno já contraiu escarlatina?'),

      'doenca_coqueluche' => array('label' => 'O aluno já contraiu coqueluche?'),

      'doenca_outras' => array('label' => 'Outras doenças que o aluno já contraiu'),

      'epiletico' => array('label' => 'O aluno é epilético?'),

      'epiletico_tratamento' => array('label' => 'Está em tratamento?'),

      'hemofilico' => array('label' => 'O aluno é hemofílico?'),

      'hipertenso' => array('label' => 'O aluno tem hipertensão?'),

      'asmatico' => array('label' => 'O aluno é asmático?'),

      'diabetico' => array('label' => 'O aluno é diabético?'),

      'insulina' => array('label' => 'Depende de insulina?'),

      'tratamento_medico' => array('label' => 'O aluno faz algum tratamento médico?'),

      'desc_tratamento_medico' => array('label' => 'Qual?'),

      'medicacao_especifica' => array('label' => 'O aluno está ingerindo medicação específica?'),

      'desc_medicacao_especifica' => array('label' => 'Qual?'),

      'acomp_medico_psicologico' => array('label' => 'O aluno tem acompanhamento médico ou psicológico?'),

      'desc_acomp_medico_psicologico' => array('label' => 'Motivo?'),

      'restricao_atividade_fisica' => array('label' => 'O aluno tem restrição a alguma atividade física?'),
      
      'desc_restricao_atividade_fisica' => array('label' => 'Qual?'),

      'fratura_trauma' => array('label' => 'O aluno sofreu alguma fratura ou trauma?'),

      'desc_fratura_trauma' => array('label' => 'Qual?'),

      'plano_saude' => array('label' => 'O aluno possui algum plano de saúde?'),

      'desc_plano_saude' => array('label' => 'Qual?'),

      'hospital_clinica' => array('label' => 'Nome'),

      'hospital_clinica_endereco' => array('label' => 'Endereço'),

      'hospital_clinica_telefone' => array('label' => 'Telefone'),

      'responsavel' => array('label' => 'Nome'),

      'responsavel_parentesco' => array('label' => 'Parentesco'),

      'responsavel_parentesco_telefone' => array('label' => 'Telefone'),

      'responsavel_parentesco_celular' => array('label' => 'Celular'),

      /*************************
      **** UNIFORME ESCOLAR ****
      **************************/

      'recebeu_uniforme' => array('label' => 'Recebeu uniforme escolar?'),

      'label_camiseta' => array('label' => 'Camiseta'),

      'quantidade_camiseta' => array('label' => 'Quantidade'),

      'tamanho_camiseta' => array('label' => 'Tamanho'),

      'label_blusa_jaqueta' => array('label' => 'Blusa/Jaqueta'),

      'quantidade_blusa_jaqueta' => array('label' => 'Quantidade'),

      'tamanho_blusa_jaqueta' => array('label' => 'Tamanho'),

      'label_bermuda' => array('label' => 'Bermuda'),

      'quantidade_bermuda' => array('label' => 'Quantidade'),

      'tamanho_bermuda' => array('label' => 'Tamanho'),      

      'label_calca' => array('label' => 'Calça'),

      'quantidade_calca' => array('label' => 'Quantidade'),

      'tamanho_calca' => array('label' => 'Tamanho'),

      'label_saia' => array('label' => 'Saia'),

      'quantidade_saia' => array('label' => 'Quantidade'),

      'tamanho_saia' => array('label' => 'Tamanho'),

      'label_calcado' => array('label' => 'Calçado'),

      'quantidade_calcado' => array('label' => 'Quantidade'),

      'tamanho_calcado' => array('label' => 'Tamanho'),

      'label_meia' => array('label' => 'Meia'),

      'quantidade_meia' => array('label' => 'Quantidade'),

      'tamanho_meia' => array('label' => 'Tamanho'),

    /************
      MORADIA    
    ************/

      'moradia' => array('label' => 'Moradia'),

      'material' => array('label' => 'Material'),

      'casa_outra' => array('label' => 'Outro'),

      'moradia_situacao' => array('label' => 'Situação'),

      'quartos' => array('label' => 'Número de quartos'),

      'sala' => array('label' => 'Número de salas'),

      'copa' => array('label' => 'Número de copas'),

      'banheiro' => array('label' => 'Número de banheiros'),

      'garagem' => array('label' => 'Número de garagens'),

      'empregada_domestica' => array('label' => 'Possui empregada doméstica?'),

      'automovel' => array('label' => 'Possui automóvel?'),

      'motocicleta' => array('label' => 'Possui motocicleta?'),

      'computador' => array('label' => 'Possui computador?'),

      'geladeira' => array('label' => 'Possui geladeira?'),

      'fogao' => array('label' => 'Possui fogão?'),

      'maquina_lavar' => array('label' => 'Possui máquina de lavar?'),

      'microondas' => array('label' => 'Possui microondas?'),

      'video_dvd' => array('label' => 'Possui vídeo/DVD?'),

      'televisao' => array('label' => 'Possui televisão?'),

      'celular' => array('label' => 'Possui celular?'),

      'telefone' => array('label' => 'Possui telefone?'),

      'quant_pessoas' => array('label' => 'Quantidades de pessoas residentes no lar'),

      'renda' => array('label' => 'Renda familiar em R$'),

      'agua_encanada' => array('label' => 'Possui água encanada?'),

      'poco' => array('label' => 'Possui poço?'),

      'energia' => array('label' => 'Possui energia?'),

      'esgoto' => array('label' => 'Possui esgoto?'),

      'fossa' => array('label' => 'Possui fossa?'),

      'lixo' => array('label' => 'Possui lixo?'),

  );


  protected function _preConstruct()
  {
    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""        => "$nomeMenu aluno"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }


  protected function _initNovo() {
    return false;
  }


  protected function _initEditar() {
    return false;
  }


  public function Gerar()
  {
    $this->url_cancelar = '/intranet/educar_aluno_lst.php';

    // código aluno
    $options = array('label'    => $this->_getLabel('id'), 'disabled' => true,
                     'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('id', $options);

    // código aluno inep
    $options = array('label' => $this->_getLabel('aluno_inep_id'), 'required' => false, 'size' => 25, 'max_length' => 14);
    $this->inputsHelper()->integer('aluno_inep_id', $options);

    // código aluno rede estadual
    $options = array('label' => $this->_getLabel('aluno_estado_id'), 'required' => false, 'size' => 25, 'max_length' => 25);
    $this->inputsHelper()->text('aluno_estado_id', $options);

    // nome
    $options = array('label' => $this->_getLabel('pessoa'), 'size' => 68);
    $this->inputsHelper()->simpleSearchPessoa('nome', $options);

    // data nascimento
    $options = array('label' => 'Data de nascimento', 'disabled' => true, 'required' => false, 'size' => 25, 'placeholder' => '');
    $this->inputsHelper()->date('data_nascimento', $options);

    // rg
    $options = array('label' => $this->_getLabel('rg'), 'disabled' => true, 'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('rg', $options);

    $this->inputPai();
    $this->inputMae();

/*    // pai
    $options = array('label' => $this->_getLabel('pai'), 'disabled' => true, 'required' => false, 'size' => 68);
    $this->inputsHelper()->text('pai', $options);


    // mãe
    $options = array('label' => $this->_getLabel('mae'), 'disabled' => true, 'required' => false, 'size' => 68);
    $this->inputsHelper()->text('mae', $options);*/


    // responsável

    // tipo

    $label = Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel'));

    /*$tiposResponsavel = array(null           => $label,
                              'pai'          => 'Pai',
                              'mae'          => 'M&atilde;e',
                              'outra_pessoa' => 'Outra pessoa');*/
    $tiposResponsavel = array(null           => 'Informe uma Pessoa primeiro');

    $options = array('label'     => $this->_getLabel('responsavel'),
                     'resources' => $tiposResponsavel,
                     'required'  => true,
                     'inline'    => true);

    $this->inputsHelper()->select('tipo_responsavel', $options);


    // nome
    $helperOptions = array('objectName' => 'responsavel');
    $options       = array('label' => '', 'size' => 50, 'required' => true);

    $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);


    // transporte publico

    $label = Portabilis_String_Utils::toLatin1($this->_getLabel('transporte'));

    $tiposTransporte = array(null        => $label,
                             'nenhum'    => 'N&atilde;o utiliza',
                             'municipal' => 'Municipal',
                             'estadual'  => 'Estadual');

    $options = array('label'     => $this->_getLabel('transporte'),
                     'resources' => $tiposTransporte,
                     'required'  => true);

    $this->inputsHelper()->select('tipo_transporte', $options);


    // religião
    $this->inputsHelper()->religiao(array('required' => false, 'label' => Portabilis_String_Utils::toLatin1('Religião')));

    // beneficio
    $this->inputsHelper()->beneficio(array('required' => false, 'label' => Portabilis_String_Utils::toLatin1('Benefício')));


    // Deficiências / habilidades especiais
    $helperOptions = array('objectName' => 'deficiencias');
    $options       = array('label' => $this->_getLabel('deficiencias'), 'size' => 50, 'required' => false,
                           'options' => array('value' => null));

    $this->inputsHelper()->multipleSearchDeficiencias('', $options, $helperOptions);


    // alfabetizado
    $options = array('label' => $this->_getLabel('alfabetizado'), 'value' => 'checked');
    $this->inputsHelper()->checkbox('alfabetizado', $options);


    /* *************************************
       ** Dados para a Aba 'Ficha médica' **
       ************************************* */

    // altura
    $options = array('label' => $this->_getLabel('altura'), 'size' => 5, 'max_length' => 4, 'required' => false, 'placeholder' => '' );
    $this->inputsHelper()->numeric('altura',$options);

    // peso
    $options = array('label' => $this->_getLabel('peso'), 'size' => 5, 'max_length' => 6, 'required' => false, 'placeholder' => '' );
    $this->inputsHelper()->numeric('peso',$options);    

    // grupo_sanguineo
    $options = array('label' => $this->_getLabel('grupo_sanguineo'), 'size' => 5, 'max_length' => 2, 'required' => false, 'placeholder' => '' );
    $this->inputsHelper()->text('grupo_sanguineo',$options);        

    // fator_rh
    $options = array('label' => $this->_getLabel('fator_rh'), 'size' => 5, 'max_length' => 1, 'required' => false, 'placeholder' => '' );
    $this->inputsHelper()->text('fator_rh',$options);            

    // sus
    $options = array('label' => $this->_getLabel('sus'), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => '' );
    $this->inputsHelper()->text('sus',$options);

    // alergia_medicamento
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('alergia_medicamento') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('alergia_medicamento',$options);            

    // desc_alergia_medicamento
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_alergia_medicamento') ), 'size' => 50, 'max_length' => 100,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('desc_alergia_medicamento',$options);                

    // alergia_alimento
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('alergia_alimento') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('alergia_alimento',$options);            

    // desc_alergia_alimento
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_alergia_alimento') ), 'size' => 50, 'max_length' => 100,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('desc_alergia_alimento',$options);                

    // doenca_congenita
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_congenita') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('doenca_congenita',$options);            

    // desc_doenca_congenita
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_doenca_congenita') ), 'size' => 50, 'max_length' => 100,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('desc_doenca_congenita',$options);      

    // fumante
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('fumante') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('fumante',$options); 

    // doenca_caxumba
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_caxumba') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('doenca_caxumba',$options); 

    // doenca_sarampo
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_sarampo') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('doenca_sarampo',$options); 

    // doenca_rubeola
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_rubeola') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('doenca_rubeola',$options); 

    // doenca_catapora
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_catapora') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('doenca_catapora',$options); 

    // doenca_escarlatina
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_escarlatina') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('doenca_escarlatina',$options); 

    // doenca_coqueluche
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_coqueluche') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('doenca_coqueluche',$options);     

    // doenca_outras
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_outras') ), 'size' => 50, 'max_length' => 100,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('doenca_outras',$options);      

    // epiletico
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('epiletico') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('epiletico',$options);     

    // epiletico_tratamento
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('epiletico_tratamento') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('epiletico_tratamento',$options);  

    // hemofilico
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hemofilico') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('hemofilico',$options);      

    // hipertenso
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hipertenso') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('hipertenso',$options);      

    // asmatico
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('asmatico') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('asmatico',$options);   

    // diabetico
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('diabetico') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('diabetico',$options);

    // insulina
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('insulina') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('insulina',$options);                  

    // tratamento_medico
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('tratamento_medico') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('tratamento_medico',$options);   

    // desc_tratamento_medico
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_tratamento_medico') ), 'size' => 50, 'max_length' => 100,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('desc_tratamento_medico',$options);                            

    // medicacao_especifica
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('medicacao_especifica') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('medicacao_especifica',$options);   

    // desc_medicacao_especifica
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_medicacao_especifica') ), 'size' => 50, 'max_length' => 100,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('desc_medicacao_especifica',$options);         

    // acomp_medico_psicologico
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('acomp_medico_psicologico') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('acomp_medico_psicologico',$options);   

    // desc_acomp_medico_psicologico
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_acomp_medico_psicologico') ), 'size' => 50, 'max_length' => 100,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('desc_acomp_medico_psicologico',$options);       

    // restricao_atividade_fisica
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('restricao_atividade_fisica') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('restricao_atividade_fisica',$options);   

    // desc_restricao_atividade_fisica
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_restricao_atividade_fisica') ), 'size' => 50, 'max_length' => 100,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('desc_restricao_atividade_fisica',$options);           

    // fratura_trauma
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('fratura_trauma') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('fratura_trauma',$options);   

    // desc_fratura_trauma
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_fratura_trauma') ), 'size' => 50, 'max_length' => 100,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('desc_fratura_trauma',$options);       

    // plano_saude
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('plano_saude') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('plano_saude',$options);   

    // desc_plano_saude
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_plano_saude') ), 'size' => 50, 'max_length' => 100,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('desc_plano_saude',$options);    

    $this->campoRotulo('tit_dados_hospital',Portabilis_String_Utils::toLatin1('Em caso de emergência, levar para hospital ou clínica')); 

    // hospital_clinica
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hospital_clinica') ), 'size' => 50, 'max_length' => 100,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('hospital_clinica',$options);    

    // hospital_clinica_endereco
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hospital_clinica_endereco') ), 'size' => 50, 'max_length' => 50,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('hospital_clinica_endereco',$options);    

    // hospital_clinica_telefone
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hospital_clinica_telefone') ), 'size' => 20, 'max_length' => 20,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('hospital_clinica_telefone',$options);            

    $this->campoRotulo('tit_dados_responsavel',Portabilis_String_Utils::toLatin1('Em caso de emergência, caso não seja encontrado pais ou responsáveis, avisar')); 

    // responsavel
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel') ), 'size' => 50, 'max_length' => 50,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('responsavel',$options);    

    // responsavel_parentesco
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel_parentesco') ), 'size' => 20, 'max_length' => 20,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('responsavel_parentesco',$options);            

    // responsavel_parentesco_telefone
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel_parentesco_telefone') ), 'size' => 20, 'max_length' => 20,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('responsavel_parentesco_telefone',$options);            

    // responsavel_parentesco_celular
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel_parentesco_celular') ), 'size' => 20, 'max_length' => 20,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('responsavel_parentesco_celular',$options);       

    // recebeu_uniforme
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('recebeu_uniforme') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('recebeu_uniforme',$options);                    

    $this->campoRotulo('label_camiseta',Portabilis_String_Utils::toLatin1($this->_getLabel('label_camiseta'))); 

    // quantidade_camiseta
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('quantidade_camiseta') ), 'size' => 2, 'max_length' => 3,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('quantidade_camiseta',$options);  

    // tamanho_camiseta
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('tamanho_camiseta') ), 'size' => 2, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('tamanho_camiseta',$options);   

    $this->campoRotulo('label_bermuda',Portabilis_String_Utils::toLatin1($this->_getLabel('label_bermuda'))); 

    // quantidade_bermuda
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('quantidade_bermuda') ), 'size' => 2, 'max_length' => 3,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('quantidade_bermuda',$options);  

    // tamanho_bermuda
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('tamanho_bermuda') ), 'size' => 2, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('tamanho_bermuda',$options);   

    $this->campoRotulo('label_calca',Portabilis_String_Utils::toLatin1($this->_getLabel('label_calca'))); 

    // quantidade_calca
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('quantidade_calca') ), 'size' => 2, 'max_length' => 3,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('quantidade_calca',$options);  

    // tamanho_calca
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('tamanho_calca') ), 'size' => 2, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('tamanho_calca',$options);   

    $this->campoRotulo('label_saia',Portabilis_String_Utils::toLatin1($this->_getLabel('label_saia'))); 

    // quantidade_saia
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('quantidade_saia') ), 'size' => 2, 'max_length' => 3,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('quantidade_saia',$options);  

    // tamanho_saia
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('tamanho_saia') ), 'size' => 2, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('tamanho_saia',$options);   

    $this->campoRotulo('label_calcado',Portabilis_String_Utils::toLatin1($this->_getLabel('label_calcado'))); 

    // quantidade_calcado
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('quantidade_calcado') ), 'size' => 2, 'max_length' => 3,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('quantidade_calcado',$options);  

    // tamanho_calcado
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('tamanho_calcado') ), 'size' => 2, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('tamanho_calcado',$options);                          

    $this->campoRotulo('label_meia',Portabilis_String_Utils::toLatin1($this->_getLabel('label_meia'))); 

    // quantidade_meia
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('quantidade_meia') ), 'size' =>2, 'max_length' => 3,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('quantidade_meia',$options);  

    // tamanho_meia
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('tamanho_meia') ), 'size' => 2, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('tamanho_meia',$options);                              

    $this->campoRotulo('label_blusa_jaqueta',Portabilis_String_Utils::toLatin1($this->_getLabel('label_blusa_jaqueta'))); 
    
    // quantidade_blusa_jaqueta
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('quantidade_blusa_jaqueta') ), 'size' =>2, 'max_length' => 3,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('quantidade_blusa_jaqueta',$options);  

    // tamanho_blusa_jaqueta
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('tamanho_blusa_jaqueta') ), 'size' => 2, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('tamanho_blusa_jaqueta',$options);   

    $moradias = array(null   => 'Selecione',
                        'A'  => 'Apartamento',
                        'C'  => 'Casa',
                        'O'  => 'Outro'); 

    $options = array('label'     => $this->_getLabel('moradia'),
                     'resources' => $moradias,
                     'required'  => false,
                     'inline'   => true);

    $this->inputsHelper()->select('moradia', $options);

    $materiais_moradia = array( 'A' => 'Alvenaria',
                                'M' => 'Madeira',
                                'I' => 'Mista');

    $options = array('label'     => null,
                     'resources' => $materiais_moradia,
                     'required'  => false,
                      'inline'   => true);

    $this->inputsHelper()->select('material', $options);         

    $options = array('label' => null, 'size' => 20, 'max_length' => 20,'required' => false, 'placeholder' => 'Descreva');
    $this->inputsHelper()->text('casa_outra',$options);     

    $situacoes = array( null => 'Selecione',
                        '1' => 'Alugado',
                        '2' => Portabilis_String_Utils::toLatin1('Próprio'),
                        '3' => 'Cedido',
                        '4' => 'Financiado',
                        '5' => 'Outros');

    $options = array('label'     => $this->_getLabel('moradia_situacao'),
                     'resources' => $situacoes,
                     'required'  => false);

    $this->inputsHelper()->select('moradia_situacao', $options);      

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('quartos') ), 'size' =>2, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('quartos',$options);       

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('sala') ), 'size' =>2, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('sala',$options);       

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('copa') ), 'size' =>2, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('copa',$options);       

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('banheiro') ), 'size' =>2, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('banheiro',$options);       

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('garagem') ), 'size' =>2, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('garagem',$options);      

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('empregada_domestica') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('empregada_domestica',$options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('automovel') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('automovel',$options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('motocicleta') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('motocicleta',$options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('computador') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('computador',$options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('geladeira') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('geladeira',$options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('fogao') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('fogao',$options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('maquina_lavar') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('maquina_lavar',$options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('microondas') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('microondas',$options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('video_dvd') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('video_dvd',$options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('televisao') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('televisao',$options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('telefone') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('telefone',$options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('celular') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('celular',$options);           
                                          
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('quant_pessoas') ), 'size' =>5, 'max_length' => 2,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('quant_pessoas',$options);  

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('renda') ), 'size' =>5, 'max_length' => 10,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->numeric('renda',$options); 

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('agua_encanada') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('agua_encanada',$options);   
              
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('poco') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('poco',$options);   
              
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('energia') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('energia',$options);   
              
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('esgoto') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('esgoto',$options);   
              
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('fossa') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('fossa',$options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('lixo') ), 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->checkbox('lixo',$options);   

    $this->inputsHelper()->simpleSearchMunicipio('pessoa-aluno', array('required' => false, 'size' => 57), array('objectName' => 'naturalidade_aluno'));

    $enderecamentoObrigatorio = false;
    $desativarCamposDefinidosViaCep = true;

    $this->campoCep(
      'cep_',
      'CEP',
      '',
      $enderecamentoObrigatorio,
      '-',
            "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel(500, 550, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'/intranet/educar_pesquisa_cep_log_bairro.php?campo1=bairro_bairro&campo2=bairro_id&campo3=cep&campo4=logradouro_logradouro&campo5=logradouro_id&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=municipio_municipio&campo12=idtlog&campo13=municipio_id&campo14=zona_localizacao\'></iframe>');\">",
      false
    );

    $options       = array('label' => Portabilis_String_Utils::toLatin1('Município'), 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

    $helperOptions = array('objectName'         => 'municipio',
                           'hiddenInputOptions' => array('options' => array('value' => $this->municipio_id)));

    $this->inputsHelper()->simpleSearchMunicipio('municipio', $options, $helperOptions);

    $helperOptions = array('hiddenInputOptions' => array('options' => array('value' => $this->bairro_id)));

    $options       = array( 'label' => Portabilis_String_Utils::toLatin1('Bairro / Zona de Localização - <b>Buscar</b>'), 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);


    $this->inputsHelper()->simpleSearchBairro('bairro', $options, $helperOptions);

    $options = array(
      'label'       => 'Bairro / Zona de Localização - <b>Cadastrar</b>',
      'placeholder' => 'Bairro',
      'value'       => $this->bairro,
      'max_length'  => 40,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'inline'      => true,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->text('bairro', $options);

    // zona localização

    $zonas = App_Model_ZonaLocalizacao::getInstance();
    $zonas = $zonas->getEnums();
    $zonas = Portabilis_Array_Utils::insertIn(null, 'Zona localiza&ccedil;&atilde;o', $zonas);

    $options = array(
      'label'       => '',
      'placeholder' => 'Zona localização',
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
      'value'       => '',
      'max_length'  => 150,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->text('logradouro', $options);

        // complemento

    $options = array(
      'required'    => false,
      'value'       => '',
      'max_length'  => 20
    );

    $this->inputsHelper()->text('complemento', $options);


    // numero

    $options = array(
      'required'    => false,
      'label'       => 'Número / Letra',
      'placeholder' => Portabilis_String_Utils::toLatin1('Número'),
      'value'       => '',
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
      'label'       => 'Nº apartamento / Bloco / Andar',
      'placeholder' =>  'Apartamento',
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

    $script = '/modules/Cadastro/Assets/Javascripts/Endereco.js';

    Portabilis_View_Helper_Application::loadJavascript($this, $script);

    $this->loadResourceAssets($this->getDispatcher());

  }

  protected function addParentsInput($parentType, $parentTypeLabel = '') {
    if (! $parentTypeLabel)
      $parentTypeLabel = $parentType;


    $parentId = $this->{$parentType . '_id'};


    // mostra uma dica nos casos em que foi informado apenas o nome dos pais,
    //pela antiga interface do cadastro de alunos.



    $hiddenInputOptions = array('options' => array('value' => $parentId));
    $helperOptions      = array('objectName' => $parentType, 'hiddenInputOptions' => $hiddenInputOptions);

    $options            = array('label'      => 'Pessoa ' . $parentTypeLabel,
                                'size'       => 69,
                                'required'   => false);

    $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);
  }

  protected function inputPai() {
    $this->addParentsInput('pai');
  }

  protected function inputMae() {
    $this->addParentsInput('mae', 'mãe');
  }
}
?>