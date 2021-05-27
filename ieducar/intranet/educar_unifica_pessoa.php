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
        $this->acao_enviar = 'carregaDadosPessoas()';
        $this->inputsHelper()->dynamic('ano', ['required' => false, 'max_length' => 4]);
        $this->campoTabelaInicio('tabela_pessoas', '', ['Pessoa duplicada', 'Campo Pessoa duplicada'], $this->tabela_pessoas);
            $this->campoRotulo('pessoa_label', '', 'Pessoa física a ser unificada <span class="campo_obrigatorio">*</span>');
            $this->campoTexto('pessoa_duplicada', 'Pessoa duplicada', $this->pessoa_duplicada, 50, 255, false, true, false, '', '', '', 'onfocus');
        $this->campoTabelaFim();

        $styles = ['/modules/Cadastro/Assets/Stylesheets/UnificaPessoa.css'];
        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
    }

    private function validaSeExisteMaisDeUmaPessoaPrincipal($pessoas)
    {
        $principal = 0;
        foreach ($pessoas as $pessoa) {
            if ($pessoa['pessoa_principal'] === true) {
                $principal ++;
            }
        }

        return $principal > 1;
    }

    private function validaSeExisteUmaPessoaPrincipal($pessoas)
    {
        foreach ($pessoas as $pessoa) {
            if ($pessoa['pessoa_principal'] === true) {
                return true;
            }
        }

        return false;
    }

    private function validaSemTemItensDuplicados($pessoas)
    {
        $temp = [];
        foreach($pessoas as $val) {
            $idpes = (int) $val['idpes'];
            if (! in_array($idpes, $temp, true))  {
                $temp[] = $idpes;
                continue;
            }

            return false;
        }

        return true;
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
        if (! property_exists($this,'pessoas')) {
            $this->simpleRedirect('index.php');
        }

        $codPessoas = [];

        //Monta um array com o código dos pessoas selecionados na tabela
        foreach ($this->pessoa_duplicada as $key => $value) {
            $explode = explode(' ', $value);

            if ($explode[0] == $codPessoaPrincipal) {
                $this->mensagem = 'Impossivel de unificar pessoas iguais.<br />';
        if (count($pessoas) <= 2) {
            $this->mensagem = 'Informe no mínimo um pessoa para unificação.<br />';
            return false;
        }

                return false;
            }
        if (! $this->validaDadodUnificacao($pessoas)) {
            $this->mensagem = 'Erro ao processar os dados, recarregue a tela e tente novamente!';
            return false;
        }

            $codPessoas[] = (int) $explode[0];
        if (! $this->validaSeExisteUmaPessoaPrincipal($pessoas)) {
            $this->mensagem = 'Pessoa principal não informada';
            return false;
        }

        if (!count($codPessoas)) {
            $this->mensagem = 'Informe no mínimo um pessoa para unificação.<br />';
        if ($this->validaSeExisteMaisDeUmaPessoaPrincipal($pessoas)) {
            $this->mensagem = 'Não pode haver mais de uma pessoa principal';
            return false;
        }

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

    private function validaDadodUnificacao($pessoa)
    {
        foreach ($pessoa as $item) {
            if (! array_key_exists('idpes',$item) || !array_key_exists('pessoa_principal',$item)) {
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
        return current(array_values(array_filter($pessoas,
                static fn ($pessoas) => $pessoas['pessoa_principal'] === true)
        ))['idpes'];
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
