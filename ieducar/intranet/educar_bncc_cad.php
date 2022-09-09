<?php

use iEducar\Legacy\Model;
use App\Models\BNCC;
use App\Models\Serie;
use App\Models\ComponenteCurricular;
use App\Models\bnccSeries;

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
                    $this->componente_curricular_id = $bncc->componente_curricular_id;
                    $this->inativo = $bncc->inativo;
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
       
            $selectOptionsComponente[$componente['id']] = $componente['nome'];
           ;
         }
       

        $selectOptionsComponente = Portabilis_Array_Utils::sortByValue($selectOptionsComponente);
        $selectOptionsComponente = array_replace([null => 'Selecione'], $selectOptionsComponente);

      
        $this->campoLista('componente_curricular_id', 'Componente Curricular', $selectOptionsComponente, $this->componente_curricular_id, '', false, '', '', '', '');

         

        //series

        $series = Serie::all();
        $a = array();
        $b = array();

    
        foreach($series as $serie){
       
           array_push($a, $serie->cod_serie);
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

 
    }

    public function Novo(){
        $data = BNCC::latest('id')->first();
        $id_bncc = $data->id + 1;
        $cadastrou =   BNCC::create( [
            'id' => $id_bncc,
            'habilidade' => $this->habilidade,
            'codigo' => $this->codigo_habilidade,
            'componente_curricular_id' => $this->componente_curricular_id,
            'inativo' => $this->inativo

          ]);

        
      //Recupera o id e cadastra na tabela intermediaria
        $bncc_id = $cadastrou->id;
        $this->series_ids  = $_POST['custom'];
        foreach ($this->series_ids as $serie_id ) {
            $data = bnccSeries::latest('id')->first();
            $id_bncc_series = $data->id + 1;
            bnccSeries::create( [
                'id' => $id_bncc_series,
                'id_bncc' => $bncc_id,
                'id_serie' => $serie_id

            ] );

        }

       
       
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
            $this->simpleRedirect('educar_bncc_lst.php' . $this->ref_cod_matricula);
        }

        
    
    }

    public function Editar()
    {
       
        BNCC::where('id', $_GET['id'])->update([
            'habilidade' => $this->habilidade,
            'codigo' => $this->codigo_habilidade,
            'componente_curricular_id' => $this->componente_curricular_id,
            'inativo' => $this->inativo
        ]);
       
        bnccSeries::where('id_bncc', $_GET['id'])->delete();

        $bncc_id = $_GET['id'];
        $this->series_ids  = $_POST['custom'];
        foreach ($this->series_ids as $serie_id ) {
             $data = bnccSeries::latest('id')->first();
            $id_bncc_series = $data->id + 1;
            bnccSeries::create( [
                'id' => $id_bncc_series,
                'id_bncc' => $bncc_id,
                'id_serie' => $serie_id

            ] );

        }
        $this->simpleRedirect('educar_bncc_lst.php');
    }

    public function Excluir()
    {
       
        BNCC::where('id', $_GET['id'])->delete(); 
        bnccSeries::where('id_bncc', $_GET['id'])->delete();
        $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
        $this->simpleRedirect('educar_bncc_lst.php');
    }

    public function Formular()
    {
        $this->title = 'BNCC';
        $this->processoAp = '9206';
    }
};
