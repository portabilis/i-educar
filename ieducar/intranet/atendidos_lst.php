<?php

use App\Models\LegacyPerson;

return new class() extends clsListagem
{
    public function Gerar()
    {
        $this->titulo = 'Pessoas Físicas';

        $par_nome = str_replace(['[', ']', '{', '}', '(', ')', '\\', '/'], '', $this->getQueryString(name: 'nm_pessoa')) ?: false;
        $par_id_federal = idFederal2Int(str: $this->getQueryString(name: 'id_federal')) ?: false;

        $this->addCabecalhos(
            coluna: [
                'Nome',
                'CPF',
            ]
        );
        $this->campoTexto(
            nome: 'nm_pessoa',
            campo: 'Nome',
            valor: $par_nome,
            tamanhovisivel: '50',
            tamanhomaximo: '255'
        );

        $this->campoCpf(
            nome: 'id_federal',
            campo: 'CPF',
            valor: $par_id_federal
        );

        // Paginador
        $limite = 10;
        $iniciolimit = ($this->getQueryString(name: "pagina_{$this->nome}")) ? $this->getQueryString(name: "pagina_{$this->nome}") * $limite - $limite : 0;

        $lista = LegacyPerson::query()->filter([
            'name' => $par_nome,
            'cpf' => is_numeric($par_id_federal) ? $par_id_federal : null,
        ])->select([
            'idpes',
            'nome',
        ])->with([
            'individual:idpes,cpf',
        ])->active()->orderBy('nome')->paginate(
            perPage: $limite,
            pageName: "pagina_{$this->nome}",
        );

        $total = $lista->total();

        if ($lista->isNotEmpty()) {
            foreach ($lista as $pessoa) {
                $cod = $pessoa->getKey();
                $nome = $pessoa->name;

                if ($pessoa->social_name) {
                    $nome = $pessoa->social_name . '<br> <i>Nome de registro: </i>' . $pessoa->name;
                }

                $cpf = $pessoa->individual?->cpf ?? int2CPF(int: $pessoa->individual->cpf);
                $this->addLinhas(linha: [
                    "<img src='imagens/noticia.jpg' border=0><a href='atendidos_det.php?cod_pessoa={$cod}'>$nome</a>",
                    $cpf,
                ]);
            }
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(
            int_processo_ap: 43,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            super_usuario: true
        )) {
            $this->acao = 'go("atendidos_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';
        $this->addPaginador2(
            strUrl: 'atendidos_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $limite
        );

        $this->breadcrumb(
            currentPage: 'Listagem de pessoa física',
            breadcrumbs: ['educar_pessoas_index.php' => 'Pessoas']
        );
    }

    public function Formular()
    {
        $this->title = 'Pessoas Físicas';
        $this->processoAp = '43';
    }
};
