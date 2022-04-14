<?php

use Illuminate\Support\Facades\Session;

return new class extends clsListagem {
    public $chave_campo;
    public $importarCpf;

    public function Gerar()
    {
        $this->nome = 'form1';

        if ($_GET['campos']) {
            $parametros = new clsParametrosPesquisas();
            $parametros->deserializaCampos($_GET['campos']);
            Session::put('campos', $parametros->geraArrayComAtributos());

            unset($_GET['campos']);
        } else {
            $parametros = new clsParametrosPesquisas();
            $parametros->preencheAtributosComArray(Session::get('campos'));
        }

        $submit = false;

        $this->addCabecalhos(['Matr&iacute;cula', 'CPF', 'Funcion&aacute;rio']);

        // Filtros de Busca
        $this->campoTexto('campo_busca', 'Funcionário', '', 50, 255, false, false, false, 'Matrícula/CPF/Nome do Funcionário');
        $this->campoOculto('com_matricula', $_GET['com_matricula']);

        if ($_GET['campo_busca']) {
            $chave_busca = @$_GET['campo_busca'];
        }

        if ($_GET['busca']) {
            $busca = @$_GET['busca'];
        }

        // Paginador
        $limite = 10;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite: 0;

        $this->chave_campo = $_GET['chave_campo'];
        $this->campoOculto('chave_campo', $this->chave_campo);
        if (is_numeric($this->chave_campo)) {
            $chave = "[$this->chave_campo]";
        } else {
            $chave = '';
        }

        $this->importarCpf = $_GET['importa_cpf'];

        if ($_GET['com_matricula']) {
            $com_matricula = null;
        } else {
            $com_matricula = true;
        }

        if ($busca == 'S') {
            $obj_funcionario = new clsFuncionario();
            $lst_funcionario = $obj_funcionario->lista(false, $chave_busca, false, false, false, false, false, $iniciolimit, $limite, false, $com_matricula);

            if (!$lst_funcionario) {
                $lst_funcionario = $obj_funcionario->lista($chave_busca, false, false, false, false, false, false, $iniciolimit, $limite, false, $com_matricula);
            }
        } else {
            $obj_funcionario = new clsFuncionario();
            $lst_funcionario = $obj_funcionario->lista(false, false, false, false, false, false, false, $iniciolimit, $limite, false, $com_matricula);
        }

        if ($lst_funcionario) {
            foreach ($lst_funcionario as $funcionario) {
                $obj_cod_servidor = new clsFuncionario($funcionario['ref_cod_pessoa_fj']);
                $det_cod_servidor = $obj_cod_servidor->detalhe();
                $det_cod_servidor = $det_cod_servidor['idpes']->detalhe();

                $funcao  = ' set_campo_pesquisa(';
                $virgula = '';
                $cont    = 0;

                foreach ($parametros->getCampoNome() as $campo) {
                    if ($parametros->getCampoTipo($cont) == 'text') {
                        if ($parametros->getCampoValor($cont) == 'cpf') {
                            if ($this->importarCpf || $busca) {
                                $objPessoa = new clsPessoaFisica($funcionario['ref_cod_pessoa_fj']);
                                $objPessoa_det = $objPessoa->detalhe();
                                $funcionario[$parametros->getCampoValor($cont)] = $objPessoa_det['cpf'];
                            }

                            $funcionario[$parametros->getCampoValor($cont)] = int2CPF($funcionario[$parametros->getCampoValor($cont)]);
                        }

                        $funcao .= "{$virgula} '{$campo}{$chave}', '{$funcionario[$parametros->getCampoValor($cont)]}'";
                        $virgula = ',';
                    } elseif ($parametros->getCampoTipo($cont) == 'select') {
                        if ($parametros->getCampoValor($cont) == 'cpf') {
                            if ($this->importarCpf || $busca) {
                                $objPessoa = new clsPessoaFisica($funcionario['ref_cod_pessoa_fj']);
                                $objPessoa_det = $objPessoa->detalhe();
                                $funcionario[$parametros->getCampoValor($cont)] = $objPessoa_det['cpf'];
                            }

                            $funcionario[$parametros->getCampoValor($cont)] = int2CPF($funcionario[$parametros->getCampoValor($cont)]);
                        }

                        $funcao .= "{$virgula} '{$campo}{$chave}', '{$funcionario[$parametros->getCampoIndice($cont)]}', '{$funcionario[$parametros->getCampoValor($cont)]}'";
                        $virgula = ',';
                    }

                    $cont++;
                }

                if ($parametros->getSubmit()) {
                    $funcao .= "{$virgula} 'submit')";
                } else {
                    $funcao .= ' )';
                }
                $this->addLinhas(["
                    <a href='javascript:void(0);' onclick=\"javascript:{$funcao}\">{$funcionario['matricula']}</a>",
                    "<a href='javascript:void(0);' onclick=\"javascript:{$funcao}\">{$det_cod_servidor['cpf']}</a>",
                    "<a href='javascript:void(0);' onclick=\"javascript:{$funcao}\">{$funcionario['nome']}</a>" ]);
                $total = $funcionario['_total'];
            }
        }
        // Paginador
        $this->addPaginador2('pesquisa_funcionario_lst.php', $total, $_GET, $this->nome, $limite);

        // Define Largura da Página
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Pesquisa por Funcion&aacute;rio!';
        $this->processoAp         = '0';
        $this->renderMenu         = false;
        $this->renderMenuSuspenso = false;
    }
};
