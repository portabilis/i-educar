<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*                                                                        *
*   @author Prefeitura Municipal de Itajaí                               *
*   @updated 29/03/2007                                                  *
*   Pacote: i-PLB Software Público Livre e Brasileiro                    *
*                                                                        *
*   Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí             *
*                       ctima@itajai.sc.gov.br                           *
*                                                                        *
*   Este  programa  é  software livre, você pode redistribuí-lo e/ou     *
*   modificá-lo sob os termos da Licença Pública Geral GNU, conforme     *
*   publicada pela Free  Software  Foundation,  tanto  a versão 2 da     *
*   Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.    *
*                                                                        *
*   Este programa  é distribuído na expectativa de ser útil, mas SEM     *
*   QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-     *
*   ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-     *
*   sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.     *
*                                                                        *
*   Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU     *
*   junto  com  este  programa. Se não, escreva para a Free Software     *
*   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
*   02111-1307, USA.                                                     *
*                                                                        *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 03/07/2006 11:07 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarCandidatoFilaUnica
{
    var $cod_candidato_fila_unica;
    var $ref_cod_aluno;
    var $ref_cod_serie;
    var $ref_cod_turno;
    var $ref_cod_pessoa_cad;
    var $ref_cod_pessoa_exc;
    var $ref_cod_matricula;
    var $ano_letivo;
    var $data_nasc;
    var $data_cadastro;
    var $data_exclusao;
    var $data_solicitacao;
    var $hora_solicitacao;
    var $horario_inicial;
    var $horario_final;
    var $situacao;
    var $via_judicial;
    var $via_judicial_doc;
    var $protocolo;
    var $ativo;

    // propriedades padrao

    // Armazena o total de resultados obtidos na ultima chamada ao metodo lista
    var $_total;

    // Nome do schema
    var $_schema;

    // Nome da tabela
    var $_tabela;

    // Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
    var $_campos_lista;

    // Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
    var $_todos_campos;

    // Valor que define a quantidade de registros a ser retornada pelo metodo lista
    var $_limite_quantidade;

    // Define o valor de offset no retorno dos registros no metodo lista
    var $_limite_offset;

    // Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
    var $_campo_order_by;

    // Construtor (PHP 4)
    function __construct($cod_candidato_fila_unica = NULL,
                                            $ref_cod_aluno = NULL,
                                            $ref_cod_serie = NULL,
                                            $ref_cod_turno = NULL,
                                            $ref_cod_pessoa_cad = NULL,
                                            $ref_cod_pessoa_exc = NULL,
                                            $ref_cod_matricula = NULL,
                                            $ano_letivo = NULL,
                                            $data_cadastro = NULL,
                                            $data_exclusao = NULL,
                                            $data_solicitacao = NULL,
                                            $hora_solicitacao = NULL,
                                            $horario_inicial = NULL,
                                            $horario_final = NULL,
                                            $situacao = NULL,
                                            $via_judicial = NULL,
                                            $via_judicial_doc = NULL,
                                            $ativo = NULL)
    {
        $db = new clsBanco();
        $this->_schema = "pmieducar.";
        $this->_tabela = "{$this->_schema}candidato_fila_unica";

        $this->_campos_lista = $this->_todos_campos = "cfu.cod_candidato_fila_unica,
                                                       cfu.ref_cod_aluno,
                                                       cfu.ref_cod_serie,
                                                       cfu.ref_cod_turno,
                                                       cfu.ref_cod_pessoa_cad,
                                                       cfu.ref_cod_pessoa_exc,
                                                       cfu.ref_cod_matricula,
                                                       cfu.ano_letivo,
                                                       cfu.data_cadastro,
                                                       cfu.data_exclusao,
                                                       cfu.data_solicitacao,
                                                       cfu.hora_solicitacao,
                                                       cfu.horario_inicial,
                                                       cfu.horario_final,
                                                       cfu.situacao,
                                                       cfu.motivo,
                                                       cfu.via_judicial,
                                                       cfu.via_judicial_doc,
                                                       cfu.ativo";

        if(is_numeric($cod_candidato_fila_unica))
        {
            $this->cod_candidato_fila_unica = $cod_candidato_fila_unica;
        }
        if(is_numeric($ref_cod_aluno))
        {
            $this->ref_cod_aluno = $ref_cod_aluno;
        }
        if(is_numeric($ref_cod_serie))
        {
            $this->ref_cod_serie = $ref_cod_serie;
        }
        if(is_numeric($ref_cod_turno))
        {
            $this->ref_cod_turno = $ref_cod_turno;
        }
        if(is_numeric($ref_cod_pessoa_cad))
        {
            $this->ref_cod_pessoa_cad = $ref_cod_pessoa_cad;
        }
        if(is_numeric($ref_cod_pessoa_exc))
        {
            $this->ref_cod_pessoa_exc = $ref_cod_pessoa_exc;
        }
        if(is_numeric($ref_cod_matricula))
        {
            $this->ref_cod_matricula = $ref_cod_matricula;
        }
        if(is_numeric($ano_letivo))
        {
            $this->ano_letivo = $ano_letivo;
        }
        if(is_string($data_cadastro))
        {
            $this->data_cadastro = $data_cadastro;
        }
        if(is_string($data_exclusao))
        {
            $this->data_exclusao = $data_exclusao;
        }
        if(is_string($data_solicitacao))
        {
            $this->data_solicitacao = $data_solicitacao;
        }
        if(is_string($hora_solicitacao))
        {
            $this->hora_solicitacao = $hora_solicitacao;
        }
        if(is_string($horario_inicial))
        {
            $this->horario_inicial = $horario_inicial;
        }
        if(is_string($horario_final))
        {
            $this->horario_final = $horario_final;
        }
        if(is_string($situacao))
        {
            $this->situacao = $situacao;
        }
        if(is_bool($via_judicial))
        {
            $this->via_judicial = $via_judicial;
        }
        if(is_string($via_judicial_doc))
        {
            $this->via_judicial_doc = $via_judicial_doc;
        }
        if(is_numeric($ativo))
        {
            $this->ativo = $ativo;
        }

    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    function cadastra()
    {
        if(is_numeric($this->ref_cod_aluno)
           && is_numeric($this->ref_cod_serie)
           && is_numeric($this->ref_cod_turno)
           && is_numeric($this->ref_cod_pessoa_cad)
           && is_numeric($this->ano_letivo))
        {
            $db = new clsBanco();

            $campos = "";
            $valores = "";
            $gruda = "";

            $campos .= "{$gruda}ref_cod_aluno";
            $valores .= "{$gruda}{$this->ref_cod_aluno}";
            $gruda = ", ";

            $campos .= "{$gruda}ref_cod_serie";
            $valores .= "{$gruda}{$this->ref_cod_serie}";
            $gruda = ", ";

            $campos .= "{$gruda}ref_cod_turno";
            $valores .= "{$gruda}{$this->ref_cod_turno}";
            $gruda = ", ";

            $campos .= "{$gruda}ref_cod_pessoa_cad";
            $valores .= "{$gruda}{$this->ref_cod_pessoa_cad}";
            $gruda = ", ";

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ", ";

            $campos .= "{$gruda}ano_letivo";
            $valores .= "{$gruda}{$this->ano_letivo}";
            $gruda = ", ";

            if(is_string($this->data_solicitacao))
            {
                $campos .= "{$gruda}data_solicitacao";
                $valores .= "{$gruda}'{$this->data_solicitacao}'";
                $gruda = ", ";
            }
            if(is_string($this->hora_solicitacao))
            {
                $campos .= "{$gruda}hora_solicitacao";
                $valores .= "{$gruda}'{$this->hora_solicitacao}'";
                $gruda = ", ";
            }
            if(is_string($this->horario_inicial) && !empty($this->horario_inicial))
            {
                $campos .= "{$gruda}horario_inicial";
                $valores .= "{$gruda}'{$this->horario_inicial}'";
                $gruda = ", ";
            }else{
                $campos .= "{$gruda}horario_inicial";
                $valores .= "{$gruda}NULL";
                $gruda = ", ";
            }
            if(is_string($this->horario_final) && !empty($this->horario_final))
            {
                $campos .= "{$gruda}horario_final";
                $valores .= "{$gruda}'{$this->horario_final}'";
                $gruda = ", ";
            }else{
                $campos .= "{$gruda}horario_final";
                $valores .= "{$gruda}NULL";
                $gruda = ", ";
            }
            if(is_string($this->situacao))
            {
                $campos .= "{$gruda}situacao";
                $valores .= "{$gruda}'{$this->situacao}'";
                $gruda = ", ";
            }
            if(dbBool($this->via_judicial))
            {
                $campos .= "{$gruda}via_judicial";
                $valores .= "{$gruda}true";
                $gruda = ", ";
            }else{
                $campos .= "{$gruda}via_judicial";
                $valores .= "{$gruda}false";
                $gruda = ", ";
            }
            if(is_string($this->via_judicial_doc))
            {
                $campos .= "{$gruda}via_judicial_doc";
                $valores .= "{$gruda}'{$this->via_judicial_doc}'";
                $gruda = ", ";
            }

            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ", ";

            return $db->campoUnico("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores ) RETURNING cod_candidato_fila_unica");
        }
        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    function edita()
    {
        if(is_numeric($this->cod_candidato_fila_unica)
           && is_numeric($this->ref_cod_aluno))
        {
            $db = new clsBanco();
            $set = "";

            if(is_numeric($this->ref_cod_serie))
            {
                $set .= "{$gruda}ref_cod_serie = {$this->ref_cod_serie}";
                $gruda = ", ";
            }
            if(is_numeric($this->ref_cod_turno))
            {
                $set .= "{$gruda}ref_cod_turno = {$this->ref_cod_turno}";
                $gruda = ", ";
            }
            if(is_numeric($this->ref_cod_pessoa_exc))
            {
                $set .= "{$gruda}ref_cod_pessoa_exc = {$this->ref_cod_pessoa_exc}";
                $gruda = ", ";
            }
            if(is_numeric($this->ref_cod_matricula))
            {
                $set .= "{$gruda}ref_cod_matricula = {$this->ref_cod_matricula}";
                $gruda = ", ";
            }
            if(is_numeric($this->ano_letivo))
            {
                $set .= "{$gruda}ano_letivo = {$this->ano_letivo}";
                $gruda = ", ";
            }
            if(is_string($this->data_exclusao))
            {
                $set .= "{$gruda}data_exclusao = '{$this->data_exclusao}'";
                $gruda = ", ";
            }
            if(is_string($this->data_solicitacao) && !empty($this->data_solicitacao))
            {
                $set .= "{$gruda}data_solicitacao = '{$this->data_solicitacao}'";
                $gruda = ", ";
            }
            if(is_string($this->hora_solicitacao) && !empty($this->hora_solicitacao))
            {
                $set .= "{$gruda}hora_solicitacao = '{$this->hora_solicitacao}'";
                $gruda = ", ";
            }else{
                $set .= "{$gruda}hora_solicitacao = NULL";
                $gruda = ', ';
            }
            if(is_string($this->horario_inicial) && !empty($this->horario_inicial))
            {
                $set .= "{$gruda}horario_inicial = '{$this->horario_inicial}'";
                $gruda = ", ";
            }else{
                $set .= "{$gruda}horario_inicial = NULL";
                $gruda = ', ';
            }
            if(is_string($this->horario_final) && !empty($this->horario_final))
            {
                $set .= "{$gruda}horario_final = '{$this->horario_final}'";
                $gruda = ", ";
            }else{
                $set .= "{$gruda}horario_final = NULL";
                $gruda = ', ';
            }
            if(is_string($this->situacao))
            {
                $set .= "{$gruda}situacao = '{$this->situacao}'";
                $gruda = ", ";
            }
            if(dbBool($this->via_judicial))
            {
                $set .= "{$gruda}via_judicial = true";
                $gruda = ", ";
            }else{
                $set .= "{$gruda}via_judicial = false";
                $gruda = ", ";
            }
            if(is_string($this->via_judicial_doc))
            {
                $set .= "{$gruda}via_judicial_doc = '{$this->via_judicial_doc}'";
                $gruda = ", ";
            }else{
                $set .= "{$gruda}via_judicial_doc = NULL";
                $gruda = ', ';
            }
            if(is_numeric($this->ativo))
            {
                $set .= "{$gruda}ativo = {$this->ativo}";
                $gruda = ", ";
            }

            if( $set )
            {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_candidato_fila_unica = {$this->cod_candidato_fila_unica}");
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
    function lista($nome = NULL,
                   $nome_responsavel = NULL,
                   $ref_cod_escola = NULL)
    {
        $sql = "SELECT {$this->_campos_lista},
                       p.nome,
                       f.data_nasc,
                       d.certidao_nascimento,
                       d.num_termo,
                       d.num_livro,
                       d.num_folha,
                       d.comprovante_residencia,
                       f.data_nasc,
                       (cfu.ano_letivo || to_char(cfu.cod_candidato_fila_unica, 'fm00000000')) AS protocolo,
                       (SELECT (replace(textcat_all(nome),' <br>',','))
                          FROM (SELECT p.nome
                                  FROM pmieducar.responsaveis_aluno ra
                                 INNER JOIN cadastro.pessoa p ON (p.idpes = ra.ref_idpes)
                                 WHERE ref_cod_aluno = cfu.ref_cod_aluno
                                 ORDER BY vinculo_familiar
                                 LIMIT 3) r) AS responsaveis
                  FROM {$this->_tabela} cfu";
        $sql .= " INNER JOIN pmieducar.aluno a ON (a.cod_aluno = cfu.ref_cod_aluno)
                  INNER JOIN cadastro.pessoa p ON (p.idpes = a.ref_idpes)
                  INNER JOIN cadastro.fisica f ON (f.idpes = a.ref_idpes)
                   LEFT JOIN cadastro.documento d ON (d.idpes = a.ref_idpes)";

        $filtros = "";

        $whereAnd = " WHERE ";

        if(is_numeric($this->cod_candidato_fila_unica) && empty($this->protocolo))
        {
            $filtros .= "{$whereAnd} cod_candidato_fila_unica = {$this->cod_candidato_fila_unica}";
            $whereAnd = " AND ";
        }
        if(is_numeric($this->ref_cod_aluno))
        {
            $filtros .= "{$whereAnd} ref_cod_aluno = {$this->ref_cod_aluno}";
            $whereAnd = " AND ";
        }
        if(is_numeric($this->ref_cod_serie))
        {
            $filtros .= "{$whereAnd} ref_cod_serie = {$this->ref_cod_serie}";
            $whereAnd = " AND ";
        }
        if(is_numeric($this->ref_cod_turno))
        {
            $filtros .= "{$whereAnd} ref_cod_turno = {$this->ref_cod_turno}";
            $whereAnd = " AND ";
        }
        if(is_numeric($this->ref_cod_pessoa_exc))
        {
            $filtros .= "{$whereAnd} ref_cod_pessoa_exc = {$this->ref_cod_pessoa_exc}";
            $whereAnd = " AND ";
        }
        if(is_numeric($this->ref_cod_matricula))
        {
            $filtros .= "{$whereAnd} ref_cod_matricula = {$this->ref_cod_matricula}";
            $whereAnd = " AND ";
        }
        if(is_numeric($this->ano_letivo) && empty($this->protocolo))
        {
            $filtros .= "{$whereAnd} ano_letivo = {$this->ano_letivo}";
            $whereAnd = " AND ";
        }
        if(is_string($this->data_exclusao))
        {
            $filtros .= "{$whereAnd} data_exclusao = '{$this->data_exclusao}'";
            $whereAnd = " AND ";
        }
        if(is_string($this->data_solicitacao))
        {
            $filtros .= "{$whereAnd} data_solicitacao = '{$this->data_solicitacao}'";
            $whereAnd = " AND ";
        }
        if(is_string($this->hora_solicitacao))
        {
            $filtros .= "{$whereAnd} hora_solicitacao = '{$this->hora_solicitacao}'";
            $whereAnd = " AND ";
        }
        if(is_string($this->data_nasc))
        {
            $filtros .= "{$whereAnd} f.data_nasc = '{$this->data_nasc}'";
            $whereAnd = " AND ";
        }
        if(is_string($this->horario_inicial))
        {
            $filtros .= "{$whereAnd} horario_inicial = '{$this->horario_inicial}'";
            $whereAnd = " AND ";
        }
        if(is_string($this->horario_final))
        {
            $filtros .= "{$whereAnd} horario_final = '{$this->horario_final}'";
            $whereAnd = " AND ";
        }
        if(is_string($this->situacao))
        {
            $filtros .= "{$whereAnd} situacao = '{$this->situacao}'";
            $whereAnd = " AND ";
        }
        if(dbBool($this->via_judicial))
        {
            $filtros .= "{$whereAnd} via_judicial = true";
            $whereAnd = " AND ";
        }else{
            $set .= "{$gruda}via_judicial = false";
            $gruda = ", ";
        }
        if(is_string($this->via_judicial_doc))
        {
            $filtros .= "{$whereAnd} via_judicial_doc = '{$this->via_judicial_doc}'";
            $whereAnd = " AND ";
        }
        if(is_numeric($this->ativo))
        {
            $filtros .= "{$whereAnd} cfu.ativo = '{$this->ativo}'";
            $whereAnd = " AND ";
        }
        if(is_string($nome))
        {
            $filtros .= "{$whereAnd} upper(nome) LIKE upper('%{$nome}%')";
            $whereAnd = " AND ";
        }
        if(is_numeric($ref_cod_escola))
        {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                               FROM pmieducar.escola_candidato_fila_unica
                                              WHERE ref_cod_candidato_fila_unica = cod_candidato_fila_unica
                                                AND ref_cod_escola = {$ref_cod_escola})";
            $whereAnd = " AND ";
        }
        if(is_string($nome_responsavel)){
            $filtros .= "{$whereAnd} (SELECT upper(replace(textcat_all(nome),' <br>',','))
                                        FROM (SELECT p.nome
                                                FROM pmieducar.responsaveis_aluno ra
                                               INNER JOIN cadastro.pessoa p ON (p.idpes = ra.ref_idpes)
                                               WHERE ref_cod_aluno = cfu.ref_cod_aluno
                                               ORDER BY vinculo_familiar
                                               LIMIT 3) r) LIKE upper('%{$nome_responsavel}%')";
        }
        if (is_numeric($this->protocolo)) {
            $protocolo = $this->protocolo;
            $ano_letivo = substr($protocolo, 0, 4);
            $cod_candidato_fila_unica = substr_replace($protocolo, '', 0, 4) + 0;
            $filtros .= "{$whereAnd} cod_candidato_fila_unica = {$cod_candidato_fila_unica}";
            $filtros .= "{$whereAnd} ano_letivo = {$ano_letivo}";
            $whereAnd = " AND ";
        }

        $db = new clsBanco();
        $countCampos = count( explode( ",", $this->_campos_lista ) );
        $resultado = array();

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0)
                                            FROM {$this->_tabela} cfu
                                            INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = cfu.ref_cod_aluno)
                                            INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                                            INNER JOIN cadastro.fisica f ON (f.idpes = aluno.ref_idpes) {$filtros}");

        $db->Consulta( $sql );

        if( $countCampos > 1 )
        {
            while ( $db->ProximoRegistro() )
            {
                $tupla = $db->Tupla();

                $tupla["_total"] = $this->_total;
                $resultado[] = $tupla;
            }
        }
        else
        {
            while ( $db->ProximoRegistro() )
            {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if( count( $resultado ) )
        {
            return $resultado;
        }
        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    function detalhe()
    {
        if(is_numeric($this->cod_candidato_fila_unica))
        {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos},
                                  (cfu.ano_letivo || to_char(cfu.cod_candidato_fila_unica, 'fm00000000')) AS protocolo,
                                  p.nome,
                                  f.data_nasc,
                                  s.nm_serie,
                                  d.certidao_nascimento,
                                  d.num_termo,
                                  d.num_folha,
                                  d.num_livro,
                                  d.comprovante_residencia,
                                  (SELECT (replace(textcat_all(nome),' <br>',','))
                                     FROM (SELECT p.nome
                                             FROM pmieducar.responsaveis_aluno ra
                                            INNER JOIN cadastro.pessoa p ON (p.idpes = ra.ref_idpes)
                                            WHERE ref_cod_aluno = cfu.ref_cod_aluno
                                            ORDER BY vinculo_familiar) r) AS responsaveis,
                                  (SELECT textcat_all(relatorio.get_nome_escola(ref_cod_escola))
                                     FROM (SELECT ref_cod_escola
                                             FROM pmieducar.escola_candidato_fila_unica ecfu
                                            WHERE ref_cod_candidato_fila_unica = cfu.cod_candidato_fila_unica
                                            ORDER BY sequencial) e) AS escolas
                             FROM {$this->_tabela} cfu
                            INNER JOIN pmieducar.aluno a ON (a.cod_aluno = cfu.ref_cod_aluno)
                            INNER JOIN cadastro.pessoa p ON (p.idpes = a.ref_idpes)
                            INNER JOIN cadastro.fisica f ON (f.idpes = a.ref_idpes)
                            INNER JOIN pmieducar.serie s ON (s.cod_serie = cfu.ref_cod_serie)
                             LEFT JOIN cadastro.documento d ON (d.idpes = a.ref_idpes)
                            WHERE cod_candidato_fila_unica = {$this->cod_candidato_fila_unica}");
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
    function existe()
    {
        if( is_numeric($this->cod_candidato_fila_unica))
        {
            $db = new clsBanco();
            $db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_canddidato_fila_unica = '{$this->cod_canddidato_fila_unica}'" );
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
    function excluir()
    {
        if( is_numeric($this->cod_canddidato_fila_unica) && is_numeric($this->ref_cod_pessoa_exc))
        {
            $this->ativo = 0;
            return $this->edita();
        }
        return false;
    }

    /**
     * Define quais campos da tabela serao selecionados na invocacao do metodo lista
     *
     * @return null
     */
    function setCamposLista( $str_campos )
    {
        $this->_campos_lista = $str_campos;
    }

    /**
     * Define que o metodo Lista devera retornoar todos os campos da tabela
     *
     * @return null
     */
    function resetCamposLista()
    {
        $this->_campos_lista = $this->_todos_campos;
    }

    /**
     * Define limites de retorno para o metodo lista
     *
     * @return null
     */
    function setLimite( $intLimiteQtd, $intLimiteOffset = null )
    {
        $this->_limite_quantidade = $intLimiteQtd;
        $this->_limite_offset = $intLimiteOffset;
    }

    /**
     * Retorna a string com o trecho da query resposavel pelo Limite de registros
     *
     * @return string
     */
    function getLimite()
    {
        if( is_numeric( $this->_limite_quantidade ) )
        {
            $retorno = " LIMIT {$this->_limite_quantidade}";
            if( is_numeric( $this->_limite_offset ) )
            {
                $retorno .= " OFFSET {$this->_limite_offset} ";
            }
            return $retorno;
        }
        return "";
    }

    /**
     * Define campo para ser utilizado como ordenacao no metolo lista
     *
     * @return null
     */
    function setOrderby( $strNomeCampo )
    {
        // limpa a string de possiveis erros (delete, insert, etc)
        //$strNomeCampo = eregi_replace();

        if( is_string( $strNomeCampo ) && $strNomeCampo )
        {
            $this->_campo_order_by = $strNomeCampo;
        }
    }

    /**
     * Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
     *
     * @return string
     */
    function getOrderby()
    {
        if( is_string( $this->_campo_order_by ) )
        {
            return " ORDER BY {$this->_campo_order_by} ";
        }
        return "";
    }

    function indefereCandidatura($motivo = null) {
        $motivo = $motivo == null ? "null" : "'". $motivo ."'";

        if (is_numeric($this->cod_candidato_fila_unica)) {
            $db = new clsBanco();
            $db->Consulta("UPDATE pmieducar.candidato_fila_unica SET situacao = 'I', motivo = $motivo, data_situacao = NOW()
                            WHERE cod_candidato_fila_unica = '{$this->cod_candidato_fila_unica}'");
            $db->ProximoRegistro();
            return $db->Tupla();
        }
        return FALSE;
    }

    function vinculaMatricula($ref_cod_matricula) {
        if (is_numeric($ref_cod_matricula)) {
            $db = new clsBanco();
            $db->Consulta("UPDATE pmieducar.candidato_fila_unica SET ref_cod_matricula = '{$ref_cod_matricula}', situacao = 'A', data_situacao = NOW()
                      WHERE cod_candidato_fila_unica = '{$this->cod_candidato_fila_unica}'");
            $db->ProximoRegistro();
            return $db->Tupla();
        }
        return FALSE;
    }

    public function alteraSituacao($situacao, $motivo = null)
    {
        if (!$this->cod_candidato_fila_unica) {
            return false;
        }

        $situacao = $situacao ?: 'NULL';
        $motivo = $motivo ?: 'NULL';

        $db = new clsBanco();
        $db->Consulta("UPDATE pmieducar.candidato_fila_unica
                                   SET situacao = {$situacao},
                                       motivo = {$motivo},
                                       data_situacao = NOW()
                                 WHERE cod_candidato_fila_unica = '{$this->cod_candidato_fila_unica}'");

        return true;
    }

    /**
     * Retorna um array com os códigos das escolas em que o aluno está
     * aguardando na fila.
     *
     * @param int $cod_candidato_fila_unica
     *
     * @return array
     *
     * @throws Exception
     */
    public function getOpcoesDeEscolas($cod_candidato_fila_unica)
    {
        $db = new clsBanco();

        $db->Consulta(
            "
                SELECT ref_cod_escola
                FROM pmieducar.escola_candidato_fila_unica
                WHERE ref_cod_candidato_fila_unica = {$cod_candidato_fila_unica}
                ORDER BY sequencial;
            "
        );

        $escolas = [];

        while ($db->ProximoRegistro()) {
            $escolas[] = $db->Tupla()['ref_cod_escola'];
        }

        return $escolas;
    }
}
?>
