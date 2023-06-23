<?php

use App\Models\LegacySchoolAcademicYear;
use App\Models\PersonHasPlace;

return new class extends clsDetalhe
{
    public $cod_escola;

    public $ref_usuario_cad;

    public $ref_usuario_exc;

    public $ref_cod_instituicao;

    public $ref_idpes;

    public $sigla;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $nm_escola;

    const POLI_INSTITUCIONAL = 1;

    const INSTITUCIONAL = 2;

    public function Gerar()
    {
        // Verificação de permissão para cadastro.
        $this->obj_permissao = new clsPermissoes();

        $this->nivel_usuario = $this->user()->getLevel();

        $this->titulo = 'Escola - Detalhe';

        $this->cod_escola = $_GET['cod_escola'];

        $tmp_obj = new clsPmieducarEscola(cod_escola: $this->cod_escola);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect(url: 'educar_aluno_lst.php');
        }

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        if ($registro['ref_idpes']) {
            $obj_escola = new clsPessoa_(int_idpes: $registro['ref_idpes']);
            $obj_escola_det = $obj_escola->detalhe();
            $url = $obj_escola_det['url'];
            $email = $obj_escola_det['email'];
            $obj_escola1 = new clsPessoaJuridica(int_idpes: $registro['ref_idpes']);
            $obj_escola_det1 = $obj_escola1->detalhe();
            $nm_escola = $obj_escola_det1['fantasia'];

            $place = PersonHasPlace::query()
                ->with(relations: 'place.city.state')
                ->where(column: 'person_id', operator: $registro['ref_idpes'])
                ->orderBy(column: 'type')
                ->first();

            $obj_telefone = new clsPessoaTelefone();
            $telefone_lst = $obj_telefone->lista(int_idpes: $registro['ref_idpes'], str_ordenacao: 'tipo');
            if ($telefone_lst) {
                foreach ($telefone_lst as $telefone) {
                    if ($telefone['tipo'] == 1) {
                        $telefone_1 = '(' . $telefone['ddd'] . ') ' . $telefone['fone'];
                    } elseif ($telefone['tipo'] == 2) {
                        $telefone_2 = '(' . $telefone['ddd'] . ') ' . $telefone['fone'];
                    } elseif ($telefone['tipo'] == 3) {
                        $telefone_mov = '(' . $telefone['ddd'] . ') ' . $telefone['fone'];
                    } elseif ($telefone['tipo'] == 4) {
                        $telefone_fax = '(' . $telefone['ddd'] . ') ' . $telefone['fone'];
                    }
                }
            }
        }

        $obj_ref_idpes = new clsPessoaJuridica(int_idpes: $registro['ref_idpes']);
        $det_ref_idpes = $obj_ref_idpes->detalhe();
        $registro['ref_idpes'] = $det_ref_idpes['nome'];

        if ($registro['ref_cod_instituicao']) {
            $this->addDetalhe(detalhe: ['Instituição', "{$registro['ref_cod_instituicao']}"]);
        }

        if ($nm_escola) {
            $this->addDetalhe(detalhe: ['Escola', "{$nm_escola}"]);
        }

        if ($registro['sigla']) {
            $this->addDetalhe(detalhe: ['Sigla', "{$registro['sigla']}"]);
        }

        if ($registro['zona_localizacao']) {
            $zona = App_Model_ZonaLocalizacao::getInstance();
            $this->addDetalhe(detalhe: [
                'Zona Localização', $zona->getValue(key: $registro['zona_localizacao']),
            ]);
        }

        if ($registro['ref_idpes']) {
            $this->addDetalhe(detalhe: ['Razão Social', "{$registro['ref_idpes']}"]);
        }

        if (isset($place)) {
            $place = $place->place;

            $this->addDetalhe(detalhe: ['Logradouro', $place->address]);
            $this->addDetalhe(detalhe: ['Número', $place->number]);
            $this->addDetalhe(detalhe: ['Complemento', $place->complement]);
            $this->addDetalhe(detalhe: ['Bairro', $place->neighborhood]);
            $this->addDetalhe(detalhe: ['CEP', int2CEP(int: $place->postal_code)]);
        }

        if ($url) {
            $this->addDetalhe(detalhe: ['Site', "{$url}"]);
        }
        if ($email) {
            $this->addDetalhe(detalhe: ['E-mail', "{$email}"]);
        }
        if ($telefone_1) {
            $this->addDetalhe(detalhe: ['Telefone 1', "{$telefone_1}"]);
        }
        if ($telefone_2) {
            $this->addDetalhe(detalhe: ['Telefone 2', "{$telefone_2}"]);
        }
        if ($telefone_mov) {
            $this->addDetalhe(detalhe: ['Celular', "{$telefone_mov}"]);
        }
        if ($telefone_fax) {
            $this->addDetalhe(detalhe: ['Fax', "{$telefone_fax}"]);
        }

        $obj = new clsPmieducarEscolaCurso();
        $lst = $obj->lista(int_ref_cod_escola: $this->cod_escola);

        if ($lst) {
            $tabela = '<table>
                           <tr align=\'center\'>
                               <td bgcolor=\'#ccdce6\'><b>nome</b></td>
                           </tr>';
            $cont = 0;

            foreach ($lst as $valor) {
                if (($cont % 2) == 0) {
                    $color = ' bgcolor=\'#f5f9fd\' ';
                } else {
                    $color = ' bgcolor=\'#ffffff\' ';
                }
                $obj_curso = new clsPmieducarCurso(cod_curso: $valor['ref_cod_curso']);
                $obj_curso->setorderby(strNomeCampo: 'nm_curso asc');
                $obj_curso_det = $obj_curso->detalhe();
                $nm_curso = $obj_curso_det['nm_curso'];

                $tabela .= "<tr>
                                <td {$color} align=left>{$nm_curso}</td>
                            </tr>";
                $cont++;
            }
            $tabela .= '</table>';
        }

        if ($nm_curso) {
            $this->addDetalhe(detalhe: ['Curso', "{$tabela}"]);
        }

        if ($tabela = $this->listaAnos()) {
            $this->addDetalhe(detalhe: ['-', "{$tabela}"]);
        }

        $obj_permissoes = new clsPermissoes();

        $canCreate = $obj_permissoes->permissao_cadastra(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3);
        $canEdit = $obj_permissoes->permissao_cadastra(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7);

        if ($canCreate) {
            $this->url_novo = 'educar_escola_cad.php';
        }

        if ($canEdit) {
            $this->url_editar = "educar_escola_cad.php?cod_escola={$registro['cod_escola']}";
            $this->array_botao = ['Definir Ano Letivo'];
            $this->array_botao_url = ["educar_escola_ano_letivo_cad.php?cod_escola={$registro['cod_escola']}"];
        }

        $styles = ['/vendor/legacy/Cadastro/Assets/Stylesheets/EscolaAnosLetivos.css'];

        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: $styles);

        $this->url_cancelar = 'educar_escola_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da escola', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function listaAnos()
    {
        if (!$this->cod_escola) {
            return false;
        }

        $existe = false;
        $lista_ano_letivo = LegacySchoolAcademicYear::query()->whereSchool($this->cod_escola)->active()->orderBy('ano')->get();

        $tabela = '<table class=\'anosLetivos\'>';

        $obj_permissoes = new clsPermissoes();
        $canEdit = $obj_permissoes->permissao_cadastra(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7);
        $cor = null;

        if ($lista_ano_letivo->isNotEmpty()) {
            $existe = true;
            $tabela .= "<tr bgcolor=$cor><td colspan='2'><b>Anos letivos</b></td></tr><tr><td>";
            $tabela .= '<table cellpadding="2" cellspacing="2" border="0" align="left" width=\'60%\'>';
            $tabela .= '<tr bgcolor=\'#ccdce6\'><th width=\'90\'>Ano<a name=\'ano_letivo\'/></th><th width=\'70\'>Iniciar</th><th width=\'70\'>Finalizar</th><th width=\'150\'>Editar</th></tr>';
            $cor = $cor == '#FFFFFF' ? '#f5f9fd' : '#FFFFFF';

            $existe_ano_andamento = LegacySchoolAcademicYear::query()->whereSchool($this->cod_escola)->active()->inProgress()->exists();

            foreach ($lista_ano_letivo as $ano) {
                if (!$existe_ano_andamento && $ano['andamento'] != 2 && $canEdit) {
                    $incluir = "<td class='evento'><a href='#' onclick=\"preencheForm('{$ano['ano']}','{$ano['ref_cod_escola']}','iniciar');\"><img src=\"imagens/i-educar/start.gif\"> Iniciar ano letivo</a></td>";
                } elseif ($ano['andamento'] == 0) {
                    $incluir = "<td class='evento'><a href='#' onclick=\"preencheForm('{$ano['ano']}','{$ano['ref_cod_escola']}','iniciar');\"><img src=\"imagens/i-educar/start.gif\"> Iniciar ano letivo</a></td>";
                } else {
                    $incluir = '<td width=\'130\'>&nbsp;</td>';
                }

                //verifica se o ano nao possui matricula em andamento para permitir finalizar o ano
                $obj_matricula_ano = new clsPmieducarMatricula();
                $matricula_em_andamento = $obj_matricula_ano->lista(int_ref_ref_cod_escola: $this->cod_escola, int_aprovado: 3, int_ativo: 1, int_ultima_matricula: 1, int_ano: $ano['ano'], bool_curso_sem_avaliacao: false);

                if (!$matricula_em_andamento && $existe_ano_andamento && $ano['andamento'] == 1 && $canEdit) {
                    $excluir = "<td class='evento'><a href='#' onclick=\"preencheForm('{$ano['ano']}','{$ano['ref_cod_escola']}','finalizar');\" ><img src=\"imagens/i-educar/stop.png\"> Finalizar ano letivo</a></td>";
                } else {
                    $excluir = '<td width=\'130\'>&nbsp;</td>';
                }

                $editar = '';

                if ($ano['andamento'] == 2) {
                    if ($this->nivel_usuario == self::POLI_INSTITUCIONAL || $this->nivel_usuario == self::INSTITUCIONAL) {
                        $incluir = "<td class='evento'><a href='#' onclick=\"preencheForm('{$ano['ano']}','{$ano['ref_cod_escola']}','reabrir');\"><img src=\"imagens/banco_imagens/reload.jpg\"> Reabrir ano letivo</a></td>";
                    }
                    $incluir .= '<td colspan=\'1\' align=\'center\'><span class=\'formlttd\'><b>--- Ano Finalizado ---</b></span></td>';
                } elseif ($canEdit) {
                    $editar = "<td class='evento'><a href='#' onclick=\"preencheForm('{$ano['ano']}','{$ano['ref_cod_escola']}','editar');\" ><img src=\"imagens/banco_imagens/e.gif\" > Editar ano letivo</a></td>";
                }

                $tabela .= "<tr bgcolor='$cor'><td style='padding-left:20px'><img src=\"imagens/noticia.jpg\" border='0'> {$ano['ano']}</td>{$incluir}{$excluir}{$editar}</tr>";
            }

            $tabela .= '</table></td></tr>';
            $tabela .= '<tr>
                            <td>
                                <span class=\'formlttd\'><b>*Somente é possível finalizar um ano letivo após não existir mais nenhuma matrícula em andamento.</b></span>
                            </td>
                        </tr>';
            if (!$canEdit) {
                $tabela .= '<tr>
                            <td>
                                <span class=\'formlttd\'><b>**Somente usuários com permissão de edição de escola podem alterar anos letivos.</b></span>
                            </td>
                        </tr>';
            }
            $tabela .= '<tr>
                            <td>
                                <form name=\'acao_ano_letivo\' action=\'educar_iniciar_ano_letivo.php\' method=\'post\'>
                                    <input type=\'hidden\' name=\'ano\' id=\'ano\'>
                                    <input type=\'hidden\' name=\'ref_cod_escola\' id=\'ref_cod_escola\'>
                                    <input type=\'hidden\' name=\'tipo_acao\' id=\'tipo_acao\'>
                                </form>
                            </td>
                        </tr>';
        }

        $tabela .= '</table>';

        return $existe == true ? $tabela : false;
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-escola-det.js');
    }

    public function Formular()
    {
        $this->title = 'Escola';
        $this->processoAp = '561';
    }
};
