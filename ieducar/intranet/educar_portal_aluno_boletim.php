<?php

use Facade\Ignition\DumpRecorder\Dump;
use iEducar\Legacy\Model;
use League\CommonMark\Node\Block\Document;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;
use PhpParser\Node\Expr\AssignOp\Div;

use function GuzzleHttp\Promise\settle;

return new class extends clsDetalhe {
  public $cod_turma;
  public $nome_aluno;
  public $cod_matricula;
  public $cod_serie;
  public $faltas;
  public $cod_aluno;
  public $disciplinas;
  public $idpes;
  public $nota_id;
  public $etapa;
  public $tipoetapa;

  
  
     
    public function Gerar()
    {
        $this->titulo = 'Notas e Faltas';
       
  
    
    

       $this->idpes = $this->pessoa_logada;
       $tmp_obj = new clsPmieducarAluno();
       $lst_obj = $tmp_obj->pegarMatriculaIdpes(  
        $this->idpes,

       );
      

       $registro['Matricula'] = $lst_obj;
      


       $this->cod_matricula = $registro['Matricula']['cod_matricula'];
      
      
       if(!is_numeric($this->cod_matricula)){
        $this->addDetalhe(
            [
             
             'Aviso',
             'Você não está cadastrado como um aluno.'

            ]
            
            );
            
       }else{
       


        $tmp_obj = new clsPmieducarMatricula();
        $lst_obj = $tmp_obj->lista(
           $this->cod_matricula,
            null,
            null,
            null,
            null,
            null,
            null,
        );

        $registro['Matricula'] = array_shift($lst_obj);

        $this->cod_serie = $registro['Matricula']['ref_ref_cod_serie'];
        //
        $this->disciplinas = $registro['Matricula']['ref_cod_disciplina'];
 /* ========================================================================= */
       
      
        $tmp_obj = new clsPmieducarAluno();
        $lst_obj = $tmp_obj->lista(
            null,
            null,
            null,
            null,
            null,
            $this->idpes,

        );
        $registro['Aluno'] = array_shift($lst_obj);

    

/* ======================================================================================================================= */         
     

$tmp_obj = new clsPmieducarNotaAluno();
$lst_obj = $tmp_obj->notas(
 $this->cod_matricula, 
 $this->etapa,


);

for ($i=0; $i < count($registro['Historico']); $i++) { 
        $registro['Historico'][$i]['notas'] = $lst_obj[$i]['notas'];

    }
      
/* ======================================================================================================================= */         
       $tmp_obj = new clsPmieducarMatriculaTurma();
       $lst_obj = $tmp_obj->lista(
           $this->cod_matricula,
           null,
           null,

       );
       $registro['MatriculaTurma'] = array_shift($lst_obj);
       
       $this->cod_turma = $registro['MatriculaTurma']['ref_cod_turma'];
       $this->cod_aluno = $registro['MatriculaTurma']['ref_cod_aluno'];
       $this->cod_matricula = $registro['MatriculaTurma']['ref_cod_matricula'];
/* ======================================================================================================================= */


      $tmp_obj = new clsPmieducarSerie();
      $this->tipoetapa = $tmp_obj->tipoPresencaRegraAvaliacao(
        $this->cod_serie,

      );
  
       
/* ====================================================================================================================== */


        foreach ($registro['Matricula'] as $key => $value) {
            $this->$key = $value;
        }
        foreach ($registro['Aluno'] as $key => $value) {
            $this->$key = $value;
        }
        
       if(!$registro) {
            $this->simpleRedirect('educar_portal_aluno_index.php');
        }

        $obj_permissoes = new clsPermissoes();
        
       
       if($this->cod_matricula){
           $this->addDetalhe(
               [
                
                'Número de Matrícula',
                $this->cod_matricula

               ]
               );
       }

        if($registro['Aluno']['nome_aluno']){

            $this->addDetalhe(
                [
                    'Nome',
                $registro['Aluno']['nome_aluno']
                ]

            );

        }
        if($registro['MatriculaTurma']['nm_turma']){
            $this->addDetalhe(
                [
                    'Série/Turma',
                    $registro['MatriculaTurma']['nm_turma']

                ]
                );
    
        }
     
     
        
        //$this->inputsHelper()->dynamic(['faseEtapa'], ['required' => false, 'label' => 'Etapa']);
        if($registro['Notas']['ref_cod_disciplina']){
            $this->addDetalhe(

                'Componentes Curriculares',
                $registro['Notas']['ref_cod_disciplina']
            );
        }
      /* ======================== COMPONENTES===================================== */
$cod_matricula = $this->cod_matricula;

if ($this->tipoetapa == 1) {
    
        $tmp_obj = new clsPmieducarEscolaSerieDisciplina();
        $lst_obj = $tmp_obj->componenteCurricularNomes(
            $this->cod_serie,
        );

        $registro['Historico'] = $lst_obj;


        
        $tmp_obj = new clsPmieducarEscolaSerieDisciplina(); 
        $lst_obj = $tmp_obj->faltaGeral(
         $this->cod_matricula,


         );      
        $registro['Historico'] = $lst_obj;
 
     
    $tmp_obj = new clsPmieducarNotaAluno();
    $lst_obj = $tmp_obj->notas(
     $this->cod_matricula, 
     $this->etapa,
     
    
    
    );
    for ($i=0; $i < count($registro['Historico']); $i++) { 
        $registro['Historico'][$i]['notas'] = $lst_obj[$i]['notas'];

    }
    
  
}elseif ($this->tipoetapa == 2) {
    
    $tmp_obj = new clsPmieducarEscolaSerieDisciplina(); 
    $lst_obj = $tmp_obj->faltaComponente(
        $this->cod_matricula,

     );      
     $registro['Historico'] = $lst_obj;

    $tmp_obj = new clsPmieducarNotaAluno();
    $lst_obj = $tmp_obj->notas(
     $this->cod_matricula, 
     $this->etapa,
    
    
    );
    for ($i=0; $i < count($registro['Historico']); $i++) { 
           
        $registro['Historico'][$i]['notas'] = $lst_obj[$i]['notas'];
       
           }    
}
$obj = new clsPmieducarTurmaModulo;
$modulo = $obj->lista(
    $this->cod_turma,
);

$modulo = $modulo[0]['ref_cod_modulo'];

$obj = new clsPmieducarModulo;
$etapas = $obj->lista(
    $modulo,
);
$etapas = $etapas[0]['num_etapas'];



    for ($i=0; $i <$etapas ; $i++) { 
       
   
    $this->montaListaComponentes($registro['Historico'],$i);
  
}
       
        $this->url_cancelar = 'educar_portal_aluno_index.php';
        $this->largura = '100%';

        $this->breadcrumb('Boletim', [
            url('intranet/educar_portal_aluno_boletim.php') => 'Portal do Aluno',
        ]);
     
        $scripts = [
            
            '/modules/Cadastro/Assets/Javascripts/Opcoes.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
      
    }
   
   
}


    public function montaListaComponentes($historico,$etapa){
    
        
        $this->tabela = '';
        $this->tabela .= ' <div style="margin-bottom: 10px;">';
        $this->tabela.= '  <span style="display: block; float: left; width: 400px; font-weight: bold">Nome</span>';
        $this->tabela .= ' <span style="display: block; float: left; width: 200px; font-weight: bold">Notas</span>';
        $this->tabela .= ' <span style="display: block; float: left; width: 300px; font-weight: bold">Faltas</span>';
        $this->tabela .= ' </div>';
        $this->tabela .= ' <br style="clear: left" />';
       
        foreach($historico as $registro){
     
       
            $faltas = explode(',',$registro['faltas']);   
            $notas = explode(',', $registro['notas']);
           
               
         
            $this->tabela .= '<div style = "margin-bottom: 10px; float: left;" class = "linha-disciplina"> ';
            $this->tabela .= "<span style = ' display: block; float: left; width: 400px'>{$registro['nome']}</span>";       
            $this->tabela .= "<span style = ' display: block; float: left; width: 200px'>{$notas[$etapa]}</span>"; 
            $this->tabela .= "<span style = ' display: block; float: left; width: 300px' >{$faltas[$etapa]}</span>";
    
       
    }
 
      
    
        $this->tabela .= '</div>';  
        $this->tabela .= '  <br style="clear: left" />';
   
        $registro = '<table cellspacing = "0" cellpading="0" border="0">';
        $registro .= sprintf('<tr align = "left"><td>%s</td></tr>', $this->tabela);
        $registro .= '</table>';
    
        $this->addDetalhe(
            [
                'Etapa ' . ($etapa+1) ,
                $registro
            ]
            );
 
        
    }



    public function Formular()
    {
        $this->title = 'Notas e Faltas';
        $this->processoAp = 6666;
        
    }
}


?>