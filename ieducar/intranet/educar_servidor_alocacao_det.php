<?php

use App\Models\EmployeeAllocation;
use App\Models\LegacyBondType;
use App\Models\LegacyEmployeeRole;
use App\Models\LegacyPerson;

return new class extends clsDetalhe
{
    public $titulo;

    public $cod_servidor_alocacao;

    public $ref_cod_servidor;

    public $ref_cod_instituicao;

    public $ref_cod_servidor_funcao;

    public $ref_cod_funcionario_vinculo;

    public $ano;

    public $data_admissao;

    public $data_saida;

    public function Gerar()
    {
        $this->titulo = 'Servidor alocação - Detalhe';

        $this->cod_servidor_alocacao = $_GET['cod_servidor_alocacao'];

        $registro = is_numeric($this->cod_servidor_alocacao)
            ? EmployeeAllocation::query()->with(['school:cod_escola,ref_idpes', 'school.organization:idpes,fantasia'])->find($this->cod_servidor_alocacao)
            : null;

        if (!$registro) {
            $this->simpleRedirect('educar_servidor_lst.php');
        }

        $this->ref_cod_servidor = $registro['ref_cod_servidor'];
        $this->ref_cod_instituicao = $registro['ref_ref_cod_instituicao'];
        $this->ref_cod_servidor_funcao = $registro['ref_cod_servidor_funcao'];
        $this->data_admissao = $registro['data_admissao'];
        $this->data_saida = $registro['data_saida'];
        $this->ref_cod_funcionario_vinculo = $registro['ref_cod_funcionario_vinculo'];
        $this->ano = $registro['ano'];

        $nome = LegacyPerson::whereKey($this->ref_cod_servidor)->value('nome');

        $this->addDetalhe(['Servidor', "{$nome}"]);

        // Escola
        $this->addDetalhe(['Escola', "{$registro->school->organization?->fantasia}"]);

        // Ano
        $this->addDetalhe(['Ano', "{$registro['ano']}"]);

        // Periodo
        $periodo = [
            1 => 'Matutino',
            2 => 'Vespertino',
            3 => 'Noturno',
        ];

        $this->addDetalhe(['Periodo', "{$periodo[$registro['periodo']]}"]);

        // Carga horária
        $this->addDetalhe(['Carga horária', substr($registro['carga_horaria'], 0, -3)]);

        // Função
        if ($this->ref_cod_servidor_funcao) {
            $employeeRole = LegacyEmployeeRole::find($this->ref_cod_servidor_funcao);
            $this->addDetalhe(['Função', $employeeRole?->role?->name]);
        }

        // Vinculo
        if ($this->ref_cod_funcionario_vinculo) {
            $nomeVinculo = LegacyBondType::whereKey($registro['ref_cod_funcionario_vinculo'])->value('nm_vinculo');

            $this->addDetalhe(['Vinculo', $nomeVinculo]);
        }

        if (!empty($this->data_admissao)) {
            $this->addDetalhe(['Data de admissão', Portabilis_Date_Utils::pgSQLToBr($this->data_admissao)]);
        }

        if (!empty($this->data_saida)) {
            $this->addDetalhe(['Data de saída', Portabilis_Date_Utils::pgSQLToBr($this->data_saida)]);
        }

        $obj_permissoes = new clsPermissoes;
        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            $this->url_novo = "educar_servidor_alocacao_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
            $this->url_editar = "educar_servidor_alocacao_cad.php?cod_servidor_alocacao={$this->cod_servidor_alocacao}";
        }

        $this->url_cancelar = "educar_servidor_alocacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da alocação', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor alocação';
        $this->processoAp = 635;
    }
};
