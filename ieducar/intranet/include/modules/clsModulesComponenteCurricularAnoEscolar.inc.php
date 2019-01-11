<?php
// error_reporting(E_ERROR);
// ini_set("display_errors", 1);
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
* Criado em 10/08/2006 17:11 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );
require_once( "lib/Portabilis/Utils/Database.php" );

class clsModulesComponenteCurricularAnoEscolar
{
    var $componente_curricular_id;
    var $ano_escolar_id;
    var $carga_horaria;
    var $tipo_nota;
    var $componentes;
    var $updateInfo;

    // propriedades padrao
    var $_total; // Armazena o total de resultados obtidos na ultima chamada ao metodo lista
    var $_schema; // Nome do schema
    var $_tabela; // Nome da tabela
    var $_campos_lista; // Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
    var $_todos_campos; // Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
    var $_limite_quantidade; // Valor que define a quantidade de registros a ser retornada pelo metodo lista
    var $_limite_offset; // Define o valor de offset no retorno dos registros no metodo lista
    var $_campo_order_by; // Define o campo padrao para ser usado como padrao de ordenacao no metodo lista


    function __construct($componente_curricular_id = NULL,
                                                      $ano_escolar_id           = NULL,
                                                      $carga_horaria            = NULL,
                                                      $tipo_nota                = NULL,
                                                      $componentes              = NULL,
                                                      $updateInfo               = NULL)
    {
        $this->_schema = "modules.";
        $this->_tabela = "{$this->_schema}componente_curricular_ano_escolar";

        $this->_campos_lista = $this->_todos_campos = "componente_curricular_id,
                                                       ano_escolar_id,
                                                       carga_horaria,
                                                       array_to_json(anos_letivos) as anos_letivos ";
        if(is_numeric($componente_curricular_id)){
            $this->componente_curricular_id = $componente_curricular_id;
        }
        if (is_numeric($ano_escolar_id)) {
            $this->ano_escolar_id = $ano_escolar_id;
        }
        if (is_numeric($carga_horaria)) {
            $this->$carga_horaria = $carga_horaria;
        }
        if (is_numeric($tipo_nota)) {
            $this->$tipo_nota = $tipo_nota;
        }
        if (is_array($componentes)) {
            $this->componentes = $componentes;
        }
        if(is_array($updateInfo)){
            $this->updateInfo = $updateInfo;
        }
    }

    function atualizaComponentesDaSerie(){

        $this->updateInfo();

        if ($this->updateInfo['update']) {
            foreach ($this->updateInfo['update'] as $componenteUpdate) {
                $this->editaComponente(intval($componenteUpdate['id']),
                                       intval($componenteUpdate['carga_horaria']),
                                       intval($componenteUpdate['tipo_nota']),
                                       $componenteUpdate['anos_letivos']);
            }
        }

        if ($this->updateInfo['insert']) {
            foreach ($this->updateInfo['insert'] as $componenteInsert) {
                $this->cadastraComponente(intval($componenteInsert['id']),
                                          intval($componenteInsert['carga_horaria']),
                                          intval($componenteInsert['tipo_nota']),
                                          $componenteInsert['anos_letivos']);
            }
        }

        if ($this->updateInfo['delete']) {
            foreach ($this->updateInfo['delete'] as $componenteDelete) {
                $this->excluiComponente(intval($componenteDelete));
            }
        }

        return true;

    }

    function updateInfo(){

        $c = $u = $i = $d = 0;

        foreach ($this->componentes as $componente) {
            $componentesArray[$c] = $componente['id'];
            $c++;
            if (in_array($componente['id'], $this->getComponentesSerie())) {
                $this->updateInfo['update'][$u]['id'] = $componente['id'];
                $this->updateInfo['update'][$u]['carga_horaria'] = $componente['carga_horaria'];
                $this->updateInfo['update'][$u]['tipo_nota'] = $componente['tipo_nota'];
                $this->updateInfo['update'][$u]['anos_letivos'] = $componente['anos_letivos'];
                $this->updateInfo['update'][$u]['anos_letivos_inseridos'] = $this->getAnosLetivosInseridos($componente['id'], $componente['anos_letivos']);
                $u++;
            }else{
                $this->updateInfo['insert'][$i]['id'] = $componente['id'];
                $this->updateInfo['insert'][$i]['carga_horaria'] = $componente['carga_horaria'];
                $this->updateInfo['insert'][$i]['tipo_nota'] = $componente['tipo_nota'];
                $this->updateInfo['insert'][$i]['anos_letivos'] = $componente['anos_letivos'];
                $i++;
            }
        }

        foreach ($this->getComponentesSerie() as $componente) {
            if (!in_array($componente, $componentesArray)) {
                $this->updateInfo['delete'][$d] = $componente;
                $d++;
            }
        }

        return $this->updateInfo;
    }

    function getComponentesSerie(){

        $sql = "SELECT componente_curricular_id
                  FROM {$this->_tabela}
                 WHERE ano_escolar_id = {$this->ano_escolar_id}";

        $db = new clsBanco();
        $db->Consulta( $sql );

        while ( $db->ProximoRegistro() )
        {
            $tupla = $db->Tupla();
            $componentesSerie[] = $tupla['componente_curricular_id'];
        }

        if ($componentesSerie) {
            return $componentesSerie;
        }

        return false;
    }

    private function getAnosLetivosInseridos($componenteCurricularId, $arrayAnosLetivos)
    {
        $sql = <<<SQL
                SELECT array_to_json(anos_letivos) as anos_letivos
                  FROM {$this->_tabela}
                 WHERE ano_escolar_id = {$this->ano_escolar_id}
                   AND componente_curricular_id = {$componenteCurricularId}
SQL;
        $db = new clsBanco();
        $db->Consulta($sql);
        $db->ProximoRegistro();

        $resultado = $db->Tupla();

        if (empty($resultado) || !isset($resultado['anos_letivos'])) {
            return [];
        }

        $anosLetivosExistentes = json_decode($resultado['anos_letivos'], true);

        foreach (array_diff($arrayAnosLetivos, $anosLetivosExistentes) as $ano) {
            $retorno[] = $ano;
        }

        return $retorno;

        return array_diff($arrayAnosLetivos, $anosLetivosExistentes);
    }

    private function cadastraComponente($componente_curricular_id = NULL,
                                        $carga_horaria            = NULL,
                                        $tipo_nota                = NULL,
                                        $anosLetivos              = NULL)
    {
        if(is_numeric($componente_curricular_id) && is_numeric($carga_horaria)){

            $db = new clsBanco();
            $tipo_nota = (int) $tipo_nota;
            $tipo_nota = $tipo_nota === 0 ? 'NULL' : $tipo_nota;

            $sql = "INSERT INTO {$this->_tabela} VALUES( $componente_curricular_id,
                                                         $this->ano_escolar_id,
                                                         $carga_horaria,
                                                         $tipo_nota,
                                                         " . Portabilis_Utils_Database::arrayToPgArray($anosLetivos) . "
                                                     )";
            $db->Consulta( $sql );

            return true;
        }

        return false;
    }

    private function editaComponente($componente_curricular_id = NULL,
                                     $carga_horaria            = NULL,
                                     $tipo_nota                = NULL,
                                     $anosLetivos              = NULL)
    {
        $db = new clsBanco();
        $set = "";

        if(is_numeric($componente_curricular_id)){

            if( is_numeric( $carga_horaria ) )
            {
                $set .= "{$gruda}carga_horaria = {$carga_horaria}";
                $gruda = ", ";
            }

            if( is_numeric( $tipo_nota ))
            {
                $tipo_nota = (int) $tipo_nota;
                $tipo_nota = $tipo_nota === 0 ? 'NULL' : $tipo_nota;

                $set .= "{$gruda}tipo_nota = {$tipo_nota}";
                $gruda = ", ";
            }

            if( is_array( $anosLetivos ) )
            {
                $set .= "{$gruda}anos_letivos = " . Portabilis_Utils_Database::arrayToPgArray($anosLetivos) . ' ';
                $gruda = ", ";
            }

            if( $set )
            {
                $sql = "UPDATE {$this->_tabela}
                           SET $set
                         WHERE componente_curricular_id = {$componente_curricular_id}
                           AND ano_escolar_id           = {$this->ano_escolar_id}";

                $db->Consulta( $sql );
                return true;
            }
        }

        return false;
    }

    private function excluiComponente($componente_curricular_id = NULL)
    {
        if(is_numeric($componente_curricular_id)){

            $db = new clsBanco();

            $sql = "DELETE FROM {$this->_tabela}
                     WHERE componente_curricular_id = {$componente_curricular_id}
                       AND ano_escolar_id = {$this->ano_escolar_id}";
            $db->Consulta( $sql );

            return true;
        }

        return false;
    }

    function cadastra(){
        if(is_numeric($this->componente_curricular_id) && is_numeric($this->ano_escolar_id)){

            $db = new clsBanco();

            $campos  = '';
            $valores = '';
            $gruda   = '';

            if (is_numeric($this->ano_escolar_id)) {
                $campos .= "{$gruda}ano_escolar_id";
                $valores .= "{$gruda}'{$this->ano_escolar_id}'";
                $gruda = ", ";
            }

            if (is_numeric($this->componente_curricular_id)) {
                $campos .= "{$gruda}componente_curricular_id";
                $valores .= "{$gruda}'{$this->componente_curricular_id}'";
                $gruda = ", ";
            }

            if (is_numeric($this->carga_horaria)) {
                $campos .= "{$gruda}carga_horaria";
                $valores .= "{$gruda}'{$this->carga_horaria}'";
                $gruda = ", ";
            }

            if (is_numeric($this->tipo_nota) && (int)$tipo_nota !== 0) {
                $campos .= "{$gruda}tipo_nota";
                $valores .= "{$gruda}'{$this->tipo_nota}'";
                $gruda = ", ";
            }

            $sql = "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )";

            $db->Consulta( $sql );

            return true;
        }

        return false;
    }

    function exclui()
    {
        if(is_numeric($this->ano_escolar_id)){

            $db = new clsBanco();

            $sql = "DELETE FROM {$this->_tabela}
                     WHERE ano_escolar_id = {$this->ano_escolar_id}";

            $db->Consulta( $sql );

            return true;
        }

        return false;
    }

    // Retorna uma lista filtrados de acordo com os parametros
    function lista( $componente_curricular_id = NULL,
                    $ano_escolar_id           = NULL,
                    $carga_horaria            = NULL,
                    $tipo_nota                = NULL)
    {
        $sql = "SELECT {$this->_campos_lista}
                  FROM {$this->_tabela}";
        $filtros = "";

        $whereAnd = " WHERE ";

        if( is_numeric( $componente_curricular_id ) )
        {
            $filtros .= "{$whereAnd} componente_curricular_id = {$componente_curricular_id}";
            $whereAnd = " AND ";
        }
        if( is_numeric( $ano_escolar_id ) )
        {
        $filtros .= "{$whereAnd} ano_escolar_id = {$ano_escolar_id}";
        $whereAnd = " AND ";
        }
        if( is_numeric( $carga_horaria ) )
        {
        $filtros .= "{$whereAnd} carga_horaria = {$carga_horaria}";
        $whereAnd = " AND ";
        }
        if( is_numeric( $tipo_nota ) )
        {
        $filtros .= "{$whereAnd} tipo_$tipo_nota = {$tipo_nota}";
        $whereAnd = " AND ";
        }

        $db = new clsBanco();
        $countCampos = count( explode( ",", $this->_campos_lista ) );
        $resultado = array();

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );

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

    // Define quais campos da tabela serao selecionados na invocacao do metodo lista
    function setCamposLista( $str_campos )
    {
        $this->_campos_lista = $str_campos;
    }

    // Define que o metodo Lista devera retornoar todos os campos da tabela
    function resetCamposLista()
    {
        $this->_campos_lista = $this->_todos_campos;
    }

    // Define limites de retorno para o metodo lista
    function setLimite( $intLimiteQtd, $intLimiteOffset = null )
    {
        $this->_limite_quantidade = $intLimiteQtd;
        $this->_limite_offset = $intLimiteOffset;
    }

    // Retorna a string com o trecho da query resposavel pelo Limite de registros
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

    // Define campo para ser utilizado como ordenacao no metolo lista
    function setOrderby( $strNomeCampo )
    {
        // limpa a string de possiveis erros (delete, insert, etc)
        // $strNomeCampo = eregi_replace();

        if( is_string( $strNomeCampo ) && $strNomeCampo )
        {
            $this->_campo_order_by = $strNomeCampo;
        }
    }

    // Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
    function getOrderby()
    {
        if( is_string( $this->_campo_order_by ) )
        {
            return " ORDER BY {$this->_campo_order_by} ";
        }
        return "";
    }

}
