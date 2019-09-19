<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Faltas/Notas Aluno" );
        $this->processoAp = "642";
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    var $nm_aluno;
    var $ref_cod_aluno;
    var $ref_cod_matricula;
    var $ref_cod_turma;
    var $ref_ref_cod_serie;
    var $ref_cod_curso;
    var $ref_ref_cod_escola;
    var $ref_cod_instituicao;
    var $ref_cod_disciplina;
    var $nota;
    var $faltas;
    var $total_faltas;
    var $disciplina_modulo;
    var $ref_cod_tipo_avaliacao;
    var $ref_sequencial_matricula_turma;

    var $media;
    var $media_exame;
    var $aluno_exame;
    var $aprovado;
    var $conceitual;
    var $ano_letivo;
    var $falta_ch_globalizada;
    var $situacao;
    var $modulo;
    var $qtd_modulos;
    var $mat_modulo;
    var $ref_cod_curso_disciplina;
    var $padrao_ano_escolar;

    var $reprova_falta;
    var $media_especial;

    var $nota_foi_removida;

    function Inicializar()
    {


        $this->ref_cod_matricula=$_GET["ref_cod_matricula"];
        $this->ref_cod_turma=$_GET["ref_cod_turma"];
        $this->ref_sequencial_matricula_turma=$_GET["ref_sequencial_matricula_turma"];
        $this->modulo=$_GET["modulo"];
        $this->reprova_falta=$_GET["falta"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 642, $this->pessoa_logada, 7,  "educar_falta_nota_aluno_lst.php" );

        if( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_turma ) && is_numeric( $this->ref_sequencial_matricula_turma ) )
        {
            $obj_matricula_turma = new clsPmieducarMatriculaTurma();
            $lst_matricula_turma = $obj_matricula_turma->lista( $this->ref_cod_matricula,$this->ref_cod_turma,null,null,null,null,null,null,1,null,null,null,null,null,null,null,null,$this->ref_sequencial_matricula_turma );

            if ( is_array($lst_matricula_turma) )
            {
                $registro = array_shift($lst_matricula_turma);
                $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
                $det_curso = $obj_curso->detalhe();
                if(!$det_curso['edicao_final'])
                {
                    echo "<script language='javascript'>alert('Edição de nota não permitido');window.location='educar_falta_nota_aluno_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}';</script>"   ;
                    die();
                }

            }
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
            }
            if (is_numeric( $this->modulo ))
            {
                $retorno = "Editar";
            }
            else
            {
                $retorno = "Novo";
            }
        }
        else
        {
            $this->simpleRedirect('educar_falta_nota_aluno_lst');
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_falta_nota_aluno_det.php?ref_cod_matricula={$this->ref_cod_matricula}&ref_cod_turma={$this->ref_cod_turma}&sequencial={$this->ref_sequencial_matricula_turma}" : "educar_falta_nota_aluno_lst.php" ;
        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {

        $this->campoOculto( "ref_cod_matricula", $this->ref_cod_matricula );
        $this->campoOculto( "ref_cod_turma", $this->ref_cod_turma );
        $this->campoOculto( "ref_ref_cod_escola", $this->ref_ref_cod_escola );
        $this->campoOculto( "ref_ref_cod_serie", $this->ref_ref_cod_serie );
        $this->campoOculto( "ref_cod_curso", $this->ref_cod_curso );
        $this->campoOculto( "ref_cod_aluno", $this->ref_cod_aluno );
        $this->campoOculto( "ref_sequencial_matricula_turma", $this->ref_sequencial_matricula_turma );

        $this->campoOculto( "reprova_falta", $this->reprova_falta );

        $obj_matricula = new clsPmieducarMatricula( $this->ref_cod_matricula );
        $det_matricula = $obj_matricula->detalhe();
        $this->mat_modulo = $det_matricula["modulo"];
        $this->situacao = $det_matricula["aprovado"];

        if ($this->ref_ref_cod_serie)
        {
            $ano_matricula = $det_matricula["ano"];
            // busca o ano em q a escola esta em andamento
            $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
            $lst_ano_letivo = $obj_ano_letivo->lista( $this->ref_ref_cod_escola,null,null,null,1,null,null,null,null,1 );
            if ( is_array($lst_ano_letivo) )
            {
                $det_ano_letivo = array_shift($lst_ano_letivo);
                $ano_letivo = $det_ano_letivo["ano"];

                if ($ano_letivo != $ano_matricula)
                {
                    $this->simpleRedirect('educar_falta_nota_aluno_lst');
                }
            }
            else
            {
                $this->mensagem = "N&atilde;o foi poss&iacute;vel encontrar o Ano Letivo.";
                return false;
            }
        }

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista( $this->ref_cod_aluno,null,null,null,null,null,null,null,null,null,1 );
        if ( is_array($lst_aluno) )
        {
            $det_aluno = array_shift($lst_aluno);
            $this->nm_aluno = $det_aluno["nome_aluno"];
            $this->campoRotulo( "nm_aluno", "Aluno", $this->nm_aluno );
        }

        $obj_curso = new clsPmieducarCurso( $this->ref_cod_curso );
        $det_curso = $obj_curso->detalhe();
        $this->ref_cod_instituicao = $det_curso["ref_cod_instituicao"];
        $this->ref_cod_tipo_avaliacao = $det_curso["ref_cod_tipo_avaliacao"];
        $this->media = $det_curso["media"];
        $this->media_exame = $det_curso["media_exame"];
        $this->falta_ch_globalizada = $det_curso["falta_ch_globalizada"];
        $this->campoOculto( "ref_cod_instituicao", $this->ref_cod_instituicao );
        $this->campoOculto( "ref_cod_tipo_avaliacao", $this->ref_cod_tipo_avaliacao );
        $this->campoOculto( "media", $this->media );
        $this->campoOculto( "media_exame", $this->media_exame );
        $this->campoOculto( "falta_ch_globalizada", $this->falta_ch_globalizada );

        // verifico qual o tipo de avaliacao usado no curso
        $obj_tipo_avaliacao = new clsPmieducarTipoAvaliacao( $this->ref_cod_tipo_avaliacao );
        $det_tipo_avaliacao = $obj_tipo_avaliacao->detalhe();
        $this->conceitual = $det_tipo_avaliacao["conceitual"];
        $this->campoOculto( "conceitual", $this->conceitual );

        // lista todos os valores do tipo de avaliacao do curso
        $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
        $obj_avaliacao_valores->setOrderby("valor ASC");
        $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao );
        if ( is_array($lst_avaliacao_valores) )
        {
            $opcoes_valores = array( "" => "Selecione" );
            $opcoes_valores_remover = array( "-1" => "Remover Nota" );
            foreach ($lst_avaliacao_valores AS $valores)
            {
                $opcoes_valores[$valores['sequencial']] = $valores["nome"];
                $opcoes_valores_remover[$valores['sequencial']] = $valores["nome"];
            }
            $opcoes_valores_ = $opcoes_valores;
        }

//************************************* MATRICULADO NUMA SERIE *************************************//
        if ($this->ref_ref_cod_serie)
        {
            // busca o ano em q a escola esta em andamento
            $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
            $lst_ano_letivo = $obj_ano_letivo->lista( $this->ref_ref_cod_escola,null,null,null,1,null,null,null,null,1 );
            if ( is_array($lst_ano_letivo) )
            {
                $det_ano_letivo = array_shift($lst_ano_letivo);
                $this->ano_letivo = $det_ano_letivo["ano"];
                $this->campoOculto( "ano_letivo", $this->ano_letivo );
            }

            $this->padrao_ano_escolar = $det_curso["padrao_ano_escolar"];
            $this->campoOculto( "padrao_ano_escolar", $this->padrao_ano_escolar );

            // Caso o curso siga o padrao da escola
            if ($this->padrao_ano_escolar)
            {
                $obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
                $lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista( $this->ano_letivo,$this->ref_ref_cod_escola );
                if ( is_array($lst_ano_letivo_modulo) )
                {
                    // guarda a qtd de modulos a serem cursados
                    $this->qtd_modulos = count($lst_ano_letivo_modulo);
                }
            }// Caso o curso NÃO siga o padrao da escola
            else
            {
                $obj_turma_modulo = new clsPmieducarTurmaModulo();
                $lst_turma_modulo = $obj_turma_modulo->lista( $this->ref_cod_turma );
                if ( is_array($lst_turma_modulo) )
                {
                    // guarda a qtd de modulos a serem cursados
                    $this->qtd_modulos = count($lst_turma_modulo);
                }
            }

            // Armazena as disciplinas em que o aluno esta dispensado
            $obj_dispensa = new clsPmieducarDispensaDisciplina();
            $lst_dispensa = $obj_dispensa->lista( $this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,null,null,null,null,null,null,null,null,1 );
            if (is_array($lst_dispensa))
            {
                foreach ($lst_dispensa AS $key => $disciplina)
                {
                    $dispensa[$disciplina["ref_cod_disciplina"]] = $disciplina["ref_cod_disciplina"];
                }
            }

            $obj_esd = new clsPmieducarEscolaSerieDisciplina();
            $obj_esd->setOrderby("nm_disciplina");
            $lst_disciplinas = $obj_esd->lista( $this->ref_ref_cod_serie,$this->ref_ref_cod_escola,null,1,true );

            //  CASO SEJA EDITAR
            if ($this->modulo)
            {
                $obj_nota_aluno = new clsPmieducarNotaAluno();
                $lst_nota_aluno = $obj_nota_aluno->lista( null,null,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,null,$this->ref_cod_matricula,null,null,null,null,null,null,1,$this->modulo );
                if (is_array($lst_nota_aluno))
                {
                    foreach ( $lst_nota_aluno AS $key => $campo )
                    {
                        $lst_disciplina[$campo['ref_cod_disciplina']]['cod_nota_aluno'] = $campo['cod_nota_aluno'];

                        if ($campo['nota'])
                        {
                            $lst_disciplina[$campo['ref_cod_disciplina']]['nota'] = $campo['nota'];
                        }
                        else
                        {
                            $lst_disciplina[$campo['ref_cod_disciplina']]['nota'] = $campo['ref_sequencial'];
                        }
                    }
                }
                if ($this->falta_ch_globalizada)
                {
                    $obj_faltas = new clsPmieducarFaltas();
                    $lst_faltas = $obj_faltas->lista( $this->ref_cod_matricula,$this->modulo );
                    if (is_array($lst_faltas))
                    {
                        $det_faltas = array_shift($lst_faltas);
                        $faltas = $det_faltas['falta'];
                    }
                }
                else
                {
                    $obj_falta_aluno = new clsPmieducarFaltaAluno();
                    $lst_falta_aluno = $obj_falta_aluno->lista( null,null,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,null,$this->ref_cod_matricula,null,null,null,null,null,1,$this->modulo );
                    if (is_array($lst_falta_aluno))
                    {
                        foreach ( $lst_falta_aluno AS $key => $campo )
                        {
                            $lst_disciplina[$campo['ref_cod_disciplina']]['cod_falta_aluno'] = $campo['cod_falta_aluno'];
                            $lst_disciplina[$campo['ref_cod_disciplina']]['faltas'] = $campo['faltas'];
                        }
                    }
                }
            } //  CASO SEJA NOVO
            else
            {
                // Armazena as disciplinas que estao ainda sem nota no modulo
                $com_nota = array();

                if ( is_array($lst_disciplinas) )
                {
                    foreach ($lst_disciplinas AS $key => $disciplinas)
                    {
                        if ( !$dispensa[$disciplinas["ref_cod_disciplina"]] )
                        {
                            $obj_nota_aluno = new clsPmieducarNotaAluno();
                            $qtd_notas = $obj_nota_aluno->getQtdNotas( $this->ref_ref_cod_escola, $this->ref_ref_cod_serie, $disciplinas["ref_cod_disciplina"], $this->ref_cod_matricula );

                            if ($qtd_notas >= $this->mat_modulo)
                            {
                                $com_nota[$disciplinas["ref_cod_disciplina"]] = $qtd_notas;
                            }
                        }
                    }
                }
//              $this->mat_modulo++;
                $this->modulo = $this->mat_modulo;
            }
            $this->campoOculto( "mat_modulo", $this->mat_modulo );

            $this->campoRotulo( "modulo_", "M&oacute;dulo", $this->modulo );
            $this->campoOculto( "modulo", $this->modulo );

            // caso o aluno esteja de EXAME
            if ($this->qtd_modulos < $this->modulo)
            {
                if ( is_array($lst_disciplinas) )
                {
                    foreach ($lst_disciplinas AS $valor)
                    {
                        $obj_nota_aluno = new clsPmieducarNotaAluno();
                        $obj_nota_aluno->setOrderby("modulo ASC");
                        // lista todas as notas do aluno em uma determinada disciplina
                        $lst_nota_aluno = $obj_nota_aluno->lista( null,null,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,$valor["ref_cod_disciplina"],$this->ref_cod_matricula,null,null,null,null,null,null,1 );
                        if ( is_array($lst_nota_aluno) )
                        {
                            // guarda as notas do aluno
                            foreach ($lst_nota_aluno AS $key => $nota_aluno)
                            {
                                if ($this->qtd_modulos > $key)
                                {
                                    $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores( $nota_aluno["ref_ref_cod_tipo_avaliacao"], $nota_aluno["ref_sequencial"] );
                                    $det_avaliacao_valores = $obj_avaliacao_valores->detalhe();
                                    $soma_notas[$valor["ref_cod_disciplina"]][$key] = $det_avaliacao_valores["valor"];
                                }
                            }
                        }
                    }
                }
                // calcula a nota media do aluno
                if ( is_array($soma_notas) )
                {
                    foreach ($soma_notas AS $disciplina => $notas)
                    {
                        foreach ($notas as $nota)
                        {
                            $nota_media_aluno[$disciplina] += $nota;
                        }
                        $nota_media_aluno[$disciplina] /= ($this->modulo - 1);
                    }

                    // verifica se o aluno esta a baixo da media,
                    // caso positivo e o curso possua exame, dexa aluno em exame
                    foreach ($nota_media_aluno AS $disciplina => $nota)
                    {
                        /*
                        $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                        $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$nota,$nota );
                        if ( is_array($lst_avaliacao_valores) )
                        {
                            $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                            $valor = $det_avaliacao_valores["valor"];

                            if ($valor < $this->media && $this->media_exame)
                                $aluno_exame_disciplina[] = $disciplina;
                        }
                        */
                        if ($nota < $this->media && $this->media_exame)
                        {
                            $aluno_exame_disciplina[] = $disciplina;
                        }
                    }
                }
            }
            // caso aluno esteja de EXAME e a avaliacao NAO eh conceitual
            if ( ($this->qtd_modulos < $this->modulo) && !$this->conceitual )
            {
                $qtd_disciplinas_aluno_exame = 0;
                foreach ($aluno_exame_disciplina AS $key => $disciplina)
                {
                    if (!$dispensa[$disciplina] && !$com_nota[$disciplina])
                    {
                        $qtd_disciplinas_aluno_exame++;
                        $obj_disciplina = new clsPmieducarDisciplina( $disciplina );
                        $det_disciplina = $obj_disciplina->detalhe();
                        $nm_disciplina = $det_disciplina["nm_disciplina"];

                        if ($lst_disciplina[$disciplina]['nota'])
                        {
                            $lst_disciplina[$disciplina]['nota'] = number_format($lst_disciplina[$disciplina]['nota'], 2, ",", ".");
                        }

                        $this->campoTextoInv( "nm_disciplina_{$det_disciplina["nm_disciplina"]}", "Disciplina", $nm_disciplina, 30, 255,false,false,true );
                        $this->campoOculto( "disciplina_modulo[{$key}][ref_cod_disciplina]", $disciplina, "" );
                        $this->campoOculto( "disciplina_modulo[{$key}][cod_nota_aluno]", $lst_disciplina[$disciplina]['cod_nota_aluno'] );
//                      $this->campoLista( "disciplina_modulo[{$key}][nota]", " Nota Exame", $opcoes_valores, $lst_disciplina[$disciplina]['nota'] );

                        /**
                         * deixa obrigatorio em caso de edicao somente as notas que ja tinham sido
                         * preenchidas
                         */
                        $prenche_edicao_obrigatorio = $lst_disciplina[$disciplina]['nota']  || strtolower($this->tipoacao) == 'novo' ? true : false;


                        $this->campoMonetario( "disciplina_modulo[{$key}][nota]", " Nota Exame", $lst_disciplina[$disciplina]['nota'], 5, 5, $prenche_edicao_obrigatorio);
                    }
                    $this->campoOculto("qtd_disciplinas_aluno_exame", $qtd_disciplinas_aluno_exame);
                    $this->campoOculto("aluno_esta_em_exame", 1);
                }
            }// caso seja uma situacao normal
            else
            {

    //          echo "<br> NORMAL";
                if ( is_array($lst_disciplinas) )
                {
                    // falta na chamada EH globalizada
                    if ($this->falta_ch_globalizada)
                    {
    //                  echo "<br> FALTA GLOBALIZADA";
                        foreach ($lst_disciplinas AS $key => $disciplinas)
                        {
                            if ( !$dispensa[$disciplinas["ref_cod_disciplina"]] && !$com_nota[$disciplinas["ref_cod_disciplina"]] )
                            {
                                $obj_disciplina = new clsPmieducarDisciplina( $disciplinas["ref_cod_disciplina"] );
                                $det_disciplina = $obj_disciplina->detalhe();
                                $nm_disciplina = $det_disciplina["nm_disciplina"];

                                $this->campoTextoInv( "nm_disciplina_{$nm_disciplina}", "Disciplina", $nm_disciplina, 30, 255,false,false,true );
                                $this->campoOculto( "disciplina_modulo[{$key}][ref_cod_disciplina]", $disciplinas["ref_cod_disciplina"] );
                                $this->campoOculto( "disciplina_modulo[{$key}][cod_nota_aluno]", $lst_disciplina[$disciplinas["ref_cod_disciplina"]]['cod_nota_aluno'] );
                                /**
                                 * deixa obrigatorio em caso de edicao somente as notas que ja tinham sido
                                 * preenchidas
                                 */

                                $prenche_edicao_obrigatorio = $lst_disciplina[$disciplinas["ref_cod_disciplina"]]['nota']  || strtolower($this->tipoacao) == 'novo' ? true : false;

                                /**
                                 * existe nota? mostra a opção para remove-la
                                 */
                                if( $lst_disciplina[$disciplinas["ref_cod_disciplina"]]['nota'] && $this->mat_modulo <= $this->modulo + 1 )
                                {
                                    $opcoes_valores = $opcoes_valores_remover;
                                }

                                $this->campoLista( "disciplina_modulo[{$key}][nota]", " Nota", $opcoes_valores, $lst_disciplina[$disciplinas["ref_cod_disciplina"]]['nota'],"",false,"","",false,$prenche_edicao_obrigatorio );

                                $opcoes_valores = $opcoes_valores_;

                            }
                        }
                        $this->campoNumero( "total_faltas", " Faltas", $faltas, 2, 2, true );
                    }
                    else // falta na chamada NAO eh globalizada
                    {
    //                  echo "<br> FALTA NAO GLOBALIZADA";
                        foreach ($lst_disciplinas AS $key => $disciplinas)
                        {
                            if ( !$dispensa[$disciplinas["ref_cod_disciplina"]] && !$com_nota[$disciplinas["ref_cod_disciplina"]] )
                            {
                                $obj_disciplina = new clsPmieducarDisciplina( $disciplinas["ref_cod_disciplina"] );
                                $det_disciplina = $obj_disciplina->detalhe();
                                $nm_disciplina = $det_disciplina["nm_disciplina"];
                                $apura_falta = $det_disciplina["apura_falta"];

                                $this->campoTextoInv( "nm_disciplina_{$det_disciplina["nm_disciplina"]}", "Disciplina", $nm_disciplina, 30, 255,false,false,true );
                                $this->campoOculto( "disciplina_modulo[{$key}][ref_cod_disciplina]", $disciplinas["ref_cod_disciplina"] );
                                $this->campoOculto( "disciplina_modulo[{$key}][cod_nota_aluno]", $lst_disciplina[$disciplinas["ref_cod_disciplina"]]['cod_nota_aluno'] );

                                /**
                                 * deixa obrigatorio em caso de edicao somente as notas que ja tinham sido
                                 * preenchidas
                                 */
                                $prenche_edicao_obrigatorio = $lst_disciplina[$disciplinas["ref_cod_disciplina"]]['nota']  || strtolower($this->tipoacao) == 'novo' ? true : false;

                                /**
                                 * existe nota? mostra a opção para remove-la
                                 */
                                if($lst_disciplina[$disciplinas["ref_cod_disciplina"]]['nota'] && $this->mat_modulo <= $this->modulo + 1)
                                {
                                    $opcoes_valores = $opcoes_valores_remover;
                                }

                                if ($apura_falta)
                                {
                                    $this->campoLista( "disciplina_modulo[{$key}][nota]", " Nota", $opcoes_valores, $lst_disciplina[$disciplinas["ref_cod_disciplina"]]['nota'], "",true,"","",false,$prenche_edicao_obrigatorio );
                                    $this->campoOculto( "disciplina_modulo[{$key}][cod_falta_aluno]", $lst_disciplina[$disciplinas["ref_cod_disciplina"]]['cod_falta_aluno'] );
                                    $this->campoNumero( "disciplina_modulo[{$key}][faltas]", " Faltas", $lst_disciplina[$disciplinas["ref_cod_disciplina"]]['faltas'], 2, 2, $prenche_edicao_obrigatorio );
                                }
                                else
                                {
                                    $this->campoLista( "disciplina_modulo[{$key}][nota]", " Nota", $opcoes_valores, $lst_disciplina[$disciplinas["ref_cod_disciplina"]]['nota'],"",false,"","",false,$prenche_edicao_obrigatorio );
                                }

                                $opcoes_valores = $opcoes_valores_;
                            }
                        }
                    }
                }
                // caso seja o ultimo modulo e a avaliacao seja conceitual

                if ( ($this->qtd_modulos == $this->modulo) && $this->conceitual )
                {
                    $opcoes = array( "" => "Selecione", 1 => "Aprovado", 2 => "Reprovado" );
                    $this->campoLista( "aprovado", "Situa&ccedil;&atilde;o", $opcoes, $this->situacao );
                }
            }
        }
//************************************* MATRICULADO NUM CURSO *************************************//
        else
        {
            $obj_turma_modulo = new clsPmieducarTurmaModulo();
            $obj_turma_modulo->setOrderby("data_fim DESC");
            $lst_turma_modulo = $obj_turma_modulo->lista( $this->ref_cod_turma );
            if ( is_array($lst_turma_modulo) )
            {
                // guarda a qtd de modulos a serem cursados
                $this->qtd_modulos = count($lst_turma_modulo);

                // armazena o ano letivo pela maior data do modulo
                $det_turma_modulo = array_shift($lst_turma_modulo);
                $this->ano_letivo = dataFromPgToBr($det_turma_modulo["data_fim"], "Y");
                $this->campoOculto( "ano_letivo", $this->ano_letivo );
            }
            $this->campoOculto( "qtd_modulos", $this->qtd_modulos );

            $obj_disciplinas = new clsPmieducarDisciplina();
            $lst_disciplinas = $obj_disciplinas->lista( null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_curso );

            if ($this->modulo)
            {
                $obj_nota_aluno = new clsPmieducarNotaAluno();
                $lst_nota_aluno = $obj_nota_aluno->lista( null,null,null,null,null,null,$this->ref_cod_matricula,null,null,null,null,null,null,1,$this->modulo );
                if (is_array($lst_nota_aluno))
                {
                    foreach ( $lst_nota_aluno AS $key => $campo )
                    {
                        $lst_disciplina[$campo['ref_cod_curso_disciplina']]['cod_nota_aluno'] = $campo['cod_nota_aluno'];
                        if ($campo['nota'])
                        {
                            $lst_disciplina[$campo['ref_cod_curso_disciplina']]['nota'] = $campo['nota'];
                        }
                        else
                        {
                            $lst_disciplina[$campo['ref_cod_curso_disciplina']]['nota'] = $campo['ref_sequencial'];
                        }
                    }
                }
                if ($this->falta_ch_globalizada)
                {
                    $obj_faltas = new clsPmieducarFaltas();
                    $lst_faltas = $obj_faltas->lista( $this->ref_cod_matricula,$this->modulo );
                    if (is_array($lst_faltas))
                    {
                        $det_faltas = array_shift($lst_faltas);
                        $faltas = $det_faltas['falta'];
                    }
                }
                else
                {
                    $obj_falta_aluno = new clsPmieducarFaltaAluno();
                    $lst_falta_aluno = $obj_falta_aluno->lista( null,null,null,null,null,null,$this->ref_cod_matricula,null,null,null,null,null,1,$this->modulo );
                    if (is_array($lst_falta_aluno))
                    {
                        foreach ( $lst_falta_aluno AS $key => $campo )
                        {
                            $lst_disciplina[$campo['ref_cod_curso_disciplina']]['cod_falta_aluno'] = $campo['cod_falta_aluno'];
                            $lst_disciplina[$campo['ref_cod_curso_disciplina']]['faltas'] = $campo['faltas'];
                        }
                    }
                }
            }
            else
            {
                // Armazena as disciplinas que estao ainda sem nota no modulo
                $com_nota = array();
                if ( is_array($lst_disciplinas) )
                {
                    foreach ($lst_disciplinas AS $key => $disciplinas)
                    {
                        $obj_nota_aluno = new clsPmieducarNotaAluno();
                        $qtd_notas = $obj_nota_aluno->getQtdNotas( null, null, null, $this->ref_cod_matricula, $disciplinas["cod_disciplina"] );

                        if ($qtd_notas >= $this->mat_modulo)
                        {
                            $com_nota[$disciplinas["cod_disciplina"]] = $qtd_notas;
                        }
                    }
                }
//              $this->mat_modulo++;
                $this->modulo = $this->mat_modulo;
            }
            $this->campoOculto( "mat_modulo", $this->mat_modulo );

            $this->campoRotulo( "modulo_", "M&oacute;dulo", $this->modulo );
            $this->campoOculto( "modulo", $this->modulo );

            // caso o aluno esteja de EXAME
            if ($this->qtd_modulos < $this->modulo)
            {
                if ( is_array($lst_disciplinas) )
                {
                    foreach ($lst_disciplinas AS $valor)
                    {
                        $obj_nota_aluno = new clsPmieducarNotaAluno();
                        $obj_nota_aluno->setOrderby("modulo ASC");
                        // lista todas as notas do aluno em uma determinada disciplina

                        $lst_nota_aluno = $obj_nota_aluno->lista( null,null,null,null,null,null,$this->ref_cod_matricula,null,null,null,null,null,null,1,null,$valor["cod_disciplina"] );
                        if ( is_array($lst_nota_aluno) )
                        {
                            // guarda as notas do aluno
                            foreach ($lst_nota_aluno AS $key => $nota_aluno)
                            {
                                if ($this->qtd_modulos > $key)
                                {
                                    $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores( $nota_aluno["ref_ref_cod_tipo_avaliacao"], $nota_aluno["ref_sequencial"] );
                                    $det_avaliacao_valores = $obj_avaliacao_valores->detalhe();
                                    $soma_notas[$valor["cod_disciplina"]][$key] = $det_avaliacao_valores["valor"];
                                }
                            }
                        }
                    }
                }
                // calcula a nota media do aluno
                if ( is_array($soma_notas) )
                {
                    foreach ($soma_notas AS $disciplina => $notas)
                    {
                        foreach ($notas as $nota)
                        {
                            $nota_media_aluno[$disciplina] += $nota;
                        }
                        $nota_media_aluno[$disciplina] /= ($this->modulo - 1);
                    }

                    // verifica se o aluno esta a baixo da media,
                    // caso positivo e o curso possua exame, dexa aluno em exame
                    foreach ($nota_media_aluno AS $disciplina => $nota)
                    {
                        /*
                        $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                        $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$nota,$nota );
                        if ( is_array($lst_avaliacao_valores) )
                        {
                            $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                            $valor = $det_avaliacao_valores["valor"];

                            if ($valor < $this->media && $this->media_exame)
                                $aluno_exame_disciplina[] = $disciplina;
                        }
                        */
                        if ($nota < $this->media && $this->media_exame)
                        {
                            $aluno_exame_disciplina[] = $disciplina;
                        }
                    }
                }
            }
            // caso aluno esteja de EXAME e a avaliacao NAO eh conceitual
            if ( ($this->qtd_modulos < $this->modulo) && !$this->conceitual )
            {
    //          echo "<br> EXAME CONCEITUAL";
                foreach ($aluno_exame_disciplina AS $key => $disciplina)
                {
                    if ( !$com_nota[$disciplina] )
                    {
                        $obj_disciplina = new clsPmieducarDisciplina( $disciplina );
                        $det_disciplina = $obj_disciplina->detalhe();
                        $nm_disciplina = $det_disciplina["nm_disciplina"];

                        if ($lst_disciplina[$disciplina]['nota'])
                        {
                            $lst_disciplina[$disciplina]['nota'] = number_format($lst_disciplina[$disciplina]['nota'], 2, ",", ".");
                        }

                        $this->campoTextoInv( "nm_disciplina_{$det_disciplina["nm_disciplina"]}", "Disciplina", $nm_disciplina, 30, 255,false,false,true );
                        $this->campoOculto( "disciplina_modulo[{$key}][ref_cod_disciplina]", $disciplina, "" );
                        $this->campoOculto( "disciplina_modulo[{$key}][cod_nota_aluno]", $lst_disciplina[$disciplina]['cod_nota_aluno'] );
//                      $this->campoLista( "disciplina_modulo[{$key}][nota]", " Nota Exame", $opcoes_valores, $lst_disciplina[$disciplina]['nota'] );

                        /**
                         * deixa obrigatorio em caso de edicao somente as notas que ja tinham sido
                         * preenchidas
                         */
                        $prenche_edicao_obrigatorio = $lst_disciplina[$disciplina]['nota']  || strtolower($this->tipoacao) == 'novo' ? true : false;

                        /**
                         * existe nota? mostra a opção para remove-la
                         */
                        if($lst_disciplina[$disciplina]['nota'] && $this->mat_modulo <= $this->modulo + 1)
                        {
                            $opcoes_valores = $opcoes_valores_remover;
                        }

                        $this->campoMonetario( "disciplina_modulo[{$key}][nota]", " Nota Exame", $lst_disciplina[$disciplina]['nota'], 5, 5, $prenche_edicao_obrigatorio );

                        $opcoes_valores = $opcoes_valores_;
                    }
                }
            }// caso seja uma situacao normal
            else
            {
                if ( is_array($lst_disciplinas) )
                {
                    // falta na chamada EH globalizada
                    if ($this->falta_ch_globalizada)
                    {
                        foreach ($lst_disciplinas AS $key => $disciplinas)
                        {
                            if ( !$com_nota[$disciplinas["cod_disciplina"]] )
                            {
                                $this->campoTextoInv( "nm_disciplina_{$disciplinas["nm_disciplina"]}", "Disciplina", $disciplinas["nm_disciplina"], 30, 255,false,false,true );
                                $this->campoOculto( "disciplina_modulo[{$key}][ref_cod_disciplina]", $disciplinas["cod_disciplina"] );
                                $this->campoOculto( "disciplina_modulo[{$key}][cod_nota_aluno]", $lst_disciplina[$disciplinas["cod_disciplina"]]['cod_nota_aluno'] );

                                /**
                                 * deixa obrigatorio em caso de edicao somente as notas que ja tinham sido
                                 * preenchidas
                                 */
                                $prenche_edicao_obrigatorio = $lst_disciplina[$disciplinas["cod_disciplina"]]['nota']  || strtolower($this->tipoacao) == 'novo' ? true : false;

                                /**
                                 * existe nota? mostra a opção para remove-la
                                 */
                                if($lst_disciplina[$disciplinas["cod_disciplina"]]['nota'] && $this->mat_modulo <= $this->modulo + 1)
                                {
                                    $opcoes_valores = $opcoes_valores_remover;
                                }

                                $this->campoLista( "disciplina_modulo[{$key}][nota]", " Nota", $opcoes_valores, $lst_disciplina[$disciplinas["cod_disciplina"]]['nota'],"",false,"","",false,$prenche_edicao_obrigatorio );

                                $opcoes_valores = $opcoes_valores_;
                            }
                        }
                        $this->campoNumero( "total_faltas", " Faltas", $faltas, 2, 2, true );
                    }
                    else // falta na chamada NAO eh globalizada
                    {
                        foreach ($lst_disciplinas AS $key => $disciplinas)
                        {
                            if ( !$com_nota[$disciplinas["cod_disciplina"]] )
                            {
                                $this->campoTextoInv( "nm_disciplina_{$disciplinas["nm_disciplina"]}", "Disciplina", $disciplinas["nm_disciplina"], 30, 255,false,false,true );
                                $this->campoOculto( "disciplina_modulo[{$key}][ref_cod_disciplina]", $disciplinas["cod_disciplina"] );
                                $this->campoOculto( "disciplina_modulo[{$key}][cod_nota_aluno]", $lst_disciplina[$disciplinas["cod_disciplina"]]['cod_nota_aluno'] );

                                /**
                                 * deixa obrigatorio em caso de edicao somente as notas que ja tinham sido
                                 * preenchidas
                                 */
                                $prenche_edicao_obrigatorio = $lst_disciplina[$disciplinas["cod_disciplina"]]['nota']  || strtolower($this->tipoacao) == 'novo' ? true : false;

                                    /**
                                 * existe nota? mostra a opção para remove-la
                                 */
                                if($lst_disciplina[$disciplinas["cod_disciplina"]]['nota'] && $this->mat_modulo <= $this->modulo + 1)
                                {
                                    $opcoes_valores = $opcoes_valores_remover;
                                }

                                if ($disciplinas["apura_falta"])
                                {
                                    $this->campoLista( "disciplina_modulo[{$key}][nota]", " Nota", $opcoes_valores, $lst_disciplina[$disciplinas["cod_disciplina"]]['nota'], "",true, "","",false,$prenche_edicao_obrigatorio );
                                    $this->campoOculto( "disciplina_modulo[{$key}][cod_falta_aluno]", $lst_disciplina[$disciplinas["cod_disciplina"]]['cod_falta_aluno'] );
                                    $this->campoNumero( "disciplina_modulo[{$key}][faltas]", " Faltas", $lst_disciplina[$disciplinas["cod_disciplina"]]['faltas'], 2, 2, $prenche_edicao_obrigatorio);
                                }
                                else
                                {
                                    $this->campoLista( "disciplina_modulo[{$key}][nota]", " Nota", $opcoes_valores, $lst_disciplina[$disciplinas["cod_disciplina"]]['nota'],"",false,"","",false,$prenche_edicao_obrigatorio );
                                }

                                $opcoes_valores = $opcoes_valores_;
                            }
                        }
                    }
                }// caso seja o ultimo modulo e a avaliacao seja conceitual
                if ( ($this->qtd_modulos == $this->modulo) && $this->conceitual )
                {
                    $opcoes = array( "" => "Selecione", 1 => "Aprovado", 2 => "Reprovado" );
                    $this->campoLista( "aprovado", "Situa&ccedil;&atilde;o", $opcoes, $this->situacao );
                }
            }
        }
        $this->campoOculto( "qtd_modulos", $this->qtd_modulos );
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 642, $this->pessoa_logada, 7,  "educar_falta_nota_aluno_lst.php" );
    //************************************* CADASTRA - MATRICULADO NUMA SERIE *************************************//
        if ($this->ref_ref_cod_serie)
        {

            $aluno_esta_em_exame = $_POST["aluno_esta_em_exame"];
            $qtd_disciplinas_aluno_exame = $_POST["qtd_disciplinas_aluno_exame"];

            if( !$this->reprova_falta)
            {
                $this->cadastraSNotasFaltas();
            }

            $aprovado = 3;

            /**
             * Antes era obrigatorio o preenchimento de todas as notas
             * agora nao é mais.. logo é preciso verificar a quantidade
             * de disciplinas que estao sem notas
             * somente prosseguir caso nao tenha mais nenhuma disciplina
             * sem nota
             */
            /**
             * verifica se existem disciplinas sem notas
             * somente aprova caso seja zero
             */

            $obj_nota_aluno = new clsPmieducarNotaAluno();
            if ($_POST["reprova_falta"] == "n")
            {
                $total = $obj_nota_aluno->getQtdRestanteNotasAlunoNaoApuraFaltas($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_cod_turma,$this->modulo-1,$this->ref_ref_cod_escola);
            }
            else
            {
                $total = $obj_nota_aluno->getQtdRestanteNotasAlunoNaoApuraFaltas($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_cod_turma,$this->modulo,$this->ref_ref_cod_escola);
            }
            /**
             * existem disciplinas sem notas
             * somente cadastra e o modulo do aluno
             * continua igual sem calcular nada
             */

                if ($aluno_esta_em_exame==1) {
                        $sql = "SELECT COUNT(0)
                                FROM pmieducar.nota_aluno na
                                , pmieducar.disciplina d
                                , pmieducar.v_matricula_matricula_turma mmt
                                WHERE na.ref_cod_matricula = '{$this->ref_cod_matricula}'
                                AND na.ref_cod_matricula = mmt.cod_matricula
                                AND mmt.ref_cod_turma = '{$this->ref_cod_turma}'
                                AND na.ativo = 1
                                AND mmt.ativo = 1
                                AND na.ref_cod_disciplina = d.cod_disciplina
                                AND na.ref_cod_serie = '{$this->ref_ref_cod_serie}'
                                AND na.modulo = '{$this->modulo}'";
                        $db = new clsBanco();
                        $notas_exame_ja_recebidas = $db->CampoUnico($sql);
                        if ($qtd_disciplinas_aluno_exame == $notas_exame_ja_recebidas) {
                            $total = 0;
                        }
                }
            if($total)
            {
                $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
                $this->simpleRedirect("educar_falta_nota_aluno_det.php?ref_cod_matricula={$this->ref_cod_matricula}&ref_cod_turma={$this->ref_cod_turma}&sequencial={$this->ref_sequencial_matricula_turma}");
            }
            $obj_serie = new clsPmieducarSerie( $this->ref_ref_cod_serie );
            $det_serie = $obj_serie->detalhe();
            $media_especial = $det_serie['media_especial'];

            if ( $this->qtd_modulos <= $this->modulo )
            {
                $obj_curso = new clsPmieducarCurso( $this->ref_cod_curso );
                $det_curso = $obj_curso->detalhe();
                $frequencia_minima = $det_curso["frequencia_minima"];
                $hora_falta = $det_curso["hora_falta"];
                $carga_horaria_curso = $det_curso["carga_horaria"];
                $ano_padrao_escolar = $det_curso["padrao_ano_escolar"];

                $obj_esd = new clsPmieducarEscolaSerieDisciplina();
                $lst_esd = $obj_esd->lista( $this->ref_ref_cod_serie,$this->ref_ref_cod_escola,null,1 );
                if ( is_array($lst_esd) )
                {
                    foreach ($lst_esd AS $campo)
                    {
                        $obj_nota_aluno = new clsPmieducarNotaAluno();
                        $obj_nota_aluno->setOrderby("modulo ASC");
                        $lst_nota_aluno = $obj_nota_aluno->lista( null,null,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,$campo["ref_cod_disciplina"],$this->ref_cod_matricula,null,null,null,null,null,null,1 );
                        /**
                         * para media especial nao precisa verificar as medias
                         * de cada disciplina
                         *
                         */
                        if ( is_array($lst_nota_aluno)  && !dbBool($media_especial))
                        {
                            foreach ($lst_nota_aluno AS $key => $nota_aluno)
                            {
                                if ($nota_aluno['nota'])
                                {
                                    $soma_notas[$campo["ref_cod_disciplina"]][$key] = $nota_aluno['nota']*2;
                                }
                                else
                                {
                                    $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores( $nota_aluno["ref_ref_cod_tipo_avaliacao"], $nota_aluno["ref_sequencial"] );
                                    $det_avaliacao_valores = $obj_avaliacao_valores->detalhe();
                                    $soma_notas[$campo["ref_cod_disciplina"]][$key] = $det_avaliacao_valores["valor"];
                                }
                            }
                        }

                        if (!$this->falta_ch_globalizada)
                        {
                            $obj_falta_aluno = new clsPmieducarFaltaAluno();
                            $lst_falta_aluno = $obj_falta_aluno->lista( null,null,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,$campo["ref_cod_disciplina"],$this->ref_cod_matricula,null,null,null,null,null,1 );
                            if ( is_array($lst_falta_aluno) )
                            {
                                foreach ($lst_falta_aluno AS $key => $falta_aluno)
                                {
                                    $soma_faltas[$campo["ref_cod_disciplina"]][$key] = $falta_aluno["faltas"];
                                }
                            }
                        }
                    }
                    if ( is_array($soma_faltas) )
                    {
                        foreach ($soma_faltas AS $disciplina => $faltas)
                        {
                            foreach ($faltas as $falta)
                            {
                                $faltas_media_aluno[$disciplina] += $falta;
                            }
                        }
                    }
                }
                if ( is_array($faltas_media_aluno) )
                {
                    foreach ($faltas_media_aluno AS $disciplina => $faltas)
                    {
                        $obj_disciplina = new clsPmieducarDisciplina( $disciplina );
                        $det_disciplina = $obj_disciplina->detalhe();
                        $carga_horaria_disciplina = $det_disciplina["carga_horaria"];

                        // calcula o maximo de horas q o aluno pode faltar na disciplina
                        $max_falta = ($carga_horaria_disciplina * $frequencia_minima)/100;
                        $max_falta = $carga_horaria_disciplina - $max_falta;
                        // calcula a quantidade de faltas por hora do aluno na disciplina
                        $faltas *= $hora_falta;

                        if ( ($faltas > $max_falta) && !$this->reprova_falta )
                        {
                            echo "<script>
                                    if( confirm('O aluno excedeu o valor máximo de faltas permitidas, \\n deseja reprová-lo? \\n Quantidade de faltas do aluno: $faltas \\n Valor máximo de faltas permitido: $max_falta \\n \\n Clique em OK para reprová-lo ou em CANCELAR para ignorar.') )
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&falta=s';
                                    }
                                    else
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&falta=n';
                                    }
                                </script>";
                            return true;
                        }
                        if( $this->reprova_falta == 's' )
                        {
                            $aprovado = 2; // aluno reprovado por falta
                        }
                    }
                }
                else
                {
                    $obj_serie = new clsPmieducarSerie( $this->ref_ref_cod_serie );
                    $det_serie = $obj_serie->detalhe();
                    $carga_horaria_serie = $det_serie["carga_horaria"];

                    // calcula o maximo de horas q o aluno pode faltar na serie
                    $max_falta = ($carga_horaria_serie * $frequencia_minima)/100;
                    $max_falta = $carga_horaria_serie - $max_falta;

                    // calcula a quantidade de faltas por hora do aluno na serie
                    $obj_faltas = new clsPmieducarFaltas();
                    $lst_faltas = $obj_faltas->lista( $this->ref_cod_matricula );
                    if ( is_array($lst_faltas) )
                    {
                        $total_faltas = 0;
                        foreach ( $lst_faltas AS $key => $faltas )
                        {
                            $total_faltas += $faltas['falta'];
                        }
                        $total_faltas *= $hora_falta;
                        if ( ($total_faltas > $max_falta) && !$this->reprova_falta )
                        {
                            echo "<script>
                                    if( confirm('O aluno excedeu o valor máximo de faltas permitidas, \\n deseja reprová-lo? \\n Quantidade de faltas do aluno: $total_faltas \\n Valor máximo de faltas permitido: $max_falta \\n \\n Clique em OK para reprová-lo ou em CANCELAR para ignorar.') )
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&falta=s';
                                    }
                                    else
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&falta=n';
                                    }
                                </script>";
                            return true;
                        }
                        if( $this->reprova_falta == 's' )
                        {
                            $aprovado = 2; // aluno reprovado por falta
                        }
                    }
                }
            }
            if ( $this->qtd_modulos == $this->modulo )
            {
                //verificacao para media normal

                if ( is_array($soma_notas) && !dbBool($media_especial))
                {
                    foreach ($soma_notas AS $disciplina => $notas)
                    {
                        foreach ($notas as $nota)
                        {
                            if (dbBool($det_serie["ultima_nota_define"]))
                            {
                                $nota_media_aluno[$disciplina] = $nota;
                            }
                            else
                            {
                                $nota_media_aluno[$disciplina] += $nota;
                            }
                        }
                        if (!dbBool($det_serie["ultima_nota_define"]))
                        {
                            $nota_media_aluno[$disciplina] /= $this->modulo;
                        }
                    }
                    foreach ($nota_media_aluno AS $disciplina => $nota)
                    {

                        $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                        $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$nota,$nota );
                        if ( is_array($lst_avaliacao_valores) )
                        {
                            $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                            $valor = $det_avaliacao_valores["valor"];
                        }
                        /**
                         * verifica se existem disciplinas sem notas
                         * somente aprova caso seja zero
                         */
                        if ( ($nota < $this->media) && $this->media_exame && !$this->conceitual /*&& !$total*/  )
                        {
                            $em_exame = true;  // aluno em exame
                        }
                        else if ( ($valor < $this->media) && !$this->media_exame && !$this->conceitual /*&& !$total*/ )
                        {
                            $aprovado = 2; // aluno reprovado direto (n existe exame)
                        }
                    }

                }
                /**
                 * calculo de media especial
                 */
                if( dbBool($media_especial) )
                {
                    $objNotaAluno = new clsPmieducarNotaAluno();
                    $media = $objNotaAluno->getMediaEspecialAluno($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,$this->qtd_modulos,$this->media);

                    if( $media < $this->media )
                    {
                        //  reprovado direto sem exame
                        $aprovado = 2;
                    }

                }
            }
            else if ($this->qtd_modulos < $this->modulo)
            {
                foreach ($soma_notas AS $disciplina => $notas)
                {
                    $qtd_notas = 0;
                    foreach ($notas as $nota)
                    {
                        $nota_media_aluno[$disciplina] += $nota;
                        $qtd_notas++;
                    }

                    if ($qtd_notas == $this->modulo)
                    {
                        $nota_media_aluno[$disciplina] /= ($this->modulo+1);
                    }
                    else
                    {
                        $nota_media_aluno[$disciplina] /= ($this->modulo - 1);
                    }
                }
                foreach ($nota_media_aluno AS $disciplina => $nota)
                {
                    $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                    $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$nota,$nota );
                    if ( is_array($lst_avaliacao_valores) )
                    {
                        $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                        $valor = $det_avaliacao_valores["valor"];

                        if ($valor < $this->media_exame)
                        {
                            $aprovado = 2; // aluno reprovado no exame
                        }
                    }
                }
            }

            /**
             * verifica se existem disciplinas sem notas
             * somente aprova caso seja zero
             */
            //$obj_nota_aluno = new clsPmieducarNotaAluno();
            //$total = $obj_nota_aluno->getQtdRestanteNotasAlunoNaoApuraFaltas($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_cod_turma,$this->modulo,$this->ref_ref_cod_escola);
            if ($this->conceitual)
            {
                $aprovado = $this->aprovado; // situacao definida pelo professor
            }
            else if( !$em_exame && ($this->qtd_modulos <= $this->modulo) && ($aprovado == 3) && !$this->conceitual /*&& !$total*/ )
            {
                $aprovado = 1; // aluno aprovado
            }

            $obj = new clsPmieducarMatricula( $this->ref_cod_matricula,null,null,null,$this->pessoa_logada,null,null,$aprovado,null,null,null,null,null,$this->modulo+1 );
            $editou = $obj->edita();

            if( $editou)
            {
                /**
                 * gerar historico para alunos que foram reprovados
                 * 01/03/2007
                 */
                if ($aprovado == 1 || $aprovado == 2)
                {
                    $obj_serie = new clsPmieducarSerie( $this->ref_ref_cod_serie );
                    $det_serie = $obj_serie->detalhe();
                    $carga_horaria_serie = $det_serie["carga_horaria"];

                    $obj_escola = new clsPmieducarEscola( $this->ref_ref_cod_escola );
                    $det_escola = $obj_escola->detalhe();
                    $ref_idpes = $det_escola["ref_idpes"];
                    // busca informacoes da escola
                    $obj_escola = new clsPessoaJuridica($ref_idpes);
                    $det_escola = $obj_escola->detalhe();
                    $nm_escola = $det_escola["fantasia"];
                    if($det_escola)
                    {
                        $cidade = $det_escola["cidade"];
                        $uf = $det_escola["sigla_uf"];
                    }
                    if ($this->padrao_ano_escolar)
                    {
                        $extra_curricular = 0;
                    }
                    else
                    {
                        $extra_curricular = 1;
                    }

                    $sql = "SELECT SUM(falta) FROM pmieducar.faltas WHERE ref_cod_matricula = {$this->ref_cod_matricula}";
                    $db5 = new clsBanco();
                    $total_faltas = $db5->CampoUnico($sql);

                    $obj = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno,null,null,$this->pessoa_logada,$det_serie['nm_serie'],$this->ano_letivo,$carga_horaria_serie,null,$nm_escola,$cidade,$uf,null,$aprovado,null,null,1,$total_faltas,$this->ref_cod_instituicao,0,$extra_curricular,$this->ref_cod_matricula );
                    $cadastrou2 = $obj->cadastra();
                    if( $cadastrou2 && !$this->conceitual)
                    {
                        $obj_historico = new clsPmieducarHistoricoEscolar();
                        $sequencial = $obj_historico->getMaxSequencial( $this->ref_cod_aluno );

                        $historico_disciplina = array();
                        foreach ($nota_media_aluno as $key => $nota)
                        {
                            $historico_disciplina[$key] = array( $nota, $faltas_media_aluno[$key] );
                        }

                        foreach ($historico_disciplina AS $disciplina => $campo)
                        {
                            $obj_disciplina = new clsPmieducarDisciplina( $disciplina );
                            $det_disciplina = $obj_disciplina->detalhe();
                            $nm_disciplina = $det_disciplina["nm_disciplina"];

                            $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                            $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$campo[0],$campo[0] );

                            if ( is_array($lst_avaliacao_valores) )
                            {
                                $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                                $nm_nota = $det_avaliacao_valores["nome"];

                                $obj = new clsPmieducarHistoricoDisciplinas( null, $this->ref_cod_aluno, $sequencial, $nm_disciplina, $nm_nota, $campo[1] );
                                $cadastrou3 = $obj->cadastra();
                                if( !$cadastrou3 )
                                {
                                    $this->mensagem = "Cadastro do Hist&oacute;rico Disciplinas n&atilde;o realizado.<br>";
                                    return false;
                                }
                            }
                            else
                            {
                                $this->mensagem = "N&atilde;o foi poss&iacute;vel encontrar os Valores do Tipo de Avalia&ccedil;&atilde;o.<br>";
                                return false;
                            }
                        }
                    }
                    else if( !$cadastrou2 )
                    {
                        $this->mensagem = "Cadastro do Hist&oacute;rico Escolar n&atilde;o realizado.<br>";
                        return false;
                    }
                }

                $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
                $this->simpleRedirect("educar_falta_nota_aluno_det.php?ref_cod_matricula={$this->ref_cod_matricula}&ref_cod_turma={$this->ref_cod_turma}&sequencial={$this->ref_sequencial_matricula_turma}");
            }

            $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada (Matr&iacute;cula).<br>";

            return false;
        }
    //************************************* CADASTRA - MATRICULADO NUM CURSO *************************************//
        else
        {
            if( !$this->reprova_falta )
            {
                $this->cadastraCNotasFaltas();
            }

            /**
             * verifica se existem disciplinas sem notas
             *
             **/
            $obj_nota_aluno = new clsPmieducarNotaAluno();
            $total = $obj_nota_aluno->getQtdRestanteNotasAlunoNaoApuraFaltas($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_cod_turma,$this->modulo,$this->ref_ref_cod_escola);

            /**
             * existem disciplinas sem notas
             * somente cadastra e o modulo do aluno
             * continua igual sem calcular nada
             */
            if($total)
            {
                $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
                $this->simpleRedirect("educar_falta_nota_aluno_det.php?ref_cod_matricula={$this->ref_cod_matricula}&ref_cod_turma={$this->ref_cod_turma}&sequencial={$this->ref_sequencial_matricula_turma}");
            }

            $aprovado = 3;

            if ( $this->qtd_modulos <= $this->modulo )
            {
                $obj_curso = new clsPmieducarCurso( $this->ref_cod_curso );
                $det_curso = $obj_curso->detalhe();
                $frequencia_minima = $det_curso["frequencia_minima"];
                $hora_falta = $det_curso["hora_falta"];
                $carga_horaria_curso = $det_curso["carga_horaria"];

                $obj_disciplina = new clsPmieducarDisciplina();
                $lst_disciplina = $obj_disciplina->lista( null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_curso );
                if ( is_array($lst_disciplina) )
                {
                    foreach ($lst_disciplina AS $campo)
                    {
                        $obj_nota_aluno = new clsPmieducarNotaAluno();
                        $lst_nota_aluno = $obj_nota_aluno->lista( null,null,null,null,null,null,$this->ref_cod_matricula,null,null,null,null,null,null,1,null,$campo["cod_disciplina"] );
                        if ( is_array($lst_nota_aluno) )
                        {
                            foreach ($lst_nota_aluno AS $key => $nota_aluno)
                            {
                                if ($nota_aluno["nota"])
                                {
                                    $soma_notas[$campo["cod_disciplina"]][$key] = $nota_aluno["nota"]*2;
                                }
                                else
                                {
                                    $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores( $nota_aluno["ref_ref_cod_tipo_avaliacao"], $nota_aluno["ref_sequencial"] );
                                    $det_avaliacao_valores = $obj_avaliacao_valores->detalhe();
                                    $soma_notas[$campo["cod_disciplina"]][$key] = $det_avaliacao_valores["valor"];
                                }
                            }
                        }

                        if (!$this->falta_ch_globalizada)
                        {
                            $obj_falta_aluno = new clsPmieducarFaltaAluno();
                            $lst_falta_aluno = $obj_falta_aluno->lista( null,null,null,null,null,null,$this->ref_cod_matricula,null,null,null,null,null,1,null,$campo["cod_disciplina"] );
                            if ( is_array($lst_falta_aluno) )
                            {
                                foreach ($lst_falta_aluno AS $key => $falta_aluno)
                                {
                                    $soma_faltas[$campo["cod_disciplina"]][$key] = $falta_aluno["faltas"];
                                }
                            }
                        }
                    }
                    if ( is_array($soma_faltas) )
                    {
                        foreach ($soma_faltas AS $disciplina => $faltas)
                        {
                            foreach ($faltas as $falta)
                            {
                                $faltas_media_aluno[$disciplina] += $falta;
                            }
                        }
                    }
                }
                if ( is_array($faltas_media_aluno) )
                {
                    foreach ($faltas_media_aluno AS $disciplina => $faltas)
                    {
                        $obj_disciplina = new clsPmieducarDisciplina( $disciplina );
                        $det_disciplina = $obj_disciplina->detalhe();
                        $carga_horaria_disciplina = $det_disciplina["carga_horaria"];

                        // calcula o maximo de horas q o aluno pode faltar na disciplina
                        $max_falta = ($carga_horaria_disciplina * $frequencia_minima)/100;
                        $max_falta = $carga_horaria_disciplina - $max_falta;
                        // calcula a quantidade de faltas por hora do aluno na disciplina
                        $faltas *= $hora_falta;

                        if ( ($faltas > $max_falta) && !$this->reprova_falta )
                        {
                            echo "<script>
                                    if( confirm('O aluno excedeu o valor máximo de faltas permitidas, \\n deseja reprová-lo? \\n Quantidade de faltas do aluno: $faltas \\n Valor máximo de faltas permitido: $max_falta \\n \\n Clique em OK para reprová-lo ou em CANCELAR para ignorar.') )
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&falta=s';
                                    }
                                    else
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&falta=n';
                                    }
                                </script>";
                            return true;
                        }
                        if( $this->reprova_falta == 's' )
                        {
                            $aprovado = 2; // aluno reprovado por falta
                        }
                    }
                }
                else
                {
                    // calcula o maximo de horas q o aluno pode faltar no curso
                    $max_falta = ($carga_horaria_curso * $frequencia_minima)/100;
                    $max_falta = $carga_horaria_curso - $max_falta;

                    // calcula a qtd de faltas por hora do aluno no curso
                    $obj_faltas = new clsPmieducarFaltas();
                    $lst_faltas = $obj_faltas->lista( $this->ref_cod_matricula );
                    if ( is_array($lst_faltas) )
                    {
                        $total_faltas = 0;
                        foreach ( $lst_faltas AS $key => $faltas )
                        {
                            $total_faltas += $faltas['falta'];
                        }
                        $total_faltas *= $hora_falta;

                        if ( ($total_faltas > $max_falta) && !$this->reprova_falta )
                        {
                            echo "<script>
                                    if( confirm('O aluno excedeu o valor máximo de faltas permitidas, \\n deseja reprová-lo? \\n Quantidade de faltas do aluno: $total_faltas \\n Valor máximo de faltas permitido: $max_falta \\n \\n Clique em OK para reprová-lo ou em CANCELAR para ignorar.') )
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&falta=s';
                                    }
                                    else
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&falta=n';
                                    }
                                </script>";
                            return true;
                        }
                        if( $this->reprova_falta == 's' )
                        {
                            $aprovado = 2; // aluno reprovado por falta
                        }
                    }
                }
            }
            if ( $this->qtd_modulos == $this->modulo )
            {
                if ( is_array($soma_notas) )
                {
                    foreach ($soma_notas AS $disciplina => $notas)
                    {
                        foreach ($notas as $nota)
                        {
                            $nota_media_aluno[$disciplina] += $nota;
                        }
                        $nota_media_aluno[$disciplina] /= $this->modulo;
                    }
                    foreach ($nota_media_aluno AS $disciplina => $nota)
                    {

                        $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                        $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$nota,$nota );
                        if ( is_array($lst_avaliacao_valores) )
                        {
                            $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                            $valor = $det_avaliacao_valores["valor"];
                            /*
                            if ( ($valor < $this->media) && $this->media_exame && !$this->conceitual )
                            {
                                $em_exame = true;
                            }
                            else if ( ($valor < $this->media) && !$this->media_exame && !$this->conceitual )
                            {
                                $aprovado = 2; // aluno reprovado direto (n existe exame)
                            }
                            */
                        }
                        /**
                         * verifica se existem disciplinas sem notas
                         * somente aprova caso seja zero
                         */
                        //$obj_nota_aluno = new clsPmieducarNotaAluno();
                        //$total = $obj_nota_aluno->getQtdRestanteNotasAlunoNaoApuraFaltas($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_cod_turma,$this->modulo,$this->ref_ref_cod_escola);

                        if ( ($nota < $this->media) && $this->media_exame && !$this->conceitual /*&& !$total*/ )
                        {
                            $em_exame = true; // aluno em exame
                        }
                        else if ( ($valor < $this->media) && !$this->media_exame && !$this->conceitual /*&& !$total*/ )
                        {
                            $aprovado = 2; // aluno reprovado direto (n existe exame)
                        }
                    }
                }
            }
            elseif ($this->qtd_modulos < $this->modulo)
            {
                foreach ($soma_notas AS $disciplina => $notas)
                {
                    $qtd_notas = 0;
                    foreach ($notas as $nota)
                    {
                        $nota_media_aluno[$disciplina] += $nota;
                        $qtd_notas++;
                    }
                    if ($qtd_notas == $this->modulo)
                    {
                        $nota_media_aluno[$disciplina] /= $this->modulo;
//                      $nota_media_aluno[$disciplina] /= $this->modulo;
                    }
                    else
                    {
                        $nota_media_aluno[$disciplina] /= ($this->modulo - 1);
                    }
                }

                foreach ($nota_media_aluno AS $disciplina => $nota)
                {
                    $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                    $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$nota,$nota );
                    if ( is_array($lst_avaliacao_valores) )
                    {
                        $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                        $valor = $det_avaliacao_valores["valor"];

                        if ($valor < $this->media_exame)
                        {
                            $aprovado = 2; // aluno reprovado no exame
                        }
                    }
                }
            }
            /**
             * verifica se existem disciplinas sem notas
             * somente aprova caso seja zero
             */
            //$obj_nota_aluno = new clsPmieducarNotaAluno();
            //$total = $obj_nota_aluno->getQtdRestanteNotasAlunoNaoApuraFaltas($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_cod_turma,$this->modulo,$this->ref_ref_cod_escola);


            if ($this->conceitual)
            {
                $aprovado = $this->aprovado; // situacao definida pelo professor
            }
            else if( !$em_exame && ($this->qtd_modulos <= $this->modulo) && ($aprovado == 3) && !$this->conceitual /*&& !$total*/ )
            {
                $aprovado = 1; // aluno aprovado
            }

            $obj = new clsPmieducarMatricula( $this->ref_cod_matricula,null,null,null,$this->pessoa_logada,null,null,$aprovado,null,null,null,null,null,$this->modulo+1 );
            $editou = $obj->edita();
            if( $editou )
            {
                /**
                 * alunos reprovados tambem gera historico
                 * 01/03/2007
                 */
                if ($aprovado == 1 || $aprovado == 2)
                {
                    // busca informacoes da instituicao
                    $obj_instituicao = new clsPmieducarInstituicao( $this->ref_cod_instituicao );
                    $det_instituicao = $obj_instituicao->detalhe();
                    $nm_instituicao = $det_instituicao["nm_instituicao"];
                    $cidade = $det_instituicao["cidade"];
                    $uf = $det_instituicao["ref_sigla_uf"];

                    $sql = "SELECT SUM(falta) FROM pmieducar.faltas WHERE ref_cod_matricula = {$this->ref_cod_matricula}";
                    $db5 = new clsBanco();
                    $total_faltas = $db5->CampoUnico($sql);

                    $obj = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno,null,null,$this->pessoa_logada,$det_curso['nm_curso'],$this->ano_letivo,$carga_horaria_curso,null,$nm_instituicao,$cidade,$uf,null,$aprovado,null,null,1,$total_faltas,$this->ref_cod_instituicao,0,1,$this->ref_cod_matricula );
                    $cadastrou2 = $obj->cadastra();
                    if( $cadastrou2 && !$this->conceitual)
                    {
                        $obj_historico = new clsPmieducarHistoricoEscolar();
                        $sequencial = $obj_historico->getMaxSequencial( $this->ref_cod_aluno );

                        $historico_disciplina = array();
                        foreach ($nota_media_aluno as $key => $nota)
                        {
                            $historico_disciplina[$key] = array( $nota, $faltas_media_aluno[$key] );
                        }

                        foreach ($historico_disciplina AS $disciplina => $campo)
                        {
                            $obj_disciplina = new clsPmieducarDisciplina( $disciplina );
                            $det_disciplina = $obj_disciplina->detalhe();
                            $nm_disciplina = $det_disciplina["nm_disciplina"];

                            $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                            $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$campo[0],$campo[0] );

                            if ( is_array($lst_avaliacao_valores) )
                            {
                                $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                                $nm_nota = $det_avaliacao_valores["nome"];

                                $obj = new clsPmieducarHistoricoDisciplinas( null, $this->ref_cod_aluno, $sequencial, $nm_disciplina, $nm_nota, $campo[1] );
                                $cadastrou3 = $obj->cadastra();
                                if( !$cadastrou3 )
                                {
                                    $this->mensagem = "Cadastro do Hist&oacute;rico Disciplinas n&atilde;o realizado.<br>";
                                    return false;
                                }
                            }
                            else
                            {
                                $this->mensagem = "N&atilde;o foi poss&iacute;vel encontrar os Valores do Tipo de Avalia&ccedil;&atilde;o.<br>";
                                return false;
                            }
                        }
                    }
                    else if( !$cadastrou2 )
                    {
                        $this->mensagem = "Cadastro do Hist&oacute;rico Escolar n&atilde;o realizado.<br>";
                        return false;
                    }
                }

                $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
                $this->simpleRedirect("educar_falta_nota_aluno_det.php?ref_cod_matricula={$this->ref_cod_matricula}&ref_cod_turma={$this->ref_cod_turma}&sequencial={$this->ref_sequencial_matricula_turma}");
            }
            $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada (Matr&iacute;cula).<br>";

            return false;
        }
    }

    function Editar()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 642, $this->pessoa_logada, 7,  "educar_falta_nota_aluno_lst.php" );

        //************************************* EDITA - MATRICULADO NUMA SERIE *************************************//
        if ($this->ref_ref_cod_serie)
        {
//          if (is_numeric($this->modulo))
//          {
//              $this->mat_modulo = $this->modulo;
//          }
            if( !$this->reprova_falta )
            {
                $this->editaSNotasFaltas();
            }

            /**
             * verifica se existem disciplinas sem notas
             *
             */

            $obj_nota_aluno = new clsPmieducarNotaAluno();
            $total = $obj_nota_aluno->getQtdRestanteNotasAlunoNaoApuraFaltas($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_cod_turma,$this->modulo,$this->ref_ref_cod_escola);

            /* verifica se o aluno está em exame
             * e se todas as matérias do exame estão com notas
             */

            $aluno_esta_em_exame = $_POST["aluno_esta_em_exame"];
            $qtd_disciplinas_aluno_exame = $_POST["qtd_disciplinas_aluno_exame"];
            if ($aluno_esta_em_exame==1) {
                        $sql = "SELECT COUNT(0)
                                FROM pmieducar.nota_aluno na
                                , pmieducar.disciplina d
                                , pmieducar.v_matricula_matricula_turma mmt
                                WHERE na.ref_cod_matricula = '{$this->ref_cod_matricula}'
                                AND na.ref_cod_matricula = mmt.cod_matricula
                                AND mmt.ref_cod_turma = '{$this->ref_cod_turma}'
                                AND na.ativo = 1
                                AND mmt.ativo = 1
                                AND na.ref_cod_disciplina = d.cod_disciplina
                                AND na.ref_cod_serie = '{$this->ref_ref_cod_serie}'
                                AND na.modulo = '{$this->modulo}'";
                        $db = new clsBanco();
                        $notas_exame_ja_recebidas = $db->CampoUnico($sql);
                        if ($qtd_disciplinas_aluno_exame == $notas_exame_ja_recebidas) {
                            $total = 0;
                        }
            }

            /**
             * existem disciplinas sem notas
             * somente cadastra e o modulo do aluno
             * continua igual sem calcular nada
             */
            if($total > 0)
            {
                /**
                 * caso NENHUMA materia tenha nota
                 * (por motivo de exclusao) verificar se o modulo da matricula
                 * é maior que o ultimo modulo com nota
                 * entao decrementar o modulo da matricula
                 */

                $ultimo_modulo_matricula = $obj_nota_aluno->getMaxNotas($this->ref_cod_matricula);

                if($ultimo_modulo_matricula < $this->mat_modulo )
                {
                    if ($this->nota_foi_removida && $this->pessoa_logada==184580)
                    {
                        $obj_hst_escolar = new clsPmieducarHistoricoEscolar();
                        $lst_hst_escolar = $obj_hst_escolar->lista( $this->ref_cod_aluno,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1,null,null,null,null,$this->ref_cod_matricula );
                        if (is_array($lst_hst_escolar))
                        {
                            $det_hst_escolar = array_shift($lst_hst_escolar);

                            $obj_hd = new clsPmieducarHistoricoDisciplinas();
                            $excluiu_hd = $obj_hd->excluirTodos( $this->ref_cod_aluno, $det_hst_escolar["sequencial"] );
                            if (!$excluiu_hd)
                            {
                                $this->mensagem = "Exclus&atilde;o do Hist&oacute;rico Disciplina n&atilde;o realizado.<br>";
                                return false;
                            }

                            $obj_hst_escolar = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno,$det_hst_escolar["sequencial"],$this->pessoa_logada,null,null,null,null,null,null,null,null,null,null,null,null,0 );
                            $excluiu_he = $obj_hst_escolar->excluir();
                            if (!$excluiu_he)
                            {
                                $this->mensagem = "Exclus&atilde;o do Hist&oacute;rico Escolar n&atilde;o realizado.<br>";
                                return false;
                            }
                        }

                    }
                    $obj = new clsPmieducarMatricula( $this->ref_cod_matricula,null,null,null,$this->pessoa_logada,null,null,3,null,null,null,null,null,$ultimo_modulo_matricula  );
                    $editou = $obj->edita();
                }

                $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
                $this->simpleRedirect("educar_falta_nota_aluno_det.php?ref_cod_matricula={$this->ref_cod_matricula}&ref_cod_turma={$this->ref_cod_turma}&sequencial={$this->ref_sequencial_matricula_turma}");
            }
            else
            {
                //$ultimo_modulo_matricula = $obj_nota_aluno->getMaxNotas($this->ref_cod_matricula);
                //die("$ultimo_modulo_matricula < $this->mat_modulo || $this->modulo");
                if(/*$this->mat_modulo <= $this->modulo*/$this->mat_modulo == $this->modulo)
                {
                    $obj = new clsPmieducarMatricula( $this->ref_cod_matricula,null,null,null,$this->pessoa_logada,null,null,3,null,null,null,null,null,$ultimo_modulo_matricula  );
                    $editou = $obj->avancaModulo();
                }
            }

            $aprovado = 3;

            if ( $this->qtd_modulos <= $this->mat_modulo)
            {
                $obj_curso = new clsPmieducarCurso( $this->ref_cod_curso );
                $det_curso = $obj_curso->detalhe();
                $frequencia_minima = $det_curso["frequencia_minima"];
                $hora_falta = $det_curso["hora_falta"];
                $carga_horaria_curso = $det_curso["carga_horaria"];

                $obj_esd = new clsPmieducarEscolaSerieDisciplina();
                $lst_esd = $obj_esd->lista( $this->ref_ref_cod_serie,$this->ref_ref_cod_escola,null,1 );
                if ( is_array($lst_esd) )
                {
                    $obj_nota_aluno = new clsPmieducarNotaAluno();
                    $max_nota = $obj_nota_aluno->getMaxNotas( $this->ref_cod_matricula );

                    $obj_serie = new clsPmieducarSerie( $this->ref_ref_cod_serie );
                    $det_serie = $obj_serie->detalhe();
                    $media_especial = $det_serie['media_especial'];

                    foreach ( $lst_esd AS $campo )
                    {
                        $obj_nota_aluno = new clsPmieducarNotaAluno();
                        $obj_nota_aluno->setOrderby("modulo ASC");
                        $lst_nota_aluno = $obj_nota_aluno->lista( null,null,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,$campo["ref_cod_disciplina"],$this->ref_cod_matricula,null,null,null,null,null,null,1 );
                        // so busca as notas da disciplina se nao for media especial
                        if ( is_array($lst_nota_aluno) && !dbBool($media_especial))
                        {
                            foreach ($lst_nota_aluno AS $key => $nota_aluno)
                            {
                                if ($nota_aluno['nota'])
                                {
                                    $soma_notas[$campo["ref_cod_disciplina"]][$key] = $nota_aluno['nota']*2;
                                }
                                else
                                {
                                    $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores( $nota_aluno["ref_ref_cod_tipo_avaliacao"], $nota_aluno["ref_sequencial"] );
                                    $det_avaliacao_valores = $obj_avaliacao_valores->detalhe();
                                    $soma_notas[$campo["ref_cod_disciplina"]][$key] = $det_avaliacao_valores["valor"];
                                }
                            }
                        }

                        if (!$this->falta_ch_globalizada)
                        {
                            $obj_falta_aluno = new clsPmieducarFaltaAluno();
                            $lst_falta_aluno = $obj_falta_aluno->lista( null,null,null,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,$campo["ref_cod_disciplina"],$this->ref_cod_matricula,null,null,null,null,null,1 );
                            if ( is_array($lst_falta_aluno) )
                            {
                                foreach ($lst_falta_aluno AS $key => $falta_aluno)
                                {
                                    $soma_faltas[$campo["ref_cod_disciplina"]][$key] = $falta_aluno["faltas"];
                                }
                            }
                        }
                    }
                    if ( is_array($soma_faltas) )
                    {
                        foreach ($soma_faltas AS $disciplina => $faltas)
                        {
                            foreach ($faltas as $falta)
                            {
                                $faltas_media_aluno[$disciplina] += $falta;
                            }
                        }
                    }
                }
                if ( is_array($faltas_media_aluno) )
                {
                    foreach ($faltas_media_aluno AS $disciplina => $faltas)
                    {
                        $obj_disciplina = new clsPmieducarDisciplina( $disciplina );
                        $det_disciplina = $obj_disciplina->detalhe();
                        $carga_horaria_disciplina = $det_disciplina["carga_horaria"];

                        // calcula o maximo de horas q o aluno pode faltar na disciplina
                        $max_falta = ($carga_horaria_disciplina * $frequencia_minima)/100;
                        $max_falta = $carga_horaria_disciplina - $max_falta;

                        // calcula a quantidade de faltas por hora do aluno na disciplina
                        $faltas *= $hora_falta;

                        if ( ($faltas > $max_falta) && !$this->reprova_falta )
                        {
                            echo "<script>
                                    if( confirm('O aluno excedeu o valor máximo de faltas permitidas, \\n deseja reprová-lo? \\n Quantidade de faltas do aluno: $faltas \\n Valor máximo de faltas permitido: $max_falta \\n \\n Clique em OK para reprová-lo ou em CANCELAR para ignorar.') )
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&modulo=$this->modulo&falta=s';
                                    }
                                    else
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&modulo=$this->modulo&falta=n';
                                    }
                                </script>";
                            return true;
                        }
                        if( $this->reprova_falta == 's' )
                        {
                            $aprovado = 2; // aluno reprovado por falta
                        }
                    }
                }
                else
                {
                    $obj_serie = new clsPmieducarSerie( $this->ref_ref_cod_serie );
                    $det_serie = $obj_serie->detalhe();
                    $carga_horaria_serie = $det_serie["carga_horaria"];

                    // calcula o maximo de horas q o aluno pode faltar na serie
                    $max_falta = ($carga_horaria_serie * $frequencia_minima)/100;
                    $max_falta = $carga_horaria_serie - $max_falta;

                    // calcula a quantidade de faltas por hora do aluno na serie
                    $obj_faltas = new clsPmieducarFaltas();
                    $lst_faltas = $obj_faltas->lista( $this->ref_cod_matricula );
                    if ( is_array($lst_faltas) )
                    {
                        $total_faltas = 0;
                        foreach ( $lst_faltas AS $key => $faltas )
                        {
                            $total_faltas += $faltas['falta'];
                        }
                        $total_faltas *= $hora_falta;

                        if ( ($total_faltas > $max_falta) && !$this->reprova_falta )
                        {
                            echo "<script>
                                    if( confirm('O aluno excedeu o valor máximo de faltas permitidas, \\n deseja reprová-lo? \\n Quantidade de faltas do aluno: $total_faltas \\n Valor máximo de faltas permitido: $max_falta \\n \\n Clique em OK para reprová-lo ou em CANCELAR para ignorar.') )
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&modulo=$this->modulo&falta=s';
                                    }
                                    else
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&modulo=$this->modulo&falta=n';
                                    }
                                </script>";
                            return true;
                        }
                        if( $this->reprova_falta == 's' )
                        {
                            $aprovado = 2; // aluno reprovado por falta
                        }
                    }
                }

                /**
                 * calculo de media especial
                 */

                if( dbBool($media_especial) )
                {
                    $objNotaAluno = new clsPmieducarNotaAluno();
                    $media = $objNotaAluno->getMediaEspecialAluno($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_ref_cod_escola,$this->qtd_modulos,$this->media);

                    if( $media < $this->media )
                    {
                        //  reprovado direto sem exame
                        $aprovado = 2;
                    }

                }
            }


            $db2 = new clsBanco();
            //retorna quantas matérias o aluno cursa não contabilizando as matérias com dispensa
            $sql = "SELECT COUNT(0) FROM pmieducar.escola_serie_disciplina WHERE ref_ref_cod_serie = {$this->ref_ref_cod_serie}
                    AND ref_ref_cod_escola = {$this->ref_ref_cod_escola} AND escola_serie_disciplina.ativo = 1
                    AND ref_cod_disciplina NOT IN (
                        SELECT ref_cod_disciplina
                        FROM pmieducar.dispensa_disciplina WHERE
                        ref_cod_matricula = {$this->ref_cod_matricula} AND ref_cod_serie = {$this->ref_ref_cod_serie}
                        AND ref_cod_escola = {$this->ref_ref_cod_escola} AND ativo = 1
                    )";
            $qtd_materias = $db2->CampoUnico($sql);
            //retorna quantas notas notas o aluno possui não contabilizandoa das matérias dispensadas
            $sql = "SELECT COUNT(0) FROM pmieducar.nota_aluno
                    WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND ativo = 1
                    AND ref_cod_disciplina NOT IN (
                            SELECT ref_cod_disciplina FROM pmieducar.dispensa_disciplina WHERE
                            ref_cod_matricula = {$this->ref_cod_matricula} AND ref_cod_serie = {$this->ref_ref_cod_serie}
                            AND ref_cod_escola = {$this->ref_ref_cod_escola} AND ativo = 1
                    )";
            //variável em uma edição para verificar se o aluno possui todas as notas
            //para mudar o estado de aprovado dele
            $qtd_notas_possui = $db2->CampoUnico($sql);

            $possui_todas_as_notas = ($this->qtd_modulos * $qtd_materias >= $qtd_notas_possui || $aluno_esta_em_exame == 1) ? true : false;

            if ( ($this->qtd_modulos < $this->mat_modulo) && ($this->qtd_modulos == $max_nota) && !dbBool($media_especial))
            {
                if ( is_array($soma_notas) && !dbBool($media_especial))
                {
                    foreach ($soma_notas AS $disciplina => $notas)
                    {
                        foreach ($notas as $nota)
                        {
                            if (dbBool($det_serie["ultima_nota_define"]))
                            {
                                $nota_media_aluno[$disciplina] = $nota;
                            }
                            else
                            {
                                $nota_media_aluno[$disciplina] += $nota;
                            }
                        }
                        if (!dbBool($det_serie["ultima_nota_define"]))
                        {
                            $nota_media_aluno[$disciplina] /= ($this->mat_modulo - 1);
                        }
                    }

                    foreach ($nota_media_aluno AS $disciplina => $nota)
                    {

                        $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                        $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$nota,$nota );
                        if ( is_array($lst_avaliacao_valores) )
                        {
                            $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                            $valor = $det_avaliacao_valores["valor"];

                        }

                        /**
                         * verifica se existem disciplinas sem notas
                         * somente aprova caso seja zero
                         */
                        //$obj_nota_aluno = new clsPmieducarNotaAluno();
                    //  $total = $obj_nota_aluno->getQtdRestanteNotasAlunoNaoApuraFaltas($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_cod_turma,$this->modulo,$this->ref_ref_cod_escola);

                        if ( ($nota < $this->media) && $this->media_exame && !$this->conceitual && $possui_todas_as_notas/* && !$total*/ )
                        {
                            $em_exame = true; // aluno em exame
                        }
                        else if ( ($valor < $this->media) && !$this->media_exame && !$this->conceitual && $possui_todas_as_notas /*&& !$total */ )
                        {
                            $aprovado = 2; // aluno reprovado direto (n existe exame)
                        }
                    }
                }

            }
            else if ( ($this->qtd_modulos < $this->mat_modulo) && ($this->qtd_modulos < $max_nota) && !dbBool($media_especial))
            {
//              echo "<pre>"; print_r($soma_notas);
                foreach ($soma_notas AS $disciplina => $notas)
                {
                    $qtd_notas = 0;
                    foreach ($notas as $nota)
                    {
                        $nota_media_aluno[$disciplina] += $nota;
                        $qtd_notas++;
                    }
                    if ($qtd_notas == $this->modulo/*$this->mat_modulo*/)
                    {
                        $nota_media_aluno[$disciplina] /= ($this->modulo/*$this->mat_modulo*/ + 1);
                    }
                    else
                    {
                        $nota_media_aluno[$disciplina] /= ($this->modulo - 1);
                    }

                }

                foreach ($nota_media_aluno AS $disciplina => $nota)
                {
                    $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                    $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$nota,$nota );
                    if ( is_array($lst_avaliacao_valores) )
                    {
                        $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                        $valor = $det_avaliacao_valores["valor"];

                        if ($valor < $this->media_exame)
                        {
                            $aprovado = 2; // aluno reprovado no exame
                        }
                        /*else if ( ($valor < $this->media) && ($this->qtd_modulos >= $this->modulo) )
                            $aprovado = 7; // aluno em exame*/
                    }
                }
            }

            /**
             * verifica se existem disciplinas sem notas
             * somente aprova caso seja zero
             */
            //$obj_nota_aluno = new clsPmieducarNotaAluno();
            //$total = $obj_nota_aluno->getQtdRestanteNotasAlunoNaoApuraFaltas($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_cod_turma,$this->modulo,$this->ref_ref_cod_escola);

            if ($this->conceitual)
            {
                $aprovado = $this->aprovado; // situacao definida pelo professor
            }
            else if( !$em_exame && ($this->qtd_modulos <= $this->mat_modulo) && ($aprovado == 3) && !$this->conceitual && $possui_todas_as_notas/*&& !$total */ )
            {
                $aprovado = 1; // aluno aprovado
            }

            $obj = new clsPmieducarMatricula( $this->ref_cod_matricula,null,null,null,$this->pessoa_logada,null,null,$aprovado );
            $editou = $obj->edita();
            //die($aprovado);
            if( $editou )
            {
                /**
                 * aluno reprovado mantem historico
                 * 01/03/2006
                 */

                if ( ($aprovado == 2) || ($aprovado == 3) || $aprovado==1)
                {
                    $obj_hst_escolar = new clsPmieducarHistoricoEscolar();
                    $lst_hst_escolar = $obj_hst_escolar->lista( $this->ref_cod_aluno,null,null,null,$det_serie["nm_serie"],$this->ano_letivo,$carga_horaria_serie,null,null,null,null,null,null,null,null,null,null,null,null,$this->ref_cod_instituicao,0,null,$this->ref_cod_matricula );
                    if (is_array($lst_hst_escolar))
                    {
                        $det_hst_escolar = array_shift($lst_hst_escolar);

                        $obj_hd = new clsPmieducarHistoricoDisciplinas();
                        $excluiu_hd = $obj_hd->excluirTodos( $this->ref_cod_aluno, $det_hst_escolar["sequencial"] );
                        if (!$excluiu_hd)
                        {
                            $this->mensagem = "Exclus&atilde;o do Hist&oacute;rico Disciplina n&atilde;o realizado.<br>";
                            return false;
                        }

                        $obj_hst_escolar = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno,$det_hst_escolar["sequencial"],$this->pessoa_logada,null,null,null,null,null,null,null,null,null,null,null,null,0 );
                        $excluiu_he = $obj_hst_escolar->excluir();
                        if (!$excluiu_he)
                        {
                            $this->mensagem = "Exclus&atilde;o do Hist&oacute;rico Escolar n&atilde;o realizado.<br>";
                            return false;
                        }
                    }
                }
                /*else */if ($aprovado == 1 || $aprovado == 2)
                {
                    $obj_serie = new clsPmieducarSerie( $this->ref_ref_cod_serie );
                    $det_serie = $obj_serie->detalhe();
                    $carga_horaria_serie = $det_serie["carga_horaria"];

                    $obj_escola = new clsPmieducarEscola( $this->ref_ref_cod_escola );
                    $det_escola = $obj_escola->detalhe();
                    $ref_idpes = $det_escola["ref_idpes"];
                    // busca informacoes da escola
                    $obj_escola = new clsPessoaJuridica($ref_idpes);
                    $det_escola = $obj_escola->detalhe();
                    $nm_escola = $det_escola["fantasia"];
                    if($det_escola)
                    {
                        $cidade = $det_escola["cidade"];
                        $uf = $det_escola["sigla_uf"];
                    }

                    if ($this->padrao_ano_escolar)
                        $extra_curricular = 0;
                    else
                        $extra_curricular = 1;

                    $sql = "SELECT SUM(falta) FROM pmieducar.faltas WHERE ref_cod_matricula = {$this->ref_cod_matricula}";
                    $db5 = new clsBanco();
                    $total_faltas = $db5->CampoUnico($sql);

                    $obj_hst_escolar = new clsPmieducarHistoricoEscolar();
                    $lst_hst_escolar = $obj_hst_escolar->lista( $this->ref_cod_aluno,null,null,null,$det_serie["nm_serie"],$this->ano_letivo,$carga_horaria_serie,null,null,null,null,null,null,null,null,null,null,null,null,$this->ref_cod_instituicao,0,null,$this->ref_cod_matricula );
                    if (is_array($lst_hst_escolar))
                    {
                        $det_hst_escolar = array_shift($lst_hst_escolar);

                        $obj = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno,$det_hst_escolar["sequencial"],$this->pessoa_logada,null,null,null,null,null,null,$cidade,$uf,null,$aprovado,null,null,1,$total_faltas,null,null,$extra_curricular );
                        $editou_he = $obj->edita();
                    }
                    else
                    {
                        $obj = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno,null,null,$this->pessoa_logada,$det_serie["nm_serie"],$this->ano_letivo,$carga_horaria_serie,null,$nm_escola,$cidade,$uf,null,$aprovado,null,null,1,$total_faltas,$this->ref_cod_instituicao,0,$extra_curricular,$this->ref_cod_matricula );
                        $cadastrou_he = $obj->cadastra();
                    }
                    if( ($editou_he || $cadastrou_he) && !$this->conceitual)
                    {
                        if ($cadastrou_he)
                        {
                            $obj_historico = new clsPmieducarHistoricoEscolar();
                            $sequencial = $obj_historico->getMaxSequencial( $this->ref_cod_aluno );
                        }
                        else
                        {
                            $sequencial = $det_hst_escolar["sequencial"];
                        }

                        $historico_disciplina = array();
                        foreach ($nota_media_aluno as $key => $nota)
                        {
                            $historico_disciplina[$key] = array( $nota, $faltas_media_aluno[$key] );
                        }
                        foreach ($historico_disciplina AS $disciplina => $campo)
                        {
                            $obj_disciplina = new clsPmieducarDisciplina( $disciplina );
                            $det_disciplina = $obj_disciplina->detalhe();
                            $nm_disciplina = $det_disciplina["nm_disciplina"];

                            $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                            $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$campo[0],$campo[0] );
                            if ( is_array($lst_avaliacao_valores) )
                            {
                                $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                                $nm_nota = $det_avaliacao_valores["nome"];

                                $obj_hd = new clsPmieducarHistoricoDisciplinas();
                                $lst_hd = $obj_hd->lista( null, $this->ref_cod_aluno, $sequencial, $nm_disciplina );
                                if (is_array($lst_hd))
                                {
                                    $det_hd = array_shift($lst_hd);
                                    $obj_hd = new clsPmieducarHistoricoDisciplinas( $det_hd["sequencial"], $this->ref_cod_aluno, $sequencial, $nm_disciplina, $nm_nota, $campo[1] );
                                    $hst_disciplina = $obj_hd->edita();
                                }
                                else
                                {
                                    $obj_hd = new clsPmieducarHistoricoDisciplinas( null, $this->ref_cod_aluno, $sequencial, $nm_disciplina, $nm_nota, $campo[1] );
                                    $hst_disciplina = $obj_hd->cadastra();
                                }
                                if( !$hst_disciplina )
                                {
                                    $this->mensagem = "Cadastro/Edi&ccedil;&atilde;o do Hist&oacute;rico Disciplinas n&atilde;o realizado.<br>";
                                    return false;
                                }
                            }
                            else
                            {
                                $this->mensagem = "N&atilde;o foi poss&iacute;vel encontrar os Valores do Tipo de Avalia&ccedil;&atilde;o.<br>";
                                return false;
                            }
                        }
                    }
                    /*
                    else if( (!$editou_he || !$cadastrou_he) && (!$this->conceitual) )
                    {
                        $this->mensagem = "Cadastro/Edi&ccedil;&atilde;o do Hist&oacute;rico Escolar n&atilde;o realizado.<br>";
                        return false;
                    }
                    */
                }

                $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
                $this->simpleRedirect("educar_falta_nota_aluno_det.php?ref_cod_matricula={$this->ref_cod_matricula}&ref_cod_turma={$this->ref_cod_turma}&sequencial={$this->ref_sequencial_matricula_turma}");
            }
            $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada (Matr&iacute;cula).<br>";

            return false;
        }
    //************************************* EDITA - MATRICULADO NUM CURSO *************************************//
        else
        {
            if( !$this->reprova_falta )
            {
                $this->editaCNotasFaltas();
            }
            /**
             * verifica se existem disciplinas sem notas
             *
             */
            $obj_nota_aluno = new clsPmieducarNotaAluno();
            $total = $obj_nota_aluno->getQtdRestanteNotasAlunoNaoApuraFaltas($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_cod_turma,$this->modulo,$this->ref_ref_cod_escola);

            /**
             * existem disciplinas sem notas
             * somente cadastra e o modulo do aluno
             * continua igual sem calcular nada
             */
            if($total)
            {
                $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
                $this->simpleRedirect("educar_falta_nota_aluno_det.php?ref_cod_matricula={$this->ref_cod_matricula}&ref_cod_turma={$this->ref_cod_turma}&sequencial={$this->ref_sequencial_matricula_turma}");
            }

            $aprovado = 3;

            if ( $this->qtd_modulos <= $this->mat_modulo )
            {
                $obj_curso = new clsPmieducarCurso( $this->ref_cod_curso );
                $det_curso = $obj_curso->detalhe();
                $frequencia_minima = $det_curso["frequencia_minima"];
                $hora_falta = $det_curso["hora_falta"];
                $carga_horaria_curso = $det_curso["carga_horaria"];

                $obj_disciplina = new clsPmieducarDisciplina();
                $lst_disciplina = $obj_disciplina->lista( null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_curso );
                if ( is_array($lst_disciplina) )
                {
                    foreach ( $lst_disciplina AS $campo )
                    {
                        $obj_nota_aluno = new clsPmieducarNotaAluno();
                        $lst_nota_aluno = $obj_nota_aluno->lista( null,null,null,null,null,null,$this->ref_cod_matricula,null,null,null,null,null,null,1,null,$campo["cod_disciplina"] );
                        if ( is_array($lst_nota_aluno) )
                        {
                            foreach ($lst_nota_aluno AS $key => $nota_aluno)
                            {
                                if ($nota_aluno["nota"])
                                {
                                    $soma_notas[$campo["cod_disciplina"]][$key] = $nota_aluno["nota"]*2;
                                }
                                else
                                {
                                    $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores( $nota_aluno["ref_ref_cod_tipo_avaliacao"], $nota_aluno["ref_sequencial"] );
                                    $det_avaliacao_valores = $obj_avaliacao_valores->detalhe();
                                    $soma_notas[$campo["cod_disciplina"]][$key] = $det_avaliacao_valores["valor"];
                                }
                            }
                        }

                        if (!$this->falta_ch_globalizada)
                        {
                            $obj_falta_aluno = new clsPmieducarFaltaAluno();
                            $lst_falta_aluno = $obj_falta_aluno->lista( null,null,null,null,null,null,$this->ref_cod_matricula,null,null,null,null,null,1,null,$campo["cod_disciplina"] );
                            if ( is_array($lst_falta_aluno) )
                            {
                                foreach ($lst_falta_aluno AS $key => $falta_aluno)
                                {
                                    $soma_faltas[$campo["cod_disciplina"]][$key] = $falta_aluno["faltas"];
                                }
                            }
                        }
                    }
                    if ( is_array($soma_faltas) )
                    {
                        foreach ($soma_faltas AS $disciplina => $faltas)
                        {
                            foreach ($faltas as $falta)
                            {
                                $faltas_media_aluno[$disciplina] += $falta;
                            }
                        }
                    }
                }
                if ( is_array($faltas_media_aluno) )
                {
                    foreach ($faltas_media_aluno AS $disciplina => $faltas)
                    {
                        $obj_disciplina = new clsPmieducarDisciplina( $disciplina );
                        $det_disciplina = $obj_disciplina->detalhe();
                        $carga_horaria_disciplina = $det_disciplina["carga_horaria"];

                        // calcula o maximo de horas q o aluno pode faltar na disciplina
                        $max_falta = ($carga_horaria_disciplina * $frequencia_minima)/100;
                        $max_falta = $carga_horaria_disciplina - $max_falta;

                        // calcula a quantidade de faltas por hora do aluno na disciplina
                        $faltas *= $hora_falta;

                        if ( ($faltas > $max_falta) && !$this->reprova_falta )
                        {
                            echo "<script>
                                    if( confirm('O aluno excedeu o valor máximo de faltas permitidas, \\n deseja reprová-lo? \\n Quantidade de faltas do aluno: $faltas \\n Valor máximo de faltas permitido: $max_falta \\n \\n Clique em OK para reprová-lo ou em CANCELAR para ignorar.') )
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&modulo=$this->modulo&falta=s';
                                    }
                                    else
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&modulo=$this->modulo&falta=n';
                                    }
                                </script>";
                            return true;
                        }
                        if( $this->reprova_falta == 's' )
                        {
                            $aprovado = 2; // aluno reprovado por falta
                        }

                    }
                }
                else
                {
                    // calcula o maximo de horas q o aluno pode faltar no curso
                    $max_falta = ($carga_horaria_curso * $frequencia_minima)/100;
                    $max_falta = $carga_horaria_curso - $max_falta;

                    // calcula a qtd de faltas por hora do aluno no curso
                    $obj_faltas = new clsPmieducarFaltas();
                    $lst_faltas = $obj_faltas->lista( $this->ref_cod_matricula );
                    if ( is_array($lst_faltas) )
                    {
                        $total_faltas = 0;
                        foreach ( $lst_faltas AS $key => $faltas )
                        {
                            $total_faltas += $faltas['falta'];
                        }
                        $total_faltas *= $hora_falta;

                        if ( ($total_faltas > $max_falta) && !$this->reprova_falta )
                        {
                            echo "<script>
                                    if( confirm('O aluno excedeu o valor máximo de faltas permitidas, \\n deseja reprová-lo? \\n Quantidade de faltas do aluno: $total_faltas \\n Valor máximo de faltas permitido: $max_falta \\n \\n Clique em OK para reprová-lo ou em CANCELAR para ignorar.') )
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&modulo=$this->modulo&falta=s';
                                    }
                                    else
                                    {
                                        window.location = 'educar_falta_nota_aluno_cad.php?ref_cod_matricula=$this->ref_cod_matricula&ref_cod_turma=$this->ref_cod_turma&ref_sequencial_matricula_turma=$this->ref_sequencial_matricula_turma&modulo=$this->modulo&falta=n';
                                    }
                                </script>";
                            return true;
                        }
                        if( $this->reprova_falta == 's' )
                        {
                            $aprovado = 2; // aluno reprovado por falta
                        }
                    }
                }
            }
            if ( ($this->qtd_modulos < $this->mat_modulo) && ($this->qtd_modulos == $max_nota) )
            {
                if ( is_array($soma_notas) )
                {
                    foreach ($soma_notas AS $disciplina => $notas)
                    {
                        foreach ($notas as $nota)
                        {
                            $nota_media_aluno[$disciplina] += $nota;
                        }
                        $nota_media_aluno[$disciplina] /= ($this->mat_modulo - 1);
                    }

                    foreach ($nota_media_aluno AS $disciplina => $nota)
                    {

                        $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                        $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$nota,$nota );
                        if ( is_array($lst_avaliacao_valores) )
                        {
                            $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                            $valor = $det_avaliacao_valores["valor"];
                            /*
                            if ( ($valor < $this->media) && $this->media_exame && !$this->conceitual )
                            {
                                $em_exame = true;
                            }
                            else if ( ($valor < $this->media) && !$this->media_exame && !$this->conceitual )
                            {
                                $aprovado = 2; // aluno reprovado direto (n existe exame)
                            }
                            */
                        }
                        /**
                         * verifica se existem disciplinas sem notas
                         * somente aprova caso seja zero
                         */
                        $obj_nota_aluno = new clsPmieducarNotaAluno();
                        $total = $obj_nota_aluno->getQtdRestanteNotasAlunoNaoApuraFaltas($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_cod_turma,$this->modulo,$this->ref_ref_cod_escola);

                        if ( ($nota < $this->media) && $this->media_exame && !$this->conceitual /*&& !$total*/ )
                        {
                            $em_exame = true; // aluno em exame
                        }
                        else if ( ($valor < $this->media) && !$this->media_exame && !$this->conceitual /*&& !$total*/ )
                        {
                            $aprovado = 2; // aluno reprovado direto (n existe exame)
                        }
                    }
                }
            }
            else if ( ($this->qtd_modulos < $this->mat_modulo) && ($this->qtd_modulos < $max_nota) )
            {
                foreach ($soma_notas AS $disciplina => $notas)
                {
                    $qtd_notas = 0;
                    foreach ($notas as $nota)
                    {
                        $nota_media_aluno[$disciplina] += $nota;
                        $qtd_notas++;
                    }

                    if ($qtd_notas == $this->mat_modulo)
                    {
                        $nota_media_aluno[$disciplina] /= $this->mat_modulo;
                    }
                    else
                    {
                        $nota_media_aluno[$disciplina] /= ($this->mat_modulo - 1);
                    }
                }

                foreach ($nota_media_aluno AS $disciplina => $nota)
                {
                    $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                    $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$nota,$nota );
                    if ( is_array($lst_avaliacao_valores) )
                    {
                        $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                        $valor = $det_avaliacao_valores["valor"];

                        if ($valor < $this->media_exame)
                        {
                            $aprovado = 2; // aluno reprovado no exame
                        }
                    }
                }
            }
            /**
             * verifica se existem disciplinas sem notas
             * somente aprova caso seja zero
             */
            //$obj_nota_aluno = new clsPmieducarNotaAluno();
            //$total = $obj_nota_aluno->getQtdRestanteNotasAlunoNaoApuraFaltas($this->ref_cod_matricula,$this->ref_ref_cod_serie,$this->ref_cod_turma,$this->modulo,$this->ref_ref_cod_escola);

            if ($this->conceitual)
            {
                $aprovado = $this->aprovado; // situacao definida pelo professor
            }
            else if( !$em_exame && ($this->qtd_modulos <= $this->mat_modulo) && ($aprovado == 3) && !$this->conceitual /*&& !$total*/ )
            {
                $aprovado = 1; // aluno aprovado
            }

            $obj = new clsPmieducarMatricula( $this->ref_cod_matricula,null,null,null,$this->pessoa_logada,null,null,$aprovado );
            $editou = $obj->edita();
            if( $editou )
            {
                /**
                 * aluno reprovado edita nao remove do historico
                 */
                if ( ($aprovado == 2) || ($aprovado == 3) )
                {
                    $obj_hst_escolar = new clsPmieducarHistoricoEscolar();
                    $lst_hst_escolar = $obj_hst_escolar->lista( $this->ref_cod_aluno,null,null,null,$det_curso['nm_curso'],$this->ano_letivo,$carga_horaria_curso,null,null,null,null,null,null,null,null,null,null,null,null,$this->ref_cod_instituicao,0,1,$this->ref_cod_matricula );
                    if (is_array($lst_hst_escolar))
                    {
                        $det_hst_escolar = array_shift($lst_hst_escolar);

                        $obj_hd = new clsPmieducarHistoricoDisciplinas();
                        $excluiu_hd = $obj_hd->excluirTodos( $this->ref_cod_aluno, $det_hst_escolar["sequencial"] );
                        if (!$excluiu_hd)
                        {
                            $this->mensagem = "Exclus&atilde;o do Hist&oacute;rico Disciplina n&atilde;o realizado.<br>";
                            return false;
                        }

                        $obj_hst_escolar = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno,$det_hst_escolar["sequencial"],$this->pessoa_logada,null,null,null,null,null,null,null,null,null,null,null,null,0 );
                        $excluiu_he = $obj_hst_escolar->excluir();
                        if (!$excluiu_he)
                        {
                            $this->mensagem = "Exclus&atilde;o do Hist&oacute;rico Escolar n&atilde;o realizado.<br>";
                            return false;
                        }
                    }
                }
                /*else*/ if ($aprovado == 1 || $aprovado == 2)
                {
                    // busca informacoes da instituicao
                    $obj_instituicao = new clsPmieducarInstituicao( $this->ref_cod_instituicao );
                    $det_instituicao = $obj_instituicao->detalhe();
                    $nm_instituicao = $det_instituicao["nm_instituicao"];
                    $cidade = $det_instituicao["cidade"];
                    $uf = $det_instituicao["ref_sigla_uf"];

                    $obj_hst_escolar = new clsPmieducarHistoricoEscolar();
                    $lst_hst_escolar = $obj_hst_escolar->lista( $this->ref_cod_aluno,null,null,null,$det_curso['nm_curso'],$this->ano_letivo,$carga_horaria_curso,null,null,null,null,null,null,null,null,null,null,null,null,$this->ref_cod_instituicao,0,1,$this->ref_cod_matricula );

                    $sql = "SELECT SUM(falta) FROM pmieducar.faltas WHERE ref_cod_matricula = {$this->ref_cod_matricula}";
                    $db5 = new clsBanco();
                    $total_faltas = $db5->CampoUnico($sql);

                    if (is_array($lst_hst_escolar))
                    {
                        $det_hst_escolar = array_shift($lst_hst_escolar);

                        $obj_hst_escolar = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno,$det_hst_escolar["sequencial"],$this->pessoa_logada,null,$det_curso['nm_curso'],$this->ano_letivo,$carga_horaria_curso,null,$nm_instituicao,$cidade,$uf,null,$aprovado,null,null,1,$total_faltas,$this->ref_cod_instituicao,0,1,$this->ref_cod_matricula );
                        $editou_he = $obj_hst_escolar->edita();
                    }
                    else
                    {
                        $obj_hst_escolar = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno,null,null,$this->pessoa_logada,$det_curso['nm_curso'],$this->ano_letivo,$carga_horaria_curso,null,$nm_instituicao,$cidade,$uf,null,$aprovado,null,null,1,$total_faltas,$this->ref_cod_instituicao,0,1,$this->ref_cod_matricula );
                        $cadastrou_he = $obj_hst_escolar->cadastra();
                    }
                    if( ($editou_he || $cadastrou_he) && !$this->conceitual)
                    {
                        if ($cadastrou_he)
                        {
                            $obj_historico = new clsPmieducarHistoricoEscolar();
                            $sequencial = $obj_historico->getMaxSequencial( $this->ref_cod_aluno );
                        }
                        else
                        {
                            $sequencial = $det_hst_escolar["sequencial"];
                        }

                        $historico_disciplina = array();
                        foreach ($nota_media_aluno as $key => $nota)
                        {
                            $historico_disciplina[$key] = array( $nota, $faltas_media_aluno[$key] );
                        }

                        foreach ($historico_disciplina AS $disciplina => $campo)
                        {
                            $obj_disciplina = new clsPmieducarDisciplina( $disciplina );
                            $det_disciplina = $obj_disciplina->detalhe();
                            $nm_disciplina = $det_disciplina["nm_disciplina"];

                            $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
                            $lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao,null,null,null,$campo[0],$campo[0] );
                            if ( is_array($lst_avaliacao_valores) )
                            {
                                $det_avaliacao_valores = array_shift($lst_avaliacao_valores);
                                $nm_nota = $det_avaliacao_valores["nome"];

                                $obj_hd = new clsPmieducarHistoricoDisciplinas();
                                $lst_hd = $obj_hd->lista( null, $this->ref_cod_aluno, $sequencial, $nm_disciplina );
                                if (is_array($lst_hd))
                                {
                                    $det_hd = array_shift($lst_hd);
                                    $obj_hd = new clsPmieducarHistoricoDisciplinas( $det_hd["sequencial"], $this->ref_cod_aluno, $sequencial, $nm_disciplina, $nm_nota, $campo[1] );
                                    $hst_disciplina = $obj_hd->edita();
                                }
                                else
                                {
                                    $obj_hd = new clsPmieducarHistoricoDisciplinas( null, $this->ref_cod_aluno, $sequencial, $nm_disciplina, $nm_nota, $campo[1] );
                                    $hst_disciplina = $obj_hd->cadastra();
                                }
                                if( !$hst_disciplina )
                                {
                                    $this->mensagem = "Cadastro/Edi&ccedil;&atilde;o do Hist&oacute;rico Disciplinas n&atilde;o realizado.<br>";
                                    return false;
                                }
                            }
                            else
                            {
                                $this->mensagem = "N&atilde;o foi poss&iacute;vel encontrar os Valores do Tipo de Avalia&ccedil;&atilde;o.<br>";
                                return false;
                            }
                        }
                    }
                    /*
                    else if( !$editou_he || !$cadastrou_he )
                    {
                        $this->mensagem = "Cadastro/Edi&ccedil;&atilde;o do Hist&oacute;rico Escolar n&atilde;o realizado.<br>";
                        return false;
                    }
                    */
                }

                $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
                $this->simpleRedirect("educar_falta_nota_aluno_det.php?ref_cod_matricula={$this->ref_cod_matricula}&ref_cod_turma={$this->ref_cod_turma}&sequencial={$this->ref_sequencial_matricula_turma}");
            }
            $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada (Matr&iacute;cula).<br>";

            return false;
        }
    }

    function cadastraSNotasFaltas()
    {
        if ( is_array($this->disciplina_modulo) )
        {
                foreach ( $this->disciplina_modulo AS $avaliacao )
                {
                    if( is_numeric($avaliacao["nota"]) )
                    {
                        $obj = new clsPmieducarNotaAluno( null, $avaliacao["nota"], $this->ref_cod_tipo_avaliacao, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $avaliacao["ref_cod_disciplina"], $this->ref_cod_matricula, null, $this->pessoa_logada, null, null, 1, $this->modulo );
                    }
                    else
                    {
                        $avaliacao["nota"] = str_replace( ".", "", $avaliacao["nota"] );
                        $avaliacao["nota"] = str_replace( ",", ".", $avaliacao["nota"] );

                        $obj = new clsPmieducarNotaAluno( null, null, null, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $avaliacao["ref_cod_disciplina"], $this->ref_cod_matricula, null, $this->pessoa_logada, null, null, 1, $this->modulo, null, $avaliacao["nota"] );
                    }

                    if( is_numeric($avaliacao["nota"]) )
                    {
                        $cadastrou = $obj->cadastra();
                        if( $cadastrou && ($this->qtd_modulos >= $this->modulo) )
                        {
                            if ( !$this->falta_ch_globalizada && is_numeric($avaliacao["faltas"]) )
                            {
                                $obj = new clsPmieducarFaltaAluno( null, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $avaliacao["ref_cod_disciplina"],$this->ref_cod_matricula, $avaliacao["faltas"], null, null, 1, $this->modulo );
                                $cadastrou1 = $obj->cadastra();
                                if( !$cadastrou1 )
                                {
                                    $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                                    return false;
                                }
                            }
                        }
                        elseif ( !$cadastrou )
                        {
                            $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                            return false;
                        }
                    }
                }
                if ($cadastrou)
                {
                    $obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula, null, null, null, $this->pessoa_logada);
                    $obj_matricula->avancaModulo();
                }
                if( $cadastrou && ($this->qtd_modulos >= $this->modulo) && $this->falta_ch_globalizada && is_numeric($this->total_faltas) )
                {
                    $obj = new clsPmieducarFaltas( $this->ref_cod_matricula, $this->modulo, $this->pessoa_logada, $this->total_faltas );
                    if($obj->existe())
                        $cadastrou1 = $obj->edita();
                    else
                        $cadastrou1 = $obj->cadastra();
                    if( !$cadastrou1 )
                    {
                        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                        return false;
                    }
                }
        }
        else
        {
            $this->mensagem = "Cadastro n&atilde;o realizado (N&atilde;o foi gerado o Array de notas e faltas das disciplinas).<br>";
            return false;
        }
    }

    function cadastraCNotasFaltas()
    {
        if ( is_array($this->disciplina_modulo) )
        {
                foreach ( $this->disciplina_modulo AS $avaliacao )
                {
                    if( is_numeric($avaliacao["nota"]) )
                    {
                        $obj = new clsPmieducarNotaAluno( null, $avaliacao["nota"], $this->ref_cod_tipo_avaliacao, null, null, null, $this->ref_cod_matricula, null, $this->pessoa_logada, null, null, 1, $this->modulo, $avaliacao["ref_cod_disciplina"] );
                    }
                    else
                    {
                        $avaliacao["nota"] = str_replace( ".", "", $avaliacao["nota"] );
                        $avaliacao["nota"] = str_replace( ",", ".", $avaliacao["nota"] );

                        $obj = new clsPmieducarNotaAluno( null, null, null, null, null, null, $this->ref_cod_matricula, null, $this->pessoa_logada, null, null, 1, $this->modulo, $avaliacao["ref_cod_disciplina"], $avaliacao["nota"] );
                    }
                    if( is_numeric($avaliacao["nota"]) )
                    {
                        $cadastrou = $obj->cadastra();
                        if( $cadastrou && ($this->qtd_modulos >= $this->modulo) )
                        {
                            if ( !$this->falta_ch_globalizada && is_numeric($avaliacao["faltas"]) )
                            {
                                $obj = new clsPmieducarFaltaAluno( null, null, $this->pessoa_logada, null, null, null, $this->ref_cod_matricula, $avaliacao["faltas"], null, null, 1, $this->modulo, $avaliacao["ref_cod_disciplina"] );
                                $cadastrou1 = $obj->cadastra();
                                if( !$cadastrou1 )
                                {
                                    $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                                    return false;
                                }
                            }
                        }
                        elseif ( !$cadastrou )
                        {
                            $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                            return false;
                        }
                    }
                }
                if( $cadastrou && ($this->qtd_modulos >= $this->modulo) && $this->falta_ch_globalizada && is_numeric($this->total_faltas) )
                {
                    $obj = new clsPmieducarFaltas( $this->ref_cod_matricula, $this->modulo, $this->pessoa_logada, $this->total_faltas );
                    $cadastrou1 = $obj->cadastra();
                    if( !$cadastrou1 )
                    {
                        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                        return false;
                    }
                }
        }
        else
        {
            $this->mensagem = "Cadastro n&atilde;o realizado (N&atilde;o foi gerado o Array de notas e faltas das disciplinas).<br>";
            return false;
        }
    }

    function editaSNotasFaltas()
    {
        if ( is_array($this->disciplina_modulo) )
        {
            $this->nota_foi_removida = false;
            foreach ( $this->disciplina_modulo AS $avaliacao )
            {
                $obj_nota_aluno = new clsPmieducarNotaAluno( $avaliacao['cod_nota_aluno'], null, null, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $avaliacao["ref_cod_disciplina"], $this->ref_cod_matricula, null, null, null, null, 1, $this->modulo );
                $existe_nota = $obj_nota_aluno->existe();

                if ($existe_nota)
                {
                    if (is_numeric($avaliacao['nota']))
                    {
                        $obj_nota_aluno = new clsPmieducarNotaAluno( $avaliacao['cod_nota_aluno'], $avaliacao['nota'], $this->ref_cod_tipo_avaliacao, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $avaliacao["ref_cod_disciplina"], $this->ref_cod_matricula, $this->pessoa_logada, null, null, null, 1, $this->modulo );
                    }
                    else
                    {
                        $avaliacao["nota"] = str_replace( ".", "", $avaliacao["nota"] );
                        $avaliacao["nota"] = str_replace( ",", ".", $avaliacao["nota"] );

                        $obj_nota_aluno = new clsPmieducarNotaAluno( $avaliacao['cod_nota_aluno'], null, null, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $avaliacao["ref_cod_disciplina"], $this->ref_cod_matricula, $this->pessoa_logada, null, null, null, 1, $this->modulo, null, $avaliacao['nota'] );
                    }
                    if($avaliacao['nota'] == -1)
                    {
                        $editou_nota = $obj_nota_aluno->excluir();
                        $this->nota_foi_removida = true;
                    }
                    else
                    {
                        $editou_nota = $obj_nota_aluno->edita();
                    }

                    if (!$editou_nota)
                    {
                        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

                        return false;
                    }
                }
                else
                {
                    if (is_numeric($avaliacao['nota']))
                    {
                        $obj_nota_aluno = new clsPmieducarNotaAluno( null, $avaliacao["nota"], $this->ref_cod_tipo_avaliacao, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $avaliacao["ref_cod_disciplina"], $this->ref_cod_matricula, null, $this->pessoa_logada, null, null, 1, $this->modulo );
                    }
                    else
                    {
                        $avaliacao["nota"] = str_replace( ".", "", $avaliacao["nota"] );
                        $avaliacao["nota"] = str_replace( ",", ".", $avaliacao["nota"] );

                        $obj_nota_aluno = new clsPmieducarNotaAluno( null, null, null, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $avaliacao["ref_cod_disciplina"], $this->ref_cod_matricula, null, $this->pessoa_logada, null, null, 1, $this->modulo, null, $avaliacao["nota"] );
                    }
                    /**
                     * somente cadastra a nota se tiver algum valor
                     */
                    if (is_numeric($avaliacao['nota']))
                    {
                        $cadastrou_nota = $obj_nota_aluno->cadastra();
                        if (!$cadastrou_nota)
                        {
                            $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                            return false;
                        }
                    }
                }
                if ( ($this->qtd_modulos >= $this->modulo) && !$this->falta_ch_globalizada && is_numeric($avaliacao["faltas"]) )
                {
                    $obj_falta_aluno = new clsPmieducarFaltaAluno( $avaliacao['cod_falta_aluno'], null, null, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $avaliacao["ref_cod_disciplina"], $this->ref_cod_matricula, null, null, null, 1, $this->modulo );
                    $existe_falta = $obj_falta_aluno->existe();
                    if ($existe_falta)
                    {
                        $obj_falta_aluno = new clsPmieducarFaltaAluno( $avaliacao['cod_falta_aluno'], $this->pessoa_logada, null, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $avaliacao["ref_cod_disciplina"], $this->ref_cod_matricula, $avaliacao["faltas"], null, null, 1, $this->modulo );
                        $editou_falta = $obj_falta_aluno->edita();
                        if (!$editou_falta)
                        {
                            $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

                            return false;
                        }
                    }
                    else
                    {
                        if(is_numeric($avaliacao["faltas"]))
                        {
                            $obj_falta_aluno = new clsPmieducarFaltaAluno( null, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $avaliacao["ref_cod_disciplina"],$this->ref_cod_matricula, $avaliacao["faltas"], null, null, 1, $this->modulo );
                            $cadastrou_falta = $obj_falta_aluno->cadastra();
                            if( !$cadastrou_falta )
                            {
                                $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                                return false;
                            }
                        }
                    }
                }
            }
            if ( ($this->qtd_modulos >= $this->modulo) && $this->falta_ch_globalizada && is_numeric($this->total_faltas) )
            {
                $obj_faltas = new clsPmieducarFaltas( $this->ref_cod_matricula, $this->modulo );
                $existe_faltas = $obj_faltas->existe();
                if ($existe_faltas)
                {
                    $obj_faltas = new clsPmieducarFaltas( $this->ref_cod_matricula, $this->modulo, null, $this->total_faltas );
                    $editou_faltas = $obj_faltas->edita();
                    if (!$editou_faltas)
                    {
                        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

                        return false;
                    }
                }
                else
                {

                    $obj_faltas = new clsPmieducarFaltas( $this->ref_cod_matricula, $this->modulo, $this->pessoa_logada, $this->total_faltas );
                    $cadastrou_faltas = $obj_faltas->cadastra();
                    if( !$cadastrou_faltas )
                    {
                        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                        return false;
                    }

                }
            }
        }
        else
        {
            $this->mensagem = "Edi&ccedil;atilde;o n&atilde;o realizada. (N&atilde;o foi gerado o Array de notas e faltas das Disciplinas).<br>";
            return false;
        }
    }

    function editaCNotasFaltas()
    {
        if ( is_array($this->disciplina_modulo) )
        {
            foreach ( $this->disciplina_modulo AS $avaliacao )
            {
                $obj_nota_aluno = new clsPmieducarNotaAluno( $avaliacao['cod_nota_aluno'], null, null, null, null, null, $this->ref_cod_matricula, null, null, null, null, 1, $this->modulo, $avaliacao["ref_cod_disciplina"] );
                $existe_nota = $obj_nota_aluno->existe();
                if ($existe_nota)
                {
                    if (is_numeric($avaliacao['nota']))
                    {
                        $obj_nota_aluno = new clsPmieducarNotaAluno( $avaliacao['cod_nota_aluno'], $avaliacao['nota'], $this->ref_cod_tipo_avaliacao, null, null, null, $this->ref_cod_matricula, $this->pessoa_logada, null, null, null, 1, $this->modulo, $avaliacao["ref_cod_disciplina"] );
                    }
                    else
                    {
                        $avaliacao["nota"] = str_replace( ".", "", $avaliacao["nota"] );
                        $avaliacao["nota"] = str_replace( ",", ".", $avaliacao["nota"] );

                        $obj_nota_aluno = new clsPmieducarNotaAluno( $avaliacao['cod_nota_aluno'], null, null, null, null, null, $this->ref_cod_matricula, $this->pessoa_logada, null, null, null, 1, $this->modulo, $avaliacao["ref_cod_disciplina"], $avaliacao['nota'] );
                    }
                    if($avaliacao['nota'] == -1)
                        $editou_nota = $obj_nota_aluno->excluir();
                    else
                        $editou_nota = $obj_nota_aluno->edita();
                    if (!$editou_nota)
                    {
                        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

                        return false;
                    }
                }
                else
                {
                    if (is_numeric($avaliacao['nota']))
                    {
                        $obj_nota_aluno = new clsPmieducarNotaAluno( null, $avaliacao["nota"], $this->ref_cod_tipo_avaliacao, null, null, null, $this->ref_cod_matricula, null, $this->pessoa_logada, null, null, 1, $this->modulo, $avaliacao["ref_cod_disciplina"] );
                    }
                    else
                    {
                        $avaliacao["nota"] = str_replace( ".", "", $avaliacao["nota"] );
                        $avaliacao["nota"] = str_replace( ",", ".", $avaliacao["nota"] );
                        $obj_nota_aluno = new clsPmieducarNotaAluno( null, null, null, null, null, null, $this->ref_cod_matricula, null, $this->pessoa_logada, null, null, 1, $this->modulo, $avaliacao["ref_cod_disciplina"], $avaliacao["nota"] );
                    }
                    if (is_numeric($avaliacao['nota']))
                    {
                        $cadastrou_nota = $obj_nota_aluno->cadastra();
                        if (!$cadastrou_nota)
                        {
                            $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                            return false;
                        }
                    }
                }

                if ( ($this->qtd_modulos >= $this->modulo) && !$this->falta_ch_globalizada && is_numeric($avaliacao["faltas"]) )
                {
                    $obj_falta_aluno = new clsPmieducarFaltaAluno( $avaliacao['cod_falta_aluno'], null, null, null, null, null, $this->ref_cod_matricula, null, null, null, 1, $this->modulo, $avaliacao["ref_cod_disciplina"] );
                    $existe_falta = $obj_falta_aluno->existe();
                    if ($existe_falta)
                    {
                        $obj_falta_aluno = new clsPmieducarFaltaAluno( $avaliacao['cod_falta_aluno'], $this->pessoa_logada, null, null, null, null, $this->ref_cod_matricula, $avaliacao["faltas"], null, null, 1, $this->modulo, $avaliacao["ref_cod_disciplina"] );
                        $editou_falta = $obj_falta_aluno->edita();
                        if (!$editou_falta)
                        {
                            $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

                            return false;
                        }
                    }
                    else
                    {
                        $obj_falta_aluno = new clsPmieducarFaltaAluno( null, null, $this->pessoa_logada, null, null, null, $this->ref_cod_matricula, $avaliacao["faltas"], null, null, 1, $this->modulo, $avaliacao["ref_cod_disciplina"] );
                        $cadastrou_falta = $obj_falta_aluno->cadastra();
                        if( !$cadastrou_falta )
                        {
                            $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                            return false;
                        }
                    }
                }
            }
            if ( ($this->qtd_modulos >= $this->modulo) && $this->falta_ch_globalizada && is_numeric($this->total_faltas) )
            {
                $obj_faltas = new clsPmieducarFaltas( $this->ref_cod_matricula, $this->modulo );
                $existe_faltas = $obj_faltas->existe();
                if ($existe_faltas)
                {
                    $obj_faltas = new clsPmieducarFaltas( $this->ref_cod_matricula, $this->modulo, null, $this->total_faltas );
                    $editou_faltas = $obj_faltas->edita();
                    if (!$editou_faltas)
                    {
                        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

                        return false;
                    }
                }
                else
                {
                    $obj_faltas = new clsPmieducarFaltas( $this->ref_cod_matricula, $this->modulo, $this->pessoa_logada, $this->total_faltas );
                    $cadastrou_faltas = $obj_faltas->cadastra();
                    if( !$cadastrou_faltas )
                    {
                        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                        return false;
                    }
                }
            }
        }
        else
        {
            $this->mensagem = "Edi&ccedil;atilde;o n&atilde;o realizada. (N&atilde;o foi gerado o Array de notas e faltas das Disciplinas).<br>";
            return false;
        }
    }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();

?>
<script>

if( document.getElementById('reprova_falta').value )
{
    document.getElementById( 'formcadastro' ).submit();
}

</script>
