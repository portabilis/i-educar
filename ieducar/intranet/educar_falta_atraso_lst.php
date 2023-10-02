<?php

use App\Models\Enums\AbsenceDelayType;
use App\Models\LegacyAbsenceDelay;
use App\Services\EmployeeService;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $ref_cod_escola;

    public $ref_ref_cod_instituicao;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_servidor;

    public $tipo;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Faltas e atrasos - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $tmp_obj = new clsPmieducarServidor(cod_servidor: $this->ref_cod_servidor, ref_cod_deficiencia: null, ref_idesco: null, carga_horaria: null, data_cadastro: null, data_exclusao: null, ativo: null, ref_cod_instituicao: $this->ref_cod_instituicao);
        $tmp_obj->detalhe();

        $this->addCabecalhos([
            'Escola',
            'Instituição',
            'Matrícula',
            'Tipo',
            'Dia',
            'Horas',
            'Minutos',
        ]);

        $fisica = new clsPessoaFisica($this->ref_cod_servidor);
        $fisica = $fisica->detalhe();

        $this->campoOculto(nome: 'ref_cod_servidor', valor: $this->ref_cod_servidor);
        $this->campoRotulo(nome: 'nm_servidor', campo: 'Servidor', valor: $fisica['nome']);

        $this->inputsHelper()->dynamic(helperNames: 'instituicao', inputOptions: [
            'required' => false,
            'show-select' => true,
            'value' => $this->ref_cod_instituicao,
        ]);
        $this->inputsHelper()->dynamic(helperNames: 'escola', inputOptions: [
            'required' => false,
            'show-select' => true,
            'value' => $this->ref_cod_escola,
        ]);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET['pagina_' . $this->nome]) ? $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $query = LegacyAbsenceDelay::query()
            ->with(['employeeRole'])
            ->orderBy('tipo', 'ASC');

        if ($this->ref_cod_instituicao) {
            $query->where('ref_ref_cod_instituicao', $this->ref_cod_instituicao);
        }
        if ($this->ref_cod_escola) {
            $query->where('ref_cod_escola', $this->ref_cod_escola);
        }
        if ($this->ref_cod_servidor) {
            $query->where('ref_cod_servidor', $this->ref_cod_servidor);
        }

        $result = $query->paginate(perPage: $this->limite, pageName: 'pagina_' . $this->nome);

        $lista = $result->items();
        $total = $result->total();

        foreach ($lista as $registro) {
            // Recupera o nome da escola
            $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $registro['nm_escola'] = $det_ref_cod_escola['nome'];

            $obj_ins = new clsPmieducarInstituicao($registro['ref_ref_cod_instituicao']);
            $det_ins = $obj_ins->detalhe();

            $service = new EmployeeService();
            $horas = $service->getHoursCompensate(
                cod_servidor: $registro['ref_cod_servidor'],
                cod_escola: $registro['ref_cod_escola'],
                cod_instituicao: $registro['data_falta']
            );

            if ($horas) {
                $horas_aux = $horas['hora'];
                $minutos_aux = $horas['min'];
            }

            $horas_aux = $horas_aux - $registro['qtd_horas'];
            $minutos_aux = $minutos_aux - $registro['qtd_min'];

            if ($horas_aux > 0 && $minutos_aux < 0) {
                $horas_aux--;
                $minutos_aux += 60;
            }

            if ($horas_aux < 0 && $minutos_aux > 0) {
                $horas_aux--;
                $minutos_aux -= 60;
            }

            if ($horas_aux < 0) {
                $horas_aux = '(' . ($horas_aux * -1) . ')';
            }

            if ($minutos_aux < 0) {
                $minutos_aux = '(' . ($minutos_aux * -1) . ')';
            }

            $tipo = $registro['tipo'] == AbsenceDelayType::DELAY->value ? 'Atraso' : 'Falta';

            $urlHelper = CoreExt_View_Helper_UrlHelper::getInstance();
            $url = 'educar_falta_atraso_det.php';
            $options = [
                'query' => [
                    'cod_falta_atraso' => $registro['cod_falta_atraso'],
                    'ref_cod_servidor' => $registro['ref_cod_servidor'],
                    'ref_cod_escola' => $registro['ref_cod_escola'],
                    'ref_cod_instituicao' => $registro['ref_ref_cod_instituicao'],
                ],
            ];

            $dt = new DateTime($registro['data_falta_atraso']);
            $data = $dt->format('d/m/Y');

            $this->addLinhas([
                $urlHelper->l(text: $registro['nm_escola'], path: $url, options: $options),
                $urlHelper->l(text: $det_ins['nm_instituicao'], path: $url, options: $options),
                $urlHelper->l(text: $registro->employeeRole?->matricula, path: $url, options: $options),
                $urlHelper->l(text: $tipo, path: $url, options: $options),
                $urlHelper->l(text: $data, path: $url, options: $options),
                $urlHelper->l(text: $registro['tipo'] == AbsenceDelayType::DELAY->value ? $horas_aux : '-', path: $url, options: $options),
                $urlHelper->l(text: $registro['tipo'] == AbsenceDelayType::DELAY->value ? $minutos_aux : '-', path: $url, options: $options),
            ]);
        }

        $this->addPaginador2(
            strUrl: 'educar_falta_atraso_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );
        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green',
            ];

            $this->array_botao_url[] = sprintf(
                'educar_falta_atraso_cad.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_ref_cod_instituicao
            );
        }

        $this->array_botao[] = 'Voltar';

        $this->array_botao_url[] = sprintf(
            'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_cod_instituicao
        );

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Registro das faltas e atrasos do servidor', breadcrumbs: [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->titulo = 'Servidores - Falta Atraso';
        $this->processoAp = 635;
    }
};
