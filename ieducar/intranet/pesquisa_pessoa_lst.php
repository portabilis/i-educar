<?php

use App\Models\LegacyEmployee;
use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

return new class extends clsListagem
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
            $parametros = new clsParametrosPesquisas;
            $parametros->deserializaCampos($_GET['campos']);
            Session::put('campos', $parametros->geraArrayComAtributos());
            unset($_GET['campos']);
        } else {
            $parametros = new clsParametrosPesquisas;
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

                $telefonesSubquery = DB::table('cadastro.fone_pessoa')
                    ->select(
                        'idpes',
                        DB::raw('MAX(CASE WHEN tipo = 1 THEN ddd END) AS ddd_1'),
                        DB::raw('MAX(CASE WHEN tipo = 1 THEN fone END) AS fone_1'),
                        DB::raw('MAX(CASE WHEN tipo = 2 THEN ddd END) AS ddd_2'),
                        DB::raw('MAX(CASE WHEN tipo = 2 THEN fone END) AS fone_2'),
                        DB::raw('MAX(CASE WHEN tipo = 3 THEN ddd END) AS ddd_mov'),
                        DB::raw('MAX(CASE WHEN tipo = 3 THEN fone END) AS fone_mov'),
                        DB::raw('MAX(CASE WHEN tipo = 4 THEN ddd END) AS ddd_fax'),
                        DB::raw('MAX(CASE WHEN tipo = 4 THEN fone END) AS fone_fax'),
                    )
                    ->groupBy('idpes');

                $query = LegacyPerson::query()
                    ->join('cadastro.fisica', 'cadastro.fisica.idpes', 'cadastro.pessoa.idpes')
                    ->leftJoinSub($telefonesSubquery, 'fp', 'fp.idpes', 'cadastro.pessoa.idpes')
                    ->select([
                        'cadastro.pessoa.idpes',
                        'cadastro.pessoa.nome',
                        'cadastro.fisica.nome_social',
                        'cadastro.pessoa.url',
                        DB::raw("'F' AS tipo"),
                        'cadastro.pessoa.email',
                        'cadastro.fisica.cpf',
                        'fp.ddd_1', 'fp.fone_1', 'fp.ddd_2', 'fp.fone_2',
                        'fp.ddd_mov', 'fp.fone_mov', 'fp.ddd_fax', 'fp.fone_fax',
                    ])
                    ->where('cadastro.fisica.ativo', 1);

                if ($busca == 'S') {
                    if (is_numeric($chave_busca)) {
                        $query->where('cadastro.pessoa.idpes', $chave_busca);
                    } elseif (is_string($chave_busca) && $chave_busca !== '') {
                        $query->whereRaw('coalesce(cadastro.pessoa.slug, f_unaccent(cadastro.pessoa.nome)) ILIKE f_unaccent(?)', ["%{$chave_busca}%"]);
                    }

                    if ($cpf) {
                        $query->whereRaw('cadastro.fisica.cpf::varchar ILIKE ?', ['%' . idFederal2int($cpf) . '%']);
                    }
                }

                if (is_numeric($parametros->getCodSistema())) {
                    $query->where(function ($q) use ($parametros) {
                        $q->where('cadastro.fisica.ref_cod_sistema', $parametros->getCodSistema())
                            ->orWhereNotNull('cadastro.fisica.cpf');
                    });
                }

                $total = (clone $query)->count();

                $lst_pessoa = $query
                    ->orderByRaw('COALESCE(cadastro.fisica.nome_social, cadastro.pessoa.nome)')
                    ->orderBy('cadastro.pessoa.idpes')
                    ->offset($iniciolimit)
                    ->limit($limite)
                    ->get();

                if ($lst_pessoa->isNotEmpty()) {
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

            $cnpjInt = $cnpj ? idFederal2int($cnpj) : null;

            $query = LegacyOrganization::query()
                ->join('cadastro.pessoa', 'cadastro.pessoa.idpes', 'cadastro.juridica.idpes')
                ->select(['cadastro.juridica.idpes', 'cadastro.juridica.fantasia', 'cadastro.juridica.cnpj', 'cadastro.pessoa.nome']);

            if ($busca == 'S' && is_numeric($cnpjInt)) {
                $cnpjLimpo = ltrim((string) $cnpjInt, '0');
                $query->whereRaw('cadastro.juridica.cnpj::varchar ILIKE ?', ["%$cnpjLimpo%"]);
            }

            if ($busca == 'S' && is_numeric($chave_busca)) {
                $query->where('cadastro.juridica.idpes', $chave_busca);
            } elseif ($busca == 'S' && is_string($chave_busca)) {
                $query->whereRaw(
                    '(fcn_upper_nrm(cadastro.juridica.fantasia) LIKE fcn_upper_nrm(?) OR fcn_upper_nrm(cadastro.pessoa.nome) LIKE fcn_upper_nrm(?))',
                    ["%$chave_busca%", "%$chave_busca%"]
                );
            }

            $total = (clone $query)->count();
            $lst_pessoa = $query->orderBy('cadastro.juridica.fantasia')
                ->offset($iniciolimit)
                ->limit($limite)
                ->get();
            if ($lst_pessoa->isNotEmpty()) {
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
            $query = LegacyPerson::query()
                ->leftJoin('cadastro.juridica', 'cadastro.juridica.idpes', 'cadastro.pessoa.idpes')
                ->leftJoin('cadastro.fisica', 'cadastro.fisica.idpes', 'cadastro.pessoa.idpes')
                ->select([
                    'cadastro.pessoa.idpes',
                    'cadastro.pessoa.nome',
                    'cadastro.pessoa.tipo',
                    'cadastro.juridica.cnpj',
                    'cadastro.fisica.cpf',
                ]);

            if ($busca == 'S') {
                if (is_numeric($chave_busca)) {
                    $query->where('cadastro.pessoa.idpes', $chave_busca);
                } elseif (is_string($chave_busca) && $chave_busca !== '') {
                    $query->whereRaw('coalesce(cadastro.pessoa.slug, f_unaccent(cadastro.pessoa.nome)) ILIKE f_unaccent(?)', ["%{$chave_busca}%"]);
                }

                if ($id_federal) {
                    $idFederalInt = idFederal2int($id_federal);
                    if (is_numeric($idFederalInt)) {
                        $query->whereIn('cadastro.pessoa.idpes', function ($q) use ($idFederalInt) {
                            $q->select('idpes')
                              ->from('cadastro.juridica')
                              ->whereRaw('cnpj::varchar LIKE ?', ["%{$idFederalInt}%"]);
                        });
                    }
                }
            }

            if (is_numeric($parametros->getCodSistema())) {
                $query->where(function ($q) use ($parametros) {
                    $q->where('cadastro.fisica.ref_cod_sistema', $parametros->getCodSistema())
                      ->orWhereRaw('COALESCE(cadastro.fisica.cpf, cadastro.juridica.cnpj) IS NOT NULL');
                });
            }

            $total = (clone $query)->count();
            $lst_pessoa = $query->orderByRaw('cadastro.pessoa.nome')->offset($iniciolimit)->limit($limite)->get();

            if ($lst_pessoa->isNotEmpty()) {
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
                    if ($parametros->getPessoaEditar() == 'S') {
                        if ($parametros->getPessoaTela() == 'frame') {
                            //
                        } else {
                            if ($pessoa['tipo'] == 'J') {
                                $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cnpj']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>"]);
                            } else {
                                $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cpf']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>"]);
                            }
                        }
                    } else {
                        if ($pessoa['tipo'] == 'J') {
                            $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cnpj']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>"]);
                        } else {
                            $this->addLinhas(["<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['cpf']}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$pessoa['nome']}</a>"]);
                        }
                    }
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

            $query = LegacyEmployee::query()
                ->join('cadastro.pessoa', 'cadastro.pessoa.idpes', 'portal.funcionario.ref_cod_pessoa_fj')
                ->select(['portal.funcionario.ref_cod_pessoa_fj', 'portal.funcionario.matricula', 'cadastro.pessoa.nome'])
                ->orderBy('cadastro.pessoa.nome');

            if (is_numeric($show)) {
                $query->where('portal.funcionario.ativo', $show);
            }

            if ($busca == 'S') {
                if (is_string($this->matricula) && $this->matricula !== '') {
                    $query->where('portal.funcionario.matricula', 'like', "%{$this->matricula}%");
                }

                if (!is_numeric($chave_busca) && is_string($this->campo_busca) && $this->campo_busca !== '') {
                    $query->whereRaw('f_unaccent(cadastro.pessoa.nome) ILIKE f_unaccent(?)', ["%{$this->campo_busca}%"]);
                }
            }

            $total = (clone $query)->count();
            $lst_pessoa = $query->offset($iniciolimit)->limit($limite)->get();

            if ($lst_pessoa->isNotEmpty()) {
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
