<?php

use App\Models\Enums\AbsenceDelayType;
use App\Models\LegacyAbsenceDelay;
use App\Services\EmployeeService;
use App\Services\FileService;
use App\Services\UrlPresigner;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $cod_falta_atraso;

    public $ref_cod_escola;

    public $ref_cod_instituicao;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_servidor;

    public $tipo;

    public $data_falta_atraso;

    public $qtd_horas;

    public $qtd_min;

    public $justificada;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_servidor_funcao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_falta_atraso = $_GET['cod_falta_atraso'];
        $this->ref_cod_servidor = $_GET['ref_cod_servidor'];
        $this->ref_cod_escola = $_GET['ref_cod_escola'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 635,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_falta_atraso_lst.php'
        );

        if (is_numeric($this->cod_falta_atraso)) {
            $registro = LegacyAbsenceDelay::find($this->cod_falta_atraso)?->getAttributes();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->data_falta_atraso = dataFromPgToBr($this->data_falta_atraso);

                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d', $this->ref_cod_servidor, $this->ref_cod_instituicao);

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' falta/atraso do servidor', breadcrumbs: [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->form_enctype = ' enctype=\'multipart/form-data\'';

        // Primary keys
        $this->campoOculto(nome: 'cod_falta_atraso', valor: $this->cod_falta_atraso);
        $this->campoOculto(nome: 'ref_cod_servidor', valor: $this->ref_cod_servidor);

        $this->inputsHelper()->dynamic(helperNames: 'instituicao', inputOptions: ['value' => $this->ref_cod_instituicao]);
        $this->inputsHelper()->dynamic(helperNames: 'escola', inputOptions: ['value' => $this->ref_cod_escola]);

        // Text
        // @todo CoreExt_Enum
        $opcoes = AbsenceDelayType::getDescriptiveValues()->prepend('Selecione', '');

        $this->campoLista(nome: 'tipo', campo: 'Tipo', valor: $opcoes, default: $this->tipo);

        $funcoesDoServidor = $this->getFuncoesServidor($this->ref_cod_servidor);
        $funcoesDoServidor = array_replace([null => 'Selecione'], $funcoesDoServidor);
        $this->campoLista(nome: 'ref_cod_servidor_funcao', campo: 'Função', valor: $funcoesDoServidor, default: $this->ref_cod_servidor_funcao, acao: null, duplo: null, descricao: null, complemento: null, desabilitado: null, obrigatorio: true);

        $this->campoNumero(nome: 'qtd_horas', campo: 'Quantidade de Horas', valor: $this->qtd_horas, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoNumero(nome: 'qtd_min', campo: 'Quantidade de Minutos', valor: $this->qtd_min, tamanhovisivel: 30, tamanhomaximo: 255);

        $opcoes = [
            '' => 'Selecione',
            0 => 'Sim',
            1 => 'Não',
        ];

        $this->campoLista(nome: 'justificada', campo: 'Justificada', valor: $opcoes, default: $this->justificada);

        // Data
        $this->campoData(nome: 'data_falta_atraso', campo: 'Dia', valor: $this->data_falta_atraso, obrigatorio: true);

        if ($this->cod_falta_atraso == '') {
            $this->cod_falta_atraso = null;
        }

        $fileService = new FileService(new UrlPresigner());
        $files = $fileService->getFiles(LegacyAbsenceDelay::find($this->cod_falta_atraso));

        $this->addHtml(view('uploads.upload', ['files' => $files])->render());

    }

    private function getFuncoesServidor($codServidor)
    {
        return DB::table('pmieducar.servidor_funcao')
            ->select(DB::raw('cod_servidor_funcao, nm_funcao || coalesce( \' - \' || matricula, \'\') as funcao_matricula'))
            ->join(table: 'pmieducar.funcao', first: 'funcao.cod_funcao', operator: 'servidor_funcao.ref_cod_funcao')
            ->where([['servidor_funcao.ref_cod_servidor', $codServidor]])
            ->orderBy(column: 'matricula')
            ->get()
            ->pluck(value: 'funcao_matricula', key: 'cod_servidor_funcao')
            ->toArray();
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 635,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: sprintf(
                'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            )
        );

        if (Validator::make([
            'data' => $this->data_falta_atraso,
        ], [
            'data' => ['date_format:d/m/Y'],
        ])->fails()) {
            $this->mensagem = 'O dia informado é inválido.<br>';

            return false;
        }

        $this->data_falta_atraso = Portabilis_Date_Utils::brToPgSQL($this->data_falta_atraso);

        if ($this->tipo == 1 && ($this->qtd_horas == '' || $this->qtd_min == '')) {
            $this->mensagem = 'Preencha os campos de quantidade de horas e minutos.<br>';

            return false;
        }

        if ($this->tipo == 1) {
            $obj = new LegacyAbsenceDelay();
            $obj->ref_cod_escola = $this->ref_cod_escola;
            $obj->ref_ref_cod_instituicao = $this->ref_cod_instituicao;
            $obj->ref_usuario_cad = $this->pessoa_logada;
            $obj->ref_cod_servidor = $this->ref_cod_servidor;
            $obj->tipo = $this->tipo;
            $obj->data_falta_atraso = $this->data_falta_atraso;
            $obj->qtd_horas = $this->qtd_horas;
            $obj->qtd_min = $this->qtd_min;
            $obj->justificada = $this->justificada;
            $obj->ref_cod_servidor_funcao = $this->ref_cod_servidor_funcao;

        } elseif ($this->tipo == 2) {
            $db = new clsBanco();
            $dia_semana = $db->CampoUnico(sprintf('(SELECT EXTRACT (DOW FROM date \'%s\') + 1 )', $this->data_falta_atraso));

            $servive = new EmployeeService();
            $horas = $servive->getQuantityHours(
                cod_servidor: $this->ref_cod_servidor,
                cod_escola: $this->ref_cod_escola,
                cod_instituicao: $this->ref_cod_instituicao,
                dia_semana: $dia_semana
            );

            if ($horas) {
                $obj = new LegacyAbsenceDelay();
                $obj->ref_cod_escola = $this->ref_cod_escola;
                $obj->ref_ref_cod_instituicao = $this->ref_cod_instituicao;
                $obj->ref_usuario_cad = $this->pessoa_logada;
                $obj->ref_cod_servidor = $this->ref_cod_servidor;
                $obj->tipo = $this->tipo;
                $obj->data_falta_atraso = $this->data_falta_atraso;
                $obj->qtd_horas = $horas['hora'];
                $obj->qtd_min = $horas['min'];
                $obj->justificada = $this->justificada;
                $obj->ref_cod_servidor_funcao = $this->ref_cod_servidor_funcao;
            }
        }

        if ($obj->save()) {
            if ($this->file_url) {
                $fileService = new FileService(new UrlPresigner());
                $newFiles = json_decode($this->file_url);
                foreach ($newFiles as $file) {
                    $fileService->saveFile(
                        $file->url,
                        $file->size,
                        $file->originalName,
                        $file->extension,
                        LegacyAbsenceDelay::class,
                        $obj->getKey()
                    );
                }
            }

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
            $this->simpleRedirect(sprintf(
                'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            ));
        }

        $this->mensagem = 'Cadastro não realizado.<br />';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 635,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: sprintf(
                'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            )
        );

        if ($this->tipo == 1 && ($this->qtd_horas == '' || $this->qtd_min == '')) {
            $this->mensagem = 'Preencha os campos de quantidade de horas e minutos.<br>';

            return false;
        }

        $this->data_falta_atraso = Portabilis_Date_Utils::brToPgSQL($this->data_falta_atraso);
        if ($this->tipo == 1) {
            $obj = LegacyAbsenceDelay::find($this->cod_falta_atraso);
            $obj->ref_cod_escola = $this->ref_cod_escola;
            $obj->ref_ref_cod_instituicao = $this->ref_cod_instituicao;
            $obj->ref_usuario_exc = $this->pessoa_logada;
            $obj->ref_cod_servidor = $this->ref_cod_servidor;
            $obj->tipo = $this->tipo;
            $obj->data_falta_atraso = $this->data_falta_atraso;
            $obj->qtd_horas = $this->qtd_horas;
            $obj->qtd_min = $this->qtd_min;
            $obj->justificada = $this->justificada;
            $obj->ref_cod_servidor_funcao = $this->ref_cod_servidor_funcao;

        } elseif ($this->tipo == 2) {
            $obj_ser = new clsPmieducarServidor(
                cod_servidor: $this->ref_cod_servidor,
                ativo: 1,
                ref_cod_instituicao: $this->ref_cod_instituicao
            );

            $det_ser = $obj_ser->detalhe();
            $horas = floor($det_ser['carga_horaria']);
            $minutos = ($det_ser['carga_horaria'] - $horas) * 60;

            $obj = LegacyAbsenceDelay::find($this->cod_falta_atraso);
            $obj->ref_cod_escola = $this->ref_cod_escola;
            $obj->ref_ref_cod_instituicao = $this->ref_cod_instituicao;
            $obj->ref_usuario_exc = $this->pessoa_logada;
            $obj->ref_cod_servidor = $this->ref_cod_servidor;
            $obj->tipo = $this->tipo;
            $obj->data_falta_atraso = $this->data_falta_atraso;
            $obj->qtd_horas = $horas;
            $obj->qtd_min = $minutos;
            $obj->justificada = $this->justificada;
            $obj->ref_cod_servidor_funcao = $this->ref_cod_servidor_funcao;
        }
        if ($obj->save()) {

            $fileService = new FileService(new UrlPresigner());

            if ($this->file_url) {
                $newFiles = json_decode($this->file_url);
                foreach ($newFiles as $file) {
                    $fileService->saveFile(
                        $file->url,
                        $file->size,
                        $file->originalName,
                        $file->extension,
                        LegacyAbsenceDelay::class,
                        $obj->getKey()
                    );
                }
            }

            if ($this->file_url_deleted) {
                $deletedFiles = explode(',', $this->file_url_deleted);
                $fileService->deleteFiles($deletedFiles);
            }

            $this->mensagem .= 'Edição efetuada com sucesso.<br />';
            $this->simpleRedirect(sprintf(
                'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            ));
        }

        $this->mensagem = 'Edição não realizada.<br />';

        return false;
    }

    public function Excluir()
    {
        $this->data_falta_atraso = Portabilis_Date_Utils::brToPgSQL($this->data_falta_atraso);
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(
            int_processo_ap: 635,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: sprintf(
                'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            )
        );

        $obj = LegacyAbsenceDelay::find($this->cod_falta_atraso);

        if ($obj->delete()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
            $this->simpleRedirect(sprintf(
                'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            ));
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-falta-atraso-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Servidores - Falta Atraso';
        $this->processoAp = 635;
    }
};
