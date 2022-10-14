<?php
use iEducar\Legacy\Model;
use App\Models\BNCC;
use App\Models\Serie;
use App\Models\ComponenteCurricular;
use App\Models\bnccSeries;
 
return new class extends clsListagem {
  
   public $limite;
   public $offset;
   public $inativo;
   public $pessoa_logada;
   public $id;
   public $codigo;
   public $series_ids;
   public $componente_curricular_id;
   public $habilidade;
   public $retorno;
 
 
 
   public function Gerar()
   {
   
       $this->titulo = 'BNCC - Listagem';
 
       foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
           $this->$var = ($val === '') ? null: $val;
       }
 
     
       $this->campoTexto('codigo', 'Código da Habilidade', $this->id, '50', '255', false);
 
       $this->campoTexto('habilidade', 'Habilidade', $this->habilidade, '50', '255', false);
 
       $selectOptionsComponente = [];
 
       $componentes = ComponenteCurricular::all();
       foreach($componentes as $componente){
     
           $selectOptionsComponente[$componente['id']] = $componente['nome'];
          
        }
     
 
       $selectOptionsComponente = Portabilis_Array_Utils::sortByValue($selectOptionsComponente);
       $selectOptionsComponente = array_replace([null => 'Selecione'], $selectOptionsComponente);
 
 
    
 
       $this->campoLista('componente_curricular_id', 'Componente Curricular', $selectOptionsComponente, $this->componente_curricular_id, '', true, '', '', '', '');
  
 
       $this->largura = '100%';
      
 
$selectOptionsComponente = [];
 
       $componentes = ComponenteCurricular::all();
       foreach($componentes as $componente){
     
           $selectOptionsComponente[$componente['id']] = $componente['nome'];
          ;
        }
     
 
       $selectOptionsComponente = Portabilis_Array_Utils::sortByValue($selectOptionsComponente);
       $selectOptionsComponente = array_replace([null => 'Selecione'], $selectOptionsComponente);
 
 
    
       $this->campoLista('componente_curricular_id', 'Componente Curricular', $selectOptionsComponente, $this->componente_curricular_id, '', true, '', '', '', '');
 
       $lista_busca = [
           'Código da Habilidade',
           'Habilidade',
           'Séries',
           'Componente',
           'Status'
       ];
 
       $obj_permissoes = new clsPermissoes();
       $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
      
       $this->addCabecalhos($lista_busca);
 
       // Filtros de Foreign Keys
  
 
       // primary keys
  
 
 
      
       // Paginador
       $limite = 20;
       $iniciolimit = ($this->getQueryString("pagina_{$this->nome}")) ? $this->getQueryString("pagina_{$this->nome}")*$limite-$limite: 0;
 
       $obj_BNCC = new clsModulesBNCC();
       $obj_BNCC->setOrderby('bncc.id ASC');
       $obj_BNCC->setLimite($limite, $iniciolimit);
 
       $lista = $obj_BNCC->lista_bncc();
     
 
     
 
       if(!empty($_GET['codigo'])){
           $bnccs = BNCC::where('codigo', $_GET['codigo'])->get();
          
       }elseif(!empty($_GET['componente_curricular_id']) and !empty($_GET['habilidade'])  ){
               $bnccs = BNCC::where('componente_curricular_id', $_GET['componente_curricular_id'])->where('habilidade', $_GET['habilidade'])->get();
              
       }elseif(!empty($_GET['componente_curricular_id']) and empty($_GET['habilidade'])  ){
           $bnccs = BNCC::where('componente_curricular_id', $_GET['componente_curricular_id'])->get();
          
       }elseif(empty($_GET['componente_curricular_id']) and !empty($_GET['habilidade'])  ){
           $bnccs = BNCC::where('habilidade', $_GET['habilidade'])->get();
 
       }else{
           $total = 0;
           $bnccs_total =  BNCC::all();
           foreach($bnccs_total as $bncc_total){
               $total ++;
           }
   $bnccs =  $obj_BNCC->lista_bncc();
  
    
 
 
 }
 
    
     
       foreach($bnccs as $bncc){

       if(empty($bncc['componente_curricular_id'])){
        $componente = ComponenteCurricular::find($bncc['campo_experiencia']);
       }else{
        $componente = ComponenteCurricular::find($bncc['componente_curricular_id']);
       }
        $bncc_serie = BNCC::find($bncc['id']);
        $series[] =  $bncc_serie->serie_ids;
        $retorno = '<ul style="width: 200px">';
       foreach($series as $serie_id){
        $limpa =  substr($serie_id, 1);
        $limpa =  substr($limpa, 0, -1);

        $array = explode(',',$limpa);
        foreach($array  as $serie_id){
        
        $json = loadJsonBncc('educacenso_json/series_educacenso.json');
     
        foreach($json as $registro){
           
                 if($registro->id==$serie_id){
                    $retorno .= '<li>'.$registro->nm_serie.'</li>';
                 }
               
        }
            
       
         
        }
          unset($series);
       }
       $retorno .= '</ul>';
    
          $status= '';
          if($bncc['inativo']){
           $status = 'Inativo';
          }else{
           $status = 'Ativo';
          }
         
         
               $lista_busca = [
                   "<a href='educar_bncc_det.php?id=".$bncc['id']."' >".$bncc['codigo']." </a>",
                   "<a href='educar_bncc_det.php?id=".$bncc['id']."' >".$bncc['habilidade']." </a>",
                   "<a href='educar_bncc_det.php?id=".$bncc['id']."' >".$retorno."</a>",
                   "<a href='educar_bncc_det.php?id=".$bncc['id']."' >".$componente->nome."</a>",
                   "<a href='educar_bncc_det.php?id=".$bncc['id']."' >".$status."</a>"
                  
                 
               ];
 
             
               $this->addLinhas($lista_busca);
           }
      
         
         
  
       $obj_permissoes = new clsPermissoes();
       if ($obj_permissoes->permissao_cadastra(9206, $this->pessoa_logada, 3)) {
           $this->acao = 'go("educar_bncc_cad.php")';
           $this->nome_acao = 'Novo';
       }
 
     
       $this->largura = '100%';
       $this->addPaginador2('educar_bncc_lst.php', $total, $_GET, $this->nome, $limite);
       $this->breadcrumb('Listagem de BNCC', [
           url('intranet/educar_bncc_lst.php') => 'BNCC',
       ]);
      
   }
 
   public function makeExtra()
   {
       return file_get_contents(__DIR__ . '/include/bncc/dependencias.php');
   }
 
 
   public function Formular()
   {
       $this->title = 'BNCC';
       $this->processoAp = '9206';
   }
};
 

