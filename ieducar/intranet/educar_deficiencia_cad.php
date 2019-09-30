<?php
require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Support\View\SelectOptions;

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Deficiência");
        $this->processoAp = '631';
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_deficiencia;
    public $nm_deficiencia;
    public $deficiencia_educacenso;

    public function Inicializar()
    {
        $retorno = 'Novo';


        $this->cod_deficiencia=$_GET['cod_deficiencia'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(631, $this->pessoa_logada, 7, 'educar_deficiencia_lst.php');

        if (is_numeric($this->cod_deficiencia)) {
            $obj = new clsCadastroDeficiencia($this->cod_deficiencia);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(631, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_deficiencia_det.php?cod_deficiencia={$registro['cod_deficiencia']}" : 'educar_deficiencia_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' deficiência', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_deficiencia', $this->cod_deficiencia);

        // foreign keys

        // text
        $this->campoTexto('nm_deficiencia', 'Deficiência', $this->nm_deficiencia, 30, 255, true);

        $options = [
            'label' => 'Deficiência educacenso',
            'resources' => SelectOptions::educacensoDeficiencies(),
            'value' => $this->deficiencia_educacenso
        ];

        $this->inputsHelper()->select('deficiencia_educacenso', $options);
        $this->campoCheck('desconsidera_regra_diferenciada', 'Desconsiderar deficiência na regra de avaliação diferenciada', dbBool($this->desconsidera_regra_diferenciada));
    }

    public function Novo()
    {


        $obj = new clsCadastroDeficiencia($this->cod_deficiencia);
        $obj->nm_deficiencia = $this->nm_deficiencia;
        $obj->deficiencia_educacenso = $this->deficiencia_educacenso;
        $obj->desconsidera_regra_diferenciada = !is_null($this->desconsidera_regra_diferenciada);

        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $deficiencia = new clsCadastroDeficiencia($cadastrou);
            $deficiencia = $deficiencia->detalhe();

            $auditoria = new clsModulesAuditoriaGeral('deficiencia', $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($deficiencia);

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_deficiencia_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';


        return false;
    }

    public function Editar()
    {


        $deficienciaDetalhe = new clsCadastroDeficiencia($this->cod_deficiencia);
        $deficienciaDetalheAntes = $deficienciaDetalhe->detalhe();

        $obj = new clsCadastroDeficiencia($this->cod_deficiencia);
        $obj->nm_deficiencia = $this->nm_deficiencia;
        $obj->deficiencia_educacenso = $this->deficiencia_educacenso;
        $obj->desconsidera_regra_diferenciada = !is_null($this->desconsidera_regra_diferenciada);

        $editou = $obj->edita();
        if ($editou) {
            $deficienciaDetalheDepois = $deficienciaDetalhe->detalhe();

            $auditoria = new clsModulesAuditoriaGeral('deficiencia', $this->pessoa_logada, $this->cod_deficiencia);
            $auditoria->alteracao($deficienciaDetalheAntes, $deficienciaDetalheDepois);

            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_deficiencia_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';


        return false;
    }

    public function Excluir()
    {


        $obj = new clsCadastroDeficiencia($this->cod_deficiencia, $this->nm_deficiencia);
        $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $auditoria = new clsModulesAuditoriaGeral('deficiencia', $this->pessoa_logada, $this->cod_deficiencia);
            $auditoria->exclusao($detalhe);

            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_deficiencia_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';


        return false;
    }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm($miolo);
// gera o html
$pagina->MakeAll();
?>
<script type="text/javascript">
    // Reescrita da função para exibir mensagem interativa
    function excluir()
    {
      document.formcadastro.reset();

      if (confirm('Deseja mesmo excluir essa deficiência? \nVinculos com os alunos serão deletados.')) {
        document.formcadastro.tipoacao.value = 'Excluir';
        document.formcadastro.submit();
      }
    }

</script>
