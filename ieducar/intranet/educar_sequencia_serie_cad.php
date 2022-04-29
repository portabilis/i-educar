<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsCadastro {
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

        $this->ref_serie_origem=$_GET['ref_serie_origem'];
        $this->ref_serie_destino=$_GET['ref_serie_destino'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(587, $this->pessoa_logada, 3, 'educar_sequencia_serie_lst.php');

        if (is_numeric($this->ref_serie_origem) && is_numeric($this->ref_serie_destino)) {
            $obj = new clsPmieducarSequenciaSerie($this->ref_serie_origem, $this->ref_serie_destino);
            $registro  = $obj->detalhe();
            if ($registro) {
                $obj_ref_serie_origem = new clsPmieducarSerie($this->ref_serie_origem);
                $det_ref_serie_origem = $obj_ref_serie_origem->detalhe();
                $this->ref_curso_origem = $det_ref_serie_origem['ref_cod_curso'];
                $obj_ref_curso_origem = new clsPmieducarCurso($this->ref_curso_origem);
                $det_ref_curso_origem = $obj_ref_curso_origem->detalhe();
                $this->ref_cod_instituicao = $det_ref_curso_origem['ref_cod_instituicao'];
                $obj_ref_serie_destino = new clsPmieducarSerie($this->ref_serie_destino);
                $det_ref_serie_destino = $obj_ref_serie_destino->detalhe();
                $this->ref_curso_destino = $det_ref_serie_destino['ref_cod_curso'];

                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(587, $this->pessoa_logada, 3)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_sequencia_serie_det.php?ref_serie_origem={$registro['ref_serie_origem']}&ref_serie_destino={$registro['ref_serie_destino']}" : 'educar_sequencia_serie_lst.php';

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
            $opcoes = [ '' => 'Selecione' ];
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

        $opcoes = [ '' => 'Selecione' ];
        $opcoes_ = [ '' => 'Selecione' ];

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

        $opcoes = [ '' => 'Selecione' ];
        $opcoes_ = [ '' => 'Selecione' ];

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

        $obj_sequencia = new clsPmieducarSequenciaSerie($this->ref_serie_origem, $this->ref_serie_destino);
        $det_sequencia = $obj_sequencia->detalhe();
        if (!$det_sequencia) {
            $obj = new clsPmieducarSequenciaSerie($this->ref_serie_origem, $this->ref_serie_destino, null, $this->pessoa_logada, null, null, 1);
            $cadastrou = $obj->cadastra();
            if ($cadastrou) {
                $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';

                throw new HttpResponseException(
                    new RedirectResponse('educar_sequencia_serie_lst.php')
                );
            }
        } else {
            $obj = new clsPmieducarSequenciaSerie($this->ref_serie_origem, $this->ref_serie_destino, $this->pessoa_logada, null, null, null, 1);
            $editou = $obj->edita();
            if ($editou) {
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

        //echo "clsPmieducarSequenciaSerie($this->ref_serie_origem, $this->ref_serie_destino, $this->pessoa_logada, null, null, null, 1);";
        $obj = new clsPmieducarSequenciaSerie($this->ref_serie_origem, $this->ref_serie_destino, $this->pessoa_logada, null, null, null, 1);
        $existe = $obj->existe();
        if (!$existe) {
            $editou = $obj->editar($this->serie_origem_old, $this->serie_destino_old);
            if ($editou) {
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

        $obj = new clsPmieducarSequenciaSerie($this->ref_serie_origem, $this->ref_serie_destino, $this->pessoa_logada, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
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
