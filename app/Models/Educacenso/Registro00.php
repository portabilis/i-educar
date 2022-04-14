<?php

namespace App\Models\Educacenso;

class Registro00 implements RegistroEducacenso
{
    /**
     * @var string Campo 1
     */
    public $registro;

    /**
     * @var string Campo 2
     */
    public $codigoInep;

    /**
     * @var string Campo 3
     */
    public $situacaoFuncionamento;

    /**
     * @var string Campo 4
     */
    public $inicioAnoLetivo;

    /**
     * @var string Campo 5
     */
    public $fimAnoLetivo;

    /**
     * @var string Campo 6
     */
    public $nome;

    /**
     * @var string Campo 7
     */
    public $cep;

    /**
     * @var string Campo 8
     */
    public $codigoIbgeMunicipio;

    /**
     * @var string Campo 9
     */
    public $codigoIbgeDistrito;

    /**
     * @var string Campo 10
     */
    public $logradouro;

    /**
     * @var string Campo 11
     */
    public $numero;

    /**
     * @var string Campo 12
     */
    public $complemento;

    /**
     * @var string Campo 13
     */
    public $bairro;

    /**
     * @var string Campo 14
     */
    public $ddd;

    /**
     * @var string Campo 15
     */
    public $telefone;

    /**
     * @var string Campo 16
     */
    public $telefoneOutro;

    /**
     * @var string Campo 17
     */
    public $email;

    /**
     * @var string Campo 18
     */
    public $orgaoRegional;

    /**
     * @var string Campo 19
     */
    public $zonaLocalizacao;

    /**
     * @var string Campo 20
     */
    public $localizacaoDiferenciada;

    /**
     * @var string Campo 21
     */
    public $dependenciaAdministrativa;

    /**
     * @var string Campo 22
     */
    public $orgaoEducacao;

    /**
     * @var string Campo 23
     */
    public $orgaoSeguranca;

    /**
     * @var string Campo 24
     */
    public $orgaoSaude;

    /**
     * @var string Campo 25
     */
    public $orgaoOutro;

    /**
     * @var string Campo 26
     */
    public $mantenedoraEmpresa;

    /**
     * @var string Campo 27
     */
    public $mantenedoraSindicato;

    /**
     * @var string Campo 28
     */
    public $mantenedoraOng;

    /**
     * @var string Campo 29
     */
    public $mantenedoraInstituicoes;

    /**
     * @var string Campo 30
     */
    public $mantenedoraSistemaS;

    /**
     * @var string Campo 31
     */
    public $mantenedoraOscip;

    /**
     * @var string Campo 32
     */
    public $categoriaEscolaPrivada;

    /**
     * @var string Campo 33
     */
    public $conveniadaPoderPublico;

    /**
     * @var string Campo 34
     */
    public $cnpjMantenedoraPrincipal;

    /**
     * @var string Campo 35
     */
    public $cnpjEscolaPrivada;

    /**
     * @var string Campo 36
     */
    public $regulamentacao;

    /**
     * @var string Campo 37
     */
    public $esferaFederal;

    /**
     * @var string Campo 38
     */
    public $esferaEstadual;

    /**
     * @var string Campo 39
     */
    public $esferaMunicipal;

    /**
     * @var string Campo 40
     */
    public $unidadeVinculada;

    /**
     * @var string Campo 41
     */
    public $inepEscolaSede;

    /**
     * @var string Campo 42
     */
    public $codigoIes;

    /**
     * @var string Campo usado na validação
     */
    public $esferaAdministrativa;

    /**
     * @var string Campo usado na validação
     */
    public $mantenedoraEscolaPrivada;

    /**
     * @var string Campo usado na validação
     */
    public $orgaoVinculado;

    /**
     * @var integer Campo usado na validação
     */
    public $idEscola;

    /**
     * @var integer Campo usado na validação
     */
    public $idInstituicao;

    /**
     * @var integer Campo usado na validação
     */
    public $idMunicipio;

    /**
     * @var integer Campo usado na validação
     */
    public $idDistrito;

    /**
     * @var string Campo usado na validação
     */
    public $siglaUf;

    /**
     * @var string Campo usado na validação
     */
    public $anoInicioAnoLetivo;

    /**
     * @var string Campo usado na validação
     */
    public $anoFimAnoLetivo;

    /**
     * @param int $column
     *
     * @return string
     */
    public function getProperty($column)
    {
        $properties = [
            'registro',
            'codigoInep',
            'situacaoFuncionamento',
            'inicioAnoLetivo',
            'fimAnoLetivo',
            'nome',
            'cep',
            'codigoIbgeMunicipio',
            'codigoIbgeDistrito',
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'ddd',
            'telefone',
            'telefoneOutro',
            'email',
            'orgaoRegional',
            'zonaLocalizacao',
            'localizacaoDiferenciada',
            'dependenciaAdministrativa',
            'orgaoEducacao',
            'orgaoSeguranca',
            'orgaoSaude',
            'orgaoOutro',
            'mantenedoraEmpresa',
            'mantenedoraSindicato',
            'mantenedoraOng',
            'mantenedoraInstituicoes',
            'mantenedoraSistemaS',
            'mantenedoraOscip',
            'categoriaEscolaPrivada',
            'conveniadaPoderPublico',
            'cnpjMantenedoraPrincipal',
            'cnpjEscolaPrivada',
            'regulamentacao',
            'esferaFederal',
            'esferaEstadual',
            'esferaMunicipal',
            'unidadeVinculada',
            'inepEscolaSede',
            'codigoIes',
        ];

        return $properties[$column];
    }

    /**
     * Popula os campos do model a partir de um array de colunas
     * do arquivo do censo
     *
     * @param $arrayColumns
     */
    public function hydrateModel($arrayColumns)
    {
        foreach ($arrayColumns as $key => $value) {
            $this->{$this->getProperty($key)} = trim($value);
        }
    }
}
