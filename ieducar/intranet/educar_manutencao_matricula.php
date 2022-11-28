<?php
use App\Models\Matricula;
use App\Models\MatriculaTurma;
use App\Models\Turma;

return new class extends clsListagem {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $cod_aluno;
    public $ref_idpes_responsavel;
    public $ref_cod_aluno_beneficio;
    public $ref_cod_religiao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_idpes;
    public $ativo;
    public $nome_aluno;
    public $mat_aluno;
    public $identidade;
    public $matriculado;
    public $inativado;
    public $nome_responsavel;
    public $cpf_responsavel;
    public $nome_pai;
    public $nome_mae;
    public $data_nascimento;
    public $ano;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $ref_cod_curso;
    public $ref_cod_serie;
    public $cpf_aluno;
    public $rg_aluno;
    public $ref_cod_turma;

    public function Gerar()
    {
        $this->titulo = 'Manutenção de matrículas';

        $configuracoes = new clsPmieducarConfiguracoesGerais();
        $configuracoes = $configuracoes->detalhe();

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->campoNumero('cod_aluno', _cl('aluno.detalhe.codigo_aluno'), $this->cod_aluno, 20, 9, false);

        if ($configuracoes['mostrar_codigo_inep_aluno']) {
            $this->campoNumero('cod_inep', 'Código INEP', $this->cod_inep, 20, 255, false);
        }

        $this->campoRA('aluno_estado_id', 'Código rede estadual do aluno (RA)', $this->aluno_estado_id, false);
        $this->campoTexto('nome_aluno', '<b>Nome do aluno</b>', $this->nome_aluno, 50, 255, false);
        $this->campoData('data_nascimento', 'Data de Nascimento', $this->data_nascimento);
        $this->campoCpf('cpf_aluno', 'CPF', $this->cpf_aluno);
        $this->campoTexto('rg_aluno', 'RG', $this->rg_aluno);
        $this->campoTexto('nome_pai', 'Nome do Pai', $this->nome_pai, 50, 255);
        $this->campoTexto('nome_mae', 'Nome da Mãe', $this->nome_mae, 50, 255);
        $this->campoTexto('nome_responsavel', 'Nome do Responsável', $this->nome_responsavel, 50, 255);
        $this->campoRotulo('filtros_matricula', '<b>Filtros de matrículas em andamento</b>');
        $this->inputsHelper()->integer('ano', ['required' => false, 'value' => $this->ano, 'max_length' => 4]);
        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'instituicao' => $this->ref_cod_instituicao]);
        $this->inputsHelper()->dynamic('escolaSemFiltroPorUsuario', ['required' => false, 'value' => $this->ref_cod_escola]);
        $this->inputsHelper()->dynamic(['curso', 'serie', 'turma'],['required' => false]);

        $obj_permissoes = new clsPermissoes();
        $cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);

        if ($cod_escola) {
            $this->campoCheck('meus_alunos', 'Meus Alunos', $_GET['meus_alunos']);
            $ref_cod_escola = false;
            if ($_GET['meus_alunos']) {
                $ref_cod_escola = $cod_escola;
            }
        }

        if (!$configuracoes['mostrar_codigo_inep_aluno']) {
            $cabecalhos = ['Código Aluno',
                'Nome do Aluno',
                'Matrículas / Situação',
                'Ações'
              
                ];
        } else {
            $cabecalhos = ['Código Aluno',
                'Nome do Aluno',
                'Matrículas / Situação',
                'Ações'
                
                ];
        }

        $this->addCabecalhos($cabecalhos);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $aluno = new clsPmieducarAluno();
        $aluno->setLimite($this->limite, $this->offset);

        $alunos = $aluno->lista2(
            $this->cod_aluno,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            $this->nome_aluno,
            null,
            idFederal2int($this->cpf_responsavel),
            null,
            null,
            null,
            $ref_cod_escola,
            null,
            $this->data_nascimento,
            $this->nome_pai,
            $this->nome_mae,
            $this->nome_responsavel,
            $this->cod_inep,
            $this->aluno_estado_id,
            $this->ano,
            null,
            $this->ref_cod_escola,
            $this->ref_cod_curso,
            $this->ref_cod_serie,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            idFederal2int($this->cpf_aluno),
            idFederal2int($this->rg_aluno),
            $this->ref_cod_turma,
        );

        $total = $aluno->_total;

        foreach ($alunos as $registro) {
            $nomeAluno = $registro['nome_aluno'];
            $nomeSocial = $registro['nome_social'];

            if ($nomeSocial) {
                $nomeAluno = $nomeSocial . '<br> <i>Nome de registro: </i>' . $nomeAluno;
            }

            // responsavel
            $aluno->cod_aluno = $registro['cod_aluno'];
            $responsavel = $aluno->getResponsavelAluno();
            $nomeResponsavel = mb_strtoupper($responsavel['nome_responsavel']);
             
            //matriculas
            if(isset($_REQUEST['ref_cod_serie']) and !empty($_REQUEST['ref_cod_serie']) and empty($_REQUEST['ref_cod_turma'])){

                $matriculas = Matricula::where('ref_cod_aluno', $registro['cod_aluno'])->where('ref_ref_cod_serie', $_REQUEST['ref_cod_serie'])->get();  
          
            }else{
                $matriculas = Matricula::where('ref_cod_aluno', $registro['cod_aluno'])->get();
            }
           
            $conteudo_matricula = "<ol class='list-group'>";
            $conteudo_acoes_matricula = "";
            $nome_turma = "";
            foreach($matriculas as $matricula){
                
                
                $matriculasturma = MatriculaTurma::where('ref_cod_matricula', $matricula['cod_matricula'])->where('ativo', 1)->get();
  
                $codigo_serie = $matricula['ref_ref_cod_serie'];

   
                foreach($matriculasturma as $matriculaturma){
                    $nome_turma = "";
                    $codigo_turma = "";
                    if(isset($_REQUEST['ref_cod_turma']) and !empty($_REQUEST['ref_cod_turma'])){

                        if($matriculaturma['ref_cod_turma']== $_REQUEST['ref_cod_turma']){

                            $turmas = Turma::where('cod_turma', $matriculaturma['ref_cod_turma'])->get();
                            foreach($turmas as $turma){

                                $nome_turma = $turma['nm_turma'];
                                $codigo_turma = $turma['cod_turma'];

                            }
                    }
                    }else{

                    $turmas = Turma::where('cod_turma', $matriculaturma['ref_cod_turma'])->get();
                    foreach($turmas as $turma){

                        $nome_turma = $turma['nm_turma'];
                        $codigo_turma = $turma['cod_turma'];
                        
                    }
                    }
                }
                if(empty($nome_turma) or empty($codigo_turma) ){ 

                }else{
                        $situacao = App_Model_MatriculaSituacao::getSituacao($matricula['aprovado']);
                        $conteudo_matricula .= "<li class='list-group-item'><a >".$nome_turma." - ".$situacao."</a> </li> ";
                        if($situacao=='Cursando'){
                        
                           
                            $conteudo_acoes_matricula .= "<br>
                            <a style='margin:2px; color:white;' href='educar_transferencia_solicitacao_manutencao_cad.php?ref_cod_matricula=".$matricula['cod_matricula']."&ref_cod_aluno=".$registro['cod_aluno']."&ano=".$matricula['ano']."&escola=".$matricula['ref_ref_cod_escola']."&curso=".$matricula['ref_cod_curso']."&serie=".$matricula['ref_ref_cod_serie']."&turma=".$codigo_turma."' class='btn btn-info'> Transferência</a>
                            <a  class='btn btn-danger' href='educar_abandono_manutencao_cad.php?ref_cod_matricula=".$matricula['cod_matricula']."&ref_cod_aluno=".$registro['cod_aluno']."&turma=".$codigo_turma."' style='margin:2px; color:white;'> Abandono</a>
                            <a style='margin:2px; color:white; background-color: grey' href='educar_falecido_manutencao_cad.php?ref_cod_matricula=".$matricula['cod_matricula']."&ref_cod_aluno=".$registro['cod_aluno']."&turma=".$codigo_turma."' class='btn '> Falecido </a>
                            <a  class='btn btn-success' style='margin:2px; color:white;' href='educar_matricula_turma_manutencao_lst.php?ref_cod_matricula=".$matricula['cod_matricula']."&ano_letivo=".$matricula['ano']."' > Enturmar </a>";
                       
                        }elseif($situacao=='Aprovado'){
                        
                            $conteudo_acoes_matricula .= "<br><a style='margin:2px; color:white;' href='educar_transferencia_solicitacao_manutencao_cad.php?ref_cod_matricula=".$matricula['cod_matricula']."&ref_cod_aluno=".$registro['cod_aluno']."&ano=".$matricula['ano']."&escola=".$matricula['ref_ref_cod_escola']."&curso=".$matricula['ref_cod_curso']."&serie=".$matricula['ref_ref_cod_serie']."&turma=".$codigo_turma."' class='btn btn-info'> Transferência</a>"; 
                        }
                        elseif($situacao=='Transferido'){
                        
                            $conteudo_acoes_matricula .= "<br>"; 
                        }
                        elseif($situacao=='Abandono'){
                        
                            $conteudo_acoes_matricula .= "<br>"; 
                        }
                        elseif($situacao=='Falecido'){
                        
                            $conteudo_acoes_matricula .= "<br>"; 
                        }
                        else{
                            $conteudo_acoes_matricula .= "<br>";  
                        }
                    }
            }
            $conteudo_matricula .="</ol>";

            if (!$configuracoes['mostrar_codigo_inep_aluno']) {
                $linhas = [
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$registro['cod_aluno']}</a>",
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$nomeAluno}</a>",
                    $conteudo_matricula,
                    $conteudo_acoes_matricula 
                ];
            } else {
                $linhas = [
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$registro['cod_aluno']}</a>",
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$nomeAluno}</a>",
                    $conteudo_matricula,
                    $conteudo_acoes_matricula 
                ];
            }

            $this->addLinhas($linhas);
        }

        $this->addPaginador2('educar_manutencao_matricula.php', $total, $_GET, $this->nome, $this->limite);

        $bloquearCadastroAluno = dbBool($configuracoes['bloquear_cadastro_aluno']);
        $usuarioTemPermissaoCadastro = $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7);
        $usuarioPodeCadastrar = $usuarioTemPermissaoCadastro && $bloquearCadastroAluno == false;

        // Verifica se o usuário tem permissão para cadastrar um aluno.
        // O sistema irá validar o cadastro de permissões e o parâmetro
        // "bloquear_cadastro_aluno" da instituição.

        if ($usuarioPodeCadastrar) {
            $this->acao = 'go("/module/Cadastro/aluno")';
            $this->nome_acao = 'Novo';
        }
       
     
       

        $this->largura = '100%';

        Portabilis_View_Helper_Application::loadJavascript($this, ['/intranet/scripts/exporter.js']);
        Portabilis_View_Helper_Application::loadJavascript($this, ['/intranet/scripts/exporter_responsaveis.js']);

        $this->breadcrumb('Manutenção de matrículas', ['/intranet/educar_index.php' => 'Configuração']);
    }

    public function Formular()
    {
        $this->title = 'Manutenção de Matrículas';
        $this->processoAp = '7797';
    }
};
