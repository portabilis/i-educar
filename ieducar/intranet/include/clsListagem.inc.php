<?php

use iEducar\Support\Navigation\Breadcrumb;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

require_once 'include/clsCampos.inc.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/View/Helper/Inputs.php';
require_once 'include/localizacaoSistema.php';

define('alTopLeft', 'valign=top align=left');
define('alTopCenter', 'valign=top align=center');
define('alTopRight', 'valign=top align=right');
define('alMiddleLeft', 'valign=middle align=left');
define('alMiddleCenter', 'valign=middle align=center');
define('alMiddleRight', 'valign=middle align=right');
define('alBottomLeft', 'valign=bottom align=left');
define('alBottomCenter', 'valign=bottom align=center');
define('alBottomRight', 'valign=bottom align=right');

class clsListagem extends clsCampos
{
    public $nome = 'formulario';
    public $__titulo;
    public $titulo;
    public $largura;
    public $linhas;
    public $colunas;
    public $cabecalho;
    public $paginacao;
    public $tabulacao;
    public $method = 'GET';
    public $camposResultado;
    public $tituloFormResultado;
    public $funcAcao = '';
    public $funcAcaoNome = '';
    public $rotulo_anterior;
    public $appendInTop = false;
    public $array_botao;
    public $array_botao_url;
    public $array_botao_script;
    public $show_botao_novo = true;
    public $acao_imprimir = false;
    public $valor_imprimir = 'Imprimir Arquivo';
    public $paginador = [];
    public $numeropaginador = 0;
    public $paginador2;
    public $busca_janela = 0;
    public $rodape = '';
    public $ordenacao;
    public $campos_ordenacao;
    public $fonte;
    public $exibirBotaoSubmit = true;

    public function __construct()
    {
        parent::__construct();

        $this->SalvaFiltros();
    }

    public function SalvaFiltros()
    {
        $uri = parse_url(Request::server('REQUEST_URI'), PHP_URL_PATH);
        $previousFilters = Session::get('previous_filters') ?? [];

        if (empty($_GET)) {
            if (!empty($previousFilters[$uri])) {
                list($path, $ts) = explode('|', $previousFilters[$uri]);
                $diff = now() - (int) $ts;

                if ($diff > 7200) { //duas horas
                    return;
                }

                $path = $uri . '?' . $path;

                return $this->simpleRedirect($path);
            }
        } else {
            $params = http_build_query($_GET) . '|' . now();

            if (count($previousFilters) > 3) {
                array_shift($previousFilters);
            }

            $previousFilters[$uri] = $params;

            Session::put('previous_filters', $previousFilters);
        }
    }

    public function Gerar()
    {
        return false;
    }

    /**
     * @deprecated
     */
    public function addBanner(
        $strBannerUrl = '',
        $strBannerLateralUrl = '',
        $strBannerTitulo = '',
        $boolFechaBanner = true
    ) {
        // Apenas para fins de compatibilidade
    }

    public function enviaLocalizacao($localizao, $appendInTop = false)
    {
        if ($localizao) {
            $this->locale = $localizao;
        }

        $this->appendInTop = $appendInTop;
    }

    public function addCabecalhos($coluna)
    {
        $this->cabecalho = $coluna;
    }

    public function addCabecalho($coluna)
    {
        $this->cabecalho[] = $coluna;
    }

    public function addLinhas($linha)
    {
        $this->linhas[] = $linha;
    }

    public function addPaginador2(
        $strUrl,
        $intTotalRegistros,
        $mixVariaveisMantidas = '',
        $nome = 'formulario',
        $intResultadosPorPagina = 20,
        $intPaginasExibidas = 3,
        $var_alteranativa = false,
        $pag_modifier = 0,
        $add_iniciolimit = false
    ) {
        if ($intTotalRegistros > 0) {
            $getVar = "pagina_{$nome}";

            if ($var_alteranativa) {
                $getVar = $var_alteranativa;
            }

            if (isset($_GET[$getVar])) {
                $intPaginaAtual = $_GET[$getVar];
            } else {
                $intPaginaAtual = 1;
            }

            $pagStart = $intPaginaAtual - $intPaginasExibidas;
            $totalPaginas = ceil($intTotalRegistros / $intResultadosPorPagina);

            if ($pagStart > $totalPaginas - $intPaginasExibidas * 2) {
                $pagStart = $totalPaginas - $intPaginasExibidas * 2;
            }

            if ($pagStart < 1) {
                $pagStart = 1;
            }

            $linkFixo = $strUrl . '?';

            $add_iniciolimi = null;

            if (is_array($mixVariaveisMantidas)) {
                foreach ($mixVariaveisMantidas as $key => $value) {
                    if ($key != $getVar) {
                        if (! ($add_iniciolimi && $key == 'iniciolimit')) {
                            $linkFixo .= "$key=$value&";
                        }
                    }
                }
            } else {
                if (is_string($mixVariaveisMantidas)) {
                    $linkFixo .= "$mixVariaveisMantidas&";
                }
            }

            $strReturn = <<<HTML
<table>
  <tr>
    <td>Total de registros: {$intTotalRegistros}</td>
  </tr>
</table>
HTML;

            $strReturn .= '<table class=\'paginacao\' border="0" cellpadding="0" cellspacing="0" align="center"><tr>';

            // Setas de início e anterior
            $imagem = ($intPaginaAtual > 1) ? 'seta' :'seta_transp';
            $compl_url = ($add_iniciolimit) ? '&iniciolimit=' . (1 + $pag_modifier): '';
            $strReturn .= "<td width=\"23\" align=\"center\"><a href=\"{$linkFixo}$getVar=" . (1 + $pag_modifier) . "{$compl_url}\" class=\"nvp_paginador\" title=\"Ir para a primeira pagina\"> &laquo; </a></td> ";
            $compl_url = ($add_iniciolimit) ? '&iniciolimit=' . max(1 + $pag_modifier, $intPaginaAtual - 1) : '';
            $strReturn .= "<td width=\"23\" align=\"center\"><a href=\"{$linkFixo}$getVar=" . max(1 + $pag_modifier, $intPaginaAtual - 1) . "{$compl_url}\" class=\"nvp_paginador\" title=\"Ir para a pagina anterior\"> &lsaquo; </a></td> ";

            // Meio
            $strReturn .= '';
            $meios = [];

            $ordenacao = $_POST['ordenacao'] ?? $_GET['ordenacao'] ?? $_POST['ordenacao'] ?? null;

            for ($i = 0; $i <= $intPaginasExibidas * 2 && $i + $pagStart <= $totalPaginas; $i++) {
                $imagem     = ($pagStart + $i + $pag_modifier == $intPaginaAtual) ? '2' : '1';
                $compl_url  = ($add_iniciolimit) ? '&iniciolimit=' . ($pagStart + $i + $pag_modifier) : '';
                $strReturn .= "<td align=\"center\" style=\"padding-left:5px;padding-right:5px;\"><a href=\"{$linkFixo}$getVar=" . ($pagStart + $i + $pag_modifier) . "{$compl_url}&ordenacao={$ordenacao}\" class=\"nvp_paginador\" title=\"Ir para a p&aacute;gina " . ($pagStart + $i) . '">' . addLeadingZero($pagStart + $i) .'</a></td>';
            }

            // Setas de fim e próxima
            $imagem     = ($intPaginaAtual < $totalPaginas) ? 'seta' : 'seta_transp';
            $compl_url  = ($add_iniciolimit) ? '&iniciolimit=' . min($totalPaginas + $pag_modifier, $intPaginaAtual + 1) : '';
            $strReturn .= "<td width=\"23\" align=\"center\"><a href=\"{$linkFixo}$getVar=" . min($totalPaginas + $pag_modifier, $intPaginaAtual + 1) . "{$compl_url}\" class=\"nvp_paginador\" title=\"Ir para a proxima pagina\"> &rsaquo; </a></td> ";
            $compl_url  = ($add_iniciolimit) ? '&iniciolimit=' . ($totalPaginas + $pag_modifier): '';
            $strReturn .= "<td width=\"23\" align=\"center\"><a href=\"{$linkFixo}$getVar=" . ($totalPaginas + $pag_modifier) . "{$compl_url}\" class=\"nvp_paginador\" title=\"Ir para a ultima pagina\"> &raquo; </a></td> ";

            $strReturn .= '</tr></table>';

            $this->paginador2 = $strReturn;
        }
    }

    /**
     * Cria o código HTML.
     *
     * @param string $caminho
     * @param int    $qdt_registros
     * @param int    $limite
     * @param string $link_atual
     *
     * @return NULL
     */
    public function paginador($caminho, $qdt_registros, $limite, $link_atual)
    {
        $this->addPaginador2(
            '',
            $qdt_registros,
            $_GET,
            'formulario',
            $limite,
            3,
            'pos_atual',
            -1,
            true
        );

        return null;
    }

    public function RenderHTML()
    {
        View::share('title', $this->titulo);

        ob_start();

        $this->_preRender();
        $this->Gerar();

        $retorno = ob_get_contents();

        ob_end_clean();

        $retorno .= '
            <script type="text/javascript">function go(url) { document.location = url; }
            var goodIE = (document.all) ? 1:0;
            var netscape6 = (document.getElementById && !document.all) ? 1:0;
            var aux = \'\';
            var aberto = false;';

        $retorno .= $this->MakeFormat();
        $retorno .= '</script>';

        if ($this->locale && $this->appendInTop) {
            app(Breadcrumb::class)->setLegacy($this->locale);
        }

        if ($this->campos) {
            $width = empty($this->largura) ? '' : "width='$this->largura'";

            $barra = '<b>Filtros de busca</b>';

            $retorno .=  "
                <!-- begin formulario -->
                    <form name='{$this->__nome}' id='{$this->__nome}' method='{$this->method}' action=\"\">
                        <input name='busca' type='hidden' value='S'>";

            if ($this->campos) {
                reset($this->campos);

                foreach ($this->campos as $nome => $componente) {
                    if ($componente[0] == 'oculto' || $componente[0] == 'rotulo') {
                        $retorno .=  "<input name='$nome' id='$nome' type='hidden' value='".urlencode($componente[3]).'\'>';
                    }
                }
            }

            if ($this->locale && !$this->appendInTop) {
                app(Breadcrumb::class)->setLegacy($this->locale);
            }

            $retorno .= "<table class='tablelistagem' $width border='0' cellpadding='2' cellspacing='1'>";

            $retorno .= "
                <tr>
                    <td class='formdktd' colspan='2' height='24'>{$barra}</td>
                </tr>";

            if (empty($this->campos)) {
                $retorno .=  '
                    <tr>
                        <td class=\'formlttd\' colspan=\'2\'><span class=\'form\'>N&atilde;o existem campos definidos para o formul&aacute;rio</span></td>
                    </tr>';
            } else {
                $retorno .= $this->MakeCampos();
            }

            $retorno .= '
                <tr>
                    <td class=\'formdktd\' colspan=\'2\'></td>
                </tr>';
            $retorno .= '
                <tr>
                    <td colspan=\'2\' align=\'center\'>
                        <script type="text/javascript" language=\'javascript\'>';

            if ($this->funcAcao) {
                $retorno .=  $this->funcAcao;
            } else {
                $retorno .=  "function acao{$this->funcAcaoNome}() { document.{$this->__nome}.submit(); } ";
            }

            $retorno .=  '</script>';

            if ($this->exibirBotaoSubmit) {
                if (isset($this->botao_submit) && $this->botao_submit) {
                    $retorno .=  '&nbsp;<input type=\'submit\' class=\'botaolistagem\' value=\'Buscar\' id=\'botao_busca\'>&nbsp;';
                } else {
                    $retorno .=  "&nbsp;<input type='button' class='botaolistagem btn-green' onclick='javascript:acao{$this->funcAcaoNome}();' value='Buscar' id='botao_busca'>&nbsp;";
                }
            }

            $retorno .=  '
                                </td>
                            </tr>
                        </table>
                    <!-- cadastro end -->
                </form>';
        }

        $ncols = count($this->cabecalho);
        $width = empty($this->largura) ? '' : "width='$this->largura'";

        if (empty($this->__titulo)) {
            $this->__titulo = $this->titulo ?? $this->_titulo;
        }

        $this->method = 'POST';

        if ($this->locale && !$this->campos && !$this->appendInTop) {
            app(Breadcrumb::class)->setLegacy($this->locale);
        }

        $retorno .=  "
            <form name=\"form_resultado\" id=\"form_resultado\" method=\"POST\" action=\"\">
                <!-- listagem begin -->
                <table class='tablelistagem' $width border='0' cellpadding='4' cellspacing='1'>
                    <tr>
                        <td class='titulo-tabela-listagem' colspan='$ncols'>{$this->__titulo}</td>
                    </tr>";

        $ncols = count($this->cabecalho);

        // Cabeçalho
        if (!empty($this->cabecalho)) {
            reset($this->cabecalho);

            $ncols = count($this->cabecalho);

            if (!empty($this->colunas)) {
                reset($this->colunas);
            }

            $ordenacao = $_POST['ordenacao'] ?? '';
            $fonte = $_POST['fonte'] ?? '';
            $retorno .= "<input type='hidden' id='ordenacao' name='ordenacao' value='{$ordenacao}'>";
            $retorno .= "<input type='hidden' id='fonte' name='fonte' value='{$fonte}'>";
            $retorno .= '<tr>';

            foreach ($this->cabecalho as $i => $texto) {
                if (!empty($this->colunas)) {
                    list($i, $fmt) = each($this->colunas);
                } else {
                    $fmt = alTopLeft;
                }

                if ($texto) {
                    $inicio = $fim = '';

                    if ($this->campos_ordenacao[$i] != '') {
                        $_POST['fonte']  = empty($_POST['fonte']) ? 'imagens/nvp_setinha_down.gif' : $_POST['fonte'];
                        $inicio = "<img name='seta' src='{$_POST['fonte']}' border='0' /> <a href='#' onclick='definirOrdenacao(\"{$this->campos_ordenacao[$i]}\");document.getElementById(\"form_resultado\").submit();'>";
                        $fim = '</a>';
                    }

                    $retorno .=  "<td class='formdktd' $fmt style=\"font-weight:bold;\" valign='middle'>{$inicio}$texto{$fim}</td>";
                }
            }

            $retorno .=  '</tr>';
        }

        // Lista
        if (empty($this->linhas)) {
            $retorno .=  "<tr><td class='formlttd' colspan='$ncols' align='center'>N&atilde;o h&aacute; informa&ccedil;&atilde;o para ser apresentada</td></tr>";
        } else {
            reset($this->linhas);

            foreach ($this->linhas as $i => $linha) {
                $classe = ($i % 2) ? 'formmdtd' : 'formlttd';
                $retornoTmp = '';

                if (is_array($linha)) {
                    if (
                        !empty($linha['tipo'])
                        && !empty($linha['conteudo'])
                        && $linha['tipo'] === 'html-puro'
                    ) {
                        $retorno .= $linha['conteudo'];
                        continue;
                    }

                    reset($linha);

                    if (!empty($this->colunas)) {
                        reset($this->colunas);
                    }

                    foreach ($linha as $i => $celula) {
                        if (!empty($this->colunas)) {
                            $fmt = current($this->colunas);
                        } else {
                            $fmt = alTopLeft;
                        }

                        if (strpos($celula, '<img src=\'imagens/noticia.jpg\' border=0>') !== false) {
                            $celula = str_replace('<img src=\'imagens/noticia.jpg\' border=0>', '<img src=\'imagens/noticia.jpg\' border=0 alt=\'\'>', $celula);
                        }

                        $retornoTmp .=  "<td class='$classe' $fmt>$celula</td>";
                    }
                } else {
                    $retornoTmp .=  "<td class='formdktd' $fmt colspan='$ncols'>$linha</td>";
                }

                $retorno .=  '<tr>' . $retornoTmp . '</tr>';
            }
        }

        $retorno .=  "
            <tr>
                <td class='formdktd' colspan=\"{$ncols}\">&nbsp;</td>
            </tr>";

        if (!empty($this->paginador2)) {
            $retorno .= "
                <tr>
                    <td align=\"center\" colspan=\"$ncols\">{$this->paginador2}</td>
                </tr>";
        }

        if (!empty($this->paginador)) {
            $ua = 0;
            $qdt_paginador = 1;
            $i = 0;
            $retorno .=  "
                <tr>
                    <td colspan='$ncols' align='center'>";

            foreach ($this->paginador as $pagina) {
                if ($pagina[2]) {
                    switch ($pagina[0]) {
                        case '<<':
                            $retorno .=  "<a href='{$pagina[1]}' class='nvp_paginador'>{$pagina[0]}</a>&nbsp;&nbsp;&nbsp;&nbsp;";
                            break;
                        case '-10':
                            $retorno .=  "<a href='{$pagina[1]}' class='nvp_paginador'>{$pagina[0]}</a>&nbsp;&nbsp;&nbsp;&nbsp;";
                            break;
                        case '>>':
                            $retorno .=  "&nbsp;&nbsp;&nbsp;&nbsp;<a href='{$pagina[1]}' class='nvp_paginador'>{$pagina[0]}</a>";
                            break;
                        case 'p10':
                            $retorno .=  "&nbsp;&nbsp;&nbsp;&nbsp;<a href='{$pagina[1]}' class='nvp_paginador'>+10</a>";
                            break;
                        default:
                            $retorno .=  "<a href='{$pagina[1]}' class='nvp_paginador'>{$pagina[0]}</a>&nbsp;";
                    }
                } else {
                    $retorno .=  "<span class='linkBory' style='text-decoration: underline; color: black;'> {$pagina[0]} </span>&nbsp;";
                }

                if ($ua++ > 15) {
                    $ua = 0;
                    $retorno .= '<br>';
                }
            }

            $retorno .=  '
                    </td>
                </tr>';
        }

        if ($this->tituloFormResultado) {
            $retorno .=  "
                <tr>
                    <td class='formdktd' colspan=\"$ncols\" height='24'><span class='form'><b>{$this->tituloFormResultado}</b></span></td>
                </tr>";
        }

        if (!empty($this->camposResultado)) {
            reset($this->camposResultado);
            $campo_anterior = '';
            $md = true;

            while (list($nome, $componente) = each($this->camposResultado)) {
                if ($componente[0] != 'oculto') {
                    $tipo = $componente[0];
                    $campo = $componente[1] . ':';

                    if ($campo == $campo_anterior) {
                        $campo = '';
                    } else {
                        $campo_anterior = $campo;
                        $md = !$md;
                    }

                    $classe = $md ? 'formmdtd' : 'formlttd';

                    $retorno .= "
                        <tr>
                            <td class='$classe' valign='top'><span class='form'>$campo</span></td>
                            <td class='$classe' valign='top'>
                            <span class='form'>";

                    switch ($tipo) {
                        case 'rotulo':
                            $retorno .=  $componente[2];
                            break;
                        case 'texto':
                            $retorno .=  "<input class='form' type='text' name='$nome' value='$componente[2]' size='$componente[3]' maxlength='$componente[4]'>\n";
                            break;
                        case 'memo':
                            $retorno .=  "<textarea class='form' name='$nome' cols='$componente[3]' rows='$componente[4]' wrap='virtual' >$componente[2]</textarea>\n";
                            break;
                        case 'lista':
                            $retorno .=  "<select class='form' name='$nome'>\n";

                            reset($componente[2]);

                            while (list($chave, $texto) = each($componente[2])) {
                                $retorno .=  '<option value=\'' . urlencode($chave) . '\'';

                                if ($chave == $componente[3]) {
                                    $retorno .=  ' selected';
                                }

                                $retorno .=  ">$texto</option>\n";
                            }

                            $retorno .=  "</select>\n";
                            break;
                    }

                    $retorno .=  '
                                </span>
                            </td>
                        </tr>';
                }
            }
        }

        $botao = '';

        if (isset($this->acao_voltar) && $this->acao_voltar) {
            $botao = "&nbsp;&nbsp;&nbsp;<input type='button' class='botaolistagem' onclick='javascript: $this->acao_voltar' value=' Voltar '>";
        }

        if (isset($this->acao_imprimir) && $this->acao_imprimir) {
            $botao = "&nbsp;&nbsp;&nbsp;<input type='button' id='imprimir' class='botaolistagem' onclick='javascript: $this->acao_imprimir' value='$this->valor_imprimir'>";
        }

        if (!empty($this->acao) && $this->show_botao_novo) {
            $retorno .=  "
                <tr>
                    <td colspan=\"$ncols\" align=\"center\"><input type='button' class='btn-green botaolistagem' onclick='javascript: $this->acao' value=' $this->nome_acao '>$botao</td>
                </tr>";
        } elseif ($this->acao_imprimir) {
            $retorno .=  "
                <tr>
                    <td colspan=\"$ncols\" align=\"center\">$botao</td>
                </tr>";
        }

        $retorno .= "
            <tr>
                <td colspan=\"$ncols\" align=\"center\">";

        if (is_array($this->array_botao_script) && count($this->array_botao_script)) {
            for ($i = 0; $i < count($this->array_botao); $i++) {
                $btnTemplate = '&nbsp;<input type=\'button\' class=\'botaolistagem\' onclick=\'%s\' value=\'%s\' id=\'%s\'>&nbsp;';
                $retorno .= sprintf($btnTemplate, $this->array_botao_script[$i], $this->array_botao[$i], $this->array_botao_id[$i] ?? '');
                //$retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='". $this->array_botao_script[$i]."' value='".$this->array_botao[$i]."'>&nbsp;\n";
            }
        } elseif (is_array($this->array_botao)) {
            for ($i = 0; $i < count($this->array_botao); $i++) {
                $retorno .= '&nbsp;<input type=\'button\' class=\'botaolistagem\' onclick=\'javascript:go( "'.$this->array_botao_url[$i].'" );\' value=\''.$this->array_botao[$i]."'>&nbsp;\n";
            }
        }

        $retorno .= '</td>
            </tr>';

        if (!is_null($this->rodape)) {
            $retorno .= "<tr><td colspan=\"$ncols\" align=\"center\" id=\"td_rodape\">\n";
            $retorno .= $this->rodape;
            $retorno .= '
                    </td>
                </tr>';
        }

        $retorno .= '
                    </table>
                </form>
            <!-- listagem end -->';

        Portabilis_View_Helper_Application::embedJavascriptToFixupFieldsWidth($this);

        return $retorno;
    }

    /**
     * Exibe mensagem de DIE formatada;
     *
     * @param String $msg
     * @param String $url Redirecionar após 1 segundo
     */
    public function erro($msg, $redir = 'index.php')
    {
        die("<div style='width: 300px; height: 100px; font: 700 11px Arial,Helv,Sans; background-color: #f6f6f6; color: #e11; position: absolute; left: 50%; top: 50%; margin-top: -20px; margin-left: -100px; text-align: center; border: solid 1px #a1a1f1;'>{$msg}</div><script>setTimeout('window.location=\'$redir\'',5000);</script>");
    }

    public function inputsHelper()
    {
        if (! isset($this->_inputsHelper)) {
            $this->_inputsHelper = new Portabilis_View_Helper_Inputs($this);
        }

        return $this->_inputsHelper;
    }
}
