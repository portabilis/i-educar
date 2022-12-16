<?php

use App\Models\UniformDistribution;

return new class extends clsCadastro {
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
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno=".request('ref_cod_aluno'));

        if (is_numeric(request('ref_cod_aluno')) && is_numeric(request('cod_distribuicao_uniforme'))) {
            $this->uniformDistribution = UniformDistribution::find(request('cod_distribuicao_uniforme'));

            if ($this->uniformDistribution) {

                if ($obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        } else {
            $this->uniformDistribution = new UniformDistribution();
        }

        $this->url_cancelar = $retorno == 'Editar'
            ? "educar_distribuicao_uniforme_det.php?ref_cod_aluno={$this->uniformDistribution->student_id}&cod_distribuicao_uniforme={$this->uniformDistribution->id}"
            : "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->uniformDistribution->studend_id}";

        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Distribuições de uniforme escolar', [
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

        $this->campoOculto('id', $this->uniformDistribution->id);

        $this->campoNumero('year', 'Ano', request('year', $this->uniformDistribution->year), 4, 4, true);

        $this->inputsHelper()->dynamic(['instituicao', 'escola']);

        $this->campoQuebra();

        $options = [
            'label' => 'Tipo',
            'value' => request('type', $this->uniformDistribution->type),
            'resources' => [
                '' => 'Tipo',
                'Solicitado' => 'Solicitado',
                'Entregue' => 'Entregue'
            ],
            'required' => true,
        ];

        $this->inputsHelper()->select('type', $options);

        $this->inputsHelper()->date('distribution_date', [
            'label' => 'Data da distribuição',
            'value' => request('distribution_date', $this->uniformDistribution->distribution_date?->format('d/m/Y')),
            'placeholder' => '',
            'size' => 15,
            'required' => false,
            'visible' => false,
        ]);

        $this->campoQuebra();


        $this->inputsHelper()->checkbox('complete_kit', [
            'label' => 'Kit completo', 'value' => request('complete_kit', $this->uniformDistribution->complete_kit)
        ]);

        $this->inputsHelper()->integer('coat_pants_qty', [
            'required' => false,
            'label' => 'Agasalhos (calça)',
            'value' => request('coat_pants_qty', $this->uniformDistribution->coat_pants_qty),
            'max_length' => 2,
            'size' => 15,
            'inline'  => true,
            'placeholder' => 'Quantidade'
        ]);

        $this->inputsHelper()->text('coat_pants_tm', [
            'required' => false,
            'label' => '',
            'value' => request('coat_pants_tm', $this->uniformDistribution->coat_pants_tm),
            'max_length'  => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer('coat_jacket_qty', [
            'required' => false,
            'label' => 'Agasalhos (jaqueta)',
            'value' => request('coat_jacket_qty', $this->uniformDistribution->coat_jacket_qty),
            'max_length' => 2,
            'size' => 15,
            'inline'  => true,
            'placeholder' => 'Quantidade'
        ]);

        $this->inputsHelper()->text('coat_jacket_tm', [
            'required' => false,
            'label' => '',
            'value' => request('coat_jacket_tm', $this->uniformDistribution->coat_jacket_tm),
            'max_length'  => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer('shirt_short_qty', [
            'required' => false,
            'label' => 'Camisetas (manga curta)',
            'value' => request('shirt_short_qty', $this->uniformDistribution->shirt_short_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade'
        ]);

        $this->inputsHelper()->text('shirt_short_tm', [
            'required' => false,
            'label' => '',
            'value' => request('shirt_short_tm', $this->uniformDistribution->shirt_short_tm),
            'max_length'  => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer('shirt_long_qty', [
            'required' => false,
            'label' => 'Camisetas (manga longa)',
            'value' => request('shirt_long_qty', $this->uniformDistribution->shirt_long_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade'
        ]);

        $this->inputsHelper()->text('shirt_long_tm', [
            'required' => false,
            'label' => '',
            'value' => request('shirt_long_tm', $this->uniformDistribution->shirt_long_tm),
            'max_length'  => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer('kids_shirt_qty', [
            'required' => false,
            'label' => 'Camisetas infantis (sem manga)',
            'value' => request('kids_shirt_qty', $this->uniformDistribution->kids_shirt_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade'
        ]);

        $this->inputsHelper()->text('kids_shirt_tm', [
            'required' => false,
            'label' => '',
            'value' => request('kids_shirt_tm', $this->uniformDistribution->kids_shirt_tm),
            'max_length'  => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer('pants_jeans_qty', [
            'required' => false,
            'label' => 'Calças jeans',
            'value' => request('pants_jeans_qty', $this->uniformDistribution->pants_jeans_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade'
        ]);

        $this->inputsHelper()->text('pants_jeans_tm', [
            'required' => false,
            'label' => '',
            'value' => request('pants_jeans_tm', $this->uniformDistribution->pants_jeans_tm),
            'max_length'  => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer('socks_qty', [
            'required' => false,
            'label' => 'Meias',
            'value' => request('socks_qty', $this->uniformDistribution->socks_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade'
        ]);

        $this->inputsHelper()->text('socks_tm', [
            'required' => false,
            'label' => '',
            'value' => request('socks_tm', $this->uniformDistribution->socks_tm),
            'max_length'  => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer('skirt_qty', [
            'required' => false,
            'label' => 'Saias',
            'value' => request('skirt_qty', $this->uniformDistribution->skirt_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade'
        ]);

        $this->inputsHelper()->text('skirt_tm', [
            'required' => false,
            'label' => '',
            'value' => request('skirt_tm', $this->uniformDistribution->skirt_tm),
            'max_length'  => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer('shorts_tactel_qty', [
            'required' => false,
            'label' => 'Bermudas tactels (masculino)',
            'value' => request('shorts_tactel_qty', $this->uniformDistribution->shorts_tactel_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade'
        ]);

        $this->inputsHelper()->text('shorts_tactel_tm', [
            'required' => false,
            'label' => '',
            'value' => request('shorts_tactel_tm', $this->uniformDistribution->shorts_tactel_tm),
            'max_length'  => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer('shorts_coton_qty', [
            'required' => false,
            'label' => 'Bermudas coton (feminino)',
            'value' => request('shorts_coton_qty', $this->uniformDistribution->shorts_coton_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade'
        ]);

        $this->inputsHelper()->text('shorts_coton_tm', [
            'required' => false,
            'label' => '',
            'value' => request('shorts_coton_tm', $this->uniformDistribution->shorts_coton_tm),
            'max_length' => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);

        $this->inputsHelper()->integer('sneakers_qty', [
            'required' => false,
            'label' => 'Tênis',
            'value' => request('sneakers_qty', $this->uniformDistribution->sneakers_qty),
            'max_length' => 2,
            'size' => 15,
            'inline' => true,
            'placeholder' => 'Quantidade'
        ]);

        $this->inputsHelper()->text('sneakers_tm', [
            'required' => false,
            'label' => '',
            'value' => request('sneakers_tm', $this->uniformDistribution->sneakers_tm),
            'max_length'  => 10,
            'size' => 15,
            'placeholder' => 'Tamanho',
        ]);
    }

    public function Novo()
    {
        $this->data = Portabilis_Date_Utils::brToPgSQL($this->data);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno=".request('ref_cod_aluno'));

        $exists = UniformDistribution::where('student_id', request('ref_cod_aluno'))
            ->where('year', request('year'))
            ->exists();

        if ($exists) {
            $this->mensagem = 'Já existe uma distribuição cadastrada para este ano, por favor, verifique.<br>';
            return false;
        }

        request()->merge([
            'school_id' => request('ref_cod_escola'),
            'student_id' => request('ref_cod_aluno'),
        ]);

        $this->uniformDistribution = UniformDistribution::create(request()->all());

        if ($this->uniformDistribution) {
            $this->redirectIf(true, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno=".request('ref_cod_aluno'));
        }

        $this->mensagem = 'Cadastro não realizado.<br>';
        return false;
    }

    public function Editar()
    {
        $this->data = Portabilis_Date_Utils::brToPgSQL($this->data);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno=".request('ref_cod_aluno'));

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
            $this->redirectIf(true, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno=".request('ref_cod_aluno'));
        }

        $this->mensagem = 'Edição não realizada.<br>';
        return false;
    }

    public function Excluir()
    {
        dd(request()->all());
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno=".request('ref_cod_aluno'));

        $obj = UniformDistribution::find(request('id'));

        if ($obj->delete()) {
            $this->redirectIf(true, "educar_distribuicao_uniforme_lst.php?ref_cod_aluno=".request('ref_cod_aluno'));
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
