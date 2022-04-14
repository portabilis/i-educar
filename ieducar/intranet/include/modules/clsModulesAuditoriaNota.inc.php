<?php

use Illuminate\Support\Facades\Auth;

class clsModulesAuditoriaNota
{
    public $notaAntiga;
    public $notaNova;
    public $stringNotaAntiga;
    public $stringNotaNova;
    public $usuario;
    public $operacao;
    public $rotina;
    public $dataHora;
    public $turma;

    const OPERACAO_INCLUSAO = 1;
    const OPERACAO_ALTERACAO = 2;
    const OPERACAO_EXCLUSAO = 3;

    public function __construct($notaAntiga, $notaNova, $turmaId)
    {
        //Foi necessário enviar turma pois não á possí­vel saber a turma atual somente através da matrí­cula
        $this->turma = $turmaId;

        $this->usuario = $this->getUsuarioAtual();
        $this->rotina = 'notas';

        $this->notaAntiga = $notaAntiga;
        $this->notaNova = $notaNova;

        if (!is_null($this->notaAntiga)) {
            $this->stringNotaAntiga = $this->montaStringInformacoes($this->montaArrayInformacoes($this->notaAntiga));
        }

        if (!is_null($this->notaNova)) {
            $this->stringNotaNova = $this->montaStringInformacoes($this->montaArrayInformacoes($this->notaNova));
        }

        $this->dataHora = date('Y-m-d H:i:s');
    }

    public function cadastra()
    {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}auditoria";
        $separador = '';
        $valores = '';

        if (!is_null($this->stringNotaAntiga) && !is_null($this->stringNotaNova)) {
            $this->operacao = self::OPERACAO_ALTERACAO;
        } elseif (!is_null($this->stringNotaAntiga) && is_null($this->stringNotaNova)) {
            $this->operacao = self::OPERACAO_EXCLUSAO;
        } elseif (is_null($this->stringNotaAntiga) && !is_null($this->stringNotaNova)) {
            $this->operacao = self::OPERACAO_INCLUSAO;
        }

        if (is_string($this->usuario)) {
            $campos .= "{$separador}usuario";
            $valores .= "{$separador}'{$this->usuario}'";
            $separador = ', ';
        }

        $campos .= "{$separador}operacao";
        $valores .= "{$separador}'{$this->operacao}'";
        $separador = ', ';

        $campos .= "{$separador}rotina";
        $valores .= "{$separador}'{$this->rotina}'";
        $separador = ', ';

        if (is_string($this->stringNotaAntiga)) {
            $this->stringNotaAntiga = str_replace('\'', "\'", $this->stringNotaAntiga);
            $campos .= "{$separador}valor_antigo";
            $valores .= "{$separador}E'{$this->stringNotaAntiga}'";
            $separador = ', ';
        }

        if (is_string($this->stringNotaNova)) {
            $this->stringNotaNova = str_replace('\'', "\'", $this->stringNotaNova);
            $campos .= "{$separador}valor_novo";
            $valores .= "{$separador}E'{$this->stringNotaNova}'";
            $separador = ', ';
        }

        $campos .= "{$separador}data_hora";
        $valores .= "{$separador}'{$this->dataHora}'";
        $separador = ', ';

        $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
    }

    private function montaStringInformacoes($arrayInformacoes)
    {
        if (empty($arrayInformacoes)) {
            return null;
        }

        $stringDados = '';
        $separadorDados = ',';
        $separadorInformacoes = ':';
        $inicioString = '{';
        $fimString = '}';

        $stringDados .= $inicioString;

        foreach ($arrayInformacoes as $campo => $valor) {
            $stringDados .= $campo;
            $stringDados .= $separadorInformacoes;
            $stringDados .= $valor;
            $stringDados .= $separadorDados;
        }

        //remove o último valor, qual seria uma vírgula
        $stringDados = substr($stringDados, 0, -1);

        $stringDados .= $fimString;

        return $stringDados;
    }

    private function montaArrayInformacoes($nota)
    {
        if (!($nota instanceof Avaliacao_Model_NotaComponente)) {
            return null;
        }
        $componenteCurricularId = $nota->get('componenteCurricular');
        $componenteCurricular = $this->getNomeComponenteCurricular($componenteCurricularId);

        $notaAlunoId = $nota->get('notaAluno');

        $arrayInformacoes = $this->getInfosMatricula($notaAlunoId);

        $arrayInformacoes += ['nota' => $nota->notaArredondada,
            'etapa' => $nota->etapa,
            'componenteCurricular' => $componenteCurricular];

        return $arrayInformacoes;
    }

    private function getNomeComponenteCurricular($componenteCurricularId)
    {
        $mapper = new ComponenteCurricular_Model_ComponenteDataMapper();
        $componenteCurricular = $mapper->find($componenteCurricularId)->nome;

        return $componenteCurricular;
    }

    private function getInfosMatricula($notaAlunoId)
    {
        $mapper = new Avaliacao_Model_NotaAlunoDataMapper();
        $matriculaId = $mapper->find($notaAlunoId)->matricula;

        $objMatricula = new clsPmieducarMatricula($matriculaId);
        $detMatricula = $objMatricula->detalhe();

        $instituicaoId = $detMatricula['ref_cod_instituicao'];
        $escolaId = $detMatricula['ref_ref_cod_escola'];
        $cursoId = $detMatricula['ref_cod_curso'];
        $serieId = $detMatricula['ref_ref_cod_serie'];
        $alunoId = $detMatricula['ref_cod_aluno'];
        $turmaId = $this->turma;

        $nomeInstitucao = $this->getNomeInstituicao($instituicaoId);
        $nomeEscola = $this->getNomeEscola($escolaId);
        $nomeCurso = $this->getNomeCurso($cursoId);
        $nomeSerie = $this->getNomeSerie($serieId);
        $nomeAluno = $this->getNomeAluno($alunoId);
        $nomeTurma = $this->getNomeTurma($turmaId);

        return ['instituicao' => $nomeInstitucao,
            'instituicao_id' => $instituicaoId,
            'escola' => $nomeEscola,
            'escola_id' => $escolaId,
            'curso' => $nomeCurso,
            'curso_id' => $cursoId,
            'serie' => $nomeSerie,
            'serie_id' => $serieId,
            'turma' => $nomeTurma,
            'turma_id' => $turmaId,
            'aluno' => $nomeAluno,
            'aluno_id' => $alunoId];
    }

    private function getNomeInstituicao($instituicaoId)
    {
        $objInstituicao = new clsPmieducarInstituicao($instituicaoId);
        $detInstituicao = $objInstituicao->detalhe();
        $nomeInstitucao = $detInstituicao['nm_instituicao'];

        return $nomeInstitucao;
    }

    private function getNomeEscola($escolaId)
    {
        $objEscola = new clsPmieducarEscola($escolaId);
        $detEscola = $objEscola->detalhe();
        $nomeEscola = $detEscola['nome'];

        return $nomeEscola;
    }

    private function getNomeCurso($cursoId)
    {
        $objCurso = new clsPmieducarCurso($cursoId);
        $detCurso = $objCurso->detalhe();
        $nomeCurso = $detCurso['nm_curso'];

        return $nomeCurso;
    }

    private function getNomeSerie($serieId)
    {
        $objSerie = new clsPmieducarSerie($serieId);
        $detSerie = $objSerie->detalhe();
        $nomeSerie = $detSerie['nm_serie'];

        return $nomeSerie;
    }

    private function getNomeAluno($alunoId)
    {
        $objAluno = new clsPmieducarAluno($alunoId);
        $detAluno = $objAluno->detalhe();
        $pessoaId = $detAluno['ref_idpes'];

        $objPessoa = new clsPessoa_($pessoaId);
        $detPessoa = $objPessoa->detalhe();

        return $detPessoa['nome'];
    }

    private function getNomeTurma($turmaId)
    {
        $objTurma = new clsPmieducarTurma($turmaId);
        $detTurma = $objTurma->detalhe();
        $nomeTurma = $detTurma['nm_turma'];

        return $nomeTurma;
    }

    private function getUsuarioAtual()
    {
        $pessoaId = Auth::id();
        $objFuncionario = new clsFuncionario($pessoaId);
        $detFuncionario = $objFuncionario->detalhe();
        $matricula = $detFuncionario['matricula'];

        return "{$pessoaId} - {$matricula}";
    }
}
