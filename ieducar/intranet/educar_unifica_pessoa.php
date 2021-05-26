<?php

use App\Models\Individual;
use App\Models\LogUnification;
use iEducar\Modules\Unification\PersonLogUnification;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro {
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
        $this->acao_enviar = 'showConfirmationMessage()';
        $this->inputsHelper()->dynamic('ano', ['required' => false, 'max_length' => 4]);
        $this->campoTabelaInicio('tabela_pessoas', '', ['Pessoa duplicada', 'Campo Pessoa duplicada'], $this->tabela_pessoas);
            $this->campoRotulo('pessoa_label', '', 'Pessoa física a ser unificada <span class="campo_obrigatorio">*</span>');
            $this->campoTexto('pessoa_duplicada', 'Pessoa duplicada', $this->pessoa_duplicada, 50, 255, false, true, false, '', '', '', 'onfocus');
        $this->campoTabelaFim();

        $styles = ['/modules/Cadastro/Assets/Stylesheets/UnificaPessoa.css'];
        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
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

        $codPessoaPrincipal = (int) $this->pessoa_id;

        if (!$codPessoaPrincipal) {
            return;
        }

        $codPessoas = [];

        //Monta um array com o código dos pessoas selecionados na tabela
        foreach ($this->pessoa_duplicada as $key => $value) {
            $explode = explode(' ', $value);

            if ($explode[0] == $codPessoaPrincipal) {
                $this->mensagem = 'Impossivel de unificar pessoas iguais.<br />';

                return false;
            }

            $codPessoas[] = (int) $explode[0];
        }

        if (!count($codPessoas)) {
            $this->mensagem = 'Informe no mínimo um pessoa para unificação.<br />';

            return false;
        }

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

    private function createLog($mainId, $duplicatesId, $createdBy)
    {
        $log = new LogUnification();
        $log->type = PersonLogUnification::getType();
        $log->main_id = $mainId;
        $log->duplicates_id = json_encode($duplicatesId);
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
