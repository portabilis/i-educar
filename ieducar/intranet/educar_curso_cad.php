<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Curso');
        $this->processoAp = '566';
    }
}

class indice extends clsCadastro
{
    public $pessoa_logada;

    public $cod_curso;
    public $ref_usuario_cad;
    public $ref_cod_tipo_regime;
    public $ref_cod_nivel_ensino;
    public $ref_cod_tipo_ensino;
    public $nm_curso;
    public $sgl_curso;
    public $qtd_etapas;
    public $carga_horaria;
    public $ato_poder_publico;
    public $habilitacao;
    public $objetivo_curso;
    public $publico_alvo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_usuario_exc;
    public $ref_cod_instituicao;
    public $padrao_ano_escolar;
    public $hora_falta;

    public $incluir;
    public $excluir_;
    public $habilitacao_curso;
    public $curso_sem_avaliacao = true;

    public $multi_seriado;
    public $modalidade_curso;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_curso = $this->getQueryString('cod_curso');

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            566,
            $this->pessoa_logada,
            3,
            'educar_curso_lst.php'
        );

        if (is_numeric($this->cod_curso)) {
            $obj = new clsPmieducarCurso($this->cod_curso);
            $registro = $obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(
                    566,
                    $this->pessoa_logada,
                    3
                );

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ?
        "educar_curso_det.php?cod_curso={$registro['cod_curso']}" : 'educar_curso_lst.php';

        $this->breadcrumb('Cursos', ['educar_index.php' => 'Escola']);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        if ($_POST['habilitacao_curso']) {
            $this->habilitacao_curso = unserialize(urldecode($_POST['habilitacao_curso']));
        }

        $qtd_habilitacao = (count($this->habilitacao_curso) == 0) ?
     	1 : (count($this->habilitacao_curso) + 1);

        if (is_numeric($this->cod_curso) && $_POST['incluir'] != 'S' && empty($_POST['excluir_'])) {
            $obj = new clsPmieducarHabilitacaoCurso(null, $this->cod_curso);
            $registros = $obj->lista(null, $this->cod_curso);

            if ($registros) {
                foreach ($registros as $campo) {
                    $this->habilitacao_curso[$campo[$qtd_habilitacao]]['ref_cod_habilitacao_'] = $campo['ref_cod_habilitacao'];

                    $qtd_habilitacao++;
                }
            }
        }

        if ($_POST['habilitacao']) {
            $this->habilitacao_curso[$qtd_habilitacao]['ref_cod_habilitacao_'] = $_POST['habilitacao'];

            $qtd_habilitacao++;
            unset($this->habilitacao);
        }

        // primary keys
        $this->campoOculto('cod_curso', $this->cod_curso);

        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // Nível ensino
        $opcoes = [ '' => 'Selecione' ];

        if ($this->ref_cod_instituicao) {
			$objTemp = new clsPmieducarNivelEnsino();
			$lista = $objTemp->lista(
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_instituicao
		);

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_nivel_ensino']] = $registro['nm_nivel'];
                }
            }
        }

        $script = 'javascript:showExpansivelIframe(520, 230, \'educar_nivel_ensino_cad_pop.php\');';
        if ($this->ref_cod_instituicao) {
            $script = "<img id='img_nivel_ensino' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        } else {
            $script = "<img id='img_nivel_ensino' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }

        $this->campoLista(
			'ref_cod_nivel_ensino',
			'Nível Ensino',
			$opcoes,
			$this->ref_cod_nivel_ensino,
			'',
			false,
			'',
			$script
		);

        // Tipo ensino
        $opcoes = ['' => 'Selecione'];

        if ($this->ref_cod_instituicao) {
			$objTemp = new clsPmieducarTipoEnsino();
			$objTemp->setOrderby('nm_tipo');
			$lista = $objTemp->lista(
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_instituicao
      	);

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_tipo_ensino']] = $registro['nm_tipo'];
                }
            }
        }

        $script = 'javascript:showExpansivelIframe(520, 150, \'educar_tipo_ensino_cad_pop.php\');';
        if ($this->ref_cod_instituicao) {
            $script = "<img id='img_tipo_ensino' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        } else {
            $script = "<img id='img_tipo_ensino' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }

        $this->campoLista(
			'ref_cod_tipo_ensino',
			'Tipo Ensino',
			$opcoes,
			$this->ref_cod_tipo_ensino,
			'',
			false,
			'',
			$script
    	);

        // Tipo regime
        $opcoes = ['' => 'Selecione'];

        if ($this->ref_cod_instituicao) {
			$objTemp = new clsPmieducarTipoRegime();
			$objTemp->setOrderby('nm_tipo');

			$lista = $objTemp->lista(
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->ref_cod_instituicao
            );

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_tipo_regime']] = $registro['nm_tipo'];
                }
            }
        }

        $script = 'javascript:showExpansivelIframe(520, 120, \'educar_tipo_regime_cad_pop.php\');';

        if ($this->ref_cod_instituicao) {
            $script = "<img id='img_tipo_regime' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        } else {
            $script = "<img id='img_tipo_regime' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }

        $this->campoLista(
			'ref_cod_tipo_regime',
			'Tipo Regime',
			$opcoes,
			$this->ref_cod_tipo_regime,
			'',
			false,
			'',
			$script,
			false,
			false
   		);

        // Outros campos
        $this->campoTexto('nm_curso', 'Curso', $this->nm_curso, 30, 255, true);

        $this->campoTexto('sgl_curso', 'Sigla Curso', $this->sgl_curso, 15, 15, false);

        $this->campoNumero('qtd_etapas', 'Quantidade Etapas', $this->qtd_etapas, 2, 2, true);

        if (is_numeric($this->hora_falta)) {
			$this->campoMonetario(
				'hora_falta',
				'Hora Falta',
				number_format($this->hora_falta, 2, ',', ''),
				5,
				5,
				false,
				'',
				'',
				''
			);
        } else {
			$this->campoMonetario(
				'hora_falta',
				'Hora Falta',
				$this->hora_falta,
				5,
				5,
				false,
				'',
				'',
				''
			);
        }

        $this->campoMonetario(
			'carga_horaria',
			'Carga Hor&aacute;ria',
			$this->carga_horaria,
			7,
			7,
			true
    	);

        $this->campoTexto(
			'ato_poder_publico',
			'Ato Poder Público',
			$this->ato_poder_publico,
			30,
			255,
			false
    	);

        $this->campoOculto('excluir_', '');
        $qtd_habilitacao = 1;
        $aux;

        $this->campoQuebra();
        if ($this->habilitacao_curso) {
            foreach ($this->habilitacao_curso as $campo) {
                if ($this->excluir_ == $campo['ref_cod_habilitacao_']) {
                    $this->habilitacao_curso[$campo['ref_cod_habilitacao']] = null;
                    $this->excluir_ = null;
                } else {
                    $obj_habilitacao = new clsPmieducarHabilitacao($campo['ref_cod_habilitacao_']);
                    $obj_habilitacao_det = $obj_habilitacao->detalhe();
                    $nm_habilitacao = $obj_habilitacao_det['nm_tipo'];

                    $this->campoTextoInv(
						"ref_cod_habilitacao_{$campo['ref_cod_habilitacao_']}",
						'',
						$nm_habilitacao,
						30,
						255,
						false,
						false,
						false,
						'',
						"<a href='#' onclick=\"getElementById('excluir_').value = '{$campo['ref_cod_habilitacao_']}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>"
					);

                    $aux[$qtd_habilitacao]['ref_cod_habilitacao_'] = $campo['ref_cod_habilitacao_'];

                    $qtd_habilitacao++;
                }
            }

            unset($this->habilitacao_curso);
            $this->habilitacao_curso = $aux;
        }

        $this->campoOculto('habilitacao_curso', serialize($this->habilitacao_curso));

        // Habilitação
        $opcoes = ['' => 'Selecione'];

        if ($this->ref_cod_instituicao) {
			$objTemp = new clsPmieducarHabilitacao();
			$objTemp->setOrderby('nm_tipo');

			$lista = $objTemp->lista(
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_instituicao
      	);

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_habilitacao']] = $registro['nm_tipo'];
                }
            }
        }

        $script = 'javascript:showExpansivelIframe(520, 225, \'educar_habilitacao_cad_pop.php\');';
        $script = "<img id='img_habilitacao' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";

        $this->campoLista(
			'habilitacao',
			'Habilitação',
			$opcoes,
			$this->habilitacao,
			'',
			false,
			'',
			"<a href='#' onclick=\"getElementById('incluir').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>{$script}",
			false,
			false
    	);
        $this->campoOculto('incluir', '');
        $this->campoQuebra();

        // Padrão ano escolar
        $this->campoCheck('padrao_ano_escolar', 'Padrão Ano Escolar', $this->padrao_ano_escolar);

        $this->campoCheck('multi_seriado', 'Multisseriado', $this->multi_seriado);

        // Objetivo do curso
        $this->campoMemo(
			'objetivo_curso',
			'Objetivo Curso',
			$this->objetivo_curso,
			60,
			5,
			false
    	);

        // Público alvo
        $this->campoMemo(
			'publico_alvo',
			'Público Alvo',
			$this->publico_alvo,
			60,
			5,
			false
    )	;

        $resources = [
			null => 'Selecione',
			1 => 'Ensino regular',
			2 => 'Educação Especial - Modalidade Substitutiva',
			3 => 'Educação de Jovens e Adultos (EJA)',
			4 => 'Educação profissional'
		];

        $options = ['label' => 'Modalidade do curso', 'resources' => $resources, 'value' => $this->modalidade_curso];
        $this->inputsHelper()->select('modalidade_curso', $options);

        $helperOptions = ['objectName' => 'etapacurso'];
        $options = ['label' => 'Etapas que o curso contêm', 'size' => 50, 'required' => false, 'options' => ['value' => null]];

        $this->inputsHelper()->multipleSearchEtapacurso('', $options, $helperOptions);
    }

    public function Novo()
    {
        if ($this->habilitacao_curso && $this->incluir != 'S' && empty($this->excluir_)) {
            $this->carga_horaria = str_replace('.', '', $this->carga_horaria);
            $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);
            $this->hora_falta = str_replace('.', '', $this->hora_falta);
            $this->hora_falta = str_replace(',', '.', $this->hora_falta);

            $this->padrao_ano_escolar = is_null($this->padrao_ano_escolar) ? 0 : 1;
            $this->multi_seriado = is_null($this->multi_seriado) ? 0 : 1;

            $obj = new clsPmieducarCurso(
				null,
				$this->pessoa_logada,
				$this->ref_cod_tipo_regime,
				$this->ref_cod_nivel_ensino,
				$this->ref_cod_tipo_ensino,
				null,
				$this->nm_curso,
				$this->sgl_curso,
				$this->qtd_etapas,
				null,
				null,
				null,
				null,
				$this->carga_horaria,
				$this->ato_poder_publico,
				null,
				$this->objetivo_curso,
				$this->publico_alvo,
				null,
				null,
				1,
				null,
				$this->ref_cod_instituicao,
				$this->padrao_ano_escolar,
				$this->hora_falta,
				null,
				$this->multi_seriado
      		);
            $obj->modalidade_curso = $this->modalidade_curso;

            $this->cod_curso = $cadastrou = $obj->cadastra();
            if ($cadastrou) {
                $curso = new clsPmieducarCurso($this->cod_curso);
                $curso = $curso->detalhe();

                $auditoria = new clsModulesAuditoriaGeral('curso', $this->pessoa_logada, $this->cod_curso);
                $auditoria->inclusao($curso);

                $this->gravaEtapacurso($cadastrou);
                $this->habilitacao_curso = unserialize(urldecode($this->habilitacao_curso));

                if ($this->habilitacao_curso) {
                    foreach ($this->habilitacao_curso as $campo) {
                        $obj = new clsPmieducarHabilitacaoCurso(
							$campo['ref_cod_habilitacao_'],
							$cadastrou
						);

                        $cadastrou2 = $obj->cadastra();

                        if (!$cadastrou2) {
                            $this->mensagem = 'Cadastro não realizado.<br>';


                            return false;
                        }
                    }
                }

                $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                $this->simpleRedirect('educar_curso_lst.php');
            }

            $this->mensagem = 'Cadastro não realizado.<br>';


            return false;
        }

        return true;
    }

    public function Editar()
    {
        if ($this->habilitacao_curso && $this->incluir != 'S' && empty($this->excluir_)) {
            $this->carga_horaria = str_replace('.', '', $this->carga_horaria);
            $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);
            $this->hora_falta = str_replace('.', '', $this->hora_falta);
            $this->hora_falta = str_replace(',', '.', $this->hora_falta);

            $this->padrao_ano_escolar = is_null($this->padrao_ano_escolar) ? 0 : 1;
            $this->multi_seriado = is_null($this->multi_seriado) ? 0 : 1;

            $obj = new clsPmieducarCurso(
				$this->cod_curso,
				null,
				$this->ref_cod_tipo_regime,
				$this->ref_cod_nivel_ensino,
				$this->ref_cod_tipo_ensino,
				null,
				$this->nm_curso,
				$this->sgl_curso,
				$this->qtd_etapas,
				null,
				null,
				null,
				null,
				$this->carga_horaria,
				$this->ato_poder_publico,
				null,
				$this->objetivo_curso,
				$this->publico_alvo,
				null,
				null,
				1,
				$this->pessoa_logada,
				$this->ref_cod_instituicao,
				$this->padrao_ano_escolar,
				$this->hora_falta,
				null,
				$this->multi_seriado
      		);
            $obj->modalidade_curso = $this->modalidade_curso;

            $detalheAntigo = $obj->detalhe();
            $alterouPadraoAnoEscolar = $detalheAntigo['padrao_ano_escolar'] != $this->padrao_ano_escolar;
            $editou = $obj->edita();
            if ($editou) {
                $detalheAtual = $obj->detalhe();
                $auditoria = new clsModulesAuditoriaGeral('curso', $this->pessoa_logada, $this->cod_curso);
                $auditoria->alteracao($detalheAntigo, $detalheAtual);

                $this->gravaEtapacurso($this->cod_curso);
                $this->habilitacao_curso = unserialize(urldecode($this->habilitacao_curso));
                $obj  = new clsPmieducarHabilitacaoCurso(null, $this->cod_curso);
                $excluiu = $obj->excluirTodos();

                if ($excluiu) {
                    if ($this->habilitacao_curso) {
                        foreach ($this->habilitacao_curso as $campo) {
                            $obj = new clsPmieducarHabilitacaoCurso(
								$campo['ref_cod_habilitacao_'],
								$this->cod_curso
              				);

                            $cadastrou2 = $obj->cadastra();

                            if (!$cadastrou2) {
                                $this->mensagem = 'Edição não realizada.<br>';


                                return false;
                            }
                        }
                    }
                }

                if ($alterouPadraoAnoEscolar) {
                    $this->updateClassStepsForCourse($this->cod_curso, $this->padrao_ano_escolar, date("Y"));
                }


                $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                $this->simpleRedirect('educar_curso_lst.php');
            }

            $this->mensagem = 'Edição não realizada.<br>';


            return false;
        }

        return true;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarCurso(
			$this->cod_curso,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			0,
			$this->pessoa_logada
    	);

        $curso = $obj->detalhe();
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $auditoria = new clsModulesAuditoriaGeral('curso', $this->pessoa_logada, $this->cod_curso);
            $auditoria->exclusao($curso);
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_curso_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';


        return false;
    }

    public function gravaEtapacurso($cod_curso)
    {
        Portabilis_Utils_Database::fetchPreparedQuery('DELETE FROM etapas_curso_educacenso WHERE curso_id = $1', ['params' => [$cod_curso]]);
        foreach ($this->getRequest()->etapacurso as $etapaId) {
            if (! empty($etapaId)) {
                Portabilis_Utils_Database::fetchPreparedQuery('INSERT INTO etapas_curso_educacenso VALUES ($1 , $2)', ['params' => [$etapaId, $cod_curso] ]);
            }
        }
    }

    public function  updateClassStepsForCourse($courseCode, $standerdSchoolYear, $currentYear)
    {
        $classStepsObject = new ClsPmieducarTurmaModulo();

        $classStepsObject->removeStepsOfClassesForCourseAndYear($courseCode, $currentYear);

        if ($standerdSchoolYear == 0) {
            $classStepsObject->copySchoolStepsIntoClassesForCourseAndYear($courseCode, $currentYear);
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
function getNivelEnsino(xml_nivel_ensino)
{
  var campoNivelEnsino = document.getElementById('ref_cod_nivel_ensino');
  var DOM_array = xml_nivel_ensino.getElementsByTagName('nivel_ensino');

  if (DOM_array.length) {
    campoNivelEnsino.length = 1;
    campoNivelEnsino.options[0].text = 'Selecione um nível de ensino';
    campoNivelEnsino.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoNivelEnsino.options[campoNivelEnsino.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_nivel_ensino"),
        false, false
      );
    }
  }
  else {
    campoNivelEnsino.options[0].text = 'A instituição não possui nenhum nível de ensino';
  }
}

function getTipoEnsino(xml_tipo_ensino)
{
  var campoTipoEnsino = document.getElementById('ref_cod_tipo_ensino');
  var DOM_array = xml_tipo_ensino.getElementsByTagName('tipo_ensino');

  if (DOM_array.length) {
    campoTipoEnsino.length = 1;
    campoTipoEnsino.options[0].text = 'Selecione um tipo de ensino';
    campoTipoEnsino.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoTipoEnsino.options[campoTipoEnsino.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute('cod_tipo_ensino'),
        false, false
      );
    }
  }
  else {
    campoTipoEnsino.options[0].text = 'A instituição não possui nenhum tipo de ensino';
  }
}

function getTipoRegime(xml_tipo_regime)
{
  var campoTipoRegime = document.getElementById('ref_cod_tipo_regime');
  var DOM_array = xml_tipo_regime.getElementsByTagName( "tipo_regime" );

  if(DOM_array.length)
  {
    campoTipoRegime.length = 1;
    campoTipoRegime.options[0].text = 'Selecione um tipo de regime';
    campoTipoRegime.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoTipoRegime.options[campoTipoRegime.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_tipo_regime"),
        false, false
      );
    }
  }
  else {
    campoTipoRegime.options[0].text = 'A instituição não possui nenhum tipo de regime';
  }
}

function getHabilitacao(xml_habilitacao)
{
  var campoHabilitacao = document.getElementById('habilitacao');
  var DOM_array = xml_habilitacao.getElementsByTagName( "habilitacao" );

  if (DOM_array.length) {
    campoHabilitacao.length = 1;
    campoHabilitacao.options[0].text = 'Selecione uma habilitação';
    campoHabilitacao.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoHabilitacao.options[campoHabilitacao.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_habilitacao"),
        false, false
      );
    }
  }
  else {
    campoHabilitacao.options[0].text = 'A instituição não possui nenhuma habilitação';
  }
}

document.getElementById('ref_cod_instituicao').onchange = function()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

  var campoNivelEnsino = document.getElementById('ref_cod_nivel_ensino');
  campoNivelEnsino.length = 1;
  campoNivelEnsino.disabled = true;
  campoNivelEnsino.options[0].text = 'Carregando nível de ensino';

  var campoTipoEnsino = document.getElementById('ref_cod_tipo_ensino');
  campoTipoEnsino.length = 1;
  campoTipoEnsino.disabled = true;
  campoTipoEnsino.options[0].text = 'Carregando tipo de ensino';

  var campoTipoRegime = document.getElementById('ref_cod_tipo_regime');
  campoTipoRegime.length = 1;
  campoTipoRegime.disabled = true;
  campoTipoRegime.options[0].text = 'Carregando tipo de regime';

  var campoHabilitacao = document.getElementById('habilitacao');
  campoHabilitacao.length = 1;
  campoHabilitacao.disabled = true;
  campoHabilitacao.options[0].text = 'Carregando habilitação';

  var xml_nivel_ensino = new ajax(getNivelEnsino);
  xml_nivel_ensino.envia("educar_nivel_ensino_xml.php?ins="+campoInstituicao);

  var xml_tipo_ensino = new ajax(getTipoEnsino);
  xml_tipo_ensino.envia("educar_tipo_ensino_xml.php?ins="+campoInstituicao);

  var xml_tipo_regime = new ajax(getTipoRegime);
  xml_tipo_regime.envia("educar_tipo_regime_xml.php?ins="+campoInstituicao);

  var xml_habilitacao = new ajax(getHabilitacao);
  xml_habilitacao.envia("educar_habilitacao_xml.php?ins="+campoInstituicao);

  if (this.value == '') {
    $('img_nivel_ensino').style.display = 'none;';
    $('img_tipo_regime').style.display = 'none;';
    $('img_tipo_ensino').style.display = 'none;';
  }
  else {
    $('img_nivel_ensino').style.display = '';
    $('img_tipo_regime').style.display = '';
    $('img_tipo_ensino').style.display = '';
  }
}

function fixupEtapacursoSize(){
  $j('.search-field input').css('height', '30px')
}

  $etapacurso = $j('#etapacurso');

  $etapacurso.trigger('chosen:updated');
  var testezin;

var handleGetEtapacurso = function(dataResponse) {
  testezin = dataResponse['etapacurso'];

  $j.each(dataResponse['etapacurso'], function(id, value) {

    $etapacurso.children("[value=" + value + "]").attr('selected', '');
  });

  $etapacurso.trigger('chosen:updated');
}

var getEtapacurso = function() {

  if ($j('#cod_curso').val()!='') {

    var additionalVars = {
      curso_id : $j('#cod_curso').val(),
    };

    var options = {
      url : getResourceUrlBuilder.buildUrl('/module/Api/etapacurso', 'etapacurso', additionalVars),
      dataType : 'json',
      data : {},
      success : handleGetEtapacurso,
    };

    getResource(options);
  }
}

getEtapacurso();

$j(document).ready( function(){

  fixupEtapacursoSize();
});

</script>
