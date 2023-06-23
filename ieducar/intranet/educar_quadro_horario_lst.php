<?php

use iEducar\Support\Navigation\Breadcrumb;
use Illuminate\Support\Facades\Auth;

return new class
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $cod_calendario_ano_letivo;

    public $ref_cod_escola;

    public $ref_cod_curso;

    public $ref_cod_serie;

    public $ref_cod_turma;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ano;

    public $data_cadastra;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public $busca;

    public function renderHTML()
    {
        $this->pessoa_logada = Auth::id();

        $retorno = '';

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada) > 7) {
            return $retorno . '
        <table width="100%" height="40%" cellspacing="1" cellpadding="2" border="0" class="tablelistagem">
          <tbody>
            <tr>
              <td colspan="2" valig="center" height="50">
                <center class="formdktd">Usuário sem permissão para acessar esta página</center>
              </td>
            </tr>
          </tbody>
        </table>';
        }

        app(abstract: Breadcrumb::class)->current(currentPage: 'Quadros de horários', pages: [
            url(path: 'intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        $retorno .= '
      <table width="100%" cellspacing="1" cellpadding="2" border="0" class="tablelistagem">
        <tbody>';

        if ($_POST) {
            $this->ref_cod_turma = $_POST['ref_cod_turma'] ?: null;
            $this->ref_cod_serie = $_POST['ref_cod_serie'] ?: null;
            $this->ref_cod_curso = $_POST['ref_cod_curso'] ?: null;
            $this->ref_cod_escola = $_POST['ref_cod_escola'] ?: null;
            $this->ref_cod_instituicao = $_POST['ref_cod_instituicao'] ?: null;
            $this->ano = $_POST['ano'] ?: null;
            $this->busca = $_GET['busca'] ?: null;
        }

        if ($_GET) {
            // Passa todos os valores obtidos no GET para atributos do objeto
            foreach ($_GET as $var => $val) {
                $this->$var = $val === '' ? null : $val;
            }
        }

        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);

        if (!$this->ref_cod_escola) {
            $this->ref_cod_escola = $obj_permissoes->getEscola(int_idpes_usuario: $this->pessoa_logada);
        }

        if (!is_numeric(value: $this->ref_cod_instituicao)) {
            $this->ref_cod_instituicao = $obj_permissoes->getInstituicao(int_idpes_usuario: $this->pessoa_logada);
        }

        // Componente curricular
        $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

        $obrigatorio = false;
        $get_instituicao = true;
        $get_escola = true;
        $get_ano = true;
        $get_curso = true;
        $get_serie = true;
        $get_turma = true;
        include 'educar_quadro_horarios_pesquisas.php';

        if ($this->busca == 'S') {
            if (is_numeric(value: $this->ref_cod_turma)) {
                $obj_turma = new clsPmieducarTurma(cod_turma: $this->ref_cod_turma);
                $det_turma = $obj_turma->detalhe();

                $obj_quadro = new clsPmieducarQuadroHorario(
                    ref_cod_turma: $this->ref_cod_turma,
                    ativo: 1
                );
                $det_quadro = $obj_quadro->detalhe();

                if (is_array(value: $det_quadro)) {
                    $quadro_horario = "<table class='calendar' cellspacing='0' cellpadding='0' border='0'>
                            <tr>
                              <td class='cal_esq_qh' width='40px'><i class='fa fa-calendar' aria-hidden='true'></i></td>
                              <td width='100%' class='mes'>Turma: {$det_turma['nm_turma']}</td>
                              <td align='right' class='cal_dir'>&nbsp;</td>
                              </tr>
                            <tr>
                              <td colspan='3'  align='center'>
                                <table width='100%' cellspacing='2' cellpadding='0'  border='0' >
                                  <tr class='header'>
                                    <td style='width: 100px;'>DOM</td>
                                    <td style='width: 100px;'>SEG</td>
                                    <td style='width: 100px;'>TER</td>
                                    <td style='width: 100px;'>QUA</td>
                                    <td style='width: 100px;'>QUI</td>
                                    <td style='width: 100px;'>SEX</td>
                                    <td style='width: 100px;'>SAB</td>
                                  </tr>";
                    $texto = '<tr>';

                    for ($c = 1; $c <= 7; $c++) {
                        $obj_horarios = new clsPmieducarQuadroHorarioHorarios();
                        $resultado = $obj_horarios->retornaHorario(
                            int_ref_cod_instituicao_servidor: $this->ref_cod_instituicao,
                            int_ref_ref_cod_escola: $this->ref_cod_escola,
                            int_ref_ref_cod_serie: $this->ref_cod_serie,
                            int_ref_ref_cod_turma: $this->ref_cod_turma,
                            int_dia_semana: $c
                        );

                        $texto .= "<td valign=top align='center' width='100' style='cursor: pointer; ' onclick='envia( this, {$this->ref_cod_turma}, {$this->ref_cod_serie}, {$this->ref_cod_curso}, {$this->ref_cod_escola}, {$this->ref_cod_instituicao}, {$det_quadro['cod_quadro_horario']}, {$c}, {$this->ano} )'>";
                        $componente = new stdClass();
                        if (is_array(value: $resultado)) {
                            $resultado = $this->organizarHorariosIguais(valores: $resultado);
                            foreach ($resultado as $registro) {
                                if ($registro['ref_cod_disciplina'] == 0) {
                                    $componente->abreviatura = 'EDUCAÇÃO INFANTIL';
                                } else {
                                    $componente = $componenteMapper->find(pkey: $registro['ref_cod_disciplina']);
                                }

                                // Servidor
                                $obj_servidor = new clsPmieducarServidor();

                                $det_servidor = null;
                                if ($registro['ref_servidor_substituto']) {
                                    $servidor = $obj_servidor->lista(
                                        int_cod_servidor: $registro['ref_servidor_substituto'],
                                        boo_professor: null,
                                        bool_ordena_por_nome: true
                                    );

                                    if (is_array(value: $servidor)) {
                                        $det_servidor = array_shift(array: $servidor);
                                    }
                                } else {
                                    $servidor = $obj_servidor->lista(
                                        int_cod_servidor: $registro['ref_servidor'],
                                        boo_professor: null,
                                        bool_ordena_por_nome: true
                                    );
                                    if (is_array(value: $servidor)) {
                                        $det_servidor = array_shift(array: $servidor);
                                    }
                                }

                                if (is_array(value: $det_servidor)) {
                                    $nomes = explode(separator: ' ', string: $det_servidor['nome']);
                                    $det_servidor['nome'] = array_shift(array: $nomes);
                                }

                                //$texto .= "<div  style='text-align: center;background-color: #F6F6F6;font-size: 11px; width: 100px; margin: 3px; border: 1px solid #CCCCCC; padding:5px; '>". substr($registro['hora_inicial'], 0, 5) . ' - ' . substr($registro['hora_final'], 0, 5) . " <br> {$componente->abreviatura} <br> {$det_servidor["nome"]}</div>";
                                $detalhes = sprintf(
                                    '%s - %s<br />%s<br />%s',
                                    substr(string: $registro['hora_inicial'], offset: 0, length: 5),
                                    substr(string: $registro['hora_final'], offset: 0, length: 5),
                                    $componente->abreviatura,
                                    $det_servidor['nome']
                                );

                                $texto .= sprintf(
                                    '<div class="horario">%s</div>',
                                    $detalhes
                                );
                            }
                        } else {
                            $texto .= '<div  class=\'horario\'><i class=\'fa fa-plus-square\' aria-hidden=\'true\'></i></div>';
                        }

                        $texto .= '</td>';
                    }

                    $texto .= '<tr><td colspan="7">&nbsp;</td></tr>';
                    $quadro_horario .= $texto;

                    $quadro_horario .= '</table></td></tr></table>';
                    $retorno .= "<tr><td colspan='2' ><center><b></b>{$quadro_horario}</center></td></tr>";
                } else {
                    $retorno .= '<tr><td colspan=\'2\' ><b><center>Não existe nenhum quadro de horário cadastrado para esta turma.</center></b></td></tr>';
                }
            }
        }

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 641, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $retorno .= '<tr><td>&nbsp;</td></tr><tr>
            <td align="center" colspan="2">';

            if (!$det_quadro) {
                $retorno .= "<input type=\"button\" value=\"Novo Quadro de Horários\" onclick=\"window.location='educar_quadro_horario_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}'\" class=\"botaolistagem btn-green\"/>";
            } else {
                if ($obj_permissoes->permissao_excluir(int_processo_ap: 641, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $retorno .= "<input type=\"button\" value=\"Excluir Quadro de Horários\" onclick=\"window.location='educar_quadro_horario_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}&ref_cod_quadro_horario={$det_quadro['cod_quadro_horario']}'\" class=\"botaolistagem\"/>";
                }
            }

            $retorno .= '</td>
            </tr>';
        }

        return $retorno . '</tbody>
      </table>';
    }

    public function organizarHorariosIguais($valores)
    {
        $x = 1;
        $quantidadeElementos = count(value: $valores);
        while ($x < $quantidadeElementos) {
            $mesmoHorario = (($valores[0]['hora_inicial'] == $valores[$x]['hora_inicial']) &&
                         ($valores[0]['hora_final'] == $valores[$x]['hora_final']));

            if ($mesmoHorario) {
                unset($valores[$x]);
                $valores[0]['ref_cod_disciplina'] = 0;
            }
            $x++;
        }

        return $valores;
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-quadro-horario-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Quadro de Horário';
        $this->processoAp = '641';
    }
};
