<?php

use App\Models\City;
use App\Models\Country;
use App\Models\LegacyBenefit;
use App\Models\LegacyDeficiency;
use App\Models\LegacyProject;
use App\Models\LegacyRace;
use App\Models\PersonHasPlace;
use App\Models\Religion;
use App\Models\TransportationProvider;
use App\Models\UniformDistribution;
use App\Services\UrlPresigner;
use iEducar\Modules\Educacenso\Model\Nacionalidade;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

return new class extends clsDetalhe
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
        Session::forget(keys: ['reload_faixa_etaria', 'reload_reserva_vaga']);

        // Verificação de permissão para cadastro.
        $this->obj_permissao = new clsPermissoes();

        $this->nivel_usuario = $this->obj_permissao->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        $this->titulo = 'Aluno - Detalhe';
        $this->cod_aluno = $this->getQueryString(name: 'cod_aluno');
        $tmp_obj = new clsPmieducarAluno(cod_aluno: $this->cod_aluno);
        $registro = $tmp_obj->detalhe();

        if (empty($registro)) {
            throw new HttpResponseException(
                response: new RedirectResponse(
                    url: URL::to(path: 'intranet/educar_aluno_lst.php')
                )
            );
        }

        foreach ($registro as $key => $value) {
            $this->$key = $value;
        }

        if ($this->ref_idpes) {
            $obj_pessoa_fj = new clsPessoaFj(int_idpes: $this->ref_idpes);
            $det_pessoa_fj = $obj_pessoa_fj->detalhe();

            $obj_fisica = new clsFisica(idpes: $this->ref_idpes);
            $det_fisica = $obj_fisica->detalhe();

            $obj_fisica_raca = new clsCadastroFisicaRaca();
            $lst_fisica_raca = $obj_fisica_raca->lista(int_ref_idpes: $this->ref_idpes);

            $nameRace = null;
            if ($lst_fisica_raca) {
                $det_fisica_raca = array_shift(array: $lst_fisica_raca);
                $nameRace = LegacyRace::query()->whereKey(id: $det_fisica_raca['ref_cod_raca'])->value(column: 'nm_raca');
            }

            $objFoto = new clsCadastroFisicaFoto(idpes: $this->ref_idpes);
            $detalheFoto = $objFoto->detalhe();

            if ($detalheFoto) {
                $caminhoFoto = $detalheFoto['caminho'];
            }

            $registro['nome_aluno'] = mb_strtoupper(string: $det_pessoa_fj['nome']);
            $registro['cpf'] = int2IdFederal(int: $det_fisica['cpf']);
            $registro['data_nasc'] = Portabilis_Date_Utils::pgSQLToBr(timestamp: $det_fisica['data_nasc']);

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
            $registro['nis_pis_pasep'] = int2Nis(nis: $det_fisica['nis_pis_pasep']);

            $registro['naturalidade'] = City::getNameById(id: $det_fisica['idmun_nascimento']);

            $countryName = Country::query()->find(id: $det_fisica['idpais_estrangeiro']);
            $registro['pais_origem'] = $countryName->name;

            $registro['ref_idpes_responsavel'] = $det_fisica['idpes_responsavel'];

            $this->idpes_pai = $det_fisica['idpes_pai'];
            $this->idpes_mae = $det_fisica['idpes_mae'];

            $this->sus = $det_fisica['sus'];

            $this->nm_pai = $registro['nm_pai'];
            $this->nm_mae = $registro['nm_mae'];

            if ($this->idpes_pai) {
                $obj_pessoa_pai = new clsPessoaFj(int_idpes: $this->idpes_pai);
                $det_pessoa_pai = $obj_pessoa_pai->detalhe();

                if ($det_pessoa_pai) {
                    $registro['nm_pai'] = $det_pessoa_pai['nome'];

                    // CPF
                    $obj_cpf = new clsFisica(idpes: $this->idpes_pai);
                    $det_cpf = $obj_cpf->detalhe();

                    if ($det_cpf['cpf']) {
                        $this->cpf_pai = int2CPF(int: $det_cpf['cpf']);
                    }
                }
            }

            if ($this->idpes_mae) {
                $obj_pessoa_mae = new clsPessoaFj(int_idpes: $this->idpes_mae);
                $det_pessoa_mae = $obj_pessoa_mae->detalhe();

                if ($det_pessoa_mae) {
                    $registro['nm_mae'] = $det_pessoa_mae['nome'];

                    // CPF
                    $obj_cpf = new clsFisica(idpes: $this->idpes_mae);
                    $det_cpf = $obj_cpf->detalhe();

                    if ($det_cpf['cpf']) {
                        $this->cpf_mae = int2CPF(int: $det_cpf['cpf']);
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
            $obj_deficiencia_pessoa_lista = $obj_deficiencia_pessoa->lista(int_ref_idpes: $this->ref_idpes);

            $obj_beneficios_lista = LegacyBenefit::query()
                ->whereHas(relation: 'students', callback: fn ($q) => $q->where('cod_aluno', $this->cod_aluno))
                ->get(columns: ['nm_beneficio']);

            if ($obj_deficiencia_pessoa_lista) {
                $deficiencia_pessoa = [];

                foreach ($obj_deficiencia_pessoa_lista as $deficiencia) {
                    $deficiencia_pessoa[$deficiencia['ref_cod_deficiencia']] = LegacyDeficiency::where('cod_deficiencia', $deficiencia['ref_cod_deficiencia'])->value('nm_deficiencia');
                }
            }

            $ObjDocumento = new clsDocumento(int_idpes: $this->ref_idpes);
            $detalheDocumento = $ObjDocumento->detalhe();

            $registro['rg'] = $detalheDocumento['rg'];

            if ($detalheDocumento['data_exp_rg']) {
                $registro['data_exp_rg'] = date(
                    format: 'd/m/Y',
                    timestamp: strtotime(datetime: substr(string: $detalheDocumento['data_exp_rg'], offset: 0, length: 19))
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
                    format: 'd/m/Y',
                    timestamp: strtotime(datetime: substr(string: $detalheDocumento['data_emissao_cert_civil'], offset: 0, length: 19))
                );
            }

            $registro['sigla_uf_cert_civil'] = $detalheDocumento['sigla_uf_cert_civil'];
            $registro['cartorio_cert_civil'] = $detalheDocumento['cartorio_cert_civil'];
            $registro['num_cart_trabalho'] = $detalheDocumento['num_cart_trabalho'];
            $registro['serie_cart_trabalho'] = $detalheDocumento['serie_cart_trabalho'];

            if ($detalheDocumento['data_emissao_cart_trabalho']) {
                $registro['data_emissao_cart_trabalho'] = date(
                    format: 'd/m/Y',
                    timestamp: strtotime(datetime: substr(string: $detalheDocumento['data_emissao_cart_trabalho'], offset: 0, length: 19))
                );
            }

            $registro['sigla_uf_cart_trabalho'] = $detalheDocumento['sigla_uf_cart_trabalho'];
            $registro['num_tit_eleitor'] = $detalheDocumento['num_titulo_eleitor'] ?? null;
            $registro['zona_tit_eleitor'] = $detalheDocumento['zona_titulo_eleitor'] ?? null;
            $registro['secao_tit_eleitor'] = $detalheDocumento['secao_titulo_eleitor'] ?? null;
            $registro['idorg_exp_rg'] = $detalheDocumento['ref_idorg_rg'] ?? null;

            $place = PersonHasPlace::query()
                ->with(relations: 'place.city.state')
                ->where(column: 'person_id', operator: $this->ref_idpes)
                ->orderBy(column: 'type')
                ->first();
        }

        if ($registro['cod_aluno']) {
            $this->addDetalhe(detalhe: [_cl(key: 'aluno.detalhe.codigo_aluno'), $registro['cod_aluno']]);
        }

        // código inep
        $alunoMapper = new Educacenso_Model_AlunoDataMapper();
        $alunoInep = null;

        try {
            $alunoInep = $alunoMapper->find(pkey: ['aluno' => $this->cod_aluno]);

            $configuracoes = new clsPmieducarConfiguracoesGerais();
            $configuracoes = $configuracoes->detalhe();

            if ($configuracoes['mostrar_codigo_inep_aluno']) {
                $this->addDetalhe(detalhe: ['Código inep', $alunoInep->alunoInep]);
            }
        } catch (Exception $e) {
        }

        // código estado
        $this->addDetalhe(detalhe: [_cl(key: 'aluno.detalhe.codigo_estado'), $registro['aluno_estado_id']]);

        if ($registro['nome_aluno']) {
            if ($caminhoFoto != null and $caminhoFoto != '') {
                $url = $this->urlPresigner()->getPresignedUrl(url: $caminhoFoto);

                $this->addDetalhe(detalhe: [
                    'Nome Aluno',
                    $registro['nome_aluno'] . '<p><img id="student-picture" height="117" src="' . $url . '"/></p>'
                    . '<div><a class="rotate-picture" data-angle="90" href="javascript:void(0)"><i class="fa fa-rotate-left"></i> Girar para esquerda</a></div>'
                    . '<div><a class="rotate-picture" data-angle="-90" href="javascript:void(0)"><i class="fa fa-rotate-right"></i> Girar para direita</a></div>',
                ]);
            } else {
                $this->addDetalhe(detalhe: ['Nome Aluno', $registro['nome_aluno']]);
            }
        }

        if ($det_fisica['nome_social']) {
            $this->addDetalhe(detalhe: ['Nome social e/ou afetivo', mb_strtoupper(string: $det_fisica['nome_social'])]);
        }

        if (idFederal2int(str: $registro['cpf'])) {
            $this->addDetalhe(detalhe: ['CPF', $registro['cpf']]);
        }

        if ($registro['data_nasc']) {
            $this->addDetalhe(detalhe: ['Data de Nascimento', $registro['data_nasc']]);
        }

        /**
         * Analfabeto.
         */
        $this->addDetalhe(detalhe: ['Analfabeto', $registro['analfabeto'] == 0 ? 'Não' : 'Sim']);

        if ($registro['sexo']) {
            $this->addDetalhe(detalhe: ['Sexo', $registro['sexo']]);
        }

        if ($registro['ideciv']) {
            $this->addDetalhe(detalhe: ['Estado Civil', $registro['ideciv']]);
        }

        if (isset($place)) {
            $place = $place->place;

            $this->addDetalhe(detalhe: ['Logradouro', $place->address]);
            $this->addDetalhe(detalhe: ['Número', $place->number]);
            $this->addDetalhe(detalhe: ['Complemento', $place->complement]);
            $this->addDetalhe(detalhe: ['Bairro', $place->neighborhood]);
            $this->addDetalhe(detalhe: ['Cidade', $place->city->name]);
            $this->addDetalhe(detalhe: ['UF', $place->city->state->abbreviation]);
            $this->addDetalhe(detalhe: ['CEP', int2CEP(int: $place->postal_code)]);
        }

        if ($registro['naturalidade']) {
            $this->addDetalhe(detalhe: ['Naturalidade', $registro['naturalidade']]);
        }

        if ($registro['nacionalidade']) {
            $lista_nacionalidade = [
                'NULL' => 'Selecione',
                1 => 'Brasileiro',
                2 => 'Naturalizado Brasileiro',
                3 => 'Estrangeiro',
            ];

            $registro['nacionalidade'] = $lista_nacionalidade[$registro['nacionalidade']];
            $this->addDetalhe(detalhe: ['Nacionalidade', $registro['nacionalidade']]);
        }

        if ($registro['pais_origem'] && $registro['nacionalidade'] != Nacionalidade::BRASILEIRA) {
            $this->addDetalhe(detalhe: ['País de Origem', $registro['pais_origem']]);
        }

        $responsavel = $tmp_obj->getResponsavelAluno();

        if ($responsavel && is_null(value: $registro['ref_idpes_responsavel'])) {
            $this->addDetalhe(detalhe: ['Nome do Responsável', $responsavel['nome_responsavel']]);
        }

        if ($registro['ref_idpes_responsavel']) {
            $obj_pessoa_resp = new clsPessoaFj(int_idpes: $registro['ref_idpes_responsavel']);
            $det_pessoa_resp = $obj_pessoa_resp->detalhe();

            if ($det_pessoa_resp) {
                $registro['ref_idpes_responsavel'] = $det_pessoa_resp['nome'];
            }

            $this->addDetalhe(detalhe: ['Responsável', $registro['ref_idpes_responsavel']]);
        }

        if ($registro['nm_pai']) {
            $this->addDetalhe(detalhe: ['Pai', $registro['nm_pai']]);
        }

        if ($registro['nm_mae']) {
            $this->addDetalhe(detalhe: ['Mãe', $registro['nm_mae']]);
        }

        if ($registro['fone_1']) {
            if ($registro['ddd_fone_1']) {
                $registro['ddd_fone_1'] = sprintf('(%s)&nbsp;', $registro['ddd_fone_1']);
            }

            $this->addDetalhe(detalhe: ['Telefone 1', $registro['ddd_fone_1'] . $registro['fone_1']]);
        }

        if ($registro['fone_2']) {
            if ($registro['ddd_fone_2']) {
                $registro['ddd_fone_2'] = sprintf('(%s)&nbsp;', $registro['ddd_fone_2']);
            }

            $this->addDetalhe(detalhe: ['Telefone 2', $registro['ddd_fone_2'] . $registro['fone_2']]);
        }

        if ($registro['fone_mov']) {
            if ($registro['ddd_mov']) {
                $registro['ddd_mov'] = sprintf('(%s)&nbsp;', $registro['ddd_mov']);
            }

            $this->addDetalhe(detalhe: ['Celular', $registro['ddd_mov'] . $registro['fone_mov']]);
        }

        if ($registro['fone_fax']) {
            if ($registro['ddd_fax']) {
                $registro['ddd_fax'] = sprintf('(%s)&nbsp;', $registro['ddd_fax']);
            }

            $this->addDetalhe(detalhe: ['Fax', $registro['ddd_fax'] . $registro['fone_fax']]);
        }

        if ($registro['email']) {
            $this->addDetalhe(detalhe: ['E-mail', $registro['email']]);
        }

        if ($registro['url']) {
            $this->addDetalhe(detalhe: ['Página Pessoal', $registro['url']]);
        }

        if ($det_fisica['ref_cod_religiao']) {
            $nm_religiao = Religion::query()
                ->where(column: 'id', operator: $det_fisica['ref_cod_religiao'])
                ->value(column: 'name');

            $this->addDetalhe(detalhe: ['Religião', $nm_religiao]);
        }

        if ($nameRace) {
            $this->addDetalhe(detalhe: ['Raça', $nameRace]);
        }

        if (!empty($obj_beneficios_lista)) {
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

            $this->addDetalhe(detalhe: ['Benefícios', $tabela]);
        }

        if ($deficiencia_pessoa) {
            $tabela = '<table border="0" width="300" cellpadding="3"><tr bgcolor="#ccdce6" align="center"><td>Deficiências</td></tr>';
            $cor = '#D1DADF';

            foreach ($deficiencia_pessoa as $valor) {
                $cor = $cor == '#D1DADF' ? '#f5f9fd' : '#D1DADF';

                $tabela .= sprintf(
                    '<tr bgcolor="%s" align="center"><td>%s</td></tr>',
                    $cor,
                    $valor
                );
            }

            $tabela .= '</table>';

            $this->addDetalhe(detalhe: ['Deficiências', $tabela]);
        }

        if (!empty($registro['url_documento']) && $registro['url_documento'] != '[]') {
            $tabela = '<table border="0" width="300" cellpadding="3"><tr bgcolor="#ccdce6" align="center"><td>Documentos</td></tr>';
            $cor = '#e9f0f8';

            $arrayDocumentos = json_decode(json: $registro['url_documento']);
            foreach ($arrayDocumentos as $key => $documento) {
                $cor = $cor == '#e9f0f8' ? '#f5f9fd' : '#e9f0f8';

                $tabela .= '<tr bgcolor=\'' . $cor . '\'
                        align=\'center\'>
                          <td>
                            <a href=\'' . $this->urlPresigner()->getPresignedUrl(url: $documento->url) . '\'
                               target=\'_blank\' > Visualizar documento ' . (count(value: (array) $documento) > 1 ? ($key + 1) : '') . '
                            </a>
                          </td>
                    </tr>';
            }

            $tabela .= '</table>';
            $this->addDetalhe(detalhe: ['Documentos do aluno', $tabela]);
        }

        if (!empty($registro['url_laudo_medico']) && $registro['url_laudo_medico'] != '[]') {
            $tabela = '<table border="0" width="300" cellpadding="3"><tr bgcolor="#ccdce6" align="center"><td>Laudo médico</td></tr>';

            $cor = '#D1DADF';

            $arrayLaudoMedico = json_decode(json: $registro['url_laudo_medico']);
            foreach ($arrayLaudoMedico as $key => $laudoMedico) {
                $cor = $cor == '#D1DADF' ? '#f5f9fd' : '#D1DADF';
                $laudoMedicoUrl = $this->urlPresigner()->getPresignedUrl(url: $laudoMedico->url);
                $tabela .= "<tr bgcolor='{$cor}' align='center'><td><a href='{$laudoMedicoUrl}' target='_blank' > Visualizar laudo " . (count(value: $arrayLaudoMedico) > 1 ? ($key + 1) : '') . ' </a></td></tr>';
            }

            $tabela .= '</table>';
            $this->addDetalhe(detalhe: ['Laudo médico do aluno', $tabela]);
        }

        if ($registro['rg']) {
            $this->addDetalhe(detalhe: ['RG', $registro['rg']]);
        }

        if ($registro['data_exp_rg']) {
            $this->addDetalhe(detalhe: ['Data de Expedição RG', $registro['data_exp_rg']]);
        }

        if ($registro['idorg_exp_rg']) {
            $this->addDetalhe(detalhe: ['Órgão Expedição RG', $registro['idorg_exp_rg']]);
        }

        if ($registro['sigla_uf_exp_rg']) {
            $this->addDetalhe(detalhe: ['Estado Expedidor', $registro['sigla_uf_exp_rg']]);
        }

        if (!$registro['tipo_cert_civil'] && $registro['certidao_nascimento']) {
            $this->addDetalhe(detalhe: ['Tipo Certidão Civil', 'Nascimento (novo formato)']);
            $this->addDetalhe(detalhe: ['Número Certidão Civil', $registro['certidao_nascimento']]);
        } else {
            if (!$registro['tipo_cert_civil'] && $registro['certidao_casamento']) {
                $this->addDetalhe(detalhe: ['Tipo Certidão Civil', 'Casamento (novo formato)']);
                $this->addDetalhe(detalhe: ['Número Certidão Civil', $registro['certidao_casamento']]);
            } else {
                $lista_tipo_cert_civil = [];
                $lista_tipo_cert_civil['0'] = 'Selecione';
                $lista_tipo_cert_civil[91] = 'Nascimento (antigo formato)';
                $lista_tipo_cert_civil[92] = 'Casamento (antigo formato)';

                $this->addDetalhe(detalhe: ['Tipo Certidão Civil', $lista_tipo_cert_civil[$registro['tipo_cert_civil']]]);

                if ($registro['num_termo']) {
                    $this->addDetalhe(detalhe: ['Termo', $registro['num_termo']]);
                }

                if ($registro['num_livro']) {
                    $this->addDetalhe(detalhe: ['Livro', $registro['num_livro']]);
                }

                if ($registro['num_folha']) {
                    $this->addDetalhe(detalhe: ['Folha', $registro['num_folha']]);
                }
            }
        }

        if ($registro['data_emissao_cert_civil']) {
            $this->addDetalhe(detalhe: ['Emissão Certidão Civil', $registro['data_emissao_cert_civil']]);
        }

        if ($registro['sigla_uf_cert_civil']) {
            $this->addDetalhe(detalhe: ['Sigla Certidão Civil', $registro['sigla_uf_cert_civil']]);
        }

        if ($registro['cartorio_cert_civil']) {
            $this->addDetalhe(detalhe: ['Cartório', $registro['cartorio_cert_civil']]);
        }

        if ($registro['num_tit_eleitor']) {
            $this->addDetalhe(detalhe: ['Título de Eleitor', $registro['num_tit_eleitor']]);
        }

        if ($registro['zona_tit_eleitor']) {
            $this->addDetalhe(detalhe: ['Zona', $registro['zona_tit_eleitor']]);
        }

        if ($registro['secao_tit_eleitor']) {
            $this->addDetalhe(detalhe: ['Seção', $registro['secao_tit_eleitor']]);
        }

        $this->addDetalhe(detalhe: ['Transporte escolar', $registro['tipo_transporte'] === 0 ? 'Não utiliza' : 'Sim']);

        if ($registro['tipo_transporte'] !== 0) {
            $tipoTransporte = ucfirst(string: (new TransportationProvider())->getValueDescription(value: $registro['tipo_transporte']));
            $this->addDetalhe(detalhe: ['Responsável transporte', $tipoTransporte]);
        }

        $this->addDetalhe(detalhe: ['Utiliza transporte rural', $registro['utiliza_transporte_rural'] ? 'Sim' : 'Não']);

        if ($registro['nis_pis_pasep']) {
            $this->addDetalhe(detalhe: ['NIS', $registro['nis_pis_pasep']]);
        }

        if ($this->obj_permissao->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $bloquearCadastroAluno = dbBool(val: $configuracoes['bloquear_cadastro_aluno']);

            if ($bloquearCadastroAluno == false) {
                $this->url_novo = '/module/Cadastro/aluno';
            }

            $this->url_editar = '/module/Cadastro/aluno?id=' . $registro['cod_aluno'];

            if ($this->permissaoNovaMatricula()) {
                $this->array_botao[] = 'Nova matrícula';
                $this->array_botao_url_script[] = sprintf('go("educar_matricula_cad.php?ref_cod_aluno=%d");', $registro['cod_aluno']);
            }

            $this->array_botao[] = 'Atualizar histórico';
            $this->array_botao_url_script[] = sprintf('go("educar_historico_escolar_lst.php?ref_cod_aluno=%d");', $registro['cod_aluno']);
            $this->array_botao[] = 'Distribuição de uniforme';
            $this->array_botao_url_script[] = sprintf('go("educar_distribuicao_uniforme_lst.php?ref_cod_aluno=%d");', $registro['cod_aluno']);

            if ($titulo = config(key: 'legacy.app.alunos.sistema_externo.titulo')) {
                $link = config(key: 'legacy.app.alunos.sistema_externo.link');
                $token = config(key: 'legacy.app.alunos.sistema_externo.token');

                $link = "go(\"{$link}\")";

                $link = str_replace(search: [
                    '@aluno',
                    '@usuario',
                    '@token',
                ], replace: [
                    $registro['cod_aluno'],
                    $this->user()->getKey(),
                    $token,
                ], subject: $link);

                array_unshift($this->array_botao, $titulo);
                array_unshift($this->array_botao_url_script, $link);
            }
        }

        $objFichaMedica = new clsModulesFichaMedicaAluno(ref_cod_aluno: $this->cod_aluno);
        $reg = $objFichaMedica->detalhe();

        if ($reg) {
            $this->addDetalhe(detalhe: ['<span id="fmedica"></span>', null]);
            if (trim(string: $reg['grupo_sanguineo']) != '') {
                $this->addDetalhe(detalhe: ['Grupo sanguíneo', $reg['grupo_sanguineo']]);
            }

            if (trim(string: $reg['fator_rh']) != '') {
                $this->addDetalhe(detalhe: ['Fator RH', $reg['fator_rh']]);
            }

            if (trim(string: $this->sus) != '') {
                $this->addDetalhe(detalhe: ['Número do cartão do SUS', $this->sus]);
            }

            $this->addDetalhe(detalhe: [
                'Possui alergia a algum medicamento',
                ($reg['alergia_medicamento'] == 'S' ? 'Sim' : 'Não'),
            ]);

            if (trim(string: $reg['desc_alergia_medicamento']) != '') {
                $this->addDetalhe(detalhe: ['Quais', $reg['desc_alergia_medicamento']]);
            }

            $this->addDetalhe(detalhe: [
                'Possui alergia a algum alimento',
                ($reg['alergia_alimento'] == 'S' ? 'Sim' : 'Não'),
            ]);

            if (trim(string: $reg['desc_alergia_alimento']) != '') {
                $this->addDetalhe(detalhe: ['Quais', $reg['desc_alergia_alimento']]);
            }

            $this->addDetalhe(detalhe: [
                'Possui alguma doenca congênita',
                ($reg['doenca_congenita'] == 'S' ? 'Sim' : 'Não'),
            ]);

            if (trim(string: $reg['desc_doenca_congenita']) != '') {
                $this->addDetalhe(detalhe: ['Quais', $reg['desc_doenca_congenita']]);
            }

            $this->addDetalhe(detalhe: ['É fumante', ($reg['fumante'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Já contraiu caxumba', ($reg['doenca_caxumba'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Já contraiu sarampo', ($reg['doenca_sarampo'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Já contraiu rubeola', ($reg['doenca_rubeola'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Já contraiu catapora', ($reg['doenca_catapora'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Já contraiu escarlatina', ($reg['doenca_escarlatina'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Já contraiu coqueluche', ($reg['doenca_coqueluche'] == 'S' ? 'Sim' : 'Não')]);

            if (trim(string: $reg['doenca_outras']) != '') {
                $this->addDetalhe(detalhe: ['Outras doenças que o aluno já contraiu', $reg['doenca_outras']]);
            }

            $this->addDetalhe(detalhe: ['Epilético', ($reg['epiletico'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Está em tratamento', ($reg['epiletico_tratamento'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Hemofílico', ($reg['hemofilico'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Hipertenso', ($reg['hipertenso'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Asmático', ($reg['asmatico'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Diabético', ($reg['diabetico'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Depende de insulina', ($reg['insulina'] == 'S' ? 'Sim' : 'Não')]);
            $this->addDetalhe(detalhe: ['Faz tratamento médico', ($reg['tratamento_medico'] == 'S' ? 'Sim' : 'Não')]);

            if (trim(string: $reg['desc_tratamento_medico']) != '') {
                $this->addDetalhe(detalhe: ['Qual', $reg['desc_tratamento_medico']]);
            }

            $this->addDetalhe(detalhe: [
                'Ingere medicação específica',
                ($reg['medicacao_especifica'] == 'S' ? 'Sim' : 'Não'),
            ]);

            if (trim(string: $reg['desc_medicacao_especifica']) != '') {
                $this->addDetalhe(detalhe: ['Qual', $reg['desc_medicacao_especifica']]);
            }

            $this->addDetalhe(detalhe: [
                'Acompanhamento médico ou psicológico',
                ($reg['acomp_medico_psicologico'] == 'S' ? 'Sim' : 'Não'),
            ]);

            if (trim(string: $reg['desc_acomp_medico_psicologico']) != '') {
                $this->addDetalhe(detalhe: ['Motivo', $reg['desc_acomp_medico_psicologico']]);
            }

            $this->addDetalhe(detalhe: [
                'Restrição para atividades físicas',
                ($reg['restricao_atividade_fisica'] == 'S' ? 'Sim' : 'Não'),
            ]);

            if (trim(string: $reg['desc_restricao_atividade_fisica']) != '') {
                $this->addDetalhe(detalhe: ['Qual', $reg['desc_restricao_atividade_fisica']]);
            }

            $this->addDetalhe(detalhe: ['Teve alguma fratura ou trauma', ($reg['fratura_trauma'] == 'S' ? 'Sim' : 'Não')]);

            if (trim(string: $reg['desc_fratura_trauma']) != '') {
                $this->addDetalhe(detalhe: ['Qual', $reg['desc_fratura_trauma']]);
            }

            $this->addDetalhe(detalhe: ['Tem plano de saúde', ($reg['plano_saude'] == 'S' ? 'Sim' : 'Não')]);

            if (trim(string: $reg['desc_plano_saude']) != '') {
                $this->addDetalhe(detalhe: ['Qual', $reg['desc_plano_saude']]);
            }

            $this->addDetalhe(detalhe: ['<span id="tr_tit_dados_hospital">Em caso de emergência, autorizo levar meu(minha) filho(a) para o Hospital ou Clínica mais próximos:</span>']);
            $this->addDetalhe(detalhe: ['Responsável', $reg['desc_aceita_hospital_proximo']]);
            $this->addDetalhe(detalhe: ['<span id="tr_tit_dados_hospital">Em caso de emergência, se não for possível contatar os responsáveis, comunicar</span>']);
            $this->addDetalhe(detalhe: ['Nome', $reg['responsavel_nome']]);
            $this->addDetalhe(detalhe: ['Parentesco', $reg['responsavel_parentesco']]);
            $this->addDetalhe(detalhe: ['Telefone', $reg['responsavel_parentesco_telefone']]);
            $this->addDetalhe(detalhe: ['Celular', $reg['responsavel_parentesco_celular']]);
        }

        $uniformDistribution = UniformDistribution::where('student_id', $this->cod_aluno)
            ->where('year', now()->year)
            ->first();

        if ($uniformDistribution) {
            if ($uniformDistribution->complete_kit) {
                $this->addDetalhe(detalhe: ['<span id=\'funiforme\'></span>Recebeu kit completo', 'Sim']);
                $this->addDetalhe(detalhe: [
                    '<span id=\'ffuniforme\'></span>' . 'Data da distribuição',
                    $uniformDistribution->distribution_date?->format('d/m/Y'),
                ]);
            } else {
                $this->addDetalhe(detalhe: [
                    '<span id=\'funiforme\'></span>Recebeu kit completo',
                    'Não',
                ]);
                $this->addDetalhe(detalhe: [
                    'Tipo',
                    $uniformDistribution->type,
                ]);
                $this->addDetalhe(detalhe: [
                    'Data da distribuição',
                    $uniformDistribution->distribution_date?->format('d/m/Y'),
                ]);
                $this->addDetalhe(detalhe: ['Quantidade de agasalhos (jaqueta)', $uniformDistribution->coat_jacket_qty ?: '0']);
                $this->addDetalhe(detalhe: ['Quantidade de agasalhos (calça)', $uniformDistribution->coat_pants_qty ?: '0']);
                $this->addDetalhe(detalhe: ['Quantidade de camisetas (manga curta)', $uniformDistribution->shirt_short_qty ?: '0']);
                $this->addDetalhe(detalhe: ['Quantidade de camisetas (manga longa)', $uniformDistribution->shirt_long_qty ?: '0']);
                $this->addDetalhe(detalhe: ['Quantidade de camisetas infantis (sem manga)', $uniformDistribution->kids_shirt_qty ?: '0']);
                $this->addDetalhe(detalhe: ['Quantidade de calça jeans', $uniformDistribution->pants_jeans_qty ?: '0']);
                $this->addDetalhe(detalhe: ['Quantidade de meias', $uniformDistribution->socks_qty ?: '0']);
                $this->addDetalhe(detalhe: ['Bermudas tectels (masculino)', $uniformDistribution->shorts_tactel_qty ?: '0']);
                $this->addDetalhe(detalhe: ['Bermudas coton (feminino)', $uniformDistribution->shorts_coton_qty ?: '0']);
                $this->addDetalhe(detalhe: [
                    '<span id=\'ffuniforme\'></span>' . 'Quantidade de tênis',
                    $uniformDistribution->sneakers_qty ?: '0',
                ]);
            }
        }

        $objMoradia = new clsModulesMoradiaAluno(ref_cod_aluno: $this->cod_aluno);
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

            $this->addDetalhe(detalhe: ['<span id="fmoradia"></span>Moradia', $moradia]);

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

            $this->addDetalhe(detalhe: ['Situação', $situacao]);
            $this->addDetalhe(detalhe: ['Quantidade de quartos', $reg['quartos']]);
            $this->addDetalhe(detalhe: ['Quantidade de salas', $reg['sala']]);
            $this->addDetalhe(detalhe: ['Quantidade de copas', $reg['copa']]);
            $this->addDetalhe(detalhe: ['Quantidade de banheiros', $reg['banheiro']]);
            $this->addDetalhe(detalhe: ['Quantidade de garagens', $reg['garagem']]);
            $this->addDetalhe(detalhe: ['Possui empregada doméstica', $reg['empregada_domestica']]);
            $this->addDetalhe(detalhe: ['Possui automóvel', $reg['automovel']]);
            $this->addDetalhe(detalhe: ['Possui motocicleta', $reg['motocicleta']]);
            $this->addDetalhe(detalhe: ['Possui geladeira', $reg['geladeira']]);
            $this->addDetalhe(detalhe: ['Possui fogão', $reg['fogao']]);
            $this->addDetalhe(detalhe: ['Possui máquina de lavar', $reg['maquina_lavar']]);
            $this->addDetalhe(detalhe: ['Possui microondas', $reg['microondas']]);
            $this->addDetalhe(detalhe: ['Possui vídeo/dvd', $reg['video_dvd']]);
            $this->addDetalhe(detalhe: ['Possui televisão', $reg['televisao']]);
            $this->addDetalhe(detalhe: ['Possui telefone', $reg['telefone']]);

            $recursosTecnlogicos = json_decode(json: $reg['recursos_tecnologicos']);
            if (is_array(value: $recursosTecnlogicos)) {
                $recursosTecnlogicos = implode(separator: ', ', array: $recursosTecnlogicos);
            }
            $this->addDetalhe(detalhe: ['Possui acesso à recursos técnologicos?', $recursosTecnlogicos]);

            $this->addDetalhe(detalhe: ['Quantidade de pessoas', $reg['quant_pessoas']]);
            $this->addDetalhe(detalhe: ['Renda familiar', 'R$ ' . $reg['renda']]);
            $this->addDetalhe(detalhe: ['Possui água encanada', $reg['agua_encanada']]);
            $this->addDetalhe(detalhe: ['Possui poço', $reg['poco']]);
            $this->addDetalhe(detalhe: ['Possui energia elétrica', $reg['energia']]);
            $this->addDetalhe(detalhe: ['Possui tratamento de esgoto', $reg['esgoto']]);
            $this->addDetalhe(detalhe: ['Possui fossa', $reg['fossa']]);
            $this->addDetalhe(detalhe: ['Possui coleta de lixo', $reg['lixo']]);
        }

        $reg = LegacyProject::query()->where(column: 'pmieducar.projeto_aluno.ref_cod_aluno', operator: $this->cod_aluno)
            ->join(table: 'pmieducar.projeto_aluno', first: 'pmieducar.projeto_aluno.ref_cod_projeto', operator: '=', second: 'pmieducar.projeto.cod_projeto')
            ->orderBy(column: 'nome', direction: 'ASC')
            ->get();

        if (!empty($reg)) {
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

                switch ($projeto->turno) {
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
                    $projeto->nome,
                    $color,
                    dataToBrasil(data_original: $projeto->data_inclusao),
                    $color,
                    dataToBrasil(data_original: $projeto->data_desligamento),
                    $color,
                    $turno
                );
            }

            $tabela_projetos .= '</table>';
            $this->addDetalhe(detalhe: ['<span id="fprojeto"></span>Projetos', $tabela_projetos]);
        }

        $this->url_cancelar = 'educar_aluno_lst.php';
        $this->largura = '100%';
        $this->addDetalhe(detalhe: "<input type='hidden' id='escola_id' name='aluno_id' value='{$registro['ref_cod_escola']}' />");
        $this->addDetalhe(detalhe: "<input type='hidden' id='aluno_id' name='aluno_id' value='{$registro['cod_aluno']}' />");
        $mostraDependencia = config(key: 'legacy.app.matricula.dependencia');
        $this->addDetalhe(detalhe: "<input type='hidden' id='can_show_dependencia' name='can_show_dependencia' value='{$mostraDependencia}' />");

        $this->breadcrumb(currentPage: 'Aluno', breadcrumbs: ['/intranet/educar_index.php' => 'Escola']);
        // js
        $scripts = [
            '/vendor/legacy/Portabilis/Assets/Javascripts/Utils.js',
            '/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js',
            '/vendor/legacy/Cadastro/Assets/Javascripts/AlunoShow.js?version=3',
        ];

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);

        $styles = ['/vendor/legacy/Cadastro/Assets/Stylesheets/Aluno.css'];

        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: $styles);
    }

    private function permissaoNovaMatricula()
    {
        $user = Auth::user();
        $allow = Gate::allows(ability: 'view', arguments: 680);
        if ($user->isLibrary()) {
            return false;
        }

        return $allow;
    }

    private function urlPresigner()
    {
        if (!isset($this->urlPresigner)) {
            $this->urlPresigner = new UrlPresigner();
        }

        return $this->urlPresigner;
    }

    public function Formular()
    {
        $this->title = 'Aluno';
        $this->processoAp = 578;
    }
};
