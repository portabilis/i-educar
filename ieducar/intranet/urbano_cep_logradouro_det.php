<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/urbano/geral.inc.php';
require_once 'include/public/clsPublicBairro.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Cep Logradouro");
        $this->processoAp = 758;
    }
}

class indice extends clsDetalhe
{
    public $cep;
    public $idlog;
    public $idpes_rev;
    public $data_rev;
    public $origem_gravacao;
    public $idpes_cad;
    public $data_cad;
    public $operacao;

    public function Gerar()
    {
        $this->titulo = 'Cep Logradouro - Detalhe';

        $this->idlog = $_GET['idlog'];

        $obj_cep_logradouro = new clsUrbanoCepLogradouro();
        $lst_cep_logradouro = $obj_cep_logradouro->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $this->idlog);

        if (!$lst_cep_logradouro) {
            $this->simpleRedirect('urbano_cep_logradouro_lst.php');
        } else {
            $registro = $lst_cep_logradouro[0];
        }

        if ($registro['nm_pais']) {
            $this->addDetalhe(['Pais', "{$registro['nm_pais']}"]);
        }
        if ($registro['nm_estado']) {
            $this->addDetalhe(['Estado', "{$registro['nm_estado']}"]);
        }
        if ($registro['nm_municipio']) {
            $this->addDetalhe(['Município', "{$registro['nm_municipio']}"]);
        }
        if ($registro['nm_logradouro']) {
            $this->addDetalhe(['Logradouro', "{$registro['nm_logradouro']}"]);
        }

        $obj_cep_log_bairro = new clsUrbanoCepLogradouroBairro();
        $lst_cep_log_bairro = $obj_cep_log_bairro->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, $this->idlog);

        if ($lst_cep_log_bairro) {
            $tab_endereco = '<TABLE>
                           <TR align=center>
                               <TD bgcolor=#ccdce6><B>CEP</B></TD>
                               <TD bgcolor=#ccdce6><B>Bairro</B></TD>
                           </TR>';

            $cont = 0;

            foreach ($lst_cep_log_bairro as $endereco) {
                if (($cont % 2) == 0) {
                    $color = ' bgcolor=#f5f9fd ';
                } else {
                    $color = ' bgcolor=#FFFFFF ';
                }

                $obj_bairro = new clsPublicBairro(null, null, $endereco['idbai']);
                $det_bairro = $obj_bairro->detalhe();

                $endereco['cep'] = int2CEP($endereco['cep']);

                $tab_endereco .= "<TR>
                                    <TD {$color} align=center>{$endereco['cep']}</TD>
                                    <TD {$color} align=center>{$det_bairro['nome']}</TD>
                                </TR>";
                $cont++;
            }
            $tab_endereco .= '</TABLE>';
        }
        if ($tab_endereco) {
            $this->addDetalhe(['Tabela de CEP-Bairro', "{$tab_endereco}"]);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(758, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'urbano_cep_logradouro_cad.php';
            $this->url_editar = "urbano_cep_logradouro_cad.php?idlog={$registro['idlog']}";
        }

        $this->url_cancelar = 'urbano_cep_logradouro_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do CEP', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
