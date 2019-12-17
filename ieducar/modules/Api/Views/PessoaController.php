<?php

use iEducar\Modules\Educacenso\Validator\NameValidator;
use iEducar\Modules\Educacenso\Validator\BirthDateValidator;
use iEducar\Modules\Educacenso\Validator\DifferentiatedLocationValidator;
use App\Models\LegacyIndividual;

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'include/pessoa/clsPessoa_.inc.php';
require_once 'include/pessoa/clsFisica.inc.php';
require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';
require_once 'intranet/include/funcoes.inc.php';

class PessoaController extends ApiCoreController
{

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
            (SELECT COALESCE((SELECT cep FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT cep FROM cadastro.endereco_externo WHERE idpes = $2))) as cep,

             (SELECT COALESCE((SELECT l.nome FROM public.logradouro l, cadastro.endereco_pessoa ep WHERE l.idlog = ep.idlog and ep.idpes = $2),
             (SELECT logradouro FROM cadastro.endereco_externo WHERE idpes = $2))) as logradouro,

             (SELECT COALESCE((SELECT l.idtlog FROM public.logradouro l, cadastro.endereco_pessoa ep WHERE l.idlog = ep.idlog and ep.idpes = $2),
             (SELECT idtlog FROM cadastro.endereco_externo WHERE idpes = $2))) as idtlog,

           (SELECT COALESCE((SELECT b.nome FROM public.bairro b, cadastro.endereco_pessoa ep WHERE b.idbai = ep.idbai and ep.idpes = $2),
             (SELECT bairro FROM cadastro.endereco_externo WHERE idpes = $2))) as bairro,

             (SELECT COALESCE((SELECT b.zona_localizacao FROM public.bairro b, cadastro.endereco_pessoa ep WHERE b.idbai = ep.idbai and ep.idpes = $2),
             (SELECT zona_localizacao FROM cadastro.endereco_externo WHERE idpes = $2))) as zona_localizacao,

             (SELECT COALESCE((SELECT l.idmun FROM public.logradouro l, cadastro.endereco_pessoa ep WHERE l.idlog = ep.idlog and ep.idpes = $2),
             (SELECT idmun FROM public.logradouro l, urbano.cep_logradouro cl, cadastro.endereco_externo ee
              WHERE cl.idlog = l.idlog AND cl.cep = ee.cep and ee.idpes = $2 order by 1 desc limit 1))) as idmun,

              idmun_nascimento,


              (SELECT COALESCE((SELECT numero FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT numero FROM cadastro.endereco_externo WHERE idpes = $2))) as numero,

              (SELECT COALESCE((SELECT letra FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT letra FROM cadastro.endereco_externo WHERE idpes = $2))) as letra,

              (SELECT COALESCE((SELECT complemento FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT complemento FROM cadastro.endereco_externo WHERE idpes = $2))) as complemento,

              (SELECT COALESCE((SELECT andar FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT andar FROM cadastro.endereco_externo WHERE idpes = $2))) as andar,

              (SELECT COALESCE((SELECT bloco FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT bloco FROM cadastro.endereco_externo WHERE idpes = $2))) as bloco,

              (SELECT COALESCE((SELECT apartamento FROM cadastro.endereco_pessoa WHERE idpes = $2),
             (SELECT apartamento FROM cadastro.endereco_externo WHERE idpes = $2))) as apartamento,


             (SELECT idbai FROM cadastro.endereco_pessoa WHERE idpes = $2) as idbai,
             fisica.idpais_estrangeiro as pais_origem_id,
           fisica.nacionalidade as tipo_nacionalidade,
           fisica.zona_localizacao_censo,
           fisica.localizacao_diferenciada,
           fisica.nome_social,
           (SELECT pais.nome
                   FROM public.pais
                   WHERE pais.idpais = fisica.idpais_estrangeiro) AS pais_origem_nome,

           (SELECT ref_cod_raca FROM cadastro.fisica_raca WHERE fisica.idpes = fisica_raca.ref_idpes) as cor_raca,

             (SELECT bairro.iddis FROM cadastro.endereco_pessoa
                INNER JOIN public.bairro ON (endereco_pessoa.idbai = bairro.idbai)
                WHERE idpes = $2) as iddis,

             (SELECT distrito.nome FROM cadastro.endereco_pessoa
                INNER JOIN public.bairro ON (endereco_pessoa.idbai = bairro.idbai)
                INNER JOIN public.distrito ON (bairro.iddis = distrito.iddis)
                         WHERE idpes = $2) as distrito,
              (SELECT fone_pessoa.fone FROM cadastro.fone_pessoa WHERE fone_pessoa.idpes = $2 AND fone_pessoa.tipo = 1) as fone_fixo,
              (SELECT fone_pessoa.fone FROM cadastro.fone_pessoa WHERE fone_pessoa.idpes = $2 AND fone_pessoa.tipo = 2) as fone_mov,
              (SELECT fone_pessoa.ddd FROM cadastro.fone_pessoa WHERE fone_pessoa.idpes = $2 AND fone_pessoa.tipo = 1) as ddd_fone_fixo,
              (SELECT fone_pessoa.ddd FROM cadastro.fone_pessoa WHERE fone_pessoa.idpes = $2 AND fone_pessoa.tipo = 2) as ddd_fone_mov,

             (SELECT idlog FROM cadastro.endereco_pessoa WHERE idpes = $2) as idlog,
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
            'cep',
            'logradouro',
            'idtlog',
            'bairro',
            'tipo_cert_civil',
            'num_termo',
            'num_livro',
            'num_folha',
            'certidao_nascimento',
            'certidao_casamento',
            'zona_localizacao',
            'idbai',
            'idlog',
            'idmun',
            'idmun_nascimento',
            'complemento',
            'apartamento',
            'andar',
            'bloco',
            'numero',
            'letra',
            'possui_documento',
            'iddis',
            'distrito',
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

        if ($details['idmun']) {
            $_sql = ' SELECT nome, sigla_uf FROM public.municipio WHERE idmun = $1; ';
            $mun = $this->fetchPreparedQuery($_sql, $details['idmun'], false, 'first-row');
            $details['municipio'] = $this->toUtf8($mun['nome']);
            $details['sigla_uf'] = $mun['sigla_uf'];
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
        $pessoa->nome = Portabilis_String_Utils::toLatin1($this->getRequest()->nome);

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
        $individual->nacionalidade = $this->getRequest()->tipo_nacionalidade ?: $individual->nacionalidade;
        $individual->zona_localizacao_censo = $this->getRequest()->zona_localizacao_censo ?: $individual->zona_localizacao_censo;
        $individual->localizacao_diferenciada = $this->getRequest()->localizacao_diferenciada ?: $individual->localizacao_diferenciada;
        $individual->nome_social = $this->getRequest()->nome_social ?: $individual->nome_social;

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

    //select fone from fone_pessoa where fone_pessoa.idpes = 18664 AND fone_pessoa.tipo = 1
    protected function _createOrUpdatePessoaEndereco($pessoaId)
    {
        $cep = idFederal2Int($this->getRequest()->cep);
        $objCepLogradouro = new ClsCepLogradouro($cep, $this->getRequest()->logradouro_id);

        if (!$objCepLogradouro->existe()) {
            $objCepLogradouro->cadastra();
        }

        $objCepLogradouroBairro = new ClsCepLogradouroBairro();
        $objCepLogradouroBairro->cep = $cep;
        $objCepLogradouroBairro->idbai = $this->getRequest()->bairro_id;
        $objCepLogradouroBairro->idlog = $this->getRequest()->logradouro_id;

        if (! $objCepLogradouroBairro->existe()) {
            $objCepLogradouroBairro->cadastra();
        }

        $endereco = new clsPessoaEndereco(
            $this->getRequest()->pessoa_id,
            $cep,
            $this->getRequest()->logradouro_id,
            $this->getRequest()->bairro_id,
            $this->getRequest()->numero,
            Portabilis_String_Utils::toLatin1($this->getRequest()->complemento),
            false,
            Portabilis_String_Utils::toLatin1($this->getRequest()->letra),
            Portabilis_String_Utils::toLatin1($this->getRequest()->bloco),
            $this->getRequest()->apartamento,
            $this->getRequest()->andar
        );

        // forçado exclusão, assim ao cadastrar endereco_pessoa novamente,
        // será excluido endereco_externo (por meio da trigger fcn_aft_ins_endereco_pessoa).
        $endereco->exclui();
        $endereco->cadastra();
    }

    protected function createOrUpdateEndereco()
    {
        $pessoaId = $this->getRequest()->pessoa_id;

        if ($this->getRequest()->cep && is_numeric($this->getRequest()->bairro_id) && is_numeric($this->getRequest()->logradouro_id)) {
            $this->_createOrUpdatePessoaEndereco($pessoaId);
        } elseif ($this->getRequest()->cep && is_numeric($this->getRequest()->municipio_id) && is_numeric($this->getRequest()->distrito_id)) {
            if (!is_numeric($this->getRequest()->bairro_id)) {
                if ($this->canCreateBairro()) {
                    $this->getRequest()->bairro_id = $this->createBairro();
                } else {
                    return;
                }
            }

            if (!is_numeric($this->getRequest()->logradouro_id)) {
                if ($this->canCreateLogradouro()) {
                    $this->getRequest()->logradouro_id = $this->createLogradouro();
                } else {
                    return;
                }
            }

            $this->_createOrUpdatePessoaEndereco($pessoaId);
        } else {
            $endereco = new clsPessoaEndereco($pessoaId);
            $endereco->exclui();
        }
    }

    protected function getInep($servidorId)
    {
        $sql = 'SELECT cod_docente_inep FROM modules.educacenso_cod_docente WHERE cod_servidor = $1';

        return Portabilis_Utils_Database::selectField($sql, ['params' => [$servidorId]]);
    }

    protected function getInfoServidor()
    {
        $servidorId = $this->getRequest()->servidor_id;
        $_servidor['inep'] = $this->getInep($servidorId);
        $_servidor['deficiencias'] = $this->loadDeficiencias($servidorId);

        return $_servidor;
    }

    protected function canCreateBairro()
    {
        return !empty($this->getRequest()->bairro) && !empty($this->getRequest()->zona_localizacao);
    }

    protected function canCreateLogradouro()
    {
        return !empty($this->getRequest()->logradouro) && !empty($this->getRequest()->idtlog);
    }

    protected function createBairro()
    {
        $objBairro = new clsBairro(null, $this->getRequest()->municipio_id, null, Portabilis_String_Utils::toLatin1($this->getRequest()->bairro), $this->currentUserId());
        $objBairro->zona_localizacao = $this->getRequest()->zona_localizacao;
        $objBairro->iddis = $this->getRequest()->distrito_id;

        return $objBairro->cadastra();
    }

    protected function createLogradouro()
    {
        $objLogradouro = new clsLogradouro(
            null,
            $this->getRequest()->idtlog,
            Portabilis_String_Utils::toLatin1($this->getRequest()->logradouro),
            $this->getRequest()->municipio_id,
            null,
            'S',
            $this->currentUserId()
        );

        return $objLogradouro->cadastra();
    }

    protected function reativarPessoa()
    {
        $var1 = $this->getRequest()->id;
        $sql = "UPDATE cadastro.fisica SET ativo = 1 WHERE idpes = $var1";
        $fisica = $this->fetchPreparedQuery($sql);

        return $fisica;
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
        } elseif ($this->isRequestFor('post', 'pessoa-endereco')) {
            $this->appendResponse($this->createOrUpdateEndereco());
        } elseif ($this->isRequestFor('get', 'pessoa-parent')) {
            $this->appendResponse($this->loadPessoaParent());
        } elseif ($this->isRequestFor('get', 'reativarPessoa')) {
            $this->appendResponse($this->reativarPessoa());
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
