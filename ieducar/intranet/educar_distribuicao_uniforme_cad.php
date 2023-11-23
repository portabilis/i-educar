<?php

use App\Models\UniformDistribution;

return new class() extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public UniformDistribution $uniformDistribution;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_distribuicao_uniforme_lst.php?ref_cod_aluno='.request('ref_cod_aluno'));

        if (is_numeric(request('ref_cod_aluno')) && is_numeric(request('cod_distribuicao_uniforme'))) {
            $exists = UniformDistribution::query()
                ->where('id', request('cod_distribuicao_uniforme'))
                ->exists();
            if ($exists) {
                $this->uniformDistribution = UniformDistribution::find(request('cod_distribuicao_uniforme'));

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            } else {
                $this->uniformDistribution = new UniformDistribution();
            }
        } else {
            $this->uniformDistribution = new UniformDistribution();
        }

        $this->url_cancelar = $retorno == 'Editar'
            ? "educar_distribuicao_uniforme_det.php?ref_cod_aluno={$this->uniformDistribution->student_id}&cod_distribuicao_uniforme={$this->uniformDistribution->id}"
            : "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->uniformDistribution->studend_id}";

        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb(currentPage: 'Distribuições de uniforme escolar', breadcrumbs: [
            'educar_index.php' => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->uniformDistribution ?? $this->uniformDistribution = new UniformDistribution();

        $objEscola = new clsPmieducarEscola();
        $lista = $objEscola->lista();

        $escolaOpcoes = ['' => 'Selecione'];

        foreach ($lista as $escola) {
            $escolaOpcoes["{$escola['cod_escola']}"] = "{$escola['nome']}";
        }

        $this->campoOculto(nome: 'id', valor: $this->uniformDistribution->id);

        $this->campoNumero(nome: 'year', campo: 'Ano', valor: request(key: 'year', default: $this->uniformDistribution->year), tamanhovisivel: 4, tamanhomaximo: 4, obrigatorio: true);

        $this->inputsHelper()->dynamic(['instituicao', 'escola']);

        $this->campoQuebra();

        $options = [
            'label' => 'Tipo',
            'value' => request(key: 'type', default: $this->uniformDistribution->type),
            'resources' => [
                '' => 'Tipo',
                'Solicitado' => 'Solicitado',
                'Entregue' => 'Entregue',
            ],
            'required' => true,
        ];

        $this->inputsHelper()->select(attrName: 'type', inputOptions: $options);

        $this->inputsHelper()->date(attrName: 'distribution_date', inputOptions: [
            'label' => 'Data da distribuição',
            'value' => request(key: 'distribution_date', default: $this->uniformDistribution->distribution_date?->format('d/m/Y')),
            'placeholder' => '',
            'size' => 15,
            'required' => false,
            'visible' => false,
        ]);

        $this->campoQuebra();

        $this->inputsHelper()->checkbox(attrName: 'complete_kit', inputOptions: [
            'label' => 'Kit completo', 'value' => request(key: 'complete_kit', default: $this->uniformDistribution->complete_kit),
        ]);

        $this->inputsHelper()->integer(attrName: 'coat_pants_qty', inputOptions: [
            'required' => false,
            'label' => 'Agasalhos (calça)',
            'value' => request(key: 'coat_pants_qty', default: $this->uniformDistribution->coat_pants_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade',
        ]);

        $this->inputsHelper()->text(attrNames: 'coat_pants_tm', inputOptions: [
            'required' => false,
            'label' => '',
            'value' => request(key: 'coat_pants_tm', default: $this->uniformDistribution->coat_pants_tm),
            'max_length' => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer(attrName: 'coat_jacket_qty', inputOptions: [
            'required' => false,
            'label' => 'Agasalhos (jaqueta)',
            'value' => request(key: 'coat_jacket_qty', default: $this->uniformDistribution->coat_jacket_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade',
        ]);

        $this->inputsHelper()->text(attrNames: 'coat_jacket_tm', inputOptions: [
            'required' => false,
            'label' => '',
            'value' => request(key: 'coat_jacket_tm', default: $this->uniformDistribution->coat_jacket_tm),
            'max_length' => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer(attrName: 'shirt_short_qty', inputOptions: [
            'required' => false,
            'label' => 'Camisetas (manga curta)',
            'value' => request(key: 'shirt_short_qty', default: $this->uniformDistribution->shirt_short_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade',
        ]);

        $this->inputsHelper()->text(attrNames: 'shirt_short_tm', inputOptions: [
            'required' => false,
            'label' => '',
            'value' => request(key: 'shirt_short_tm', default: $this->uniformDistribution->shirt_short_tm),
            'max_length' => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer(attrName: 'shirt_long_qty', inputOptions: [
            'required' => false,
            'label' => 'Camisetas (manga longa)',
            'value' => request(key: 'shirt_long_qty', default: $this->uniformDistribution->shirt_long_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade',
        ]);

        $this->inputsHelper()->text(attrNames: 'shirt_long_tm', inputOptions: [
            'required' => false,
            'label' => '',
            'value' => request(key: 'shirt_long_tm', default: $this->uniformDistribution->shirt_long_tm),
            'max_length' => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer(attrName: 'kids_shirt_qty', inputOptions: [
            'required' => false,
            'label' => 'Camisetas infantis (sem manga)',
            'value' => request(key: 'kids_shirt_qty', default: $this->uniformDistribution->kids_shirt_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade',
        ]);

        $this->inputsHelper()->text(attrNames: 'kids_shirt_tm', inputOptions: [
            'required' => false,
            'label' => '',
            'value' => request(key: 'kids_shirt_tm', default: $this->uniformDistribution->kids_shirt_tm),
            'max_length' => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer(attrName: 'pants_jeans_qty', inputOptions: [
            'required' => false,
            'label' => 'Calças jeans',
            'value' => request(key: 'pants_jeans_qty', default: $this->uniformDistribution->pants_jeans_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade',
        ]);

        $this->inputsHelper()->text(attrNames: 'pants_jeans_tm', inputOptions: [
            'required' => false,
            'label' => '',
            'value' => request(key: 'pants_jeans_tm', default: $this->uniformDistribution->pants_jeans_tm),
            'max_length' => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer(attrName: 'socks_qty', inputOptions: [
            'required' => false,
            'label' => 'Meias',
            'value' => request(key: 'socks_qty', default: $this->uniformDistribution->socks_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade',
        ]);

        $this->inputsHelper()->text(attrNames: 'socks_tm', inputOptions: [
            'required' => false,
            'label' => '',
            'value' => request(key: 'socks_tm', default: $this->uniformDistribution->socks_tm),
            'max_length' => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer(attrName: 'skirt_qty', inputOptions: [
            'required' => false,
            'label' => 'Saias',
            'value' => request(key: 'skirt_qty', default: $this->uniformDistribution->skirt_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade',
        ]);

        $this->inputsHelper()->text(attrNames: 'skirt_tm', inputOptions: [
            'required' => false,
            'label' => '',
            'value' => request(key: 'skirt_tm', default: $this->uniformDistribution->skirt_tm),
            'max_length' => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer(attrName: 'shorts_tactel_qty', inputOptions: [
            'required' => false,
            'label' => 'Bermuda masculina (tecidos diversos)',
            'value' => request(key: 'shorts_tactel_qty', default: $this->uniformDistribution->shorts_tactel_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade',
        ]);

        $this->inputsHelper()->text(attrNames: 'shorts_tactel_tm', inputOptions: [
            'required' => false,
            'label' => '',
            'value' => request(key: 'shorts_tactel_tm', default: $this->uniformDistribution->shorts_tactel_tm),
            'max_length' => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer(attrName: 'shorts_coton_qty', inputOptions: [
            'required' => false,
            'label' => 'Bermuda feminina (tecidos diversos)',
            'value' => request(key: 'shorts_coton_qty', default: $this->uniformDistribution->shorts_coton_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade',
        ]);

        $this->inputsHelper()->text(attrNames: 'shorts_coton_tm', inputOptions: [
            'required' => false,
            'label' => '',
            'value' => request(key: 'shorts_coton_tm', default: $this->uniformDistribution->shorts_coton_tm),
            'max_length' => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer(attrName: 'sneakers_qty', inputOptions: [
            'required' => false,
            'label' => 'Tênis',
            'value' => request(key: 'sneakers_qty', default: $this->uniformDistribution->sneakers_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade',
        ]);

        $this->inputsHelper()->text(attrNames: 'sneakers_tm', inputOptions: [
            'required' => false,
            'label' => '',
            'value' => request(key: 'sneakers_tm', default: $this->uniformDistribution->sneakers_tm),
            'max_length' => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);
    }

    public function Novo()
    {
        $this->data = Portabilis_Date_Utils::brToPgSQL($this->data);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_distribuicao_uniforme_lst.php?ref_cod_aluno='.request('ref_cod_aluno'));

        $exists = UniformDistribution::where('student_id', request('ref_cod_aluno'))
            ->where('year', request('year'))
            ->exists();

        if ($exists) {
            $this->mensagem = 'Já existe uma distribuição cadastrada para este ano, por favor, verifique.<br>';

            return false;
        }

        if (request('type') == 'Entregue' && request('distribution_date') == null) {
            $this->mensagem = 'Informe a Data da distribuição do uniforme.<br>';

            return false;
        }

        request()->merge([
            'school_id' => request('ref_cod_escola'),
            'student_id' => request('ref_cod_aluno'),
        ]);

        $this->uniformDistribution = UniformDistribution::create(request()->all());

        if ($this->uniformDistribution) {
            $this->redirectIf(condition: true, url: 'educar_distribuicao_uniforme_lst.php?ref_cod_aluno='.request('ref_cod_aluno'));
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $this->data = Portabilis_Date_Utils::brToPgSQL($this->data);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_distribuicao_uniforme_lst.php?ref_cod_aluno='.request('ref_cod_aluno'));

        $uniformDistribution = UniformDistribution::where('student_id', request('ref_cod_aluno'))
            ->where('year', request('year'))
            ->first();

        if ($uniformDistribution->id != request('cod_distribuicao_uniforme')) {
            $this->mensagem = 'Já existe uma distribuição cadastrada para este ano, por favor, verifique.<br>';

            return false;
        }

        request()->merge([
            'school_id' => request('ref_cod_escola'),
            'student_id' => request('ref_cod_aluno'),
        ]);

        $uniformDistribution->update(request()->all());

        if ($uniformDistribution->save()) {
            $this->redirectIf(condition: true, url: 'educar_distribuicao_uniforme_lst.php?ref_cod_aluno='.request('ref_cod_aluno'));
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_distribuicao_uniforme_lst.php?ref_cod_aluno='.request('ref_cod_aluno'));

        $obj = UniformDistribution::find(request('id'));

        if ($obj->delete()) {
            $this->redirectIf(condition: true, url: 'educar_distribuicao_uniforme_lst.php?ref_cod_aluno='.request('ref_cod_aluno'));
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-distribuicao-uniforme-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Distribuição de uniforme';
        $this->processoAp = 578;
    }
};
