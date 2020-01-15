<?php

/**
 * i-Educar - Sistema de gestÃ£o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de ItajaÃ­
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa Ã© software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo
 * sob os termos da LicenÃ§a PÃºblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versÃ£o 2 da LicenÃ§a, como (a seu critÃ©rio)
 * qualquer versÃ£o posterior.
 *
 * Este programa Ã© distribuÃ­Â­do na expectativa de que seja Ãºtil, porÃ©m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implÃ­Â­cita de COMERCIABILIDADE OU
 * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral
 * do GNU para mais detalhes.
 *
 * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU junto
 * com este programa; se nÃ£o, escreva para a Free Software Foundation, Inc., no
 * endereÃ§o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponÃ­vel desde a versÃ£o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

/**
 * clsIndexBase class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponÃ­vel desde a versÃ£o 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Dispensa Componente Curricular');
        $this->processoAp = 578;
    }
}

/**
 * indice class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponÃ­vel desde a versÃ£o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
    var $pessoa_logada;

    var $observacao;

    var $ref_cod_matricula;
    var $ref_cod_turma;
    var $ref_cod_serie;
    var $ref_cod_disciplina;
    var $ref_sequencial;
    var $ref_cod_instituicao;
    var $ref_cod_escola;

    function Inicializar()
    {
        $retorno = 'Novo';


        $this->ref_cod_disciplina = $_GET['ref_cod_disciplina'];
        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7,
            'educar_disciplina_dependencia_lst.php?ref_ref_cod_matricula=' . $this->ref_cod_matricula);

        if (is_numeric($this->ref_cod_matricula)) {
            $obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula, NULL,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

            $det_matricula = $obj_matricula->detalhe();

            if (!$det_matricula) {
                $this->simpleRedirect("educar_matricula_lst.php");
            }

            $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
            $this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];
        } else {
            $this->simpleRedirect("educar_matricula_lst.php");
        }

        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina)
        ) {
            $obj = new clsPmieducarDisciplinaDependencia($this->ref_cod_matricula,
                $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina);

            $registro = $obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7)) {
                    $this->fexcluir = TRUE;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = $retorno == 'Editar' ?
            sprintf('educar_disciplina_dependencia_det.php?ref_cod_matricula=%d&ref_cod_serie=%d&ref_cod_escola=%d&ref_cod_disciplina=%d',
                $registro['ref_cod_matricula'], $registro['ref_cod_serie'],
                $registro['ref_cod_escola'], $registro['ref_cod_disciplina']) :
            'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;

        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Disciplinas de dependência', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        /**
         * Busca dados da matricula
         */
        $obj_ref_cod_matricula = new clsPmieducarMatricula();
        $detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));

        $obj_aluno = new clsPmieducarAluno();
        $det_aluno = array_shift($det_aluno = $obj_aluno->lista($detalhe_aluno['ref_cod_aluno'],
            NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1));

        $obj_escola = new clsPmieducarEscola($this->ref_cod_escola, NULL, NULL, NULL,
            NULL, NULL, NULL, NULL, NULL, NULL, 1);

        $det_escola = $obj_escola->detalhe();
        $this->ref_cod_instituicao = $det_escola['ref_cod_instituicao'];

        $obj_matricula_turma = new clsPmieducarMatriculaTurma();
        $lst_matricula_turma = $obj_matricula_turma->lista($this->ref_cod_matricula,
            NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_serie, NULL,
            $this->ref_cod_escola);

        if (is_array($lst_matricula_turma)) {
            $det = array_shift($lst_matricula_turma);
            $this->ref_cod_turma = $det['ref_cod_turma'];
            $this->ref_sequencial = $det['sequencial'];
        }

        $this->campoRotulo('nm_aluno', 'Nome do Aluno', $det_aluno['nome_aluno']);

        if (!isset($this->ref_cod_turma)) {
            $this->mensagem = 'Para cadastrar uma disciplina de depend&ecirc;ncia de um aluno, &eacute; necess&aacute;rio que este esteja enturmado.';
            return;
        }

        // primary keys
        $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);
        $this->campoOculto('ref_cod_serie', $this->ref_cod_serie);
        $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);

        $opcoes = array('' => 'Selecione');

        // Seleciona os componentes curriculares da turma

        try {
            $componentes = App_Model_IedFinder::getComponentesTurma($this->ref_cod_serie,
                $this->ref_cod_escola, $this->ref_cod_turma);
        } catch (App_Model_Exception $e) {
            $this->mensagem = $e->getMessage();
            return;
        }

        foreach ($componentes as $componente) {
            $opcoes[$componente->id] = $componente->nome;
        }

        if ($this->ref_cod_disciplina) {
            $this->campoRotulo('nm_disciplina', 'Disciplina', $opcoes[$this->ref_cod_disciplina]);
            $this->campoOculto('ref_cod_disciplina', $this->ref_cod_disciplina);
        } else {
            $this->campoLista('ref_cod_disciplina', 'Disciplina', $opcoes,
                $this->ref_cod_disciplina);
        }

        $this->campoMemo('observacao', 'Observa&ccedil;&atilde;o', $this->observacao, 60, 10, FALSE);
    }

    function existeComponenteSerie()
    {
        $db = new clsBanco();
        $sql = "SELECT  EXISTS (SELECT 1
                                FROM pmieducar.escola_serie_disciplina
                               WHERE ref_ref_cod_serie = {$this->ref_cod_serie}
                                 AND ref_ref_cod_escola = {$this->ref_cod_escola}
                                 AND ref_cod_disciplina = {$this->ref_cod_disciplina}
                                 AND escola_serie_disciplina.ativo = 1)";
        return dbBool($db->campoUnico($sql));
    }

    function validaQuantidadeDisciplinasDependencia()
    {
        $query = <<<'SQL'
            SELECT t.ano
            FROM pmieducar.matricula AS m
            INNER JOIN pmieducar.matricula_turma AS mt ON mt.ref_cod_matricula = m.cod_matricula
            INNER JOIN pmieducar.turma AS t ON t.cod_turma = mt.ref_cod_turma
            WHERE m.cod_matricula = $1
SQL;
        $ano = Portabilis_Utils_Database::selectField($query, [$this->ref_cod_matricula]);

        $db = new clsBanco();
        $db->consulta("SELECT (CASE
                               WHEN escola.utiliza_regra_diferenciada AND rasa.regra_avaliacao_diferenciada_id IS NOT NULL
                               THEN regra_avaliacao_diferenciada.qtd_disciplinas_dependencia
                               ELSE regra_avaliacao.qtd_disciplinas_dependencia
                                END) AS qtd_disciplinas_dependencia
                         FROM pmieducar.escola,
                              pmieducar.serie
                    LEFT JOIN modules.regra_avaliacao_serie_ano AS rasa ON (rasa.serie_id = serie.cod_serie AND rasa.ano_letivo = {$ano})
                    LEFT JOIN modules.regra_avaliacao ON (rasa.regra_avaliacao_id = regra_avaliacao.id)
                    LEFT JOIN modules.regra_avaliacao AS regra_avaliacao_diferenciada ON (rasa.regra_avaliacao_diferenciada_id = regra_avaliacao_diferenciada.id)
                        WHERE serie.cod_serie = {$this->ref_cod_serie}
                          AND escola.cod_escola = {$this->ref_cod_escola}");

        $db->ProximoRegistro();
        $m = $db->Tupla();
        $qtdDisciplinasLimite = $m['qtd_disciplinas_dependencia'];

        $db->consulta("SELECT COUNT(1) as qtd
                    FROM pmieducar.disciplina_dependencia
                    WHERE ref_cod_matricula = {$this->ref_cod_matricula} ");
        $db->ProximoRegistro();
        $m = $db->Tupla();
        $qtdDisciplinas = $m['qtd'];

        $valid = $qtdDisciplinas < $qtdDisciplinasLimite;

        if (!$valid) {
            $this->mensagem .= "A regra desta s&eacute;rie limita a quantidade de disciplinas de depend&ecirc;ncia para {$qtdDisciplinasLimite}. <br/>";
        }

        return $valid;
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7,
            'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        if (!$this->validaQuantidadeDisciplinasDependencia()) {
            return false;
        }

        if (!$this->existeComponenteSerie()) {
            $this->mensagem = 'O componente não está habilitado na série da escola.';
            $this->url_cancelar = 'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;
            $this->nome_url_cancelar = 'Cancelar';
            return false;
        }


        $sql = 'SELECT MAX(cod_disciplina_dependencia) + 1 FROM pmieducar.disciplina_dependencia';
        $db = new clsBanco();
        $max_cod_disciplina_dependencia = $db->CampoUnico($sql);

        // Caso nÃ£o exista nenhuma dispensa, atribui o cÃ³digo 1, tabela nÃ£o utiliza sequences
        $max_cod_disciplina_dependencia = $max_cod_disciplina_dependencia > 0 ? $max_cod_disciplina_dependencia : 1;

        $obj = new clsPmieducarDisciplinaDependencia($this->ref_cod_matricula,
            $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina,
            $this->observacao, $max_cod_disciplina_dependencia);

        if ($obj->existe()) {
            $obj = new clsPmieducarDisciplinaDependencia($this->ref_cod_matricula,
                $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina,
                $this->observacao);

            $obj->edita();
            $this->simpleRedirect('educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
            $this->simpleRedirect('educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br />';

        return FALSE;
    }

    function Editar()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7,
            'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        $obj = new clsPmieducarDisciplinaDependencia($this->ref_cod_matricula,
            $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina,
            $this->observacao);

        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br />';
            $this->simpleRedirect('educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o nÃ£o realizada.<br />';

        return FALSE;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7,
            'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        $obj = new clsPmieducarDisciplinaDependencia($this->ref_cod_matricula,
            $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina,
            $this->observacao);

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br />';
            $this->simpleRedirect('educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
        }

        $this->mensagem = 'Exclus&atilde;o nÃ£o realizada.<br />';

        return FALSE;
    }
}

// Instancia objeto de pÃ¡gina
$pagina = new clsIndexBase();

// Instancia objeto de conteÃºdo
$miolo = new indice();

// Atribui o conteÃºdo Ã   pÃ¡gina
$pagina->addForm($miolo);

// Gera o cÃ³digo HTML
$pagina->MakeAll();
