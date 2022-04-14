<?php

use iEducar\Legacy\Model;

class clsPmieducarInstituicao extends Model
{
    public $cod_instituicao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_idtlog;
    public $ref_sigla_uf;
    public $cep;
    public $cidade;
    public $bairro;
    public $logradouro;
    public $numero;
    public $complemento;
    public $nm_responsavel;
    public $ddd_telefone;
    public $telefone;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nm_instituicao;
    public $data_base_remanejamento;
    public $data_base_transferencia;
    public $exigir_vinculo_turma_professor;
    public $controlar_espaco_utilizacao_aluno;
    public $percentagem_maxima_ocupacao_salas;
    public $quantidade_alunos_metro_quadrado;
    public $gerar_historico_transferencia;
    public $controlar_posicao_historicos;
    public $restringir_multiplas_enturmacoes;
    public $permissao_filtro_abandono_transferencia;
    public $data_base_matricula;
    public $multiplas_reserva_vaga;
    public $permitir_carga_horaria;
    public $reserva_integral_somente_com_renda;
    public $data_expiracao_reserva_vaga;
    public $componente_curricular_turma;
    public $reprova_dependencia_ano_concluinte;
    public $data_educacenso;
    public $exigir_dados_socioeconomicos;
    public $altera_atestado_para_declaracao;
    public $obrigar_campos_censo;
    public $obrigar_documento_pessoa;
    public $orgao_regional;
    public $exigir_lancamentos_anteriores;
    public $exibir_apenas_professores_alocados;
    public $bloquear_vinculo_professor_sem_alocacao_escola;
    public $permitir_matricula_fora_periodo_letivo;
    public $ordenar_alunos_sequencial_enturmacao;
    public $obrigar_telefone_pessoa;

    public function __construct(
        $cod_instituicao = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $ref_idtlog = null,
        $ref_sigla_uf = null,
        $cep = null,
        $cidade = null,
        $bairro = null,
        $logradouro = null,
        $numero = null,
        $complemento = null,
        $nm_responsavel = null,
        $ddd_telefone = null,
        $telefone = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null,
        $nm_instituicao = null,
        $controlar_espaco_utilizacao_aluno = null,
        $percentagem_maxima_ocupacao_salas = null,
        $quantidade_alunos_metro_quadrado = null,
        $exigir_dados_socioeconomicos = null,
        $altera_atestado_para_declaracao = null,
        $obrigar_campos_censo = null,
        $obrigar_documento_pessoa = null,
        $exigir_lancamentos_anteriores = null,
        $exibir_apenas_professores_alocados = null,
        $bloquear_vinculo_professor_sem_alocacao_escola = null,
        $permitir_matricula_fora_periodo_letivo = null,
        $ordenar_alunos_sequencial_enturmacao = null,
        $obrigar_telefone_pessoa = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}instituicao";
        $this->_campos_lista = $this->_todos_campos = '
            cod_instituicao,
            ref_usuario_exc,
            ref_usuario_cad,
            ref_idtlog,
            ref_sigla_uf,
            cep,
            cidade,
            bairro,
            logradouro,
            numero,
            complemento,
            nm_responsavel,
            ddd_telefone,
            telefone,
            data_cadastro,
            data_exclusao,
            ativo,
            nm_instituicao,
            data_base_transferencia,
            data_base_remanejamento,
            controlar_espaco_utilizacao_aluno,
            percentagem_maxima_ocupacao_salas,
            quantidade_alunos_metro_quadrado,
            exigir_vinculo_turma_professor,
            gerar_historico_transferencia,
            matricula_apenas_bairro_escola,
            restringir_historico_escolar,
            coordenador_transporte,
            restringir_multiplas_enturmacoes,
            permissao_filtro_abandono_transferencia,
            data_base_matricula,
            multiplas_reserva_vaga,
            reserva_integral_somente_com_renda,
            data_expiracao_reserva_vaga,
            data_fechamento,
            componente_curricular_turma,
            controlar_posicao_historicos,
            reprova_dependencia_ano_concluinte,
            data_educacenso,
            bloqueia_matricula_serie_nao_seguinte,
            permitir_carga_horaria,
            exigir_dados_socioeconomicos,
            altera_atestado_para_declaracao,
            obrigar_campos_censo,
            obrigar_documento_pessoa,
            orgao_regional,
            exigir_lancamentos_anteriores,
            exibir_apenas_professores_alocados,
            bloquear_vinculo_professor_sem_alocacao_escola,
            permitir_matricula_fora_periodo_letivo,
            ordenar_alunos_sequencial_enturmacao,
            obrigar_telefone_pessoa
        ';

        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_string($ref_idtlog)) {
            $this->ref_idtlog = $ref_idtlog;
        }

        if (is_numeric($cod_instituicao)) {
            $this->cod_instituicao = $cod_instituicao;
        }

        if (is_string($ref_sigla_uf)) {
            $this->ref_sigla_uf = $ref_sigla_uf;
        }

        if (is_numeric($cep)) {
            $this->cep = $cep;
        }

        if (is_string($cidade)) {
            $this->cidade = $cidade;
        }

        if (is_string($bairro)) {
            $this->bairro = $bairro;
        }

        if (is_string($logradouro)) {
            $this->logradouro = $logradouro;
        }

        if (is_numeric($numero)) {
            $this->numero = $numero;
        }

        if (is_string($complemento)) {
            $this->complemento = $complemento;
        }

        if (is_string($nm_responsavel)) {
            $this->nm_responsavel = $nm_responsavel;
        }

        if (is_numeric($ddd_telefone)) {
            $this->ddd_telefone = $ddd_telefone;
        }

        if (is_numeric($telefone)) {
            $this->telefone = $telefone;
        }

        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }

        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }

        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }

        if (is_string($nm_instituicao)) {
            $this->nm_instituicao = $nm_instituicao;
        }

        if (is_numeric($controlar_espaco_utilizacao_aluno)) {
            $this->controlar_espaco_utilizacao_aluno = $controlar_espaco_utilizacao_aluno;
        }

        if (is_numeric($percentagem_maxima_ocupacao_salas)) {
            $this->percentagem_maxima_ocupacao_salas = $percentagem_maxima_ocupacao_salas;
        }

        if (is_numeric($quantidade_alunos_metro_quadrado)) {
            $this->quantidade_alunos_metro_quadrado = $quantidade_alunos_metro_quadrado;
        }

        if (is_bool($exigir_dados_socioeconomicos)) {
            $this->exigir_dados_socioeconomicos = $exigir_dados_socioeconomicos;
        }

        if (is_bool($obrigar_campos_censo)) {
            $this->obrigar_campos_censo = $obrigar_campos_censo;
        }

        if (is_bool($obrigar_documento_pessoa)) {
            $this->obrigar_documento_pessoa = $obrigar_documento_pessoa;
        }

        if (is_bool($exigir_lancamentos_anteriores)) {
            $this->exigir_lancamentos_anteriores = $exigir_lancamentos_anteriores;
        }

        if (is_bool($exibir_apenas_professores_alocados)) {
            $this->exibir_apenas_professores_alocados = $exibir_apenas_professores_alocados;
        }

        if (is_bool($bloquear_vinculo_professor_sem_alocacao_escola)) {
            $this->bloquear_vinculo_professor_sem_alocacao_escola = $bloquear_vinculo_professor_sem_alocacao_escola;
        }

        if (is_bool($permitir_matricula_fora_periodo_letivo)) {
            $this->permitir_matricula_fora_periodo_letivo = $permitir_matricula_fora_periodo_letivo;
        }

        if (is_bool($ordenar_alunos_sequencial_enturmacao)) {
            $this->ordenar_alunos_sequencial_enturmacao = $ordenar_alunos_sequencial_enturmacao;
        }

        if (is_bool($obrigar_telefone_pessoa)) {
            $this->obrigar_telefone_pessoa = $obrigar_telefone_pessoa;
        }
    }

    public function canRegister()
    {
        return is_numeric($this->ref_usuario_cad) && is_string($this->ref_idtlog) &&
            is_string($this->ref_sigla_uf) && is_numeric($this->cep) &&
            is_string($this->cidade) && is_string($this->bairro) &&
            is_string($this->logradouro) && is_string($this->nm_responsavel) &&
            is_numeric($this->ativo) && is_string($this->nm_instituicao);
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if ($this->canRegister()) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $campos .= "{$gruda}ref_usuario_exc";
                $valores .= "{$gruda}'{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_string($this->ref_idtlog)) {
                $campos .= "{$gruda}ref_idtlog";
                $valores .= "{$gruda}'{$this->ref_idtlog}'";
                $gruda = ', ';
            }

            if (is_string($this->ref_sigla_uf)) {
                $campos .= "{$gruda}ref_sigla_uf";
                $valores .= "{$gruda}'{$this->ref_sigla_uf}'";
                $gruda = ', ';
            }

            if (is_numeric($this->cep)) {
                $campos .= "{$gruda}cep";
                $valores .= "{$gruda}'{$this->cep}'";
                $gruda = ', ';
            }

            if (is_string($this->cidade)) {
                $campos .= "{$gruda}cidade";
                $valores .= "{$gruda}'{$this->cidade}'";
                $gruda = ', ';
            }

            if (is_string($this->bairro)) {
                $campos .= "{$gruda}bairro";
                $valores .= "{$gruda}'{$this->bairro}'";
                $gruda = ', ';
            }

            if (is_string($this->logradouro)) {
                $campos .= "{$gruda}logradouro";
                $valores .= "{$gruda}'{$this->logradouro}'";
                $gruda = ', ';
            }

            if (is_numeric($this->numero)) {
                $campos .= "{$gruda}numero";
                $valores .= "{$gruda}'{$this->numero}'";
                $gruda = ', ';
            }

            if (is_string($this->complemento)) {
                $campos .= "{$gruda}complemento";
                $valores .= "{$gruda}'{$this->complemento}'";
                $gruda = ', ';
            }

            if (is_string($this->nm_responsavel)) {
                $campos .= "{$gruda}nm_responsavel";
                $valores .= "{$gruda}'{$this->nm_responsavel}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ddd_telefone)) {
                $campos .= "{$gruda}ddd_telefone";
                $valores .= "{$gruda}'{$this->ddd_telefone}'";
                $gruda = ', ';
            }

            if (is_numeric($this->telefone)) {
                $campos .= "{$gruda}telefone";
                $valores .= "{$gruda}'{$this->telefone}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            if (is_numeric($this->ativo)) {
                $campos .= "{$gruda}ativo";
                $valores .= "{$gruda}'{$this->ativo}'";
                $gruda = ', ';
            }

            if (is_string($this->nm_instituicao)) {
                $instituicao = $db->escapeString($this->nm_instituicao);
                $campos .= "{$gruda}nm_instituicao";
                $valores .= "{$gruda}'{$instituicao}'";
                $gruda = ', ';
            }

            if ((is_string($this->data_base_remanejamento)) and !empty($this->data_base_remanejamento)) {
                $campos .= "{$gruda}data_base_remanejamento";
                $valores .= "{$gruda}'{$this->data_base_remanejamento}'";
                $gruda = ', ';
            }

            if ((is_string($this->data_base_transferencia)) and !empty($this->data_base_transferencia)) {
                $campos .= "{$gruda}data_base_transferencia";
                $valores .= "{$gruda}'{$this->data_base_transferencia}'";
                $gruda = ', ';
            }

            if ((is_string($this->data_expiracao_reserva_vaga)) and !empty($this->data_expiracao_reserva_vaga)) {
                $campos .= "{$gruda}data_expiracao_reserva_vaga";
                $valores .= "{$gruda}'{$this->data_expiracao_reserva_vaga}'";
                $gruda = ', ';
            }

            if (is_numeric($this->exigir_vinculo_turma_professor)) {
                $campos .= "{$gruda}exigir_vinculo_turma_professor";
                $valores .= "{$gruda}'{$this->exigir_vinculo_turma_professor}'";
                $gruda = ', ';
            }

            if (is_numeric($this->controlar_espaco_utilizacao_aluno)) {
                $campos .= "{$gruda}controlar_espaco_utilizacao_aluno";
                $valores .= "{$gruda}'{$this->controlar_espaco_utilizacao_aluno}'";
                $gruda = ', ';
            }

            if (is_numeric($this->percentagem_maxima_ocupacao_salas)) {
                $campos .= "{$gruda}percentagem_maxima_ocupacao_salas";
                $valores .= "{$gruda}'{$this->percentagem_maxima_ocupacao_salas}'";
                $gruda = ', ';
            }

            if (is_numeric($this->quantidade_alunos_metro_quadrado)) {
                $campos .= "{$gruda}quantidade_alunos_metro_quadrado";
                $valores .= "{$gruda}'{$this->quantidade_alunos_metro_quadrado}'";
                $gruda = ', ';
            }

            if (dbBool($this->gerar_historico_transferencia)) {
                $campos .= "{$gruda}gerar_historico_transferencia";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}gerar_historico_transferencia";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }
            if (dbBool($this->controlar_posicao_historicos)) {
                $campos .= "{$gruda}controlar_posicao_historicos";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}controlar_posicao_historicos";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }
            if (dbBool($this->matricula_apenas_bairro_escola)) {
                $campos .= "{$gruda}matricula_apenas_bairro_escola";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}matricula_apenas_bairro_escola";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }
            if (dbBool($this->restringir_historico_escolar)) {
                $campos .= "{$gruda}restringir_historico_escolar";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}restringir_historico_escolar";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (strripos($this->coordenador_transporte, '-') and strripos($this->coordenador_transporte, '(')) {
                $this->coordenador_transporte = $this->parteString($this->coordenador_transporte, '-', '(');
            }

            if (is_string($this->coordenador_transporte)) {
                $campos .= "{$gruda}coordenador_transporte";
                $valores .= "{$gruda}'{$this->coordenador_transporte}'";
                $gruda = ', ';
            }

            if (dbBool($this->restringir_multiplas_enturmacoes)) {
                $campos .= "{$gruda}restringir_multiplas_enturmacoes";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}restringir_multiplas_enturmacoes";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->permissao_filtro_abandono_transferencia)) {
                $campos .= "{$gruda}permissao_filtro_abandono_transferencia";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}permissao_filtro_abandono_transferencia";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->multiplas_reserva_vaga)) {
                $campos .= "{$gruda}multiplas_reserva_vaga";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}multiplas_reserva_vaga";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->permitir_carga_horaria)) {
                $campos .= "{$gruda}permitir_carga_horaria";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}permitir_carga_horaria";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->componente_curricular_turma)) {
                $campos .= "{$gruda}componente_curricular_turma";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}componente_curricular_turma";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->reprova_dependencia_ano_concluinte)) {
                $campos .= "{$gruda}reprova_dependencia_ano_concluinte";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}reprova_dependencia_ano_concluinte";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->reserva_integral_somente_com_renda)) {
                $campos .= "{$gruda}reserva_integral_somente_com_renda";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}reserva_integral_somente_com_renda";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->bloqueia_matricula_serie_nao_seguinte)) {
                $campos .= "{$gruda}bloqueia_matricula_serie_nao_seguinte";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}bloqueia_matricula_serie_nao_seguinte";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if ((is_string($this->data_base_matricula)) and !empty($this->data_base_matricula)) {
                $campos .= "{$gruda}data_base_matricula";
                $valores .= "{$gruda}'{$this->data_base_matricula}'";
                $gruda = ', ';
            }

            if ((is_string($this->data_fechamento)) and !empty($this->data_fechamento)) {
                $campos .= "{$gruda}data_fechamento";
                $valores .= "{$gruda}'{$this->data_fechamento}'";
                $gruda = ', ';
            }

            if ((is_string($this->data_educacenso)) and !empty($this->data_educacenso)) {
                $campos .= "{$gruda}data_educacenso";
                $valores .= "{$gruda}'{$this->data_educacenso}'";
                $gruda = ', ';
            }

            if (dbBool($this->exigir_dados_socieconomicos)) {
                $campos .= "{$gruda}exigir_dados_socioeconomicos";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}exigir_dados_socioeconomicos";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->altera_atestado_para_declaracao)) {
                $campos .= "{$gruda}altera_atestado_para_declaracao";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}altera_atestado_para_declaracao";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->obrigar_campos_censo)) {
                $campos .= "{$gruda}obrigar_campos_censo";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}obrigar_campos_censo";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->obrigar_documento_pessoa)) {
                $campos .= "{$gruda}obrigar_documento_pessoa";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}obrigar_documento_pessoa";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->exigir_lancamentos_anteriores)) {
                $campos .= "{$gruda}exigir_lancamentos_anteriores";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}exigir_lancamentos_anteriores";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->exibir_apenas_professores_alocados)) {
                $campos .= "{$gruda}exibir_apenas_professores_alocados";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}exibir_apenas_professores_alocados";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->bloquear_vinculo_professor_sem_alocacao_escola)) {
                $campos .= "{$gruda}bloquear_vinculo_professor_sem_alocacao_escola";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}bloquear_vinculo_professor_sem_alocacao_escola";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->permitir_matricula_fora_periodo_letivo)) {
                $campos .= "{$gruda}permitir_matricula_fora_periodo_letivo";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}permitir_matricula_fora_periodo_letivo";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->ordenar_alunos_sequencial_enturmacao)) {
                $campos .= "{$gruda}ordenar_alunos_sequencial_enturmacao";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}ordenar_alunos_sequencial_enturmacao";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (dbBool($this->obrigar_telefone_pessoa)) {
                $campos .= "{$gruda}obrigar_telefone_pessoa";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}obrigar_telefone_pessoa";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (is_string($this->orgao_regional) and !empty($this->orgao_regional)) {
                $campos .= "{$gruda}orgao_regional";
                $valores .= "{$gruda}'{$this->orgao_regional}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_instituicao_seq");
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->cod_instituicao)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_string($this->ref_idtlog)) {
                $set .= "{$gruda}ref_idtlog = '{$this->ref_idtlog}'";
                $gruda = ', ';
            }

            if (is_string($this->ref_sigla_uf)) {
                $set .= "{$gruda}ref_sigla_uf = '{$this->ref_sigla_uf}'";
                $gruda = ', ';
            }

            if (is_numeric($this->cep)) {
                $set .= "{$gruda}cep = '{$this->cep}'";
                $gruda = ', ';
            }

            if (is_string($this->cidade)) {
                $set .= "{$gruda}cidade = '{$this->cidade}'";
                $gruda = ', ';
            }

            if (is_string($this->bairro)) {
                $set .= "{$gruda}bairro = '{$this->bairro}'";
                $gruda = ', ';
            }

            if (is_string($this->logradouro)) {
                $set .= "{$gruda}logradouro = '{$this->logradouro}'";
                $gruda = ', ';
            }

            if (is_numeric($this->numero)) {
                $set .= "{$gruda}numero = '{$this->numero}'";
                $gruda = ', ';
            }

            if (is_string($this->complemento)) {
                $set .= "{$gruda}complemento = '{$this->complemento}'";
                $gruda = ', ';
            }

            if (is_string($this->nm_responsavel)) {
                $set .= "{$gruda}nm_responsavel = '{$this->nm_responsavel}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ddd_telefone)) {
                $set .= "{$gruda}ddd_telefone = '{$this->ddd_telefone}'";
                $gruda = ', ';
            }

            if (is_numeric($this->telefone)) {
                $set .= "{$gruda}telefone = '{$this->telefone}'";
                $gruda = ', ';
            }

            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }

            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';

            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if (is_string($this->nm_instituicao)) {
                $instituicao = $db->escapeString($this->nm_instituicao);
                $set .= "{$gruda}nm_instituicao = '{$instituicao}'";
                $gruda = ', ';
            }

            if (is_string($this->data_base_transferencia) and !empty($this->data_base_transferencia)) {
                $set .= "{$gruda}data_base_transferencia = '{$this->data_base_transferencia}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}data_base_transferencia = null ";
                $gruda = ', ';
            }

            if (is_string($this->data_expiracao_reserva_vaga) and !empty($this->data_expiracao_reserva_vaga)) {
                $set .= "{$gruda}data_expiracao_reserva_vaga = '{$this->data_expiracao_reserva_vaga}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}data_expiracao_reserva_vaga = null ";
                $gruda = ', ';
            }

            if (is_string($this->data_base_remanejamento) and !empty($this->data_base_remanejamento)) {
                $set .= "{$gruda}data_base_remanejamento = '{$this->data_base_remanejamento}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}data_base_remanejamento = null ";
                $gruda = ', ';
            }

            if (is_numeric($this->controlar_espaco_utilizacao_aluno)) {
                $set .= "{$gruda}controlar_espaco_utilizacao_aluno = '{$this->controlar_espaco_utilizacao_aluno}'";
                $gruda = ', ';
            }

            if (is_numeric($this->exigir_vinculo_turma_professor)) {
                $set .= "{$gruda}exigir_vinculo_turma_professor = '{$this->exigir_vinculo_turma_professor}'";
                $gruda = ', ';
            }

            if (dbBool($this->gerar_historico_transferencia)) {
                $set .= "{$gruda}gerar_historico_transferencia = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}gerar_historico_transferencia = false ";
                $gruda = ', ';
            }

            if (dbBool($this->controlar_posicao_historicos)) {
                $set .= "{$gruda}controlar_posicao_historicos = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}controlar_posicao_historicos = false ";
                $gruda = ', ';
            }

            if (dbBool($this->matricula_apenas_bairro_escola)) {
                $set .= "{$gruda}matricula_apenas_bairro_escola = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}matricula_apenas_bairro_escola = false ";
                $gruda = ', ';
            }

            if (dbBool($this->restringir_historico_escolar)) {
                $set .= "{$gruda}restringir_historico_escolar = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}restringir_historico_escolar = false ";
                $gruda = ', ';
            }
            if (strripos($this->coordenador_transporte, '-') and strripos($this->coordenador_transporte, '(')) {
                $this->coordenador_transporte = $this->parteString($this->coordenador_transporte, '-', '(');
            }

            if (is_string($this->coordenador_transporte)) {
                $set .= "{$gruda}coordenador_transporte = '{$this->coordenador_transporte}'";
                $gruda = ', ';
            }

            if (is_numeric($this->percentagem_maxima_ocupacao_salas) and !empty($this->percentagem_maxima_ocupacao_salas)) {
                $set .= "{$gruda}percentagem_maxima_ocupacao_salas = '{$this->percentagem_maxima_ocupacao_salas}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}percentagem_maxima_ocupacao_salas = null";
                $gruda = ', ';
            }

            if (is_numeric($this->quantidade_alunos_metro_quadrado) and !empty($this->quantidade_alunos_metro_quadrado)) {
                $set .= "{$gruda}quantidade_alunos_metro_quadrado = '{$this->quantidade_alunos_metro_quadrado}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}quantidade_alunos_metro_quadrado = null";
                $gruda = ', ';
            }

            if (dbBool($this->restringir_multiplas_enturmacoes)) {
                $set .= "{$gruda}restringir_multiplas_enturmacoes = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}restringir_multiplas_enturmacoes = false ";
                $gruda = ', ';
            }

            if (dbBool($this->permissao_filtro_abandono_transferencia)) {
                $set .= "{$gruda}permissao_filtro_abandono_transferencia = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}permissao_filtro_abandono_transferencia = false ";
                $gruda = ', ';
            }

            if (dbBool($this->multiplas_reserva_vaga)) {
                $set .= "{$gruda}multiplas_reserva_vaga = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}multiplas_reserva_vaga = false ";
                $gruda = ', ';
            }

            if (dbBool($this->permitir_carga_horaria)) {
                $set .= "{$gruda}permitir_carga_horaria = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}permitir_carga_horaria = false ";
                $gruda = ', ';
            }

            if (dbBool($this->reserva_integral_somente_com_renda)) {
                $set .= "{$gruda}reserva_integral_somente_com_renda = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}reserva_integral_somente_com_renda = false ";
                $gruda = ', ';
            }

            if (dbBool($this->componente_curricular_turma)) {
                $set .= "{$gruda}componente_curricular_turma = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}componente_curricular_turma = false ";
                $gruda = ', ';
            }

            if (dbBool($this->reprova_dependencia_ano_concluinte)) {
                $set .= "{$gruda}reprova_dependencia_ano_concluinte = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}reprova_dependencia_ano_concluinte = false ";
                $gruda = ', ';
            }

            if (dbBool($this->bloqueia_matricula_serie_nao_seguinte)) {
                $set .= "{$gruda}bloqueia_matricula_serie_nao_seguinte = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}bloqueia_matricula_serie_nao_seguinte = false ";
                $gruda = ', ';
            }

            if (is_string($this->data_base_matricula) and !empty($this->data_base_matricula)) {
                $set .= "{$gruda}data_base_matricula = '{$this->data_base_matricula}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}data_base_matricula = null ";
                $gruda = ', ';
            }

            if (is_string($this->data_fechamento) and !empty($this->data_fechamento)) {
                $set .= "{$gruda}data_fechamento = '{$this->data_fechamento}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}data_fechamento = null ";
                $gruda = ', ';
            }

            if (is_string($this->data_educacenso) and !empty($this->data_educacenso)) {
                $data_educacenso_pg = Portabilis_Date_Utils::brToPgSQL($this->data_educacenso);
                $set .= "{$gruda}data_educacenso = '{$data_educacenso_pg}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}data_educacenso = null ";
                $gruda = ', ';
            }

            if (is_string($this->orgao_regional) and !empty($this->orgao_regional)) {
                $set .= "{$gruda}orgao_regional = '{$this->orgao_regional}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}orgao_regional = null ";
                $gruda = ', ';
            }

            if ($this->exigir_dados_socioeconomicos) {
                $set .= "{$gruda}exigir_dados_socioeconomicos = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}exigir_dados_socioeconomicos = false ";
                $gruda = ', ';
            }

            if ($this->altera_atestado_para_declaracao) {
                $set .= "{$gruda}altera_atestado_para_declaracao = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}altera_atestado_para_declaracao = false ";
                $gruda = ', ';
            }

            if (dbBool($this->obrigar_campos_censo)) {
                $set .= "{$gruda}obrigar_campos_censo = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}obrigar_campos_censo = false ";
                $gruda = ', ';
            }

            if (dbBool($this->obrigar_documento_pessoa)) {
                $set .= "{$gruda}obrigar_documento_pessoa = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}obrigar_documento_pessoa = false ";
                $gruda = ', ';
            }

            if (dbBool($this->exigir_lancamentos_anteriores)) {
                $set .= "{$gruda}exigir_lancamentos_anteriores = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}exigir_lancamentos_anteriores = false ";
                $gruda = ', ';
            }

            if (dbBool($this->exibir_apenas_professores_alocados)) {
                $set .= "{$gruda}exibir_apenas_professores_alocados = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}exibir_apenas_professores_alocados = false ";
                $gruda = ', ';
            }

            if (dbBool($this->bloquear_vinculo_professor_sem_alocacao_escola)) {
                $set .= "{$gruda}bloquear_vinculo_professor_sem_alocacao_escola = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}bloquear_vinculo_professor_sem_alocacao_escola = false ";
                $gruda = ', ';
            }

            if (dbBool($this->permitir_matricula_fora_periodo_letivo)) {
                $set .= "{$gruda}permitir_matricula_fora_periodo_letivo = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}permitir_matricula_fora_periodo_letivo = false ";
                $gruda = ', ';
            }

            if (dbBool($this->ordenar_alunos_sequencial_enturmacao)) {
                $set .= "{$gruda}ordenar_alunos_sequencial_enturmacao = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}ordenar_alunos_sequencial_enturmacao = false ";
                $gruda = ', ';
            }

            if (dbBool($this->obrigar_telefone_pessoa)) {
                $set .= "{$gruda}obrigar_telefone_pessoa = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}obrigar_telefone_pessoa = false ";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_instituicao = '{$this->cod_instituicao}'");

                return true;
            }
        }

        return false;
    }

    public function parteString($string, $ponto_inicio, $ponto_final)
    {
        $num = 1;
        if ($string and $ponto_inicio) {
            $registro = explode($ponto_inicio, $string);
        }
        if ($ponto_final) {
            $num = 0;
            $registro = explode($ponto_final, $registro[1]);
        }

        return $registro[$num];
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function lista(
        $int_cod_instituicao = null,
        $str_ref_sigla_uf = null,
        $int_cep = null,
        $str_cidade = null,
        $str_bairro = null,
        $str_logradouro = null,
        $int_numero = null,
        $str_complemento = null,
        $str_nm_responsavel = null,
        $int_ddd_telefone = null,
        $int_telefone = null,
        $date_data_cadastro = null,
        $date_data_exclusao = null,
        $int_ativo = null,
        $str_nm_instituicao = null
    ) {
        $db = new clsBanco();
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_instituicao)) {
            $filtros .= "{$whereAnd} cod_instituicao = '{$int_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_ref_sigla_uf)) {
            $filtros .= "{$whereAnd} ref_sigla_uf LIKE '%{$str_ref_sigla_uf}%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cep)) {
            $filtros .= "{$whereAnd} cep = '{$int_cep}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_cidade)) {
            $filtros .= "{$whereAnd} cidade LIKE '%{$str_cidade}%'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_bairro)) {
            $filtros .= "{$whereAnd} bairro LIKE '%{$str_bairro}%'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_logradouro)) {
            $filtros .= "{$whereAnd} logradouro LIKE '%{$str_logradouro}%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_numero)) {
            $filtros .= "{$whereAnd} numero = '{$int_numero}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_complemento)) {
            $filtros .= "{$whereAnd} complemento LIKE '%{$str_complemento}%'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nm_responsavel)) {
            $filtros .= "{$whereAnd} nm_responsavel LIKE '%{$str_nm_responsavel}%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ddd_telefone)) {
            $filtros .= "{$whereAnd} ddd_telefone = '{$int_ddd_telefone}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_telefone)) {
            $filtros .= "{$whereAnd} telefone = '{$int_telefone}'";
            $whereAnd = ' AND ';
        }

        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nm_instituicao)) {
            $nm_instituicao = $db->escapeString($str_nm_instituicao);
            $filtros .= "{$whereAnd} nm_instituicao ILIKE '%{$nm_instituicao}%'";
            $whereAnd = ' AND ';
        }

        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function primeiraAtiva()
    {
        $instituicoes = $this->lista(
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
            true
        );

        return COUNT($instituicoes) ? $instituicoes[0] : null;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_instituicao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos},fcn_upper_nrm(nm_instituicao) as nm_instituicao_upper FROM {$this->_tabela} WHERE cod_instituicao = '{$this->cod_instituicao}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cod_instituicao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_instituicao = '{$this->cod_instituicao}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->cod_instituicao)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
