<?php

use App\Services\iDiarioService;
use RuntimeException;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'App/Date/Utils.php';
require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';

class clsIndexBase extends clsBase
{

    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Ano Letivo Etapa');
        $this->processoAp = 561;
    }
}

class indice extends clsCadastro
{

    public $pessoa_logada;

    public $ref_cod_instituicao;

    public $ref_ano;

    public $ref_ref_cod_escola;

    public $sequencial;

    public $ref_cod_modulo;

    public $data_inicio;

    public $data_fim;

    public $ano_letivo_modulo;

    public $modulos = [];

    public $etapas = [];

    public function Inicializar()
    {
        $retorno = 'Novo';



        $this->ref_cod_modulo = $_GET['ref_cod_modulo'];
        $this->ref_ref_cod_escola = $_GET['ref_cod_escola'];
        $this->ref_ano = $_GET['ano'];

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            561,
            $this->pessoa_logada,
            7,
            'educar_escola_lst.php'
        );

        if (is_numeric($this->ref_ano) && is_numeric($this->ref_ref_cod_escola)) {
            $obj = new clsPmieducarEscolaAnoLetivo($this->ref_ref_cod_escola, $this->ref_ano);
            $registro = $obj->detalhe();

            if ($registro) {
                if ($obj_permissoes->permissao_excluir(561, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';

                $etapasObj = new clsPmieducarAnoLetivoModulo();
                $etapasObj->setOrderBy('sequencial ASC');
                $this->etapas = $etapasObj->lista($this->ref_ano, $this->ref_ref_cod_escola);
                $this->ref_cod_modulo = $this->etapas[0]['ref_cod_modulo'];
            }
        }

        $this->url_cancelar = $_GET['referrer']
            ? $_GET['referrer'] . '?cod_escola=' . $this->ref_ref_cod_escola
            : 'educar_escola_lst.php';

        $this->breadcrumb('Etapas do ano letivo', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = $this->$campo ? $this->$campo : $val;
            }
        }

        // Primary keys
        $this->campoOculto('ref_ano', $this->ref_ano);
        $this->campoOculto('ref_ref_cod_escola', $this->ref_ref_cod_escola);

        $obj_escola = new clsPmieducarEscola($this->ref_ref_cod_escola);
        $det_escola = $obj_escola->detalhe();
        $ref_cod_instituicao = $det_escola['ref_cod_instituicao'];
        $this->ref_cod_instituicao = $ref_cod_instituicao;

        $obj = new clsPmieducarAnoLetivoModulo();
        $obj->setOrderBy('sequencial ASC');
        $registros = $obj->lista($this->ref_ano - 1, $this->ref_ref_cod_escola);
        $cont = 0;

        if ($registros) {
            $cor = '';
            $tabela = '<table border=0 style=\'\' cellpadding=2 width=\'100%\'>';
            $tabela .= "<tr bgcolor=$cor><td colspan='2'>Etapas do ano anterior (".($this->ref_ano - 1).')</td></tr><tr><td>';
            $tabela .= '<table cellpadding="2" cellspacing="2" border="0" align="left" width=\'300px\'>';
            $tabela .= '<tr bgcolor=\'#ccdce6\'><th width=\'100px\'>Etapa<a name=\'ano_letivo\'/></th><th width=\'200px\'>Período</th></tr>';

            $existeBissexto = false;

            foreach ($registros as $campo) {
                $cor = '#f5f9fd';
                $cont++;
                $tabela .= "<tr bgcolor='$cor'><td align='center'>{$cont}</td><td align='center'>".dataFromPgToBr($campo['data_inicio']).' à '.dataFromPgToBr($campo['data_fim']).'</td></tr>';

                $ano = date_parse_from_format('Y-m-d', $campo['data_inicio']);
                $ano = $ano['year'];

                $novaDataInicio = str_replace($ano, $this->ref_ano, $campo['data_inicio']);
                $novaDataFim = str_replace($ano, $this->ref_ano, $campo['data_fim']);

                if (
                    Portabilis_Date_Utils::checkDateBissexto($novaDataInicio)
                    || Portabilis_Date_Utils::checkDateBissexto($novaDataFim)
                ) {
                    $existeBissexto = true;
                }
            }

            if ($existeBissexto) {
                $tabela .= "<tr bgcolor='#FCF8E3' style='color: #8A6D3B; font-weight:normal;'>
                    <td align='center'><b>Observação:</b></td>
                    <td align='center'>A data 29/02/$this->ref_ano não poderá ser migrada pois $this->ref_ano não é um ano bissexto, portanto será substituída por 28/02/$this->ref_ano.</td>
                    </tr>";
            }

            $tabela .='</table>';
            $tabela .= "<tr><td colspan='2'><b> Adicione as etapas abaixo para {$this->ref_ano} semelhante ao exemplo do ano anterior: </b></td></tr><tr><td>";
            $tabela .= '</table>';
        }

        $ref_ano_ = $this->ref_ano;

        $this->campoTexto(
            'ref_ano_',
            'Ano',
            $ref_ano_,
            4,
            4,
            false,
            false,
            false,
            '',
            '',
            '',
            '',
            true
        );

        $opcoesCampoModulo = [];

            $objTemp = new clsPmieducarModulo();
            $objTemp->setOrderby('nm_tipo ASC');

            $lista = $objTemp->lista(
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $ref_cod_instituicao
            );

            if (is_array($lista) && count($lista)) {
                $this->modulos = $lista;

                foreach ($lista as $registro) {
                    $opcoesCampoModulo[$registro['cod_modulo']] = sprintf('%s - %d etapa(s)', $registro['nm_tipo'], $registro['num_etapas']);
                }
            }

        $this->campoLista(
            'ref_cod_modulo',
            'Etapa',
            $opcoesCampoModulo,
            $this->ref_cod_modulo,
            null,
            null,
            null,
            null,
            null,
            true
        );

        if ($tabela) {
            $this->campoQuebra();
            $this->campoRotulo('modulosAnoAnterior', '-', $tabela);
        }

        $this->campoQuebra();

        if (is_numeric($this->ref_ano) && is_numeric($this->ref_ref_cod_escola) && !$_POST) {
            $qtd_registros = 0;

            foreach ($this->etapas as $campo) {
                $this->ano_letivo_modulo[$qtd_registros][] = dataFromPgToBr($campo['data_inicio']);
                $this->ano_letivo_modulo[$qtd_registros][] = dataFromPgToBr($campo['data_fim']);
                $this->ano_letivo_modulo[$qtd_registros][] = $campo['dias_letivos'];
                $qtd_registros++;
            }

            $this->campoTabelaInicio(
                'modulos_ano_letivo',
                'Etapas do ano letivo',
                ['Data inicial', 'Data final', 'Dias Letivos'],
                $this->ano_letivo_modulo
            );

            $this->campoData('data_inicio', 'Hora', $this->data_inicio, true);
            $this->campoData('data_fim', 'Hora', $this->data_fim, true);
            $this->campoNumero('dias_letivos', 'Dias Letivos', $this->dias_letivos, 6, 3, false);

            $this->campoTabelaFim();
        }

        Portabilis_View_Helper_Application::loadJavascript($this, [
            '/modules/Portabilis/Assets/Javascripts/Validator.js',
            '/intranet/scripts/etapas.js'
        ]);
    }

    public function Novo()
    {


        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            561,
            $this->pessoa_logada,
            7,
            'educar_escola_lst.php'
        );

        if ($this->ref_cod_modulo && $this->data_inicio && $this->data_fim) {
            $this->copiarTurmasUltimoAno($this->ref_ref_cod_escola, $this->ref_ano);
            Portabilis_Utils_Database::selectField("SELECT pmieducar.copiaAnosLetivos({$this->ref_ano}::smallint, {$this->ref_ref_cod_escola});");

            $obj = new clsPmieducarEscolaAnoLetivo(
                $this->ref_ref_cod_escola,
                $this->ref_ano,
                $this->pessoa_logada,
                null,
                0,
                null,
                null,
                1,
                1
            );

            $cadastrou = $obj->cadastra();

            if ($cadastrou) {
                foreach ($this->data_inicio as $key => $campo) {
                    $this->data_inicio[$key] = dataToBanco($this->data_inicio[$key]);
                    $this->data_fim[$key] = dataToBanco($this->data_fim[$key]);

                    if ($this->dias_letivos[$key] == '') {
                        $this->dias_letivos[$key] = '0';
                    }

                    $obj = new clsPmieducarAnoLetivoModulo(
                        $this->ref_ano,
                        $this->ref_ref_cod_escola,
                        $key + 1,
                        $this->ref_cod_modulo,
                        $this->data_inicio[$key],
                        $this->data_fim[$key],
                        $this->dias_letivos[$key]
                    );

                    $cadastrou1 = $obj->cadastra();

                    if (!$cadastrou1) {
                        $this->mensagem = 'Cadastro não realizado.<br />';

                        return false;
                    }
                }

                $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';

                $this->simpleRedirect('educar_escola_det.php?cod_escola=' . $this->ref_ref_cod_escola . '#ano_letivo');
            }

            $this->mensagem = 'Cadastro não realizado. <br />';

            return false;
        }

        $this->mensagem = 'Cadastro não realizado.<br />';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            561,
            $this->pessoa_logada,
            7,
            'educar_escola_lst.php'
        );

        if ($this->ref_cod_modulo && $this->data_inicio && $this->data_fim) {
            try {
                $this->validaModulos();
            } catch (Exception $e) {
                $_POST = [];

                $this->Inicializar();

                $this->mensagem = $e->getMessage();

                return false;
            }

            $obj = new clsPmieducarAnoLetivoModulo($this->ref_ano, $this->ref_ref_cod_escola);
            $excluiu = $obj->excluirTodos();

            if ($excluiu) {
                foreach ($this->data_inicio as $key => $campo) {
                    $this->data_inicio[$key] = dataToBanco($this->data_inicio[$key]);
                    $this->data_fim[$key] = dataToBanco($this->data_fim[$key]);

                    if ($this->dias_letivos[$key] == '') {
                        $this->dias_letivos[$key] = '0';
                    }

                    $obj = new clsPmieducarAnoLetivoModulo(
                        $this->ref_ano,
                        $this->ref_ref_cod_escola,
                        $key + 1,
                        $this->ref_cod_modulo,
                        $this->data_inicio[$key],
                        $this->data_fim[$key],
                        $this->dias_letivos[$key]
                    );

                    $cadastrou1 = $obj->cadastra();

                    if (!$cadastrou1) {
                        $this->mensagem = 'Edição não realizada.<br />';

                        return false;
                    }
                }

                $this->mensagem .= 'Edição efetuada com sucesso.<br />';
                $this->simpleRedirect('educar_escola_lst.php');
            }
        }

        echo '<script>alert(\'É necessário adicionar pelo menos uma etapa!\')</script>';
        $this->mensagem = 'Edição não realizada.<br />';

        return false;
    }

    public function Excluir()
    {


        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_excluir(
            561,
            $this->pessoa_logada,
            7,
            'educar_escola_lst.php'
        );

        $obj = new clsPmieducarEscolaAnoLetivo(
            $this->ref_ref_cod_escola,
            $this->ref_ano,
            null,
            $this->pessoa_logada,
            null,
            null,
            null,
            0
        );

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $obj = new clsPmieducarAnoLetivoModulo($this->ref_ano, $this->ref_ref_cod_escola);
            $excluiu1 = $obj->excluirTodos();

            if ($excluiu1) {
                $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
                $this->simpleRedirect('educar_escola_lst.php');
            }

            $this->mensagem = 'Exclusão não realizada.<br />';

            return false;
        }

        $this->mensagem = 'Exclusão não realizada.<br />';

        return false;
    }

    public function copiarTurmasUltimoAno($escolaId, $anoDestino)
    {
        $sql = 'select ano from pmieducar.escola_ano_letivo where ref_cod_escola = $1 ' .
            'and ativo = 1 and ano in (select max(ano) from pmieducar.escola_ano_letivo where ' .
            'ref_cod_escola = $1 and ativo = 1)';

        $ultimoAnoLetivo = Portabilis_Utils_Database::selectRow($sql, $escolaId);
        $turmasEscola = new clsPmieducarTurma();
        $turmasEscola = $turmasEscola->lista(
            null,
            null,
            null,
            null,
            $escolaId,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            false,
            null,
            true,
            null,
            null,
            $ultimoAnoLetivo['ano']
        );

        foreach ($turmasEscola as $turma) {
            $this->copiarTurma($turma, $ultimoAnoLetivo['ano'], $anoDestino);
        }
    }

    public function copiarTurma($turmaOrigem, $anoOrigem, $anoDestino)
    {
        $sql = 'select 1 from turma where ativo = 1 and visivel = true
            and ref_ref_cod_escola = $1 and nm_turma = $2 and ref_ref_cod_serie = $3 and ano = $4 limit 1';

        $params = [
            $turmaOrigem['ref_ref_cod_escola'],
            $turmaOrigem['nm_turma'],
            $turmaOrigem['ref_ref_cod_serie'],
            $anoDestino
        ];

        $existe = Portabilis_Utils_Database::selectField($sql, $params);

        if ($existe != 1) {
            $fields = [
                'ref_usuario_exc',
                'ref_usuario_cad',
                'ref_ref_cod_serie',
                'ref_ref_cod_escola',
                'ref_cod_infra_predio_comodo',
                'nm_turma',
                'sgl_turma',
                'max_aluno',
                'multiseriada',
                'data_cadastro',
                'data_exclusao',
                'ativo',
                'ref_cod_turma_tipo',
                'hora_inicial',
                'hora_final',
                'hora_inicio_intervalo',
                'hora_fim_intervalo',
                'ref_cod_regente',
                'ref_cod_instituicao_regente',
                'ref_cod_instituicao',
                'ref_cod_curso',
                'ref_ref_cod_serie_mult',
                'ref_ref_cod_escola_mult',
                'visivel',
                'turma_turno_id',
                'tipo_boletim',
                'tipo_boletim_diferenciado',
                'ano',
                'dias_semana',
                'atividades_complementares',
                'atividades_aee',
                'turma_unificada',
                'tipo_atendimento',
                'etapa_educacenso',
                'cod_curso_profissional',
                'tipo_mediacao_didatico_pedagogico',
                'nao_informar_educacenso',
                'local_funcionamento_diferenciado'
            ];

            $turmaDestino = new clsPmieducarTurma();

            foreach ($fields as $fieldName) {
                $turmaDestino->$fieldName = $turmaOrigem[$fieldName];
            }

            $turmaDestino->ano = $anoDestino;
            $turmaDestino->ref_usuario_cad = $this->pessoa_logada;
            $turmaDestino->ref_usuario_exc = $this->pessoa_logada;
            $turmaDestino->visivel = dbBool($turmaOrigem['visivel']);
            $turmaDestinoId = $turmaDestino->cadastra();

            $this->copiarComponenteCurricularTurma($turmaOrigem['cod_turma'], $turmaDestinoId);
            $this->copiarModulosTurma($turmaOrigem['cod_turma'], $turmaDestinoId, $anoOrigem, $anoDestino);
        }
    }

    public function copiarComponenteCurricularTurma($turmaOrigemId, $turmaDestinoId)
    {
        $dataMapper = new ComponenteCurricular_Model_TurmaDataMapper();
        $componentesTurmaOrigem = $dataMapper->findAll([], ['turma' => $turmaOrigemId]);

        foreach ($componentesTurmaOrigem as $componenteTurmaOrigem) {
            $data = [
                'componenteCurricular' => $componenteTurmaOrigem->get('componenteCurricular'),
                'escola' => $componenteTurmaOrigem->get('escola'),
                'cargaHoraria' => $componenteTurmaOrigem->get('cargaHoraria'),
                'turma' => $turmaDestinoId,
                // está sendo mantido o mesmo ano_escolar_id, uma vez que não foi
                // foi encontrado de onde o valor deste campo é obtido.
                'anoEscolar' => $componenteTurmaOrigem->get('anoEscolar')
            ];

            $componenteTurmaDestino = $dataMapper->createNewEntityInstance($data);
            $dataMapper->save($componenteTurmaDestino);
        }
    }

    public function copiarModulosTurma($turmaOrigemId, $turmaDestinoId, $anoOrigem, $anoDestino)
    {
        $modulosTurmaOrigem = new clsPmieducarTurmaModulo();
        $modulosTurmaOrigem = $modulosTurmaOrigem->lista($turmaOrigemId);

        foreach ($modulosTurmaOrigem as $moduloOrigem) {
            $moduloDestino = new clsPmieducarTurmaModulo();

            $moduloDestino->ref_cod_modulo = $moduloOrigem['ref_cod_modulo'];
            $moduloDestino->sequencial = $moduloOrigem['sequencial'];
            $moduloDestino->ref_cod_turma = $turmaDestinoId;

            $moduloDestino->data_inicio = str_replace(
                $anoOrigem,
                $anoDestino,
                $moduloOrigem['data_inicio']
            );

            $moduloDestino->data_fim = str_replace(
                $anoOrigem,
                $anoDestino,
                $moduloOrigem['data_fim']
            );

            if (Portabilis_Date_Utils::checkDateBissexto($moduloDestino->data_inicio)) {
                $moduloDestino->data_inicio = str_replace(29, 28, $moduloDestino->data_inicio);
            }

            if (Portabilis_Date_Utils::checkDateBissexto($moduloDestino->data_fim)) {
                $moduloDestino->data_fim = str_replace(29, 28, $moduloDestino->data_fim);
            }

            $moduloDestino->cadastra();
        }
    }

    public function gerarJsonDosModulos()
    {
        $retorno = [];

        foreach ($this->modulos as $modulo) {
            $retorno[$modulo['cod_modulo']] = [
                'label' => $modulo['nm_tipo'],
                'etapas' => (int)$modulo['num_etapas']
            ];
        }

        return json_encode($retorno);
    }

    protected function validaModulos()
    {
        $ano = $this->ref_ano;
        $escolaId = $this->ref_ref_cod_escola;
        $etapasCount = count($this->data_inicio);
        $etapasCountAntigo = (int) Portabilis_Utils_Database::selectField(
            'SELECT COUNT(*) AS count FROM pmieducar.ano_letivo_modulo WHERE ref_ano = $1 AND ref_ref_cod_escola = $2',
            [$ano, $escolaId]
        );

        if ($etapasCount >= $etapasCountAntigo) {
            return true;
        }

        $etapasTmp = $etapasCount;
        $etapas = [];

        while ($etapasTmp < $etapasCountAntigo) {
            $etapasTmp += 1;
            $etapas[] = $etapasTmp;
        }

        $counts = [];

        $counts[] = DB::table('modules.falta_componente_curricular as fcc')
            ->join('modules.falta_aluno as fa', 'fa.id',  '=', 'fcc.falta_aluno_id')
            ->join('pmieducar.matricula as m', 'm.cod_matricula', '=', 'fa.matricula_id')
            ->whereIn('fcc.etapa', $etapas)
            ->where('m.ref_ref_cod_escola', $escolaId)
            ->where('m.ano', $ano)
            ->where('m.ativo', 1)
            ->count();

        $counts[] = DB::table('modules.falta_geral as fg')
            ->join('modules.falta_aluno as fa', 'fa.id',  '=', 'fg.falta_aluno_id')
            ->join('pmieducar.matricula as m', 'm.cod_matricula', '=', 'fa.matricula_id')
            ->whereIn('fg.etapa', $etapas)
            ->where('m.ref_ref_cod_escola', $escolaId)
            ->where('m.ano', $ano)
            ->where('m.ativo', 1)
            ->count();

        $counts[] = DB::table('modules.nota_componente_curricular as ncc')
            ->join('modules.nota_aluno as na', 'na.id',  '=', 'ncc.nota_aluno_id')
            ->join('pmieducar.matricula as m', 'm.cod_matricula', '=', 'na.matricula_id')
            ->whereIn('ncc.etapa', $etapas)
            ->where('m.ref_ref_cod_escola', $escolaId)
            ->where('m.ano', $ano)
            ->where('m.ativo', 1)
            ->count();

        $sum = array_sum($counts);

        if ($sum > 0) {
            throw new RuntimeException('Não foi possível remover uma das etapas pois existem notas ou faltas lançadas.');
        }

        // Caso não exista token e URL de integração com o i-Diário, não irá
        // validar se há lançamentos nas etapas removidas

        $checkReleases = config('legacy.config.url_novo_educacao')
            && config('legacy.config.token_novo_educacao');

        if (!$checkReleases) {
            return true;
        }

        $iDiarioService = app(iDiarioService::class);

        foreach ($etapas as $etapa) {
            if ($iDiarioService->getStepActivityByUnit($escolaId, $ano, $etapa)) {
                throw new RuntimeException('Não foi possível remover uma das etapas pois existem notas ou faltas lançadas no diário online.');
            }
        }

        return true;
    }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
    var modulosDisponiveis = <?php echo $miolo->gerarJsonDosModulos(); ?>;
</script>
