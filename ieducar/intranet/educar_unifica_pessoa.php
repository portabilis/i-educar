<?php

use App\Models\Individual;
use App\Models\LogUnification;
use App\Services\ValidationDataService;
use iEducar\Modules\Unification\PersonLogUnification;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
{
    public $pessoas;

    public $pessoa_logada;

    public $tabela_pessoas = [];

    public $pessoa_duplicada;

    public function Formular()
    {
        $this->titulo = 'i-Educar - Unificação de pessoas';
        $this->processoAp = '9998878';

        $this->breadcrumb(currentPage: 'Unificação de pessoas', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 9998878,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'index.php'
        );

        return $retorno;
    }

    public function Gerar()
    {
        $this->acao_enviar = 'carregaDadosPessoas()';
        $this->campoTabelaInicio(nome: 'tabela_pessoas', arr_campos: ['Pessoa duplicada', 'Campo Pessoa duplicada'], arr_valores: $this->tabela_pessoas);
        $this->campoRotulo(nome: 'pessoa_label', campo: '', valor: 'Pessoa física a ser unificada <span class="campo_obrigatorio">*</span>');
        $this->campoTexto(nome: 'pessoa_duplicada', campo: 'Pessoa duplicada', valor: $this->pessoa_duplicada, tamanhovisivel: 50, tamanhomaximo: 255, expressao: true, duplo: false);
        $this->campoTabelaFim();

        $styles = ['/vendor/legacy/Cadastro/Assets/Stylesheets/UnificaPessoa.css'];
        $scripts = ['/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js'];
        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: $styles);
        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 9998878,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'index.php'
        );

        if (empty($this->pessoas)) {
            $this->simpleRedirect('index.php');
        }

        try {
            $pessoas = json_decode(json: $this->pessoas, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (TypeError $exception) {
            $this->mensagem = 'Informações inválidas para unificação';

            return false;
        } catch (Exception $exception) {
            $this->mensagem = 'Informações inválidas para unificação';

            return false;
        }

        if (count($pessoas) < 2) {
            $this->mensagem = 'Informe no mínimo duas pessoas para unificação.<br />';

            return false;
        }

        if (!$this->validaDadosDaUnificacao($pessoas)) {
            $this->mensagem = 'Dados enviados inválidos, recarregue a tela e tente novamente!';

            return false;
        }

        $validationData = new ValidationDataService();

        if (!$validationData->verifyQuantityByKey(data: $pessoas, key: 'pessoa_principal', quantity: 0)) {
            $this->mensagem = 'Pessoa principal não informada';

            return false;
        }

        if ($validationData->verifyQuantityByKey(data: $pessoas, key: 'pessoa_principal', quantity: 1)) {
            $this->mensagem = 'Não pode haver mais de uma pessoa principal';

            return false;
        }

        if (!$validationData->verifyDataContainsDuplicatesByKey(data: $pessoas, key: 'idpes')) {
            $this->mensagem = 'Erro ao tentar unificar Pessoas, foi inserido cadastro duplicados';

            return false;
        }

        $codPessoaPrincipal = $this->buscaPessoaPrincipal($pessoas);
        $codPessoas = $this->buscaIdesDasPessoasParaUnificar($pessoas);

        DB::beginTransaction();

        $unificationId = $this->createLog(mainId: $codPessoaPrincipal, duplicatesId: $codPessoas, createdBy: $this->pessoa_logada);
        $unificador = new App_Unificacao_Pessoa(codigoUnificador: $codPessoaPrincipal, codigosDuplicados: $codPessoas, codPessoaLogada: $this->pessoa_logada, db: new clsBanco(), unificationId: $unificationId);

        try {
            $unificador->unifica();
            DB::commit();
        } catch (CoreExt_Exception $exception) {
            $this->mensagem = $exception->getMessage();
            DB::rollBack();

            return false;
        }

        $this->mensagem = '<span>Pessoas unificadas com sucesso.</span>';

        return true;
    }

    private function validaDadosDaUnificacao($pessoa)
    {
        foreach ($pessoa as $item) {
            if (!array_key_exists(key: 'idpes', array: $item)) {
                return false;
            }

            if (!array_key_exists(key: 'pessoa_principal', array: $item)) {
                return false;
            }
        }

        return true;
    }

    private function buscaIdesDasPessoasParaUnificar($pessoas)
    {
        return array_map(callback: static fn ($item) => (int) $item['idpes'],
            array: array_filter(array: $pessoas, callback: static fn ($pessoas) => $pessoas['pessoa_principal'] === false)
        );
    }

    private function buscaPessoaPrincipal($pessoas)
    {
        $pessoas = array_values(array_filter(array: $pessoas,
            callback: static fn ($pessoas) => $pessoas['pessoa_principal'] === true)
        );

        return current($pessoas)['idpes'];
    }

    private function createLog($mainId, $duplicatesId, $createdBy)
    {
        $log = new LogUnification();
        $log->type = PersonLogUnification::getType();
        $log->main_id = $mainId;
        $log->duplicates_id = json_encode(array_values($duplicatesId));
        $log->created_by = $createdBy;
        $log->updated_by = $createdBy;
        $log->duplicates_name = json_encode($this->getNamesOfUnifiedPeople($duplicatesId));
        $log->save();

        return $log->id;
    }

    /**
     * Retorna os nomes das pessoas unificadas
     *
     * @param int[] $duplicatesId
     * @return string[]
     */
    private function getNamesOfUnifiedPeople($duplicatesId)
    {
        $names = [];

        foreach ($duplicatesId as $personId) {
            $names[] = Individual::findOrFail($personId)->real_name;
        }

        return $names;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-unifica-pessoa.js');
    }
};
