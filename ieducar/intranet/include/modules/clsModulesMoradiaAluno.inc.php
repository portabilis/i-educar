<?php

use iEducar\Legacy\Model;

class clsModulesMoradiaAluno extends Model
{
    public $ref_cod_aluno;
    public $moradia;
    public $material;
    public $casa_outra;
    public $moradia_situacao;
    public $quartos;
    public $sala;
    public $copa;
    public $banheiro;
    public $garagem;
    public $empregada_domestica;
    public $automovel;
    public $motocicleta;
    public $geladeira;
    public $fogao;
    public $maquina_lavar;
    public $microondas;
    public $video_dvd;
    public $televisao;
    public $telefone;
    public $recursos_tecnologicos;
    public $quant_pessoas;
    public $renda;
    public $agua_encanada;
    public $poco;
    public $energia;
    public $esgoto;
    public $fossa;
    public $lixo;

    public function __construct(
        $ref_cod_aluno = null,
        $moradia = null,
        $material = null,
        $casa_outra = null,
        $moradia_situacao = null,
        $quartos = null,
        $sala = null,
        $copa = null,
        $banheiro = null,
        $garagem = null,
        $empregada_domestica = null,
        $automovel = null,
        $motocicleta = null,
        $geladeira = null,
        $fogao = null,
        $maquina_lavar = null,
        $microondas = null,
        $video_dvd = null,
        $televisao = null,
        $telefone = null,
        $recursos_tecnologicos = null,
        $quant_pessoas = null,
        $renda = null,
        $agua_encanada = null,
        $poco = null,
        $energia = null,
        $esgoto = null,
        $fossa = null,
        $lixo = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}moradia_aluno";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_aluno,
        moradia, material, casa_outra, moradia_situacao,
        quartos, sala, copa, banheiro, garagem, empregada_domestica,
      automovel, motocicleta, geladeira, fogao, maquina_lavar, microondas, video_dvd,televisao, telefone, recursos_tecnologicos, quant_pessoas, renda, agua_encanada, poco, energia, esgoto, fossa, lixo';

        if (is_numeric($ref_cod_aluno)) {
            $this->ref_cod_aluno = $ref_cod_aluno;
        }

        if (is_string($moradia)) {
            $this->moradia = $moradia;
        }

        if (is_string($material)) {
            $this->material = $material;
        }

        if (is_string($casa_outra)) {
            $this->casa_outra = $casa_outra;
        }

        if (is_numeric($moradia_situacao)) {
            $this->moradia_situacao = $moradia_situacao;
        }

        if (is_numeric($quartos)) {
            $this->quartos = $quartos;
        }

        if (is_numeric($sala)) {
            $this->sala = $sala;
        }

        if (is_numeric($copa)) {
            $this->copa = $copa;
        }

        if (is_numeric($banheiro)) {
            $this->banheiro = $banheiro;
        }

        if (is_numeric($garagem)) {
            $this->garagem = $garagem;
        }

        if (is_string($empregada_domestica)) {
            $this->empregada_domestica = $empregada_domestica;
        }

        if (is_string($motocicleta)) {
            $this->motocicleta = $motocicleta;
        }

        if (is_string($geladeira)) {
            $this->geladeira = $geladeira;
        }

        if (is_string($fogao)) {
            $this->fogao = $fogao;
        }

        if (is_string($maquina_lavar)) {
            $this->maquina_lavar = $maquina_lavar;
        }

        if (is_string($microondas)) {
            $this->microondas = $microondas;
        }

        if (is_string($video_dvd)) {
            $this->video_dvd = $video_dvd;
        }

        if (is_string($televisao)) {
            $this->televisao = $televisao;
        }

        if (is_string($telefone)) {
            $this->telefone = $telefone;
        }

        if (is_string($recursos_tecnologicos)) {
            $this->recursos_tecnologicos = $recursos_tecnologicos;
        }

        if (is_string($quant_pessoas)) {
            $this->quant_pessoas = $quant_pessoas;
        }

        if (is_numeric($renda)) {
            $this->renda = $renda;
        }

        if (is_numeric($agua_encanada)) {
            $this->agua_encanada = $agua_encanada;
        }

        if (is_string($poco)) {
            $this->poco = $poco;
        }

        if (is_string($energia)) {
            $this->energia = $energia;
        }

        if (is_string($esgoto)) {
            $this->esgoto = $esgoto;
        }

        if (is_string($fossa)) {
            $this->fossa = $fossa;
        }

        if (is_string($lixo)) {
            $this->lixo = $lixo;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_aluno)) {
                $campos .= "{$gruda}ref_cod_aluno";
                $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
                $gruda = ', ';
            }

            if (is_string($this->moradia)) {
                $campos .= "{$gruda}moradia";
                $valores .= "{$gruda}'{$this->moradia}'";
                $gruda = ', ';
            }

            if (is_string($this->material)) {
                $campos .= "{$gruda}material";
                $valores .= "{$gruda}'{$this->material}'";
                $gruda = ', ';
            }

            if (is_string($this->casa_outra)) {
                $campos .= "{$gruda}casa_outra";
                $valores .= "{$gruda}'{$this->casa_outra}'";
                $gruda = ', ';
            }

            if (is_numeric($this->moradia_situacao)) {
                $campos .= "{$gruda}moradia_situacao";
                $valores .= "{$gruda}'{$this->moradia_situacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->quartos)) {
                $campos .= "{$gruda}quartos";
                $valores .= "{$gruda}'{$this->quartos}'";
                $gruda = ', ';
            }

            if (is_numeric($this->sala)) {
                $campos .= "{$gruda}sala";
                $valores .= "{$gruda}'{$this->sala}'";
                $gruda = ', ';
            }

            if (is_numeric($this->copa)) {
                $campos .= "{$gruda}copa";
                $valores .= "{$gruda}'{$this->copa}'";
                $gruda = ', ';
            }

            if (is_numeric($this->banheiro)) {
                $campos .= "{$gruda}banheiro";
                $valores .= "{$gruda}'{$this->banheiro}'";
                $gruda = ', ';
            }

            if (is_numeric($this->garagem)) {
                $campos .= "{$gruda}garagem";
                $valores .= "{$gruda}'{$this->garagem}'";
                $gruda = ', ';
            }

            if (is_string($this->empregada_domestica)) {
                $campos .= "{$gruda}empregada_domestica";
                $valores .= "{$gruda}'{$this->empregada_domestica}'";
                $gruda = ', ';
            }

            if (is_string($this->automovel)) {
                $campos .= "{$gruda}automovel";
                $valores .= "{$gruda}'{$this->automovel}'";
                $gruda = ', ';
            }

            if (is_string($this->motocicleta)) {
                $campos .= "{$gruda}motocicleta";
                $valores .= "{$gruda}'{$this->motocicleta}'";
                $gruda = ', ';
            }

            if (is_string($this->geladeira)) {
                $campos .= "{$gruda}geladeira";
                $valores .= "{$gruda}'{$this->geladeira}'";
                $gruda = ', ';
            }

            if (is_string($this->fogao)) {
                $campos .= "{$gruda}fogao";
                $valores .= "{$gruda}'{$this->fogao}'";
                $gruda = ', ';
            }

            if (is_string($this->maquina_lavar)) {
                $campos .= "{$gruda}maquina_lavar";
                $valores .= "{$gruda}'{$this->maquina_lavar}'";
                $gruda = ', ';
            }

            if (is_string($this->microondas)) {
                $campos .= "{$gruda}microondas";
                $valores .= "{$gruda}'{$this->microondas}'";
                $gruda = ', ';
            }

            if (is_string($this->video_dvd)) {
                $campos .= "{$gruda}video_dvd";
                $valores .= "{$gruda}'{$this->video_dvd}'";
                $gruda = ', ';
            }

            if (is_string($this->televisao)) {
                $campos .= "{$gruda}televisao";
                $valores .= "{$gruda}'{$this->televisao}'";
                $gruda = ', ';
            }

            if (is_string($this->telefone)) {
                $campos .= "{$gruda}telefone";
                $valores .= "{$gruda}'{$this->telefone}'";
                $gruda = ', ';
            }

            if (is_string($this->recursos_tecnologicos)) {
                $campos .= "{$gruda}recursos_tecnologicos";
                $valores .= "{$gruda}'{$this->recursos_tecnologicos}'";
                $gruda = ', ';
            }

            if (is_numeric($this->quant_pessoas)) {
                $campos .= "{$gruda}quant_pessoas";
                $valores .= "{$gruda}'{$this->quant_pessoas}'";
                $gruda = ', ';
            }

            if (is_numeric($this->renda)) {
                $campos .= "{$gruda}renda";
                $valores .= "{$gruda}'{$this->renda}'";
                $gruda = ', ';
            }

            if (is_string($this->agua_encanada)) {
                $campos .= "{$gruda}agua_encanada";
                $valores .= "{$gruda}'{$this->agua_encanada}'";
                $gruda = ', ';
            }

            if (is_string($this->poco)) {
                $campos .= "{$gruda}poco";
                $valores .= "{$gruda}'{$this->poco}'";
                $gruda = ', ';
            }

            if (is_string($this->energia)) {
                $campos .= "{$gruda}energia";
                $valores .= "{$gruda}'{$this->energia}'";
                $gruda = ', ';
            }

            if (is_string($this->esgoto)) {
                $campos .= "{$gruda}esgoto";
                $valores .= "{$gruda}'{$this->esgoto}'";
                $gruda = ', ';
            }

            if (is_string($this->fossa)) {
                $campos .= "{$gruda}fossa";
                $valores .= "{$gruda}'{$this->fossa}'";
                $gruda = ', ';
            }

            if (is_string($this->lixo)) {
                $campos .= "{$gruda}lixo";
                $valores .= "{$gruda}'{$this->lixo}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $this->ref_cod_aluno;
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_cod_aluno)) {
                $set .= "{$gruda}ref_cod_aluno = '{$this->ref_cod_aluno}'";
                $gruda = ', ';
            }

            if (is_string($this->moradia)) {
                $set .= "{$gruda}moradia = '{$this->moradia}'";
                $gruda = ', ';
            }

            if (is_string($this->material)) {
                $set .= "{$gruda}material = '{$this->material}'";
                $gruda = ', ';
            }

            if (is_string($this->casa_outra)) {
                $set .= "{$gruda}casa_outra = '{$this->casa_outra}'";
                $gruda = ', ';
            }

            if (is_numeric($this->moradia_situacao)) {
                $set .= "{$gruda}moradia_situacao = '{$this->moradia_situacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->quartos)) {
                $set .= "{$gruda}quartos = '{$this->quartos}'";
                $gruda = ', ';
            }

            if (is_numeric($this->sala)) {
                $set .= "{$gruda}sala = '{$this->sala}'";
                $gruda = ', ';
            }

            if (is_numeric($this->copa)) {
                $set .= "{$gruda}copa = '{$this->copa}'";
                $gruda = ', ';
            }

            if (is_numeric($this->banheiro)) {
                $set .= "{$gruda}banheiro = '{$this->banheiro}'";
                $gruda = ', ';
            }

            if (is_numeric($this->garagem)) {
                $set .= "{$gruda}garagem = '{$this->garagem}'";
                $gruda = ', ';
            }

            if (is_string($this->empregada_domestica)) {
                $set .= "{$gruda}empregada_domestica = '{$this->empregada_domestica}'";
                $gruda = ', ';
            }

            if (is_string($this->automovel)) {
                $set .= "{$gruda}automovel = '{$this->automovel}'";
                $gruda = ', ';
            }

            if (is_string($this->motocicleta)) {
                $set .= "{$gruda}motocicleta = '{$this->motocicleta}'";
                $gruda = ', ';
            }

            if (is_string($this->geladeira)) {
                $set .= "{$gruda}geladeira = '{$this->geladeira}'";
                $gruda = ', ';
            }

            if (is_string($this->fogao)) {
                $set .= "{$gruda}fogao = '{$this->fogao}'";
                $gruda = ', ';
            }

            if (is_string($this->maquina_lavar)) {
                $set .= "{$gruda}maquina_lavar = '{$this->maquina_lavar}'";
                $gruda = ', ';
            }

            if (is_string($this->microondas)) {
                $set .= "{$gruda}microondas = '{$this->microondas}'";
                $gruda = ', ';
            }

            if (is_string($this->video_dvd)) {
                $set .= "{$gruda}video_dvd = '{$this->video_dvd}'";
                $gruda = ', ';
            }

            if (is_string($this->televisao)) {
                $set .= "{$gruda}televisao = '{$this->televisao}'";
                $gruda = ', ';
            }

            if (is_string($this->telefone)) {
                $set .= "{$gruda}telefone = '{$this->telefone}'";
                $gruda = ', ';
            }

            if (is_string($this->recursos_tecnologicos)) {
                $set .= "{$gruda}recursos_tecnologicos = '{$this->recursos_tecnologicos}'";
                $gruda = ', ';
            }

            if (is_numeric($this->quant_pessoas)) {
                $set .= "{$gruda}quant_pessoas = '{$this->quant_pessoas}'";
                $gruda = ', ';
            }

            if (is_numeric($this->renda)) {
                $set .= "{$gruda}renda = '{$this->renda}'";
                $gruda = ', ';
            }

            if (is_string($this->agua_encanada)) {
                $set .= "{$gruda}agua_encanada = '{$this->agua_encanada}'";
                $gruda = ', ';
            }

            if (is_string($this->poco)) {
                $set .= "{$gruda}poco = '{$this->poco}'";
                $gruda = ', ';
            }

            if (is_string($this->energia)) {
                $set .= "{$gruda}energia = '{$this->energia}'";
                $gruda = ', ';
            }

            if (is_string($this->esgoto)) {
                $set .= "{$gruda}esgoto = '{$this->esgoto}'";
                $gruda = ', ';
            }

            if (is_string($this->fossa)) {
                $set .= "{$gruda}fossa = '{$this->fossa}'";
                $gruda = ', ';
            }

            if (is_string($this->lixo)) {
                $set .= "{$gruda}lixo = '{$this->lixo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function lista()
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';
        $whereAnd = ' WHERE ';

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista)) + 2;
        $resultado = [];

        $sql .= $filtros . $whereNomes . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

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
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->ref_cod_aluno)) {
            $sql = "DELETE FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }
}
