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
    public $instituicao_id;
    public $id;
    public $codigo_habilidade;
    public $series_ids;
    public $componente_curricular_id;
    public $habilidade;
    public $retorno;
    public $unidade_tematica;
    public $campo_experiencia;
 


    public function Gerar()
    {
     
        $this->titulo = 'BNCC - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

       
        $this->campoTexto('id', 'Código', $this->id, '50', '255', false);

        $this->campoTexto('habilidade', 'Habilidade', $this->habilidade, '50', '255', false);

        $selectOptionsComponente = [];

        $componentes = ComponenteCurricular::all();
        foreach($componentes as $componente){
       
            $selectOptionsComponente[$componente['id']] = $componente['nome'];
           ;
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
            'Codigo',
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
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_BNCC = new clsModulesBNCC();
        $obj_BNCC->setOrderby('bncc.id DESC');
        $obj_BNCC->setLimite($this->limite, $this->offset);

        $lista = $obj_BNCC->lista(
           $this->id,
           $this->codigo,
           $this->habilidade,
           $this->campo_experiencia,
           $this->unidade_tematica,
           $this->componente_curricular_id
        );
       

       

        if(!empty($_GET['id'])){
            $bnccs = BNCC::where('id', $_GET['id'])->get();
            
        }elseif(!empty($_GET['componente_curricular_id']) and !empty($_GET['habilidade'])  ){
                $bnccs = BNCC::where('componente_curricular_id', $_GET['componente_curricular_id'])->where('habilidade', $_GET['habilidade'])->get();
                
        }elseif(!empty($_GET['componente_curricular_id']) and empty($_GET['habilidade'])  ){
            $bnccs = BNCC::where('componente_curricular_id', $_GET['componente_curricular_id'])->get();
            
        }elseif(empty($_GET['componente_curricular_id']) and !empty($_GET['habilidade'])  ){
            $bnccs = BNCC::where('habilidade', $_GET['habilidade'])->get();

        }else{
    $bnccs = BNCC::all()->sortByDesc("id");
   

  }

        $total = 0;
        foreach($bnccs as $bncc){
            $total ++;
           array_push($a, $serie->cod_serie);
           array_push($b, $serie->nm_serie);
 
           $componente = ComponenteCurricular::find($bncc->componente_curricular_id);
           $retorno ='<ul>';
           foreach($bncc->series as $serie){
           $retorno .= '<li>'.$serie->nm_serie.'</li>';
           }
           $retorno .= '</ul>';
           $status= '';
           if($bncc->inativo){
            $status = 'Inativo';
           }else{
            $status = 'Ativo';
           }
           
           
                $lista_busca = [
                    "<a href='educar_bncc_det.php?id=$bncc->id' >".$bncc->id."</a>",
                    "<a href='educar_bncc_det.php?id=$bncc->id' >".$bncc->habilidade."</a>",
                    "<a href='educar_bncc_det.php?id=$bncc->id' >".$retorno."</a>",
                    "<a href='educar_bncc_det.php?id=$bncc->id' >".$componente->nome."</a>",
                    "<a href='educar_bncc_det.php?id=$bncc->id' >".$status."</a>"
                    
                   
                ];

               
                $this->addLinhas($lista_busca);
            }
           
       
           
        $this->addPaginador2('educar_bncc_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(9206, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_bncc_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de BNCC', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
        
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/include/bncc/dependencias.php');
    }

 

    public function Formular()
    {
        $this->title = 'BNCC';
        $this->processoAp = 9206;
    }
};
