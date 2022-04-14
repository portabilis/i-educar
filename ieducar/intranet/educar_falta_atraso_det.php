<?php

use App\Models\LegacySchool;

return new class extends clsDetalhe {
    public $titulo;

    public $cod_falta_atraso;
    public $ref_cod_escola;
    public $ref_ref_cod_instituicao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_servidor;
    public $tipo;
    public $data_falta_atraso;
    public $qtd_horas;
    public $qtd_min;
    public $justificada;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Falta Atraso - Detalhe';

        $this->ref_cod_servidor        = $_GET['ref_cod_servidor'];
        $this->ref_cod_escola          = $_GET['ref_cod_escola'];
        $this->ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $tmp_obj = new clsPmieducarFaltaAtraso();
        $tmp_obj->setOrderby('data_falta_atraso DESC');
        $this->cod_falta_atraso = $_GET['cod_falta_atraso'];
        $registro = $tmp_obj->lista($this->cod_falta_atraso);

        if (!$registro) {
            $this->simpleRedirect(sprintf(
                'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_ref_cod_instituicao
            ));
        } else {
            $tabela = '<table>
                 <tr align=center>
                     <td bgcolor="#ccdce6"><b>Dia</b></td>
                     <td bgcolor="#ccdce6"><b>Tipo</b></td>
                     <td bgcolor="#ccdce6"><b>Qtd. Horas</b></td>
                     <td bgcolor="#ccdce6"><b>Qtd. Minutos</b></td>
                     <td bgcolor="#ccdce6"><b>Escola</b></td>
                     <td bgcolor="#ccdce6"><b>Instituição</b></td>
                     <td bgcolor="#ccdce6"><b>Matrícula</b></td>
                 </tr>';

            $cont  = 0;
            $total = 0;

            foreach ($registro as $falta) {
                if (($cont % 2) == 0) {
                    $color = ' bgcolor="#f5f9fd" ';
                } else {
                    $color = ' bgcolor="#FFFFFF" ';
                }

                $school = LegacySchool::query()->with('person')->find($falta['ref_cod_escola']);

                $obj_ins = new clsPmieducarInstituicao($falta['ref_ref_cod_instituicao']);
                $det_ins = $obj_ins->detalhe();

                $corpo .= sprintf(
                    '
          <tr>
            <td %s align="left">%s</td>
            <td %s align="left">%s</td>
            <td %s align="right">%s</td>
            <td %s align="right">%s</td>
            <td %s align="left">%s</td>
            <td %s align="left">%s</td>
            <td %s align="left">%s</td>
          </tr>',
                    $color,
                    dataFromPgToBr($falta['data_falta_atraso']),
                    $color,
                    $falta['tipo'] == 1 ? 'Atraso' : 'Falta',
                    $color,
                    $falta['qtd_horas'],
                    $color,
                    $falta['qtd_min'],
                    $color,
                    $school->person->name ?? null,
                    $color,
                    $det_ins['nm_instituicao'],
                    $color,
                    $falta['matricula']
                );

                $cont++;
            }

            $tabela .= $corpo;
            $tabela .= '</table>';

            if ($tabela) {
                $this->addDetalhe(['Faltas/Atrasos', $tabela]);
            }
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            $this->caption_novo = 'Compensar';
            $this->url_novo     = sprintf(
                'educar_falta_atraso_compensado_cad.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_escola,
                $this->ref_ref_cod_instituicao
            );
            $this->url_editar   = sprintf(
                'educar_falta_atraso_cad.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d&cod_falta_atraso=%d',
                $this->ref_cod_servidor,
                $this->ref_cod_escola,
                $this->ref_ref_cod_instituicao,
                $this->cod_falta_atraso
            );
        }

        $this->url_cancelar = sprintf(
            'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_ref_cod_instituicao
        );

        $this->largura = '100%';

        $this->breadcrumb('Detalhe da falta/atraso do servidor', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Falta Atraso';
        $this->processoAp = 635;
    }
};
