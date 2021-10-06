<?php

use App\Models\Individual;
use App\Models\LogUnification;
use App\Services\ValidationDataService;
use iEducar\Modules\Unification\PersonLogUnification;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro {
    public $pessoas;
    public $pessoa_logada;
    public $tabela_pessoas = [];
    public $pessoa_duplicada;

    public function Formular()
    {
        $this->titulo = 'i-Educar - Unificação de pessoas';
        $this->processoAp = '9998878';

        $this->breadcrumb('Unificação de pessoas', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            9998878,
            $this->pessoa_logada,
            7,
            'index.php'
        );

        return $retorno;
    }

    public function Gerar()
    {
        $this->acao_enviar = 'carregaDadosPessoas()';
        $this->campoTabelaInicio('tabela_pessoas', '', ['Pessoa duplicada', 'Campo Pessoa duplicada'], $this->tabela_pessoas);
            $this->campoRotulo('pessoa_label', '', 'Pessoa física a ser unificada <span class="campo_obrigatorio">*</span>');
            $this->campoTexto('pessoa_duplicada', 'Pessoa duplicada', $this->pessoa_duplicada, 50, 255, false, true, false, '', '', '', 'onfocus');
        $this->campoTabelaFim();

        $styles = ['/modules/Cadastro/Assets/Stylesheets/UnificaPessoa.css'];
        $scripts = ['/modules/Portabilis/Assets/Javascripts/ClientApi.js'];
        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }


    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            9998878,
            $this->pessoa_logada,
            7,
            'index.php'
        );

        if (empty($this->pessoas)) {
            $this->simpleRedirect('index.php');
        }

        try {
            $pessoas = json_decode($this->pessoas, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            $this->mensagem = 'Informações inválidas para unificação';
            return false;
        }

        if (count($pessoas) < 2) {
            $this->mensagem = 'Informe no mínimo duas pessoas para unificação.<br />';
            return false;
        }

        if (! $this->validaDadosDaUnificacao($pessoas)) {
            $this->mensagem = 'Dados enviados inválidos, recarregue a tela e tente novamente!';
            return false;
        }

        $validationData = new ValidationDataService();

        if (! $validationData->verifyQuantityByKey($pessoas,'pessoa_principal', 0)) {
            $this->mensagem = 'Pessoa principal não informada';
            return false;
        }

        if ($validationData->verifyQuantityByKey($pessoas, 'pessoa_principal', 1)) {
            $this->mensagem = 'Não pode haver mais de uma pessoa principal';
            return false;
        }

        if (! $validationData->verifyDataContainsDuplicatesByKey($pessoas, 'idpes')) {
            $this->mensagem = 'Erro ao tentar unificar Pessoas, foi inserido cadastro duplicados';
            return false;
        }

        $codPessoaPrincipal = $this->buscaPessoaPrincipal($pessoas);
        $codPessoas = $this->buscaIdesDasPessoasParaUnificar($pessoas);

        DB::beginTransaction();

        $unificationId = $this->createLog($codPessoaPrincipal, $codPessoas, $this->pessoa_logada);
        $unificador = new App_Unificacao_Pessoa($codPessoaPrincipal, $codPessoas, $this->pessoa_logada, new clsBanco(), $unificationId);

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
            if (! array_key_exists('idpes',$item)) {
                return false;
            }

            if (! array_key_exists('pessoa_principal',$item)) {
                return false;
            }
        }

        return true;
    }

    private function buscaIdesDasPessoasParaUnificar($pessoas)
    {
       return array_map(static fn ($item) => (int) $item['idpes'],
            array_filter($pessoas, static fn ($pessoas) => $pessoas['pessoa_principal'] === false)
        );
    }

    private function buscaPessoaPrincipal($pessoas)
    {
        $pessoas = array_values(array_filter($pessoas,
                static fn ($pessoas) => $pessoas['pessoa_principal'] === true)
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
     * @param integer[] $duplicatesId
     *
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
