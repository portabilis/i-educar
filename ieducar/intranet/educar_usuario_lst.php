<?php

use App\User;
use Illuminate\Support\Facades\Auth;

return new class extends clsListagem
{
    public function Gerar()
    {
        $this->titulo = 'Usuários';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos(coluna: ['Nome', 'Matrícula', 'Matrícula Interna', 'Status', 'Tipo usuário', 'Nível de Acesso']);

        // Filtros de Busca
        $this->campoTexto(nome: 'nm_pessoa', campo: 'Nome', valor: $this->nm_pessoa, tamanhovisivel: 42, tamanhomaximo: 255);
        $this->campoTexto(nome: 'matricula', campo: 'Matrícula', valor: $this->matricula, tamanhovisivel: 20, tamanhomaximo: 15);
        $this->campoTexto(nome: 'matricula_interna', campo: 'Matrícula Interna', valor: $this->matricula_interna, tamanhovisivel: 20, tamanhomaximo: 30);

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPmieducarTipoUsuario();
        $objTemp->setOrderby(strNomeCampo: 'nm_tipo ASC');
        $lista = $objTemp->lista(int_ativo: 1);
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_tipo_usuario']}"] = "{$registro['nm_tipo']}";
            }
        }

        $this->campoLista(nome: 'ref_cod_tipo_usuario', campo: 'Tipo Usuário', valor: $opcoes, default: $this->ref_cod_tipo_usuario, acao: null, duplo: null, descricao: null, complemento: null, desabilitado: null, obrigatorio: false);

        $obj_usuario = new clsPmieducarUsuario(cod_usuario: $this->pessoa_logada);
        $detalhe = $obj_usuario->detalhe();

        // filtro de nivel de acesso
        $obj_tipo_usuario = new clsPmieducarTipoUsuario(cod_tipo_usuario: $detalhe['ref_cod_tipo_usuario']);
        $tipo_usuario = $obj_tipo_usuario->detalhe();

        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            $opcoes = ['' => 'Selecione', '1' => 'Poli-Institucional', '2' => 'Institucional', '4' => 'Escolar', '8' => 'Biblioteca'];
        } elseif ($tipo_usuario['nivel'] == 1) {
            $opcoes = ['' => 'Selecione', '2' => 'Institucional', '4' => 'Escolar', '8' => 'Biblioteca'];
        } elseif ($tipo_usuario['nivel'] == 2) {
            $opcoes = ['' => 'Selecione', '4' => 'Escolar', '8' => 'Biblioteca'];
        } elseif ($tipo_usuario['nivel'] == 4) {
            $opcoes = ['' => 'Selecione', '8' => 'Biblioteca'];
        }
        $this->campoLista(nome: 'ref_cod_nivel_usuario', campo: 'Nível de Acesso', valor: $opcoes, default: $this->ref_cod_nivel_usuario, acao: null, duplo: null, descricao: null, complemento: null, desabilitado: null, obrigatorio: false);

        $this->inputsHelper()->dynamic(helperNames: 'instituicao', inputOptions: ['required' => false, 'show-select' => true, 'value' => $this->ref_cod_instituicao]);
        $this->inputsHelper()->dynamic(helperNames: 'escola', inputOptions: ['required' => false, 'show-select' => true, 'value' => $this->ref_cod_escola]);
        $selectOptions = [
            0 => 'Selecione',
            1 => 'Inativo',
            2 => 'Ativo',
        ];

        $options = [
            'required' => false,
            'label' => 'Status',
            'value' => $this->int_ativo,
            'resources' => $selectOptions,
        ];

        $this->inputsHelper()->select(attrName: 'int_ativo', inputOptions: $options);
        //gambiarra pois o inputsHelper está bugado
        switch ($this->int_ativo) {
            case 0:
                $this->int_ativo = null;
                break;
            case 1:
                $this->int_ativo = 0;
                break;
            case 2:
                $this->int_ativo = 1;
                break;
        }
        // Paginador
        $limite = 10;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite - $limite : 0;

        $obj_func = new clsFuncionario();
        $obj_func->setOrderby(strNomeCampo: '(nome) ASC');
        $obj_func->setLimite(intLimiteQtd: $limite, intLimiteOffset: $iniciolimit);
        $lst_func = $obj_func->listaFuncionarioUsuario(
            str_matricula: pg_escape_string(connection: $_GET['matricula']),
            str_nome: pg_escape_string(connection: $_GET['nm_pessoa']),
            matricula_interna: pg_escape_string(connection: $_GET['matricula_interna']),
            int_ref_cod_escola: $this->ref_cod_escola,
            int_ref_cod_instituicao: $this->ref_cod_instituicao,
            int_ref_cod_tipo_usuario: $this->ref_cod_tipo_usuario,
            int_nivel: $this->ref_cod_nivel_usuario,
            int_ativo: $this->int_ativo
        );

        if ($lst_func) {
            foreach ($lst_func as $pessoa) {
                $ativo = ($pessoa['ativo'] == '1') ? 'Ativo' : 'Inativo';
                $total = $pessoa['_total'];

                if ($pessoa['nivel'] == 1) {
                    $nivel = 'Poli-Institucional';
                } elseif ($pessoa['nivel'] == 2) {
                    $nivel = 'Institucional';
                } elseif ($pessoa['nivel'] == 4) {
                    $nivel = 'Escolar';
                } elseif ($pessoa['nivel'] == 8) {
                    $nivel = 'Biblioteca';
                } else {
                    $nivel = '';
                }

                $this->addLinhas(linha: [
                    "<a href='educar_usuario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'><img src='imagens/noticia.jpg' border=0>{$pessoa['nome']}</a>",
                    "<a href='educar_usuario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'>{$pessoa['matricula']}</a>",
                    "<a href='educar_usuario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'>{$pessoa['matricula_interna']}</a>",
                    "<a href='educar_usuario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'>{$ativo}</a>",
                    "<a href='educar_usuario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'>{$pessoa['nm_tipo']}</a>",
                    "<a href='educar_usuario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'>{$nivel}</a>",
                ]);
            }
        }

        $this->addPaginador2(strUrl: 'educar_usuario_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $limite);

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(int_processo_ap: 555, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, super_usuario: true)) {
            $this->acao = 'go("educar_usuario_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Usuários', breadcrumbs: [
            url(path: 'intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Usuários';
        $this->processoAp = '555';
    }
};
