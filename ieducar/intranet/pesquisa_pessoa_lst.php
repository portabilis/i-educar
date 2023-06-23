<?php

use Illuminate\Support\Facades\Session;

return new class() extends clsListagem
{
    public $cpf;

    public $cnpj;

    public $matricula;

    public $campo_busca;

    public $chave_campo;

    public function Gerar()
    {
        $this->nome = 'form1';

        $show = $_REQUEST['show'];
        $this->campoOculto(nome: 'show', valor: $show);

        if ($show == 'todos') {
            $show = false;
        } else {
            $show = 1;
        }

        $this->chave_campo = $_GET['chave_campo'];

        if ($_GET['campos']) {
            $parametros = new clsParametrosPesquisas();
            $parametros->deserializaCampos($_GET['campos']);
            Session::put('campos', $parametros->geraArrayComAtributos());
            unset($_GET['campos']);
        } else {
            $parametros = new clsParametrosPesquisas();
            $parametros->preencheAtributosComArray(Session::get('campos'));
        }

        foreach ($_GET as $key => $value) {
            $this->$key = $value;
        }

        if ($parametros->getPessoa() == null || $parametros->getPessoa() == 'F' || $parametros->getPessoa() == '') {
            $this->addCabecalhos(['CPF', 'Nome']);

            // Filtros de Busca
            $this->campoTexto(nome: 'campo_busca', campo: 'Pessoa', valor: $this->campo_busca, tamanhovisivel: 35, tamanhomaximo: 255, descricao: 'Código/Nome');

            $this->campoCpf(nome: 'cpf', campo: 'CPF', valor: !empty($this->cpf) ? $this->cpf : '');

            if ($this->cpf == null || validaCPF($this->cpf)) {

                if (!empty(request('campo_busca') || !empty(request('cpf')))) {
                    $chave_busca = request('campo_busca');
                    $cpf = request(key: 'cpf', default: '');
                    $busca = request(key: 'busca', default: '');
                }

                // Paginador
                $limite = 10;
                $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite : 0;

                if (is_numeric($this->chave_campo)) {
                    $chave = "[$this->chave_campo]";
                } else {
                    $chave = '';
                }

                if ($busca == 'S') {
                    if (is_numeric($chave_busca)) {
                        $obj_pessoa = new clsPessoaFisica();
                        $lst_pessoa = $obj_pessoa->lista(str_nome: null, numeric_cpf: (($cpf) ? idFederal2int($cpf) : null), inicio_limite: $iniciolimit, qtd_registros: $limite, int_ref_cod_sistema: $parametros->getCodSistema(), int_idpes: $chave_busca);
                    } else {
                        $obj_pessoa = new clsPessoaFisica();
                        $lst_pessoa = $obj_pessoa->lista(str_nome: $chave_busca, numeric_cpf: (($cpf) ? idFederal2int($cpf) : null), inicio_limite: $iniciolimit, qtd_registros: $limite, int_ref_cod_sistema: $parametros->getCodSistema());
                    }
                } else {
                    $obj_pessoa = new clsPessoaFisica();
                    $lst_pessoa = $obj_pessoa->lista(str_nome: null, numeric_cpf: null, inicio_limite: $iniciolimit, qtd_registros: $limite, int_ref_cod_sistema: $parametros->getCodSistema());
                }

                if ($lst_pessoa) {
                    foreach ($lst_pessoa as $pessoa) {
                        $funcao = ' set_campo_pesquisa(';
                        $virgula = '';
                        $cont = 0;
                        $pessoa['cpf'] = (is_numeric($pessoa['cpf'])) ? int2CPF($pessoa['cpf']) : null;

                        foreach ($parametros->getCampoNome() as $campo) {
                            if ($parametros->getCampoTipo($cont) == 'text') {
                                $campoTexto = addslashes($pessoa[$parametros->getCampoValor($cont)]);
                                $funcao .= "{$virgula} '{$campo}{$chave}', '{$campoTexto}'";
                                $virgula = ',';
                            } elseif ($parametros->getCampoTipo($cont) == 'select') {
                                $campoTexto = addslashes($pessoa[$parametros->getCampoValor($cont)]);
                                $funcao .= "{$virgula} '{$campo}{$chave}', '{$pessoa[$parametros->getCampoIndice($cont)]}', '{$campoTexto}'";
                                $virgula = ',';
                            }
                            $cont++;
                        }
                        if ($parametros->getSubmit()) {
                            $funcao .= "{$virgula} 'submit' )";
                        } else {
                            $funcao .= ' )';
                        }

                        $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cpf']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>"]);
                        $total = $pessoa['total'];
                    }
                } else {
                    $this->addLinhas(['Não existe nenhum resultado a ser apresentado.']);
                }
            } else {
                $this->addLinhas(['Informado um CPF Inválido']);
            }
        } elseif ($parametros->getPessoa() == 'J') {
            $this->addCabecalhos(['CNPJ', 'Nome']);

            // Filtros de Busca
            $this->campoTexto(nome: 'campo_busca', campo: 'Pessoa', valor: $this->campo_busca, tamanhovisivel: 35, tamanhomaximo: 255, descricao: 'Código/Nome');
            if ($this->cnpj) {
                if (is_numeric($this->cnpj)) {
                    $this->cnpj = int2CNPJ($this->cnpj);
                }
            } else {
                $this->cnpj = '';
            }
            $this->campoCnpj(nome: 'cnpj', campo: 'CNPJ', valor: $this->cnpj);

            $chave_busca = @$_GET['campo_busca'];
            $cnpj = @$_GET['cnpj'];
            $busca = @$_GET['busca'];

            // Paginador
            $limite = 10;
            $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite : 0;

            if ($busca == 'S') {
                if (is_numeric($chave_busca)) {
                    $obj_pessoa = new clsPessoaJuridica();
                    $lst_pessoa = $obj_pessoa->lista(numeric_cnpj: (($cnpj) ? idFederal2int($cnpj) : null), inicio_limit: $iniciolimit, fim_limite: $limite, int_idpes: $chave_busca);
                } else {
                    $obj_pessoa = new clsPessoaJuridica();
                    $lst_pessoa = $obj_pessoa->lista(numeric_cnpj: (($cnpj) ? idFederal2int($cnpj) : null), str_fantasia: $chave_busca, inicio_limit: $iniciolimit, fim_limite: $limite);
                }
            } else {
                $obj_pessoa = new clsPessoaJuridica();
                $lst_pessoa = $obj_pessoa->lista(numeric_cnpj: null, str_fantasia: null, numeric_insc_estadual: null, inicio_limit: $iniciolimit, fim_limite: $limite);
            }
            if ($lst_pessoa) {
                foreach ($lst_pessoa as $pessoa) {
                    $funcao = ' set_campo_pesquisa(';
                    $virgula = '';
                    $cont = 0;
                    $pessoa['cnpj'] = (is_numeric($pessoa['cnpj'])) ? int2CNPJ($pessoa['cnpj']) : null;
                    foreach ($parametros->getCampoNome() as $campo) {
                        $campoTexto = addslashes($pessoa[$parametros->getCampoValor($cont)]);
                        if ($parametros->getCampoTipo($cont) === 'text') {
                            $funcao .= "{$virgula} '{$campo}', '{$campoTexto}'";
                        } elseif ($parametros->getCampoTipo($cont) === 'select') {
                            $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoIndice($cont)]}', '{$campoTexto}'";
                        }
                        $virgula = ',';
                        $cont++;
                    }
                    if ($parametros->getSubmit()) {
                        $funcao .= "{$virgula} 'submit' )";
                    } else {
                        $funcao .= ' )';
                    }

                    $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cnpj']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>"]);
                    $total = $pessoa['total'];
                }
            } else {
                $this->addLinhas(['Não existe nenhum resultado a ser apresentado.']);
            }
        } elseif ($parametros->getPessoa() == 'FJ') {
            $this->addCabecalhos(['CNPJ/CPF', 'Nome']);

            // Filtros de Busca
            $this->campoTexto(nome: 'campo_busca', campo: 'Pessoa', valor: $this->campo_busca, tamanhovisivel: 50, tamanhomaximo: 255, descricao: 'Código/Nome');
            $this->campoIdFederal(nome: 'id_federal', campo: 'CNPJ/CPF', valor: ($this->id_federal) ? int2IdFederal($this->id_federal) : '');

            $chave_busca = @$_GET['campo_busca'];
            $id_federal = @$_GET['id_federal'];
            $busca = @$_GET['busca'];

            // Paginador
            $limite = 10;
            $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite : 0;
            if ($busca == 'S') {
                if (is_numeric($chave_busca)) {
                    $obj_pessoa = new clsPessoaFj();
                    $lst_pessoa = $obj_pessoa->lista_rapida(idpes: $chave_busca, id_federal: idFederal2int($id_federal), inicio_limite: $iniciolimit, limite: $limite, int_ref_cod_sistema: $parametros->getCodSistema());
                } else {
                    $obj_pessoa = new clsPessoaFj();
                    $lst_pessoa = $obj_pessoa->lista_rapida(nome: $chave_busca, id_federal: idFederal2int($id_federal), inicio_limite: $iniciolimit, limite: $limite, int_ref_cod_sistema: $parametros->getCodSistema());
                }
            } else {
                $obj_pessoa = new clsPessoaFj();
                $lst_pessoa = $obj_pessoa->lista_rapida(inicio_limite: $iniciolimit, limite: $limite, str_order_by: 'nome ASC', int_ref_cod_sistema: $parametros->getCodSistema());
            }
            if ($lst_pessoa) {
                foreach ($lst_pessoa as $pessoa) {
                    $funcao = ' set_campo_pesquisa(';
                    $virgula = '';
                    $cont = 0;
                    foreach ($parametros->getCampoNome() as $campo) {
                        $campoTexto = addslashes($pessoa[$parametros->getCampoValor($cont)]);
                        if ($parametros->getCampoTipo($cont) === 'text') {
                            $funcao .= "{$virgula} '{$campo}', '{$campoTexto}'";
                        } elseif ($parametros->getCampoTipo($cont) === 'select') {
                            $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoIndice($cont)]}', '{$campoTexto}'";
                        }
                        $virgula = ',';
                        $cont++;
                    }
                    if ($parametros->getSubmit()) {
                        $funcao .= "{$virgula} 'submit' )";
                    } else {
                        $funcao .= ' )';
                    }
                    $pessoa['cnpj'] = ($pessoa['tipo'] == 'J' && $pessoa['cnpj']) ? int2CNPJ($pessoa['cnpj']) : null;
                    $pessoa['cpf'] = ($pessoa['tipo'] == 'F' && $pessoa['cpf']) ? int2CPF($pessoa['cpf']) : null;
                    $obj_pes = new clsPessoa_($pessoa['idpes']);
                    $det_pes = $obj_pes->detalhe();
                    if ($parametros->getPessoaEditar() == 'S') {
                        if ($parametros->getPessoaTela() == 'frame') {
                            //
                        } else {
                            if ($det_pes['tipo'] == 'J') {
                                $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cnpj']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>"]);
                            } else {
                                $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cpf']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>"]);
                            }
                        }
                    } else {
                        if ($det_pes['tipo'] == 'J') {
                            $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cnpj']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>"]);
                        } else {
                            $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cpf']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>"]);
                        }
                    }
                    $total = $pessoa['_total'];
                }
            } else {
                $this->addLinhas(['Não existe nenhum resultado a ser apresentado.']);
            }
        } elseif ($parametros->getPessoa() == 'FUNC') {
            $this->addCabecalhos(['Matricula', 'Nome']);

            // Filtros de Busca
            $this->campoTexto(nome: 'campo_busca', campo: 'Pessoa', valor: $this->campo_busca, tamanhovisivel: 50, tamanhomaximo: 255, descricao: 'Código/Nome');
            $this->campoNumero(nome: 'matricula', campo: 'Matricula', valor: $this->matricula, tamanhovisivel: 15, tamanhomaximo: 255);

            $chave_busca = @$_GET['campo_busca'];
            $cpf = @$_GET['cpf'];
            $busca = @$_GET['busca'];

            // Paginador
            $limite = 10;
            $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite : 0;

            if ($busca == 'S') {
                if (is_numeric($chave_busca)) {
                    $obj_funcionario = new clsFuncionario();
                    $lst_pessoa = $obj_funcionario->lista(str_matricula: $this->matricula, int_ativo: $show, int_qtd_registros: $limite);
                } else {
                    $obj_funcionario = new clsFuncionario();
                    $lst_pessoa = $obj_funcionario->lista(str_matricula: $this->matricula, str_nome: $this->campo_busca, int_ativo: $show, int_inicio_limit: $iniciolimit, int_qtd_registros: $limite);
                }
            } else {
                $obj_funcionario = new clsFuncionario();
                $lst_pessoa = $obj_funcionario->lista(int_ativo: $show);
            }
            if ($lst_pessoa) {
                foreach ($lst_pessoa as $pessoa) {
                    $funcao = ' set_campo_pesquisa(';
                    $virgula = '';
                    $cont = 0;
                    $pessoa['cpf'] = (is_numeric($pessoa['cpf'])) ? int2CPF($pessoa['cpf']) : null;
                    foreach ($parametros->getCampoNome() as $campo) {
                        $campoTexto = addslashes($pessoa[$parametros->getCampoValor($cont)]);
                        if ($parametros->getCampoTipo($cont) === 'text') {
                            $funcao .= "{$virgula} '{$campo}', '{$campoTexto}'";
                        } elseif ($parametros->getCampoTipo($cont) === 'select') {
                            $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoIndice($cont)]}', '{$campoTexto}'";
                        }
                        $virgula = ',';
                        $cont++;
                    }
                    if ($parametros->getSubmit()) {
                        $funcao .= "{$virgula} 'submit' )";
                    } else {
                        $funcao .= ' )';
                    }
                    if ($parametros->getPessoaEditar() == 'S') {
                        if ($parametros->getPessoaTela() == 'frame') {
                            //
                        } else {
                            $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['matricula']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>"]);
                        }
                    } else {
                        $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['matricula']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>"]);
                    }
                    $total = $pessoa['_total'];
                }
            } else {
                $this->addLinhas(['Não existe nenhum resultado a ser apresentado.']);
            }
        }

        // Paginador
        $this->addPaginador2(strUrl: 'pesquisa_pessoa_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $limite);

        // Define Largura da Página
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Pesquisa por Pessoa!';
        $this->processoAp = '0';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
