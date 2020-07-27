<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarServidor extends Model
{
    public $cod_servidor;
    public $ref_idesco = false;
    public $carga_horaria;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;
    public $ref_cod_subnivel;
    public $pos_graduacao;
    public $curso_formacao_continuada;
    public $multi_seriado;
    public $tipo_ensino_medio_cursado;
    public $_campos_lista2;
    public $_todos_campos2;

    public function __construct(
        $cod_servidor = null,
        $ref_cod_deficiencia = null,
        $ref_idesco = null,
        $carga_horaria = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null,
        $ref_cod_instituicao = null,
        $ref_cod_subnivel = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'servidor';
        $this->_campos_lista = $this->_todos_campos = 'cod_servidor, ref_idesco, carga_horaria, data_cadastro, data_exclusao, ativo, ref_cod_instituicao,ref_cod_subnivel,
    pos_graduacao, curso_formacao_continuada, multi_seriado, tipo_ensino_medio_cursado
    ';
        $this->_campos_lista2 = $this->_todos_campos2 = 's.cod_servidor, s.ref_idesco, s.carga_horaria, s.data_cadastro, s.data_exclusao, s.ativo, s.ref_cod_instituicao,s.ref_cod_subnivel,
    s.pos_graduacao, s.curso_formacao_continuada, s.multi_seriado, s.tipo_ensino_medio_cursado,
    (SELECT replace(textcat_all(matricula),\' <br>\',\',\')
          FROM pmieducar.servidor_funcao sf
         WHERE s.cod_servidor = sf.ref_cod_servidor) as matricula_servidor
    ';
        $this->ref_idesco = $ref_idesco;

        /**
         * Filtrar cod_servidor
         */
        if (is_numeric($cod_servidor)) {
            $this->cod_servidor = $cod_servidor;
        }
        if (is_numeric($carga_horaria)) {
            $this->carga_horaria = $carga_horaria;
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
        if (is_numeric($ref_cod_instituicao)) {
            $this->ref_cod_instituicao = $ref_cod_instituicao;
        }
        if (is_numeric($ref_cod_subnivel)) {
            $this->ref_cod_subnivel = $ref_cod_subnivel;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->cod_servidor) && is_numeric($this->carga_horaria) &&
            is_numeric($this->ref_cod_instituicao)
        ) {
            $db = new clsBanco();
            $campos = '';
            $valores = '';
            $gruda = '';
            if (is_numeric($this->cod_servidor)) {
                $campos .= "{$gruda}cod_servidor";
                $valores .= "{$gruda}'{$this->cod_servidor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->carga_horaria)) {
                $campos .= "{$gruda}carga_horaria";
                $valores .= "{$gruda}'{$this->carga_horaria}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_subnivel)) {
                $campos .= "{$gruda}ref_cod_subnivel";
                $valores .= "{$gruda}'{$this->ref_cod_subnivel}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idesco)) {
                $campos .= "{$gruda}ref_idesco";
                $valores .= "{$gruda}'{$this->ref_idesco}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';
            if (is_numeric($this->ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->tipo_ensino_medio_cursado)) {
                $campos .= "{$gruda}tipo_ensino_medio_cursado";
                $valores .= "{$gruda}'{$this->tipo_ensino_medio_cursado}'";
                $gruda = ', ';
            }
            if (is_string($this->pos_graduacao)) {
                $campos .= "{$gruda}pos_graduacao";
                $valores .= "{$gruda}'{$this->pos_graduacao}'";
                $gruda = ', ';
            }
            if (is_string($this->curso_formacao_continuada)) {
                $campos .= "{$gruda}curso_formacao_continuada";
                $valores .= "{$gruda}'{$this->curso_formacao_continuada}'";
                $gruda = ', ';
            }
            if (dbBool($this->multi_seriado)) {
                $campos .= "{$gruda}multi_seriado";
                $valores .= "{$gruda} TRUE ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}multi_seriado";
                $valores .= "{$gruda} FALSE ";
                $gruda = ', ';
            }
            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");

            return $this->cod_servidor;
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
        if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
            $db = new clsBanco();
            $set = '';
            $gruda = '';

            if (is_numeric($this->ref_idesco)) {
                $set .= "{$gruda}ref_idesco = '{$this->ref_idesco}'";
                $gruda = ', ';
            } elseif ($this->ref_idesco !== false) {
                $set .= "{$gruda}ref_idesco = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->carga_horaria)) {
                $set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
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
            if (is_numeric($this->ref_cod_subnivel)) {
                $set .= "{$gruda}ref_cod_subnivel = '{$this->ref_cod_subnivel}'";
                $gruda = ', ';
            }
            if (is_numeric($this->tipo_ensino_medio_cursado)) {
                $set .= "{$gruda}tipo_ensino_medio_cursado = '{$this->tipo_ensino_medio_cursado}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}tipo_ensino_medio_cursado = NULL";
                $gruda = ', ';
            }
            if (is_string($this->pos_graduacao)) {
                $set .= "{$gruda}pos_graduacao = '{$this->pos_graduacao}'";
                $gruda = ', ';
            }
            if (is_string($this->curso_formacao_continuada)) {
                $set .= "{$gruda}curso_formacao_continuada = '{$this->curso_formacao_continuada}'";
                $gruda = ', ';
            }
            if (dbBool($this->multi_seriado)) {
                $set .= "{$gruda}multi_seriado = TRUE ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}multi_seriado = FALSE ";
                $gruda = ', ';
            }
            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_servidor = '{$this->cod_servidor}' AND ref_cod_instituicao = '{$this->ref_cod_instituicao}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna um array com resultados de uma pesquisa parametrizada
     *
     * O array retornado contém em cada um de seus items um array associativo onde
     * as chaves correspondem aos campos da tabela indicados por
     * $this->_campos_lista
     * .
     *
     * A pesquisa SELECT realizada é afetada por diversos parâmetros disponíveis.
     * Alguns dos parâmetros induzem a subqueries para a avaliação de diferentes
     * funcionalidades do sistema.
     *
     * @see intranet/educar_pesqu
     * isa_servidor_lst
     * .php  Listagem de busca de
     *  servidores
     * @see intranet/educar_quadro_horario_horarios_cad.php  Cadastro de horário
     *  de aula para uma turma
     * @see intranet/educar_turma_cad.php  Cadastro de turma
     *
     * @param int        $int_cod_servidor            Código do servidor
     * @param int        $int_ref_cod_deficiencia     Código da deficiência do servidor
     * @param int        $int_ref_idesco              Código da escolaridade do servidor
     * @param int        $int_carga_horaria           Carga horária do servidor
     * @param string     $date_data_cadastro_ini      Data de cadastro inicial (busca por intervalo >= ao valor)
     * @param string     $date_data_cadastro_fim      Data de cadastro final (busca por intervalo <= ao valor)
     * @param string     $date_data_exclusao_ini      Data da exclusão inicial (busca por intervalo >= ao valor)
     * @param string     $date_data_exclusao_fim      Data da exclusão final (busca por intervalo <= ao valor)
     * @param int        $int_ativo                   '1' para buscar apenas por servidores ativos
     * @param int        $int_ref_cod_instituicao     Código da instituição do servidor
     * @param string     $str_tipo                    'livre' para buscar apenas por servidores não alocados (subquery)
     * @param array      $array_horario               Busca por horário de alocação do servidor (subquery)
     * @param int        $str_not_in_servidor         Código de servidor a excluir
     * @param string     $str_nome_servidor           Busca do tipo LIKE pelo padrão de nome do servidor (subquery)
     * @param int|string $boo_professor               Qualquer valor que avalie para TRUE para buscar por servidores professores (subquery)
     * @param string     $str_horario                 'S' para buscar se o servidor está alocado em um dos horários (indicados $matutino, $vespertino ou $noturno) (subquery)
     * @param bool       $bool_ordena_por_nome        TRUE para ordenar os resultados pelo campo nome por ordem alfabética crescente
     * @param string     $lst_matriculas              Verifica se o servidor não está na lista de matriculas (string com inteiros separados por vírgula: 54, 55, 60).
     *                                                Apenas verifica quando a buscar por horário de alocação é realizada
     * @param bool       $matutino                    Busca por professores com horário livre no período matutino
     * @param bool       $vespertino                  Busca por professores com horário livre no período vespertino
     * @param bool       $noturno                     Busca por professores com horário livre no período noturno
     * @param int        $int_ref_cod_escola          Código da escola para verificar se o servidor está alocado nela (usado em várias das subqueries)
     * @param string     $str_hr_mat                  Duração da aula (formato HH:MM) para o período matutino
     * @param string     $str_hr_ves                  Duração da aula (formato HH:MM) para o período vespertino
     * @param string     $str_hr_not                  Duração da aula (formato HH:MM) para o período noturno
     * @param int        $int_dia_semana              Inteiro para o dia da semana (1 = domingo, 7 = sábado)
     * @param int        $alocacao_escola_instituicao Código da instituição ao qual o servidor deve estar cadastrado (subquery)
     * @param int        $int_identificador           Campo identificado para busca na tabela pmieducar.quadro_horario_horarios_aux (subquery)
     * @param int        $int_ref_cod_curso           Código do curso que o professor deve estar cadastrado (subquery)
     * @param int        $int_ref_cod_disciplina      Código da disciplina que o professor deve ser habilitado (subquery).
     *                                                Somente verifica quando o curso passado por $int_ref_cod_curso não
     *                                                possui sistema de falta globalizada
     * @param int        $int_ref_cod_subnivel        Código de subnível que o servidor deve possuir
     *
     * @return array|bool Array com os resultados da query SELECT ou FALSE caso
     *                    nenhum registro tenha sido encontrado
     */
    public function lista_professor($cod_instituicao, $cod_escola, $str_nome_servidor)
    {
        $this->_campos_lista = 's.cod_servidor, p.nome, func.matricula, s.ref_cod_instituicao';

        $this->_schema = 'pmieducar.';
        $tabela_compl = 'LEFT JOIN cadastro.pessoa p ON (s.cod_servidor = p.idpes)
                      LEFT JOIN portal.funcionario func ON (s.cod_servidor = func.ref_cod_pessoa_fj)
                      LEFT JOIN pmieducar.servidor_funcao as sf ON s.cod_servidor = sf.ref_cod_servidor';
        $filtros = "WHERE s.ativo = '1'
                        AND s.ref_cod_instituicao = $cod_instituicao
                        AND (s.cod_servidor IN (SELECT a.ref_cod_servidor
                                                  FROM pmieducar.servidor_alocacao a
                                                 WHERE a.ativo = 1
                                                   AND a.ref_ref_cod_instituicao = $cod_instituicao
                                                   AND ref_cod_escola = $cod_escola))
                        AND EXISTS (SELECT 1
                                      FROM pmieducar.servidor_funcao sf,
                                           pmieducar.funcao f,
                                           pmieducar.servidor_disciplina sd
                                     WHERE f.cod_funcao = sf.ref_cod_funcao
                                       AND f.professor = 1
                                       AND sf.ref_ref_cod_instituicao = s.ref_cod_instituicao
                                       AND s.cod_servidor = sf.ref_cod_servidor
                                       AND s.cod_servidor = sd.ref_cod_servidor
                                       AND s.ref_cod_instituicao = sd.ref_ref_cod_instituicao)";

        if ($str_nome_servidor != '') {
            $filtros .= " AND translate(upper(p.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome_servidor}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
        }

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_schema}servidor s {$tabela_compl} {$filtros} GROUP BY {$this->_campos_lista}" . $this->getOrderby();

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_schema}servidor s {$tabela_compl} {$filtros}");

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

    public function lista(
        $int_cod_servidor = null,
        $int_ref_cod_deficiencia = null,
        $int_ref_idesco = null,
        $int_carga_horaria = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $int_ref_cod_instituicao = null,
        $str_tipo = null,
        $array_horario = null,
        $str_not_in_servidor = null,
        $str_nome_servidor = null,
        $boo_professor = false,
        $str_horario = null,
        $bool_ordena_por_nome = false,
        $lst_matriculas = null,
        $matutino = false,
        $vespertino = false,
        $noturno = false,
        $int_ref_cod_escola = null,
        $str_hr_mat = null,
        $str_hr_ves = null,
        $str_hr_not = null,
        $int_dia_semana = null,
        $alocacao_escola_instituicao = null,
        $int_identificador = null,
        $int_ref_cod_curso = null,
        $int_ref_cod_disciplina = null,
        $int_ref_cod_subnivel = null,
        $bool_servidor_sem_alocacao = false,
        $ano_alocacao = null,
        $matricula_funcionario = null
    ) {
        // Extrai as informações de hora inicial e hora final, para definir melhor
        // o lookup de carga horária de servidores alocados, para operações como
        // a alocação de docente em quadro de horário. Isso é necessário para que
        // não seja necessário alocar o docente em dois períodos diferentes apenas
        // porque o horário final de uma aula extrapola o limite de horário do
        // período.
        if (is_array($array_horario) && 3 >= count($array_horario)) {
            $horarioInicial = explode(':', $array_horario[1]);
            $horarioFinal = explode(':', $array_horario[2]);
            $horarioInicial = $horarioInicial[0] * 60 + $horarioInicial[1];
            $horarioFinal = $horarioFinal[0] * 60 + $horarioFinal[1];
            // Caso o horário definido inicie no período "matutino" e se encerre no
            // período "vespertino", irá considerar como "matutino" apenas.
            $matutinoLimite = 12 * 60;
            if ($horarioInicial < $matutinoLimite && $horarioFinal > $matutinoLimite) {
                $vespertino = false;
            }
            // Caso o horário definido inicie no período "vespertino" e se encerre
            // no período "noturno", irá considerar como "vespertino" apenas.
            $vespertinoLimite = 18 * 60;
            if ($horarioInicial < $vespertinoLimite && $horarioFinal > $vespertinoLimite) {
                $noturno = false;
            }
        }
        $whereAnd = ' WHERE ';
        $filtros = '';
        $tabela_compl = '';
        if (is_bool($bool_ordena_por_nome)) {
            $tabela_compl .= ' LEFT JOIN cadastro.pessoa p ON s.cod_servidor = p.idpes ';
            $tabela_compl .= ' LEFT JOIN portal.funcionario func ON s.cod_servidor = func.ref_cod_pessoa_fj';
            $tabela_compl .= ' LEFT JOIN pmieducar.servidor_funcao as sf ON s.cod_servidor = sf.ref_cod_servidor';
            $this->_campos_lista2 .= ', p.nome';
            $this->setOrderby('nome');
        } else {
            $this->_campos_lista2 = $this->_todos_campos2;
            $this->setOrderby(' 1 ');
        }
        $db = new clsBanco();
        $sql = "SELECT {$this->_campos_lista2} FROM {$this->_schema}servidor s{$tabela_compl}";
        if (is_numeric($int_cod_servidor)) {
            $filtros .= "{$whereAnd} s.cod_servidor = '{$int_cod_servidor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_idesco)) {
            $filtros .= "{$whereAnd} s.ref_idesco = '{$int_ref_idesco}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_carga_horaria)) {
            $filtros .= "{$whereAnd} s.carga_horaria = '{$int_carga_horaria}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} s.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} s.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} s.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} s.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} s.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} s.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} s.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }
        if (is_string($matricula_funcionario)) {
            $filtros .= "{$whereAnd} public.fcn_upper(sf.matricula) LIKE public.fcn_upper('%{$matricula_funcionario}%')";
            $whereAnd = ' AND ';
        }

        $where = '';

        // Busca tipo LIKE pelo nome do servidor
        if (is_string($str_nome_servidor)) {
            $nome_servidor = $db->escapeString($str_nome_servidor);
            $filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM cadastro.pessoa WHERE idpes = cod_servidor and unaccent(nome) ILIKE unaccent('%{$nome_servidor}%'))";
            $whereAnd = ' AND ';
        }
        // Seleciona apenas servidores que tenham a carga atual maior ou igual ao
        // do servidor atual
        if (is_string($str_tipo) && $str_tipo == 'livre') {
            if (is_numeric($int_ref_cod_instituicao)) {
                $where = " AND s.ref_cod_instituicao      = '{$int_ref_cod_instituicao}' ";
                $where2 = " AND sa.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}' ";
            }
            $filtros .= "
  {$whereAnd} NOT EXISTS
    (SELECT 1
    FROM pmieducar.servidor_alocacao sa
    WHERE sa.ref_cod_servidor = s.cod_servidor $where2)";
            $filtros .= "
  {$whereAnd} (s.carga_horaria::text || ':00:00') >= COALESCE(
    (SELECT SUM(carga_horaria::time)::text
    FROM pmieducar.servidor_alocacao saa
    WHERE saa.ref_cod_servidor = {$str_not_in_servidor}),'00:00') $where";
            $whereAnd = ' AND ';
        } else {
            $filtros .= " {$whereAnd} (s.cod_servidor IN
                  (SELECT a.ref_cod_servidor
                    FROM pmieducar.servidor_alocacao a
                    WHERE a.ativo = 1 ";

            if (is_numeric($int_ref_cod_instituicao)) {
                $filtros .= " AND a.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            }
            if (is_numeric($int_ref_cod_escola)) {
                $filtros .= " AND ref_cod_escola = '{$int_ref_cod_escola}' ";
            }
            if (is_numeric($ano_alocacao)) {
                $filtros .= " AND a.ano = '{$ano_alocacao}'";
            }
            if ($bool_servidor_sem_alocacao) {
                $filtros .= ') OR NOT EXISTS(SELECT 1 FROM pmieducar.servidor_alocacao where servidor_alocacao.ativo = 1 and servidor_alocacao.ref_cod_servidor = s.cod_servidor ';
                $filtros .= $ano_alocacao ? "and servidor_alocacao.ano = {$ano_alocacao})) " : ')) ';
            } else {
                if ($bool_servidor_sem_alocacao) {
                    $filtros .= ') OR NOT EXISTS(SELECT 1 FROM pmieducar.servidor_alocacao where servidor_alocacao.ativo = 1 and servidor_alocacao.ref_cod_servidor = s.cod_servidor)) ';
                } else {
                    $filtros .= ')) ';
                }
            }

            if (is_array($array_horario)) {
                $cond = 'AND';
                if (is_numeric($int_ref_cod_instituicao)) {
                    $where .= " {$cond} a.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}' ";
                    $cond = 'AND';
                }
                if (is_numeric($int_ref_cod_escola)) {
                    $where .= " {$cond} a.ref_cod_escola = '{$int_ref_cod_escola}' ";
                    $cond = 'AND';
                }
                $where .= " {$cond} a.ativo = '1'";
                $cond = 'AND';
                $hora_ini = explode(':', $array_horario[1]);
                $hora_fim = explode(':', $array_horario[2]);
                $horas = sprintf('%02d', (int) abs($hora_fim[0]) - abs($hora_ini[0]));
                $minutos = sprintf('%02d', (int) abs($hora_fim[1]) - abs($hora_ini[1]));
                // Remove qualquer AND que esteja no início da cláusula SQL
                $wherePieces = explode(' ', trim($where));
                if ('AND' == $wherePieces[0]) {
                    array_shift($wherePieces);
                    $where = implode(' ', $wherePieces);
                }
                if ($matutino) {
                    if (is_string($str_horario) && $str_horario == 'S') {
                        // A somatória retorna nulo
                        $filtros .= "
    {$whereAnd} (s.cod_servidor IN (SELECT a.ref_cod_servidor
          FROM pmieducar.servidor_alocacao a
          WHERE $where
          AND a.periodo = 1
          AND a.carga_horaria >= COALESCE(
          (SELECT SUM(qhh.hora_final - qhh.hora_inicial)
            FROM pmieducar.quadro_horario_horarios qhh
            INNER JOIN pmieducar.quadro_horario ON (quadro_horario.cod_quadro_horario = qhh.ref_cod_quadro_horario
                                                    AND quadro_horario.ativo = 1)
            INNER JOIN pmieducar.turma ON (turma.cod_turma = quadro_horario.ref_cod_turma
                                           AND turma.ativo = 1)
            WHERE qhh.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
            AND qhh.ref_cod_escola = '$int_ref_cod_escola'
            AND qhh.hora_inicial >= '06:00'
            AND qhh.hora_inicial <= '12:00'
            AND qhh.ativo = '1'
            AND qhh.dia_semana <> '$int_dia_semana'
            AND qhh.ref_servidor = a.ref_cod_servidor
            GROUP BY qhh.ref_servidor) ,'00:00')  + '$str_hr_mat' + COALESCE(
            (SELECT SUM( qhha.hora_final - qhha.hora_inicial )
              FROM pmieducar.quadro_horario_horarios_aux qhha
              INNER JOIN pmieducar.quadro_horario ON (quadro_horario.cod_quadro_horario = qhha.ref_cod_quadro_horario
                                                      AND quadro_horario.ativo = 1)
              INNER JOIN pmieducar.turma ON (turma.cod_turma = quadro_horario.ref_cod_turma
                                             AND turma.ativo = 1)
              WHERE qhha.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
              AND qhha.ref_cod_escola = $int_ref_cod_escola
              AND qhha.hora_inicial >= '06:00'
              AND qhha.hora_inicial <= '12:00'
              AND qhha.ref_servidor = a.ref_cod_servidor
              AND identificador = '$int_identificador'
              GROUP BY qhha.ref_servidor),'00:00')) OR s.multi_seriado )";
                    } else {
                        $filtros .= "
      {$whereAnd} (s.cod_servidor NOT IN (SELECT a.ref_cod_servidor
              FROM pmieducar.servidor_alocacao a
              WHERE $where
              AND a.periodo = 1) OR s.multi_seriado )";
                    }
                }
                if ($vespertino) {
                    if (is_string($str_horario) && $str_horario == 'S') {
                        $filtros .= "
      {$whereAnd} (s.cod_servidor IN
              (SELECT a.ref_cod_servidor
                FROM pmieducar.servidor_alocacao a
                WHERE $where
                AND a.periodo = 2
                AND a.carga_horaria >= COALESCE(
                  (SELECT SUM( qhh.hora_final - qhh.hora_inicial )
                  FROM pmieducar.quadro_horario_horarios qhh
                  INNER JOIN pmieducar.quadro_horario ON (quadro_horario.cod_quadro_horario = qhh.ref_cod_quadro_horario
                                                          AND quadro_horario.ativo = 1)
                  INNER JOIN pmieducar.turma ON (turma.cod_turma = quadro_horario.ref_cod_turma
                                                 AND turma.ativo = 1)
                  WHERE qhh.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
                  AND qhh.ref_cod_escola = '$int_ref_cod_escola'
                  AND qhh.ativo = '1'
                  AND qhh.hora_inicial >= '12:00'
                  AND qhh.hora_inicial <= '18:00'
                  AND qhh.dia_semana <> '$int_dia_semana'
                  AND qhh.ref_servidor = a.ref_cod_servidor
                  AND quadro_horario.ano = $ano_alocacao
                  AND qhh.sequencial = (
                    SELECT s_qhh.sequencial
                    FROM pmieducar.quadro_horario_horarios s_qhh
                    WHERE s_qhh.dia_semana = qhh.dia_semana
                    AND s_qhh.hora_inicial = qhh.hora_inicial
                    AND s_qhh.ref_cod_quadro_horario = quadro_horario.cod_quadro_horario
                    AND s_qhh.hora_final = qhh.hora_final
                    ORDER BY s_qhh.sequencial DESC
                    LIMIT 1
                  )
                  GROUP BY qhh.ref_servidor ),'00:00') + '$str_hr_ves' +  COALESCE(
                  (SELECT SUM( qhha.hora_final - qhha.hora_inicial )
                    FROM pmieducar.quadro_horario_horarios_aux qhha
                    INNER JOIN pmieducar.quadro_horario ON (quadro_horario.cod_quadro_horario = qhha.ref_cod_quadro_horario
                                                            AND quadro_horario.ativo = 1)
                    INNER JOIN pmieducar.turma ON (turma.cod_turma = quadro_horario.ref_cod_turma
                                                   AND turma.ativo = 1)
                    WHERE qhha.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
                    AND qhha.ref_cod_escola = '$int_ref_cod_escola'
                    AND qhha.ref_servidor = a.ref_cod_servidor
                    AND qhha.hora_inicial >= '12:00'
                    AND qhha.hora_inicial <= '18:00'
                    AND quadro_horario.ano = $ano_alocacao
                    AND identificador = '$int_identificador'
                    GROUP BY qhha.ref_servidor),'00:00') ) OR s.multi_seriado ) ";
                    } else {
                        $filtros .= "
      {$whereAnd} (s.cod_servidor NOT IN ( SELECT a.ref_cod_servidor
              FROM pmieducar.servidor_alocacao a
              WHERE $where
              AND a.periodo = 2 ) OR s.multi_seriado) ";
                    }
                }
                if ($noturno) {
                    if (is_string($str_horario) && $str_horario == 'S') {
                        $filtros .= "
      {$whereAnd} (s.cod_servidor IN ( SELECT a.ref_cod_servidor
              FROM pmieducar.servidor_alocacao a
              WHERE $where
              AND a.periodo = 3
              AND a.carga_horaria >= COALESCE(
              (SELECT SUM(qhh.hora_final - qhh.hora_inicial)
                FROM pmieducar.quadro_horario_horarios qhh
               INNER JOIN pmieducar.quadro_horario ON (quadro_horario.cod_quadro_horario = qhh.ref_cod_quadro_horario
                                                       AND quadro_horario.ativo = 1)
               INNER JOIN pmieducar.turma ON (turma.cod_turma = quadro_horario.ref_cod_turma
                                              AND turma.ativo = 1)
                WHERE qhh.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
                AND qhh.ref_cod_escola = '$int_ref_cod_escola'
                AND qhh.ativo = '1'
                AND qhh.hora_inicial >= '18:00'
                AND qhh.hora_inicial <= '23:59'
                AND qhh.dia_semana <> '$int_dia_semana'
                AND qhh.ref_servidor = a.ref_cod_servidor
                GROUP BY qhh.ref_servidor ),'00:00')  + '$str_hr_not' +  COALESCE(
                  (SELECT SUM( qhha.hora_final - qhha.hora_inicial )
                  FROM pmieducar.quadro_horario_horarios_aux qhha
                  INNER JOIN pmieducar.quadro_horario ON (quadro_horario.cod_quadro_horario = qhha.ref_cod_quadro_horario
                                                          AND quadro_horario.ativo = 1)
                  INNER JOIN pmieducar.turma ON (turma.cod_turma = quadro_horario.ref_cod_turma
                                                 AND turma.ativo = 1)
                  WHERE qhha.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
                  AND qhha.ref_cod_escola = '$int_ref_cod_escola'
                  AND qhha.ref_servidor = a.ref_cod_servidor
                  AND qhha.hora_inicial >= '18:00'
                  AND qhha.hora_inicial <= '23:59'
                  AND identificador = '$int_identificador'
                  GROUP BY qhha.ref_servidor),'00:00') ) OR s.multi_seriado) ";
                    } else {
                        $filtros .= "
      {$whereAnd} (s.cod_servidor NOT IN (
            SELECT a.ref_cod_servidor
              FROM pmieducar.servidor_alocacao a
              WHERE $where
              AND a.periodo = 3 ) OR s.multi_seriado) ";
                    }
                }
                if (is_string($str_horario) && $str_horario == 'S') {
                } else {
                    $filtros .= "
      {$whereAnd} ((s.carga_horaria >= COALESCE(
                    (SELECT sum(hora_final - qhh.hora_inicial) + '" . abs($horas) . ':' . abs($minutos) . "'
                      FROM pmieducar.servidor_alocacao sa
                      WHERE sa.ref_cod_servidor = s.cod_servidor
                      AND sa.ref_ref_cod_instituicao ='{$int_ref_cod_instituicao}'),'00:00')) OR s.multi_seriado)";
                }
            }
        }
        if ((is_array($array_horario) && $str_not_in_servidor) || (is_string($str_tipo) && $str_not_in_servidor)) {
            $filtros .= "{$whereAnd} s.cod_servidor NOT IN ( {$str_not_in_servidor} )";
            $whereAnd = ' AND ';
        }
        $obj_curso = new clsPmieducarCurso($int_ref_cod_curso);
        $det_curso = $obj_curso->detalhe();
        // Seleciona apenas servidor cuja uma de suas funções seja a de professor
        // @todo Extract method
        if ($boo_professor) {
            /*
             * Caso os códigos de disciplina e de curso não sejam informado, mas o de
             * servidor para não buscar sim, seleciona as disciplinas deste servidor
             * com o qual o professor candidato terá que lecionar para ser retornado
             * na query.
             */
            if (!$int_ref_cod_disciplina && !$int_ref_cod_curso) {
                $servidorDisciplina = new clsPmieducarServidorDisciplina();
                $disciplinas = $servidorDisciplina->lista(null, null, $str_not_in_servidor);
                $servidorDisciplinas = [];
                if (is_array($disciplinas)) {
                    foreach ($disciplinas as $disciplina) {
                        $servidorDisciplinas[] = sprintf(
                            '(sd.ref_cod_disciplina = %d AND sd.ref_cod_curso = %d)',
                            $disciplina['ref_cod_disciplina'],
                            $disciplina['ref_cod_curso']
                        );
                    }
                    $servidorDisciplinas = sprintf('AND (%s)', implode(' AND ', $servidorDisciplinas));
                } else {
                    $servidorDisciplinas = '';
                }
            } else {
                $servidorDisciplinas = sprintf(
                    'AND (case when %1$d = 0 then
                  sd.ref_cod_curso = %2$d
                else
                  (sd.ref_cod_disciplina = %1$d AND sd.ref_cod_curso = %2$d)
                end)',
                    $int_ref_cod_disciplina,
                    $int_ref_cod_curso
                );
            }
            $filtros .= "
    {$whereAnd} EXISTS
      (SELECT
         1
       FROM
         pmieducar.servidor_funcao sf, pmieducar.funcao f, pmieducar.servidor_disciplina sd
       WHERE
        f.cod_funcao = sf.ref_cod_funcao AND
        f.professor = 1 AND
        sf.ref_ref_cod_instituicao = s.ref_cod_instituicao AND
        s.cod_servidor = sf.ref_cod_servidor AND
        s.cod_servidor = sd.ref_cod_servidor AND
        s.ref_cod_instituicao = sd.ref_ref_cod_instituicao
        {$servidorDisciplinas})";
            $whereAnd = ' AND ';
        }

        if (is_string($str_horario) && $str_horario == 'S') {
            $whereAno = is_numeric($ano_alocacao) ? 'AND quadro_horario.ano = ' . $ano_alocacao : null;
            $filtros .= "
    {$whereAnd} (s.cod_servidor NOT IN
      (SELECT DISTINCT qhh.ref_servidor
         FROM pmieducar.quadro_horario_horarios qhh
        INNER JOIN pmieducar.quadro_horario ON (quadro_horario.cod_quadro_horario = qhh.ref_cod_quadro_horario
                                                AND quadro_horario.ativo = 1)
        INNER JOIN pmieducar.turma ON (turma.cod_turma = quadro_horario.ref_cod_turma
                                       AND turma.ativo = 1)
        WHERE qhh.ref_servidor = s.cod_servidor
        AND qhh.ref_cod_instituicao_servidor = s.ref_cod_instituicao
        AND qhh.dia_semana = '{$array_horario[0]}'
        AND ((('{$array_horario[1]}' > qhh.hora_inicial AND '{$array_horario[1]}' < qhh.hora_final)
              OR ('{$array_horario[2]}' > qhh.hora_inicial AND '{$array_horario[2]}' < qhh.hora_final))
            OR ('{$array_horario[1]}' = qhh.hora_inicial AND '{$array_horario[2]}' = qhh.hora_final)
            OR ('{$array_horario[1]}' <= qhh.hora_inicial AND '{$array_horario[2]}' >= qhh.hora_final))
        AND qhh.ativo = '1'
        {$whereAno}";
            if (is_string($lst_matriculas)) {
                $filtros .= "AND qhh.ref_servidor NOT IN ({$lst_matriculas})";
            }
            $filtros .= ' ) OR s.multi_seriado) ';
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_subnivel)) {
            $filtros .= "{$whereAnd} s.ref_cod_subnivel = '{$int_ref_cod_subnivel}'";
            $whereAnd = ' AND ';
        }
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];
        $sql = "SELECT distinct {$this->_campos_lista2} FROM {$this->_schema}servidor s{$tabela_compl} {$filtros}" . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT distinct COUNT(0) FROM {$this->_schema}servidor s{$tabela_compl} {$filtros}");
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
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_servidor = '{$this->cod_servidor}' AND ref_cod_instituicao = '{$this->ref_cod_instituicao}'");
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
        if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_servidor = '{$this->cod_servidor}' AND ref_cod_instituicao = '{$this->ref_cod_instituicao}'");
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
        if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Retorna array com as funções do servidor
     *
     * Exemplo de array de retorno:
     * <code>
     * array(
     *   '2' => array(
     *     'cod_funcao' => 2,
     *     'nm_funcao' => 'Professor',
     *     'professor' => 1
     *   )
     * );
     * <code>
     *
     * @return array Array associativo com a primeira chave sendo o código da
     *               função. O array interno contém o nome da função e se a função desempenha
     *               um papel de professor
     * @since   Método disponível desde a versão 1.0.2
     *
     */
    public function getServidorFuncoes()
    {
        $db = new clsBanco();
        $sql = 'SELECT t2.cod_funcao, t2.nm_funcao, t2.professor FROM pmieducar.servidor_funcao AS t1, pmieducar.funcao AS t2 ';
        $sql .= 'WHERE t1.ref_cod_servidor = \'%d\' AND t1.ref_ref_cod_instituicao = \'%d\' ';
        $sql .= 'AND t1.ref_cod_funcao = t2.cod_funcao';
        $sql = sprintf($sql, $this->cod_servidor, $this->ref_cod_instituicao);
        $db->Consulta($sql);
        $funcoes = [];
        while ($db->ProximoRegistro() != false) {
            $row = $db->Tupla();
            $funcoes[$row['cod_funcao']] = [
                'cod_funcao' => $row['cod_funcao'],
                'nm_funcao' => $row['nm_funcao'],
                'professor' => $row['professor'],
            ];
        }

        return $funcoes;
    }

    /**
     * Retorna um array com as disciplinas alocadas ao servidor no quadro de
     * horários
     *
     * @param int $codServidor    Código do servidor, caso não seja informado,
     *                            usa o código disponível no objeto atual
     * @param int $codInstituicao Código da instituição, caso não seja
     *                            informado, usa o código disponível no objeto atual
     *
     * @return array|bool Array com códigos das disciplinas ordenados ou FALSE
     *                    caso o servidor não tenha disciplinas
     * @since   Método disponível desde a versão 1.0.2
     *
     */
    public function getServidorDisciplinasQuadroHorarioHorarios(
        $codServidor = null,
        $codInstituicao = null
    ) {
        $codServidor = $codServidor != null ? $codServidor : $this->cod_servidor;
        $codInstituicao = $codInstituicao != null ? $codInstituicao : $this->ref_cod_instituicao;
        $sql = 'SELECT DISTINCT(qhh.ref_cod_disciplina) AS ref_cod_disciplina ';
        $sql .= 'FROM pmieducar.quadro_horario_horarios qhh, pmieducar.servidor s ';
        $sql .= 'WHERE qhh.ref_servidor = s.cod_servidor AND ';
        $sql .= 'qhh.ref_servidor = \'%d\' AND qhh.ref_cod_instituicao_servidor = \'%d\'';
        $sql = sprintf($sql, $codServidor, $codInstituicao);
        $db = new clsBanco();
        $db->Consulta($sql);
        $disciplinas = [];
        while ($db->ProximoRegistro() != false) {
            $row = $db->Tupla();
            $disciplinas[] = $row['ref_cod_disciplina'];
        }
        if (count($disciplinas)) {
            return asort($disciplinas);
        }

        return false;
    }

    /**
     * Retorna um array com os códigos de servidor e instituição, usando os
     * valores dos parâmetros ou das propriedades da instância atual.
     *
     * @param int $codServidor    Código do servidor, caso não seja informado,
     *                            usa o código disponível no objeto atual
     * @param int $codInstituicao Código da instituição, caso não seja
     *                            informado, usa o código disponível no objeto atual
     *
     * @return array|bool (codServidor => (int), codInstituicao => (int))
     * @since   Método disponível desde a versão 1.2.0
     *
     */
    public function _getCodServidorInstituicao($codServidor = null, $codInstituicao = null)
    {
        $codServidor = $codServidor != null ? $codServidor : $this->cod_servidor;
        $codInstituicao = $codInstituicao != null ? $codInstituicao : $this->ref_cod_instituicao;
        // Se códigos não forem fornecidos, nem pela classe nem pelo código cliente,
        // retorna FALSE
        if ($codServidor == null || $codInstituicao == null) {
            return false;
        }

        return [
            'codServidor' => $codServidor,
            'codInstituicao' => $codInstituicao
        ];
    }

    /**
     * Retorna um array com os códigos das disciplinas do servidor
     *
     * @param int $codServidor    Código do servidor, caso não seja informado,
     *                            usa o código disponível no objeto atual
     * @param int $codInstituicao Código da instituição, caso não seja
     *                            informado, usa o código disponível no objeto atual
     *
     * @return array|bool Array com códigos das disciplinas ordenados ou FALSE
     *                    caso o servidor não tenha disciplinas
     * @since   Método disponível desde a versão 1.0.2
     *
     */
    public function getServidorDisciplinas(
        $codServidor = null,
        $codInstituicao = null
    ) {
        $codigos = $this->_getCodServidorInstituicao($codServidor, $codInstituicao);
        if (!$codigos) {
            return false;
        }
        // Se códigos não forem fornecidos, nem pela classe nem pelo código cliente,
        // retorna FALSE
        if ($codServidor == null || $codInstituicao == null) {
            return false;
        }
        $sql = 'SELECT DISTINCT(sd.ref_cod_disciplina) AS ref_cod_disciplina ';
        $sql .= 'FROM pmieducar.servidor_disciplina sd, pmieducar.servidor s ';
        $sql .= 'WHERE sd.ref_cod_servidor = s.cod_servidor AND ';
        $sql .= 'sd.ref_cod_servidor = \'%d\' AND sd.ref_ref_cod_instituicao = \'%d\'';
        $sql = sprintf($sql, $codigos['codServidor'], $codigos['codInstituicao']);
        $db = new clsBanco();
        $db->Consulta($sql);
        $disciplinas = [];
        while ($db->ProximoRegistro() != false) {
            $row = $db->Tupla();
            $disciplinas[] = $row['ref_cod_disciplina'];
        }
        if (count($disciplinas)) {
            return asort($disciplinas);
        }

        return false;
    }

    /**
     * Retorna os horários de aula do servidor na instituição.
     *
     * @param int $codServidor    Código do servidor, caso não seja informado,
     *                            usa o código disponível no objeto atual
     * @param int $codInstituicao Código da instituição, caso não seja
     *                            informado, usa o código disponível no objeto atual
     *
     * @return array|bool Array associativo com os índices nm_escola, nm_curso,
     *                    nm_serie, nm_turma, nome (componente curricular), dia_semana,
     *                    qhh.hora_inicial e hora_final.
     * @since   Método disponível desde a versão 1.0.2
     *
     */
    public function getHorariosServidor($codServidor = null, $codInstituicao = null)
    {
        $codigos = $this->_getCodServidorInstituicao($codServidor, $codInstituicao);
        if (!$codigos) {
            return false;
        }
        $sql = 'SELECT
              ec.nm_escola,
              c.nm_curso,
              s.nm_serie,
              t.nm_turma,
              cc.nome,
              qhh.dia_semana,
              qhh.hora_inicial,
              qhh.hora_final
            FROM
              pmieducar.quadro_horario_horarios qhh,
              pmieducar.quadro_horario qh,
              pmieducar.turma t,
              pmieducar.serie s,
              pmieducar.curso c,
              pmieducar.escola_complemento ec,
              modules.componente_curricular cc
            WHERE
              qh.cod_quadro_horario = qhh.ref_cod_quadro_horario
              AND qh.ref_cod_turma = t.cod_turma
              AND t.ref_ref_cod_serie = s.cod_serie
              AND s.ref_cod_curso = c.cod_curso
              AND qhh.ref_cod_escola = ec.ref_cod_escola
              AND qhh.ref_cod_disciplina = cc.id
              AND qh.ativo = 1
              AND qhh.ativo = 1
              AND t.ativo = 1
              AND qhh.ref_servidor = %d
              AND qhh.ref_cod_instituicao_servidor = %d
            ORDER BY
              nm_escola,
              dia_semana,
              qhh.hora_inicial';
        $sql = sprintf($sql, $codigos['codServidor'], $codigos['codInstituicao']);
        $db = new clsBanco();
        $db->Consulta($sql);
        $horarios = [];
        while ($db->ProximoRegistro() != false) {
            $row = $db->Tupla();
            $horarios[] = $row;
        }
        if (count($horarios)) {
            return $horarios;
        }

        return false;
    }

    /**
     * Verifica se um servidor desempenha a função de professor.
     *
     * Primeiro, recuperamos todas as funções do servidor e procuramos
     * por um dos itens que tenha o índice professor igual a 1.
     *
     * @return bool TRUE caso o servidor desempenhe a função de professor
     * @since   Método disponível desde a versão 1.0.2
     *
     */
    public function isProfessor()
    {
        $funcoes = $this->getServidorFuncoes();
        foreach ($funcoes as $funcao) {
            if (1 == $funcao['professor']) {
                return true;
                break;
            }
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function qtdhoras(
        $int_cod_servidor,
        $int_cod_escola,
        $int_ref_cod_instituicao,
        $dia_semana
    ) {
        $db = new clsBanco();
        $db->Consulta(
            "
      SELECT
        EXTRACT(HOUR FROM (SUM(hora_final - hora_inicial))) AS hora,
        EXTRACT(MINUTE FROM (SUM(hora_final - hora_inicial))) AS min
      FROM
        pmieducar.servidor_alocacao
      WHERE
        ref_cod_servidor = {$int_cod_servidor} AND
        ref_cod_escola = {$int_cod_escola} AND
        ref_ref_cod_instituicao = {$int_ref_cod_instituicao} AND
        dia_semana = {$dia_semana}"
        );
        $db->ProximoRegistro();

        return $db->Tupla();
    }
}
