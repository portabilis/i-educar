<?php

use App\Models\City;
use App\Models\Country;
use App\Models\PersonHasPlace;
use App\Services\UrlPresigner;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesFichaMedicaAluno.inc.php';
require_once 'include/modules/clsModulesMoradiaAluno.inc.php';
require_once 'App/Model/ZonaLocalizacao.php';
require_once 'Educacenso/Model/AlunoDataMapper.php';
require_once 'Transporte/Model/AlunoDataMapper.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Utils/CustomLabel.php';
require_once 'lib/Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Aluno');
        $this->processoAp = 578;
    }
}

class indice extends clsDetalhe
{
    public $titulo;
    public $cod_aluno;
    public $ref_idpes_responsavel;
    public $idpes_pai;
    public $idpes_mae;
    public $ref_cod_pessoa_educ;
    public $ref_cod_aluno_beneficio;
    public $ref_cod_religiao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_idpes;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nm_pai;
    public $nm_mae;
    public $ref_cod_raca;
    public $sus;
    public $url_laudo_medico;
    public $url_documento;
    private $urlPresigner;

    public function Gerar()
    {
        Session::forget(['reload_faixa_etaria', 'reload_reserva_vaga']);

        // Verificação de permissão para cadastro.
        $this->obj_permissao = new clsPermissoes();

        $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);
        $this->titulo = 'Aluno - Detalhe';
        $this->cod_aluno = $this->getQueryString('cod_aluno');
        $tmp_obj = new clsPmieducarAluno($this->cod_aluno);
        $registro = $tmp_obj->detalhe();

        if (empty($registro)) {
            throw new HttpResponseException(
                new RedirectResponse(
                    URL::to('intranet/educar_aluno_lst.php')
                )
            );
        }

        foreach ($registro as $key => $value) {
            $this->$key = $value;
        }

        if ($this->ref_idpes) {
            $obj_pessoa_fj = new clsPessoaFj($this->ref_idpes);
            $det_pessoa_fj = $obj_pessoa_fj->detalhe();

            $obj_fisica = new clsFisica($this->ref_idpes);
            $det_fisica = $obj_fisica->detalhe();

            $obj_fisica_raca = new clsCadastroFisicaRaca();
            $lst_fisica_raca = $obj_fisica_raca->lista($this->ref_idpes);

            if ($lst_fisica_raca) {
                $det_fisica_raca = array_shift($lst_fisica_raca);
                $obj_raca = new clsCadastroRaca($det_fisica_raca['ref_cod_raca']);
                $det_raca = $obj_raca->detalhe();
            }

            $objFoto = new clsCadastroFisicaFoto($this->ref_idpes);
            $detalheFoto = $objFoto->detalhe();

            if ($detalheFoto) {
                $caminhoFoto = $detalheFoto['caminho'];
            }

            $registro['nome_aluno'] = strtoupper($det_pessoa_fj['nome']);
            $registro['cpf'] = int2IdFederal($det_fisica['cpf']);
            $registro['data_nasc'] = Portabilis_Date_Utils::pgSQLToBr($det_fisica['data_nasc']);

            $opcoes = [
                'F' => 'Feminino',
                'M' => 'Masculino',
            ];

            $registro['sexo'] = $det_fisica['sexo'] ? $opcoes[$det_fisica['sexo']] : '';

            $obj_estado_civil = new clsEstadoCivil();
            $obj_estado_civil_lista = $obj_estado_civil->lista();

            $lista_estado_civil = [];

            if ($obj_estado_civil_lista) {
                foreach ($obj_estado_civil_lista as $estado_civil) {
                    $lista_estado_civil[$estado_civil['ideciv']] = $estado_civil['descricao'];
                }
            }

            $registro['ideciv'] = $lista_estado_civil[$det_fisica['ideciv']->ideciv];
            $registro['email'] = $det_pessoa_fj['email'];
            $registro['url'] = $det_pessoa_fj['url'];

            $registro['nacionalidade'] = $det_fisica['nacionalidade'];
            $registro['nis_pis_pasep'] = int2Nis($det_fisica['nis_pis_pasep']);

            $registro['naturalidade'] = City::getNameById($det_fisica['idmun_nascimento']);

            $countryName = Country::query()->find($det_fisica['idpais_estrangeiro']);
            $registro['pais_origem'] = $countryName->name;

            $registro['ref_idpes_responsavel'] = $det_fisica['idpes_responsavel'];

            $this->idpes_pai = $det_fisica['idpes_pai'];
            $this->idpes_mae = $det_fisica['idpes_mae'];

            $this->sus = $det_fisica['sus'];

            $this->nm_pai = $registro['nm_pai'];
            $this->nm_mae = $registro['nm_mae'];

            if ($this->idpes_pai) {
                $obj_pessoa_pai = new clsPessoaFj($this->idpes_pai);
                $det_pessoa_pai = $obj_pessoa_pai->detalhe();

                if ($det_pessoa_pai) {
                    $registro['nm_pai'] = $det_pessoa_pai['nome'];

                    // CPF
                    $obj_cpf = new clsFisica($this->idpes_pai);
                    $det_cpf = $obj_cpf->detalhe();

                    if ($det_cpf['cpf']) {
                        $this->cpf_pai = int2CPF($det_cpf['cpf']);
                    }
                }
            }

            if ($this->idpes_mae) {
                $obj_pessoa_mae = new clsPessoaFj($this->idpes_mae);
                $det_pessoa_mae = $obj_pessoa_mae->detalhe();

                if ($det_pessoa_mae) {
                    $registro['nm_mae'] = $det_pessoa_mae['nome'];

                    // CPF
                    $obj_cpf = new clsFisica($this->idpes_mae);
                    $det_cpf = $obj_cpf->detalhe();

                    if ($det_cpf['cpf']) {
                        $this->cpf_mae = int2CPF($det_cpf['cpf']);
                    }
                }
            }

            $registro['ddd_fone_1'] = $det_pessoa_fj['ddd_1'];
            $registro['fone_1'] = $det_pessoa_fj['fone_1'];

            $registro['ddd_fone_2'] = $det_pessoa_fj['ddd_2'];
            $registro['fone_2'] = $det_pessoa_fj['fone_2'];

            $registro['ddd_fax'] = $det_pessoa_fj['ddd_fax'] ?? null;
            $registro['fone_fax'] = $det_pessoa_fj['fone_fax'] ?? null;

            $registro['ddd_mov'] = $det_pessoa_fj['ddd_mov'] ?? null;
            $registro['fone_mov'] = $det_pessoa_fj['fone_mov'] ?? null;

            $obj_deficiencia_pessoa = new clsCadastroFisicaDeficiencia();
            $obj_deficiencia_pessoa_lista = $obj_deficiencia_pessoa->lista($this->ref_idpes);

            $obj_beneficios = new clsPmieducarAlunoBeneficio();
            $obj_beneficios_lista = $obj_beneficios->lista(null, null, null, null, null, null, null, null, null, null, $this->cod_aluno);

            if ($obj_deficiencia_pessoa_lista) {
                $deficiencia_pessoa = [];

                foreach ($obj_deficiencia_pessoa_lista as $deficiencia) {
                    $obj_def = new clsCadastroDeficiencia($deficiencia['ref_cod_deficiencia']);
                    $det_def = $obj_def->detalhe();

                    $deficiencia_pessoa[$deficiencia['ref_cod_deficiencia']] = $det_def['nm_deficiencia'];
                }
            }

            $ObjDocumento = new clsDocumento($this->ref_idpes);
            $detalheDocumento = $ObjDocumento->detalhe();

            $registro['rg'] = $detalheDocumento['rg'];

            if ($detalheDocumento['data_exp_rg']) {
                $registro['data_exp_rg'] = date(
                    'd/m/Y',
                    strtotime(substr($detalheDocumento['data_exp_rg'], 0, 19))
                );
            }

            $registro['sigla_uf_exp_rg'] = $detalheDocumento['sigla_uf_exp_rg'];
            $registro['tipo_cert_civil'] = $detalheDocumento['tipo_cert_civil'];
            $registro['certidao_nascimento'] = $detalheDocumento['certidao_nascimento'];
            $registro['certidao_casamento'] = $detalheDocumento['certidao_casamento'];
            $registro['num_termo'] = $detalheDocumento['num_termo'];
            $registro['num_livro'] = $detalheDocumento['num_livro'];
            $registro['num_folha'] = $detalheDocumento['num_folha'];

            if ($detalheDocumento['data_emissao_cert_civil']) {
                $registro['data_emissao_cert_civil'] = date(
                    'd/m/Y',
                    strtotime(substr($detalheDocumento['data_emissao_cert_civil'], 0, 19))
                );
            }

            $registro['sigla_uf_cert_civil'] = $detalheDocumento['sigla_uf_cert_civil'];
            $registro['cartorio_cert_civil'] = $detalheDocumento['cartorio_cert_civil'];
            $registro['num_cart_trabalho'] = $detalheDocumento['num_cart_trabalho'];
            $registro['serie_cart_trabalho'] = $detalheDocumento['serie_cart_trabalho'];

            if ($detalheDocumento['data_emissao_cart_trabalho']) {
                $registro['data_emissao_cart_trabalho'] = date(
                    'd/m/Y',
                    strtotime(substr($detalheDocumento['data_emissao_cart_trabalho'], 0, 19))
                );
            }

            $registro['sigla_uf_cart_trabalho'] = $detalheDocumento['sigla_uf_cart_trabalho'];
            $registro['num_tit_eleitor'] = $detalheDocumento['num_titulo_eleitor'] ?? null;
            $registro['zona_tit_eleitor'] = $detalheDocumento['zona_titulo_eleitor'] ?? null;
            $registro['secao_tit_eleitor'] = $detalheDocumento['secao_titulo_eleitor'] ?? null;
            $registro['idorg_exp_rg'] = $detalheDocumento['ref_idorg_rg'] ?? null;

            $place = PersonHasPlace::query()
                ->with('place.city.state')
                ->where('person_id', $this->ref_idpes)
                ->orderBy('type')
                ->first();
        }

        if ($registro['cod_aluno']) {
            $this->addDetalhe([_cl('aluno.detalhe.codigo_aluno'), $registro['cod_aluno']]);
        }

        // código inep
        $alunoMapper = new Educacenso_Model_AlunoDataMapper();
        $alunoInep = null;

        try {
            $alunoInep = $alunoMapper->find(['aluno' => $this->cod_aluno]);

            $configuracoes = new clsPmieducarConfiguracoesGerais();
            $configuracoes = $configuracoes->detalhe();

            if ($configuracoes['mostrar_codigo_inep_aluno']) {
                $this->addDetalhe(['Código inep', $alunoInep->alunoInep]);
            }
        } catch (Exception $e) {
        }

        // código estado
        $this->addDetalhe([_cl('aluno.detalhe.codigo_estado'), $registro['aluno_estado_id']]);

        if ($registro['caminho_foto']) {
            $this->addDetalhe([
                'Foto',
                sprintf(
                    '<img src="arquivos/educar/aluno/small/%s" border="0">',
                    $this->urlPresigner()->getPresignedUrl($registro['caminho_foto'])
                )
            ]);
        }

        if ($registro['nome_aluno']) {
            if ($caminhoFoto != null and $caminhoFoto != '') {
                $url = $this->urlPresigner()->getPresignedUrl($caminhoFoto);

                $this->addDetalhe([
                    'Nome Aluno',
                    $registro['nome_aluno'] . '<p><img id="student-picture" height="117" src="' . $url . '"/></p>'
                        . '<div><a class="rotate-picture" data-angle="90" href="javascript:void(0)"><i class="fa fa-rotate-left"></i> Girar para esquerda</a></div>'
                        . '<div><a class="rotate-picture" data-angle="-90" href="javascript:void(0)"><i class="fa fa-rotate-right"></i> Girar para direita</a></div>'
                ]);
            } else {
                $this->addDetalhe(['Nome Aluno', $registro['nome_aluno']]);
            }
        }

        if ($det_fisica['nome_social']) {
            $this->addDetalhe(['Nome Social', strtoupper($det_fisica['nome_social'])]);
        }

        if (idFederal2int($registro['cpf'])) {
            $this->addDetalhe(['CPF', $registro['cpf']]);
        }

        if ($registro['data_nasc']) {
            $this->addDetalhe(['Data de Nascimento', $registro['data_nasc']]);
        }

        /**
         * Analfabeto.
         */
        $this->addDetalhe(['Analfabeto', $registro['analfabeto'] == 0 ? 'Não' : 'Sim']);

        if ($registro['sexo']) {
            $this->addDetalhe(['Sexo', $registro['sexo']]);
        }

        if ($registro['ideciv']) {
            $this->addDetalhe(['Estado Civil', $registro['ideciv']]);
        }

        if (isset($place)) {
            $place = $place->place;

            $this->addDetalhe(['Logradouro', $place->address]);
            $this->addDetalhe(['Número', $place->number]);
            $this->addDetalhe(['Complemento', $place->complement]);
            $this->addDetalhe(['Bairro', $place->neighborhood]);
            $this->addDetalhe(['Cidade', $place->city->name]);
            $this->addDetalhe(['UF', $place->city->state->abbreviation]);
            $this->addDetalhe(['CEP', int2CEP($place->postal_code)]);
        }

        if ($registro['naturalidade']) {
            $this->addDetalhe(['Naturalidade', $registro['naturalidade']]);
        }

        if ($registro['nacionalidade']) {
            $lista_nacionalidade = [
                'NULL' => 'Selecione',
                1 => 'Brasileiro',
                2 => 'Naturalizado Brasileiro',
                3 => 'Estrangeiro'
            ];

            $registro['nacionalidade'] = $lista_nacionalidade[$registro['nacionalidade']];
            $this->addDetalhe(['Nacionalidade', $registro['nacionalidade']]);
        }

        if ($registro['pais_origem']) {
            $this->addDetalhe(['País de Origem', $registro['pais_origem']]);
        }

        $responsavel = $tmp_obj->getResponsavelAluno();

        if ($responsavel && is_null($registro['ref_idpes_responsavel'])) {
            $this->addDetalhe(['Nome do Responsável', $responsavel['nome_responsavel']]);
        }

        if ($registro['ref_idpes_responsavel']) {
            $obj_pessoa_resp = new clsPessoaFj($registro['ref_idpes_responsavel']);
            $det_pessoa_resp = $obj_pessoa_resp->detalhe();

            if ($det_pessoa_resp) {
                $registro['ref_idpes_responsavel'] = $det_pessoa_resp['nome'];
            }

            $this->addDetalhe(['Responsável', $registro['ref_idpes_responsavel']]);
        }

        if ($registro['nm_pai']) {
            $this->addDetalhe(['Pai', $registro['nm_pai']]);
        }

        if ($registro['nm_mae']) {
            $this->addDetalhe(['Mãe', $registro['nm_mae']]);
        }

        if ($registro['fone_1']) {
            if ($registro['ddd_fone_1']) {
                $registro['ddd_fone_1'] = sprintf('(%s)&nbsp;', $registro['ddd_fone_1']);
            }

            $this->addDetalhe(['Telefone 1', $registro['ddd_fone_1'] . $registro['fone_1']]);
        }

        if ($registro['fone_2']) {
            if ($registro['ddd_fone_2']) {
                $registro['ddd_fone_2'] = sprintf('(%s)&nbsp;', $registro['ddd_fone_2']);
            }

            $this->addDetalhe(['Telefone 2', $registro['ddd_fone_2'] . $registro['fone_2']]);
        }

        if ($registro['fone_mov']) {
            if ($registro['ddd_mov']) {
                $registro['ddd_mov'] = sprintf('(%s)&nbsp;', $registro['ddd_mov']);
            }

            $this->addDetalhe(['Celular', $registro['ddd_mov'] . $registro['fone_mov']]);
        }

        if ($registro['fone_fax']) {
            if ($registro['ddd_fax']) {
                $registro['ddd_fax'] = sprintf('(%s)&nbsp;', $registro['ddd_fax']);
            }

            $this->addDetalhe(['Fax', $registro['ddd_fax'] . $registro['fone_fax']]);
        }

        if ($registro['email']) {
            $this->addDetalhe(['E-mail', $registro['email']]);
        }

        if ($registro['url']) {
            $this->addDetalhe(['Página Pessoal', $registro['url']]);
        }

        if ($registro['ref_cod_religiao']) {
            $obj_religiao = new clsPmieducarReligiao($registro['ref_cod_religiao']);
            $obj_religiao_det = $obj_religiao->detalhe();

            $this->addDetalhe(['Religião', $obj_religiao_det['nm_religiao']]);
        }

        if ($det_raca['nm_raca']) {
            $this->addDetalhe(['Raça', $det_raca['nm_raca']]);
        }

        if ($obj_beneficios_lista) {
            $tabela = '<table border="0" width="300" cellpadding="3"><tr bgcolor="#ccdce6" align="center"><td>Benefícios</td></tr>';
            $cor = '#D1DADF';

            foreach ($obj_beneficios_lista as $reg) {
                $cor = $cor == '#D1DADF' ? '#f5f9fd' : '#D1DADF';

                $tabela .= sprintf(
                    '<tr bgcolor="%s" align="center"><td>%s</td></tr>',
                    $cor,
                    $reg['nm_beneficio']
                );
            }

            $tabela .= '</table>';

            $this->addDetalhe(['Benefícios', $tabela]);
        }

        if ($deficiencia_pessoa) {
            $tabela = '<table border="0" width="300" cellpadding="3"><tr bgcolor="#ccdce6" align="center"><td>Deficiências</td></tr>';
            $cor = '#D1DADF';

            foreach ($deficiencia_pessoa as $indice => $valor) {
                $cor = $cor == '#D1DADF' ? '#f5f9fd' : '#D1DADF';

                $tabela .= sprintf(
                    '<tr bgcolor="%s" align="center"><td>%s</td></tr>',
                    $cor,
                    $valor
                );
            }

            $tabela .= '</table>';

            $this->addDetalhe(['Deficiências', $tabela]);
        }

        if ($registro['url_documento'] && $registro['url_documento'] != '') {
            $tabela = '<table border="0" width="300" cellpadding="3"><tr bgcolor="#ccdce6" align="center"><td>Documentos</td></tr>';
            $cor = '#e9f0f8';

            $arrayDocumentos = json_decode($registro['url_documento']);
            foreach ($arrayDocumentos as $key => $documento) {
                $cor = $cor == '#e9f0f8' ? '#f5f9fd' : '#e9f0f8';

                $tabela .= '<tr bgcolor=\'' . $cor . '\'
                        align=\'center\'>
                          <td>
                            <a href=\'' . $this->urlPresigner()->getPresignedUrl($documento->url) . '\'
                               target=\'_blank\' > Visualizar documento ' . (count($documento) > 1 ? ($key + 1) : '') . '
                            </a>
                          </td>
                    </tr>';
            }

            $tabela .= '</table>';
            $this->addDetalhe(['Documentos do aluno', $tabela]);
        }

        if ($registro['url_laudo_medico'] && $registro['url_laudo_medico'] != '') {
            $tabela = '<table border="0" width="300" cellpadding="3"><tr bgcolor="#ccdce6" align="center"><td>Laudo médico</td></tr>';

            $cor = '#D1DADF';

            $arrayLaudoMedico = json_decode($registro['url_laudo_medico']);
            foreach ($arrayLaudoMedico as $key => $laudoMedico) {
                $cor = $cor == '#D1DADF' ? '#f5f9fd' : '#D1DADF';
                $laudoMedicoUrl = $this->urlPresigner()->getPresignedUrl($laudoMedico->url);
                $tabela .= "<tr bgcolor='{$cor}' align='center'><td><a href='{$laudoMedicoUrl}' target='_blank' > Visualizar laudo " . (count($arrayLaudoMedico) > 1 ? ($key + 1) : '') . ' </a></td></tr>';
            }

            $tabela .= '</table>';
            $this->addDetalhe(['Laudo médico do aluno', $tabela]);
        }

        if ($registro['rg']) {
            $this->addDetalhe(['RG', $registro['rg']]);
        }

        if ($registro['data_exp_rg']) {
            $this->addDetalhe(['Data de Expedição RG', $registro['data_exp_rg']]);
        }

        if ($registro['idorg_exp_rg']) {
            $this->addDetalhe(['Órgão Expedição RG', $registro['idorg_exp_rg']]);
        }

        if ($registro['sigla_uf_exp_rg']) {
            $this->addDetalhe(['Estado Expedidor', $registro['sigla_uf_exp_rg']]);
        }

        /**
         * @todo CoreExt_Enum?
         */
        if (!$registro['tipo_cert_civil'] && $registro['certidao_nascimento']) {
            $this->addDetalhe(['Tipo Certidão Civil', 'Nascimento (novo formato)']);
            $this->addDetalhe(['Número Certidão Civil', $registro['certidao_nascimento']]);
        } else {
            if (!$registro['tipo_cert_civil'] && $registro['certidao_casamento']) {
                $this->addDetalhe(['Tipo Certidão Civil', 'Casamento (novo formato)']);
                $this->addDetalhe(['Número Certidão Civil', $registro['certidao_casamento']]);
            } else {
                $lista_tipo_cert_civil = [];
                $lista_tipo_cert_civil['0'] = 'Selecione';
                $lista_tipo_cert_civil[91] = 'Nascimento (antigo formato)';
                $lista_tipo_cert_civil[92] = 'Casamento (antigo formato)';

                $this->addDetalhe(['Tipo Certidão Civil', $lista_tipo_cert_civil[$registro['tipo_cert_civil']]]);

                if ($registro['num_termo']) {
                    $this->addDetalhe(['Termo', $registro['num_termo']]);
                }

                if ($registro['num_livro']) {
                    $this->addDetalhe(['Livro', $registro['num_livro']]);
                }

                if ($registro['num_folha']) {
                    $this->addDetalhe(['Folha', $registro['num_folha']]);
                }
            }
        }

        if ($registro['data_emissao_cert_civil']) {
            $this->addDetalhe(['Emissão Certidão Civil', $registro['data_emissao_cert_civil']]);
        }

        if ($registro['sigla_uf_cert_civil']) {
            $this->addDetalhe(['Sigla Certidão Civil', $registro['sigla_uf_cert_civil']]);
        }

        if ($registro['cartorio_cert_civil']) {
            $this->addDetalhe(['Cartório', $registro['cartorio_cert_civil']]);
        }

        if ($registro['num_tit_eleitor']) {
            $this->addDetalhe(['Título de Eleitor', $registro['num_tit_eleitor']]);
        }

        if ($registro['zona_tit_eleitor']) {
            $this->addDetalhe(['Zona', $registro['zona_tit_eleitor']]);
        }

        if ($registro['secao_tit_eleitor']) {
            $this->addDetalhe(['Seção', $registro['secao_tit_eleitor']]);
        }

        // Transporte escolar.
        $transporteMapper = new Transporte_Model_AlunoDataMapper();
        $transporteAluno = null;
        try {
            $transporteAluno = $transporteMapper->find(['aluno' => $this->cod_aluno]);
        } catch (Exception $e) {
        }

        $this->addDetalhe([
            'Transporte escolar',
            isset($transporteAluno) && $transporteAluno->responsavel != 'Não utiliza' ? 'Sim' : 'Não utiliza'
        ]);
        if ($transporteAluno && $transporteAluno->responsavel != 'Não utiliza') {
            $this->addDetalhe(['Responsável transporte', $transporteAluno->responsavel]);
        }

        if ($registro['nis_pis_pasep']) {
            $this->addDetalhe(['NIS', $registro['nis_pis_pasep']]);
        }

        // Verifica se o usuário tem permissão para cadastrar um aluno.
        // O sistema irá validar o cadastro de permissões e o parâmetro
        // "bloquear_cadastro_aluno" da instituição.

        if ($this->obj_permissao->permissao_cadastra(578, $this->pessoa_logada, 7)) {

            $bloquearCadastroAluno = dbBool($configuracoes['bloquear_cadastro_aluno']);

            if ($bloquearCadastroAluno == false) {
                $this->url_novo = '/module/Cadastro/aluno';
            }

            $this->url_editar = '/module/Cadastro/aluno?id=' . $registro['cod_aluno'];

            $this->array_botao = ['Nova matrícula', 'Atualizar histórico', 'Distribuição de uniforme'];
            $this->array_botao_url_script = [
                sprintf('go("educar_matricula_cad.php?ref_cod_aluno=%d");', $registro['cod_aluno']),
                sprintf('go("educar_historico_escolar_lst.php?ref_cod_aluno=%d");', $registro['cod_aluno']),
                sprintf('go("educar_distribuicao_uniforme_lst.php?ref_cod_aluno=%d");', $registro['cod_aluno'])
            ];
        }

        $objFichaMedica = new clsModulesFichaMedicaAluno($this->cod_aluno);
        $reg = $objFichaMedica->detalhe();

        if ($reg) {
            $this->addDetalhe(['<span id="fmedica"></span>Altura/metro', $reg['altura']]);
            if (trim($reg['peso']) != '') {
                $this->addDetalhe(['Peso/kg', $reg['peso']]);
            }

            if (trim($reg['grupo_sanguineo']) != '') {
                $this->addDetalhe(['Grupo sanguíneo', $reg['grupo_sanguineo']]);
            }

            if (trim($reg['fator_rh']) != '') {
                $this->addDetalhe(['Fator RH', $reg['fator_rh']]);
            }

            if (trim($this->sus) != '') {
                $this->addDetalhe(['Número do cartão do SUS', $this->sus]);
            }

            $this->addDetalhe([
                'Possui alergia a algum medicamento',
                ($reg['alergia_medicamento'] == 'S' ? 'Sim' : 'Não')
            ]);

            if (trim($reg['desc_alergia_medicamento']) != '') {
                $this->addDetalhe(['Quais', $reg['desc_alergia_medicamento']]);
            }

            $this->addDetalhe([
                'Possui alergia a algum alimento',
                ($reg['alergia_alimento'] == 'S' ? 'Sim' : 'Não')
            ]);

            if (trim($reg['desc_alergia_alimento']) != '') {
                $this->addDetalhe(['Quais', $reg['desc_alergia_alimento']]);
            }

            $this->addDetalhe([
                'Possui alguma doenca congênita',
                ($reg['doenca_congenita'] == 'S' ? 'Sim' : 'Não')
            ]);

            if (trim($reg['desc_doenca_congenita']) != '') {
                $this->addDetalhe(['Quais', $reg['desc_doenca_congenita']]);
            }

            $this->addDetalhe(['É fumante', ($reg['fumante'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Já contraiu caxumba', ($reg['doenca_caxumba'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Já contraiu sarampo', ($reg['doenca_sarampo'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Já contraiu rubeola', ($reg['doenca_rubeola'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Já contraiu catapora', ($reg['doenca_catapora'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Já contraiu escarlatina', ($reg['doenca_escarlatina'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Já contraiu coqueluche', ($reg['doenca_coqueluche'] == 'S' ? 'Sim' : 'Não')]);

            if (trim($reg['doenca_outras']) != '') {
                $this->addDetalhe(['Outras doenças que o aluno já contraiu', $reg['doenca_outras']]);
            }

            $this->addDetalhe(['Epilético', ($reg['epiletico'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Está em tratamento', ($reg['epiletico_tratamento'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Hemofílico', ($reg['hemofilico'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Hipertenso', ($reg['hipertenso'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Asmático', ($reg['asmatico'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Diabético', ($reg['diabetico'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Depende de insulina', ($reg['insulina'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(['Faz tratamento médico', ($reg['tratamento_medico'] == 'S' ? 'Sim' : 'Não')]);

            if (trim($reg['desc_tratamento_medico']) != '') {
                $this->addDetalhe(['Qual', $reg['desc_tratamento_medico']]);
            }

            $this->addDetalhe([
                'Ingere medicação específica',
                ($reg['medicacao_especifica'] == 'S' ? 'Sim' : 'Não')
            ]);

            if (trim($reg['desc_medicacao_especifica']) != '') {
                $this->addDetalhe(['Qual', $reg['desc_medicacao_especifica']]);
            }

            $this->addDetalhe([
                'Acompanhamento médico ou psicológico',
                ($reg['acomp_medico_psicologico'] == 'S' ? 'Sim' : 'Não')
            ]);

            if (trim($reg['desc_acomp_medico_psicologico']) != '') {
                $this->addDetalhe(['Motivo', $reg['desc_acomp_medico_psicologico']]);
            }

            $this->addDetalhe([
                'Restrição para atividades físicas',
                ($reg['restricao_atividade_fisica'] == 'S' ? 'Sim' : 'Não')
            ]);

            if (trim($reg['desc_restricao_atividade_fisica']) != '') {
                $this->addDetalhe(['Qual', $reg['desc_restricao_atividade_fisica']]);
            }

            $this->addDetalhe(['Teve alguma fratura ou trauma', ($reg['fratura_trauma'] == 'S' ? 'Sim' : 'Não')]);

            if (trim($reg['desc_fratura_trauma']) != '') {
                $this->addDetalhe(['Qual', $reg['desc_fratura_trauma']]);
            }

            $this->addDetalhe(['Tem plano de saúde', ($reg['plano_saude'] == 'S' ? 'Sim' : 'Não')]);

            if (trim($reg['desc_plano_saude']) != '') {
                $this->addDetalhe(['Qual', $reg['desc_plano_saude']]);
            }

            $this->addDetalhe(['<span id="tr_tit_dados_hospital">Em caso de emergência, levar para hospital ou clínica</span>']);
            $this->addDetalhe(['Nome', $reg['hospital_clinica']]);
            $this->addDetalhe(['Endereço', $reg['hospital_clinica_endereco']]);
            $this->addDetalhe(['Telefone', $reg['hospital_clinica_telefone']]);
            $this->addDetalhe(['<span id="tr_tit_dados_hospital">Em caso de emergência, se não for possível contatar os responsáveis, comunicar</span>']);
            $this->addDetalhe(['Nome', $reg['responsavel_nome']]);
            $this->addDetalhe(['Parentesco', $reg['responsavel_parentesco']]);
            $this->addDetalhe(['Telefone', $reg['responsavel_parentesco_telefone']]);
            $this->addDetalhe(['Celular', $reg['responsavel_parentesco_celular']]);
        }

        $objDistribuicaoUniforme = new clsPmieducarDistribuicaoUniforme(null, $this->cod_aluno, date('Y'));
        $reg = $objDistribuicaoUniforme->detalhePorAlunoAno();

        if ($reg) {
            if (dbBool($reg['kit_completo'])) {
                $this->addDetalhe(['<span id=\'funiforme\'></span>Recebeu kit completo', 'Sim']);
                $this->addDetalhe([
                    '<span id=\'ffuniforme\'></span>' . 'Data da distribuição',
                    Portabilis_Date_Utils::pgSQLToBr($reg['data'])
                ]);
            } else {
                $this->addDetalhe([
                    '<span id=\'funiforme\'></span>Recebeu kit completo',
                    'Não'
                ]);
                $this->addDetalhe([
                    'Data da distribuição',
                    Portabilis_Date_Utils::pgSQLToBr($reg['data'])
                ]);
                $this->addDetalhe([
                    'Quantidade de agasalhos (jaqueta e calça)',
                    $reg['agasalho_qtd'] ?: '0'
                ]);
                $this->addDetalhe(['Quantidade de camisetas (manga curta)', $reg['camiseta_curta_qtd'] ?: '0']);
                $this->addDetalhe(['Quantidade de camisetas (manga longa)', $reg['camiseta_longa_qtd'] ?: '0']);
                $this->addDetalhe(['Quantidade de meias', $reg['meias_qtd'] ?: '0']);
                $this->addDetalhe(['Bermudas tectels (masculino)', $reg['bermudas_tectels_qtd'] ?: '0']);
                $this->addDetalhe(['Bermudas coton (feminino)', $reg['bermudas_coton_qtd'] ?: '0']);
                $this->addDetalhe([
                    '<span id=\'ffuniforme\'></span>' . 'Quantidade de tênis',
                    $reg['tenis_qtd'] ?: '0'
                ]);
            }
        }

        $objMoradia = new clsModulesMoradiaAluno($this->cod_aluno);
        $reg = $objMoradia->detalhe();

        if ($reg) {
            $moradia = '';
            switch ($reg['moradia']) {
                case 'A':
                    $moradia = 'Apartamento';
                    break;
                case 'C':
                    $moradia = 'Casa';
                    switch ($reg['material']) {
                        case 'A':
                            $moradia .= ' de alvenaria';
                            break;
                        case 'M':
                            $moradia .= ' de madeira';
                            break;
                        case 'I':
                            $moradia .= ' mista';
                            break;
                    }
                    break;
                case 'O':
                    $moradia = 'Outra: ' . $reg['casa_outra'];
                    break;
                default:
                    $moradia = 'Não informado';
            }

            $this->addDetalhe(['<span id="fmoradia"></span>Moradia', $moradia]);
            $situacao;

            switch ($reg['moradia_situacao']) {
                case 1:
                    $situacao = 'Alugado';
                    break;
                case 2:
                    $situacao = 'Próprio';
                    break;
                case 3:
                    $situacao = 'Cedido';
                    break;
                case 4:
                    $situacao = 'Financiado';
                    break;
                case 5:
                    $situacao = 'Outra';
                    break;
            }

            $this->addDetalhe(['Situação', $situacao]);
            $this->addDetalhe(['Quantidade de quartos', $reg['quartos']]);
            $this->addDetalhe(['Quantidade de salas', $reg['sala']]);
            $this->addDetalhe(['Quantidade de copas', $reg['copa']]);
            $this->addDetalhe(['Quantidade de banheiros', $reg['banheiro']]);
            $this->addDetalhe(['Quantidade de garagens', $reg['garagem']]);
            $this->addDetalhe(['Possui empregada doméstica', $reg['empregada_domestica']]);
            $this->addDetalhe(['Possui automóvel', $reg['automovel']]);
            $this->addDetalhe(['Possui motocicleta', $reg['motocicleta']]);
            $this->addDetalhe(['Possui geladeira', $reg['geladeira']]);
            $this->addDetalhe(['Possui fogão', $reg['fogao']]);
            $this->addDetalhe(['Possui máquina de lavar', $reg['maquina_lavar']]);
            $this->addDetalhe(['Possui microondas', $reg['microondas']]);
            $this->addDetalhe(['Possui vídeo/dvd', $reg['video_dvd']]);
            $this->addDetalhe(['Possui televisão', $reg['televisao']]);
            $this->addDetalhe(['Possui telefone', $reg['telefone']]);

            $recursosTecnlogicos = json_decode($reg['recursos_tecnologicos']);
            $recursosTecnlogicos = implode(", ", $recursosTecnlogicos);
            $this->addDetalhe(['Possui acesso à recursos técnologicos?', $recursosTecnlogicos]);

            $this->addDetalhe(['Quantidade de pessoas', $reg['quant_pessoas']]);
            $this->addDetalhe(['Renda familiar', 'R$ ' . $reg['renda']]);
            $this->addDetalhe(['Possui água encanada', $reg['agua_encanada']]);
            $this->addDetalhe(['Possui poço', $reg['poco']]);
            $this->addDetalhe(['Possui energia elétrica', $reg['energia']]);
            $this->addDetalhe(['Possui tratamento de esgoto', $reg['esgoto']]);
            $this->addDetalhe(['Possui fossa', $reg['fossa']]);
            $this->addDetalhe(['Possui coleta de lixo', $reg['lixo']]);
        }

        $objProjetos = new clsPmieducarProjeto();
        $reg = $objProjetos->listaProjetosPorAluno($this->cod_aluno);
        ;

        if ($reg) {
            $tabela_projetos = '
            <table>
              <tr align="center">
                <td bgcolor="#ccdce6"><b>Projeto</b></td>
                <td bgcolor="#ccdce6"><b>Data de inclusão</b></td>
                <td bgcolor="#ccdce6"><b>Data de desligamento</b></td>
                <td bgcolor="#ccdce6"><b>Turno</b></td>
              </tr>
            ';

            $cont = 0;

            foreach ($reg as $projeto) {
                $color = ($cont++ % 2 == 0) ? ' bgcolor="#f5f9fd" ' : ' bgcolor="#FFFFFF" ';
                $turno = '';

                switch ($projeto['turno']) {
                    case 1:
                        $turno = 'Matutino';
                        break;
                    case 2:
                        $turno = 'Vespertino';
                        break;
                    case 3:
                        $turno = 'Noturno';
                        break;
                }

                $tabela_projetos .= sprintf(
                    '
                    <tr>
                        <td %s align="left">%s</td>
                        <td %s align="center">%s</td>
                        <td %s align="center">%s</td>
                        <td %s align="center">%s</td>
                    </tr>',
                    $color,
                    $projeto['projeto'],
                    $color,
                    dataToBrasil($projeto['data_inclusao']),
                    $color,
                    dataToBrasil($projeto['data_desligamento']),
                    $color,
                    $turno
                );
            }

            $tabela_projetos .= '</table>';
            $this->addDetalhe(['<span id="fprojeto"></span>Projetos', $tabela_projetos]);
        }

        $this->url_cancelar = 'educar_aluno_lst.php';
        $this->largura = '100%';
        $this->addDetalhe("<input type='hidden' id='escola_id' name='aluno_id' value='{$registro['ref_cod_escola']}' />");
        $this->addDetalhe("<input type='hidden' id='aluno_id' name='aluno_id' value='{$registro['cod_aluno']}' />");
        $mostraDependencia = config('legacy.app.matricula.dependencia');
        $this->addDetalhe("<input type='hidden' id='can_show_dependencia' name='can_show_dependencia' value='{$mostraDependencia}' />");

        $this->breadcrumb('Aluno', ['/intranet/educar_index.php' => 'Escola']);
        // js
        $scripts = [
            '/modules/Portabilis/Assets/Javascripts/Utils.js',
            '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
            '/modules/Cadastro/Assets/Javascripts/AlunoShow.js?version=3'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

        $styles = ['/modules/Cadastro/Assets/Stylesheets/Aluno.css'];

        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
    }

    private function urlPresigner()
    {
        if (!isset($this->urlPresigner)) {
            $this->urlPresigner = new UrlPresigner();
        }

        return $this->urlPresigner;
    }
}

// Instancia o objeto da página
$pagina = new clsIndexBase();

// Instancia o objeto de conteúdo
$miolo = new indice();

// Passa o conteúdo para a página
$pagina->addForm($miolo);

// Gera o HTML
$pagina->MakeAll();
