<?php
return new class extends clsListagem{

public $pessoa_logada;
public $titulo;
public $limite;
public $offset;

public function Gerar(){
    $this->idpes = $this->pessoa_logada;
      
    $tmp_obj = new clsPmieducarAluno();
    $lst_obj = $tmp_obj->pegarMatriculaIdpes(  
        $this->idpes,
        $this->ano,
    );
   
   $registro['Matricula'] = $lst_obj[0];
  
   $this->cod_matricula = $registro['Matricula']['cod_matricula'];
  
    if (!is_numeric($this->cod_matricula)) {
       echo
            'Aviso', '<br>',
            'Você não está cadastrado como um aluno.';
       
    } else {
   
    $this->titulo = 'Historico Escolar - Listagem';

    foreach($_GET as $var => $val){
        $this->$var = ($val === '') ? null : $val;
    }

    $lista_busca = [
        'Ano',
        'Escola',
        'Turma',
        'Matricula'
        
       

    ];
    $this->addCabecalhos($lista_busca);

    
    
    $this->inputsHelper()->dynamic(['ano'], ['required' => false]);
    //$this->inputsHelper()->dynamic(['instituicao', 'escola', 'curso', 'serie', 'turma'], ['required' => false]);

    $this->campoQuebra();
    
    $this->limite = 5;
    $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite-$this->limite : 0;
    
    $obj_data = new clsPmieducarAluno(); 
    $obj_data->setOrderby('ano DESC');
    $obj_data->setLimite($this->limite, $this->offset);
    $this->idpes = $this->pessoa_logada;
    $lista = $obj_data->pegarMatriculaIdpes(
        $this->idpes,
        $this->ano,
        

       
    );
    $total = $obj_data->_total;
    
    if(is_array($lista) && count($lista)){
        foreach($lista as $registro){
           
            $lista_busca = [
                "<a href=\"educar_portal_aluno_boletim_det.php?ano={$registro['ano']}\">{$registro['ano']}</a>",
                "<a href=\"educar_portal_aluno_boletim_det.php?ano={$registro['ano']}\">{$registro['fantasia']}</a>",
                "<a href=\"educar_portal_aluno_boletim_det.php?ano={$registro['ano']}\">{$registro['nm_turma']}</a>",
                "<a href=\"educar_portal_aluno_boletim_det.php?ano={$registro['ano']}\">{$registro['cod_matricula']}</a>"
                    

            ];
            $this->addLinhas($lista_busca);
            
        }
    }
    
    $this->addPaginador2('educar_portal_aluno_boletim_lst.php',$total, $_GET, $this->nome, $this->limite);
    $this->largura = '100%';
    $this->breadcrumb('Historico Escolar', [
        url('intranet/educar_portal_aluno_index.php') => 'Portal do Aluno',
    
    ]);

}
}
 public function Formular()
{
    $this->title = 'Historico Escolar - Listagem';
    $this->processoAp = 144;
} 




};