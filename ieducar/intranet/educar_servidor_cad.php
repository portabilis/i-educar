<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
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
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

require_once 'Educacenso/Model/DocenteDataMapper.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor');
    $this->processoAp = 635;
    $this->addEstilo("localizacaoSistema");
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
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $cod_servidor;
  var $ref_cod_instituicao;
  var $ref_idesco;
  var $ref_cod_funcao = array();
  var $carga_horaria;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_cod_instituicao_original;
  var $situacao_curso_superior_1;
  var $formacao_complementacao_pedagogica_1;
  var $codigo_curso_superior_1;
  var $ano_inicio_curso_superior_1;
  var $ano_conclusao_curso_superior_1;
  var $tipo_instituicao_curso_superior_1;
  var $instituicao_curso_superior_1;
  var $situacao_curso_superior_2;
  var $formacao_complementacao_pedagogica_2;
  var $codigo_curso_superior_2;
  var $ano_inicio_curso_superior_2;
  var $ano_conclusao_curso_superior_2;
  var $tipo_instituicao_curso_superior_2;
  var $instituicao_curso_superior_2;  
  var $situacao_curso_superior_3;
  var $formacao_complementacao_pedagogica_3;
  var $codigo_curso_superior_3;
  var $ano_inicio_curso_superior_3;
  var $ano_conclusao_curso_superior_3;
  var $tipo_instituicao_curso_superior_3;
  var $instituicao_curso_superior_3;    
  var $pos_especializacao;  
  var $pos_mestrado;  
  var $pos_doutorado;  
  var $pos_nenhuma;
  var $curso_creche;
  var $curso_pre_escola;
  var $curso_anos_iniciais;
  var $curso_anos_finais;
  var $curso_ensino_medio;
  var $curso_eja;
  var $curso_educacao_especial;
  var $curso_educacao_indigena;
  var $curso_educacao_campo;
  var $curso_educacao_ambiental;
  var $curso_educacao_direitos_humanos;
  var $curso_genero_diversidade_sexual;
  var $curso_direito_crianca_adolescente;
  var $curso_relacoes_etnicorraciais;
  var $curso_outros;
  var $curso_nenhum;
  var $matricula = array();

  var $total_horas_alocadas;

  var $cod_docente_inep;

  // Determina se o servidor é um docente para buscar código Educacenso/Inep.
  var $docente = FALSE;

  function Inicializar()
  {
    $retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->cod_servidor                 = $_GET['cod_servidor'];
    $this->ref_cod_instituicao          = $_GET['ref_cod_instituicao'];
    $this->ref_cod_instituicao_original = $_GET['ref_cod_instituicao'];

    if ($_POST['ref_cod_instituicao_original']) {
      $this->ref_cod_instituicao_original = $_POST['ref_cod_instituicao_original'];
    }

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      'educar_servidor_lst.php');

    if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
      $obj = new clsPmieducarServidor($this->cod_servidor, NULL, NULL, NULL,
        NULL, NULL, NULL, $this->ref_cod_instituicao);

      $registro = $obj->detalhe();

      if ($registro) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
          $this->fexcluir = TRUE;
        }

        $db = new clsBanco();

        // Carga horária alocada
        $sql = sprintf("SELECT
            carga_horaria
          FROM
            pmieducar.servidor_alocacao
          WHERE
            ref_cod_servidor = '%d' AND
            ativo            = 1", $this->cod_servidor);

        $db->Consulta($sql);

        $carga = 0;
        while ($db->ProximoRegistro()) {
          $cargaHoraria = $db->Tupla();
          $cargaHoraria = explode(':', $cargaHoraria['carga_horaria']);
          $carga += $cargaHoraria[0] * 60 + $cargaHoraria[1];
        }

        $this->total_horas_alocadas = sprintf('%02d:%02d',
          $carga / 60, $carga % 60);

        // Funções
        $obj_funcoes = new clsPmieducarServidorFuncao();
        $lst_funcoes = $obj_funcoes->lista($this->ref_cod_instituicao, $this->cod_servidor);

        if ($lst_funcoes) {
          foreach ($lst_funcoes as $funcao) {
            $obj_funcao = new clsPmieducarFuncao($funcao['ref_cod_funcao']);
            $det_funcao = $obj_funcao->detalhe();

            $this->ref_cod_funcao[] = array($funcao['ref_cod_funcao'] . '-' . $det_funcao['professor'], null, null, $funcao['matricula']);
            
            // $this->ref_cod_funcao[] = array($funcao['ref_cod_funcao'] . '-' . $det_funcao['professor']);

            if (FALSE == $this->docente && (bool) $det_funcao['professor']) {
              $this->docente = TRUE;
            }
          }
        }

        $obj_servidor_disciplina = new clsPmieducarServidorDisciplina();
        $lst_servidor_disciplina = $obj_servidor_disciplina->lista(NULL,
          $this->ref_cod_instituicao,$this->cod_servidor);

        if ($lst_servidor_disciplina) {
          foreach ($lst_servidor_disciplina as $disciplina) {
            $obj_disciplina = new clsPmieducarDisciplina($disciplina['ref_cod_disciplina']);
            $det_disciplina = $obj_disciplina->detalhe();
            $this->cursos_disciplina[$det_disciplina['ref_cod_curso']][$disciplina['ref_cod_disciplina']] = $disciplina['ref_cod_disciplina'];
          }
        }

        @session_start();

        if ($_SESSION['cod_servidor'] == $this->cod_servidor) {
          $_SESSION['cursos_disciplina'] = $this->cursos_disciplina;
        }
        else {
          unset($_SESSION['cursos_disciplina']);
        }

        @session_write_close();

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = ($retorno == 'Editar') ?
      "educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" :
      "educar_servidor_lst.php";

    $this->nome_url_cancelar = 'Cancelar';

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""        => "{$nomeMenu} servidor"             
    ));
    $this->enviaLocalizacao($localizacao->montar());    
    return $retorno;
  }

  function Gerar()
  {
    // Foreign keys
    $obrigatorio     = TRUE;
    $get_instituicao = TRUE;
    include 'include/pmieducar/educar_campo_lista.php';

    $opcoes = array('' => 'Pesquise o funcionario clicando na lupa ao lado');
    if ($this->cod_servidor) {
      $objTemp = new clsFuncionario($this->cod_servidor);
      $detalhe = $objTemp->detalhe();
      $detalhe = $detalhe['idpes']->detalhe();

      $this->campoRotulo('nm_servidor', 'Servidor', $detalhe['nome']);
      $this->campoOculto('cod_servidor', $this->cod_servidor);
      $this->campoOculto('ref_cod_instituicao_original', $this->ref_cod_instituicao_original);
    }
    else {
      $parametros = new clsParametrosPesquisas();
      $parametros->setSubmit(0);
      $parametros->adicionaCampoSelect('cod_servidor', 'ref_cod_pessoa_fj', 'nome');
      $this->campoListaPesq('cod_servidor', 'Servidor', $opcoes,
        $this->cod_servidor, 'pesquisa_funcionario_lst.php', '', FALSE, '', '',
        NULL, NULL, '', FALSE, $parametros->serializaCampos(), TRUE);
    }

    $this->inputsHelper()->integer('cod_docente_inep', array('label' => 'Código INEP', 'required' => false));

    $helperOptions = array('objectName' => 'deficiencias');
    $options       = array('label' => 'Deficiências', 'size' => 50, 'required' => false,
                           'options' => array('value' => null));

    $this->inputsHelper()->multipleSearchDeficiencias('', $options, $helperOptions);

    $opcoes = array('' => 'Selecione');

    if (class_exists('clsPmieducarFuncao')) {
      if (is_numeric($this->ref_cod_instituicao)) {
        $objTemp = new clsPmieducarFuncao();
        $objTemp->setOrderby("nm_funcao ASC");
        $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
          NULL, NULL, 1, $this->ref_cod_instituicao);

        if (is_array($lista) && count($lista)) {
          foreach ($lista as $registro) {
            $opcoes[$registro['cod_funcao'] . '-' . $registro['professor']] = $registro['nm_funcao'];
          }
        }
      }
    }
    else {
      echo "<!--\nErro\nClasse clsPmieducarFuncao nao encontrada\n-->";
      $opcoes = array('' => 'Erro na geracao');
    }

    $this->campoTabelaInicio('funcao', 'Funções Servidor',
      array("Função", "Componentes Curriculares", "Cursos", "Matrícula"), ($this->ref_cod_funcao));

    $funcao = 'popless()';

    $this->campoLista('ref_cod_funcao', 'Função', $opcoes, $this->ref_cod_funcao,
      'funcaoChange(this)', '', '', '');

    $this->campoRotulo('disciplina', 'Componentes Curriculares',
      "<img src='imagens/lupa_antiga.png' border='0' style='cursor:pointer;' alt='Buscar Componente Curricular' title='Buscar Componente Curricular' onclick=\"$funcao\">");

    $funcao = 'popCurso()';

    $this->campoRotulo('curso', 'Curso',
      "<img src='imagens/lupa_antiga.png' border='0' style='cursor:pointer;' alt='Buscar Cursos' title='Buscar Cursos' onclick=\"$funcao\">");

    $this->campoTexto('matricula', 'Matricula', $this->matricula);

    $this->campoTabelaFim();

    if (strtoupper($this->tipoacao) == 'EDITAR') {
      $this->campoTextoInv('total_horas_alocadas_', 'Total de Horas Alocadadas',
        $this->total_horas_alocadas, 9, 20);

      $hora = explode(':', $this->total_horas_alocadas);
      $this->total_horas_alocadas = $hora[0] + ($hora[1] / 60);
      $this->campoOculto('total_horas_alocadas', $this->total_horas_alocadas);
      $this->acao_enviar = 'acao2()';
    }

    if ($this->carga_horaria) {
      $horas = (int) $this->carga_horaria;
      $minutos = round(($this->carga_horaria - (int) $this->carga_horaria) * 60);
      $hora_formatada = sprintf('%02d:%02d', $horas, $minutos);
    }

    $this->campoHora('carga_horaria', 'Carga Horária', $hora_formatada, TRUE,
      'Número de horas deve ser maior que horas alocadas','',FALSE);

    // Dados do docente no Inep/Educacenso.
    if ($this->docente) {
      $docenteMapper = new Educacenso_Model_DocenteDataMapper();

      $docenteInep = NULL;
      try {
        $docenteInep = $docenteMapper->find(array('docente' => $this->cod_servidor));
      }
      catch (Exception $e) {
      }

      if (isset($docenteInep)) {
        $this->campoRotulo('_inep_cod_docente', 'Código do docente no Educacenso/Inep',
          $docenteInep->docenteInep);

        if (isset($docenteInep->nomeInep)) {
          $this->campoRotulo('_inep_nome_docente', 'Nome do docente no Educacenso/Inep',
            $docenteInep->nomeInep);
        }
      }
    }

    $opcoes = array('' => 'Selecione');
    if (class_exists('clsCadastroEscolaridade')) {
      $objTemp = new clsCadastroEscolaridade();
      $lista = $objTemp->lista();

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes[$registro['idesco']] = $registro['descricao'];
        }
      }
    }
    else {
      echo "<!--\nErro\nClasse clsCadastroEscolaridade nao encontrada\n-->";
      $opcoes = array('' => 'Erro na geracao');
    }   

    $obj_permissoes = new clsPermissoes();
    if ($obj_permissoes->permissao_cadastra( 632, $this->pessoa_logada, 4)){
      $script = "javascript:showExpansivelIframe(350, 135, 'educar_escolaridade_cad_pop.php');";
      $script = "<img id='img_deficiencia' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";  
    }else
      $script = NULL;    

    $this->campoLista('ref_idesco', 'Escolaridade', $opcoes,
      $this->ref_idesco, '', FALSE, '', $script, FALSE, FALSE);    

    $resources = array(NULL => 'Selecione',
                       1 => Portabilis_String_Utils::toLatin1('Concluído'),
                       2 => 'Em andamento');

    $options = array('label' => Portabilis_String_Utils::toLatin1('Situação do curso superior 1'), 'resources' => $resources, 'value' => $this->situacao_curso_superior_1, 'required' => false);
    $this->inputsHelper()->select('situacao_curso_superior_1', $options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1('Possui formação/complementação pedagógica 1'), 'value' => $this->formacao_complementacao_pedagogica_1);
    $this->inputsHelper()->checkbox('formacao_complementacao_pedagogica_1', $options); 

    $options       = array('label' => Portabilis_String_Utils::toLatin1('Curso superior 1'), 'required'   => false);  
    $helperOptions = array('objectName'         => 'codigo_curso_superior_1',
                           'hiddenInputOptions' => array('options' => array('value' => $this->codigo_curso_superior_1)));
    $this->inputsHelper()->simpleSearchCursoSuperior(null, $options, $helperOptions);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ano de início do curso superior 1'), 'placeholder' => '','value' => $this->ano_inicio_curso_superior_1, 'max_length' => 4, 'size' => 5, 'required' => false);
    $this->inputsHelper()->integer('ano_inicio_curso_superior_1', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ano de conclusão do curso superior 1'), 'placeholder' => '','value' => $this->ano_conclusao_curso_superior_1, 'max_length' => 4, 'size' => 5, 'required' => false);
    $this->inputsHelper()->integer('ano_conclusao_curso_superior_1', $options);    

    $resources = array(NULL => 'Selecione',
                       1 => Portabilis_String_Utils::toLatin1('Pública'),
                       2 => 'Privada');

    $options = array('label' => Portabilis_String_Utils::toLatin1('Tipo de instituição do curso superior 1'), 'resources' => $resources, 'value' => $this->tipo_instituicao_curso_superior_1, 'required' => false);
    $this->inputsHelper()->select('tipo_instituicao_curso_superior_1', $options);       

    $options       = array('label' => Portabilis_String_Utils::toLatin1('Instituição do curso superior 1'), 'required'   => false);  
    $helperOptions = array('objectName'         => 'instituicao_curso_superior_1',
                           'hiddenInputOptions' => array('options' => array('value' => $this->instituicao_curso_superior_1)));
    $this->inputsHelper()->simpleSearchIes(null, $options, $helperOptions);   

    $this->campoQuebra();

    $resources = array(NULL => 'Selecione',
                       1 => Portabilis_String_Utils::toLatin1('Concluído'),
                       2 => 'Em andamento');

    $options = array('label' => Portabilis_String_Utils::toLatin1('Situação do curso superior 2'), 'resources' => $resources, 'value' => $this->situacao_curso_superior_2, 'required' => false);
    $this->inputsHelper()->select('situacao_curso_superior_2', $options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1('Possui formação/complementação pedagógica 2'), 'value' => $this->formacao_complementacao_pedagogica_2);
    $this->inputsHelper()->checkbox('formacao_complementacao_pedagogica_2', $options); 

    $options       = array('label' => Portabilis_String_Utils::toLatin1('Curso superior 2'), 'required'   => false);  
    $helperOptions = array('objectName'         => 'codigo_curso_superior_2',
                           'hiddenInputOptions' => array('options' => array('value' => $this->codigo_curso_superior_2)));
    $this->inputsHelper()->simpleSearchCursoSuperior(null, $options, $helperOptions);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ano de início do curso superior 2'), 'placeholder' => '','value' => $this->ano_inicio_curso_superior_2, 'max_length' => 4, 'size' => 5, 'required' => false);
    $this->inputsHelper()->integer('ano_inicio_curso_superior_2', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ano de conclusão do curso superior 2'), 'placeholder' => '','value' => $this->ano_conclusao_curso_superior_2, 'max_length' => 4, 'size' => 5, 'required' => false);
    $this->inputsHelper()->integer('ano_conclusao_curso_superior_2', $options);    

    $resources = array(NULL => 'Selecione',
                       1 => Portabilis_String_Utils::toLatin1('Pública'),
                       2 => 'Privada');

    $options = array('label' => Portabilis_String_Utils::toLatin1('Tipo de instituição do curso superior 2'), 'resources' => $resources, 'value' => $this->tipo_instituicao_curso_superior_2, 'required' => false);
    $this->inputsHelper()->select('tipo_instituicao_curso_superior_2', $options);       

    $options       = array('label' => Portabilis_String_Utils::toLatin1('Instituição do curso superior 2'), 'required'   => false);  
    $helperOptions = array('objectName'         => 'instituicao_curso_superior_2',
                           'hiddenInputOptions' => array('options' => array('value' => $this->instituicao_curso_superior_2)));
    $this->inputsHelper()->simpleSearchIes(null, $options, $helperOptions);     

    $this->campoQuebra();

    $resources = array(NULL => 'Selecione',
                       1 => Portabilis_String_Utils::toLatin1('Concluído'),
                       2 => 'Em andamento');

    $options = array('label' => Portabilis_String_Utils::toLatin1('Situação do curso superior 3'), 'resources' => $resources, 'value' => $this->situacao_curso_superior_3, 'required' => false);
    $this->inputsHelper()->select('situacao_curso_superior_3', $options);   

    $options = array('label' => Portabilis_String_Utils::toLatin1('Possui formação/complementação pedagógica 3'), 'value' => $this->formacao_complementacao_pedagogica_3);
    $this->inputsHelper()->checkbox('formacao_complementacao_pedagogica_3', $options); 

    $options       = array('label' => Portabilis_String_Utils::toLatin1('Curso superior 3'), 'required'   => false);  
    $helperOptions = array('objectName'         => 'codigo_curso_superior_3',
                           'hiddenInputOptions' => array('options' => array('value' => $this->codigo_curso_superior_3)));
    $this->inputsHelper()->simpleSearchCursoSuperior(null, $options, $helperOptions);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ano de início do curso superior 3'), 'placeholder' => '','value' => $this->ano_inicio_curso_superior_3, 'max_length' => 4, 'size' => 5, 'required' => false);
    $this->inputsHelper()->integer('ano_inicio_curso_superior_3', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ano de conclusão do curso superior 3'), 'placeholder' => '','value' => $this->ano_conclusao_curso_superior_3, 'max_length' => 4, 'size' => 5, 'required' => false);
    $this->inputsHelper()->integer('ano_conclusao_curso_superior_3', $options);    

    $resources = array(NULL => 'Selecione',
                       1 => Portabilis_String_Utils::toLatin1('Pública'),
                       2 => 'Privada');

    $options = array('label' => Portabilis_String_Utils::toLatin1('Tipo de instituição do curso superior 3'), 'resources' => $resources, 'value' => $this->tipo_instituicao_curso_superior_3, 'required' => false);
    $this->inputsHelper()->select('tipo_instituicao_curso_superior_3', $options);       

    $options       = array('label' => Portabilis_String_Utils::toLatin1('Instituição do curso superior 3'), 'required'   => false);  
    $helperOptions = array('objectName'         => 'instituicao_curso_superior_3',
                           'hiddenInputOptions' => array('options' => array('value' => $this->instituicao_curso_superior_3)));
    $this->inputsHelper()->simpleSearchIes(null, $options, $helperOptions);   

    $this->campoQuebra();

    $options = array('label' => Portabilis_String_Utils::toLatin1('Pós-Graduação - Especialização'), 'value' => $this->pos_especializacao);
    $this->inputsHelper()->checkbox('pos_especializacao', $options); 

    $options = array('label' => Portabilis_String_Utils::toLatin1('Pós-Graduação - Mestrado'), 'value' => $this->pos_mestrado);
    $this->inputsHelper()->checkbox('pos_mestrado', $options); 

    $options = array('label' => Portabilis_String_Utils::toLatin1('Pós-Graduação - Doutorado'), 'value' => $this->pos_doutorado);
    $this->inputsHelper()->checkbox('pos_doutorado', $options); 

    $options = array('label' => Portabilis_String_Utils::toLatin1('Pós-Graduação - Nenhuma'), 'value' => $this->pos_nenhuma);
    $this->inputsHelper()->checkbox('pos_nenhuma', $options); 

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Específico para Creche (0 a 3 anos)'), 'value' => $this->curso_creche);
    $this->inputsHelper()->checkbox('curso_creche', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Específico para Pré-Escola (4 e 5 anos)'), 'value' => $this->curso_pre_escola);
    $this->inputsHelper()->checkbox('curso_pre_escola', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Específico para anos iniciais do ensino fundamental'), 'value' => $this->curso_anos_iniciais);
    $this->inputsHelper()->checkbox('curso_anos_iniciais', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Específico para anos finais do ensino fundamental'), 'value' => $this->curso_anos_finais);
    $this->inputsHelper()->checkbox('curso_anos_finais', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Específico para ensino médio'), 'value' => $this->curso_ensino_medio);
    $this->inputsHelper()->checkbox('curso_ensino_medio', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Específico para educação de jovens e adultos'), 'value' => $this->curso_eja);
    $this->inputsHelper()->checkbox('curso_eja', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Específico para educação especial'), 'value' => $this->curso_educacao_especial);
    $this->inputsHelper()->checkbox('curso_educacao_especial', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Específico para educaão indígena'), 'value' => $this->curso_educacao_indigena);
    $this->inputsHelper()->checkbox('curso_educacao_indigena', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Específico para educação do campo'), 'value' => $this->curso_educacao_campo);
    $this->inputsHelper()->checkbox('curso_educacao_campo', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Específico para educação ambiental'), 'value' => $this->curso_educacao_ambiental);
    $this->inputsHelper()->checkbox('curso_educacao_ambiental', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Específico para educação em direitos humanos'), 'value' => $this->curso_educacao_direitos_humanos);
    $this->inputsHelper()->checkbox('curso_educacao_direitos_humanos', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Gênero e diversidade sexual'), 'value' => $this->curso_genero_diversidade_sexual);
    $this->inputsHelper()->checkbox('curso_genero_diversidade_sexual', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Direito das crianças e adolescentes'), 'value' => $this->curso_direito_crianca_adolescente);
    $this->inputsHelper()->checkbox('curso_direito_crianca_adolescente', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Educação para as relações etnicorraciais e História e cultura Afro-Brasileira e Africana'), 'value' => $this->curso_relacoes_etnicorraciais);
    $this->inputsHelper()->checkbox('curso_relacoes_etnicorraciais', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Outros'), 'value' => $this->curso_outros);
    $this->inputsHelper()->checkbox('curso_outros', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Curso de Formação Continuada(min. 80hrs) - Nenhum'), 'value' => $this->curso_nenhum);
    $this->inputsHelper()->checkbox('curso_nenhum', $options);

    Portabilis_View_Helper_Application::loadJQueryLib($this);

    $scripts = array(
      '/modules/Cadastro/Assets/Javascripts/Servidor.js'
      );

    Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

    $styles = array ('/modules/Cadastro/Assets/Stylesheets/Servidor.css');

    Portabilis_View_Helper_Application::loadStylesheet($this, $styles);

  }

  function Novo()
  {
    $timesep = explode(':', $this->carga_horaria);
    $hour    = $timesep[0] + ((int) ($timesep[1] / 60));
    $min     = abs(((int) ($timesep[1] / 60)) - ($timesep[1] / 60)) . '<br>';

    $this->carga_horaria = $hour + $min;
    $this->carga_horaria = $hour + $min;

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      'educar_servidor_lst.php');

    $obj = new clsPmieducarServidor($this->cod_servidor, NULL, NULL, NULL, NULL,
      NULL, NULL, $this->ref_cod_instituicao);

    if ($obj->detalhe()) {
      $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);
      $obj = new clsPmieducarServidor($this->cod_servidor,
        NULL, $this->ref_idesco, $this->carga_horaria,
        NULL, NULL, 1, $this->ref_cod_instituicao);
      $obj = $this->addCamposCenso($obj);
      $editou = $obj->edita();

      if ($editou) {
        $this->cadastraFuncoes();
        $this->createOrUpdateInep();
        $this->createOrUpdateDeficiencias();

        include 'educar_limpa_sessao_curso_disciplina_servidor.php';

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
        header('Location: educar_servidor_lst.php');
        die();
      }
    }
    else {
      $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);
      $obj_2 = new clsPmieducarServidor($this->cod_servidor,
        NULL, $this->ref_idesco, $this->carga_horaria,
        NULL, NULL, 1, $this->ref_cod_instituicao);
      $obj_2 = $this->addCamposCenso($obj_2);
      $cadastrou = $obj_2->cadastra();

      if ($cadastrou) {
        $this->cadastraFuncoes();
        $this->createOrUpdateInep();
        $this->createOrUpdateDeficiencias();

        include 'educar_limpa_sessao_curso_disciplina_servidor.php';

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
        header("Location: educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}");
        die();
      }
    }

    $this->mensagem = 'Cadastro não realizado.<br>';
    return FALSE;
  }

  function Editar()
  {
    $timesep = explode(':', $this->carga_horaria);
    $hour    = $timesep[0] + ((int) ($timesep[1] / 60));
    $min     = abs(((int) ($timesep[1] / 60)) - ($timesep[1] / 60)) . '<br>';
    $this->carga_horaria = $hour + $min;
    $this->carga_horaria = $hour + $min;

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      'educar_servidor_lst.php');

    if ($this->ref_cod_instituicao == $this->ref_cod_instituicao_original) {
      $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);

      $obj = new clsPmieducarServidor($this->cod_servidor, NULL,
        $this->ref_idesco, $this->carga_horaria, NULL, NULL, 1, $this->ref_cod_instituicao);
      $obj = $this->addCamposCenso($obj);
      $editou = $obj->edita();

      if ($editou) {
        $this->cadastraFuncoes();
        $this->createOrUpdateInep();
        $this->createOrUpdateDeficiencias();

        include 'educar_limpa_sessao_curso_disciplina_servidor.php';

        $this->mensagem .= 'Edição efetuada com sucesso.<br>';
        header("Location: educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}");
        die();
      }
    }
    else {
      $obj_quadro_horario = new clsPmieducarQuadroHorarioHorarios(NULL, NULL,
        NULL, NULL, NULL, NULL, $this->cod_servidor, NULL, NULL, NULL, NULL,
        NULL, NULL, 1, $this->ref_cod_instituicao);

      if ($obj_quadro_horario->detalhe()) {
        $this->mensagem = "Edição não realizada. O servidor está vinculado a um quadro de horários.<br>";
        return FALSE;
      }
      else {
        $obj_quadro_horario = new clsPmieducarQuadroHorarioHorarios(NULL, NULL,
          NULL, NULL, NULL, NULL, NULL, $this->cod_servidor, NULL, NULL, NULL,
          NULL, NULL, 1, NULL, $this->ref_cod_instituicao);

        if ($obj_quadro_horario->detalhe()) {
          $this->mensagem = "Edição não realizada. O servidor está vinculado a um quadro de horários.<br>";
          return FALSE;
        }
        else {
          $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);

          $obj = new clsPmieducarServidor($this->cod_servidor,
            NULL, $this->ref_idesco, $this->carga_horaria,
            NULL, NULL, 0, $this->ref_cod_instituicao_original);
          $obj = $this->addCamposCenso($obj);
          $editou = $obj->edita();

          if ($editou) {
            $obj = new clsPmieducarServidor($this->cod_servidor,
              NULL, $this->ref_idesco,
              $this->carga_horaria, NULL, NULL, 1, $this->ref_cod_instituicao);

            if ($obj->existe()) {
              $cadastrou = $obj->edita();
            }
            else {
              $cadastrou = $obj->cadastra();
            }

            if ($cadastrou) {
              $this->cadastraFuncoes();
              $this->createOrUpdateInep();
              $this->createOrUpdateDeficiencias();

              include 'educar_limpa_sessao_curso_disciplina_servidor.php';

              $this->mensagem .= "Edição efetuada com sucesso.<br>";
              header("Location: educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}");
              die();
            }
          }
        }
      }
    }

    $this->mensagem = "Edição não realizada.<br>";
    return FALSE;
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7,
      'educar_servidor_lst.php');

    $obj_quadro_horario = new clsPmieducarQuadroHorarioHorarios(NULL, NULL, NULL,
      NULL, NULL, NULL, $this->cod_servidor, NULL, NULL, NULL, NULL, NULL,
      NULL, 1, $this->ref_cod_instituicao);

    if ($obj_quadro_horario->detalhe()) {
      $this->mensagem = "Exclusão não realizada. O servidor está vinculado a um quadro de horários.<br>";
      return FALSE;
    }
    else {
      $obj_quadro_horario = new clsPmieducarQuadroHorarioHorarios(NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, $this->cod_servidor, NULL, NULL, NULL,
        NULL, NULL, 1, NULL, $this->ref_cod_instituicao);

      if ($obj_quadro_horario->detalhe()) {
        $this->mensagem = "Exclusão não realizada. O servidor está vinculado a um quadro de horários.<br>";
        return FALSE;
      }
      else {
        $obj = new clsPmieducarServidor($this->cod_servidor,
          NULL, $this->ref_idesco, $this->carga_horaria,
          NULL, NULL, 0, $this->ref_cod_instituicao_original);

        $excluiu = $obj->excluir();

        if ($excluiu) {
          $this->excluiFuncoes();
          $this->mensagem .= "Exclusão efetuada com sucesso.<br>";
          header("Location: educar_servidor_lst.php");
          die();
        }
      }
    }

    $this->mensagem = 'Exclusão não realizada.<br>';
    return FALSE;
  }

  function addCamposCenso($obj){

    $obj->situacao_curso_superior_1 = $this->situacao_curso_superior_1;
    $obj->formacao_complementacao_pedagogica_1 = $this->formacao_complementacao_pedagogica_1 == 'on' ? 1 : 0;
    $obj->codigo_curso_superior_1 = $this->codigo_curso_superior_1_id;
    $obj->ano_inicio_curso_superior_1 = $this->ano_inicio_curso_superior_1;
    $obj->ano_conclusao_curso_superior_1 = $this->ano_conclusao_curso_superior_1;
    $obj->tipo_instituicao_curso_superior_1 = $this->tipo_instituicao_curso_superior_1;
    $obj->instituicao_curso_superior_1 = $this->instituicao_curso_superior_1_id;
    $obj->situacao_curso_superior_2 = $this->situacao_curso_superior_2;
    $obj->formacao_complementacao_pedagogica_2 = $this->formacao_complementacao_pedagogica_2 == 'on' ? 1 : 0;
    $obj->codigo_curso_superior_2 = $this->codigo_curso_superior_2_id;
    $obj->ano_inicio_curso_superior_2 = $this->ano_inicio_curso_superior_2;
    $obj->ano_conclusao_curso_superior_2 = $this->ano_conclusao_curso_superior_2;
    $obj->tipo_instituicao_curso_superior_2 = $this->tipo_instituicao_curso_superior_2;
    $obj->instituicao_curso_superior_2 = $this->instituicao_curso_superior_2_id;
    $obj->situacao_curso_superior_3 = $this->situacao_curso_superior_3;
    $obj->formacao_complementacao_pedagogica_3 = $this->formacao_complementacao_pedagogica_3 == 'on' ? 1 : 0;
    $obj->codigo_curso_superior_3 = $this->codigo_curso_superior_3_id;
    $obj->ano_inicio_curso_superior_3 = $this->ano_inicio_curso_superior_3;
    $obj->ano_conclusao_curso_superior_3 = $this->ano_conclusao_curso_superior_3;
    $obj->tipo_instituicao_curso_superior_3 = $this->tipo_instituicao_curso_superior_3;
    $obj->instituicao_curso_superior_3 = $this->instituicao_curso_superior_3_id;
    $obj->pos_especializacao = $this->pos_especializacao == 'on' ? 1 : 0;
    $obj->pos_mestrado = $this->pos_mestrado == 'on' ? 1 : 0;
    $obj->pos_doutorado = $this->pos_doutorado == 'on' ? 1 : 0;
    $obj->pos_nenhuma = $this->pos_nenhuma == 'on' ? 1 : 0;
    $obj->curso_creche = $this->curso_creche == 'on' ? 1 : 0;
    $obj->curso_pre_escola = $this->curso_pre_escola == 'on' ? 1 : 0;
    $obj->curso_anos_iniciais = $this->curso_anos_iniciais == 'on' ? 1 : 0;
    $obj->curso_anos_finais = $this->curso_anos_finais == 'on' ? 1 : 0;
    $obj->curso_ensino_medio = $this->curso_ensino_medio == 'on' ? 1 : 0;
    $obj->curso_eja = $this->curso_eja == 'on' ? 1 : 0;
    $obj->curso_educacao_especial = $this->curso_educacao_especial == 'on' ? 1 : 0;
    $obj->curso_educacao_indigena = $this->curso_educacao_indigena == 'on' ? 1 : 0;
    $obj->curso_educacao_campo = $this->curso_educacao_campo == 'on' ? 1 : 0;
    $obj->curso_educacao_ambiental = $this->curso_educacao_ambiental == 'on' ? 1 : 0;
    $obj->curso_educacao_direitos_humanos = $this->curso_educacao_direitos_humanos == 'on' ? 1 : 0;
    $obj->curso_genero_diversidade_sexual = $this->curso_genero_diversidade_sexual == 'on' ? 1 : 0;
    $obj->curso_direito_crianca_adolescente = $this->curso_direito_crianca_adolescente == 'on' ? 1 : 0;
    $obj->curso_relacoes_etnicorraciais = $this->curso_relacoes_etnicorraciais == 'on' ? 1 : 0;
    $obj->curso_outros = $this->curso_outros == 'on' ? 1 : 0;
    $obj->curso_nenhum = $this->curso_nenhum == 'on' ? 1 : 0;
    return $obj; 
  }

  function cadastraFuncoes()
  {
    @session_start();
    $cursos_disciplina = $_SESSION['cursos_disciplina'];
    $cursos_servidor   = $_SESSION['cursos_servidor'];
    @session_write_close();

    $existe_funcao_professor = FALSE;
    // echo "<pre>";var_dump($this->ref_cod_funcao);die;
    if ($this->ref_cod_funcao) {
      $cont = -1;
      $this->excluiFuncoes();
      foreach ($this->ref_cod_funcao as $funcao) {
        $cont++;
        $funcao_professor = explode('-', $funcao);
        $funcao = array_shift($funcao_professor);
        $professor = array_shift($funcao_professor);

        if ($professor) {
          $existe_funcao_professor = true;
        }

        $obj_servidor_funcao = new clsPmieducarServidorFuncao($this->ref_cod_instituicao, $this->cod_servidor, $funcao, $this->matricula[$cont]);

        if (! $obj_servidor_funcao->existe()) {
          $obj_servidor_funcao->cadastra();
        }
      }
    }

    if ($existe_funcao_professor) {
      if ($cursos_disciplina) {
        $this->excluiDisciplinas();
        foreach ($cursos_disciplina as $curso => $disciplinas) {
          if ($disciplinas) {
            foreach ($disciplinas as $disciplina) {
              $obj_servidor_disciplina = new clsPmieducarServidorDisciplina(
                $disciplina, $this->ref_cod_instituicao, $this->cod_servidor,
                $curso);

              if (!$obj_servidor_disciplina->existe()) {
                $obj_servidor_disciplina->cadastra();
              }
            }
          }
        }
      }

      if ($cursos_servidor) {
        $this->excluiCursos();
        foreach ($cursos_servidor as $curso) {
          $obj_curso_servidor = new clsPmieducarServidorCursoMinistra($curso,
            $this->ref_cod_instituicao, $this->cod_servidor);

          if (!$obj_curso_servidor->existe()) {
            $det_curso_servidor = $obj_curso_servidor->cadastra();
          }
        }
      }
    }
  }

  function excluiFuncoes()
  {
    $obj_servidor_funcao = new clsPmieducarServidorFuncao($this->ref_cod_instituicao,
      $this->cod_servidor);

    $obj_servidor_funcao->excluirTodos();
  }

  function excluiDisciplinas()
  {
    $obj_servidor_disciplina = new clsPmieducarServidorDisciplina(NULL,
      $this->ref_cod_instituicao, $this->cod_servidor);

    $obj_servidor_disciplina->excluirTodos();
  }

  function excluiCursos()
  {
    $obj_servidor_curso = new clsPmieducarServidorCursoMinistra(NULL,
      $this->ref_cod_instituicao, $this->cod_servidor);

    $obj_servidor_curso->excluirTodos();
  }

  protected function createOrUpdateDeficiencias(){
    $servidorId = $this->cod_servidor;

    $sql = "delete from cadastro.fisica_deficiencia where ref_idpes = $1";
    Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($servidorId)), false);

    foreach ($this->getRequest()->deficiencias as $id) {
      if (! empty($id)) {
        $deficiencia = new clsCadastroFisicaDeficiencia($servidorId, $id);
        $deficiencia->cadastra();
      }
    }

  }

  protected function createOrUpdateInep(){
    Portabilis_Utils_Database::fetchPreparedQuery("DELETE FROM modules.educacenso_cod_docente WHERE cod_servidor = $1",array('params' => array($this->cod_servidor)), false );
    if ($this->cod_docente_inep){      
      $sql = "INSERT INTO modules.educacenso_cod_docente (cod_servidor,cod_docente_inep, fonte, created_at) 
                                                  VALUES ($1, $2,'U', 'NOW()')";
      Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($this->cod_servidor, $this->cod_docente_inep)));
    }
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
?>
<script type="text/javascript">
/**
 * Carrega as opções de um campo select de funções via Ajax
 */
function getFuncao(id_campo)
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var campoFuncao      = document.getElementById(id_campo);
  campoFuncao.length   = 1;

  if (campoFuncao) {
    campoFuncao.disabled = true;
    campoFuncao.options[0].text = 'Carregando funções';

    var xml = new ajax(atualizaLstFuncao,id_campo);
    xml.envia("educar_funcao_xml.php?ins="+campoInstituicao+"&professor=true");
  }
  else {
    campoFuncao.options[0].text = 'Selecione';
  }
}

/**
 * Parse de resultado da chamada Ajax de getFuncao(). Adiciona cada item
 * retornado como option do select
 */
function atualizaLstFuncao(xml)
{
  var campoFuncao = document.getElementById(arguments[1]);

  campoFuncao.length = 1;
  campoFuncao.options[0].text = 'Selecione uma função';
  campoFuncao.disabled = false;

  funcaoChange(campoFuncao);

  var funcoes = xml.getElementsByTagName('funcao');
  if (funcoes.length) {
    for (var i = 0; i < funcoes.length; i++) {
      campoFuncao.options[campoFuncao.options.length] =
        new Option(funcoes[i].firstChild.data, funcoes[i].getAttribute('cod_funcao'), false, false);
    }
  }
  else {
    campoFuncao.options[0].text = 'A instituição não possui funções de servidores';
  }
}


/**
 * Altera a visibilidade de opções extras
 *
 * Quando a função escolhida para o servidor for do tipo professor, torna as
 * opções de escolha de disciplina e cursos visíveis
 *
 * É um toggle on/off
 */
function funcaoChange(campo)
{
  var valor = campo.value.split("-");
  var id = /[0-9]+/.exec(campo.id)[0];
  var professor = (valor[1] == true);

  var campo_img  = document.getElementById('td_disciplina['+ id +']').lastChild.lastChild;
  var campo_img2 = document.getElementById('td_curso['+ id +']').lastChild.lastChild;

  // Se for professor
  if (professor == true) {
    setVisibility(campo_img,  true);
    setVisibility(campo_img2, true);
  }
  else {
    setVisibility(campo_img,  false);
    setVisibility(campo_img2, false);
  }
}


/**
 * Chama as funções getFuncao e funcaoChange para todas as linhas da tabela
 * de função de servidor
 */
function trocaTodasfuncoes() {
  for (var ct = 0; ct < tab_add_1.id; ct++) {
    // Não executa durante onload senão, funções atuais são substituídas
    if (onloadCallOnce == false) {
      getFuncao('ref_cod_funcao[' + ct + ']');
    }
    funcaoChange(document.getElementById('ref_cod_funcao[' + ct + ']'));
  }
}


/**
 * Verifica se ref_cod_instituicao existe via DOM e dá um bind no evento
 * onchange do elemento para executar a função trocaTodasfuncoes()
 */
if (document.getElementById('ref_cod_instituicao')) {
  var ref_cod_instituicao = document.getElementById('ref_cod_instituicao');

  // Função anônima para evento onchance do select de instituição
  ref_cod_instituicao.onchange = function() {
    trocaTodasfuncoes();
    var xml = new ajax(function(){});
    xml.envia("educar_limpa_sessao_curso_disciplina_servidor.php");
  }
}


/**
 * Chama as funções funcaoChange e getFuncao após a execução da função addRow
 */
tab_add_1.afterAddRow = function () {
  funcaoChange(document.getElementById('ref_cod_funcao['+(tab_add_1.id - 1)+']'));
  getFuncao('ref_cod_funcao['+(tab_add_1.id-1)+']');
}


/**
 * Variável de estado, deve ser checada por funções que queiram executar ou
 * não um trecho de código apenas durante o onload
 */
var onloadCallOnce = true;
window.onload = function() {
  trocaTodasfuncoes();
  onloadCallOnce = false;
}


function getArrayHora(hora) {
  var array_h;
  if (hora) {
    array_h = hora.split(":");
  }
  else {
    array_h = new Array(0,0);
  }

  return array_h;
}

function acao2()
{
  var total_horas_alocadas = getArrayHora(document.getElementById('total_horas_alocadas').value);
  var carga_horaria = (document.getElementById('carga_horaria').value).replace(',', '.');

  if (parseFloat(total_horas_alocadas) > parseFloat(carga_horaria)) {
    alert('Atenção, carga horária deve ser maior que horas alocadas!');

    return false;
  }
  else {
    acao();
  }
}

if (document.getElementById('total_horas_alocadas')) {
  document.getElementById('total_horas_alocadas').style.textAlign='right';
}


function popless()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var campoServidor = document.getElementById('cod_servidor').value;
  pesquisa_valores_popless1('educar_servidor_disciplina_lst.php?ref_cod_servidor='+campoServidor+'&ref_cod_instituicao='+campoInstituicao, '');
}

function popCurso()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var campoServidor = document.getElementById('cod_servidor').value;
  pesquisa_valores_popless('educar_servidor_curso_lst.php?ref_cod_servidor='+campoServidor+'&ref_cod_instituicao='+campoInstituicao, '');
}

function pesquisa_valores_popless1(caminho, campo)
{
  new_id = DOM_divs.length;
  div = 'div_dinamico_' + new_id;
  if (caminho.indexOf('?') == -1) {
    showExpansivel(850, 500, '<iframe src="' + caminho + '?campo=' + campo + '&div=' + div + '&popless=1" frameborder="0" height="100%" width="100%" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', 'Pesquisa de valores' );
  }
  else {
    showExpansivel(850, 500, '<iframe src="' + caminho + '&campo=' + campo + '&div=' + div + '&popless=1" frameborder="0" height="100%" width="100%" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', 'Pesquisa de valores' );
  }
}

var handleGetInformacoesServidor = function(dataResponse){

  // deficiencias
  $j('#deficiencias').closest('tr').show();
  $j('#cod_docente_inep').val(dataResponse.inep).closest('tr').show();

  $deficiencias = $j('#deficiencias');

  $j.each(dataResponse.deficiencias, function(id, nome) {
    $deficiencias.children("[value=" + id + "]").attr('selected', '');
  });

  $deficiencias.trigger('liszt:updated');  
};

function atualizaInformacoesServidor(){

  $j('#deficiencias').closest('tr').hide();
  $j('#deficiencias option').removeAttr('selected');
  $j('#deficiencias').trigger('liszt:updated');
  $j('#cod_docente_inep').closest('tr').hide();

  var servidor_id = $j('#cod_servidor').val();

  if (servidor_id != ''){
    var data = {
      servidor_id : servidor_id
    };
    var options = {
      url : getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'info-servidor', {}),
        dataType : 'json',
        data : data,
        success : handleGetInformacoesServidor
    };
    getResources(options);
  }
}
$j(document).ready(function() {
  
  atualizaInformacoesServidor();

  // fixup multipleSearchDeficiencias size:
  $j('#deficiencias_chzn ul').css('width', '307px');  
  $j('#deficiencias_chzn input').css('height', '25px');

  $j('#cod_servidor').attr('onchange', 'atualizaInformacoesServidor();');
});
</script>