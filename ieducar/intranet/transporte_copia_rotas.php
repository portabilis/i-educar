<?php
/*
--
-- @author   Isac Borgert <isac@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
*/

error_reporting(E_ERROR);
ini_set("display_errors", 1);

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

require_once 'include/modules/clsModulesItinerarioTransporteEscolar.inc.php';
require_once("include/modules/clsModulesRotaTransporteEscolar.inc.php");
require_once("include/modules/clsModulesEmpresaTransporteEscolar.inc.php");
class clsIndexBase extends clsBase{
    function Formular(){
        $this->SetTitulo( "{$this->_instituicao} i-Educar - C&oacute;pia de Rotas" );
        $this->processoAp = "21240";
    }
}

class indice extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    var $nome_url_sucesso = "Efetuar c&oacute;pia";

    function Inicializar(){

        $retorno = "Novo";
        


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 950, $this->pessoa_logada, 7);

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_transporte_escolar_index.php"                  => "Transporte escolar",
             ""                                  => "C&oacute;pia de rotas"
        ));
        $this->enviaLocalizacao($localizacao->montar());

        return $retorno;
    }

    function Gerar(){

        $empresas = array( "" => "Selecione" );
        $anos_origem = array( "" => "Selecione" );
        $objTemp = new clsModulesEmpresaTransporteEscolar();
        $objTemp->setOrderby(' nome_empresa ASC');
        $lista = $objTemp->lista();
        if ( is_array( $lista ) && count( $lista ) ){
            foreach ( $lista as $registro ){
                $empresas["{$registro['cod_empresa_transporte_escolar']}"] = "{$registro['nome_empresa']}";
            }
        }else{
            $empresas = array( "" => "Sem empresas cadastradas" );
        }
        $obj_rota = new clsModulesRotaTransporteEscolar();
        $obj_rota->setOrderby( " descricao ASC" );
        $obj_rota->setLimite( $this->limite, $this->offset );

        $lista = $obj_rota->lista(
            null,
            $this->descricao,
            null,
            $this->nome_destino,
            $this->ano,
            null
        );
        if ( is_array( $lista ) && count( $lista ) ){
            foreach ( $lista as $registro ){
                $anos_origem["{$registro['ano']}"] = "{$registro['ano']}";
            }
        }else{
            $anos_origem = array( "" => "N&atilde;o existe rotas anteriores" );
        }
        $this->campoLista( "ref_cod_empresa_transporte_escolar", "Empresa", $empresas, $this->ref_cod_empresa_transporte_escolar, "", false, "", "", false, false );
        $this->campoLista( "ano_orig", "Ano de origem", $anos_origem, $this->ano_orig, "", false, "", "", false, false );
        $this->campoNumero('ano_dest','Ano de destino',$this->ano_dest,4,5);
    }

    function Novo(){

        

        if (!$this->ano_orig or !$this->ref_cod_empresa_transporte_escolar or !$this->ano_dest){
            $this->mensagem = "Preencha os dados corretamente.<br>";
            return false;
        }

        $obj_rota = new clsModulesRotaTransporteEscolar();
        $obj_rota->setOrderby( " descricao ASC" );
        $obj_rota->setLimite( $this->limite, $this->offset );

        $lista = $obj_rota->lista(
            null,
            null,
            null,
            null,
            $this->ano_orig,
            $this->ref_cod_empresa_transporte_escolar
        );
        if ( is_array( $lista ) && count( $lista ) ){
            $obj_rota = new clsModulesRotaTransporteEscolar();
                $obj_rota->setOrderby( " descricao ASC" );
                $obj_rota->setLimite( $this->limite, $this->offset );
                $lista_new_rota = $obj_rota->lista(
                    null,
                    null,
                    null,
                    null,
                    $this->ano_dest,
                    $this->ref_cod_empresa_transporte_escolar
                );//verificar se a ampresa ja tem rotas no ano destino.
            if (!$lista_new_rota){
                foreach ( $lista as $registro ){
                    $db = new clsBanco();
                    $this->_schema = "modules.";
                    $this->_tabela = "{$this->_schema}rota_transporte_escolar";

                    $campos  = '';
                    $valores = '';
                    $gruda   = '';

                    if (is_numeric($registro['ref_idpes_destino'])) {
                        $campos .= "{$gruda}ref_idpes_destino";
                        $valores .= "{$gruda}'{$registro['ref_idpes_destino']}'";
                        $gruda = ", ";
                    }
                    if (is_string($registro['descricao'])) {
                        $campos .= "{$gruda}descricao";
                        $valores .= "{$gruda}'{$registro['descricao']}'";
                        $gruda = ", ";
                    }
                    if (is_numeric($this->ano_dest)) {
                        $campos .= "{$gruda}ano";
                        $valores .= "{$gruda}'{$this->ano_dest}'";
                        $gruda = ", ";
                    }
                    if (is_string($registro['tipo_rota'])) {
                        $campos .= "{$gruda}tipo_rota";
                        $valores .= "{$gruda}'{$registro['tipo_rota']}'";
                        $gruda = ", ";
                    }
                    if (is_numeric($registro['km_pav'])) {
                        $campos .= "{$gruda}km_pav";
                        $valores .= "{$gruda}'{$registro['km_pav']}'";
                        $gruda = ", ";
                    }
                    if (is_numeric($registro['km_npav'])) {
                        $campos .= "{$gruda}km_npav";
                        $valores .= "{$gruda}'{$registro['km_npav']}'";
                        $gruda = ", ";
                    }
                    if (is_numeric($this->ref_cod_empresa_transporte_escolar)) {
                        $campos .= "{$gruda}ref_cod_empresa_transporte_escolar";
                        $valores .= "{$gruda}'{$this->ref_cod_empresa_transporte_escolar}'";
                        $gruda = ", ";
                    }
                    if (is_string($registro['tercerizado'])) {
                        $campos .= "{$gruda}tercerizado";
                        $valores .= "{$gruda}'{$registro['tercerizado']}'";
                        $gruda = ", ";
                    }

                    $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

                    $this->cod_rota_transporte_escolar = $db->InsertId("{$this->_tabela}_seq");

                    if($this->cod_rota_transporte_escolar){
                        $objRota = new clsModulesRotaTransporteEscolar($this->cod_rota_transporte_escolar);
                        $detalhe = $objRota->detalhe();
                        $auditoria = new clsModulesAuditoriaGeral("rota_transporte_escolar", $this->pessoa_logada, $this->cod_rota_transporte_escolar);
                        $auditoria->inclusao($detalhe);
                    }
                    // return $db->InsertId("{$this->_tabela}_seq");
                }
                $obj_rota = new clsModulesRotaTransporteEscolar();
                $obj_rota->setOrderby( " descricao ASC" );
                $obj_rota->setLimite( $this->limite, $this->offset );
                $lista_new_rota = $obj_rota->lista(
                    null,
                    null,
                    null,
                    null,
                    $this->ano_dest,
                    $this->ref_cod_empresa_transporte_escolar
                );//pega as rotas novas.


                $num = 0;
                foreach ($lista as $registro) {
                    $cod_rota_nova = $lista_new_rota[$num]['cod_rota_transporte_escolar'];
                    $obj = new clsModulesItinerarioTransporteEscolar();
                    $intinerario_old = $obj->lista(NULL, $registro['cod_rota_transporte_escolar']);  //pega os intinerários antigos
                    $num2 = 0;
                    foreach($intinerario_old as $intinerario){
                        $intinerario_old[$num2]['ref_cod_rota_transporte_escolar'] = $cod_rota_nova; //substitui o cod das rotas antigas pelos novos
                        $num2++;
                    }
                    foreach ( $intinerario_old as $registro ){
                            $obj = new clsModulesItinerarioTransporteEscolar(null,
                                    $registro['ref_cod_rota_transporte_escolar'],
                                    $registro['seq'],
                                    $registro['ref_cod_ponto_transporte_escolar'],
                                    $registro['ref_cod_veiculo'],
                                    $registro['hora'],
                                    $registro['tipo']);
                            $obj->cadastra(); //grava os novos intinerários no banco
                    }
                    $num++;
                }
            $this->mensagem = Portabilis_String_Utils::toLatin1("Cópia efetuada com sucesso");
            return true;

            }else{
                $this->mensagem = Portabilis_String_Utils::toLatin1("A empresa já possuí­ rotas em {$this->ano_dest}");
                return false;
            }
        }else{
            $this->mensagem = Portabilis_String_Utils::toLatin1("Não existe rotas em $this->ano_orig para essa empresa");
            return false;
        }
    }
    protected function flashMessage() {

        if (strpos($this->mensagem, 'sucesso'))
            return empty($this->mensagem) ? "" : "<p class='form_erro success'>$this->mensagem</p>";
        else
            return empty($this->mensagem) ? "" : "<p class='form_erro error'>$this->mensagem</p>";
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
