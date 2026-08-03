<?php

use App\Services\AgendaService;
use App\Support\View\AgendaCalendar;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

return new class extends clsCadastro
{
    public $agenda;

    public $editor;

    public $compromissos;

    public $time_atual;

    public $data_atual;

    public $data_atual_db;

    public $time_real_atual;

    public $publica = 0;

    public $dono;

    public $time_amanha;

    public $time_ontem;

    public $erro_msg;

    public $scriptNome;

    public function RenderHTML()
    {
        $this->scriptNome = 'agenda.php';
        $db = new clsBanco;
        $db2 = new clsBanco;
        // inicializacao de variaveis
        $this->editor = Auth::id();

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: '/intranet/scripts/agenda.js');
        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: '/intranet/styles/agenda.css');

        $agendaService = app(AgendaService::class);

        if (request('cod_agenda')) {
            $this->agenda = request()->integer('cod_agenda');
            $agenda = $agendaService->find(agenda: $this->agenda);
        } else {
            $agenda = $agendaService->firstOrCreate(person: $this->editor);
            $this->agenda = $agenda->getKey();
        }

        // Checa se a pessoa possui permissao (daqui por diante comeca a visualizar, editar, excluir, etc.)
        if (!$agendaService->hasPermission(editor: $this->editor, agenda: $agenda)) {
            throw new HttpResponseException(
                response: new RedirectResponse(url: $this->scriptNome)
            );
        }

        $this->time_atual = request('time') ?? time();

        $this->time_amanha = $this->time_atual + 86400;
        $this->time_ontem = $this->time_atual - 86400;

        $this->time_real_atual = time();

        $this->data_atual = date(format: 'd/m/Y', timestamp: $this->time_atual);
        $this->data_atual_db = date(format: 'Y-m-d', timestamp: $this->time_atual);

        /*
            DELETAR
        */
        if (request('deletar') !== null) {
            $agendaService->deleteCommitment(agenda: $agenda, commitment: request()->integer('deletar'));
        }

        /*
            EDITAR
        */
        if (request('agenda_rap_id') !== null) {
            $this->erro_msg .= $agendaService->updateCommitment(
                agenda: $agenda,
                editor: $this->editor,
                commitment: request()->integer('agenda_rap_id'),
                title: request('agenda_rap_titulo'),
                description: request('agenda_rap_conteudo'),
                date: request('agenda_rap_data'),
                startTime: request('agenda_rap_hora'),
                endTime: request('agenda_rap_horafim'),
                public: request()->boolean('agenda_rap_publico'),
                important: request()->boolean('agenda_rap_importante'),
            );
        }

        /*
            INSERIR
        */
        if (request('novo_hora_inicio') !== null) {
            $this->erro_msg .= $agendaService->createCommitment(
                agenda: $agenda,
                editor: $this->editor,
                commitment: null,
                title: request('novo_titulo'),
                description: request('novo_descricao'),
                date: request('novo_data'),
                startTime: request('novo_hora_inicio'),
                endTime: request('novo_hora_fim'),
                public: request()->boolean('novo_publico'),
                important: request()->boolean('novo_importante'),
                repeatDays: request('novo_repetir_dias'),
                repeatCount: request('novo_repetir_qtd'),
            );
        }

        /*
            GRAVA NOTA PARA COMPROMISSO
        */
        if (is_numeric(value: request('grava_compromisso'))) {
            $this->erro_msg .= $agendaService->saveNoteAsCommitment(
                agenda: $agenda,
                editor: $this->editor,
                commitment: request()->integer('grava_compromisso'),
                endTime: request('grava_hora_fim'),
            );
        }

        /*
            RESTAURAR UMA VERSAO
        */
        if (request('restaura') !== null && request('versao') !== null) {
            $this->erro_msg .= $agendaService->restoreVersion(
                agenda: $agenda,
                commitment: request()->integer('restaura'),
                version: request()->integer('versao'),
            );
        }

        /*
            INICIO DA PAGINA
        */
        $conteudo = '';
        $this->breadcrumb(currentPage: 'Agenda');

        if ($this->locale) {
            $conteudo .= '
        <table class=\'tablelistagem\' width=\'100%\' border=\'0\'  cellpadding=\'0\' cellspacing=\'0\'>';

            $conteudo .= "<tr height='10px'>
                      <td class='fundoLocalizacao' colspan='5'>{$this->locale}</td>
                    </tr>";

            $conteudo .= '</table>';
        }

        $conteudo .= '
        <div id="DOM_expansivel" class="DOM_expansivel"></div>
        <table border="0" cellpadding="0" cellspacing="3" width="100%">';

        $mesesArr = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        $diasArr = ['Domingo', 'Segunda Feira', 'Terça Feira', 'Quarta Feira', 'Quinta Feira', 'Sexta Feira', 'Sábado'];

        $this->arr_data_atual = [date(format: 'd', timestamp: $this->time_atual), date(format: 'n', timestamp: $this->time_atual), date(format: 'Y', timestamp: $this->time_atual), date(format: 'w', timestamp: $this->time_atual)];

        $nm_agenda = $agenda->nm_agenda;
        $this->publica = $agenda->publica;
        $this->dono = $agenda->ref_ref_cod_pessoa_own;

        if ($this->editor == $this->dono) {
            $preferencias = '<a class="small" href="agenda_preferencias.php">
                                <div><i class="fa fa-gear" aria-hidden="true"></i> Preferências</div>
                             </a> &nbsp;<br>';
        }

        $conteudo .= "
        <tr>
            <td width=\"80%\" height=\"80\" class=\"escuro\">
                <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
                    <tr>
                        <td width=\"80%\" style=\"padding-left:5px;\">
                            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" height=\"80\">
                                <tr>
                                    <td class=\"arrow-dia\" rowspan=\"2\" valign=\"middle\">
                                        <a href=\"{$this->scriptNome}?cod_agenda={$this->agenda}&time={$this->time_ontem}\">
                                            &laquo;
                                        </a>
                                    </td>
                                    <td class=\"data-info\" title=\"Dia: {$this->arr_data_atual[0]} de {$mesesArr[$this->arr_data_atual[1]]} de {$this->arr_data_atual[2]}\" align=\"center\">
                                        <span class=\"data1\">{$this->arr_data_atual[0]}<br></span>
                                        <span class=\"data2\">" . mb_strtoupper(string: substr(string: $mesesArr[$this->arr_data_atual[1]], offset: 0, length: 3)) . "<br>
                                        <span class=\"data3\">{$this->arr_data_atual[2]}</span></td>
                                    <td class=\"arrow-dia\" rowspan=\"2\" valign=\"middle\">
                                        <a href=\"{$this->scriptNome}?cod_agenda={$this->agenda}&time={$this->time_amanha}\">
                                            &raquo;
                                        </a>
                                    </td>
                                    <td rowspan=\"2\" valign=\"middle\" style=\"padding:5px;\"><span class=\"titulo\">{$diasArr[$this->arr_data_atual[3]]}</span></td>
                                </tr>
                            </table>
                        </td>
                        <td width=\"20%\" valign=\"bottom\" align=\"right\">{$preferencias} &nbsp; </td>
                    </tr>
                </table>
            </td>
            <td width=\"20%\" height=\"80\" valign=\"bottom\" align=\"center\" class=\"escuro\">
                <table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" height=\"80\">
                    <tr>
                        <td valign=\"top\" height=\"40\" align=\"center\">{$nm_agenda}</td>
                    </tr>
                    <tr>
                        <td valign=\"bottom\" height=\"40\"><span class=\"titulo\">Calendário</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width=\"80%\" valign=\"top\" rowspan=\"5\" class=\"escuro\" style=\"padding:3px;\">
                <form id=\"agenda_principal\" method=\"POST\" action=\"\">
                    <input type=\"hidden\" name=\"parametros\" id=\"parametros\" value=\"?cod_agenda={$this->agenda}&time={$this->time_atual}\">
                    <input type=\"hidden\" name=\"cod_agenda\" id=\"agenda\" value=\"{$this->agenda}\">
                    <input type=\"hidden\" name=\"time\" id=\"time\" value=\"{$this->time_atual}\">
                    <input type=\"hidden\" name=\"data_atual\" id=\"data_atual\" value=\"{$this->data_atual}\">
                    <input type=\"hidden\" name=\"agenda_publica\" id=\"agenda_publica\" value=\"{$this->publica}\">
        ";
        if ($this->erro_msg) {
            $conteudo .= "<center><h3>{$this->erro_msg}</h3></center>";
        }
        $conteudo .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';

        /*
        *   COMPROMISSOS
        */
        if (request('versoes') === null) {
            $this->compromissos = $agendaService->getDayCommitments(agenda: $agenda, date: $this->data_atual);

            if ($this->compromissos->isNotEmpty()) {
                $max_versoes = $agendaService->getMaxVersions(commitments: $this->compromissos);

                foreach ($this->compromissos as $compromisso) {
                    $data_inicio = $compromisso['data_inicio'];
                    $cod_agenda_compromisso = $compromisso['cod_agenda_compromisso'];
                    $versao = $compromisso['versao'];
                    $data_fim = $compromisso['data_fim'];
                    $titulo = $compromisso['titulo'];
                    $descricao = $compromisso['descricao'];
                    $importante = $compromisso['importante'];
                    $publico = $compromisso['publico'];

                    /*
                        variaveis que vao ser usadas para montar cada compromisso
                    */
                    $qtd_palavras_corta = 21;
                    $qtd_tit_copia_desc = 5;
                    $extras = 0;
                    $extenso = false;
                    $disp_conteudo = $descricao;
                    $img_publico = '';
                    $img_expandir = '';
                    $img_importante = '';
                    $img_versao = '';

                    $hora_inicio = date(format: 'H:i', timestamp: strtotime(datetime: $data_inicio));
                    if (strlen(string: $data_inicio) > 11) {
                        $hora_inicio_disp = "$hora_inicio -";
                    } else {
                        $hora_inicio_disp = '';
                    }
                    $hora_fim = date(format: 'H:i', timestamp: strtotime(datetime: $data_fim));
                    $aberto = 1;

                    // TITULO
                    if ($titulo) {
                        $disp_titulo = $titulo;
                    } else {
                        // se nao tiver titulo pega as X primeiras palavras da descricao ( X = $qtd_tit_copia_desc )
                        $disp_titulo = implode(separator: ' ', array: array_slice(array: explode(separator: ' ', string: $descricao), offset: 0, length: $qtd_tit_copia_desc));
                    }
                    $disp_titulo = "{$hora_inicio_disp} {$disp_titulo} - {$hora_fim}";
                    $disp_titulo = str_replace(search: '"', replace: '&quot;', subject: $disp_titulo);

                    // DESCRICAO
                    // exibe apenas as primeira X palavras da descricao, se tiver mais corta e define como texto mto extenso ( X = $qtd_palavras_corta )
                    $descArr = explode(separator: ' ', string: $descricao);
                    if (count(value: $descArr) > $qtd_palavras_corta) {
                        $extenso = true;
                        $disp_conteudo = implode(separator: ' ', array: array_slice(array: $descArr, offset: 0, length: $qtd_palavras_corta)) . '...';
                    }
                    $disp_conteudo = str_replace(search: "\n", replace: '<br>', subject: $disp_conteudo);
                    // se o texto for muito extenso exibe o botao para expandir e retrair
                    if ($extenso) {
                        $img_expandir = "<a href=\"javascript:agenda_expandir( {$cod_agenda_compromisso} );\"><img src=\"imagens/agenda_icon_retraido.gif\" border=\"0\" alt=\"Expandir\" title=\"Expandir este compromisso\"></a>";
                        $aberto = 0;
                    }

                    // se a agenda for publica exibira os icones de compromisso publico ou ptrivado
                    if ($this->publica) {
                        if ($publico) {
                            // eh publico
                            $extras += 2;
                            $img_publico = '<img src="imagens/nvp_icon_olho.gif" border="0" alt="Publico" title="Evento Publico">';
                        } else {
                            $img_publico = '<img src="imagens/nvp_icon_olho2.gif" border="0" alt="Privado" title="Evento Privado">';
                        }
                    }

                    if ($importante) {
                        // imagem de impotante
                        $img_importante = '<br><div class="important">
                                                  <i class="fa fa-check" aria-hidden="true"></i> Importante
                                               </div>';
                        // coloca o titulo dentro de um span com classe de alerta (somente se o compromisso ainda nao aconteceu)
                        if (strtotime(datetime: $data_inicio) >= $this->time_real_atual) {
                            $disp_titulo = "<span class=\"alerta\">{$disp_titulo}</span>";
                        }
                        // eh importante
                        $extras += 1;
                    }

                    $max_versao = $max_versoes[$cod_agenda_compromisso] ?? 0;
                    if ($max_versao > 1) {
                        $img_versao = "<br><a class=\"small\" href=\"{$this->scriptNome}?cod_agenda={$this->agenda}&time={$this->time_atual}&versoes={$cod_agenda_compromisso}\">
                                    <div class=\"history\">
                                        <i class=\"fa fa-history\" aria-hidden=\"true\"></i> Histórico
                                    </div>
                                </a>";
                    }

                    // se a hora atual for maior ele fica mais apagado porque eh antigo
                    if (strtotime(datetime: $data_inicio) < $this->time_real_atual) {
                        // eh antigo
                        $extras += 4;
                        $class_titulo = 'class="comp_tit_antigo"';
                        $class_desc = 'class="comp_antigo"';
                    } else {
                        $class_titulo = 'class="comp_tit"';
                        $class_desc = '';
                    }

                    $classe = ($classe == 'claro') ? 'escuro' : 'claro';

                    if ($cod_agenda_compromisso) {
                        $img_padrao = "<a class=\"small\" href=\"javascript: text2form( {$cod_agenda_compromisso} );\">
                                    <div>
                                        <i class=\"fa fa-pencil\" aria-hidden=\"true\"></i> Editar
                                    </div></a>
                                <a class=\"small\" href=\"javascript: excluir_compromisso( {$cod_agenda_compromisso} );\">
                                <div>
                                        <i class=\"fa fa-close\" aria-hidden=\"true\"></i> Excluir
                                </div></a>";
                    }

                    $conteudo .= "<tr>
                        <td class=\"{$classe}\" valign=\"top\" width=\"19\"><div id=\"agenda_expandir_{$cod_agenda_compromisso}\">{$img_expandir}</div><br>{$img_publico}</td>
                        <td class=\"{$classe}\" valign=\"top\">
                            <a name=\"anch{$cod_agenda_compromisso}\"></a>
                            <input type=\"hidden\" name=\"conteudo_original_{$cod_agenda_compromisso}\" id=\"conteudo_original_{$cod_agenda_compromisso}\" value=\"" . str_replace(search: '"', replace: '&quot;', subject: $descricao) . "\">
                            <input type=\"hidden\" name=\"titulo_original_{$cod_agenda_compromisso}\" id=\"titulo_original_{$cod_agenda_compromisso}\" value=\"" . str_replace(search: '"', replace: '&quot;', subject: $titulo) . "\">
                            <input type=\"hidden\" name=\"hora_original_ini_{$cod_agenda_compromisso}\" id=\"hora_original_ini_{$cod_agenda_compromisso}\" value=\"{$hora_inicio}\">
                            <input type=\"hidden\" name=\"hora_original_fim_{$cod_agenda_compromisso}\" id=\"hora_original_fim_{$cod_agenda_compromisso}\" value=\"{$hora_fim}\">
                            <input type=\"hidden\" name=\"data_original_{$cod_agenda_compromisso}\" id=\"data_original_{$cod_agenda_compromisso}\" value=\"{$this->data_atual}\">
                            <input type=\"hidden\" name=\"extras_original_{$cod_agenda_compromisso}\" id=\"extras_original_{$cod_agenda_compromisso}\" value=\"{$extras}\">
                            <input type=\"hidden\" name=\"aberto_{$cod_agenda_compromisso}\" id=\"aberto_{$cod_agenda_compromisso}\" value=\"{$aberto}\">
                            <div id=\"compromisso_{$cod_agenda_compromisso}\" class=\"compromisso\">
                                <div id=\"titulo_{$cod_agenda_compromisso}\" {$class_titulo}>{$disp_titulo}</div>
                                <div id=\"conteudo_{$cod_agenda_compromisso}\" {$class_desc}>{$disp_conteudo}</div>
                            </div>
                        </td>
                        <td class=\"{$classe}\" align=\"right\" width=\"115\" valign=\"top\"><div id=\"botoes_{$cod_agenda_compromisso}\" class=\"small\">{$img_padrao}{$img_importante}{$img_versao}</div></td>
                    </tr>";
                }
            }

            $classe = ($classe == 'claro') ? 'escuro' : 'claro';

            $conteudo .= "<tr><td colspan=\"3\" class=\"{$classe}\" align=\"center\" height=\"60\"><br><input type=\"button\" name=\"agenda_novo\" class=\"agenda_rap_botao btn-green\" id=\"agenda_novo\" value=\"Novo Compromisso\" onclick=\"novoForm();\"></td></tr>";
        } else {
            $filtered_versoes = request()->integer('versoes');

            // verifica se o compromisso eh mesmo dessa agenda
            $db->Consulta(consulta: "SELECT 1 FROM portal.agenda_compromisso WHERE ref_cod_agenda = '{$this->agenda}' AND cod_agenda_compromisso = '{$filtered_versoes}'");
            if ($db->numLinhas()) {
                // seleciona as versoes desse compromisso
                $db->Consulta(consulta: "SELECT versao, ref_ref_cod_pessoa_cad, ativo, data_inicio, titulo, descricao, importante, publico, data_cadastro, data_fim FROM portal.agenda_compromisso WHERE cod_agenda_compromisso = '{$filtered_versoes}' ORDER BY versao DESC");
                while ($db->ProximoRegistro()) {
                    unset($versao, $ref_ref_cod_pessoa_cad, $ativo, $data_inicio, $titulo, $descricao, $importante, $publico, $data_cadastro, $data_fim);
                    [$versao, $ref_ref_cod_pessoa_cad, $ativo, $data_inicio, $titulo, $descricao, $importante, $publico, $data_cadastro, $data_fim] = $db->Tupla();

                    $nome = $db2->CampoUnico(consulta: "SELECT nome FROM cadastro.pessoa WHERE idpes = '{$ref_ref_cod_pessoa_cad}'");
                    $ativo = ($ativo) ? '<b>Ativo</b>' : 'Inativo';
                    $importante = ($importante) ? 'Sim' : 'Não';
                    $publico = ($publico) ? 'Sim' : 'Não';
                    if ($data_fim) {
                        $data_fim = date(format: 'd/m/Y H:i', timestamp: strtotime(datetime: $data_fim));
                    } else {
                        $data_fim = 'Este compromisso era uma Anotação';
                    }

                    $conteudo .= "<tr><td>Versão:</td><td>{$versao}</td></tr>\n";
                    $conteudo .= "<tr><td>Titulo:</td><td>{$titulo}</td></tr>\n";
                    $conteudo .= '<tr><td>Inicio:</td><td>' . date(format: 'd/m/Y H:i', timestamp: strtotime(datetime: $data_inicio)) . "</td></tr>\n";
                    $conteudo .= "<tr><td>Fim:</td><td>{$data_fim}</td></tr>\n";
                    $conteudo .= '<tr><td>Descricao:</td><td>' . str_replace(search: "\n", replace: "<br>\n", subject: $descricao) . "</td></tr>\n";
                    $conteudo .= "<tr><td>Status:</td><td>{$ativo}</td></tr>\n";
                    $conteudo .= "<tr><td>Importante:</td><td>{$importante}</td></tr>\n";
                    $conteudo .= "<tr><td>Publico:</td><td>{$publico}</td></tr>\n";
                    $conteudo .= "<tr><td>Responsável:</td><td>$nome</td></tr>\n";
                    $conteudo .= "<tr><td>Reativar?</td><td><a href=\"{$this->scriptNome}?cod_agenda={$this->agenda}&time={$this->time_atual}&restaura={$filtered_versoes}&versao={$versao}\">Clique aqui para reativar esta versão</a></td></tr>\n";
                    $conteudo .= "<tr><td colspan=\"2\"><hr></td></tr>\n";
                }
                $conteudo .= "<tr><td colspan=\"2\" align=\"center\"><input type=\"button\" name=\"voltar\" value=\"Voltar\" class=\"agenda_rap_botao\" onclick=\"document.location.href='{$this->scriptNome}?cod_agenda={$this->agenda}&time={$this->time_atual}'\"></td></tr>";
            }
        }

        /*
        *   fim da pagina
        */
        $conteudo .= '</table>
                    </form>
                </td>
                <td width="20%" valign="top" align="center" class="escuro">
        ';
        $objCalendario = new AgendaCalendar(time: $this->time_atual, url_default: "{$this->scriptNome}?cod_agenda={$this->agenda}");
        $conteudo .= $objCalendario->gera_calendario();

        $conteudo .= '
                </td>
            </tr>
            <tr>
                <td align="center" class="escuro"><span class="titulo">Importante</span></td>
            </tr>
            <tr>
                <td class="escuro" valign="top">';

        $db->Consulta(consulta: "SELECT data_inicio, titulo, descricao FROM portal.agenda_compromisso WHERE ref_cod_agenda = '{$this->agenda}' AND ativo = 1 AND importante = 1 AND data_inicio > NOW() ORDER BY data_inicio ASC LIMIT 5 OFFSET 0");
        while ($db->ProximoRegistro()) {
            [$aviso_inicio, $aviso_titulo, $aviso_descricao] = $db->Tupla();
            $avis_desc_arr = explode(separator: ' ', string: $aviso_descricao);
            if (count(value: $avis_desc_arr) > 25) {
                $aviso_descricao = implode(separator: ' ', array: array_slice(array: $avis_desc_arr, offset: 0, length: 25)) . '...';
            }
            if (!$aviso_titulo) {
                $aviso_titulo = implode(separator: ' ', array: array_slice(array: $avis_desc_arr, offset: 0, length: 7)) . '...';
            }
            $aviso_time = strtotime(datetime: $aviso_inicio);
            $conteudo .= "<span title=\"{$aviso_descricao}\">
                <a href=\"{$this->scriptNome}?cod_agenda={$this->agenda}&time={$aviso_time}\"><b>" . date(format: 'd/m/Y', timestamp: $aviso_time) . ' - ' . date(format: 'H:i', timestamp: $aviso_time) . "</b></a><br>
                {$aviso_titulo}
            </span>
            <br><br>";
        }

        $conteudo .= '</td>
            </tr>
            <tr>
                <td align="center" class="escuro"><span class="titulo">Anotações</span></td>
            </tr>
            <tr>
                <td class="escuro" valign="top">
                    <form id="notas" action="" method="POST">
                        <table border="0" cellpadding="0" cellspacing="0">
        ';
        unset($cod_agenda_compromisso, $versao, $data_inicio, $data_fim, $titulo, $descricao, $importante, $publico);
        $i = 0;
        $db->Consulta(consulta: "SELECT cod_agenda_compromisso, versao, data_inicio, data_fim, titulo, descricao, importante, publico FROM portal.agenda_compromisso WHERE ref_cod_agenda = '{$this->agenda}' AND ativo = 1 AND data_fim IS NULL AND data_inicio >= '{$this->data_atual_db}' AND data_inicio <= '{$this->data_atual_db} 23:59:59' ORDER BY data_inicio ASC");
        while ($db->ProximoRegistro()) {
            [$cod_agenda_compromisso, $versao, $data_inicio, $data_fim, $titulo, $descricao, $importante, $publico] = $db->Tupla();
            $conteudo .= "<tr><td><input class=\"notas\" type=\"text\" name=\"nota_{$i}\" id=\"nota_{$i}\" value=\"{$titulo}\"></td><td><a href=\"javascript: salvaNota( {$cod_agenda_compromisso} );\"><img src=\"imagens/nvp_agenda_compromisso.gif\" border=\"0\" alt=\"Salvar\" title=\"Salvar como Compromisso\"></a></td></tr>";
            $i++;
        }

        return $conteudo . '
                        </table>
                    </form>
                </td>
            </tr>
        </table>';
    }

    public function Formular()
    {
        $this->titulo = 'Agenda Particular';
        $this->processoAp = '0';
    }
};
