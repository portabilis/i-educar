<?php

use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;

return new class extends clsListagem
{
    public function Gerar()
    {
        $this->titulo = 'Empresas';

        $this->addCabecalhos(coluna: ['Razão Social', 'Nome Fantasia']);

        $this->campoTexto(nome: 'fantasia', campo: 'Nome Fantasia', valor: $_GET['fantasia'], tamanhovisivel: '50', tamanhomaximo: '255');
        $this->campoTexto(nome: 'razao_social', campo: 'Razão Social', valor: $_GET['razao_social'], tamanhovisivel: '50', tamanhomaximo: '255');
        $this->campoCnpj(nome: 'id_federal', campo: 'CNPJ', valor: $_GET['id_federal']);

        // Paginador
        $limite = 10;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite : 0;
        $par_fantasia = $_GET['fantasia'] ?? null;
        $opcoes = false;
        if ($_GET['razao_social']) {
            $par_razao = $_GET['razao_social'];
            $paraBusca = str_replace(' ', '%', $par_razao);

            $opcoes = LegacyPerson::query()
                ->whereRaw('f_unaccent(nome) ILIKE f_unaccent(?)', ["%{$paraBusca}%"])
                ->pluck('idpes')
                ->all();
        }
        $db = new clsBanco;

        $query = LegacyOrganization::query()
            ->join('cadastro.pessoa', 'cadastro.pessoa.idpes', 'cadastro.juridica.idpes')
            ->select(['cadastro.juridica.idpes', 'cadastro.juridica.fantasia', 'cadastro.pessoa.nome']);

        if (is_string($par_fantasia)) {
            $query->whereRaw(
                '(fcn_upper_nrm(cadastro.juridica.fantasia) LIKE fcn_upper_nrm(?) OR fcn_upper_nrm(cadastro.pessoa.nome) LIKE fcn_upper_nrm(?))',
                ["%$par_fantasia%", "%$par_fantasia%"]
            );
        }

        $query->when(limpaCnpj($_GET['id_federal'] ?? ''), fn ($query, $cnpj) => $query->whereCnpj($cnpj));

        if (is_array($opcoes)) {
            $opcoesValidas = array_filter($opcoes, 'is_numeric');
            if (count($opcoesValidas) === count($opcoes)) {
                $query->whereIn('cadastro.juridica.idpes', $opcoesValidas);
            }
        }

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar(codUsuario: $this->pessoa_logada)) {
            $query->whereExists(function ($q) {
                $q->from('pmieducar.escola')
                    ->join('pmieducar.escola_usuario', 'escola_usuario.ref_cod_escola', 'escola.cod_escola')
                    ->whereColumn('escola.ref_idpes', 'cadastro.juridica.idpes')
                    ->where('escola_usuario.ref_cod_usuario', $this->pessoa_logada)
                    ->where('escola.ativo', 1);
            });
        }

        $total = (clone $query)->count();

        $empresas = $query->orderBy('cadastro.juridica.fantasia')
            ->offset($iniciolimit)
            ->limit($limite)
            ->get();

        if ($empresas->isNotEmpty()) {
            foreach ($empresas as $empresa) {
                $cod_empresa = $empresa['idpes'];
                $razao_social = $db->escapeString(string: $empresa['nome']);
                $nome_fantasia = $db->escapeString(string: $empresa['fantasia']);
                $this->addLinhas(linha: ["<a href='empresas_det.php?cod_empresa={$cod_empresa}'><img src='imagens/noticia.jpg' border=0>$razao_social</a>", "<a href='empresas_det.php?cod_empresa={$cod_empresa}'>{$nome_fantasia}</a>"]);
            }
        }
        // Paginador
        $this->addPaginador2(strUrl: ' empresas_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $limite);

        $obj_permissao = new clsPermissoes;

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 41, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, super_usuario: true)) {
            $this->acao = 'go("empresas_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de pessoas jurídicas', breadcrumbs: [
            url(path: 'intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Empresas';
        $this->processoAp = 41;
    }
};
