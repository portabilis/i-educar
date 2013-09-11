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
      'label'  => 'Código inep',
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

      'altura' => array('label' => 'Altura'),

      'peso' => array('label' => 'Peso'),

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

  );


  protected function _preConstruct()
  {
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
    $options = array('label' => 'Data nascimento', 'disabled' => true, 'required' => false, 'size' => 25, 'placeholder' => '');
    $this->inputsHelper()->date('data_nascimento', $options);

    // rg
    $options = array('label' => $this->_getLabel('rg'), 'disabled' => true, 'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('rg', $options);

    // pai
    $options = array('label' => $this->_getLabel('pai'), 'disabled' => true, 'required' => false, 'size' => 68);
    $this->inputsHelper()->text('pai', $options);


    // mãe
    $options = array('label' => $this->_getLabel('mae'), 'disabled' => true, 'required' => false, 'size' => 68);
    $this->inputsHelper()->text('mae', $options);


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
    $this->inputsHelper()->religiao(array('required' => false));

    // beneficio
    $this->inputsHelper()->beneficio(array('required' => false));


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

    $this->campoRotulo('tit_dados_hospital',Portabilis_String_Utils::toLatin1('Em caso de emergencia, levar para hospital ou clinica')); 

    // hospital_clinica
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hospital_clinica') ), 'size' => 50, 'max_length' => 100,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('hospital_clinica',$options);    

    // hospital_clinica_endereco
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hospital_clinica_endereco') ), 'size' => 50, 'max_length' => 50,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('hospital_clinica_endereco',$options);    

    // hospital_clinica_telefone
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hospital_clinica_telefone') ), 'size' => 20, 'max_length' => 20,'required' => false, 'placeholder' => '');
    $this->inputsHelper()->text('hospital_clinica_telefone',$options);            

    $this->campoRotulo('tit_dados_responsavel',Portabilis_String_Utils::toLatin1('Em caso de emergencia, caso não seja encontrado pais ou responsáveis, avisar')); 

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

    $this->loadResourceAssets($this->getDispatcher());
  }
}
?>
