<?php

use iEducar\Legacy\Model;
use App\Models\BNCC;
use App\Models\Serie;
use App\Models\ComponenteCurricular;
use App\Models\bnccSeries;
use App\Models\EspecificacaoBncc;

return new class extends clsCadastro {

    public $pessoa_logada;
    public $instituicao_id;
    public $id;
    public $codigo_habilidade;
    public $series_ids;
    public $componente_curricular_id;
    public $habilidade;
    public $retorno;
    public $inativo;
    public $especificacao_bncc = [];
    public $especificacoes_cod = [];

    public function Inicializar(){
        $retorno = 'Novo';

        $this->id=$_GET['id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            9206,
            $this->pessoa_logada,
            3,
            'educar_bncc_lst.php'
        );

        if (is_numeric($this->id)) {
            $retorno = 'Editar';

            $bncc = BNCC::find($this->id);

            if ($bncc) {
                    $this->habilidade = $bncc->habilidade;
                    $this->codigo_habilidade = $bncc->codigo;
                    if(empty($bncc->componente_curricular_id)){
                        $this->componente_curricular_id = $bncc->campo_experiencia;
                       }else{
                        $this->componente_curricular_id = $bncc->componente_curricular_id;
                       }
                    
                    $this->inativo = $bncc->inativo;
                    $especificacao = EspecificacaoBncc::where('bncc_id', $bncc->id)->get();
                    //caso editar, popula a lista de especificações
                    foreach($especificacao as $list) {
                        $this->especificacoes_cod[] = [ $list->especificacao ];   
                    }
                    $series_bncc = '';
                    foreach($bncc->series as $serie){
           $series_bncc .= $serie->cod_serie.', ';
           }
         
              
                $this->fexcluir = $obj_permissoes->permissao_excluir(
                    9206,
                    $this->pessoa_logada,
                    3
                );
            }
        }

        $this->url_cancelar = 'educar_bncc_lst.php';

        $this->breadcrumb('bncc', [
        url('intranet/educar_index.php') => 'Escola',
    ]);

        $this->nome_url_cancelar = 'Cancelar';


        $this->retorno = $retorno;

        return $retorno;
    }

    public function Gerar()
    {
       
        $this->campoTexto('codigo_habilidade', 'Código da Habilidade', $this->codigo_habilidade, '50', '255', true); 
        $this->campoTexto('habilidade', 'Habilidade', $this->habilidade, '50', '255', true);
       
       
       
       
        // Componente Curricular.

      
        $selectOptionsComponente = [];

       
       
     
        $componentes = ComponenteCurricular::all();
        foreach($componentes as $componente){
            $selectOptionsComponente[$componente->id] = $componente->id.' - '.$componente->nome;
        }
       
       

        $selectOptionsComponente = Portabilis_Array_Utils::sortByValue($selectOptionsComponente);
        $selectOptionsComponente = array_replace([null => 'Selecione'], $selectOptionsComponente);

      
        $this->campoLista('componente_curricular_id', 'Componente Curricular', $selectOptionsComponente, $this->componente_curricular_id, '', false, '', '', '', '');

         

        //series

        $series = Serie::all();
        $a = array();
        $b = array();

      
        
        // Decodifica o formato JSON e retorna um Objeto
        $json = loadJsonBncc('educacenso_json/series_educacenso.json');
     
        foreach($json as $serie){
           
                array_push($a, $serie->id);
                array_push($b, $serie->nm_serie);
        }
        $c = array_combine($a, $b);
        $options = [
            'label' => 'Séries',
            'required' => true,
            'size' => 50,
            'value' => $this->$series_ids,
            'options' => [
                'all_values' =>$c
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);
       
       //inativo
       $options = ['label' => 'Inativo', 'required' => false, 'value' => dbBool($this->inativo)];

       $this->inputsHelper()->checkbox('inativo', $options);


        //especificações bncc
        $contador = 0;
        
     
       

            // especificação BNCC
    
            $this->campoTabelaInicio(
                'especificacoes_cod',
                'Especificações BNCC',
                [
                    'Especificação'
                ], ($this->especificacoes_cod)
            );
    
         
            
          
            
            $this->campoTexto('especificacao_bncc', 'Especificação BNCC', $this->especificacao_bncc); 
        
            
    
          
    
            $this->campoTabelaFim();
    


       $scripts = ['/modules/Cadastro/Assets/Javascripts/especificacao_bncc.js'];
       Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
       $styles = ['/modules/Cadastro/Assets/Stylesheets/especificacao_bncc.css'];
       Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
 
    }

    public function Novo(){
        $data = BNCC::latest('id')->first();
        $id_bncc = $data->id + 1;


        $retorno = '{';
            $this->series_ids  = $_POST['custom'];
            $contador = 0;
            $contador_campo_experiencia = 0;
            foreach ($this->series_ids as $serie_id ) {
        
                if($contador>0){
                    $retorno .= ', '.$serie_id;
                }else{
                    $retorno .= ''.$serie_id;  
                }
               
                  $contador++;
                  if( $serie_id == 1 || $serie_id == 2 || $serie_id == 3){
                    $contador_campo_experiencia++;
                  }
            }
    
          
        
        $retorno .= '}';
        if($contador_campo_experiencia>0){
            $cadastrou =   BNCC::create( [
                'id' => $id_bncc,
                'habilidade' => $this->habilidade,
                'codigo' => $this->codigo_habilidade,
                'campo_experiencia' => $this->componente_curricular_id,
                'inativo' => $this->inativo,
                'serie_ids' => $retorno
              ]);
        }else{
            $cadastrou =   BNCC::create( [
                'id' => $id_bncc,
                'habilidade' => $this->habilidade,
                'codigo' => $this->codigo_habilidade,
                'componente_curricular_id' => $this->componente_curricular_id,
                'inativo' => $this->inativo,
                'serie_ids' => $retorno
              ]);
        }
       
        

        
      //Recupera o id e cadastra na tabela intermediaria
        $bncc_id = $cadastrou->id;

        $this->especficacoes_cad  = $_POST['especificacao_bncc'];
        foreach ($this->especficacoes_cad as $especficacao_text ) {
            $data = EspecificacaoBncc::latest('id')->first();
            $id_especificacao = $data->id + 1;
            EspecificacaoBncc::create( [
                'id' => $id_especificacao,
                'bncc_id' => $bncc_id,
                'especificacao' => $especficacao_text

            ] );
            

        }

       
       
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
            $this->simpleRedirect('educar_bncc_lst.php' . $this->ref_cod_matricula);
        }

        
    
    }

    public function Editar()
    {
        $retorno = '{';
            $this->series_ids  = $_POST['custom'];
            $contador = 0;
            foreach ($this->series_ids as $serie_id ) {
                if($contador>0){
                    $retorno .= ', '.$serie_id;
                }else{
                    $retorno .= ''.$serie_id;  
                }
               
                  $contador++;
            }
    
          
        
        $retorno .= '}';
       
        BNCC::where('id', $_GET['id'])->update([
            'habilidade' => $this->habilidade,
            'codigo' => $this->codigo_habilidade,
            'componente_curricular_id' => $this->componente_curricular_id,
            'inativo' => $this->inativo,
            'serie_ids' => $retorno
        ]);
       
        

        $bncc_id = $_GET['id'];
        EspecificacaoBncc::where('bncc_id', $_GET['id'])->delete();

        $this->especficacoes_cad  = $_POST['especificacao_bncc'];
        foreach ($this->especficacoes_cad as $especficacao_text ) {
            $data = EspecificacaoBncc::latest('id')->first();
            $id_especificacao = $data->id + 1;
            EspecificacaoBncc::create( [
                'id' => $id_especificacao,
                'bncc_id' => $bncc_id,
                'especificacao' => $especficacao_text

            ] );
            

        }
        $this->simpleRedirect('educar_bncc_lst.php');
    }

    public function Excluir()
    {
       
        BNCC::where('id', $_GET['id'])->delete(); 
        bnccSeries::where('id_bncc', $_GET['id'])->delete();
        EspecificacaoBncc::where('bncc_id', $_GET['id'])->delete();
        $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
        $this->simpleRedirect('educar_bncc_lst.php');
    }

    public function Formular()
    {
        $this->title = 'BNCC';
        $this->processoAp = '9206';
    }
};
