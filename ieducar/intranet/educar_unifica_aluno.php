<?php

use App\Models\LogUnification;
use App\Services\ValidationDataService;
use iEducar\Modules\Unification\StudentLogUnification;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $tabela_alunos = [];

    public $aluno_duplicado;

    public $alunos;

    public function Inicializar()
    {
        $this->validaPermissaoDaPagina();

        $this->breadcrumb(currentPage: 'Cadastrar unificação', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        return 'Novo';
    }

    public function Gerar()
    {
        $this->acao_enviar = 'carregaDadosAlunos()';
        $this->campoTabelaInicio(nome: 'tabela_alunos', arr_campos: ['Aluno duplicado', 'Campo aluno duplicado'], arr_valores: $this->tabela_alunos);
        $this->campoRotulo(nome: 'aluno_label', campo: '', valor: 'Aluno(a) a ser unificado(a)  <span class="campo_obrigatorio">*</span>');
        $this->campoTexto(nome: 'aluno_duplicado', campo: 'Aluno duplicado', valor: $this->aluno_duplicado, tamanhovisivel: 50, tamanhomaximo: 255, expressao: true, duplo: false);
        $this->campoTabelaFim();

        $styles = ['/vendor/legacy/Cadastro/Assets/Stylesheets/UnificaAluno.css'];
        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: $styles);
        $scripts = ['/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js'];
        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);
    }

    private function validaPermissaoDaPagina()
    {
        (new clsPermissoes())
            ->permissao_cadastra(
                int_processo_ap: 999847,
                int_idpes_usuario: $this->pessoa_logada,
                int_soma_nivel_acesso: 7,
                str_pagina_redirecionar: 'index.php'
            );
    }

    private function validaDadosDaUnificacaoAluno($alunos): bool
    {
        foreach ($alunos as $item) {
            if (!isset($item['codAluno'])) {
                return false;
            }

            if (!isset($item['aluno_principal'])) {
                return false;
            }
        }

        return true;
    }

    public function Novo()
    {
        $this->validaPermissaoDaPagina();

        try {
            $alunos = json_decode(json: $this->alunos, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (TypeError $exception) {
            $this->mensagem = 'Informações inválidas para unificação';

            return false;
        } catch (Exception $exception) {
            $this->mensagem = 'Informações inválidas para unificação';

            return false;
        }

        if (!$this->validaDadosDaUnificacaoAluno(alunos: $alunos)) {
            $this->mensagem = 'Dados enviados inválidos, recarregue a tela e tente novamente!';

            return false;
        }

        $validationData = new ValidationDataService();

        if (!$validationData->verifyQuantityByKey(data: $alunos, key: 'aluno_principal', quantity: 0)) {
            $this->mensagem = 'Aluno principal não informado';

            return false;
        }

        if ($validationData->verifyQuantityByKey(data: $alunos, key: 'aluno_principal', quantity: 1)) {
            $this->mensagem = 'Não pode haver mais de uma aluno principal';

            return false;
        }

        if (!$validationData->verifyDataContainsDuplicatesByKey(data: $alunos, key: 'codAluno')) {
            $this->mensagem = 'Erro ao tentar unificar Alunos, foi inserido cadastro duplicados';

            return false;
        }

        $cod_aluno_principal = $this->buscaPessoaPrincipal(pessoas: $alunos);
        $cod_alunos = $this->buscaIdesDasPessoasParaUnificar(pessoas: $alunos);

        DB::beginTransaction();
        $unificationId = $this->createLog(mainId: $cod_aluno_principal, duplicatesId: $cod_alunos, createdBy: $this->pessoa_logada);
        App_Unificacao_Aluno::unifica(codAlunoPrincipal: $cod_aluno_principal, codAlunos: $cod_alunos, codPessoa: $this->pessoa_logada, db: new clsBanco(), unificationId: $unificationId);

        try {
            DB::commit();
        } catch (Throwable) {
            DB::rollBack();
            $this->mensagem = 'Não foi possível realizar a unificação';

            return false;
        }

        $this->mensagem = '<span>Alunos unificados com sucesso.</span>';
        $this->simpleRedirect(url: route(name: 'student-log-unification.index'));
    }

    private function buscaIdesDasPessoasParaUnificar($pessoas)
    {
        return array_values(array: array_map(callback: static fn ($item) => (int) $item['codAluno'],
            array: array_filter(array: $pessoas, callback: static fn ($pessoas) => $pessoas['aluno_principal'] === false)
        ));
    }

    private function buscaPessoaPrincipal($pessoas)
    {
        $pessoas = array_values(array: array_filter(array: $pessoas,
            callback: static fn ($pessoas) => $pessoas['aluno_principal'] === true)
        );

        return (int) current(array: $pessoas)['codAluno'];
    }

    private function createLog($mainId, $duplicatesId, $createdBy)
    {
        $log = new LogUnification();
        $log->type = StudentLogUnification::getType();
        $log->main_id = $mainId;
        $log->duplicates_id = json_encode(value: array_values(array: $duplicatesId));
        $log->created_by = $createdBy;
        $log->updated_by = $createdBy;
        $log->save();

        return $log->id;
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-unifica-aluno.js');
    }

    public function Formular()
    {
        $this->title = 'Unificação de alunos';
        $this->processoAp = '999847';
    }
};
