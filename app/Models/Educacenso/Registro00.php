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
    public $qtdMatEducacaoProfissionalIntegradaEjaFundamentalParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalIntegradaEjaFundamentalIntegral;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalIntegradaEjaNivelMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalIntegradaEjaNivelMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalConcomitanteEjaNivelMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalConcomitanteEjaNivelMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalIntercomentarEjaNivelMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalIntercomentarEjaNivelMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalIntegradaEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalIntegradaEnsinoMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalConcomitenteEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalConcomitenteEnsinoMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalIntercomplementarEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalIntercomplementarEnsinoMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaIntegradaEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaIntegradaEnsinoMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaConcomitanteEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaConcomitanteEnsinoMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaIntercomplementarEnsinoMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaIntercomplementarEnsinoMedioItegral;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaSubsequenteEnsinoMedio;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaIntegradaEjaNivelMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaIntegradaEjaNivelMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaConcomitanteEjaNivelMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaConcomitanteEjaNivelMedioIntegral;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaIntercomplementarEjaNivelMedioParcial;

    /**
     * @var integer
     */
    public $qtdMatEducacaoProfissionalTecnicaIntercomplementarEjaNivelMedioIntegral;

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

}
