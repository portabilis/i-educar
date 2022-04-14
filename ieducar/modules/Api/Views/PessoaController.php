<?php

use App\Models\LegacyIndividual;
use App\Models\PersonHasPlace;
use iEducar\Modules\Addressing\LegacyAddressingFields;
use iEducar\Modules\Educacenso\Model\Nacionalidade;
use iEducar\Modules\Educacenso\Validator\BirthDateValidator;
use iEducar\Modules\Educacenso\Validator\DifferentiatedLocationValidator;
use iEducar\Modules\Educacenso\Validator\NameValidator;

class PessoaController extends ApiCoreController
{
    use LegacyAddressingFields;

    protected function canGet()
    {
        $can = true;

        if (! $this->getRequest()->id && ! $this->getRequest()->cpf) {
            $can = false;
            $this->messenger->append('É necessário receber uma variavel \'id\' ou \'cpf\'');
        } elseif ($this->getRequest()->id) {
            $can = $this->validatesResourceId();
        }

        return $can;
    }

    // validators
    // overwrite api core validator
    protected function validatesResourceId()
    {
        $existenceOptions = ['schema_name' => 'cadastro', 'field_name' => 'idpes'];

        return (
            $this->validatesPresenceOf('id') &&
            $this->validatesExistenceOf('fisica', $this->getRequest()->id, $existenceOptions)
        );
    }

    // load resources
    protected function tryLoadAlunoId($pessoaId)
    {
        $sql = 'select cod_aluno as id from pmieducar.aluno where ref_idpes = $1';
        $id = $this->fetchPreparedQuery($sql, $pessoaId, false, 'first-field');

        // caso um array vazio seja retornado, seta resultado como null,
        // evitando erro em loadDetails
        if (empty($id)) {
            $id = null;
        }

        return $id;
    }

    protected function loadPessoa($id = null)
    {
        $sql = '
            select
                idpes as id,
                nome
            from
                cadastro.pessoa
            where true
                and idpes = $1
        ';

        $pessoa = $this->fetchPreparedQuery($sql, $id, false, 'first-row');
        $pessoa['nome'] = $this->toUtf8($pessoa['nome'], ['transform' => true]);

        return $pessoa;
    }

    protected function loadPessoaByCpf($cpf = null)
    {
        $cpf = preg_replace('/[^0-9]/', '', (string)$cpf);

        if (! $cpf) {
            throw new Exception('CPF deve conter caracteres numéricos');
        }

        $sql = 'select pessoa.idpes as id, nome from cadastro.pessoa, cadastro.fisica
            where fisica.idpes = pessoa.idpes and cpf = $1 limit 1';

        $pessoa = $this->fetchPreparedQuery($sql, $cpf, false, 'first-row');
        $pessoa['nome'] = $this->toUtf8($pessoa['nome'], ['transform' => true]);

        return $pessoa;
    }

    protected function loadDetails($pessoaId = null)
    {
        $alunoId = $this->tryLoadAlunoId($pessoaId);

        $sql = 'SELECT cpf, data_nasc as data_nascimento, idpes_pai as pai_id, ref_cod_religiao as religiao_id,
            idpes_mae as mae_id, idpes_responsavel as responsavel_id,
            ideciv as estadocivil, sexo, nis_pis_pasep,
            nome_social,
            coalesce((select nome from cadastro.pessoa where idpes = fisica.idpes_pai),
            (select nm_pai from pmieducar.aluno where cod_aluno = $1)) as nome_pai,
            coalesce((select nome from cadastro.pessoa where idpes = fisica.idpes_mae),
            (select nm_mae from pmieducar.aluno where cod_aluno = $1)) as nome_mae,
            (select nome from cadastro.pessoa where idpes = fisica.idpes_responsavel) as nome_responsavel,
            (select rg from cadastro.documento where documento.idpes = fisica.idpes) as rg,
            (select sigla_uf_exp_rg from cadastro.documento where documento.idpes = fisica.idpes) as uf_emissao_rg,
            (select idorg_exp_rg from cadastro.documento where documento.idpes = fisica.idpes) as orgao_emissao_rg,
            (select data_exp_rg from cadastro.documento where documento.idpes = fisica.idpes) as data_emissao_rg,
            (select tipo_cert_civil from cadastro.documento where documento.idpes = fisica.idpes) as tipo_cert_civil,

            (select data_emissao_cert_civil from cadastro.documento where documento.idpes = fisica.idpes) as data_emissao_cert_civil,
            (select sigla_uf_cert_civil from cadastro.documento where documento.idpes = fisica.idpes) as sigla_uf_cert_civil,
            (select cartorio_cert_civil_inep from cadastro.documento where documento.idpes = fisica.idpes) as cartorio_cert_civil_inep,
            (select cartorio_cert_civil from cadastro.documento where documento.idpes = fisica.idpes) as cartorio_cert_civil,
            (select id_cartorio FROM cadastro.codigo_cartorio_inep, cadastro.documento WHERE codigo_cartorio_inep.id = documento.cartorio_cert_civil_inep AND documento.idpes = fisica.idpes) as id_cartorio,
            (select descricao FROM cadastro.codigo_cartorio_inep, cadastro.documento WHERE codigo_cartorio_inep.id = documento.cartorio_cert_civil_inep AND documento.idpes = fisica.idpes) as nome_cartorio,


            (select num_termo from cadastro.documento where documento.idpes = fisica.idpes) as num_termo,
            (select num_livro from cadastro.documento where documento.idpes = fisica.idpes) as num_livro,
            (select num_folha from cadastro.documento where documento.idpes = fisica.idpes) as num_folha,
            (select certidao_nascimento from cadastro.documento where documento.idpes = fisica.idpes) as certidao_nascimento,
            (select certidao_casamento from cadastro.documento where documento.idpes = fisica.idpes) as certidao_casamento,
              idmun_nascimento,
             fisica.idpais_estrangeiro as pais_origem_id,
           fisica.nacionalidade as tipo_nacionalidade,
           fisica.zona_localizacao_censo,
           fisica.localizacao_diferenciada,
           fisica.nome_social,
           (SELECT pais.nome
                   FROM public.pais
                   WHERE pais.idpais = fisica.idpais_estrangeiro) AS pais_origem_nome,
           (SELECT ref_cod_raca FROM cadastro.fisica_raca WHERE fisica.idpes = fisica_raca.ref_idpes) as cor_raca,
              (SELECT fone_pessoa.fone FROM cadastro.fone_pessoa WHERE fone_pessoa.idpes = $2 AND fone_pessoa.tipo = 1) as fone_fixo,
              (SELECT fone_pessoa.fone FROM cadastro.fone_pessoa WHERE fone_pessoa.idpes = $2 AND fone_pessoa.tipo = 2) as fone_mov,
              (SELECT fone_pessoa.ddd FROM cadastro.fone_pessoa WHERE fone_pessoa.idpes = $2 AND fone_pessoa.tipo = 1) as ddd_fone_fixo,
              (SELECT fone_pessoa.ddd FROM cadastro.fone_pessoa WHERE fone_pessoa.idpes = $2 AND fone_pessoa.tipo = 2) as ddd_fone_mov,

             fisica.pais_residencia
            from cadastro.fisica
            where idpes = $2';

        $details = $this->fetchPreparedQuery($sql, [$alunoId, $pessoaId], false, 'first-row');

        $details['possui_documento'] = !(
            empty($details['cpf']) &&
            empty($details['nis_pis_pasep']) &&
            empty($details['certidao_nascimento'])
        );

        $attrs = [
            'cpf',
            'rg',
            'nis_pis_pasep',
            'data_nascimento',
            'religiao_id',
            'pai_id',
            'mae_id',
            'responsavel_id',
            'nome_pai',
            'nome_mae',
            'nome_responsavel',
            'sexo',
            'estadocivil',
            'tipo_cert_civil',
            'num_termo',
            'num_livro',
            'num_folha',
            'certidao_nascimento',
            'certidao_casamento',
            'idmun_nascimento',
            'possui_documento',
            'ddd_fone_fixo',
            'fone_fixo',
            'fone_mov',
            'ddd_fone_mov',
            'pais_origem_id',
            'tipo_nacionalidade',
            'zona_localizacao_censo',
            'localizacao_diferenciada',
            'pais_origem_nome',
            'cor_raca',
            'uf_emissao_rg',
            'orgao_emissao_rg',
            'data_emissao_rg',
            'data_emissao_cert_civil',
            'sigla_uf_cert_civil',
            'cartorio_cert_civil_inep',
            'cartorio_cert_civil',
            'id_cartorio',
            'nome_cartorio',
            'nome_social',
            'pais_residencia',
        ];

        $details = Portabilis_Array_Utils::filter($details, $attrs);

        $details['aluno_id'] = $alunoId;
        $details['nome_mae'] = $this->toUtf8($details['nome_mae'], ['transform' => true]);
        $details['nome_pai'] = $this->toUtf8($details['nome_pai'], ['transform' => true]);
        $details['nome_responsavel'] = $this->toUtf8($details['nome_responsavel'], ['transform' => true]);
        $details['cep'] = int2CEP($details['cep']);
        $details['cpf'] = int2CPF($details['cpf']);

        $details['nis_pis_pasep'] = int2Nis($details['nis_pis_pasep']);

        $details['num_termo'] = $this->toUtf8($details['num_termo']);
        $details['num_folha'] = $this->toUtf8($details['num_folha']);
        $details['num_livro'] = $this->toUtf8($details['num_livro']);
        $details['certidao_casamento'] = $this->toUtf8($details['certidao_casamento']);
        $details['certidao_nascimento'] = $this->toUtf8($details['certidao_nascimento']);

        $details['distrito'] = $this->toUtf8($details['distrito']);
        $details['logradouro'] = $this->toUtf8($details['logradouro']);
        $detaihandleGetPersonls['complemento'] = $this->toUtf8($details['complemento']);
        $details['ddd_fone_fixo'] = $this->toUtf8($details['ddd_fone_fixo']);
        $details['fone_fixo'] = $this->toUtf8($details['fone_fixo']);
        $details['ddd_fone_mov'] = $this->toUtf8($details['ddd_fone_mov']);
        $details['fone_mov'] = $this->toUtf8($details['fone_mov']);
        $details['falecido'] = $this->toUtf8($details['falecido']);

        $details['pais_origem_nome'] = $this->toUtf8($details['pais_origem_nome']);

        $has = PersonHasPlace::query()->with('place.city.state')->where('person_id', $pessoaId)->orderBy('type')->first();

        if ($has) {
            $place = $has->place;

            $details['id'] = $place->id;
            $details['postal_code'] = $place->postal_code;
            $details['address'] = $place->address;
            $details['number'] = $place->number;
            $details['complement'] = $place->complement;
            $details['neighborhood'] = $place->neighborhood;
            $details['city_id'] = $place->city_id;
            $details['city_name'] = $place->city->name;
            $details['state_abbreviation'] = $place->city->state->abbreviation;

            $details['cep'] = int2CEP($place->postal_code);
            $details['logradouro'] = $place->address;
            $details['idtlog'] = $place->id;
            $details['bairro'] = $place->neighborhood;
            $details['zona_localizacao'] = 1;
            $details['idmun'] = $place->city_id;
            $details['numero'] = $place->number;
            $details['letra'] = null;
            $details['complemento'] = $place->complement;
            $details['andar'] = null;
            $details['bloco'] = null;
            $details['apartamento'] = null;
            $details['idbai'] = $place->id;
            $details['iddis'] = null;
            $details['distrito'] = null;
            $details['idlog'] = $place->id;
            $details['municipio'] = $place->city->name;
            $details['sigla_uf'] = $place->city->state->abbreviation;
        }

        if ($details['idmun_nascimento']) {
            $_sql = ' SELECT nome, sigla_uf FROM public.municipio WHERE idmun = $1; ';
            $mun = $this->fetchPreparedQuery($_sql, $details['idmun_nascimento'], false, 'first-row');

            $details['municipio_nascimento'] = $this->toUtf8($mun['nome']);
            $details['sigla_uf_nascimento'] = $mun['sigla_uf'];
        }

        if ($details['pai_id']) {
            $_sql = ' SELECT ideciv as estadocivil, sexo FROM cadastro.fisica WHERE idpes = $1; ';
            $pai = $this->fetchPreparedQuery($_sql, $details['pai_id'], false, 'first-row');

            $paiDetails['estadocivil'] = $pai['estadocivil'];
            $paiDetails['sexo'] = $pai['sexo'];

            $details['pai_details'] = $paiDetails;
        }

        if ($details['mae_id']) {
            $_sql = ' SELECT ideciv as estadocivil, sexo FROM cadastro.fisica WHERE idpes = $1; ';

            $mae = $this->fetchPreparedQuery($_sql, $details['mae_id'], false, 'first-row');

            $maeDetails['estadocivil'] = $mae['estadocivil'];
            $maeDetails['sexo'] = $mae['sexo'];

            $details['mae_details'] = $maeDetails;
        }

        $details['data_nascimento'] = Portabilis_Date_Utils::pgSQLToBr($details['data_nascimento']);
        $details['data_emissao_rg'] = Portabilis_Date_Utils::pgSQLToBr($details['data_emissao_rg']);
        $details['data_emissao_cert_civil'] = Portabilis_Date_Utils::pgSQLToBr($details['data_emissao_cert_civil']);

        return $details;
    }

    protected function loadPessoaParent()
    {
        if ($this->getRequest()->id) {
            $_sql = ' SELECT (select nome from cadastro.pessoa where pessoa.idpes = fisica.idpes) as nome ,ideciv as estadocivil, data_nasc, sexo, falecido FROM cadastro.fisica WHERE idpes = $1; ';

            $details = $this->fetchPreparedQuery($_sql, $this->getRequest()->id, false, 'first-row');

            $details['data_nascimento'] = Portabilis_Date_Utils::pgSQLToBr($details['data_nasc']);
            $details['nome'] = Portabilis_String_Utils::toUtf8($details['nome']);
            $details['id'] = $this->getRequest()->id;
            $details['falecido'] = dbBool($details['falecido']);

            return $details;
        } else {
            return '';
        }
    }

    protected function loadDeficiencias($pessoaId)
    {
        $sql = 'select cod_deficiencia as id, nm_deficiencia as nome from cadastro.fisica_deficiencia,
            cadastro.deficiencia where cod_deficiencia = ref_cod_deficiencia and ref_idpes = $1';

        $deficiencias = $this->fetchPreparedQuery($sql, $pessoaId, false);

        // transforma array de arrays em array chave valor
        $_deficiencias = [];

        foreach ($deficiencias as $deficiencia) {
            $nome = $this->toUtf8($deficiencia['nome'], ['transform' => true]);
            $_deficiencias[$deficiencia['id']] = $nome;
        }

        return $_deficiencias;
    }

    protected function loadRg($pessoaId)
    {
        $sql = 'select rg from cadastro.documento where idpes = $1';
        $rg = $this->fetchPreparedQuery($sql, $pessoaId, false, 'first-field');

        // caso um array vazio seja retornado, seta resultado como null
        if (empty($rg)) {
            $rg = null;
        }

        return $rg;
    }

    protected function loadDataNascimento($pessoaId)
    {
        $sql = 'select data_nasc from cadastro.fisica where idpes = $1';
        $nascimento = $this->fetchPreparedQuery($sql, $pessoaId, false, 'first-field');

        // caso um array vazio seja retornado, seta resultado como null
        if (empty($nascimento)) {
            $nascimento = null;
        }

        return $nascimento;
    }

    // search

    protected function searchOptions()
    {
        return ['namespace' => 'cadastro', 'idAttr' => 'idpes'];
    }

    protected function sqlsForNumericSearch()
    {
        $sqls = [];

        // search by idpes or cpf
        $sqls[] = '
            select
                distinct pessoa.idpes as id,
                (case
                    when fisica.nome_social not like \'\' then
                        fisica.nome_social || \' - Nome de registro: \' || pessoa.nome
                    else
                        pessoa.nome
                end) as name
            from
                cadastro.pessoa,
                cadastro.fisica
            where true
                and fisica.idpes = pessoa.idpes
                and fisica.ativo = 1
                and (
                    pessoa.idpes::varchar like $1||\'%\'
                    or trim(leading \'0\' from fisica.cpf::varchar) like trim(leading \'0\' from $1)||\'%\'
                    or fisica.cpf::varchar like $1||\'%\'
                )
            order by
                id
            limit 15
        ';

        // search by rg
        $sqls[] = '
            select
                distinct pessoa.idpes as id,
                (case
                    when fisica.nome_social not like \'\' then
                        fisica.nome_social || \' - Nome de registro: \' || pessoa.nome
                    else
                        pessoa.nome
                end) as name
            from
                cadastro.pessoa,
                cadastro.documento,
                cadastro.fisica
            where true
                and fisica.idpes = pessoa.idpes
                and fisica.ativo = 1
                and pessoa.idpes = documento.idpes
                and (
                    (documento.rg like $1||\'%\')
                    or trim(leading \'0\' from documento.rg) like trim(leading \'0\' from $1)||\'%\'
                )
            order by
                id
            limit 15
        ';

        return $sqls;
    }

    // subscreve formatResourceValue para adicionar o rg da pessoa, ao final do valor,
    // "<id_pessoa> - <nome_pessoa> (RG: <rg>)", ex: "1 - Lucas D'Avila (RG: 1234567)"
    protected function formatResourceValue($resource)
    {
        $nome = $this->toUtf8($resource['name'], ['transform' => true]);
        $rg = $this->loadRg($resource['id']);
        $nascimento = $this->loadDataNascimento($resource['id']);

        // Quando informado, inclui detalhes extra sobre a pessoa, como RG e Data nascimento.
        $details = [];

        if ($nascimento) {
            $details[] = 'Nascimento: ' . Portabilis_Date_Utils::pgSQLToBr($nascimento);
        }

        if ($rg) {
            $details[] = "RG: $rg";
        }

        $details = $details ? ' (' . implode(', ', $details) . ')' : '';

        return $resource['id'] . " - $nome$details";
    }

    // api responders

    protected function get()
    {
        $pessoa = [];

        if ($this->canGet()) {
            if ($this->getRequest()->id) {
                $pessoa = $this->loadPessoa($this->getRequest()->id);
            } else {
                $pessoa = $this->loadPessoaByCpf($this->getRequest()->cpf);
            }

            $attrs = ['id', 'nome'];
            $pessoa = Portabilis_Array_Utils::filter($pessoa, $attrs);

            $details = $this->loadDetails($pessoa['id']);
            $pessoa = Portabilis_Array_Utils::merge($pessoa, $details);

            $pessoa['deficiencias'] = $this->loadDeficiencias($pessoa['id']);
        }

        return $pessoa;
    }

    private function validateName()
    {
        $validator = new NameValidator($this->getRequest()->nome);

        if (!$validator->isValid()) {
            $this->messenger->append($validator->getMessage());

            return false;
        }

        return true;
    }

    private function validateBirthDate()
    {
        if (empty($this->getRequest()->datanasc)) {
            return true;
        }

        $validator = new BirthDateValidator(Portabilis_Date_Utils::brToPgSQL($this->getRequest()->datanasc));

        if (!$validator->isValid()) {
            $this->messenger->append($validator->getMessage());

            return false;
        }

        return true;
    }

    private function validateDifferentiatedLocation()
    {
        $validator = new DifferentiatedLocationValidator($this->getRequest()->localizacao_diferenciada, $this->getRequest()->zona_localizacao_censo);

        if (!$validator->isValid()) {
            $this->messenger->append($validator->getMessage());

            return false;
        }

        return true;
    }

    protected function canPost()
    {
        return $this->validateName() && $this->validateBirthDate() && $this->validateDifferentiatedLocation();
    }

    protected function post()
    {
        if ($this->canPost()) {
            $pessoaId = $this->getRequest()->pessoa_id;
            $pessoaId = $this->createOrUpdatePessoa($pessoaId);

            $this->createOrUpdatePessoaFisica($pessoaId);
            $this->appendResponse('pessoa_id', $pessoaId);
        }
    }

    protected function createOrUpdatePessoa($pessoaId = null)
    {
        $pessoa = new clsPessoa_();
        $pessoa->idpes = $pessoaId;
        $pessoa->nome = $this->getRequest()->nome;

        $sql = 'select 1 from cadastro.pessoa WHERE idpes = $1 limit 1';

        if (! $pessoaId || Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
            $pessoa->tipo = 'F';
            $pessoa->idpes_cad = $this->currentUserId();
            $pessoaId = $pessoa->cadastra();
        } else {
            $pessoa->idpes_rev = $this->currentUserId();
            $pessoa->data_rev = date('Y-m-d H:i:s', time());
            $pessoa->edita();
        }

        return $pessoaId;
    }

    protected function createOrUpdatePessoaFisica($pessoaId)
    {
        $individual = LegacyIndividual::findOrNew($pessoaId);
        $individual->idpes = $pessoaId;
        $individual->data_nasc = empty($this->getRequest()->datanasc) ? null : Portabilis_Date_Utils::brToPgSQL($this->getRequest()->datanasc);
        $individual->sexo = $this->getRequest()->sexo;
        $individual->ref_cod_sistema = null;
        $individual->ideciv = $this->getRequest()->estadocivil ?: $individual->ideciv;
        $individual->idmun_nascimento = $this->getRequest()->naturalidade ?: $individual->idmun_nascimento;
        $individual->pais_residencia = $this->getRequest()->pais_residencia ?: $individual->pais_residencia;
        $individual->falecido = $this->getRequest()->falecido == 'true';
        $individual->idpais_estrangeiro = $this->getRequest()->pais_origem_id ?: $individual->idpais_estrangeiro;
        if ($this->getRequest()->tipo_nacionalidade == Nacionalidade::BRASILEIRA) {
            $individual->idpais_estrangeiro = null;
        }
        $individual->nacionalidade = $this->getRequest()->tipo_nacionalidade ?: $individual->nacionalidade;
        $individual->zona_localizacao_censo = $this->getRequest()->zona_localizacao_censo ?: $individual->zona_localizacao_censo;
        $individual->localizacao_diferenciada = $this->getRequest()->localizacao_diferenciada ?: $individual->localizacao_diferenciada;
        $individual->nome_social = $this->getRequest()->nome_social ?? $this->getRequest()->nome_social;

        $individual->saveOrFail();

        $raca = new clsCadastroFisicaRaca($pessoaId, $this->getRequest()->cor_raca);
        if ($raca->existe()) {
            $this->getRequest()->cor_raca ? $raca->edita() : $raca->excluir();
        } elseif ($this->getRequest()->cor_raca) {
            $raca->cadastra();
        }

        $ddd_fone_fixo = $this->getRequest()->ddd_telefone_1;
        $fone_fixo = $this->getRequest()->telefone_1;
        $ddd_fone_mov = $this->getRequest()->ddd_telefone_mov;
        $fone_mov = $this->getRequest()->telefone_mov;

        if ($fone_fixo || $fone_fixo == '') {
            $ddd_fixo = $ddd_fone_fixo;
            $fone_fixo = $fone_fixo;
            $telefone = new clsPessoaTelefone($individual->idpes, 1, $fone_fixo, $ddd_fixo);
            $telefone->cadastra();
        }
        if ($fone_mov || $fone_mov == '') {
            $ddd_mov = $ddd_fone_mov;
            $fone_mov = $fone_mov;
            $telefone = new clsPessoaTelefone($individual->idpes, 2, $fone_mov, $ddd_mov);
            $telefone->cadastra();
        }
    }

    protected function createOrUpdateEndereco()
    {
        $this->person_id = $this->getRequest()->person_id;
        $this->postal_code = $this->getRequest()->postal_code;
        $this->address = $this->getRequest()->address;
        $this->number = $this->getRequest()->number;
        $this->complement = $this->getRequest()->complement;
        $this->neighborhood = $this->getRequest()->neighborhood;
        $this->city_id = $this->getRequest()->city_id;

        $this->saveAddress($this->getRequest()->person_id);
    }

    protected function getInep($servidorId)
    {
        $sql = 'SELECT cod_docente_inep FROM modules.educacenso_cod_docente WHERE cod_servidor = $1';

        return Portabilis_Utils_Database::selectField($sql, ['params' => [$servidorId]]);
    }

    protected function existServant($servidorId)
    {
        $sql = 'SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = $1';

        return Portabilis_Utils_Database::selectField($sql, ['params' => [$servidorId]]);
    }

    protected function getInfoServidor()
    {
        $servidorId = $this->getRequest()->servidor_id;
        $_servidor['inep'] = $this->getInep($servidorId);
        $_servidor['deficiencias'] = $this->loadDeficiencias($servidorId);

        return $_servidor;
    }

    protected function isExistServant()
    {
        $id = (int) $this->getRequest()->servidor_id;
        $exist = $this->existServant($id) === 1;

        $_servidor['exist'] = $exist;
        $_servidor['id'] = $id;
        $_servidor['nome'] = $exist ? $this->loadPessoa($id)['nome'] : null;

        return $_servidor;
    }

    protected function reativarPessoa()
    {
        $var1 = $this->getRequest()->id;
        $sql = "UPDATE cadastro.fisica SET ativo = 1 WHERE idpes = $var1";
        $fisica = $this->fetchPreparedQuery($sql);

        return $fisica;
    }

    protected function dadosUnificacaoPessoa()
    {
        $pessoasIds = $this->getRequest()->pessoas_ids ?? 0;

        $sql = 'SELECT
                p.idpes,
                concat_ws(\', \',
                    CASE WHEN cod_aluno IS NOT NULL THEN \'Aluno(a)\' ELSE NULL end,
                    CASE WHEN responsavel.idpes IS NOT NULL THEN \'Responsável\' ELSE NULL end,
                    CASE WHEN cod_servidor IS NOT NULL THEN \'Servidor(a)\' ELSE NULL end,
                    CASE WHEN cod_usuario IS NOT NULL THEN \'Usuário(a)\' ELSE NULL end
                ) vinculo,
                p.nome,
                COALESCE(to_char(f.data_nasc, \'dd/mm/yyyy\'), \'Não consta\') AS data_nascimento,
                CASE f.sexo
                    WHEN \'M\' THEN \'Masculino\'
                    WHEN \'F\' THEN \'Feminino\'
                    ELSE \'Não consta\'
                END AS sexo,
                COALESCE(f.cpf::varchar, \'Não consta\') AS cpf,
                COALESCE(d.rg, \'Não consta\') AS rg,
                COALESCE(pm.nome, \'Não consta\') AS pessoa_mae
            FROM cadastro.pessoa p
            JOIN cadastro.fisica f ON f.idpes = p.idpes
            LEFT JOIN cadastro.documento d ON d.idpes = f.idpes
            LEFT JOIN pmieducar.aluno a ON a.ref_idpes = p.idpes AND a.ativo = 1
            LEFT JOIN pmieducar.servidor s ON s.cod_servidor = p.idpes AND s.ativo = 1
            LEFT JOIN cadastro.pessoa pm ON pm.idpes = f.idpes_mae
            LEFT JOIN pmieducar.usuario u on u.cod_usuario = p.idpes
            LEFT JOIN LATERAL (
                SELECT idpes FROM cadastro.fisica f1 WHERE exists (
                    SELECT 1 FROM cadastro.fisica f2 WHERE f1.idpes IN (f2.idpes_pai, f2.idpes_mae, f2.idpes_responsavel)
                ) AND f1.idpes = f.idpes
            ) responsavel ON TRUE

            WHERE p.idpes IN (' . $pessoasIds . ') ORDER BY vinculo DESC;
        ';

        $pessoas = $this->fetchPreparedQuery($sql, [], false);

        $attrs = [
            'idpes',
            'vinculo',
            'nome',
            'data_nascimento',
            'sexo',
            'cpf',
            'rg',
            'pessoa_mae',
        ];

        $filters = Portabilis_Array_Utils::filterSet($pessoas, $attrs);

        foreach ($filters as &$item) {
            if (isset($item['vinculo']) && empty($item['vinculo'])) {
                $item['vinculo'] = 'Sem vínculo';
            }
        }

        return [
            'pessoas' => $filters
        ];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'pessoa-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'pessoa')) {
            $this->appendResponse($this->get());
        } elseif ($this->isRequestFor('post', 'pessoa')) {
            $this->appendResponse($this->post());
        } elseif ($this->isRequestFor('get', 'info-servidor')) {
            $this->appendResponse($this->getInfoServidor());
        } elseif ($this->isRequestFor('get', 'exist-servidor')) {
            $this->appendResponse($this->isExistServant());
        } elseif ($this->isRequestFor('post', 'pessoa-endereco')) {
            $this->appendResponse($this->createOrUpdateEndereco());
        } elseif ($this->isRequestFor('get', 'pessoa-parent')) {
            $this->appendResponse($this->loadPessoaParent());
        } elseif ($this->isRequestFor('get', 'reativarPessoa')) {
            $this->appendResponse($this->reativarPessoa());
        } elseif ($this->isRequestFor('get', 'dadosUnificacaoPessoa')) {
            $this->appendResponse($this->dadosUnificacaoPessoa());
        } else {
            $this->notImplementedOperationError();
        }
    }

    protected function sqlsForStringSearch()
    {
        $searchOptions = $this->mergeOptions($this->searchOptions(), $this->defaultSearchOptions());

        return '
            select
                distinct pessoa.idpes as id,
                (case
                    when fisica.nome_social not like \'\' then
                        fisica.nome_social || \' - Nome de registro: \' || pessoa.nome
                    else
                        pessoa.nome
                end) as name
            from
                cadastro.pessoa
            inner join cadastro.fisica
                on fisica.idpes = pessoa.idpes
            where true
                and fisica.ativo = 1
                and lower(coalesce(fisica.nome_social, \'\') || pessoa.nome) like \'%\'||lower(($1))||\'%\'
            order by
                id,
                name
            limit 15
        ';
    }
}
