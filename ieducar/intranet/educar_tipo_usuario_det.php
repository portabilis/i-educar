<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Tipo Usuário');
        $this->processoAp = '554';
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsDetalhe
{
    /**
     * Título no topo da página.
     *
     * @var int
     */
    public $titulo;

    public $cod_tipo_usuario;
    public $ref_funcionario_cad;
    public $ref_funcionario_exc;
    public $nm_tipo;
    public $descricao;
    public $nivel;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Tipo Usuário - Detalhe';

        $this->cod_tipo_usuario = $_GET['cod_tipo_usuario'];

        $tmp_obj = new clsPmieducarTipoUsuario(
            $this->cod_tipo_usuario,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );

        if (!$registro = $tmp_obj->detalhe()) {
            $this->simpleRedirect('educar_tipo_usuario_lst.php');
        }

        if ($registro['cod_tipo_usuario']) {
            $this->addDetalhe(['C&oacute;digo Tipo Usu&aacute;rio', $registro['cod_tipo_usuario']]);
        }

        if ($registro['nm_tipo']) {
            $this->addDetalhe(['Tipo de Usu&aacute;rio', $registro['nm_tipo']]);
        }

        $array_nivel = [
            '8' => 'Biblioteca',
            '4' => 'Escola',
            '2' => 'Institucional',
            '1' => 'Poli-institucional'
        ];

        if ($array_nivel[$registro['nivel']]) {
            $this->addDetalhe(['N&iacute;vel', $array_nivel[$registro['nivel']]]);
        }

        if ($registro['descricao']) {
            $this->addDetalhe(['Descri&ccedil;&atilde;o', $registro['descricao']]);
        }

        // Listagem de permissães
        $objTemp = new clsBanco();
        $objTemp->Consulta(sprintf(
            '
                SELECT
                    m.cod_menu_menu,
                    m.nm_menu,
                    sub.cod_menu_submenu,
                    sub.nm_submenu,
                    u.visualiza,
                    u.cadastra,
                    u.exclui
                FROM
                    menu_submenu sub,
                    menu_menu m,
                    pmieducar.menu_tipo_usuario u
                WHERE
                    sub.cod_menu_submenu = u.ref_cod_menu_submenu
                    AND sub.ref_cod_menu_menu = m.cod_menu_menu
                    AND ((m.cod_menu_menu = 55 OR m.ref_cod_menu_pai = 55) OR (m.cod_menu_menu = 57 OR m.ref_cod_menu_pai = 57))
                    AND u.ref_cod_tipo_usuario = %d
                ORDER BY cod_menu_menu, upper(sub.nm_submenu)
            ', $this->cod_tipo_usuario)
        );

        while ($objTemp->ProximoRegistro()) {
            list($menu_pai, $nm_menu_pai, $codigo, $nome, $visualiza, $cadastra, $exclui) = $objTemp->Tupla();
            $opcoes[$menu_pai]['nome_menu_pai'] = $nm_menu_pai;
            $opcoes[$menu_pai][$codigo]['nm_submenu'] = $nome;
            $opcoes[$menu_pai][$codigo]['cadastra'] = $cadastra;
            $opcoes[$menu_pai][$codigo]['visualiza'] = $visualiza;
            $opcoes[$menu_pai][$codigo]['exclui'] = $exclui;
        }

        if ($opcoes) {
            $det_menus = $this->lista_menus($opcoes);
            if ($det_menus) {
                $this->addDetalhe([
                    'Permiss&otilde;es de acesso aos menus</b>',
                    '<a href=\'javascript:void(0);\' onclick=\'trocaDisplay("det_pree")\'>Mostrar detalhe</a><div id=\'det_pree\' name=\'det_pree\' style=\'display:inline;\'>' . $det_menus . '</div>']);
            }
        }

        // Verificação de permissão para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(554, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'educar_tipo_usuario_cad.php';
            $this->url_editar = 'educar_tipo_usuario_cad.php?cod_tipo_usuario=' . $registro['cod_tipo_usuario'];
        }

        $this->url_cancelar = 'educar_tipo_usuario_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do tipo de usuário', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
    }

    public function lista_menus($opcoes)
    {
        $existe = true;
        $tabela .= '<table cellpadding="2" cellspacing="2" border="0" align="left" width=\'80%\'>';
        $tabela .= '<tr bgcolor=\'#ccdce6\'><th width=\'400\'>Menu - submenus</th><th>Visualizar</th><th>Cadastrar</th><th width=\'70\'>Excluir</th></tr>';

        foreach ($opcoes as $key => $menu) {
            $menu_pai = array_shift($menu);
            $cor = '#ccdce6';
            $tabela .= "<tr bgcolor='$cor' align='center'><td colspan='4' align='left' width='400'><b>{$menu_pai}</b></td></tr>";
            $cor = '#f5f9fd';

            foreach ($menu as $cod_sub => $sub_menu) {
                $cor = $cor == '#FFFFFF' ? '#f5f9fd' : '#FFFFFF';
                $sub_menu['visualiza'] = $sub_menu['visualiza'] == 0 ? 'N&atilde;o' : 'Sim';
                $sub_menu['cadastra'] = $sub_menu['cadastra'] == 0 ? 'N&atilde;o' : 'Sim';
                $sub_menu['exclui'] = $sub_menu['exclui'] == 0 ? 'N&atilde;o' : 'Sim';

                $tabela .= "<tr bgcolor='$cor' align='center'><td style='padding-left:20px' align='left' width='400'><img src=\"imagens/noticia.jpg\" border='0'>{$sub_menu['nm_submenu']}</td><td>{$sub_menu['visualiza']}</td><td>{$sub_menu['cadastra']}</td><td>{$sub_menu['exclui']}</td></tr>";
            }
        }
        $tabela .= '</tr>';
        $tabela .= '</table>';

        return $existe == true ? $tabela : false;
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();
$pagina->addForm($miolo);
$pagina->MakeAll();

?>

<script type="text/javascript">
    function trocaDisplay(id) {
        var element = document.getElementById(id);
        element.style.display = (element.style.display == 'none') ? 'inline' : 'none';
    }
</script>
