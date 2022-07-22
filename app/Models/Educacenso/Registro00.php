<?php

namespace App\Models\Educacenso;

class Registro00 implements RegistroEducacenso
{
    /**
     * @var string
     */
    public $registro;

    /**
     * @var string
     */
    public $codigoInep;

    /**
     * @var string
     */
    public $situacaoFuncionamento;

    /**
     * @var string
     */
    public $inicioAnoLetivo;

    /**
     * @var string
     */
    public $fimAnoLetivo;

    /**
     * @var string
     */
    public $nome;

    /**
     * @var string
     */
    public $cep;

    /**
     * @var string
     */
    public $codigoIbgeMunicipio;

    /**
     * @var string
     */
    public $codigoIbgeDistrito;

    /**
     * @var string
     */
    public $logradouro;

    /**
     * @var string
     */
    public $numero;

    /**
     * @var string
     */
    public $complemento;

    /**
     * @var string
     */
    public $bairro;

    /**
     * @var string
     */
    public $ddd;

    /**
     * @var string
     */
    public $telefone;

    /**
     * @var string
     */
    public $telefoneOutro;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $orgaoRegional;

    /**
     * @var string
     */
    public $zonaLocalizacao;

    /**
     * @var string
     */
    public $localizacaoDiferenciada;

    /**
     * @var string
     */
    public $dependenciaAdministrativa;

    /**
     * @var string
     */
    public $orgaoEducacao;

    /**
     * @var string
     */
    public $orgaoSeguranca;

    /**
     * @var string
     */
    public $orgaoSaude;

    /**
     * @var string
     */
    public $orgaoOutro;

    /**
     * @var string
     */
    public $mantenedoraEmpresa;

    /**
     * @var string
     */
    public $mantenedoraSindicato;

    /**
     * @var string
     */
    public $mantenedoraOng;

    /**
     * @var string
     */
    public $mantenedoraInstituicoes;

    /**
     * @var string
     */
    public $mantenedoraSistemaS;

    /**
     * @var string
     */
    public $mantenedoraOscip;

    /**
     * @var string
     */
    public $categoriaEscolaPrivada;

    /**
     * @var string
     */
    public $conveniadaPoderPublico;

    /**
     * @var string
     */
    public $cnpjMantenedoraPrincipal;

    /**
     * @var string
     */
    public $cnpjEscolaPrivada;

    /**
     * @var string
     */
    public $regulamentacao;

    /**
     * @var string
     */
    public $esferaFederal;

    /**
     * @var string
     */
    public $esferaEstadual;

    /**
     * @var string
     */
    public $esferaMunicipal;

    /**
     * @var string
     */
    public $unidadeVinculada;

    /**
     * @var string
     */
    public $inepEscolaSede;

    /**
     * @var string
     */
    public $codigoIes;

    /**
     * @var array
     */
    public $poderPublicoConveniado;

    /**
     * @var array
     */
    public $formasContratacaoPoderPublico;

    /**
     * @var integer
     */
    public $qtdMatAtividadesComplentar;

    /**
     * @var integer
     */
    public $qtdMatAee;

    /**
     * @var integer
     */
    public $qtdMatCrecheParcial;

    /**
     * @var integer
     */
    public $qtdMatCrecheIntegral;

    /**
     * @var integer
     */
    public $qtdMatPreEscolaParcial;

    /**
     * @var integer
     */
    public $qtdMatPreEscolaIntegral;

    /**
     * @var integer
     */
    public $qtdMatFundamentalIniciaisParcial;

    /**
     * @var integer
     */
    public $qtdMatFundamentalIniciaisIntegral;

    /**
     * @var integer
     */
    public $qtdMatFundamentalFinaisParcial;

    /**
     * @var integer
     */
    public $qtdMatFundamentalFinaisIntegral;

    /**
     * @var integer
     */
    public $qtdMatEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEnsinoMedioIntegral;

    /**
     * @var integer
     */
    public $qdtMatClasseEspecialParcial;

    /**
     * @var integer
     */
    public $qdtMatClasseEspecialIntegral;

    /**
     * @var integer
     */
    public $qdtMatEjaFundamental;

    /**
     * @var integer
     */
    public $qtdMatEjaEnsinoMedio;

    /**
     * @var integer
     */
    public $qtdMatEdProfIntegradaEjaFundamentalParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfIntegradaEjaFundamentalIntegral;

    /**
     * @var integer
     */
    public $qtdMatEdProfIntegradaEjaNivelMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfIntegradaEjaNivelMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEdProfConcomitanteEjaNivelMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfConcomitanteEjaNivelMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEdProfIntercomentarEjaNivelMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfIntercomentarEjaNivelMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEdProfIntegradaEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfIntegradaEnsinoMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEdProfConcomitenteEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfConcomitenteEnsinoMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEdProfIntercomplementarEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfIntercomplementarEnsinoMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaIntegradaEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaIntegradaEnsinoMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaConcomitanteEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaConcomitanteEnsinoMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaIntercomplementarEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaIntercomplementarEnsinoMedioItegral;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaSubsequenteEnsinoMedio;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaIntegradaEjaNivelMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaIntegradaEjaNivelMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaConcomitanteEjaNivelMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaConcomitanteEjaNivelMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaIntercomplementarEjaNivelMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEdProfTecnicaIntercomplementarEjaNivelMedioIntegral;

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

    public function NaoPossuiQuantidadeDeMatriculasAtendidas()
    {
        return
            !$this->qtdMatAtividadesComplentar &&
            !$this->qtdMatAee &&
            !$this->qtdMatCrecheParcial &&
            !$this->qtdMatCrecheIntegral &&
            !$this->qtdMatPreEscolaParcial &&
            !$this->qtdMatPreEscolaIntegral &&
            !$this->qtdMatFundamentalIniciaisParcial &&
            !$this->qtdMatFundamentalIniciaisIntegral &&
            !$this->qtdMatFundamentalFinaisParcial &&
            !$this->qtdMatFundamentalFinaisIntegral &&
            !$this->qtdMatEnsinoMedioParcial &&
            !$this->qtdMatEnsinoMedioIntegral &&
            !$this->qtdMatEjaEnsinoMedio &&
            !$this->qtdMatEdProfIntegradaEjaFundamentalParcial &&
            !$this->qtdMatEdProfIntegradaEjaFundamentalIntegral &&
            !$this->qtdMatEdProfIntegradaEjaNivelMedioParcial &&
            !$this->qtdMatEdProfIntegradaEjaNivelMedioIntegral &&
            !$this->qtdMatEdProfConcomitanteEjaNivelMedioParcial &&
            !$this->qtdMatEdProfConcomitanteEjaNivelMedioIntegral &&
            !$this->qtdMatEdProfIntercomentarEjaNivelMedioParcial &&
            !$this->qtdMatEdProfIntercomentarEjaNivelMedioIntegral &&
            !$this->qtdMatEdProfIntegradaEnsinoMedioParcial &&
            !$this->qtdMatEdProfIntegradaEnsinoMedioIntegral &&
            !$this->qtdMatEdProfConcomitenteEnsinoMedioParcial &&
            !$this->qtdMatEdProfConcomitenteEnsinoMedioIntegral &&
            !$this->qtdMatEdProfIntercomplementarEnsinoMedioParcial &&
            !$this->qtdMatEdProfIntercomplementarEnsinoMedioIntegral &&
            !$this->qtdMatEdProfTecnicaIntegradaEnsinoMedioParcial &&
            !$this->qtdMatEdProfTecnicaIntegradaEnsinoMedioIntegral &&
            !$this->qtdMatEdProfTecnicaConcomitanteEnsinoMedioParcial &&
            !$this->qtdMatEdProfTecnicaConcomitanteEnsinoMedioIntegral &&
            !$this->qtdMatEdProfTecnicaIntercomplementarEnsinoMedioParcial &&
            !$this->qtdMatEdProfTecnicaIntercomplementarEnsinoMedioItegral &&
            !$this->qtdMatEdProfTecnicaSubsequenteEnsinoMedio &&
            !$this->qtdMatEdProfTecnicaIntegradaEjaNivelMedioParcial &&
            !$this->qtdMatEdProfTecnicaIntegradaEjaNivelMedioIntegral &&
            !$this->qtdMatEdProfTecnicaConcomitanteEjaNivelMedioParcial &&
            !$this->qtdMatEdProfTecnicaConcomitanteEjaNivelMedioIntegral &&
            !$this->qtdMatEdProfTecnicaIntercomplementarEjaNivelMedioParcial &&
            !$this->qtdMatEdProfTecnicaIntercomplementarEjaNivelMedioIntegral;
    }

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
