<?php
 
use iEducar\Legacy\Model;

class clsPmieducarNotaAluno extends Model
{
    public $cod_nota_aluno;
    public $ref_sequencial;
    public $ref_ref_cod_tipo_avaliacao;
    public $ref_cod_serie;
    public $ref_cod_escola;
    public $ref_cod_disciplina;
    public $ref_cod_matricula;
    public $ref_ref_cod_turma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $modulo;
    public $ref_cod_curso_disciplina;
    public $nota;
    public $etapa;

    public function __construct($cod_nota_aluno = null, $ref_sequencial = null, $ref_ref_cod_tipo_avaliacao = null, $ref_cod_serie = null, $ref_cod_escola = null, $ref_cod_disciplina = null, $ref_cod_matricula = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $modulo = null, $ref_cod_curso_disciplina = null, $nota = null, $etapa = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}nota_aluno";

        $this->_campos_lista = $this->_todos_campos = 'cod_nota_aluno, ref_sequencial, ref_ref_cod_tipo_avaliacao, ref_cod_serie, ref_cod_escola, ref_cod_disciplina, ref_cod_matricula, ref_usuario_exc, ref_usuario_cad, data_cadastro, data_exclusao, ativo, modulo, ref_cod_curso_disciplina, nota';

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_ref_cod_tipo_avaliacao) && is_numeric($ref_sequencial)) {
            $this->ref_ref_cod_tipo_avaliacao = $ref_ref_cod_tipo_avaliacao;
            $this->ref_sequencial = $ref_sequencial;
        }
        if (is_numeric($ref_cod_matricula)) {
            $this->ref_cod_matricula = $ref_cod_matricula;
        }
        if (is_numeric($ref_cod_curso_disciplina)) {
            $this->ref_cod_curso_disciplina = $ref_cod_curso_disciplina;
        }
        if (is_numeric($ref_cod_disciplina) && is_numeric($ref_cod_escola) && is_numeric($ref_cod_serie)) {
            $this->ref_cod_disciplina = $ref_cod_disciplina;
            $this->ref_cod_escola = $ref_cod_escola;
            $this->ref_cod_serie = $ref_cod_serie;
        }

        if (is_numeric($cod_nota_aluno)) {
            $this->cod_nota_aluno = $cod_nota_aluno;
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
        if (is_numeric($modulo)) {
            $this->modulo = $modulo;
        }
        if (is_numeric($nota)) {
            $this->nota = $nota;
        }
        if(is_numeric($etapa)){
            $this->etapa = $etapa;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_usuario_cad) && is_numeric($this->modulo)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_sequencial)) {
                $campos .= "{$gruda}ref_sequencial";
                $valores .= "{$gruda}'{$this->ref_sequencial}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_tipo_avaliacao)) {
                $campos .= "{$gruda}ref_ref_cod_tipo_avaliacao";
                $valores .= "{$gruda}'{$this->ref_ref_cod_tipo_avaliacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_serie)) {
                $campos .= "{$gruda}ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_disciplina)) {
                $campos .= "{$gruda}ref_cod_disciplina";
                $valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_matricula)) {
                $campos .= "{$gruda}ref_cod_matricula";
                $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->modulo)) {
                $campos .= "{$gruda}modulo";
                $valores .= "{$gruda}'{$this->modulo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_curso_disciplina)) {
                $campos .= "{$gruda}ref_cod_curso_disciplina";
                $valores .= "{$gruda}'{$this->ref_cod_curso_disciplina}'";
                $gruda = ', ';
            }
            if (is_numeric($this->nota)) {
                $campos .= "{$gruda}nota";
                $valores .= "{$gruda}'{$this->nota}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_nota_aluno_seq");
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->cod_nota_aluno) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_sequencial)) {
                $set .= "{$gruda}ref_sequencial = '{$this->ref_sequencial}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_tipo_avaliacao)) {
                $set .= "{$gruda}ref_ref_cod_tipo_avaliacao = '{$this->ref_ref_cod_tipo_avaliacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_serie)) {
                $set .= "{$gruda}ref_cod_serie = '{$this->ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_disciplina)) {
                $set .= "{$gruda}ref_cod_disciplina = '{$this->ref_cod_disciplina}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_matricula)) {
                $set .= "{$gruda}ref_cod_matricula = '{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
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
            if (is_numeric($this->modulo)) {
                $set .= "{$gruda}modulo = '{$this->modulo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_curso_disciplina)) {
                $set .= "{$gruda}ref_cod_curso_disciplina = '{$this->ref_cod_curso_disciplina}'";
                $gruda = ', ';
            }
            if (is_numeric($this->nota)) {
                $set .= "{$gruda}nota = '{$this->nota}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_nota_aluno = '{$this->cod_nota_aluno}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista($int_cod_nota_aluno = null, $int_ref_sequencial = null, $int_ref_ref_cod_tipo_avaliacao = null, $int_ref_cod_serie = null, $int_ref_cod_escola = null, $int_ref_cod_disciplina = null, $int_ref_cod_matricula = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_modulo = null, $int_ref_cod_curso_disciplina = null, $int_nota = null, $int_etapa = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_nota_aluno)) {
            $filtros .= "{$whereAnd} cod_nota_aluno = '{$int_cod_nota_aluno}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_sequencial)) {
            $filtros .= "{$whereAnd} ref_sequencial = '{$int_ref_sequencial}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_cod_tipo_avaliacao)) {
            $filtros .= "{$whereAnd} ref_ref_cod_tipo_avaliacao = '{$int_ref_ref_cod_tipo_avaliacao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_disciplina)) {
            $filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_modulo)) {
            $filtros .= "{$whereAnd} modulo = '{$int_modulo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_curso_disciplina)) {
            $filtros .= "{$whereAnd} ref_cod_curso_disciplina = '{$int_ref_cod_curso_disciplina}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_nota)) {
            $filtros .= "{$whereAnd} nota = '{$int_nota}'";
            $whereAnd = ' AND ';
        }
        if(is_numeric($int_etapa)){
            $filtros .= "{$whereAnd} etapa '{$int_etapa}'";
            $whereAnd = 'AND ';
        }

        $db = new clsBanco();
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

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_nota_aluno)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_nota_aluno = '{$this->cod_nota_aluno}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cod_nota_aluno)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_nota_aluno = '{$this->cod_nota_aluno}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->cod_nota_aluno) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * calcula a média do aluno $cod_matricula na disciplina $cod_disciplina
     *
     * @param int   $cod_matricula
     * @param int   $cod_disciplina
     * @param int   $qtd_modulos
     * @param float $media_sem_exame caso a media das notas esteja abaixo da media nao realiza arredondamento da media
     * @param float $media_com_exame caso a nota seja de exame deve ser informado true para que esta nota seja multiplicada por 2 conforme regras da instituicao
     *
     * @return float
     */
    public function getMediaAluno($cod_matricula, $cod_disciplina, $cod_serie, $qtd_modulos, $media_sem_exame = false, $media_com_exame = false)
    {
        if (is_numeric($cod_matricula) && is_numeric($cod_disciplina) && is_numeric($qtd_modulos) && $qtd_modulos && is_numeric($cod_serie) && $cod_serie) {
            $db = new clsBanco();
            /**
             * para calcular a nota do exame,
             * esta nota e multiplicada por 2
             * e dividido pela quantidade de
             * modulos da materia.. esta media
             * pode ser arredondada
             */
            $nota_exame = 0;

            if ($media_com_exame) {
                /**
                 * diminui em 1 o numero de modulos para
                 * o calculo do exame, uma vez que a nota do
                 * exame eh multiplicada por 2 ex: 4 modulos + 1 exame => 5 + 5.5 + 6 + 7 + (4 * 2) / 5 = nota exame
                 */

                $nota_exame = $db->CampoUnico("
                SELECT tav.valor * 2
                FROM pmieducar.nota_aluno na
                , pmieducar.tipo_avaliacao_valores tav
                WHERE na.ref_cod_matricula = '{$cod_matricula}'
                AND na.ref_cod_disciplina = '{$cod_disciplina}'
                AND na.ref_cod_serie = '{$cod_serie}'
                AND na.ativo = 1
                AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
                AND tav.sequencial = na.ref_sequencial
                AND na.modulo = '{$qtd_modulos}'
                ");

                /**
                 * diminiu em um no numero de modulos
                 * jah que a nota do exame eh multiplicada
                 * por 2 entao esta nota sera somada com as restantes
                 * e o calculo prossegue normalmente
                 */
                $qtd_modulos_sem_exame = $qtd_modulos - 1;
            } else {
                $qtd_modulos_sem_exame = $qtd_modulos;
            }

            $soma = $db->CampoUnico("
            SELECT SUM( tav.valor )
            FROM pmieducar.nota_aluno na
            , pmieducar.tipo_avaliacao_valores tav
            WHERE na.ref_cod_matricula = '{$cod_matricula}'
            AND na.ref_cod_disciplina = '{$cod_disciplina}'
            AND na.ref_cod_serie = '{$cod_serie}'
            AND na.ativo = 1
            AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
            AND tav.sequencial = na.ref_sequencial
            AND na.modulo <= '{$qtd_modulos_sem_exame}'
            GROUP BY ref_cod_disciplina
            ");
            /**
             * notas +  nota exame
             */
            if ($media_com_exame) {
                $soma += $nota_exame;
            }
            if ($soma !== false) {
                $tipo_avaliacao = $db->CampoUnico("SELECT ref_ref_cod_tipo_avaliacao FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$cod_matricula}' AND ref_ref_cod_tipo_avaliacao IS NOT NULL LIMIT 1");
                if ($media_com_exame) {
                    $media = $soma / ($qtd_modulos + 1);
                } else {
                    $media = $soma / $qtd_modulos;
                }
                /**
                 * @see    15-12-2006
                 * quando for dar as notas e for calcular a ultima
                 * ao fazer a media e essa nota estiver abaixo nao
                 * pode ser feito o arredondamento, somente se estiver
                 *  acima da media deixando o aluno em exame
                 *
                 * @author Haissam
                 *
                 */
                if ($media_sem_exame && !$media_com_exame/*nota com exame pode ser arredondada*/) {
                    if ($media < $media_sem_exame) {
                        return $media;
                    }
                }
                $objTipoAvaliacaoValores = new clsPmieducarTipoAvaliacaoValores();
                $objTipoAvaliacaoValores->setLimite(1);
                $objTipoAvaliacaoValores->setOrderby('valor DESC');
                $lista = $objTipoAvaliacaoValores->lista($tipo_avaliacao, null, null, null, $media, $media);
                if (is_array($lista)) {
                    foreach ($lista as $valor) {
                        return $valor['valor'];
                    }
                }
            }
        }

        return false;
    }

    public function getMediaAlunoExame($cod_matricula, $cod_disciplina, $cod_serie, $qtd_modulos)
    {
        if (is_numeric($cod_matricula) && is_numeric($cod_disciplina) && is_numeric($cod_serie) && is_numeric($qtd_modulos)) {
            $sqlNotas = "SELECT
                            SUM( tav.valor )
                        FROM
                            pmieducar.nota_aluno na
                            , pmieducar.tipo_avaliacao_valores tav
                        WHERE
                            na.ref_cod_matricula = '{$cod_matricula}'
                            AND na.ref_cod_disciplina = '{$cod_disciplina}'
                            AND na.ref_cod_serie = '{$cod_serie}'
                            AND na.ativo = 1
                            AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
                            AND tav.sequencial = na.ref_sequencial
                            AND na.modulo <= {$qtd_modulos}";
            $sqlExame = "SELECT
                            na.nota * 2
                        FROM
                            pmieducar.nota_aluno na
                        WHERE
                            na.ref_cod_matricula = '{$cod_matricula}'
                            AND na.ref_cod_disciplina = '{$cod_disciplina}'
                            AND na.ref_cod_serie = '{$cod_serie}'
                            AND na.ativo = 1
                            AND na.modulo = {$qtd_modulos} + 1";
            $db = new clsBanco();
            $somaNotas = $db->CampoUnico($sqlNotas);
            $notaExame = $db->CampoUnico($sqlExame);
            $media = ($somaNotas + $notaExame) / ($qtd_modulos + 2);
            $tipo_avaliacao = $db->CampoUnico("SELECT ref_ref_cod_tipo_avaliacao FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$cod_matricula}' AND ref_ref_cod_tipo_avaliacao IS NOT NULL ORDER BY modulo LIMIT 1");
            if (is_numeric($tipo_avaliacao)) {
                $objTipoAvaliacaoValores = new clsPmieducarTipoAvaliacaoValores();
                $objTipoAvaliacaoValores->setLimite(1);
                $objTipoAvaliacaoValores->setOrderby('valor DESC');
                $lista = $objTipoAvaliacaoValores->lista($tipo_avaliacao, null, null, null, $media, $media);
                $lista = array_shift($lista);

                return $lista['valor'];
            }
        }

        return false;
    }

    /**
     * calcula a média especial do aluno $cod_matricula na disciplina $cod_disciplina
     * calculo = (quantidade de disciplinas acima da media / quantidades de disciplinas) * 10 ) tem que ser maior que a media
     * se for maior o aluno esta aprovado
     *
     * @param int $cod_matricula
     * @param int $cod_disciplina
     *
     * @return boolean
     */
    public function getMediaEspecialAluno($cod_matricula, $cod_serie, $cod_escola, $qtd_modulos, $media_curso_sem_exame)
    {
        if (is_numeric($cod_matricula) && is_numeric($cod_escola) && $cod_escola && is_numeric($qtd_modulos) && $qtd_modulos && is_numeric($cod_serie) && $cod_serie && is_numeric($media_curso_sem_exame)) {
            $db = new clsBanco();

            $objEscolaSerieDisciplina = new clsPmieducarEscolaSerieDisciplina();
            $listaEscolaSerieDisciplina = $objEscolaSerieDisciplina->lista($cod_serie, $cod_escola, null, 1);

            $disciplinas_acima_media = 0;
            $total_disciplinas = count($listaEscolaSerieDisciplina);
            if ($listaEscolaSerieDisciplina) {
                foreach ($listaEscolaSerieDisciplina as $key => $disciplina) {
                    $objNotaAluno = new clsPmieducarNotaAluno();
                    $media = $objNotaAluno->getMediaAluno($cod_matricula, $disciplina['ref_cod_disciplina'], $disciplina['ref_ref_cod_serie'], $qtd_modulos);
                    if ($media >= $media_curso_sem_exame) {
                        //media acima da media incrementa o numero de disciplinas acima da media
                        $disciplinas_acima_media++;
                    }
                }

                $media_final = ($disciplinas_acima_media / $total_disciplinas) * 10;

                $tipo_avaliacao = $db->CampoUnico("SELECT ref_ref_cod_tipo_avaliacao FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$cod_matricula}' LIMIT 1");
                $objTipoAvaliacaoValores = new clsPmieducarTipoAvaliacaoValores();
                $objTipoAvaliacaoValores->setLimite(1);
                $objTipoAvaliacaoValores->setOrderby('valor DESC');
                if ($media_final) {
                    $lista = $objTipoAvaliacaoValores->lista($tipo_avaliacao, null, null, null, $media_final, $media_final);
                    if (is_array($lista)) {
                        foreach ($lista as $valor) {
                            return $valor['valor'];
                        }
                    }
                }

                return false;
            }

            return false;
        }
    }

    /**
     * retorna a quantidade de disciplinas que a matricula $cod_matricula pegou exame
     *
     * @param int   $cod_matricula
     * @param int   $qtd_modulos_normais
     * @param float $media
     *
     * return int
     *
     */
    public function getQtdMateriasExame($cod_matricula, $qtd_modulos_normais, $media, $nao_arredondar_nota = false)
    {
        $exames = 0;
        if (is_numeric($cod_matricula) && is_numeric($qtd_modulos_normais) && is_numeric($media)) {
            $medias = $this->getMediasAluno($cod_matricula, $qtd_modulos_normais, $nao_arredondar_nota);

            if (is_array($medias)) {
                foreach ($medias as $value) {
                    if ($value['media'] < $media) {
                        $exames++;
                    }
                }
            }
        }

        return $exames;
    }

    /**
     * retorna a quantidade de disciplinas que a matricula $cod_matricula ja recebeu nota no exame
     *
     * @param int $cod_matricula
     * @param int $qtd_modulos_normais
     *
     * return int
     *
     */
    public function getQtdNotasExame($cod_matricula, $qtd_modulos_normais)
    {
        $db = new clsBanco();

        return $db->CampoUnico("SELECT COUNT(0) FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$cod_matricula}' AND ativo = 1 AND modulo > '{$qtd_modulos_normais}'");
    }

    /**
     * calcula as médias do aluno $cod_matricula em todas as disciplinas, encontra
     * os que estão abaixo da média ($media) e retorna as disciplinas
     *
     * @param int $cod_matricula
     * @param int $qtd_modulos
     * @param int $media
     *
     * @return array
     */
    public function getDisciplinasExameDoAluno($cod_matricula, $qtd_modulos_normais, $media, $nao_arredondar_nota = false)
    {
        $exames = [];
        if (is_numeric($cod_matricula) && is_numeric($qtd_modulos_normais) && is_numeric($media)) {
            $medias = $this->getMediasAluno($cod_matricula, $qtd_modulos_normais, $arredondar_nota);
            if (is_array($medias)) {
                foreach ($medias as $value) {
                    if ($value['media'] < $media) {
                        $exames[] = ['cod_disciplina' => $value['cod_disciplina'], 'cod_serie' => $value['cod_serie']];
                    }
                }
            }
        }

        return $exames;
    }

    /**
     * calcula as médias do aluno $cod_matricula em todas as disciplinas
     *
     * @param int $cod_matricula
     * @param int $qtd_modulos
     *
     * @return array
     */
    public function getMediasAluno($cod_matricula, $qtd_modulos, $nao_arredondar_nota = false)
    {
        $retorno = [];
        if (is_numeric($cod_matricula) && is_numeric($qtd_modulos) && $qtd_modulos) {
            $i = 0;

            $db = new clsBanco();
            $db2 = new clsBanco();
            $db->Consulta("
            SELECT na.ref_cod_disciplina, na.ref_cod_serie, SUM( tav.valor )
            FROM pmieducar.nota_aluno na
            , pmieducar.tipo_avaliacao_valores tav
            WHERE na.ref_cod_matricula = '{$cod_matricula}'
            AND na.ativo = 1
            AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
            AND tav.sequencial = na.ref_sequencial
            AND na.modulo <= '{$qtd_modulos}'
            GROUP BY ref_cod_disciplina, ref_cod_serie
            ");
            while ($db->ProximoRegistro()) {
                list($cod_disciplina, $cod_serie, $soma) = $db->Tupla();
                $retorno[$i]['cod_disciplina'] = $cod_disciplina;
                $retorno[$i]['cod_serie'] = $cod_serie;

                $tipo_avaliacao = $db2->CampoUnico("SELECT ref_ref_cod_tipo_avaliacao FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$cod_matricula}' AND ref_ref_cod_tipo_avaliacao IS NOT NULL LIMIT 1");

                $media = $soma / $qtd_modulos;
                if (!$nao_arredondar_nota) {
                    $objTipoAvaliacaoValores = new clsPmieducarTipoAvaliacaoValores();
                    $objTipoAvaliacaoValores->setLimite(1);
                    $objTipoAvaliacaoValores->setOrderby('valor DESC');
                    $lista = $objTipoAvaliacaoValores->lista($tipo_avaliacao, null, null, null, $media, $media);
                    foreach ($lista as $valor) {
                        $media_valor = $valor['valor'];
                    }
                } else {
                    $media_valor = $media;
                }
                $retorno[$i]['media'] = $media_valor;
                $i++;
            }
        }

        return $retorno;
    }

    /**
     * calcula as médias dos alunos da turma $cod_turma em todas as disciplinas, encontra
     * os que estão abaixo da média ($media) e retorna as matriculas
     *
     * @param int $cod_turma
     * @param int $qtd_modulos
     * @param int $media
     *
     * @return array
     */
    public function getAlunosExame($cod_turma, $qtd_modulos, $media, $ref_cod_disciplina = null)
    {
        $retorno = [];
        if (is_numeric($cod_turma) && is_numeric($qtd_modulos) && $qtd_modulos) {
            if (is_numeric($ref_cod_disciplina)) {
                $disciplina_exame = " AND na.ref_cod_disciplina = '{$ref_cod_disciplina}' ";
            }

            $db = new clsBanco();
            $db->Consulta("
            SELECT ref_cod_matricula, ref_cod_disciplina, total_notas
            FROM
            (
                SELECT na.ref_cod_matricula
                , na.ref_cod_disciplina
                , SUM( tav.valor ) AS total_notas
                , COUNT(0) AS qtd_modulos
                , ( SELECT me.permite_exame FROM pmieducar.matricula_excessao me WHERE me.ref_cod_matricula = na.ref_cod_matricula AND me.ref_cod_disciplina = na.ref_cod_disciplina) AS permite_exame
                FROM pmieducar.nota_aluno na
                , pmieducar.tipo_avaliacao_valores tav
                , pmieducar.v_matricula_matricula_turma mmt
                WHERE na.ref_cod_matricula = mmt.cod_matricula
                AND mmt.ref_cod_turma = '{$cod_turma}'
                AND na.ativo = 1
                AND mmt.ativo = 1
                AND mmt.aprovado = 3
                AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
                AND tav.sequencial = na.ref_sequencial
                $disciplina_exame
                GROUP BY na.ref_cod_disciplina, na.ref_cod_matricula
            ) AS sub
            WHERE qtd_modulos = '{$qtd_modulos}'
            AND ( permite_exame = TRUE OR permite_exame IS NULL )
            ");
            while ($db->ProximoRegistro()) {
                list($cod_matricula, $cod_disciplina, $soma) = $db->Tupla();
                if (!isset($retorno[$cod_matricula])) {
                    if ($soma / $qtd_modulos < $media) {
                        $retorno[$cod_matricula] = $cod_matricula;
                    }
                }
            }
        }

        return $retorno;
    }

    /**
     * calcula as médias dos alunos da turma $cod_turma em todas as disciplinas, encontra
     * os que estão abaixo da média ($media) e retorna as disciplinas
     *
     * @param int $cod_turma
     * @param int $qtd_modulos
     * @param int $media
     *
     * @return array
     */
    public function getDisciplinasExame($cod_turma, $qtd_modulos, $media, $verifica_aluno_possui_nota = false)
    {
        $retorno = [];
        if (is_numeric($cod_turma) && is_numeric($qtd_modulos) && $qtd_modulos) {
            $db = new clsBanco();
            $db->Consulta("
            SELECT ref_cod_matricula,  ref_cod_disciplina, soma, ref_cod_serie
            FROM
            (
                SELECT na.ref_cod_matricula
                , na.ref_cod_disciplina
                , SUM( tav.valor ) AS soma
                , na.ref_cod_serie
                , COUNT(tav.valor) AS qtd_notas
                , ( SELECT me.permite_exame FROM pmieducar.matricula_excessao me WHERE me.ref_cod_matricula = na.ref_cod_matricula AND me.ref_cod_disciplina = na.ref_cod_disciplina) AS permite_exame
                FROM pmieducar.nota_aluno na
                , pmieducar.tipo_avaliacao_valores tav
                , pmieducar.v_matricula_matricula_turma mmt
                WHERE na.ref_cod_matricula = mmt.cod_matricula
                AND mmt.ref_cod_turma = '{$cod_turma}'
                AND na.ativo = 1
                AND mmt.ativo = 1
                AND mmt.aprovado = 3
                AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
                AND tav.sequencial = na.ref_sequencial
                GROUP BY na.ref_cod_disciplina, na.ref_cod_matricula, na.ref_cod_serie
            ) AS sub1
            WHERE qtd_notas = '{$qtd_modulos}'
            AND ( permite_exame = TRUE OR permite_exame IS NULL )
            ");
            while ($db->ProximoRegistro()) {
                list($cod_matricula, $cod_disciplina, $soma, $cod_serie) = $db->Tupla();
                if (!isset($retorno["{$cod_serie}_{$cod_disciplina}"])) {
                    if ($verifica_aluno_possui_nota) {
                        $obj_nota_aluno = new clsPmieducarNotaAluno();
                        $lst_nota_aluno = $obj_nota_aluno->lista(null, null, null, null, null, $cod_disciplina, $cod_matricula, null, null, null, null, null, null, 1, $qtd_modulos + 1);
                        if (!$lst_nota_aluno) {
                            if ($soma / $qtd_modulos < $media) {
                                $retorno["{$cod_serie}_{$cod_disciplina}"] = ['cod_serie' => $cod_serie, 'cod_disciplina' => $cod_disciplina];
                            }
                        }
                    } else {
                        if ($soma / $qtd_modulos < $media) {
                            $retorno["{$cod_serie}_{$cod_disciplina}"] = ['cod_serie' => $cod_serie, 'cod_disciplina' => $cod_disciplina];
                        }
                    }
                }
            }
        }

        return $retorno;
    }

    /**
     * calcula as médias dos alunos da turma $cod_turma em uma disciplina especifica $cod_disciplina, encontra
     * os que estão abaixo da média ($media) e retorna as matriculas
     *
     * @param int $cod_turma
     * @param int $cod_disciplina
     * @param int $qtd_modulos
     * @param int $media
     *
     * @return array
     */
    public function getAlunosDisciplinaExame($cod_turma, $cod_disciplina, $qtd_modulos, $media)
    {
        $retorno = [];
        if (is_numeric($cod_turma) && is_numeric($qtd_modulos) && $qtd_modulos) {
            $db = new clsBanco();
            $db->Consulta("
            SELECT ref_cod_matricula, soma
            FROM
            (
                SELECT na.ref_cod_matricula
                , SUM( tav.valor ) AS soma
                , COUNT(tav.valor) AS qtd_notas
                , ( SELECT me.permite_exame FROM pmieducar.matricula_excessao me WHERE me.ref_cod_matricula = na.ref_cod_matricula AND me.ref_cod_disciplina = na.ref_cod_disciplina) AS permite_exame
                FROM pmieducar.nota_aluno na
                , pmieducar.tipo_avaliacao_valores tav
                , pmieducar.v_matricula_matricula_turma mmt
                WHERE na.ref_cod_matricula = mmt.cod_matricula
                AND na.ref_cod_disciplina = '{$cod_disciplina}'
                AND mmt.ref_cod_turma = '{$cod_turma}'
                AND na.ativo = 1
                AND mmt.ativo = 1
                AND mmt.aprovado = 3
                AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
                AND tav.sequencial = na.ref_sequencial
                GROUP BY na.ref_cod_matricula
            ) AS sub1
            WHERE qtd_notas = '{$qtd_modulos}'
            AND ( permite_exame = TRUE OR permite_exame IS NULL )
            ");
            while ($db->ProximoRegistro()) {
                list($cod_matricula, $soma) = $db->Tupla();
                if (!isset($retorno[$cod_matricula])) {
                    if ($soma / $qtd_modulos < $media) {
                        $retorno[$cod_matricula] = $cod_matricula;
                    }
                }
            }
        }

        return $retorno;
    }

    /**
     * Retorna uma variável com o resultado
     *
     * @return int
     */
    public function retornaDiscMod($int_ref_ref_cod_serie = null, $int_ref_ref_cod_escola = null, $int_cod_disciplina = null, $int_ref_ref_cod_turma = null, $int_ref_cod_turma = null, $int_ref_cod_matricula = null, $conta = false, $int_modulos = null)
    {
        if (is_numeric($int_ref_ref_cod_serie) && is_numeric($int_ref_ref_cod_escola) && is_numeric($int_cod_disciplina) && is_numeric($int_ref_ref_cod_turma) && is_numeric($int_ref_cod_turma) && is_numeric($int_modulos)) {
            $db = new clsBanco();

            if ($conta) {
                $sql = "SELECT MIN( ( SELECT DISTINCT CASE WHEN ( SELECT 1
                                                           FROM pmieducar.dispensa_disciplina dd
                                                          WHERE dd.ref_ref_cod_turma           = na.ref_ref_cod_turma
                                                            AND dd.ref_cod_matricula       = na.ref_cod_matricula
                                                            AND dd.disc_ref_ref_cod_turma      = na.disc_ref_cod_turma
                                                            AND dd.disc_ref_ref_cod_serie      = na.disc_ref_ref_cod_serie
                                                            AND dd.disc_ref_ref_cod_escola     = na.disc_ref_ref_cod_escola
                                                            AND dd.disc_ref_ref_cod_disciplina = na.disc_ref_ref_cod_disciplina ) = 1
                                                  THEN {$int_modulos}
                                                  ELSE
                                                    ( SELECT COUNT(0)
                                                        FROM pmieducar.nota_aluno n
                                                       WHERE n.ref_cod_matricula       = na.ref_cod_matricula
                                                         AND n.disc_ref_cod_turma          = na.disc_ref_cod_turma
                                                         AND n.ref_ref_cod_turma           = na.ref_ref_cod_turma
                                                         AND n.disc_ref_ref_cod_serie      = na.disc_ref_ref_cod_serie
                                                         AND n.disc_ref_ref_cod_escola     = na.disc_ref_ref_cod_escola
                                                         AND n.ativo                       = 1
                                                         AND n.disc_ref_ref_cod_disciplina = na.disc_ref_ref_cod_disciplina )
                                                  END
                                        FROM pmieducar.nota_aluno na
                                       WHERE na.ref_cod_matricula       = mt.ref_cod_matricula
                                         AND na.disc_ref_cod_turma          = mt.ref_cod_turma
                                         AND na.ref_ref_cod_turma           = mt.ref_cod_turma
                                         AND na.disc_ref_ref_cod_serie      = m.ref_ref_cod_serie
                                         AND na.disc_ref_ref_cod_escola     = m.ref_ref_cod_escola
                                         AND na.ativo                       = 1
                                         AND na.disc_ref_ref_cod_disciplina = {$int_cod_disciplina} ) )
                          FROM pmieducar.matricula       m,
                               pmieducar.matricula_turma mt
                         WHERE mt.ref_cod_matricula = m.cod_matricula
                           AND mt.ref_cod_turma     = {$int_ref_cod_turma}
                           AND m.ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
                           AND m.ref_ref_cod_escola = {$int_ref_ref_cod_escola}
                           AND m.ativo              = 1";
            } else {
                $sql = "SELECT MIN( qtd )
                          FROM ( SELECT DISTINCT COUNT( na.cod_nota_aluno ) AS qtd
                                   FROM pmieducar.nota_aluno na
                                  WHERE na.disc_ref_ref_cod_serie      = {$int_ref_ref_cod_serie}
                                    AND na.disc_ref_ref_cod_escola     = {$int_ref_ref_cod_escola}
                                    AND na.disc_ref_cod_turma          = {$int_ref_ref_cod_turma}
                                    AND na.ref_ref_cod_turma           = {$int_ref_cod_turma}
                                    AND na.ativo                       = 1
                                    AND na.disc_ref_ref_cod_disciplina = {$int_cod_disciplina}
                                    AND na.disc_ref_ref_cod_disciplina NOT IN ( SELECT dd.disc_ref_ref_cod_disciplina
                                                                                  FROM pmieducar.dispensa_disciplina dd
                                                                                 WHERE dd.ref_ref_cod_turma           = na.ref_ref_cod_turma
                                                                                   AND dd.ref_cod_matricula       = na.ref_cod_matricula
                                                                                   AND dd.disc_ref_ref_cod_turma      = na.disc_ref_cod_turma
                                                                                   AND dd.disc_ref_ref_cod_serie      = na.disc_ref_ref_cod_serie
                                                                                   AND dd.disc_ref_ref_cod_escola     = na.disc_ref_ref_cod_escola
                                                                                   AND dd.disc_ref_ref_cod_disciplina = na.disc_ref_ref_cod_disciplina )";

                if (is_numeric($int_ref_cod_matricula)) {
                    $sql .= " AND ref_cod_matricula = {$int_ref_cod_matricula}";
                }

                $sql .= ' GROUP BY ref_cod_matricula ) AS subquery';
            }

            return $db->CampoUnico($sql);
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function todasNotas($int_disc_ref_ref_cod_serie = null, $int_disc_ref_ref_cod_escola = null, $int_disc_ref_cod_turma = null, $int_ref_ref_cod_turma = null, $int_qtd_modulos = null, $int_ref_cod_matricula = null)
    {
        $db = new clsBanco();

        $sql = "SELECT CASE WHEN ( ( SELECT ( COUNT( * ) - ( SELECT COUNT( * )
                                                               FROM pmieducar.dispensa_disciplina
                                                              WHERE ref_ref_cod_turma       = {$int_ref_ref_cod_turma}
                                                                AND ref_cod_matricula   = {$int_ref_cod_matricula}
                                                                AND disc_ref_ref_cod_turma  = {$int_disc_ref_cod_turma}
                                                                AND disc_ref_ref_cod_serie  = {$int_disc_ref_ref_cod_serie}
                                                                AND disc_ref_ref_cod_escola = {$int_disc_ref_ref_cod_escola} ) ) * {$int_qtd_modulos}
                                       FROM pmieducar.turma_disciplina
                                      WHERE ref_cod_turma  = {$int_disc_ref_cod_turma}
                                        AND ref_cod_escola = {$int_disc_ref_ref_cod_escola}
                                        AND ref_cod_serie  = {$int_disc_ref_ref_cod_serie} ) <= ( SELECT COUNT( * )
                                                                                                    FROM pmieducar.nota_aluno
                                                                                                   WHERE disc_ref_ref_cod_serie  = {$int_disc_ref_ref_cod_serie}
                                                                                                     AND disc_ref_ref_cod_escola = {$int_disc_ref_ref_cod_escola}
                                                                                                     AND disc_ref_cod_turma      = {$int_disc_ref_cod_turma}
                                                                                                     AND ref_ref_cod_turma       = {$int_ref_ref_cod_turma}
                                                                                                     AND ref_cod_matricula   = {$int_ref_cod_matricula} ) ) THEN 'S'
                        ELSE 'N'
                         END AS terminou";

        return $db->CampoUnico($sql);
    }

public function notas($ref_cod_matricula)
    {
   $sql = "
   SELECT 
   cc.nome, 
   STRING_AGG (ncc.nota_arredondada::character varying, ',' ORDER BY ncc.etapa ASC ) AS Notas

    FROM pmieducar.matricula AS m

    JOIN modules.nota_aluno AS na
        ON na.matricula_id = m.cod_matricula

    JOIN modules.nota_componente_curricular AS ncc
        ON ncc.nota_aluno_id = na.id

    JOIN modules.componente_curricular AS cc
        ON ncc.componente_curricular_id = cc.id
";

$whereAnd = 'WHERE';
$join = '';
$filtros = '';

    $filtros .= "{$whereAnd} cod_matricula = '{$ref_cod_matricula}' ";
    $whereAnd = "AND ";
$db = new clsBanco();
$countCampos = count(explode(',', " notas"));
$resultado = [];

$sql .= $filtros ."GROUP BY cc.nome". $this->getOrderby() . $this->getLimite();

$this->_total = $db->CampoUnico("SELECT COUNT(0) FROM pmieducar.matricula AS m

JOIN modules.nota_aluno AS na
	ON na.matricula_id = m.cod_matricula

JOIN modules.nota_componente_curricular AS ncc
	ON ncc.nota_aluno_id = na.id

JOIN modules.componente_curricular AS cc
	ON ncc.componente_curricular_id = cc.id

{$filtros}
GROUP BY cc.nome");


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
        $resultado[] = $tupla;
    }
}

if (count($resultado)) {
    return $resultado;
   
}

return false;


}

    /**
     * Retorna uma variável com o resultado
     *
     * @return int
     */
    public function retornaDiscNota($int_ref_ref_cod_serie, $int_ref_ref_cod_escola, $int_ref_ref_cod_turma, $int_ref_cod_turma, $int_ref_cod_matricula, $int_num_modulo)
    {
        $db = new clsBanco();
        $sql = "SELECT count( cod_nota_aluno )
                  FROM pmieducar.nota_aluno
                 WHERE ref_cod_serie     = {$int_ref_ref_cod_serie}
                   AND ref_cod_escola    = {$int_ref_ref_cod_escola}
                   AND ativo             = 1
                   AND ref_cod_matricula = {$int_ref_cod_matricula}";

        $qtd_nota = $db->CampoUnico($sql);

        $sql = "SELECT ( ( SELECT COUNT(*)
                             FROM pmieducar.disciplina_serie
                            WHERE ref_cod_serie = {$int_ref_ref_cod_serie} ) - ( SELECT COUNT(*)
                                                                                   FROM pmieducar.dispensa_disciplina
                                                                                  WHERE ref_cod_matricula = {$int_ref_cod_matricula}
                                                                                    AND ref_cod_serie     = {$int_ref_ref_cod_serie}
                                                                                    AND ref_cod_escola    = {$int_ref_ref_cod_escola} ) )";

        $qtd_disc = $db->CampoUnico($sql);

        return (($int_num_modulo > 1) ? ($qtd_nota - (($int_num_modulo - 1) * $qtd_disc)) : ($qtd_nota));
    }

    /**
     * Total de notas de uma turma
     *
     * @return int
     */
    public function retornaTotalNotas($int_ref_ref_cod_serie, $int_ref_ref_cod_escola, $int_ref_ref_cod_turma, $int_ref_cod_turma)
    {
        $db = new clsBanco();
        $sql = "SELECT COUNT(0)
                  FROM pmieducar.nota_aluno
                 WHERE disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
                   AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola}
                   AND disc_ref_cod_turma      = {$int_ref_ref_cod_turma}
                   AND ref_ref_cod_turma       = {$int_ref_cod_turma}";

        $qtd_nota = $db->CampoUnico($sql);

        return $qtd_nota;
    }

    /**
     * Total de notas de uma turma
     *
     * @return int
     */
    public function retornaModuloAluno($int_ref_cod_serie, $int_ref_cod_escola, $int_ref_cod_matricula)
    {
        if (is_numeric($int_ref_cod_serie) && is_numeric($int_ref_cod_escola) && is_numeric($int_ref_cod_matricula)) {
            $db = new clsBanco();

            $sql = "
                SELECT COALESCE( MIN(total), 0 ) FROM
                (
                    SELECT COUNT(0) AS total
                    FROM pmieducar.nota_aluno
                    WHERE ref_cod_serie             = '{$int_ref_cod_serie}'
                    AND ref_cod_escola              = '{$int_ref_cod_escola}'
                    AND ref_cod_matricula           = '{$int_ref_cod_matricula}'
                    AND ativo                       = 1
                    GROUP BY ref_cod_disciplina
                ) AS sub1
            ";

            $qtd_nota = $db->CampoUnico($sql);

            return $qtd_nota;
        }

        return false;
    }

    /**
     * Total de notas do aluno em determinada disciplina
     *
     * @return int
     */
    public function getQtdNotas($int_ref_cod_escola = null, $int_ref_cod_serie = null, $int_ref_cod_disciplina = null, $int_ref_cod_matricula = null, $int_ref_cod_curso_disciplina = null)
    {
        if (is_numeric($int_ref_cod_matricula)) {
            $db = new clsBanco();
            $sql = "SELECT COUNT(cod_nota_aluno)
                    FROM pmieducar.nota_aluno
                    WHERE ref_cod_matricula = '{$int_ref_cod_matricula}'
                    AND ativo = 1";

            if ($int_ref_cod_disciplina) {
                $sql .= " AND ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
            }

            if ($int_ref_cod_escola) {
                $sql .= " AND ref_cod_escola = '{$int_ref_cod_escola}'";
            }

            if ($int_ref_cod_serie) {
                $sql .= " AND ref_cod_serie = '{$int_ref_cod_serie}'";
            }

            if ($int_ref_cod_curso_disciplina) {
                $sql .= " AND ref_cod_curso_disciplina = '{$int_ref_cod_curso_disciplina}'";
            }

            $qtd_nota = $db->CampoUnico($sql);

            return $qtd_nota;
        }

        return false;
    }

    /**
     * Maximo de notas em uma matricula
     *
     * @return int
     */
    public function getMaxNotas($int_ref_cod_matricula)
    {
        if (is_numeric($int_ref_cod_matricula)) {
            $db = new clsBanco();
            $sql = "SELECT
                        max(modulo)
                    FROM
                        pmieducar.nota_aluno
                    WHERE
                        ref_cod_matricula = '{$int_ref_cod_matricula}'
                        AND ativo = 1";

            $max_nota = $db->CampoUnico($sql);

            return $max_nota;
        }

        return false;
    }

    /**
     * Funcao que retorna a ultima nota do modulo para as series que
     * a ultima nota define a situacao do aluno
     *
     * @param int $cod_matricula
     * @param int $cod_disciplina
     * @param int $cod_serie
     * @param int $ultimo_modulo
     *
     * @return int
     */
    public function getUltimaNotaModulo($cod_matricula, $cod_disciplina, $cod_serie, $ultimo_modulo)
    {
        if (is_numeric($cod_matricula) && is_numeric($cod_disciplina) && is_numeric($cod_serie) && is_numeric($ultimo_modulo)) {
            $sql = "SELECT tav.valor
                    FROM pmieducar.nota_aluno na
                    , pmieducar.tipo_avaliacao_valores tav
                    WHERE na.ref_cod_matricula = '{$cod_matricula}'
                    AND na.ref_cod_disciplina = '{$cod_disciplina}'
                    AND na.ref_cod_serie = '{$cod_serie}'
                    AND na.ativo = 1
                    AND tav.ref_cod_tipo_avaliacao = na.ref_ref_cod_tipo_avaliacao
                    AND tav.sequencial = na.ref_sequencial
                    AND na.modulo = '{$ultimo_modulo}'";
            $db = new clsBanco();

            return $db->CampoUnico($sql);
        }

        return false;
    }
}
