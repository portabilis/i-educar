<?php

use App\Models\LegacyEmployee;
use Illuminate\Support\Facades\Session;

return new class extends clsListagem
{
    public $chave_campo;

    public $importarCpf;

    public function Gerar()
    {
        $this->nome = 'form1';

        if ($_GET['campos']) {
            $parametros = new clsParametrosPesquisas;
            $parametros->deserializaCampos($_GET['campos']);
            Session::put('campos', $parametros->geraArrayComAtributos());

            unset($_GET['campos']);
        } else {
            $parametros = new clsParametrosPesquisas;
            $parametros->preencheAtributosComArray(Session::get('campos'));
        }

        $this->addCabecalhos(['Matrícula', 'CPF', 'Funcionário']);

        // Filtros de Busca
        $this->campoTexto(nome: 'campo_busca', campo: 'Funcionário', valor: '', tamanhovisivel: 50, tamanhomaximo: 255, descricao: 'Matrícula/CPF/Nome do Funcionário');
        $this->campoOculto(nome: 'com_matricula', valor: $_GET['com_matricula']);

        if ($_GET['campo_busca']) {
            $chave_busca = @$_GET['campo_busca'];
        }

        if ($_GET['busca']) {
            $busca = @$_GET['busca'];
        }

        // Paginador
        $limite = 10;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite : 0;

        $this->chave_campo = $_GET['chave_campo'];
        $this->campoOculto(nome: 'chave_campo', valor: $this->chave_campo);
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

        $baseQuery = LegacyEmployee::query()
            ->join('cadastro.pessoa', 'cadastro.pessoa.idpes', 'portal.funcionario.ref_cod_pessoa_fj')
            ->select(['portal.funcionario.ref_cod_pessoa_fj', 'portal.funcionario.matricula', 'cadastro.pessoa.nome'])
            ->with('person.individual')
            ->orderBy('cadastro.pessoa.nome');

        if ($com_matricula) {
            $baseQuery->whereNotNull('portal.funcionario.matricula');
        }

        if ($busca == 'S' && is_string($chave_busca) && $chave_busca !== '') {
            $matchPorNome = (clone $baseQuery)
                ->whereRaw(
                    "translate(upper(cadastro.pessoa.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper(?),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')",
                    ["%{$chave_busca}%"]
                )
                ->limit($limite)
                ->get();

            if ($matchPorNome->isNotEmpty()) {
                $lst_funcionario = $matchPorNome;
                $total = $matchPorNome->count();
            } else {
                $queryMatricula = (clone $baseQuery)
                    ->where('portal.funcionario.matricula', 'like', "%{$chave_busca}%");
                $total = (clone $queryMatricula)->count();
                $lst_funcionario = $queryMatricula->offset($iniciolimit)->limit($limite)->get();
            }
        } else {
            $total = (clone $baseQuery)->count();
            $lst_funcionario = $baseQuery->offset($iniciolimit)->limit($limite)->get();
        }

        if ($lst_funcionario->isNotEmpty()) {
            foreach ($lst_funcionario as $funcionario) {
                $det_cod_servidor = ['cpf' => $funcionario->person?->individual?->cpf];

                $funcao = ' set_campo_pesquisa(';
                $virgula = '';
                $cont = 0;

                foreach ($parametros->getCampoNome() as $campo) {
                    if ($parametros->getCampoTipo($cont) == 'text') {
                        if ($parametros->getCampoValor($cont) == 'cpf') {
                            if ($this->importarCpf || $busca) {
                                $funcionario['cpf'] = $funcionario->person?->individual?->cpf;
                            }

                            $funcionario['cpf'] = int2CPF($funcionario['cpf']);
                        }

                        $funcao .= "{$virgula} '{$campo}{$chave}', '{$funcionario[$parametros->getCampoValor($cont)]}'";
                        $virgula = ',';
                    } elseif ($parametros->getCampoTipo($cont) == 'select') {
                        if ($parametros->getCampoValor($cont) == 'cpf') {
                            if ($this->importarCpf || $busca) {
                                $funcionario['cpf'] = $funcionario->person?->individual?->cpf;
                            }

                            $funcionario['cpf'] = int2CPF($funcionario['cpf']);
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
                    "<a href='javascript:void(0);' onclick=\"javascript:{$funcao}\">{$funcionario['nome']}</a>"]);
            }
        }
        // Paginador
        $this->addPaginador2(strUrl: 'pesquisa_funcionario_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $limite);

        // Define Largura da Página
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Pesquisa por Funcionário!';
        $this->processoAp = '0';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
