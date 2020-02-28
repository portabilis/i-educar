<?php

use Illuminate\Support\Facades\Session;

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'include/funcoes.inc.php';

class FilaUnicaController extends ApiCoreController
{
    protected function getDadosAluno()
    {
        $tipoCertidao = $this->getRequest()->tipo_certidao;
        $numNovaCeridao = $this->getRequest()->certidao_nascimento;
        $numTermo = $this->getRequest()->num_termo ? $this->getRequest()->num_termo : 0;
        $numLivro = $this->getRequest()->num_livro ? $this->getRequest()->num_livro : 0;
        $numFolha = $this->getRequest()->num_folha ? $this->getRequest()->num_folha : 0;
        $id = $this->getRequest()->id ? $this->getRequest()->id : 0;
        $byCertidao = $this->getRequest()->by_certidao ? $this->getRequest()->by_certidao == '1' : false;
        $byId = $this->getRequest()->by_id ? $this->getRequest()->by_id == '1' : false;

        $sql = "SELECT cod_aluno,
                       pessoa.nome,
                       pessoa.idpes,
                       to_char(fisica.data_nasc, 'dd/mm/yyyy') AS data_nasc,
                       replace(to_char(fisica.cpf, '000:000:000-00'), ':', '.') AS cpf,
                       documento.num_termo,
                       documento.num_folha,
                       documento.num_livro,
                       documento.certidao_nascimento,
                       ad.postal_code AS cep,
                       ad.neighborhood AS nm_bairro,
                       ad.city_id,
                       ad.city_id || ' - ' || ad.city AS nm_municipio,
                       ad.address AS nm_logradouro,
                       ad.number as numero,
                       ad.complement as complemento,
                       fisica.sexo,
                       fisica.ideciv
                  FROM pmieducar.aluno
                 INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                 INNER JOIN cadastro.fisica ON (fisica.idpes = aluno.ref_idpes)
                 INNER JOIN cadastro.documento ON (documento.idpes = aluno.ref_idpes)
                  LEFT JOIN person_has_place php ON php.person_id = aluno.ref_idpes
                  LEFT JOIN addresses ad ON ad.id = php.place_id
                  WHERE
                 ";
        if ($byCertidao) {
            $sql .= "  CASE WHEN {$tipoCertidao} != 1
                                 THEN num_termo = '{$numTermo}'
                                  AND num_livro = '{$numLivro}'
                                  AND num_folha = '{$numFolha}'
                            ELSE certidao_nascimento = '{$numNovaCeridao}'
                        END  ";

        }

        if ($byId) {
            $sql .= "  cod_aluno = {$id} ";
        }

        $attrs = [
            'cod_aluno',
            'nome',
            'idpes',
            'data_nasc',
            'cpf',
            'num_termo',
            'num_folha',
            'num_livro',
            'certidao_nascimento',
            'cep',
            'nm_bairro',
            'idmun',
            'nm_municipio',
            'nm_logradouro',
            'numero',
            'letra',
            'complemento',
            'sexo',
            'ideciv'
        ];

        $aluno = Portabilis_Array_Utils::filterSet($this->fetchPreparedQuery($sql), $attrs);

        return ['aluno' => $aluno[0]];
    }

    protected function getMatriculaAlunoAndamento()
    {
        $anoLetivo = $this->getRequest()->ano_letivo;
        $aluno = $this->getRequest()->aluno_id;

        if ($aluno && $anoLetivo) {
            $sql = 'SELECT cod_matricula,
                           ref_cod_aluno AS cod_aluno
                      FROM pmieducar.matricula
                     WHERE ativo = 1
                       AND aprovado = 3
                       AND ano = $1
                       AND ref_cod_aluno = $2';
            $matricula = $this->fetchPreparedQuery($sql, [$anoLetivo, $aluno], false, 'first-line');

            return $matricula;
        }

        return false;
    }

    protected function getSolicitacaoAndamento()
    {
        $anoLetivo = $this->getRequest()->ano_letivo;
        $aluno = $this->getRequest()->aluno_id;

        if ($aluno && $anoLetivo) {
            $sql = 'SELECT ref_cod_aluno AS cod_aluno,
                           cod_candidato_fila_unica AS cod_candidato
                      FROM pmieducar.candidato_fila_unica
                     WHERE ativo = 1
                       AND ano_letivo = $1
                       AND ref_cod_aluno = $2';

            $matricula = $this->fetchPreparedQuery($sql, [$anoLetivo, $aluno], false, 'first-line');

            return $matricula;
        }

        return false;
    }

    protected function getSeriesSugeridas()
    {
        $idade = $this->getRequest()->idade;

        if ($idade) {
            $sql = 'SELECT nm_serie
                      FROM pmieducar.serie
                     WHERE ativo = 1
                       AND $1 BETWEEN idade_inicial AND idade_final';
            $series = Portabilis_Array_Utils::filterSet($this->fetchPreparedQuery($sql, $idade), 'nm_serie');

            return ['series' => $series];
        }

        return false;
    }

    protected function getDadosResponsaveisAluno()
    {
        $aluno = $this->getRequest()->aluno_id;

        if ($aluno) {
            $sql = "SELECT pessoa.idpes,
                    CASE
                        WHEN fisica_aluno.idpes_pai = pessoa.idpes THEN '1'
                        WHEN fisica_aluno.idpes_mae = pessoa.idpes THEN '2'
                        ELSE '3' END as vinculo_familiar,
                       pessoa.nome,
                       fisica.sexo,
                       fisica.ideciv,
                       to_char(fisica.data_nasc, 'dd/mm/yyyy') AS data_nasc,
                       fisica.cpf,
                       fisica.tipo_trabalho,
                       fisica.local_trabalho,
                       documento.declaracao_trabalho_autonomo,
                       to_char(fisica.horario_inicial_trabalho, 'HH24:MI') AS horario_inicial_trabalho,
                       to_char(fisica.horario_final_trabalho, 'HH24:MI') AS horario_final_trabalho,
                       fpr.ddd AS ddd_telefone,
                       fpr.fone AS telefone,
                       fpc.ddd AS ddd_telefone_celular,
                       fpc.fone AS telefone_celular
                  FROM pmieducar.aluno
                  JOIN cadastro.fisica fisica_aluno
                  ON aluno.ref_idpes = fisica_aluno.idpes
                  JOIN cadastro.fisica
                  ON (fisica.idpes = fisica_aluno.idpes_pai AND aluno.tipo_responsavel IN ('a', 'p') )
                    OR (fisica.idpes = fisica_aluno.idpes_mae AND aluno.tipo_responsavel IN ('a', 'm') )
                    OR (fisica.idpes = fisica_aluno.idpes_responsavel AND aluno.tipo_responsavel = 'r' )
                  INNER JOIN cadastro.pessoa ON (pessoa.idpes = fisica.idpes)

                  LEFT JOIN cadastro.documento ON (documento.idpes = fisica.idpes)
                  LEFT JOIN cadastro.fone_pessoa fpr ON (fpr.idpes = fisica.idpes
                                                         AND fpr.tipo = 1)
                  LEFT JOIN cadastro.fone_pessoa fpc ON (fpc.idpes = fisica.idpes
                                                         AND fpc.tipo = 2)

                 WHERE aluno.cod_aluno = {$aluno} ";

            $attrs = [
                'idpes',
                'vinculo_familiar',
                'nome',
                'sexo',
                'data_nasc',
                'ideciv',
                'cpf',
                'tipo_trabalho',
                'local_trabalho',
                'declaracao_trabalho_autonomo',
                'horario_inicial_trabalho',
                'horario_final_trabalho',
                'ddd_telefone',
                'telefone',
                'ddd_telefone_celular',
                'telefone_celular'
            ];

            $responsaveis = Portabilis_Array_Utils::filterSet($this->fetchPreparedQuery($sql), $attrs);

            if (!count($responsaveis)) {
                $sql = "SELECT pessoa.idpes,
                               vinculo_familiar,
                               pessoa.nome,
                               fisica.sexo,
                               fisica.ideciv,
                               to_char(fisica.data_nasc, 'dd/mm/yyyy') AS data_nasc,
                               fisica.cpf,
                               fisica.tipo_trabalho,
                               fisica.local_trabalho,
                               documento.declaracao_trabalho_autonomo,
                               to_char(fisica.horario_inicial_trabalho, 'HH24:MI') AS horario_inicial_trabalho,
                               to_char(fisica.horario_final_trabalho, 'HH24:MI') AS horario_final_trabalho,
                               fpr.ddd AS ddd_telefone,
                               fpr.fone AS telefone,
                               fpc.ddd AS ddd_telefone_celular,
                               fpc.fone AS telefone_celular
                          FROM pmieducar.responsaveis_aluno
                         INNER JOIN cadastro.fisica ON (fisica.idpes = responsaveis_aluno.ref_idpes)
                         INNER JOIN cadastro.pessoa ON (pessoa.idpes = responsaveis_aluno.ref_idpes)
                          LEFT JOIN cadastro.documento ON (documento.idpes = responsaveis_aluno.ref_idpes)
                          LEFT JOIN cadastro.fone_pessoa fpr ON (fpr.idpes = responsaveis_aluno.ref_idpes
                                                                 AND fpr.tipo = 1)
                          LEFT JOIN cadastro.fone_pessoa fpc ON (fpc.idpes = responsaveis_aluno.ref_idpes
                                                                 AND fpc.tipo = 2)
                         WHERE ref_cod_aluno = {$aluno}";

                $responsaveis = Portabilis_Array_Utils::filterSet($this->fetchPreparedQuery($sql), $attrs);
            }

            return ['responsaveis' => $responsaveis];
        }

        return false;
    }

    protected function getMontaSelectEscolasCandidato()
    {
        $cod_candidato_fila_unica = $this->getRequest()->cod_candidato_fila_unica;
        $userId = Session::get('id_pessoa');
        $nivelAcesso = $this->getNivelAcesso();
        $acessoEscolar = $nivelAcesso == 4;

        if ($cod_candidato_fila_unica) {
            $sql = "SELECT ecdu.ref_cod_escola AS ref_cod_escola,
                           juridica.fantasia AS nome
                      FROM pmieducar.escola_candidato_fila_unica AS ecdu
                INNER JOIN pmieducar.escola AS esc ON esc.cod_escola = ecdu.ref_cod_escola
                INNER JOIN cadastro.juridica ON juridica.idpes = esc.ref_idpes
                     WHERE ecdu.ref_cod_candidato_fila_unica = {$cod_candidato_fila_unica}";

            if ($acessoEscolar) {
                $sql .= " AND EXISTS( SELECT 1
                                        FROM pmieducar.escola_usuario
                                       WHERE escola_usuario.ref_cod_usuario = {$userId}
                                         AND escola_usuario.ref_cod_escola = esc.cod_escola )";
            }

            $escolas_candidato = Portabilis_Utils_Database::fetchPreparedQuery($sql);

            return ['escolas' => $escolas_candidato];
        }

        return false;
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'get-aluno')) {
            $this->appendResponse($this->getDadosAluno());
        } elseif ($this->isRequestFor('get', 'matricula-andamento')) {
            $this->appendResponse($this->getMatriculaAlunoAndamento());
        } elseif ($this->isRequestFor('get', 'solicitacao-andamento')) {
            $this->appendResponse($this->getSolicitacaoAndamento());
        } elseif ($this->isRequestFor('get', 'series-sugeridas')) {
            $this->appendResponse($this->getSeriesSugeridas());
        } elseif ($this->isRequestFor('get', 'responsaveis-aluno')) {
            $this->appendResponse($this->getDadosResponsaveisAluno());
        } elseif ($this->isRequestFor('get', 'escolas-candidato')) {
            $this->appendResponse($this->getMontaSelectEscolasCandidato());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
