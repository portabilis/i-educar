<?php

use Illuminate\Support\Facades\DB;

return new class extends clsCadastro {
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

        $this->cod_falta_atraso    = $_GET['cod_falta_atraso'];
        $this->ref_cod_servidor    = $_GET['ref_cod_servidor'];
        $this->ref_cod_escola      = $_GET['ref_cod_escola'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            635,
            $this->pessoa_logada,
            7,
            'educar_falta_atraso_lst.php'
        );

        if (is_numeric($this->cod_falta_atraso)) {
            $obj = new clsPmieducarFaltaAtraso($this->cod_falta_atraso);
            $registro  = $obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->data_falta_atraso = dataFromPgToBr($this->data_falta_atraso);

                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = sprintf('educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d', $this->ref_cod_servidor, $this->ref_cod_instituicao);

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' falta/atraso do servidor', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // Primary keys
        $this->campoOculto('cod_falta_atraso', $this->cod_falta_atraso);
        $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);

        $this->inputsHelper()->dynamic('instituicao', ['value' => $this->ref_cod_instituicao]);
        $this->inputsHelper()->dynamic('escola', ['value' => $this->ref_cod_escola]);

        // Text
        // @todo CoreExt_Enum
        $opcoes = [
            '' => 'Selecione',
            1  => 'Atraso',
            2  => 'Falta'
        ];

        $this->campoLista('tipo', 'Tipo', $opcoes, $this->tipo);

        $funcoesDoServidor = $this->getFuncoesServidor($this->ref_cod_servidor);
        $funcoesDoServidor = array_replace([null => 'Selecione'], $funcoesDoServidor);
        $this->campoLista('ref_cod_servidor_funcao', 'Função', $funcoesDoServidor, $this->ref_cod_servidor_funcao, null, null, null, null, null, false);

        $this->campoNumero('qtd_horas', 'Quantidade de Horas', $this->qtd_horas, 30, 255, false);
        $this->campoNumero('qtd_min', 'Quantidade de Minutos', $this->qtd_min, 30, 255, false);

        $opcoes = [
            '' => 'Selecione',
            0  => 'Sim',
            1  => 'Não'
        ];

        $this->campoLista('justificada', 'Justificada', $opcoes, $this->justificada);

        // Data
        $this->campoData('data_falta_atraso', 'Dia', $this->data_falta_atraso, true);
    }

    private function getFuncoesServidor($codServidor)
    {
        return DB::table('pmieducar.servidor_funcao')
            ->select(DB::raw('cod_servidor_funcao, nm_funcao || coalesce( \' - \' || matricula, \'\') as funcao_matricula'))
            ->join('pmieducar.funcao', 'funcao.cod_funcao', 'servidor_funcao.ref_cod_funcao')
            ->where([['servidor_funcao.ref_cod_servidor', $codServidor]])
            ->orderBy('matricula', 'asc')
            ->get()
            ->pluck('funcao_matricula', 'cod_servidor_funcao')
            ->toArray();
    }

    public function Novo()
    {
        $this->data_falta_atraso = Portabilis_Date_Utils::brToPgSQL($this->data_falta_atraso);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            635,
            $this->pessoa_logada,
            7,
            sprintf(
                'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            )
        );

        if ($this->tipo == 1) {
            $obj = new clsPmieducarFaltaAtraso(
                null,
                $this->ref_cod_escola,
                $this->ref_cod_instituicao,
                null,
                $this->pessoa_logada,
                $this->ref_cod_servidor,
                $this->tipo,
                $this->data_falta_atraso,
                $this->qtd_horas,
                $this->qtd_min,
                $this->justificada,
                null,
                null,
                1,
                $this->ref_cod_servidor_funcao
            );
        } elseif ($this->tipo == 2) {
            $db = new clsBanco();
            $dia_semana = $db->CampoUnico(sprintf('(SELECT EXTRACT (DOW FROM date \'%s\') + 1 )', $this->data_falta_atraso));

            $obj_ser = new clsPmieducarServidor();
            $horas   = $obj_ser->qtdhoras($this->ref_cod_servidor, $this->ref_cod_escola, $this->ref_cod_instituicao, $dia_semana);

            if ($horas) {
                $obj = new clsPmieducarFaltaAtraso(
                    null,
                    $this->ref_cod_escola,
                    $this->ref_cod_instituicao,
                    null,
                    $this->pessoa_logada,
                    $this->ref_cod_servidor,
                    $this->tipo,
                    $this->data_falta_atraso,
                    $horas['hora'],
                    $horas['min'],
                    $this->justificada,
                    null,
                    null,
                    1,
                    $this->ref_cod_servidor_funcao
                );
            }
        }

        $cadastrou = $obj->cadastra();

        if ($cadastrou) {
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
            635,
            $this->pessoa_logada,
            7,
            sprintf(
                'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            )
        );
        $this->data_falta_atraso = Portabilis_Date_Utils::brToPgSQL($this->data_falta_atraso);
        if ($this->tipo == 1) {
            $obj = new clsPmieducarFaltaAtraso(
                $this->cod_falta_atraso,
                $this->ref_cod_escola,
                $this->ref_cod_instituicao,
                $this->pessoa_logada,
                null,
                $this->ref_cod_servidor,
                $this->tipo,
                $this->data_falta_atraso,
                $this->qtd_horas,
                $this->qtd_min,
                $this->justificada,
                null,
                null,
                1,
                $this->ref_cod_servidor_funcao
            );
        } elseif ($this->tipo == 2) {
            $obj_ser = new clsPmieducarServidor(
                $this->ref_cod_servidor,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->ref_cod_instituicao
            );

            $det_ser = $obj_ser->detalhe();
            $horas   = floor($det_ser['carga_horaria']);
            $minutos = ($det_ser['carga_horaria'] - $horas) * 60;
            $obj = new clsPmieducarFaltaAtraso(
                $this->cod_falta_atraso,
                $this->ref_cod_escola,
                $this->ref_cod_instituicao,
                $this->pessoa_logada,
                null,
                $this->ref_cod_servidor,
                $this->tipo,
                $this->data_falta_atraso,
                $horas,
                $minutos,
                $this->justificada,
                null,
                null,
                1,
                $this->ref_cod_servidor_funcao
            );
        }
        $editou = $obj->edita();
        if ($editou) {
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
            635,
            $this->pessoa_logada,
            7,
            sprintf(
                'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            )
        );

        $obj = new clsPmieducarFaltaAtraso(
            $this->cod_falta_atraso,
            $this->ref_cod_escola,
            $this->ref_ref_cod_instituicao,
            $this->pessoa_logada,
            $this->pessoa_logada,
            $this->ref_cod_servidor,
            $this->tipo,
            $this->data_falta_atraso,
            $this->qtd_horas,
            $this->qtd_min,
            $this->justificada,
            $this->data_cadastro,
            $this->data_exclusao,
            0
        );
        $excluiu = $obj->excluir();
        if ($excluiu) {
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
