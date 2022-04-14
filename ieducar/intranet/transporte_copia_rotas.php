<?php

error_reporting(E_ERROR);
ini_set('display_errors', 1);

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $nome_url_sucesso = 'Efetuar c&oacute;pia';

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(950, $this->pessoa_logada, 7);

        $this->breadcrumb('C&oacute;pia de rotas', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);

        return $retorno;
    }

    public function Gerar()
    {
        $empresas = [ '' => 'Selecione' ];
        $anos_origem = [ '' => 'Selecione' ];
        $objTemp = new clsModulesEmpresaTransporteEscolar();
        $objTemp->setOrderby(' nome_empresa ASC');
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $empresas["{$registro['cod_empresa_transporte_escolar']}"] = "{$registro['nome_empresa']}";
            }
        } else {
            $empresas = [ '' => 'Sem empresas cadastradas' ];
        }
        $obj_rota = new clsModulesRotaTransporteEscolar();
        $obj_rota->setOrderby(' descricao ASC');
        $obj_rota->setLimite($this->limite, $this->offset);

        $lista = $obj_rota->lista(
            null,
            $this->descricao,
            null,
            $this->nome_destino,
            $this->ano,
            null
        );
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $anos_origem["{$registro['ano']}"] = "{$registro['ano']}";
            }
        } else {
            $anos_origem = [ '' => 'N&atilde;o existe rotas anteriores' ];
        }
        $this->campoLista('ref_cod_empresa_transporte_escolar', 'Empresa', $empresas, $this->ref_cod_empresa_transporte_escolar, '', false, '', '', false, false);
        $this->campoLista('ano_orig', 'Ano de origem', $anos_origem, $this->ano_orig, '', false, '', '', false, false);
        $this->campoNumero('ano_dest', 'Ano de destino', $this->ano_dest, 4, 5);
    }

    public function Novo()
    {
        if (!$this->ano_orig or !$this->ref_cod_empresa_transporte_escolar or !$this->ano_dest) {
            $this->mensagem = 'Preencha os dados corretamente.<br>';

            return false;
        }

        $obj_rota = new clsModulesRotaTransporteEscolar();
        $obj_rota->setOrderby(' descricao ASC');
        $obj_rota->setLimite($this->limite, $this->offset);

        $lista = $obj_rota->lista(
            null,
            null,
            null,
            null,
            $this->ano_orig,
            $this->ref_cod_empresa_transporte_escolar
        );
        if (is_array($lista) && count($lista)) {
            $obj_rota = new clsModulesRotaTransporteEscolar();
            $obj_rota->setOrderby(' descricao ASC');
            $obj_rota->setLimite($this->limite, $this->offset);
            $lista_new_rota = $obj_rota->lista(
                null,
                null,
                null,
                null,
                $this->ano_dest,
                $this->ref_cod_empresa_transporte_escolar
            );//verificar se a ampresa ja tem rotas no ano destino.
            if (!$lista_new_rota) {
                foreach ($lista as $registro) {
                    $db = new clsBanco();
                    $this->_schema = 'modules.';
                    $this->_tabela = "{$this->_schema}rota_transporte_escolar";

                    $campos  = '';
                    $valores = '';
                    $gruda   = '';

                    if (is_numeric($registro['ref_idpes_destino'])) {
                        $campos .= "{$gruda}ref_idpes_destino";
                        $valores .= "{$gruda}'{$registro['ref_idpes_destino']}'";
                        $gruda = ', ';
                    }
                    if (is_string($registro['descricao'])) {
                        $campos .= "{$gruda}descricao";
                        $valores .= "{$gruda}'{$registro['descricao']}'";
                        $gruda = ', ';
                    }
                    if (is_numeric($this->ano_dest)) {
                        $campos .= "{$gruda}ano";
                        $valores .= "{$gruda}'{$this->ano_dest}'";
                        $gruda = ', ';
                    }
                    if (is_string($registro['tipo_rota'])) {
                        $campos .= "{$gruda}tipo_rota";
                        $valores .= "{$gruda}'{$registro['tipo_rota']}'";
                        $gruda = ', ';
                    }
                    if (is_numeric($registro['km_pav'])) {
                        $campos .= "{$gruda}km_pav";
                        $valores .= "{$gruda}'{$registro['km_pav']}'";
                        $gruda = ', ';
                    }
                    if (is_numeric($registro['km_npav'])) {
                        $campos .= "{$gruda}km_npav";
                        $valores .= "{$gruda}'{$registro['km_npav']}'";
                        $gruda = ', ';
                    }
                    if (is_numeric($this->ref_cod_empresa_transporte_escolar)) {
                        $campos .= "{$gruda}ref_cod_empresa_transporte_escolar";
                        $valores .= "{$gruda}'{$this->ref_cod_empresa_transporte_escolar}'";
                        $gruda = ', ';
                    }
                    if (is_string($registro['tercerizado'])) {
                        $campos .= "{$gruda}tercerizado";
                        $valores .= "{$gruda}'{$registro['tercerizado']}'";
                        $gruda = ', ';
                    }

                    $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

                    $this->cod_rota_transporte_escolar = $db->InsertId("{$this->_tabela}_seq");
                }
                $obj_rota = new clsModulesRotaTransporteEscolar();
                $obj_rota->setOrderby(' descricao ASC');
                $obj_rota->setLimite($this->limite, $this->offset);
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
                    $intinerario_old = $obj->lista(null, $registro['cod_rota_transporte_escolar']);  //pega os intinerários antigos
                    $num2 = 0;
                    foreach ($intinerario_old as $intinerario) {
                        $intinerario_old[$num2]['ref_cod_rota_transporte_escolar'] = $cod_rota_nova; //substitui o cod das rotas antigas pelos novos
                        $num2++;
                    }
                    foreach ($intinerario_old as $registro) {
                        $obj = new clsModulesItinerarioTransporteEscolar(
                            null,
                            $registro['ref_cod_rota_transporte_escolar'],
                            $registro['seq'],
                            $registro['ref_cod_ponto_transporte_escolar'],
                            $registro['ref_cod_veiculo'],
                            $registro['hora'],
                            $registro['tipo']
                        );
                        $obj->cadastra(); //grava os novos intinerários no banco
                    }
                    $num++;
                }
                $this->mensagem = 'Cópia efetuada com sucesso';

                return true;
            } else {
                $this->mensagem = "A empresa já possuí­ rotas em {$this->ano_dest}";

                return false;
            }
        } else {
            $this->mensagem ="Não existe rotas em $this->ano_orig para essa empresa";

            return false;
        }
    }
    protected function flashMessage()
    {
        if (strpos($this->mensagem, 'sucesso')) {
            return empty($this->mensagem) ? '' : "<p class='form_erro success'>$this->mensagem</p>";
        } else {
            return empty($this->mensagem) ? '' : "<p class='form_erro error'>$this->mensagem</p>";
        }
    }

    public function Formular()
    {
        $this->title = 'C&oacute;pia de Rotas';
        $this->processoAp = '21240';
    }
};
