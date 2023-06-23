<?php

use App\Models\LegacySequenceGrade;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $ref_serie_origem;

    public $ref_serie_destino;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $id;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public $ref_curso_origem;

    public $ref_curso_destino;

    public $serie_origem_old;

    public $serie_destino_old;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(587, $this->pessoa_logada, 3, 'educar_sequencia_serie_lst.php');
        if (is_numeric($this->id)) {
            $registro = LegacySequenceGrade::query()
                ->find($this->id);

            $this->ref_serie_origem = $registro['ref_serie_origem'];
            $this->ref_serie_destino = $registro['ref_serie_destino'];

            if ($registro) {
                $this->ref_curso_origem = $registro->gradeOrigin->ref_cod_curso;
                $this->ref_cod_instituicao = $registro->gradeOrigin->course->ref_cod_instituicao;
                $this->ref_curso_destino = $registro->gradeDestiny->ref_cod_curso;

                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(587, $this->pessoa_logada, 3)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_sequencia_serie_det.php?id={$this->id}" : 'educar_sequencia_serie_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' sequência de enturmação', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        $this->campoOculto('serie_origem_old', $this->ref_serie_origem);
        $this->campoOculto('serie_destino_old', $this->ref_serie_destino);
        // foreign keys
        if ($nivel_usuario == 1) {
            $GLOBALS['nivel_usuario_fora'] = 1;
            $objInstituicao = new clsPmieducarInstituicao();
            $opcoes = ['' => 'Selecione'];
            $objInstituicao->setOrderby('nm_instituicao ASC');
            $lista = $objInstituicao->lista();
            if (is_array($lista)) {
                foreach ($lista as $linha) {
                    $opcoes[$linha['cod_instituicao']] = $linha['nm_instituicao'];
                }
            }
            $this->campoLista('ref_cod_instituicao', 'Instituição', $opcoes, $this->ref_cod_instituicao);
        } else {
            $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
            $obj_usuario_det = $obj_usuario->detalhe();
            $this->ref_cod_instituicao = $obj_usuario_det['ref_cod_instituicao'];
        }

        $opcoes = ['' => 'Selecione'];
        $opcoes_ = ['' => 'Selecione'];

        // EDITAR
        if ($this->ref_cod_instituicao) {
            $objTemp = new clsPmieducarCurso();
            $objTemp->setOrderby('nm_curso');
            $lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 1, null, $this->ref_cod_instituicao);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_curso']] = $this->concatNameAndDescription('nm_curso', $registro);
                    $opcoes_[$registro['cod_curso']] = $this->concatNameAndDescription('nm_curso', $registro);
                }
            }
        }

        $this->campoLista('ref_curso_origem', 'Curso Origem', $opcoes, $this->ref_curso_origem, '', true);
        $this->campoLista('ref_curso_destino', ' Curso Destino', $opcoes_, $this->ref_curso_destino);

        // primary keys

        $opcoes = ['' => 'Selecione'];
        $opcoes_ = ['' => 'Selecione'];

        if ($this->ref_curso_origem) {
            $objTemp = new clsPmieducarSerie();
            $objTemp->setOrderby('nm_serie ASC');
            $lista = $objTemp->lista(null, null, null, $this->ref_curso_origem, null, null, null, null, null, null, null, null, 1);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_serie']] = $this->concatNameAndDescription('nm_serie', $registro);
                }
            }
        }
        if ($this->ref_curso_destino) {
            $objTemp = new clsPmieducarSerie();
            $objTemp->setOrderby('nm_serie ASC');
            $lista = $objTemp->lista(null, null, null, $this->ref_curso_destino, null, null, null, null, null, null, null, null, 1);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes_[$registro['cod_serie']] = $this->concatNameAndDescription('nm_serie', $registro);
                }
            }
        }

        $this->campoLista('ref_serie_origem', 'Série Origem', $opcoes, $this->ref_serie_origem, null, true);
        $this->campoLista('ref_serie_destino', ' Série Destino', $opcoes_, $this->ref_serie_destino);

        $this->campoOculto('nivel_usuario', $nivel_usuario);
    }

    private function concatNameAndDescription($dataName, array $data): string
    {
        return $data[$dataName] . (!empty($data['descricao']) ? ' - ' . $data['descricao'] : '');
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(587, $this->pessoa_logada, 3, 'educar_sequencia_serie_lst.php');

        $det_sequencia = LegacySequenceGrade::query()
            ->find($this->id);

        if (!$det_sequencia) {
            $cadastrou = LegacySequenceGrade::create([
                'ref_serie_origem' => $this->ref_serie_origem,
                'ref_serie_destino' => $this->ref_serie_destino,
                'ref_usuario_cad' => $this->pessoa_logada,
                'ativo' => 1,
            ]);

            if ($cadastrou) {
                $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';

                throw new HttpResponseException(
                    new RedirectResponse('educar_sequencia_serie_lst.php')
                );
            }
        } else {
            $det_sequencia->fill([
                'ref_serie_origem' => $this->ref_serie_origem,
                'ref_serie_destino' => $this->ref_serie_destino,
                'ref_usuario_cad' => $this->pessoa_logada,
                'ativo' => 1,
            ]);

            if ($det_sequencia->save()) {
                $this->mensagem .= 'Edição efetuada com sucesso.<br>';

                throw new HttpResponseException(
                    new RedirectResponse('educar_sequencia_serie_lst.php')
                );
            }
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(587, $this->pessoa_logada, 3, 'educar_sequencia_serie_lst.php');

        $obj = LegacySequenceGrade::query()
            ->whereGradeOrigin($this->ref_serie_origem)
            ->whereGradeDestiny($this->ref_serie_destino)
            ->active()
            ->first();

        if (!$obj) {
            $objEdicao = LegacySequenceGrade::query()
                ->active()
                ->find(request('id'));

            $objEdicao->fill([
                'ref_serie_origem' => $this->ref_serie_origem,
                'ref_serie_destino' => $this->ref_serie_destino,
                'ref_usuario_exc' => $this->pessoa_logada,
            ]);

            if ($objEdicao->save()) {
                $this->mensagem .= 'Edição efetuada com sucesso.<br>';

                throw new HttpResponseException(
                    new RedirectResponse('educar_sequencia_serie_lst.php')
                );
            }
            $this->mensagem = 'Edição não realizada.<br>';

            return false;
        }
        echo '<script> alert(\'Edição não realizada! \\n Já existe essa sequência.\') </script>';
        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(587, $this->pessoa_logada, 3, 'educar_sequencia_serie_lst.php');
        $obj = LegacySequenceGrade::query()
            ->find(request('id'));

        if ($obj->delete()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';

            throw new HttpResponseException(
                new RedirectResponse('educar_sequencia_serie_lst.php')
            );
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-sequencia-serie.js');
    }

    public function Formular()
    {
        $this->title = 'Sequência Enturmação';
        $this->processoAp = '587';
    }
};
