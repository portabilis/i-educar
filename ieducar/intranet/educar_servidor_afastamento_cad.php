<?php

use App\Models\EmployeeWithdrawal;
use App\Models\LegacyPerson;
use App\Models\WithdrawalReason;
use App\Services\FileService;
use App\Services\UrlPresigner;
use App\Support\View\Employee\EmployeeReturn;
use Illuminate\Support\Carbon;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $id;

    public $ref_cod_servidor;

    public $sequencial;

    public $ref_cod_instituicao;

    public $ref_cod_motivo_afastamento;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $data_cadastro;

    public $data_exclusao;

    public $data_retorno;

    public $data_saida;

    public $ativo;

    public $status;

    public $parametros;

    /**
     * Implementação do método clsCadastro::Inicializar()
     *
     * @see ieducar/intranet/include/clsCadastro#Inicializar()
     */
    public function Inicializar()
    {
        $retorno = 'Novo';
        $this->status = clsCadastro::NOVO;

        $this->ref_cod_instituicao = $this->getQueryString('ref_cod_instituicao');
        $this->ref_cod_servidor = $this->getQueryString('ref_cod_servidor');
        $this->sequencial = $this->getQueryString('sequencial');
        $this->retornar_servidor = $this->getQueryString('retornar_servidor');

        $urlPermite = sprintf(
            'educar_servidor_det.php?cod_servidor=%s&ref_cod_instituicao=%s',
            $this->ref_cod_servidor,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $urlPermite);

        if (is_numeric($this->ref_cod_servidor) && is_numeric($this->sequencial) &&
            is_numeric($this->ref_cod_instituicao)) {
            $registro = EmployeeWithdrawal::query()
                ->where('ref_cod_servidor', $this->ref_cod_servidor)
                ->where('ref_ref_cod_instituicao', $this->ref_cod_instituicao)
                ->where('sequencial', $this->sequencial)
                ->first()?->toArray();

            if ($registro) {
                // Passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                if ($this->data_retorno) {
                    $this->data_retorno = dataFromPgToBr($this->data_retorno);
                }

                if ($this->data_saida) {
                    $this->data_saida = dataFromPgToBr($this->data_saida);
                }

                $retorno = 'Editar';
                $this->status = clsCadastro::EDITAR;
            }
        }

        $this->url_cancelar = sprintf(
            'educar_servidor_det.php?cod_servidor=%s&ref_cod_instituicao=%s',
            $this->ref_cod_servidor,
            $this->ref_cod_instituicao
        );

        $this->nome_url_cancelar = 'Cancelar';

        // Início > Servidores > Nome > Afastamento (ou Retorno)
        $this->breadcrumb($this->retornar_servidor == EmployeeReturn::SIM ? 'Retorno' : 'Afastamento', [
            url('intranet/educar_servidor_lst.php') => 'Servidores',
            url("intranet/educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}") => LegacyPerson::whereKey($this->ref_cod_servidor)->value('nome'),
        ]);

        return $retorno;
    }

    /**
     * Implementação do método clsCadastro::Gerar()
     *
     * @see ieducar/intranet/include/clsCadastro#Gerar()
     */
    public function Gerar()
    {
        $this->form_enctype = ' enctype=\'multipart/form-data\'';
        $this->campoOculto('id', $this->id);
        $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
        $this->campoOculto('sequencial', $this->sequencial);
        $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        $this->campoOculto('retornar_servidor', $this->retornar_servidor);

        $opcoes = WithdrawalReason::query()
            ->orderBy('nm_motivo', 'ASC')
            ->pluck('nm_motivo', 'cod_motivo_afastamento')
            ->prepend('Selecione', '');

        if ($this->status == clsCadastro::NOVO || $this->retornar_servidor != EmployeeReturn::SIM) {
            $this->campoLista(
                'ref_cod_motivo_afastamento',
                'Motivo Afastamento',
                $opcoes,
                $this->ref_cod_motivo_afastamento
            );
        } elseif ($this->status == clsCadastro::EDITAR) {
            $this->campoLista(
                'ref_cod_motivo_afastamento',
                'Motivo Afastamento',
                $opcoes,
                $this->ref_cod_motivo_afastamento,
                '',
                false,
                '',
                '',
                true
            );
        }

        // Datas para registro
        // Se novo registro
        if ($this->status == clsCadastro::NOVO || $this->retornar_servidor != EmployeeReturn::SIM) {
            $this->campoData('data_saida', 'Data de Afastamento', $this->data_saida, true);
        }
        // Se edição, mostra a data de afastamento
        elseif ($this->status == clsCadastro::EDITAR) {
            $this->campoOculto('data_saida', $this->data_saida);
            $this->campoRotulo('data_saida', 'Data de Afastamento', $this->data_saida);
        }

        // Se edição, mostra campo para entrar com data de retornoc
        if ($this->retornar_servidor == EmployeeReturn::SIM || $this->data_retorno) {
            $this->campoData('data_retorno', 'Data de Retorno', $this->data_retorno, true);
        }

        if ($this->retornar_servidor != EmployeeReturn::SIM) {
            if ($this->id == '') {
                $this->id = null;
            }
            $fileService = new FileService(new UrlPresigner);
            $files = $fileService->getFiles(EmployeeWithdrawal::find($this->id));
            $this->addHtml(view('uploads.upload', ['files' => $files])->render());
        }
    }

    public function Novo()
    {
        $this->data_saida = formatDateParse($this->data_saida, 'Y-m-d');
        if ($this->data_saida == null || $this->data_saida <= date('Y-m-d', strtotime('-1 year'))) {
            $this->data_saida = null;
            $this->mensagem = 'Data de Afastamento Inválida.<br>';

            return false;
        }
        $this->data_retorno = dataToBanco($this->data_retorno);

        $this->ref_cod_servidor = isset($_POST['ref_cod_servidor']) ? $_POST['ref_cod_servidor'] : null;

        $urlPermite = sprintf(
            'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $urlPermite);

        $withdrawal = new EmployeeWithdrawal;
        $withdrawal->ref_cod_servidor = $this->ref_cod_servidor;
        $withdrawal->ref_usuario_cad = $this->pessoa_logada;
        $withdrawal->ref_cod_motivo_afastamento = $this->ref_cod_motivo_afastamento;
        $withdrawal->data_retorno = $this->data_retorno ? formatDateParse($this->data_retorno, 'Y-m-d') : null;
        $withdrawal->data_saida = formatDateParse($this->data_saida, 'Y-m-d');
        $withdrawal->ref_ref_cod_instituicao = $this->ref_cod_instituicao;
        $withdrawal->sequencial = null;

        $cadastrou = $withdrawal->save();
        if (!$cadastrou) {
            // Inalcançável: `save()` devolve verdadeiro ou lança exceção.
            // @codeCoverageIgnoreStart
            $this->mensagem = 'Cadastro não realizado.<br>';

            return false;
            // @codeCoverageIgnoreEnd
        }

        $fileService = new FileService(new UrlPresigner);

        if ($this->file_url) {
            $newFiles = json_decode($this->file_url);
            foreach ($newFiles as $file) {
                $fileService->saveFile(
                    $file->url,
                    $file->size,
                    $file->originalName,
                    $file->extension,
                    EmployeeWithdrawal::class,
                    $withdrawal->getKey()
                );
            }
        }

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
        $this->simpleRedirect("educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}");
    }

    public function Editar()
    {
        $this->data_saida = formatDateParse(str_replace('%2F', '/', $this->data_saida), 'Y-m-d');
        if ($this->data_saida == null) {
            $this->data_saida = null;
            $this->mensagem = 'Data de Afastamento Inválida.<br>';

            return false;
        }

        $urlPermite = sprintf(
            'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $urlPermite);

        $returnDate = $this->data_retorno ? dataToBanco($this->data_retorno) : $this->data_retorno;

        if ($this->data_saida && $returnDate) {
            $returnDate = Carbon::createFromFormat('Y-m-d', $returnDate);
            $exitDate = Carbon::createFromFormat('Y-m-d', $this->data_saida);
            if (!$this->validateDates($exitDate, $returnDate)) {
                $this->mensagem = 'A data de retorno não pode ser inferior à data de afastamento.';

                return false;
            }
            $returnDate = $returnDate->format('Y-m-d');
        }

        $withdrawal = EmployeeWithdrawal::query()
            ->where('ref_cod_servidor', $this->ref_cod_servidor)
            ->where('ref_ref_cod_instituicao', $this->ref_cod_instituicao)
            ->where('sequencial', $this->sequencial)
            ->first();

        if (!is_null($this->ref_cod_motivo_afastamento)) {
            $withdrawal->ref_cod_motivo_afastamento = $this->ref_cod_motivo_afastamento;
        }
        $withdrawal->data_retorno = $returnDate;
        $withdrawal->data_saida = $this->data_saida;

        $editou = $withdrawal->save();
        if ($editou) {
            $fileService = new FileService(new UrlPresigner);

            if ($this->file_url) {
                $newFiles = json_decode($this->file_url);
                foreach ($newFiles as $file) {
                    $fileService->saveFile(
                        $file->url,
                        $file->size,
                        $file->originalName,
                        $file->extension,
                        EmployeeWithdrawal::class,
                        $this->id
                    );
                }
            }

            if ($this->file_url_deleted) {
                $deletedFiles = explode(',', $this->file_url_deleted);
                $fileService->deleteFiles($deletedFiles);
            }

            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect("educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}");
        }

        // Inalcançável: `save()` devolve verdadeiro ou lança exceção.
        // @codeCoverageIgnoreStart
        $this->mensagem = 'Edição não realizada.<br>';

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Implementação do método clsCadastro::Excluir()
     *
     * @see ieducar/intranet/include/clsCadastro#Excluir()
     */
    public function Excluir()
    {
        $urlPermite = sprintf(
            'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7, $urlPermite);

        $withdrawal = EmployeeWithdrawal::query()
            ->where('ref_cod_servidor', $this->ref_cod_servidor)
            ->where('ref_ref_cod_instituicao', $this->ref_cod_instituicao)
            ->where('sequencial', $this->sequencial)
            ->first();

        $excluiu = $withdrawal->delete();

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_servidor_afastamento_lst.php');
        }

        // Inalcançável: `delete()` devolve verdadeiro ou lança exceção.
        // @codeCoverageIgnoreStart
        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
        // @codeCoverageIgnoreEnd
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor Afastamento';
        $this->processoAp = '635';
    }

    private function validateDates(Carbon $exitDate, Carbon $returnDate)
    {
        return $returnDate->gte($exitDate);
    }
};
