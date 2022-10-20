<?php
use iEducar\Legacy\Model;
use App\Models\BNCC;
use App\Models\Serie;
use App\Models\ComponenteCurricular;
use App\Models\bnccSeries;
use App\Models\EspecificacaoBncc;

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;
    public $id;
    public $idpes_exc;
    public $idpes_cad;
    public $nm_raca;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $pessoa_logada;

    public function Gerar()
    {
        $this->titulo = 'BNCC - Detalhes';

        $this->id=$_GET['id'];
        $bncc = BNCC::find($this->id);
        if (! $bncc) {
            $this->simpleRedirect('educar_bncc_lst.php');
        }

       
        if(empty($bncc->componente_curricular_id)){
            $componente = ComponenteCurricular::find($bncc->campo_experiencia);
           }else{
            $componente = ComponenteCurricular::find($bncc->componente_curricular_id);
           }

        $series[] =  $bncc->serie_ids;
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

          
           $retorno_especificacoes ='<ol>';
           $especificacao = EspecificacaoBncc::where('bncc_id', $bncc->id)->get();
           foreach($especificacao as $list) {
            $retorno_especificacoes .= '<li>'.$list->especificacao.'</li><br>';   
           }
           $retorno_especificacoes .= '</ol>';


           $this->addDetalhe([ 'Código', $bncc->id]);
           $this->addDetalhe([ 'Código da Habilidade', $bncc->codigo]);
           $this->addDetalhe([ 'Habilidade', $bncc->habilidade]);
           $this->addDetalhe([ 'Componente', $componente->nome]);
           $this->addDetalhe([ 'Séries', $retorno]);
           $status= '';
           if($bncc->inativo){
            $status = 'Inativo';
           }else{
            $status = 'Ativo';
           }
           
           $this->addDetalhe([ 'Status', $status]);
           $this->addDetalhe([ 'Especificações', $retorno_especificacoes]);
        

       

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(9206, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_bncc_cad.php';
            $this->url_editar = "educar_bncc_cad.php?id={$this->id}";
        }

        $this->url_cancelar = 'educar_bncc_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do BNCC', [
            url('intranet/educar_bncc_lst.php') => 'bncc',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Detalhes BNCC';
        $this->processoAp = '9206';
    }
};
