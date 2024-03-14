<?php

use App\Models\Educacenso\Registro30;
use App\Models\Individual;
use App\Models\LegacyDeficiency;
use App\Models\LegacyIndividual;
use App\Models\LegacyInstitution;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolHistory;
use App\Models\LegacyStudentBenefit;
use App\Models\LegacyStudentHistoricalHeightWeight;
use App\Models\LegacyStudentProject;
use App\Models\LogUnification;
use App\Models\SchoolInep;
use App\Models\TransportationProvider;
use App\User;
use iEducar\Modules\Educacenso\Model\Nacionalidade;
use iEducar\Modules\Educacenso\Validator\BirthCertificateValidator;
use iEducar\Modules\Educacenso\Validator\DeficiencyValidator;
use iEducar\Modules\Educacenso\Validator\InepExamValidator;
use iEducar\Modules\Educacenso\Validator\NisValidator;
use iEducar\Modules\People\CertificateType;
use Illuminate\Support\Facades\Auth;

class AlunoController extends ApiCoreController
{
    protected $_processoAp = 578;

    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;

    // validators
    protected function validatesPessoaId()
    {
        $existenceOptions = ['schema_name' => 'cadastro', 'field_name' => 'idpes'];

        return $this->validatesPresenceOf('pessoa_id') &&
            $this->validatesExistenceOf('fisica', $this->getRequest()->pessoa_id, $existenceOptions);
    }

    protected function validatesBeneficioId()
    {
        // TODO Alterar pois foi alterado relacionamento para N:N
        $isValid = true;

        // beneficio is optional
        if (is_numeric($this->getRequest()->beneficio_id)) {
            $isValid = ($this->validatesPresenceOf('beneficio_id') &&
                $this->validatesExistenceOf('aluno_beneficio', $this->getRequest()->beneficio_id)
            );
        }

        return $isValid;
    }

    protected function validatesResponsavelId()
    {
        $isValid = true;

        if ($this->getRequest()->tipo_responsavel == 'outra_pessoa') {
            $existenceOptions = ['schema_name' => 'cadastro', 'field_name' => 'idpes'];

            $isValid = ($this->validatesPresenceOf('responsavel_id') &&
                $this->validatesExistenceOf('fisica', $this->getRequest()->responsavel_id, $existenceOptions)
            );
        }

        return $isValid;
    }

    protected function validatesResponsavelTipo()
    {
        $expectedValues = ['mae', 'pai', 'outra_pessoa', 'pai_mae'];

        return $this->validatesPresenceOf('tipo_responsavel') &&
            $this->validator->validatesValueInSetOf(
                $this->getRequest()->tipo_responsavel,
                $expectedValues,
                'tipo_responsavel'
            );
    }

    protected function validatesResponsavel()
    {
        return $this->validatesResponsavelTipo() &&
            $this->validatesResponsavelId();
    }

    protected function validatesTransporte()
    {
        $expectedValues = ['nenhum', 'municipal', 'estadual'];

        return $this->validatesPresenceOf('tipo_transporte') &&
            $this->validator->validatesValueInSetOf(
                $this->getRequest()->tipo_transporte,
                $expectedValues,
                'tipo_transporte'
            );
    }

    protected function validatesUniquenessOfAlunoByPessoaId()
    {
        $existenceOptions = ['schema_name' => 'pmieducar', 'field_name' => 'ref_idpes', 'add_msg_on_error' => false];

        if (!$this->validatesUniquenessOf('aluno', $this->getRequest()->pessoa_id, $existenceOptions)) {
            $this->messenger->append("Já existe um aluno cadastrado para a pessoa {$this->getRequest()->pessoa_id}.");

            return false;
        }

        return true;
    }

    protected function validatesUniquenessOfAlunoInepId()
    {
        if ($this->getRequest()->aluno_inep_id) {
            $sql = 'SELECT a.cod_aluno FROM modules.educacenso_cod_aluno eca INNER JOIN pmieducar.aluno a ON (a.cod_aluno = eca.cod_aluno) WHERE eca.cod_aluno_inep = $1 AND a.ativo = 1 ';
            $params = [$this->getRequest()->aluno_inep_id];

            if ($this->getRequest()->id) {
                $sql .= ' AND a.cod_aluno != $2';
                $params[] = $this->getRequest()->id;
            }

            $alunoId = $this->fetchPreparedQuery($sql, $params, true, 'first-field');

            if ($alunoId) {
                $this->messenger->append("Já existe o aluno $alunoId cadastrado com código inep " .
                    " {$this->getRequest()->aluno_inep_id}.");

                return false;
            }
        }

        return true;
    }

    protected function validatesUniquenessOfAlunoEstadoId()
    {
        if ($this->getRequest()->aluno_estado_id) {
            $sql = 'select cod_aluno from pmieducar.aluno where aluno_estado_id = $1 AND ativo = 1 ';
            $params = [$this->getRequest()->aluno_estado_id];

            if ($this->getRequest()->id) {
                $sql .= ' and cod_aluno != $2';
                $params[] = $this->getRequest()->id;
            }

            $alunoId = $this->fetchPreparedQuery($sql, $params, true, 'first-field');
            $configuracoes = new clsPmieducarConfiguracoesGerais();
            $configuracoes = $configuracoes->detalhe();

            if (!empty($configuracoes['tamanho_min_rede_estadual'])) {
                $count = strlen($this->getRequest()->aluno_estado_id);
                if ($count < $configuracoes['tamanho_min_rede_estadual']) {
                    $this->messenger->append("O Código rede estadual informado é inválido. {$this->getRequest()->aluno_estado_id}.");

                    return false;
                }
            }

            if ($alunoId) {
                $this->messenger->append("Já existe o aluno $alunoId cadastrado com código estadual (RA) {$this->getRequest()->aluno_estado_id}.");

                return false;
            }
        }

        return true;
    }

    // validations
    protected function canGetMatriculas()
    {
        return $this->validatesId('aluno');
    }

    protected function canGetTodosAlunos()
    {
        return $this->validatesPresenceOf('instituicao_id') && $this->validatesPresenceOf('escola');
    }

    protected function canGetAlunosByGuardianCpf()
    {
        return $this->validatesPresenceOf('aluno_id') && $this->validatesPresenceOf('cpf');
    }

    protected function validateInepCode()
    {
        if ($this->getRequest()->aluno_inep_id) {
            $inepCode = str_split($this->getRequest()->aluno_inep_id);

            if (count($inepCode) !== 12 || $inepCode[0] === '0') {
                return false;
            }
        }

        return true;
    }

    protected function canChange()
    {
        return $this->validatesPessoaId() &&
            $this->validatesResponsavel() &&
            $this->validatesTransporte() &&
            $this->validatesUniquenessOfAlunoInepId() &&
            $this->validatesUniquenessOfAlunoEstadoId();
    }

    protected function canPost()
    {
        return parent::canPost() &&
            $this->validatesUniquenessOfAlunoByPessoaId() &&
            $this->commonValidate();
    }

    protected function canPut()
    {
        return parent::canPut() && $this->commonValidate();
    }

    protected function commonValidate(): bool
    {
        return $this->validateDeficiencies() &&
            $this->validateNis() &&
            $this->validateInepExam() &&
            $this->validateTechnologicalResources() &&
            $this->validateCpfCode() &&
            $this->validateBirthCertificate() &&
            $this->validateInepCode();
    }

    private function validateCpfCode()
    {
        /** @var User $user */
        $user = Auth::user();

        $cpf = $this->getRequest()->id_federal;

        if ($user->ref_cod_instituicao) {
            $strictValitation = LegacyInstitution::query()
                ->find($user->ref_cod_instituicao, ['obrigar_cpf'])?->obrigar_cpf;
        } else {
            $strictValitation = LegacyInstitution::query()
                ->first(['obrigar_cpf'])?->obrigar_cpf;
        }

        if ($strictValitation) {
            $ignoreValidateCpf = LegacyIndividual::where('nacionalidade', Nacionalidade::ESTRANGEIRA)->whereKey($this->getRequest()->pessoa_id)->exists();

            if ($ignoreValidateCpf || validaCPF($cpf)) {
                return true;
            }
            $this->messenger->append('O CPF informado é inválido');

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function validateDeficiencies()
    {
        $deficiencias = array_filter((array) $this->getRequest()->deficiencias);

        $deficiencias = $this->replaceByEducacensoDeficiencies($deficiencias);

        $validator = new DeficiencyValidator($deficiencias);

        if ($validator->isValid()) {
            return true;
        } else {
            $this->messenger->append($validator->getMessage());

            return false;
        }
    }

    /**
     * @return bool
     */
    private function validateBirthCertificate()
    {
        $usesBirthCertificate = $this->getRequest()->tipo_certidao_civil == CertificateType::BIRTH_NEW_FORMAT;
        $individual = Individual::find($this->getRequest()->pessoa_id);
        if (!$usesBirthCertificate || empty($this->getRequest()->certidao_nascimento) || !$individual || empty($individual->birthdate)) {
            return true;
        }

        $validator = new BirthCertificateValidator($this->getRequest()->certidao_nascimento, $individual->birthdate);

        if ($validator->isValid()) {
            return true;
        }

        $this->messenger->append($validator->getMessage());

        return false;
    }

    /**
     * @return bool
     */
    private function validateNis()
    {
        $validator = new NisValidator($this->getRequest()->nis_pis_pasep ?? '');

        if ($validator->isValid()) {
            return true;
        }

        $this->messenger->append($validator->getMessage());

        return false;
    }

    /**
     * @return bool
     */
    private function validateTechnologicalResources()
    {
        $technologicalResources = array_filter((array) $this->getRequest()->recursos_tecnologicos__);

        if (in_array('Nenhum', $technologicalResources) && count($technologicalResources) > 1) {
            $this->messenger->append('Não é possível informar mais de uma opção no campo: <strong>Possui acesso à recursos tecnológicos?</strong>, quando a opção: <b>Nenhum</b> estiver selecionada.');

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function validateInepExam()
    {
        $resources = array_filter((array) $this->getRequest()->recursos_prova_inep__);
        $deficiencies = array_filter((array) $this->getRequest()->deficiencias);

        $deficiencies = $this->replaceByEducacensoDeficiencies($deficiencies);

        $validator = new InepExamValidator($resources, $deficiencies);

        if ($validator->isValid()) {
            return true;
        }

        $this->messenger->append($validator->getMessage());

        return false;
    }

    protected function canGetOcorrenciasDisciplinares()
    {
        return $this->validatesPresenceOf('escola');
    }

    // load resources
    protected function loadNomeAluno($alunoId)
    {
        $sql = 'select nome from cadastro.pessoa, pmieducar.aluno where idpes = ref_idpes and cod_aluno = $1';
        $nome = $this->fetchPreparedQuery($sql, $alunoId, false, 'first-field');

        return $this->toUtf8($nome, ['transform' => true]);
    }

    protected function validaTurnoProjeto($alunoId, $turnoId)
    {
        if (config('legacy.app.projetos.ignorar_turno_igual_matricula') == 1) {
            return true;
        }

        $sql = 'SELECT 1
                  FROM pmieducar.turma t
            INNER JOIN pmieducar.matricula_turma mt ON (mt.ref_cod_turma = t.cod_turma)
            INNER JOIN pmieducar.matricula m ON (m.cod_matricula = mt.ref_cod_matricula)
                 WHERE t.turma_turno_id = $2
                   AND mt.ativo = 1
                   AND m.ativo = 1
                   AND m.aprovado = 3
                   AND m.ref_cod_aluno = $1';

        $turnoValido = !(bool) $this->fetchPreparedQuery($sql, [$alunoId, $turnoId], false, 'first-field');

        return $turnoValido;
    }

    protected function loadTransporte($responsavelTransporte): string
    {
        return (new TransportationProvider())->getValueDescription($responsavelTransporte);
    }

    protected function saveSus($pessoaId)
    {
        $sus = $this->getRequest()->sus;
        $sql = 'update cadastro.fisica set sus = $1 where idpes = $2';

        return $this->fetchPreparedQuery($sql, [$sus, $pessoaId]);
    }

    protected function createOrUpdateFichaMedica($id)
    {
        $obj = new clsModulesFichaMedicaAluno();

        $obj->ref_cod_aluno = $id;
        $obj->altura = $this->getRequest()->altura;
        $obj->peso = $this->getRequest()->peso;
        $obj->grupo_sanguineo = $this->getRequest()->grupo_sanguineo;
        $obj->grupo_sanguineo = trim($obj->grupo_sanguineo);
        $obj->fator_rh = $this->getRequest()->fator_rh;
        $obj->alergia_medicamento = ($this->getRequest()->alergia_medicamento == 'on' ? 'S' : 'N');
        $obj->desc_alergia_medicamento = $this->getRequest()->desc_alergia_medicamento;
        $obj->alergia_alimento = ($this->getRequest()->alergia_alimento == 'on' ? 'S' : 'N');
        $obj->desc_alergia_alimento = $this->getRequest()->desc_alergia_alimento;
        $obj->doenca_congenita = ($this->getRequest()->doenca_congenita == 'on' ? 'S' : 'N');
        $obj->desc_doenca_congenita = $this->getRequest()->desc_doenca_congenita;
        $obj->fumante = ($this->getRequest()->fumante == 'on' ? 'S' : 'N');
        $obj->doenca_caxumba = ($this->getRequest()->doenca_caxumba == 'on' ? 'S' : 'N');
        $obj->doenca_sarampo = ($this->getRequest()->doenca_sarampo == 'on' ? 'S' : 'N');
        $obj->doenca_rubeola = ($this->getRequest()->doenca_rubeola == 'on' ? 'S' : 'N');
        $obj->doenca_catapora = ($this->getRequest()->doenca_catapora == 'on' ? 'S' : 'N');
        $obj->doenca_escarlatina = ($this->getRequest()->doenca_escarlatina == 'on' ? 'S' : 'N');
        $obj->doenca_coqueluche = ($this->getRequest()->doenca_coqueluche == 'on' ? 'S' : 'N');
        $obj->doenca_outras = $this->getRequest()->doenca_outras;
        $obj->epiletico = ($this->getRequest()->epiletico == 'on' ? 'S' : 'N');
        $obj->epiletico_tratamento = ($this->getRequest()->epiletico_tratamento == 'on' ? 'S' : 'N');
        $obj->hemofilico = ($this->getRequest()->hemofilico == 'on' ? 'S' : 'N');
        $obj->hipertenso = ($this->getRequest()->hipertenso == 'on' ? 'S' : 'N');
        $obj->asmatico = ($this->getRequest()->asmatico == 'on' ? 'S' : 'N');
        $obj->diabetico = ($this->getRequest()->diabetico == 'on' ? 'S' : 'N');
        $obj->insulina = ($this->getRequest()->insulina == 'on' ? 'S' : 'N');
        $obj->tratamento_medico = ($this->getRequest()->tratamento_medico == 'on' ? 'S' : 'N');
        $obj->desc_tratamento_medico = $this->getRequest()->desc_tratamento_medico;
        $obj->medicacao_especifica = ($this->getRequest()->medicacao_especifica == 'on' ? 'S' : 'N');
        $obj->desc_medicacao_especifica = $this->getRequest()->desc_medicacao_especifica;
        $obj->acomp_medico_psicologico = ($this->getRequest()->acomp_medico_psicologico == 'on' ? 'S' : 'N');
        $obj->desc_acomp_medico_psicologico = $this->getRequest()->desc_acomp_medico_psicologico;
        $obj->acomp_medico_psicologico = ($this->getRequest()->acomp_medico_psicologico == 'on' ? 'S' : 'N');
        $obj->desc_acomp_medico_psicologico = $this->getRequest()->desc_acomp_medico_psicologico;
        $obj->restricao_atividade_fisica = ($this->getRequest()->restricao_atividade_fisica == 'on' ? 'S' : 'N');
        $obj->desc_restricao_atividade_fisica = $this->getRequest()->desc_restricao_atividade_fisica;
        $obj->fratura_trauma = ($this->getRequest()->fratura_trauma == 'on' ? 'S' : 'N');
        $obj->desc_fratura_trauma = $this->getRequest()->desc_fratura_trauma;
        $obj->plano_saude = ($this->getRequest()->plano_saude == 'on' ? 'S' : 'N');
        $obj->desc_plano_saude = $this->getRequest()->desc_plano_saude;
        $obj->responsavel = $this->getRequest()->responsavel;
        $obj->responsavel_parentesco = $this->getRequest()->responsavel_parentesco;
        $obj->responsavel_parentesco_telefone = $this->getRequest()->responsavel_parentesco_telefone;
        $obj->responsavel_parentesco_celular = $this->getRequest()->responsavel_parentesco_celular;
        $obj->aceita_hospital_proximo = ($this->getRequest()->aceita_hospital_proximo == 'on' ? 'S' : 'N');
        $obj->desc_aceita_hospital_proximo = $this->getRequest()->desc_aceita_hospital_proximo;

        return $obj->existe() ? $obj->edita() : $obj->cadastra();
    }

    protected function createOrUpdateMoradia($id)
    {
        $obj = new clsModulesMoradiaAluno();

        $obj->ref_cod_aluno = $id;
        $obj->moradia = $this->getRequest()->moradia;
        $obj->material = $this->getRequest()->material;
        $obj->casa_outra = $this->getRequest()->casa_outra;
        $obj->moradia_situacao = $this->getRequest()->moradia_situacao;
        $obj->quartos = $this->getRequest()->quartos;
        $obj->sala = $this->getRequest()->sala;
        $obj->copa = $this->getRequest()->copa;
        $obj->banheiro = $this->getRequest()->banheiro;
        $obj->garagem = $this->getRequest()->garagem;
        $obj->empregada_domestica = ($this->getRequest()->empregada_domestica == 'on' ? 'S' : 'N');
        $obj->automovel = ($this->getRequest()->automovel == 'on' ? 'S' : 'N');
        $obj->motocicleta = ($this->getRequest()->motocicleta == 'on' ? 'S' : 'N');
        $obj->geladeira = ($this->getRequest()->geladeira == 'on' ? 'S' : 'N');
        $obj->fogao = ($this->getRequest()->fogao == 'on' ? 'S' : 'N');
        $obj->maquina_lavar = ($this->getRequest()->maquina_lavar == 'on' ? 'S' : 'N');
        $obj->microondas = ($this->getRequest()->microondas == 'on' ? 'S' : 'N');
        $obj->video_dvd = ($this->getRequest()->video_dvd == 'on' ? 'S' : 'N');
        $obj->televisao = ($this->getRequest()->televisao == 'on' ? 'S' : 'N');
        $obj->telefone = ($this->getRequest()->telefone == 'on' ? 'S' : 'N');

        $recursosTeconologicos = array_filter((array) $this->getRequest()->recursos_tecnologicos__);
        $obj->recursos_tecnologicos = json_encode(array_values($recursosTeconologicos));

        $obj->quant_pessoas = $this->getRequest()->quant_pessoas;
        $obj->renda = floatval(preg_replace("/[^0-9\.]/", '', str_replace(',', '.', $this->getRequest()->renda)));
        $obj->agua_encanada = ($this->getRequest()->agua_encanada == 'on' ? 'S' : 'N');
        $obj->poco = ($this->getRequest()->poco == 'on' ? 'S' : 'N');
        $obj->energia = ($this->getRequest()->energia == 'on' ? 'S' : 'N');
        $obj->esgoto = ($this->getRequest()->esgoto == 'on' ? 'S' : 'N');
        $obj->fossa = ($this->getRequest()->fossa == 'on' ? 'S' : 'N');
        $obj->lixo = ($this->getRequest()->lixo == 'on' ? 'S' : 'N');

        return $obj->existe() ? $obj->edita() : $obj->cadastra();
    }

    protected function loadAlunoInepId($alunoId)
    {
        $dataMapper = $this->getDataMapperFor('educacenso', 'aluno');
        $entity = $this->tryGetEntityOf($dataMapper, $alunoId);

        return is_null($entity) ? null : $entity->get('alunoInep');
    }

    protected function createUpdateOrDestroyEducacensoAluno($alunoId)
    {
        $dataMapper = $this->getDataMapperFor('educacenso', 'aluno');

        $result = $this->deleteEntityOf($dataMapper, $alunoId);
        if (!empty($this->getRequest()->aluno_inep_id)) {
            $data = [
                'aluno' => $alunoId,
                'alunoInep' => $this->getRequest()->aluno_inep_id,
                'fonte' => 'fonte',
                'nomeInep' => '-',
                'created_at' => 'NOW()',
            ];

            $entity = $this->getOrCreateEntityOf($dataMapper, $alunoId);
            $entity->setOptions($data);

            $result = $this->saveEntity($dataMapper, $entity);
        }

        return $result;
    }

    // #TODO mover updateResponsavel e updateDeficiencias para API pessoa ?
    protected function updateResponsavel()
    {
        $pessoa = new clsFisica();
        $pessoa->idpes = $this->getRequest()->pessoa_id;
        $pessoa->nome_responsavel = '';

        $_pessoa = $pessoa->detalhe();

        if ($this->getRequest()->tipo_responsavel == 'outra_pessoa') {
            $pessoa->idpes_responsavel = $this->getRequest()->responsavel_id;
        } elseif ($this->getRequest()->tipo_responsavel == 'pai' && $_pessoa['idpes_pai']) {
            $pessoa->idpes_responsavel = $_pessoa['idpes_pai'];
        } elseif ($this->getRequest()->tipo_responsavel == 'mae' && $_pessoa['idpes_mae']) {
            $pessoa->idpes_responsavel = $_pessoa['idpes_mae'];
        } else {
            $pessoa->idpes_responsavel = 'NULL';
        }

        return $pessoa->edita();
    }

    protected function updateDeficiencias()
    {
        $individual = LegacyIndividual::find($this->getRequest()->pessoa_id, ['idpes']);
        $old = $individual->deficiency()->pluck('ref_cod_deficiencia')->toArray();
        $news = array_filter(array_merge($this->getRequest()->deficiencias, $this->getRequest()->transtornos));
        $individual->deficiency()->sync($news);

        $diff = array_merge(array_diff($old, $news), array_diff($news, $old));

        if (!empty($diff)) {
            LegacyDeficiency::whereIn('cod_deficiencia', $diff)->update(['updated_at' => now()]);
        }
    }

    protected function createOrUpdateAluno($id = null)
    {
        $tiposResponsavel = ['pai' => 'p', 'mae' => 'm', 'outra_pessoa' => 'r', 'pai_mae' => 'a'];

        $aluno = new clsPmieducarAluno();
        $aluno->cod_aluno = $id;

        $alunoEstadoId = mb_strtoupper($this->getRequest()->aluno_estado_id);
        $alunoEstadoId = str_replace(['-', '.'], '', $alunoEstadoId);

        if (strlen($alunoEstadoId) < 10) {
            $mask['pattern'] = '"(.{3})(.{3})(.{3})"';
            $mask['replacement'] = '\\1.\\2.\\3';
        } else {
            $mask['pattern'] = '"(.{3})(.{3})(.{3})(.{1})"';
            $mask['replacement'] = '\\1.\\2.\\3-\\4';
        }

        $aluno->aluno_estado_id = preg_replace($mask['pattern'], $mask['replacement'], $alunoEstadoId);
        $aluno->codigo_sistema = $this->getRequest()->codigo_sistema;
        $aluno->autorizado_um = $this->getRequest()->autorizado_um;
        $aluno->parentesco_um = $this->getRequest()->parentesco_um;
        $aluno->autorizado_dois = $this->getRequest()->autorizado_dois;
        $aluno->parentesco_dois = $this->getRequest()->parentesco_dois;
        $aluno->autorizado_tres = $this->getRequest()->autorizado_tres;
        $aluno->parentesco_tres = $this->getRequest()->parentesco_tres;
        $aluno->autorizado_quatro = $this->getRequest()->autorizado_quatro;
        $aluno->parentesco_quatro = $this->getRequest()->parentesco_quatro;
        $aluno->autorizado_cinco = $this->getRequest()->autorizado_cinco;
        $aluno->parentesco_cinco = $this->getRequest()->parentesco_cinco;

        // após cadastro não muda mais id pessoa
        if (is_null($id)) {
            $aluno->ref_idpes = $this->getRequest()->pessoa_id;
        }

        if (!config('legacy.app.alunos.nao_apresentar_campo_alfabetizado')) {
            $aluno->analfabeto = $this->getRequest()->alfabetizado ? 0 : 1;
        }

        $aluno->emancipado = (bool) $this->getRequest()->emancipado;
        $aluno->tipo_responsavel = $tiposResponsavel[$this->getRequest()->tipo_responsavel];
        $aluno->ref_usuario_exc = \Illuminate\Support\Facades\Auth::id();

        // INFORAMÇÕES PROVA INEP
        $recursosProvaInepRequest = $this->getRequest()->recursos_prova_inep__;
        $recursosProvaInep = null;
        if (is_array($recursosProvaInepRequest)) {
            $recursosProvaInep = array_filter($recursosProvaInepRequest);
            $recursosProvaInep = '{' . implode(',', $recursosProvaInep) . '}';
        }
        $aluno->recursos_prova_inep = $recursosProvaInep;
        $aluno->recebe_escolarizacao_em_outro_espaco = $this->getRequest()->recebe_escolarizacao_em_outro_espaco;
        $aluno->justificativa_falta_documentacao = $this->getRequest()->justificativa_falta_documentacao;

        $veiculoTransportEscolarFromRequest = $this->getRequest()->veiculo_transporte_escolar;
        $veiculoTransporteEscolar = null;
        if (is_array($veiculoTransportEscolarFromRequest)) {
            $veiculoTransporteEscolar = implode(',', array_filter($this->getRequest()->veiculo_transporte_escolar));
        }

        $aluno->veiculo_transporte_escolar = $veiculoTransporteEscolar;

        $this->file_foto = $_FILES['file'];
        $this->del_foto = $_POST['file_delete'];

        if (!$this->validatePhoto()) {
            $this->mensagem = 'Foto inválida';

            return false;
        }

        $pessoaId = $this->getRequest()->pessoa_id;
        $this->savePhoto($pessoaId);

        //documentos
        $aluno->url_documento = $this->getRequest()->url_documento;

        //laudo medico
        $aluno->url_laudo_medico = $this->getRequest()->url_laudo_medico;

        $aluno->tipo_transporte = (new TransportationProvider())->from($this->getRequest()->tipo_transporte);

        if (is_null($id)) {
            $id = $aluno->cadastra();
            $aluno->cod_aluno = $id;
        } else {
            $detalheAntigo = $aluno->detalhe();
            $id = $aluno->edita();
        }

        return $id;
    }

    protected function loadTurmaByMatriculaId($matriculaId)
    {
        $sql = '
            SELECT ref_cod_turma as id,
                   turma.nm_turma as nome,
                   turma.tipo_boletim
              FROM pmieducar.matricula_turma,
                   pmieducar.turma
             WHERE ref_cod_matricula = $1
               AND turma.cod_turma = ref_cod_turma
               AND (matricula_turma.ativo = 1 OR matricula_turma.transferido = TRUE)
             LIMIT 1
        ';

        $turma = Portabilis_Utils_Database::selectRow($sql, $matriculaId);
        $turma['nome'] = $this->toUtf8($turma['nome'], ['transform' => true]);

        return $turma;
    }

    protected function loadEscolaNome($id)
    {
        $escola = new clsPmieducarEscola();
        $escola->cod_escola = $id;
        $escola = $escola->detalhe();

        $schoolInep = SchoolInep::query()
            ->select('cod_escola_inep')
            ->where('cod_escola', $id)
            ->value('cod_escola_inep');

        return $this->toUtf8($escola['nome'] . ' - INEP: ' . $schoolInep, ['transform' => true]);
    }

    protected function loadCursoNome($id)
    {
        $curso = new clsPmieducarCurso();
        $curso->cod_curso = $id;
        $curso = $curso->detalhe();

        return $this->toUtf8($curso['nm_curso'], ['transform' => true]);
    }

    protected function loadSerieNome($id)
    {
        $serie = new clsPmieducarSerie();
        $serie->cod_serie = $id;
        $serie = $serie->detalhe();

        return $this->toUtf8($serie['nm_serie'], ['transform' => true]);
    }

    protected function loadTransferenciaDataEntrada($matriculaId)
    {
        $sql = '
            select COALESCE(to_char(data_matricula,\'DD/MM/YYYY\'), to_char(data_cadastro, \'DD/MM/YYYY\')) from pmieducar.matricula
            where cod_matricula=$1 and ativo = 1
        ';

        return Portabilis_Utils_Database::selectField($sql, $matriculaId);
    }

    protected function loadNomeTurmaOrigem($matriculaId)
    {
        $sql = 'SELECT nm_turma
                  FROM pmieducar.matricula_turma mt
             LEFT JOIN pmieducar.turma t ON (t.cod_turma = mt.ref_cod_turma)
                 WHERE ref_cod_matricula = $1
                   AND mt.ativo = 0
                   AND mt.ref_cod_turma <> COALESCE((SELECT ref_cod_turma
                                                       FROM pmieducar.matricula_turma
                                                      WHERE ref_cod_matricula = $1
                                                        AND ativo = 1
                                                      LIMIT 1), 0)
              ORDER BY mt.data_exclusao DESC
                 LIMIT 1';

        return $this->toUtf8(Portabilis_Utils_Database::selectField($sql, $matriculaId), ['transform' => true]);
    }

    protected function loadTransferenciaDataSaida($matriculaId)
    {
        $sql = '
            select COALESCE(to_char(data_cancel,\'DD/MM/YYYY\'), to_char(data_exclusao, \'DD/MM/YYYY\')) from pmieducar.matricula
            where cod_matricula=$1 and ativo = 1 and ((aprovado=4 or aprovado=6) OR data_cancel is not null)
        ';

        return Portabilis_Utils_Database::selectField($sql, $matriculaId);
    }

    protected function possuiTransferenciaEmAberto($matriculaId)
    {
        $sql = '
            select count(cod_transferencia_solicitacao) from pmieducar.transferencia_solicitacao where
            ativo = 1 and ref_cod_matricula_saida = $1 and ref_cod_matricula_entrada is null and
            data_transferencia is null
        ';

        return Portabilis_Utils_Database::selectField($sql, $matriculaId) > 0;
    }

    protected function loadTipoOcorrenciaDisciplinar($id)
    {
        if (!isset($this->_tiposOcorrenciasDisciplinares)) {
            $this->_tiposOcorrenciasDisciplinares = [];
        }

        if (!isset($this->_tiposOcorrenciasDisciplinares[$id])) {
            $ocorrencia = LegacyDisciplinaryOccurrenceType::find($id)?->toArray();

            $this->_tiposOcorrenciasDisciplinares[$id] = $this->toUtf8(
                $ocorrencia['nm_tipo'],
                ['transform' => true]
            );
        }

        return $this->_tiposOcorrenciasDisciplinares[$id];
    }

    protected function loadOcorrenciasDisciplinares()
    {
        $escola = $this->getRequest()->escola;
        $modified = $this->getRequest()->modified;
        $alunoId = $this->getRequest()->aluno_id;

        if (is_array($alunoId)) {
            $alunoId = implode(',', $alunoId);
        }

        if (is_array($escola)) {
            $escola = implode(',', $escola);
        }

        $params = [];
        $where = '';

        if ($modified) {
            $where = ' AND od.updated_at >= $1';
            $params[] = $modified;
        }

        $alunoFilter = '';
        if ($alunoId) {
            $alunoFilter = "AND m.ref_cod_aluno in ({$alunoId})";
        }

        $sql = "
            select
                tod.nm_tipo as tipo,
                od.data_cadastro as data_hora,
                od.observacao as descricao,
                od.cod_ocorrencia_disciplinar as ocorrencia_disciplinar_id,
                m.ref_cod_aluno as aluno_id,
                m.ref_ref_cod_escola as escola_id,
                od.updated_at as updated_at,
                (
                    CASE WHEN od.ativo = 1 THEN
                        null
                    ELSE
                        od.data_exclusao::timestamp(0)
                    END
                ) as deleted_at
            from pmieducar.matricula_ocorrencia_disciplinar od
            inner join pmieducar.matricula m
            on m.cod_matricula = od.ref_cod_matricula
            inner join pmieducar.tipo_ocorrencia_disciplinar tod
            on tod.cod_tipo_ocorrencia_disciplinar = od.ref_cod_tipo_ocorrencia_disciplinar
            where true
                and od.visivel_pais = 1
                {$alunoFilter}
                and m.ref_ref_cod_escola IN ({$escola})
                {$where}
        ";

        $ocorrencias = $this->fetchPreparedQuery($sql, $params);

        $attrsFilter = [
            'tipo',
            'data_hora',
            'descricao',
            'ocorrencia_disciplinar_id',
            'aluno_id',
            'escola_id',
            'updated_at',
            'deleted_at',
        ];

        $ocorrencias = Portabilis_Array_Utils::filterSet($ocorrencias, $attrsFilter);

        return ['ocorrencias_disciplinares' => $ocorrencias];
    }

    protected function getGradeUltimoHistorico()
    {
        $sql = '
            SELECT historico_grade_curso_id as id
            FROM pmieducar.historico_escolar
            WHERE ref_cod_aluno = $1 AND ativo = 1
            ORDER BY sequencial DESC LIMIT 1
        ';

        $params = [$this->getRequest()->aluno_id];
        $grade_curso = $this->fetchPreparedQuery($sql, $params, false, 'first-row');

        return ['grade_curso' => $grade_curso['id']];
    }

    // search options
    protected function searchOptions()
    {
        $escolaId = $this->getRequest()->escola_id ? $this->getRequest()->escola_id : 0;

        return [
            'sqlParams' => [$escolaId],
            'selectFields' => ['matricula_id'],
        ];
    }

    protected function sqlsForNumericSearch()
    {
        $sqls = [];

        // caso nao receba id da escola, pesquisa por codigo aluno em todas as escolas,
        // alunos com e sem matricula são selecionados.
        if (!$this->getRequest()->escola_id) {
            $sqls[] = '
                select
                    distinct aluno.cod_aluno as id,
                    (case
                        when fisica.nome_social not like \'\' then
                            fisica.nome_social || \' - Nome de registro: \' || pessoa.nome
                        else
                            pessoa.nome
                    end) as name
                from
                    pmieducar.aluno,
                    cadastro.pessoa,
                    cadastro.fisica
                where true
                    and pessoa.idpes = aluno.ref_idpes
                    and fisica.idpes = aluno.ref_idpes
                    and aluno.ativo = 1
                    and aluno.cod_aluno::varchar(255) like $1||\'%\'
                    and $2 = $2
                order by
                    cod_aluno
                limit 15
            ';
        }

        $sqls[] = '
            select
                *
            from (
                select
                    distinct ON (aluno.cod_aluno) aluno.cod_aluno as id,
                    matricula.cod_matricula as matricula_id,
                    (case
                        when fisica.nome_social not like \'\' then
                            fisica.nome_social || \' - Nome de registro: \' || pessoa.nome
                        else
                            pessoa.nome
                    end) as name
                from
                    pmieducar.matricula,
                    pmieducar.aluno,
                    cadastro.pessoa,
                    cadastro.fisica
                where true
                    and aluno.cod_aluno = matricula.ref_cod_aluno
                    and pessoa.idpes = aluno.ref_idpes
                    and fisica.idpes = aluno.ref_idpes
                    and aluno.ativo = matricula.ativo
                    and matricula.ativo = 1
                    and (
                        select
                            case
                                when $2 != 0 then matricula.ref_ref_cod_escola = $2
                                else 1=1
                            end
                    )
                    and (matricula.ref_cod_aluno::varchar(255) like $1||\'%\')
                limit 15
            ) as alunos
            order by
                id
        ';

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $sqls = [];

        // caso nao receba id da escola, pesquisa por nome aluno em todas as escolas,
        // alunos com e sem matricula são selecionados.
        if (!$this->getRequest()->escola_id) {
            $sqls[] = '
                select
                    distinct aluno.cod_aluno as id,
                    (case
                        when fisica.nome_social not like \'\' then
                            fisica.nome_social || \' - Nome de registro: \' || pessoa.nome
                        else
                            pessoa.nome
                    end) as name
                from
                    pmieducar.aluno,
                    cadastro.pessoa,
                    cadastro.fisica
                where true
                    and pessoa.idpes = aluno.ref_idpes
                    and fisica.idpes = aluno.ref_idpes
                    and aluno.ativo = 1
                    and lower(coalesce(fisica.nome_social, \'\') || pessoa.nome) like \'%\'||lower(($1))||\'%\'
                    and $2 = $2
                order by
                    name
                limit 15
            ';
        }

        // seleciona por nome aluno e e opcionalmente por codigo escola,
        // apenas alunos com matricula são selecionados.
        $sqls[] = '
            select
                *
            from (
                select
                    distinct ON (aluno.cod_aluno) aluno.cod_aluno as id,
                    matricula.cod_matricula as matricula_id,
                    (case
                        when fisica.nome_social not like \'\' then
                            fisica.nome_social || \' - Nome de registro: \' || pessoa.nome
                        else
                            pessoa.nome
                    end) as name
                from
                    pmieducar.matricula,
                    pmieducar.aluno,
                    cadastro.pessoa,
                    cadastro.fisica
                where true
                    and aluno.cod_aluno = matricula.ref_cod_aluno
                    and pessoa.idpes = aluno.ref_idpes
                    and fisica.idpes = aluno.ref_idpes
                    and aluno.ativo = matricula.ativo
                    and matricula.ativo = 1 and (
                        select
                            case
                                when $2 != 0 then matricula.ref_ref_cod_escola = $2
                                else 1=1
                            end
                    )
                    and pessoa.slug ilike \'%\'|| $1 ||\'%\'
                    and matricula.aprovado in (1, 2, 3, 4, 7, 8, 9)
                limit 15
            ) as alunos
            order by
                name
        ';

        return $sqls;
    }

    // api
    protected function tipoResponsavel($aluno)
    {
        $tipos = ['p' => 'pai', 'm' => 'mae', 'r' => 'outra_pessoa', 'a' => 'pai_mae'];
        $tipo = $tipos[$aluno['tipo_responsavel']];

        // no antigo cadastro de aluno, caso não fosse encontrado um tipo de responsavel
        // verificava se a pessoa possua responsavel, pai ou mãe, considerando como
        // responsavel um destes, na respectiva ordem, sendo assim esta api mantem
        // compatibilidade com o antigo cadastro.
        if (!$tipo) {
            $pessoa = new clsFisica();
            $pessoa->idpes = $aluno['pessoa_id'];
            $pessoa = $pessoa->detalhe();

            if ($pessoa['idpes_responsavel'] || $pessoa['nome_responsavel']) {
                $tipo = $tipos['r'];
            } elseif ($pessoa['idpes_pai'] || $pessoa['nome_pai']) {
                $tipo = $tipos['p'];
            } elseif ($pessoa['idpes_mae'] || $pessoa['nome_mae']) {
                $tipo = $tipos['m'];
            }
        }

        return $tipo;
    }

    protected function loadBeneficios($alunoId)
    {
        $sql = '
            select aluno_beneficio_id as id, nm_beneficio as nome from pmieducar.aluno_aluno_beneficio,
            pmieducar.aluno_beneficio where aluno_beneficio_id = cod_aluno_beneficio and aluno_id = $1
        ';

        $beneficios = $this->fetchPreparedQuery($sql, $alunoId, false);

        // transforma array de arrays em array chave valor
        $_beneficios = [];

        foreach ($beneficios as $beneficio) {
            $nome = $this->toUtf8($beneficio['nome'], ['transform' => true]);
            $_beneficios[$beneficio['id']] = $nome;
        }

        return $_beneficios;
    }

    protected function loadProjetos($alunoId)
    {
        $sql = '
            SELECT cod_projeto, nome, data_inclusao, data_desligamento, turno
            FROM pmieducar.projeto_aluno, pmieducar.projeto
            WHERE ref_cod_projeto = cod_projeto
            AND ref_cod_aluno = $1
        ';

        $projetos = $this->fetchPreparedQuery($sql, $alunoId, false);

        // transforma array de arrays em array chave valor
        $_projetos = [];
        foreach ($projetos as $projeto) {
            $nome = $this->toUtf8($projeto['nome'], ['transform' => true]);
            $_projetos[] = [
                'projeto_cod_projeto' => $projeto['cod_projeto'] . ' - ' . $nome,
                'projeto_data_inclusao' => Portabilis_Date_Utils::pgSQLToBr($projeto['data_inclusao']),
                'projeto_data_desligamento' => Portabilis_Date_Utils::pgSQLToBr($projeto['data_desligamento']),
                'projeto_turno' => $projeto['turno'],
            ];
        }

        return $_projetos;
    }

    protected function loadHistoricoAlturaPeso($alunoId)
    {
        $sql = '
            SELECT to_char(data_historico, \'dd/mm/yyyy\') AS data_historico, altura, peso
            FROM pmieducar.aluno_historico_altura_peso
            WHERE ref_cod_aluno = $1
        ';

        $historicoAlturaPeso = $this->fetchPreparedQuery($sql, $alunoId, false);

        // transforma array de arrays em array chave valor
        $_historicoAlturaPeso = [];

        foreach ($historicoAlturaPeso as $alturaPeso) {
            $_historicoAlturaPeso[] = [
                'data_historico' => $alturaPeso['data_historico'],
                'altura' => $alturaPeso['altura'],
                'peso' => $alturaPeso['peso'],
            ];
        }

        return $_historicoAlturaPeso;
    }

    protected function get()
    {
        if ($this->canGet()) {
            $id = $this->getRequest()->id;

            $alunoDetalhe = (new clsPmieducarAluno($id))->detalhe();

            $attrs = [
                'cod_aluno' => 'id',
                'ref_cod_aluno_beneficio' => 'beneficio_id',
                'ref_idpes' => 'pessoa_id',
                'tipo_responsavel' => 'tipo_responsavel',
                'ref_usuario_exc' => 'destroyed_by',
                'data_exclusao' => 'destroyed_at',
                'analfabeto',
                'ativo',
                'aluno_estado_id',
                'recursos_prova_inep',
                'recebe_escolarizacao_em_outro_espaco',
                'justificativa_falta_documentacao',
                'veiculo_transporte_escolar',
                'url_laudo_medico',
                'url_documento',
                'codigo_sistema',
                'url_foto_aluno',
                'autorizado_um',
                'parentesco_um',
                'autorizado_dois',
                'parentesco_dois',
                'autorizado_tres',
                'parentesco_tres',
                'autorizado_quatro',
                'parentesco_quatro',
                'autorizado_cinco',
                'parentesco_cinco',
                'emancipado',
                'tipo_transporte',
            ];

            $aluno = Portabilis_Array_Utils::filter($alunoDetalhe, $attrs);

            $aluno['nome'] = $this->loadNomeAluno($id);
            $aluno['tipo_transporte'] = $this->loadTransporte($aluno['tipo_transporte']);
            $aluno['tipo_responsavel'] = $this->tipoResponsavel($aluno);
            $aluno['aluno_inep_id'] = $this->loadAlunoInepId($id);
            $aluno['ativo'] = $aluno['ativo'] == 1;

            $aluno['veiculo_transporte_escolar'] = Portabilis_Utils_Database::pgArrayToArray($aluno['veiculo_transporte_escolar']);
            $aluno['alfabetizado'] = $aluno['analfabeto'] == 0;
            unset($aluno['analfabeto']);

            // destroyed_by username
            $dataMapper = $this->getDataMapperFor('usuario', 'funcionario');
            $entity = $this->tryGetEntityOf($dataMapper, $aluno['destroyed_by']);

            $aluno['destroyed_by'] = is_null($entity) ? null : $entity->get('matricula');
            $aluno['destroyed_at'] = Portabilis_Date_Utils::pgSQLToBr($aluno['destroyed_at']);

            $objFichaMedica = new clsModulesFichaMedicaAluno($id);

            if ($objFichaMedica->existe()) {
                $objFichaMedica = $objFichaMedica->detalhe();

                foreach ($objFichaMedica as $chave => $value) {
                    $objFichaMedica[$chave] = Portabilis_String_Utils::toUtf8($value);
                }

                $aluno = Portabilis_Array_Utils::merge($objFichaMedica, $aluno);
            }

            $objMoradia = new clsModulesMoradiaAluno($id);
            if ($objMoradia->existe()) {
                $objMoradia = $objMoradia->detalhe();
                $aluno = Portabilis_Array_Utils::merge($objMoradia, $aluno);
            }

            // TODO remover no futuro #transport-package
            if (class_exists(clsModulesPessoaTransporte::class)) {
                $objPessoaTransporte = new clsModulesPessoaTransporte(null, null, $aluno['pessoa_id']);
                $objPessoaTransporte = $objPessoaTransporte->detalhe();

                if ($objPessoaTransporte) {
                    $aluno = Portabilis_Array_Utils::merge($objPessoaTransporte, $aluno);
                }
            }

            $sql = 'select sus, ref_cod_religiao, observacao from cadastro.fisica where idpes = $1';
            $camposFisica = $this->fetchPreparedQuery($sql, $aluno['pessoa_id'], false, 'first-row');

            $aluno['sus'] = $camposFisica['sus'];
            $aluno['observacao_aluno'] = $camposFisica['observacao'];
            $aluno['religiao_id'] = $camposFisica['ref_cod_religiao'];
            $aluno['beneficios'] = $this->loadBeneficios($id);
            $aluno['projetos'] = $this->loadProjetos($id);
            $aluno['historico_altura_peso'] = $this->loadHistoricoAlturaPeso($id);

            $objFoto = new clsCadastroFisicaFoto($aluno['pessoa_id']);
            $detalheFoto = $objFoto->detalhe();

            if ($detalheFoto) {
                $aluno['url_foto_aluno'] = $detalheFoto['caminho'];
            }

            return $aluno;
        }
    }

    protected function getTodosAlunos()
    {
        if ($this->canGetTodosAlunos()) {
            $modified = $this->getRequest()->modified;
            $escola = $this->getRequest()->escola;
            $ano = $this->getRequest()->ano ?? null;
            $cursando = $this->getRequest()->apenas_cursando ?? null;

            if (is_array($escola)) {
                $escola = implode(', ', $escola);
            }

            $params = [];

            $where = '';
            $whereAno = '';
            $whereCursando = '';
            $whereDeleteds = '';

            if ($modified) {
                $params[] = $modified;
                $where = 'AND greatest(p.data_rev::timestamp(0), f.data_rev::timestamp(0), a.updated_at, ff.updated_at) >= $1';
                $whereDeleteds = 'AND aluno_excluidos.deleted_at >= $1';
            }

            if ($ano) {
                $ano = intval($ano);
                $whereAno = " AND ano = {$ano}";
            }

            if ($cursando) {
                $whereCursando = ' AND aprovado = 3';
            }

            $sql = "
                SELECT a.cod_aluno AS aluno_id,
                p.nome as nome_aluno,
                f.nome_social,
                f.data_nasc as data_nascimento,
                ff.caminho as foto_aluno,
                greatest(p.data_rev::timestamp(0), f.data_rev::timestamp(0), a.updated_at, ff.updated_at) as updated_at,
                (
                    CASE WHEN a.ativo = 0 THEN
                        p.data_rev::timestamp(0)
                    ELSE
                        null
                    END
                ) as deleted_at
                FROM pmieducar.aluno a
                INNER JOIN cadastro.pessoa p ON p.idpes = a.ref_idpes
                INNER JOIN cadastro.fisica f ON f.idpes = p.idpes
                LEFT JOIN cadastro.fisica_foto ff ON p.idpes = ff.idpes
                WHERE TRUE
                and exists (
                    select *
                    from pmieducar.matricula
                    where ref_ref_cod_escola in ({$escola})
                    and ref_cod_aluno = a.cod_aluno
                    $whereAno
                    $whereCursando
                )
                {$where}

                UNION ALL

                SELECT aluno_excluidos.cod_aluno AS aluno_id,
                COALESCE(p.nome, '-') as nome_aluno,
                f.nome_social,
                COALESCE(f.data_nasc, CURRENT_DATE) as data_nascimento,
                ff.caminho as foto_aluno,
                greatest(p.data_rev::timestamp(0), f.data_rev::timestamp(0), aluno_excluidos.updated_at, ff.updated_at) as updated_at,
                aluno_excluidos.deleted_at as deleted_at
                FROM pmieducar.aluno_excluidos
                LEFT JOIN cadastro.pessoa p ON p.idpes = aluno_excluidos.ref_idpes
                LEFT JOIN cadastro.fisica f ON f.idpes = p.idpes
                LEFT JOIN cadastro.fisica_foto ff ON p.idpes = ff.idpes
                WHERE TRUE
                and exists (
                    select *
                    from pmieducar.matricula
                    where ref_ref_cod_escola in ({$escola})
                    and ref_cod_aluno = aluno_excluidos.cod_aluno
                    $whereAno
                    $whereCursando
                )
                {$whereDeleteds}

                ORDER BY updated_at, nome_aluno ASC
            ";

            $alunos = $this->fetchPreparedQuery($sql, $params);

            $alunos = Portabilis_Array_Utils::filterSet($alunos, [
                'aluno_id', 'nome_aluno', 'nome_social', 'foto_aluno',
                'data_nascimento', 'updated_at', 'deleted_at',
            ]);

            return [
                'alunos' => $alunos,
            ];
        }
    }

    protected function getIdpesFromCpf($cpf)
    {
        $sql = 'SELECT idpes FROM cadastro.fisica WHERE cpf = $1';

        return $this->fetchPreparedQuery($sql, $cpf, true, 'first-field');
    }

    protected function checkAlunoIdpesGuardian($idpesGuardian, $alunoId)
    {
        $sql = '
            SELECT 1
            FROM pmieducar.aluno
            INNER JOIN cadastro.fisica ON (aluno.ref_idpes = fisica.idpes)
            WHERE cod_aluno = $2
            AND (idpes_pai = $1
            OR idpes_mae = $1
            OR idpes_responsavel = $1) LIMIT 1
        ';

        return $this->fetchPreparedQuery($sql, [$idpesGuardian, $alunoId], true, 'first-field') == 1;
    }

    protected function getAlunosByGuardianCpf()
    {
        if ($this->canGetAlunosByGuardianCpf()) {
            $cpf = $this->getRequest()->cpf;
            $alunoId = $this->getRequest()->aluno_id;

            $idpesGuardian = $this->getIdpesFromCpf($cpf);

            if (is_numeric($idpesGuardian) && $this->checkAlunoIdpesGuardian($idpesGuardian, $alunoId)) {
                $sql = '
                    SELECT cod_aluno as aluno_id, pessoa.nome as nome_aluno
                    FROM pmieducar.aluno
                    INNER JOIN cadastro.fisica ON (aluno.ref_idpes = fisica.idpes)
                    INNER JOIN cadastro.pessoa ON (pessoa.idpes = fisica.idpes)
                    WHERE idpes_pai = $1
                    OR idpes_mae = $1
                    OR idpes_responsavel = $1
                ';

                $alunos = $this->fetchPreparedQuery($sql, [$idpesGuardian]);
                $attrs = ['aluno_id', 'nome_aluno'];
                $alunos = Portabilis_Array_Utils::filterSet($alunos, $attrs);

                foreach ($alunos as &$aluno) {
                    $aluno['nome_aluno'] = Portabilis_String_Utils::toUtf8($aluno['nome_aluno']);
                }

                return ['alunos' => $alunos];
            } else {
                $this->messenger->append('Não foi encontrado nenhum vínculos entre esse aluno e cpf.');
            }
        }
    }

    protected function getMatriculas()
    {
        if ($this->canGetMatriculas()) {
            $matriculas = new clsPmieducarMatricula();
            $matriculas->setOrderby('ano DESC, coalesce(m.data_matricula, m.data_cadastro) DESC, (CASE WHEN dependencia THEN 1 ELSE 0 END), nm_serie DESC, cod_matricula DESC, aprovado');

            $only_valid_boletim = $this->getRequest()->only_valid_boletim;

            $matriculas = $matriculas->lista(
                null,
                null,
                null,
                null,
                null,
                null,
                $this->getRequest()->aluno_id,
                null,
                null,
                null,
                null,
                null,
                1
            );

            $attrs = [
                'cod_matricula' => 'id',
                'ref_cod_instituicao' => 'instituicao_id',
                'ref_ref_cod_escola' => 'escola_id',
                'ref_cod_curso' => 'curso_id',
                'ref_ref_cod_serie' => 'serie_id',
                'ref_cod_aluno' => 'aluno_id',
                'nome' => 'aluno_nome',
                'aprovado' => 'situacao',
                'ano',
                'dependencia',
            ];

            $matriculas = Portabilis_Array_Utils::filterSet($matriculas, $attrs);

            foreach ($matriculas as $index => $matricula) {
                $turma = $this->loadTurmaByMatriculaId($matricula['id']);

                if (dbBool($only_valid_boletim) && (is_null($turma['id']) || is_null($turma['tipo_boletim']))) {
                    unset($matriculas[$index]);

                    continue;
                }

                $matriculas[$index]['aluno_nome'] = $this->toUtf8($matricula['aluno_nome'], ['transform' => true]);
                $matriculas[$index]['turma_id'] = $turma['id'];
                $matriculas[$index]['turma_nome'] = $turma['nome'];
                $matriculas[$index]['escola_nome'] = $this->loadEscolaNome($matricula['escola_id']);
                $matriculas[$index]['curso_nome'] = $this->loadCursoNome($matricula['curso_id']);
                $matriculas[$index]['serie_nome'] = $this->loadSerieNome($matricula['serie_id']);
                $matriculas[$index]['ultima_enturmacao'] = $this->loadNomeTurmaOrigem($matricula['id']);
                $matriculas[$index]['data_entrada'] = $this->loadTransferenciaDataEntrada($matricula['id']);
                $matriculas[$index]['data_saida'] = $this->loadTransferenciaDataSaida($matricula['id']);

                $matriculas[$index]['situacao'] = App_Model_MatriculaSituacao::getInstance()->getValue(
                    $matricula['situacao']
                );

                $matriculas[$index]['codigo_situacao'] = $matricula['situacao'];
                $matriculas[$index]['user_can_access'] = Portabilis_Utils_User::canAccessEscola($matricula['escola_id']);
                $matriculas[$index]['user_can_change_date'] = $this->loadAcessoDataEntradaSaida();
                $matriculas[$index]['user_can_change_situacao'] = $this->isUsuarioAdmin();
                $matriculas[$index]['transferencia_em_aberto'] = $this->possuiTransferenciaEmAberto($matricula['id']);
            }

            $attrs = [
                'id',
                'instituicao_id',
                'escola_id',
                'curso_id',
                'serie_id',
                'aluno_id',
                'aluno_nome',
                'situacao',
                'ano',
                'turma_id',
                'turma_nome',
                'escola_nome',
                'escola_nome',
                'curso_nome',
                'serie_nome',
                'ultima_enturmacao',
                'data_entrada',
                'data_entrada',
                'data_saida',
                'user_can_access',
                'user_can_change_date',
                'codigo_situacao',
                'user_can_change_situacao',
                'transferencia_em_aberto',
                'dependencia',
            ];

            $matriculas = Portabilis_Array_Utils::filterSet($matriculas, $attrs);

            return ['matriculas' => $matriculas];
        }
    }

    protected function saveParents()
    {
        $maeId = $this->getRequest()->mae_id;
        $paiId = $this->getRequest()->pai_id;

        if (!empty($maeId) && !empty($paiId) && $maeId == $paiId) {
            $this->messenger->append('Não é possível informar a mesma pessoa para Pai e Mãe.');

            return false;
        }

        $pessoaId = $this->getRequest()->pessoa_id;

        $sql = 'UPDATE cadastro.fisica set ';

        $virgulaOuNada = '';

        if ($maeId) {
            $sql .= " idpes_mae = {$maeId} ";
            $virgulaOuNada = ', ';
        } elseif ($maeId == '') {
            $sql .= ' idpes_mae = NULL ';
            $virgulaOuNada = ', ';
        }

        if ($paiId) {
            $sql .= "{$virgulaOuNada} idpes_pai = {$paiId} ";
            $virgulaOuNada = ', ';
        } elseif ($paiId == '') {
            $sql .= "{$virgulaOuNada} idpes_pai = NULL ";
            $virgulaOuNada = ', ';
        }

        $sql .= " WHERE idpes = {$pessoaId}";
        Portabilis_Utils_Database::fetchPreparedQuery($sql);

        return true;
    }

    protected function getOcorrenciasDisciplinares()
    {
        if ($this->canGetOcorrenciasDisciplinares()) {
            return $this->loadOcorrenciasDisciplinares();
        }
    }

    public function updateBeneficios($id)
    {
        LegacyStudentBenefit::query()->where('aluno_id', $id)->delete();
        foreach ($this->getRequest()->beneficios as $beneficioId) {
            if (!empty($beneficioId)) {
                $alunoBeneficio = new LegacyStudentBenefit();
                $alunoBeneficio->aluno_id = $id;
                $alunoBeneficio->aluno_beneficio_id = $beneficioId;

                $alunoBeneficio->save();
            }
        }
    }

    protected function retornaCodigo($palavra)
    {
        return substr($palavra, 0, strpos($palavra, ' -'));
    }

    public function saveProjetos($alunoId)
    {
        LegacyStudentProject::query()->where('ref_cod_aluno', $alunoId)->delete();

        foreach ($this->getRequest()->projeto_turno as $key => $value) {
            $projetoId = $this->retornaCodigo($this->getRequest()->projeto_cod_projeto[$key]);

            if (is_numeric($projetoId)) {
                $dataInclusao = Portabilis_Date_Utils::brToPgSQL($this->getRequest()->projeto_data_inclusao[$key]);
                $dataDesligamento = Portabilis_Date_Utils::brToPgSQL($this->getRequest()->projeto_data_desligamento[$key]);
                $turnoId = $value;

                if (is_numeric($projetoId) && is_numeric($turnoId) && !empty($dataInclusao)) {
                    if ($this->validaTurnoProjeto($alunoId, $turnoId)) {
                        $exists = LegacyStudentProject::query()->where('ref_cod_aluno', $alunoId)
                            ->whereNull('data_desligamento')
                            ->where('ref_cod_projeto', $projetoId)
                            ->exists();
                        if ($exists) {
                            $this->messenger->append('O aluno não pode ser cadastrado no mesmo projeto mais de uma vez.');
                        } else {
                            $exists = LegacyStudentProject::query()->where('ref_cod_aluno', $alunoId)
                                ->where('data_desligamento', '>=', $dataInclusao)
                                ->where('ref_cod_projeto', $projetoId)
                                ->exists();
                            if ($exists) {
                                $this->messenger->append('A data de inclusão deve ser superior ao desligamento anterior do mesmo curso.');
                            } else {
                                $alunoProjeto = new LegacyStudentProject();
                                $alunoProjeto->ref_cod_aluno = $alunoId;
                                $alunoProjeto->data_inclusao = $dataInclusao;
                                if ($dataDesligamento && $dataDesligamento != '') {
                                    $alunoProjeto->data_desligamento = $dataDesligamento;
                                }
                                $alunoProjeto->ref_cod_projeto = $projetoId;
                                $alunoProjeto->turno = $turnoId;

                                $alunoProjeto->save();
                            }
                        }
                    } else {
                        $this->messenger->append('O aluno não pode ser cadastrado em projetos no mesmo turno em que estuda, por favor, verifique.');
                    }
                } else {
                    $this->messenger->append('Para cadastrar o aluno em um projeto é necessário no mínimo informar a data de inclusão e o turno.');
                }
            }
        }
    }

    public function saveHistoricoAlturaPeso($alunoId)
    {
        LegacyStudentHistoricalHeightWeight::query()
            ->where('ref_cod_aluno', $alunoId)
            ->delete();

        foreach ($this->getRequest()->data_historico as $key => $value) {
            $data_historico = Portabilis_Date_Utils::brToPgSQL($value);
            $altura = $this->getRequest()->historico_altura[$key];
            $peso = $this->getRequest()->historico_peso[$key];

            $obj = new LegacyStudentHistoricalHeightWeight();
            $obj->ref_cod_aluno = $alunoId;
            $obj->data_historico = $data_historico;
            $obj->altura = $altura;
            $obj->peso = $peso;

            if (!$obj->save()) {
                $this->messenger->append('Erro ao cadastrar histórico de altura e peso.');
            }
        }
    }

    protected function post()
    {
        if ($this->canPost()) {
            $id = $this->createOrUpdateAluno();
            $pessoaId = $this->getRequest()->pessoa_id;

            if (!$this->saveParents()) {
                return [];
            }

            if (is_numeric($id)) {
                $this->updateBeneficios($id);
                $this->updateResponsavel();
                $this->saveSus($pessoaId);
                $this->createUpdateOrDestroyEducacensoAluno($id);
                $this->updateDeficiencias();
                $this->createOrUpdateFichaMedica($id);
                $this->createOrUpdateMoradia($id);
                $this->saveProjetos($id);
                $this->saveHistoricoAlturaPeso($id);
                $this->createOrUpdatePessoaTransporte($pessoaId);
                $this->createOrUpdateDocumentos($pessoaId);
                $this->createOrUpdatePessoa($pessoaId);

                $this->messenger->append('Cadastrado realizado com sucesso', 'success', false, 'error');
            } else {
                $this->messenger->append('Aparentemente o aluno não pode ser cadastrado, por favor, verifique.');
            }
        }

        return ['id' => $id];
    }

    protected function put()
    {
        $id = $this->getRequest()->id;
        $pessoaId = $this->getRequest()->pessoa_id;

        if (!$this->saveParents()) {
            return [];
        }

        if ($this->canPut() && $this->createOrUpdateAluno($id)) {
            $this->updateBeneficios($id);
            $this->updateResponsavel();
            $this->saveSus($pessoaId);
            $this->createUpdateOrDestroyEducacensoAluno($id);
            $this->updateDeficiencias();
            $this->createOrUpdateFichaMedica($id);
            $this->createOrUpdateMoradia($id);
            $this->saveProjetos($id);
            $this->saveHistoricoAlturaPeso($id);
            $this->createOrUpdatePessoaTransporte($pessoaId);
            $this->createOrUpdateDocumentos($pessoaId);
            $this->createOrUpdatePessoa($pessoaId);

            $this->messenger->append('Cadastro alterado com sucesso', 'success', false, 'error');
        } else {
            $this->messenger->append('Aparentemente o cadastro não pode ser alterado, por favor, verifique.', 'error', false, 'error');
        }

        return ['id' => $id];
    }

    protected function createOrUpdatePessoaTransporte($ref_idpes)
    {
        // TODO remover no futuro o uso deste método createOrUpdatePessoaTransporte #transport-package
        if (class_exists(clsModulesPessoaTransporte::class) === false) {
            return;
        }

        $pt = new clsModulesPessoaTransporte(null, null, $ref_idpes);
        $det = $pt->detalhe();

        $id = $det['cod_pessoa_transporte'];

        $pt = new clsModulesPessoaTransporte($id);
        // após cadastro não muda mais id pessoa
        $pt->ref_idpes = $ref_idpes;
        $pt->ref_idpes_destino = $this->getRequest()->pessoaj_id;
        $pt->ref_cod_ponto_transporte_escolar = $this->getRequest()->transporte_ponto;
        $pt->ref_cod_rota_transporte_escolar = $this->getRequest()->transporte_rota;
        $pt->observacao = $this->getRequest()->transporte_observacao;

        return is_null($id) ? $pt->cadastra() : $pt->edita();
    }

    protected function enable()
    {
        $id = $this->getRequest()->id;

        if ($this->canEnable()) {
            $aluno = new clsPmieducarAluno();
            $aluno->cod_aluno = $id;
            $aluno->ref_usuario_exc = \Illuminate\Support\Facades\Auth::id();
            $aluno->ativo = 1;

            if ($aluno->edita()) {
                $this->messenger->append('Cadastro ativado com sucesso', 'success', false, 'error');
            } else {
                $this->messenger->append('Aparentemente o cadastro não pode ser ativado, por favor, verifique.', 'error', false, 'error');
            }
        }

        return ['id' => $id];
    }

    protected function delete()
    {
        $id = $this->getRequest()->id;
        $matriculaAtiva = dbBool($this->possuiMatriculaAtiva($id));

        if (!$matriculaAtiva) {
            if ($this->canDelete()) {
                $aluno = new clsPmieducarAluno();
                $aluno->cod_aluno = $id;
                $aluno->ref_usuario_exc = \Illuminate\Support\Facades\Auth::id();

                $detalheAluno = $aluno->detalhe();

                if ($aluno->excluir()) {
                    $this->messenger->append('Cadastro removido com sucesso', 'success', false, 'error');
                } else {
                    $this->messenger->append('Aparentemente o cadastro não pode ser removido, por favor, verifique.', 'error', false, 'error');
                }
            }
        } else {
            $this->messenger->append('O cadastro não pode ser removido, pois existem matrículas vinculadas.', 'error', false, 'error');
        }

        return ['id' => $id];
    }

    protected function possuiMatriculaAtiva($alunoId)
    {
        $sql = 'select exists (select 1 from pmieducar.matricula where ref_cod_aluno = $1 and ativo = 1)';

        return Portabilis_Utils_Database::selectField($sql, $alunoId);
    }

    //envia foto e salva caminha no banco
    protected function savePhoto($id)
    {
        if ($this->objPhoto != null) {
            //salva foto com data, para evitar problemas com o cache do navegador
            $caminhoFoto = $this->objPhoto->sendPicture();

            if ($caminhoFoto != '') {
                //new clsCadastroFisicaFoto($id)->exclui();
                $obj = new clsCadastroFisicaFoto($id, $caminhoFoto);
                $detalheFoto = $obj->detalhe();

                if (is_array($detalheFoto) && count($detalheFoto) > 0) {
                    $obj->edita();
                } else {
                    $obj->cadastra();
                }

                return true;
            } else {
                echo '<script>alert(\'Foto não salva.\')</script>';

                return false;
            }
        } elseif ($this->del_foto == 'on') {
            $obj = new clsCadastroFisicaFoto($id);
            $obj->excluir();
        }
    }

    // Retorna true caso a foto seja válida
    protected function validatePhoto()
    {
        $this->arquivoFoto = $this->file_foto;

        if (!empty($this->arquivoFoto['name'])) {
            $this->arquivoFoto['name'] = mb_strtolower($this->arquivoFoto['name'], 'UTF-8');
            $this->objPhoto = new PictureController($this->arquivoFoto);

            if ($this->objPhoto->validatePicture()) {
                return true;
            } else {
                $this->messenger->append($this->objPhoto->getErrorMessage());

                return false;
            }

            return false;
        } else {
            $this->objPhoto = null;

            return true;
        }
    }

    protected function createOrUpdateDocumentos($pessoaId)
    {
        $documentos = new clsDocumento();
        $documentos->idpes = $pessoaId;

        // o tipo certidão novo padrão é apenas para exibição ao usuário,
        // não precisa ser gravado no banco
        //
        // quando selecionado um tipo diferente do novo formato,
        // é removido o valor de certidao_nascimento.
        if ($this->getRequest()->tipo_certidao_civil == CertificateType::BIRTH_NEW_FORMAT) {
            $documentos->tipo_cert_civil = null;
            $documentos->certidao_casamento = '';
            $documentos->certidao_nascimento = $this->getRequest()->certidao_nascimento;
        } elseif ($this->getRequest()->tipo_certidao_civil == CertificateType::MARRIAGE_NEW_FORMAT) {
            $documentos->tipo_cert_civil = null;
            $documentos->certidao_nascimento = '';
            $documentos->certidao_casamento = $this->getRequest()->certidao_casamento;
        } else {
            $documentos->tipo_cert_civil = $this->getRequest()->tipo_certidao_civil;
            $documentos->certidao_nascimento = '';
            $documentos->certidao_casamento = '';
        }

        $documentos->num_termo = $this->getRequest()->termo_certidao_civil;
        $documentos->num_livro = $this->getRequest()->livro_certidao_civil;
        $documentos->num_folha = $this->getRequest()->folha_certidao_civil;

        $documentos->rg = trim($this->getRequest()->rg);
        $documentos->data_exp_rg = Portabilis_Date_Utils::brToPgSQL(
            $this->getRequest()->data_emissao_rg
        );
        $documentos->sigla_uf_exp_rg = $this->getRequest()->uf_emissao_rg;
        $documentos->idorg_exp_rg = $this->getRequest()->orgao_emissao_rg;

        $documentos->data_emissao_cert_civil = Portabilis_Date_Utils::brToPgSQL(
            $this->getRequest()->data_emissao_certidao_civil
        );

        $documentos->sigla_uf_cert_civil = $this->getRequest()->uf_emissao_certidao_civil;
        $documentos->cartorio_cert_civil = addslashes($this->getRequest()->cartorio_emissao_certidao_civil);
        $documentos->passaporte = addslashes($this->getRequest()->passaporte);

        // Alteração de documentos compativel com a versão anterior do cadastro,
        // onde era possivel criar uma pessoa, não informando os documentos,
        // o que não criaria o registro do documento, sendo assim, ao editar uma pessoa,
        // o registro do documento será criado, caso não exista.

        $sql = 'select 1 from cadastro.documento WHERE idpes = $1 limit 1';

        if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
            $documentos->cadastra();
        } else {
            $documentos->edita_aluno();
        }
    }

    protected function createOrUpdatePessoa($idPessoa)
    {
        $fisica = new clsFisica($idPessoa);
        $fisica->cpf = $this->getRequest()->id_federal ? idFederal2int($this->getRequest()->id_federal) : 'NULL';
        $fisica->ref_cod_religiao = $this->getRequest()->religiao_id;
        $fisica->nis_pis_pasep = $this->getRequest()->nis_pis_pasep ?: 'NULL';
        $fisica->observacao = $this->getRequest()->observacao_aluno ?: 'NULL';
        $fisica->renda_mensal = $this->getRequest()->renda_mensal ?: 'NULL';
        $fisica = $fisica->edita();
    }

    protected function loadAcessoDataEntradaSaida()
    {
        $acesso = new clsPermissoes();

        return $acesso->permissao_cadastra(626, $this->pessoa_logada, 7, null, true);
    }

    protected function isUsuarioAdmin()
    {
        if (Auth::user()) {
            return Auth::user()->isAdmin();
        }

        return false;
    }

    protected function canGetAlunosMatriculados()
    {
        return $this->validatesPresenceOf('instituicao_id') &&
            $this->validatesPresenceOf('escola_id') &&
            $this->validatesPresenceOf('data') &&
            $this->validatesPresenceOf('ano');
    }

    protected function getAlunosMatriculados()
    {
        if ($this->canGetAlunosMatriculados()) {
            $instituicaoId = $this->getRequest()->instituicao_id;
            $escolaId = $this->getRequest()->escola_id;
            $data = $this->getRequest()->data;
            $ano = $this->getRequest()->ano;
            $turnoId = $this->getRequest()->turno_id;
            $cursoId = $this->getRequest()->curso_id;
            $serieId = $this->getRequest()->serie_id;
            $turmaId = $this->getRequest()->turma_id;

            $sql = 'SELECT a.cod_aluno as aluno_id
                      FROM pmieducar.aluno a
                INNER JOIN pmieducar.matricula m ON m.ref_cod_aluno = a.cod_aluno
                INNER JOIN pmieducar.matricula_turma mt ON m.cod_matricula = mt.ref_cod_matricula
                INNER JOIN pmieducar.turma t ON mt.ref_cod_turma = t.cod_turma
                INNER JOIN cadastro.pessoa p ON p.idpes = a.ref_idpes
                INNER JOIN pmieducar.serie s ON s.cod_serie = m.ref_ref_cod_serie
                INNER JOIN pmieducar.curso c ON c.cod_curso = m.ref_cod_curso
                INNER JOIN pmieducar.escola e ON e.cod_escola = m.ref_ref_cod_escola
                     WHERE m.ativo = 1
                       AND a.ativo = 1
                       AND t.ativo = 1
                       AND t.ref_cod_instituicao = $1
                       AND e.cod_escola = $2
                       AND (CASE WHEN coalesce($3, current_date)::date = current_date
                            THEN mt.ativo = 1
                            ELSE
                            (CASE WHEN mt.ativo = 0 THEN
                            mt.sequencial = (select max(matricula_turma.sequencial)
                                               from pmieducar.matricula_turma
                                         inner join pmieducar.matricula on(matricula_turma.ref_cod_matricula = matricula.cod_matricula)
                                              where matricula_turma.ref_cod_matricula = mt.ref_cod_matricula
                                                and matricula_turma.ref_cod_turma = mt.ref_cod_turma
                                                and ($3::date >= matricula_turma.data_enturmacao::date
                                                     and $3::date < coalesce(matricula_turma.data_exclusao::date, matricula.data_cancel::date, current_date))
                                                and matricula_turma.ativo = 0
                                                and not exists(select 1
                                                                 from pmieducar.matricula_turma mt_sub
                                                                where mt_sub.ativo = 1
                                                                  and mt_sub.ref_cod_matricula = mt.ref_cod_matricula
                                                                  and mt_sub.ref_cod_turma = mt.ref_cod_turma
                                                              )
                                             )
                            ELSE
                            ($3::date >= mt.data_enturmacao::date
                            and $3::date < coalesce(m.data_cancel::date, mt.data_exclusao::date, current_date))
                       END)
                      END)
              AND t.ano = $4 ';

            $params = [$instituicaoId, $escolaId, $data, $ano];

            if (is_numeric($turnoId)) {
                $params[] = $turnoId;
                $sql .= 'AND t.turma_turno_id = $' . count($params) . ' ';
            }

            if (is_numeric($cursoId)) {
                $params[] = $cursoId;
                $sql .= 'AND c.cod_curso = $' . count($params) . ' ';
            }

            if (is_numeric($serieId)) {
                $params[] = $serieId;
                $sql .= 'AND s.cod_serie = $' . count($params) . ' ';
            }

            if (is_numeric($turmaId)) {
                $params[] = $turmaId;
                $sql .= 'AND t.cod_turma = $' . count($params) . ' ';
            }

            $sql .= ' ORDER BY (upper(p.nome))';

            $alunos = $this->fetchPreparedQuery($sql, $params);
            $attrs = ['aluno_id'];
            $alunos = Portabilis_Array_Utils::filterSet($alunos, $attrs);

            return ['alunos' => $alunos];
        }
    }

    protected function getNomeBairro()
    {
        $var1 = $this->getRequest()->id;

        $sql = "
            SELECT unaccent(upper(neighborhood)) AS nome
            FROM addresses a
            JOIN person_has_place php ON a.id = php.place_id
            JOIN pmieducar.aluno al ON al.ref_idpes = php.person_id
            WHERE al.cod_aluno = {$var1}
        ";

        $bairro = $this->fetchPreparedQuery($sql);

        return $bairro;
    }

    protected function getUnificacoes()
    {
        if (!$this->canGetUnificacoes()) {
            return;
        }

        $arrayEscola = explode(',', $this->getRequest()->escola);

        $unificationsQuery = LogUnification::query();
        $unificationsQuery->whereHas('studentMain', function ($studentQuery) use ($arrayEscola) {
            $studentQuery->whereHas('registrations', function ($registrationsQuery) use ($arrayEscola) {
                $registrationsQuery->whereIn('school_id', $arrayEscola);
            });
        });

        return ['unificacoes' => $unificationsQuery->get(['id', 'main_id', 'duplicates_id', 'created_at', 'active'])->all()];
    }

    protected function dadosUnificacaoAlunos()
    {
        $alunosIds = $this->getRequest()->alunos_ids ?? 0;

        $sql = "
            SELECT
                a.cod_aluno AS codigo,
                p.nome AS nome,
                coalesce(eca.cod_aluno_inep::varchar, 'Não consta') AS inep,
                coalesce(to_char(f.data_nasc, 'dd/mm/yyyy'), 'Não consta') AS data_nascimento,
                coalesce(f.cpf::varchar, 'Não consta') AS cpf,
                coalesce(d.rg, 'Não consta') AS rg,
                coalesce(relatorio.get_mae_aluno(a.cod_aluno), 'Não consta') AS mae_aluno
            FROM pmieducar.aluno a
            JOIN cadastro.pessoa p ON p.idpes = a.ref_idpes
            JOIN cadastro.fisica f ON f.idpes = a.ref_idpes
            LEFT JOIN cadastro.documento d ON d.idpes = a.ref_idpes
            LEFT JOIN modules.educacenso_cod_aluno eca ON eca.cod_aluno = a.cod_aluno
            WHERE a.cod_aluno IN ($alunosIds);
        ";

        $alunos = $this->fetchPreparedQuery($sql, [], false);

        $attrs = [
            'codigo',
            'nome',
            'inep',
            'data_nascimento',
            'cpf',
            'rg',
            'mae_aluno',
        ];

        return [
            'alunos' => Portabilis_Array_Utils::filterSet($alunos, $attrs),
        ];
    }

    protected function dadosMatriculasHistoricosAlunos()
    {
        $alunoId = $this->getRequest()->aluno_id;

        if (empty($alunoId)) {
            return;
        }

        $registrations = LegacyRegistration::query()
            ->where('ref_cod_aluno', $alunoId)
            ->with('school')
            ->orderBy('ano')
            ->get()
            ->map(function ($registration) {
                return [
                    'ano' => $registration->ano,
                    'escola' => $registration->school->name,
                    'curso' => $registration->course->name,
                    'serie' => $registration->grade->name,
                    'turma' => $registration->lastEnrollment->schoolClass->name,
                ];
            })
            ->toArray();

        $schoolHistories = LegacySchoolHistory::query()
            ->where('ref_cod_aluno', $alunoId)
            ->get()
            ->map(function ($schoolHistory) {
                $situacao = App_Model_MatriculaSituacao::getInstance()->getValue($schoolHistory->aprovado);

                return [
                    'ano' => $schoolHistory->ano,
                    'escola' => $schoolHistory->escola,
                    'curso' => $schoolHistory->nm_curso,
                    'serie' => $schoolHistory->nm_serie,
                    'situacao' => $situacao,
                ];
            })
            ->toArray();

        return [
            'matriculas' => $registrations,
            'historicos' => $schoolHistories,
        ];
    }

    protected function canGetUnificacoes()
    {
        return $this->validatesPresenceOf('escola');
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'aluno')) {
            $this->appendResponse($this->get());
        } elseif ($this->isRequestFor('get', 'aluno-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'alunos-matriculados')) {
            $this->appendResponse($this->getAlunosMatriculados());
        } elseif ($this->isRequestFor('get', 'matriculas')) {
            $this->appendResponse($this->getMatriculas());
        } elseif ($this->isRequestFor('get', 'todos-alunos')) {
            $this->appendResponse($this->getTodosAlunos());
        } elseif ($this->isRequestFor('get', 'ocorrencias_disciplinares')) {
            $this->appendResponse($this->getOcorrenciasDisciplinares());
        } elseif ($this->isRequestFor('get', 'grade_ultimo_historico')) {
            $this->appendResponse($this->getGradeUltimoHistorico());
        } elseif ($this->isRequestFor('get', 'alunos_by_guardian_cpf')) {
            $this->appendResponse($this->getAlunosByGuardianCpf());
        } elseif ($this->isRequestFor('post', 'aluno')) {
            $this->appendResponse($this->post());
        } elseif ($this->isRequestFor('put', 'aluno')) {
            $this->appendResponse($this->put());
        } elseif ($this->isRequestFor('enable', 'aluno')) {
            $this->appendResponse($this->enable());
        } elseif ($this->isRequestFor('delete', 'aluno')) {
            $this->appendResponse($this->delete());
        } elseif ($this->isRequestFor('get', 'get-nome-bairro')) {
            $this->appendResponse($this->getNomeBairro());
        } elseif ($this->isRequestFor('get', 'unificacao-alunos')) {
            $this->appendResponse($this->getUnificacoes());
        } elseif ($this->isRequestFor('get', 'dadosUnificacaoAlunos')) {
            $this->appendResponse($this->dadosUnificacaoAlunos());
        } elseif ($this->isRequestFor('get', 'dadosMatriculasHistoricosAlunos')) {
            $this->appendResponse($this->dadosMatriculasHistoricosAlunos());
        } elseif ($this->isRequestFor('get', 'deve-habilitar-campo-recursos-prova-inep')) {
            $this->appendResponse($this->deveHabilitarCampoRecursosProvaInep());
        } elseif ($this->isRequestFor('get', 'deve-obrigar-laudo-medico')) {
            $this->appendResponse($this->deveObrigarLaudoMedico());
        } else {
            $this->notImplementedOperationError();
        }
    }

    private function replaceByEducacensoDeficiencies($deficiencies)
    {
        $databaseDeficiencies = LegacyDeficiency::all()->getKeyValueArray('deficiencia_educacenso');

        $arrayEducacensoDeficiencies = [];
        foreach ($deficiencies as $deficiency) {
            $arrayEducacensoDeficiencies[] = $databaseDeficiencies[$deficiency];
        }

        return $arrayEducacensoDeficiencies;
    }

    private function deveHabilitarCampoRecursosProvaInep()
    {
        // Pega os códigos das deficiências do censo
        $deficiencias = $this->replaceByEducacensoDeficiencies(array_filter(explode(',', $this->getRequest()->deficiencias)));

        // Remove "Altas Habilidades"
        $deficiencias = Registro30::removeAltasHabilidadesArrayDeficiencias($deficiencias);

        return [
            'result' => !empty($deficiencias),
        ];
    }

    private function deveObrigarLaudoMedico()
    {
        $deficiencias = array_filter(explode(',', $this->getRequest()->deficiencias));

        return [
            'result' => LegacyDeficiency::whereIn('cod_deficiencia', $deficiencias)
                ->where('exigir_laudo_medico', true)
                ->exists(),
        ];
    }
}
