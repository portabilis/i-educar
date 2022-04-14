<?php

class clsPessoaJuridica extends clsPessoaFj
{
    public $idpes;
    public $cnpj;
    public $fantasia;
    public $insc_estadual;
    public $capital_social;
    public $banco = 'gestao_homolog';
    public $schema_cadastro = 'cadastro';
    public $codUsuario;

    public function __construct($int_idpes = false, $numeric_cnpj = false, $str_fantasia = false, $numeric_insc_estadual = false, $numeric_capital_social = false)
    {
        $this->idpes = $int_idpes;
        $this->cnpj = $numeric_cnpj;
        $this->fantasia = $str_fantasia;
        $this->insc_estadual = $numeric_insc_estadual;
        $this->capital_social = $numeric_capital_social;
    }

    public function lista($numeric_cnpj = false, $str_fantasia = false, $numeric_insc_estadual = false, $inicio_limit = false, $fim_limite = false, $str_ordenacao = false, $arrayint_idisin = false, $arrayint_idnotin = false, $int_idpes = false)
    {
        $objJuridica = new clsJuridica();
        $objJuridica->codUsuario = $this->codUsuario;
        $lista = $objJuridica->lista($str_fantasia, false, $numeric_cnpj, $str_ordenacao, $inicio_limit, $fim_limite, $arrayint_idisin, $arrayint_idnotin, $int_idpes);
        if ($lista) {
            foreach ($lista as $linha) {
                $this->fantasia = $linha['fantasia'];
                $this->cnpj = $linha['cnpj'];
                $this->insc_estadual = $linha['insc_estadual'];
                $this->capital_social = $linha['capital_social'];
                $this->idpes = $linha['idpes'];
                $this->total = $linha['total'];
                $tupla = parent::detalhe();
                $tupla['insc_estadual'] = $this->insc_estadual;
                $tupla[] = &$tupla['insc_estadual'];
                $tupla['capital_social'] = $this->capital_social;
                $tupla[] = &$tupla['capital_social'];
                $tupla['cnpj'] = $this->cnpj;
                $tupla[] = &$tupla['cnpj'];
                $tupla['fantasia'] = $this->fantasia;
                $tupla[] = &$tupla['fantasia'];
                $tupla['total'] = $this->total;
                $tupla[] = &$tupla['total'];

                $resultado[] = $tupla;
            }
        }
        if (count($resultado) > 0) {
            return $resultado;
        }
    }

    public function detalhe()
    {
        if ($this->idpes) {
            $tupla = parent::detalhe();

            $objJuridica = new clsJuridica($this->idpes);
            $detalhe = $objJuridica->detalhe();
            if ($detalhe) {
                $this->fantasia = $detalhe['fantasia'];
                $this->cnpj = $detalhe['cnpj'];
                $this->insc_estadual = $detalhe['insc_estadual'];
                $this->capital_social = $detalhe['capital_social'];

                $tupla['insc_estadual'] = $this->insc_estadual;
                $tupla[] = &$tupla['insc_estadual'];
                $tupla['capital_social'] = $this->capital_social;
                $tupla[] = &$tupla['capital_social'];
                $tupla['cnpj'] = $this->cnpj;
                $tupla[] = &$tupla['cnpj'];
                $tupla['fantasia'] = $this->fantasia;
                $tupla[] = &$tupla['fantasia'];
            }

            return $tupla;
        } elseif ($this->cnpj) {
            $objJuridica = new clsJuridica(false, $this->cnpj);
            $detalhe = $objJuridica->detalhe();
            if ($detalhe) {
                $this->fantasia = $detalhe['fantasia'];
                $this->cnpj = $detalhe['cnpj'];
                $this->insc_estadual = $detalhe['insc_estadual'];
                $this->capital_social = $detalhe['capital_social'];
                $this->idpes = $detalhe['idpes'];
                $tupla = parent::detalhe();
                $tupla['insc_estadual'] = $this->insc_estadual;
                $tupla[] = &$tupla['insc_estadual'];
                $tupla['capital_social'] = $this->capital_social;
                $tupla[] = &$tupla['capital_social'];

                $tupla['cnpj'] = $this->cnpj;
                $tupla[] = &$tupla['cnpj'];
                $tupla['fantasia'] = $this->fantasia;
                $tupla[] = &$tupla['fantasia'];
            }

            return $tupla;
        }

        return false;
    }

    public function queryRapida($int_idpes)
    {
        $this->idpes = $int_idpes;
        $this->detalhe();
        $resultado = [];
        $pos = 0;
        for ($i = 1; $i < func_num_args(); $i++) {
            $campo = func_get_arg($i);
            $resultado[$pos] = ($this->$campo) ? $this->$campo : '';
            $resultado[$campo] =  &$resultado[$pos];
            $pos++;
        }
        if (count($resultado) > 0) {
            return $resultado;
        }

        return false;
    }

    public function queryRapidaCNPJ($int_CNPJ)
    {
        $this->cnpj = $int_CNPJ;
        $this->detalhe();
        $resultado = [];
        $pos = 0;
        for ($i = 1; $i < func_num_args(); $i++) {
            $campo = func_get_arg($i);
            $resultado[$pos] = ($this->$campo) ? $this->$campo : '';
            $resultado[$campo] =  &$resultado[$pos];
            $pos++;
        }
        if (count($resultado) > 0) {
            return $resultado;
        }

        return false;
    }
}
