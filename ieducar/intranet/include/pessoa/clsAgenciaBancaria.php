<?php

use iEducar\Legacy\Model;

class clsAgenciaBancaria extends Model
{
    public $codigo;
    public $nome;
    public $tabela = 'banco';
    public $schema = 'cadastro';

    public function __construct($codigo = false, $nome = false)
    {
        $this->codigo = $codigo;
        $this->nome = $nome;
    }

    public function lista($codigo)
    {
        $sql = "SELECT codigo,nome FROM cadastro.banco";
        $filtros = '';
        $whereAnd = ' AND';

        if(is_integer($codigo))
        {
            $filtros .="{$whereAnd} codigo = '$codigo' ";
            $whereAnd = ' AND';
        }
        $db = new clsBanco();
        $countCampos = count(explode(',','codigo,nome'));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM cadastro.banco");
        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }

        }else{
                while ($db->ProximoRegistro()) {
                    $tupla = $db->Tupla();
                    $resultado[] = $tupla['codigo,nome'];
                }
            }
            if (count($resultado)) {
                return $resultado;
            }
          
            return false;
    }
    public function detalhe ()
    {
        if($this->codigo){
            $db = new clsBanco();
            $db->Consulta("SELECT codigo, nome FROM {$this->esquema}.{$this->tabela}  WHERE codigo = {$this->codigo}");
           
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
    
                return $tupla;
            }
        }
        return false;
    }

 

}