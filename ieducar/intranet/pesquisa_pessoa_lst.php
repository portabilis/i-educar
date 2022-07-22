<?php

use Illuminate\Support\Facades\Session;

return new class extends clsListagem {
    public $cpf;
    public $cnpj;
    public $matricula;
    public $campo_busca;
    public $chave_campo;

    public function Gerar()
    {
        $this->nome = 'form1';

        $show = $_REQUEST['show'];
        $this->campoOculto('show', $show);

        if ($show == 'todos') {
            $show = false;
        } else {
            $show = 1;
        }

        $this->chave_campo = $_GET['chave_campo'];

        if ($_GET['campos']) {
            $parametros       = new clsParametrosPesquisas();
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

        if ($parametros->getPessoa() == 'F' || $parametros->getPessoa() == '') {
            $this->addCabecalhos(['CPF', 'Nome']);

            // Filtros de Busca
            $this->campoTexto('campo_busca', 'Pessoa', $this->campo_busca, 35, 255, false, false, false, 'Código/Nome');
            $this->campoCpf('cpf', 'CPF', !empty($this->cpf) ? int2CPF(idFederal2int($this->cpf)) : '');

            $chave_busca = @$_GET['campo_busca'];
            $cpf = @$_GET['cpf'];
            $busca = @$_GET['busca'];

            // Paginador
            $limite      = 10;
            $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite: 0;

            if (is_numeric($this->chave_campo)) {
                $chave = "[$this->chave_campo]";
            } else {
                $chave = '';
            }

            if ($busca == 'S') {
                if (is_numeric($chave_busca)) {
                    $obj_pessoa = new clsPessoaFisica();
                    $lst_pessoa = $obj_pessoa->lista(null, (($cpf) ? idFederal2int($cpf) : null), $iniciolimit, $limite, false, $parametros->getCodSistema(), $chave_busca);
                } else {
                    $obj_pessoa = new clsPessoaFisica();
                    $lst_pessoa = $obj_pessoa->lista($chave_busca, (($cpf) ? idFederal2int($cpf) : null), $iniciolimit, $limite, false, $parametros->getCodSistema());
                }
            } else {
                $obj_pessoa = new clsPessoaFisica();
                $lst_pessoa = $obj_pessoa->lista(null, null, $iniciolimit, $limite, false, $parametros->getCodSistema());
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

                    $this->addLinhas([ "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cpf']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>" ]);
                    $total = $pessoa['total'];
                }
            } else {
                $this->addLinhas([ 'Não existe nenhum resultado a ser apresentado.' ]);
            }
        } elseif ($parametros->getPessoa() == 'J') {
            $this->addCabecalhos([ 'CNPJ', 'Nome' ]);

            // Filtros de Busca
            $this->campoTexto('campo_busca', 'Pessoa', $this->campo_busca, 35, 255, false, false, false, 'Código/Nome');
            if ($this->cnpj) {
                if (is_numeric($this->cnpj)) {
                    $this->cnpj = int2CNPJ($this->cnpj);
                }
            } else {
                $this->cnpj = '';
            }
            $this->campoCnpj('cnpj', 'CNPJ', $this->cnpj);

            $chave_busca = @$_GET['campo_busca'];
            $cnpj        = @$_GET['cnpj'];
            $busca       = @$_GET['busca'];

            // Paginador
            $limite      = 10;
            $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite: 0;

            if ($busca == 'S') {
                if (is_numeric($chave_busca)) {
                    $obj_pessoa = new clsPessoaJuridica();
                    $lst_pessoa = $obj_pessoa->lista((($cnpj) ? idFederal2int($cnpj) : null), false, false, $iniciolimit, $limite, false, false, false, $chave_busca);
                } else {
                    $obj_pessoa = new clsPessoaJuridica();
                    $lst_pessoa = $obj_pessoa->lista((($cnpj) ? idFederal2int($cnpj) : null), $chave_busca, false, $iniciolimit, $limite);
                }
            } else {
                $obj_pessoa = new clsPessoaJuridica();
                $lst_pessoa = $obj_pessoa->lista(null, null, null, $iniciolimit, $limite);
            }
            if ($lst_pessoa) {
                foreach ($lst_pessoa as $pessoa) {
                    $funcao         = ' set_campo_pesquisa(';
                    $virgula        = '';
                    $cont           = 0;
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

                    $this->addLinhas([ "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cnpj']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>" ]);
                    $total = $pessoa['total'];
                }
            } else {
                $this->addLinhas([ 'Não existe nenhum resultado a ser apresentado.' ]);
            }
        } elseif ($parametros->getPessoa() == 'FJ') {
            $this->addCabecalhos([ 'CNPJ/CPF', 'Nome' ]);

            // Filtros de Busca
            $this->campoTexto('campo_busca', 'Pessoa', $this->campo_busca, 50, 255, false, false, false, 'Código/Nome');
            $this->campoIdFederal('id_federal', 'CNPJ/CPF', ($this->id_federal)?int2IdFederal($this->id_federal):'');

            $chave_busca = @$_GET['campo_busca'];
            $id_federal  = @$_GET['id_federal'];
            $busca       = @$_GET['busca'];

            // Paginador
            $limite      = 10;
            $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite: 0;
            if ($busca == 'S') {
                if (is_numeric($chave_busca)) {
                    $obj_pessoa = new clsPessoaFj();
                    $lst_pessoa = $obj_pessoa->lista_rapida($chave_busca, null, idFederal2int($id_federal), $iniciolimit, $limite, null, 'nome ASC', $parametros->getCodSistema());
                } else {
                    $obj_pessoa = new clsPessoaFj();
                    $lst_pessoa = $obj_pessoa->lista_rapida(null, $chave_busca, idFederal2int($id_federal), $iniciolimit, $limite, null, 'nome ASC', $parametros->getCodSistema());
                }
            } else {
                $obj_pessoa = new clsPessoaFj();
                $lst_pessoa = $obj_pessoa->lista_rapida(null, null, null, $iniciolimit, $limite, null, 'nome ASC', $parametros->getCodSistema());
            }
            if ($lst_pessoa) {
                foreach ($lst_pessoa as $pessoa) {
                    $funcao               = ' set_campo_pesquisa(';
                    $virgula              = '';
                    $cont                 = 0;
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
                    $pessoa['cpf'] = ($pessoa['tipo'] == 'F' && $pessoa['cpf']) ?  int2CPF($pessoa['cpf']) : null;
                    $obj_pes = new clsPessoa_($pessoa['idpes']);
                    $det_pes = $obj_pes->detalhe();
                    if ($parametros->getPessoaEditar() == 'S') {
                        if ($parametros->getPessoaTela() == 'frame') {
                            //
                        } else {
                            if ($det_pes['tipo'] == 'J') {
                                $this->addLinhas([ "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cnpj']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>" ]);
                            } else {
                                $this->addLinhas([ "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cpf']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>" ]);
                            }
                        }
                    } else {
                        if ($det_pes['tipo'] == 'J') {
                            $this->addLinhas([ "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cnpj']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>" ]);
                        } else {
                            $this->addLinhas([ "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cpf']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>" ]);
                        }
                    }
                    $total = $pessoa['_total'];
                }
            } else {
                $this->addLinhas([ 'Não existe nenhum resultado a ser apresentado.' ]);
            }
        } elseif ($parametros->getPessoa() == 'FUNC') {
            $this->addCabecalhos([ 'Matricula', 'Nome' ]);

            // Filtros de Busca
            $this->campoTexto('campo_busca', 'Pessoa', $this->campo_busca, 50, 255, false, false, false, 'Código/Nome');
            $this->campoNumero('matricula', 'Matricula', $this->matricula, 15, 255);

            $chave_busca = @$_GET['campo_busca'];
            $cpf         = @$_GET['cpf'];
            $busca       = @$_GET['busca'];

            // Paginador
            $limite      = 10;
            $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite: 0;

            if ($busca == 'S') {
                if (is_numeric($chave_busca)) {
                    $obj_funcionario = new clsFuncionario();
                    $lst_pessoa = $obj_funcionario->lista($this->matricula, false, $show, false, false, false, false, $iniciolimit, $limite, false, false, $this->campo_busca);
                } else {
                    $obj_funcionario = new clsFuncionario();
                    $lst_pessoa = $obj_funcionario->lista($this->matricula, $this->campo_busca, $show, false, false, false, false, $iniciolimit, $limite);
                }
            } else {
                $obj_funcionario = new clsFuncionario();
                $lst_pessoa = $obj_funcionario->lista(false, false, $show, false, false, false, false, $iniciolimit, $limite);
            }
            if ($lst_pessoa) {
                foreach ($lst_pessoa as $pessoa) {
                    $funcao        = ' set_campo_pesquisa(';
                    $virgula       = '';
                    $cont          = 0;
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
                            $this->addLinhas([ "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['matricula']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>" ]);
                        }
                    } else {
                        $this->addLinhas([ "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['matricula']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>" ]);
                    }
                    $total = $pessoa['_total'];
                }
            } else {
                $this->addLinhas([ 'Não existe nenhum resultado a ser apresentado.' ]);
            }
        }

        // Paginador
        $this->addPaginador2('pesquisa_pessoa_lst.php', $total, $_GET, $this->nome, $limite);

        // Define Largura da Página
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Pesquisa por Pessoa!';
        $this->processoAp         = '0';
        $this->renderMenu         = false;
        $this->renderMenuSuspenso = false;
    }
};
