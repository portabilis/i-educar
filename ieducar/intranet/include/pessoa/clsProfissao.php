<?php

use iEducar\Legacy\Model;
class clsProfissao extends Model
{

    public $cod_profissao;
    public $nm_profissao;
    public $tabela = 'profissao';
    public $schema = 'cadastro';


    public function __construct($cod_profissao = false, $nm_profissão = false)
    {
        $this->codigo = $cod_profissao;
        $this->nome = $nm_profissão;
     
    }

    

    public function lista($cod_profissao)
    {
        $sql = "SELECT cod_profissao, nm_profissao FROM cadastro.profissao";
        $filtros = '';
        $whereAnd = ' AND';
      

        if(is_integer($cod_profissao)){
            $filtros .= "{$whereAnd} cod_profissao = '$cod_profissao'";
            $whereAnd = ' AND';
        }
       
        $db = new clsBanco();
        $countCampos = count(explode(',', 'cod_profissao , nm_profissão'));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM cadastro.profissao");
        $db->Consulta($sql);
       
        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla['cod_profissao , nm_profissão'];
            }
        }
        
        
        if (count($resultado)) {
            return $resultado;
        }
      

        return false;
    }


   public function detalhe()
   {
    if ($this->cod_profissao) {
        $db = new clsBanco();
        $db->Consulta("SELECT cod_profissao, nm_profissao FROM {$this->schema}.{$this->tabela} WHERE cod_profissao = {$this->cod_profissao}");
        if ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();

            return $tupla;
        }
    }

    return false;
   } 


}    