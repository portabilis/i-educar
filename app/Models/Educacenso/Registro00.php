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
    public $formasContratacaoPoderPublicoEstadual;

    /**
     * @var array
     */
    public $formasContratacaoPoderPublicoMunicipal;

    /**
     * @var int
     */
    public $qtdMatAtividadesComplentar;

    /**
     * @var int
     */
    public $qtdMatAee;

    /**
     * @var int
     */
    public $qtdMatCrecheParcial;

    /**
     * @var int
     */
    public $qtdMatCrecheIntegral;

    /**
     * @var int
     */
    public $qtdMatPreEscolaParcial;

    /**
     * @var int
     */
    public $qtdMatPreEscolaIntegral;

    /**
     * @var int
     */
    public $qtdMatFundamentalIniciaisParcial;

    /**
     * @var int
     */
    public $qtdMatFundamentalIniciaisIntegral;

    /**
     * @var int
     */
    public $qtdMatFundamentalFinaisParcial;

    /**
     * @var int
     */
    public $qtdMatFundamentalFinaisIntegral;

    /**
     * @var int
     */
    public $qtdMatEnsinoMedioParcial;

    /**
     * @var int
     */
    public $qtdMatEnsinoMedioIntegral;

    /**
     * @var int
     */
    public $qdtMatClasseEspecialParcial;

    /**
     * @var int
     */
    public $qdtMatClasseEspecialIntegral;

    /**
     * @var int
     */
    public $qdtMatEjaFundamental;

    /**
     * @var int
     */
    public $qtdMatEjaEnsinoMedio;

    /**
     * @var int
     */
    public $qtdMatEdProfIntegradaEjaFundamentalParcial;

    /**
     * @var int
     */
    public $qtdMatEdProfIntegradaEjaFundamentalIntegral;

    /**
     * @var int
     */
    public $qtdMatEdProfIntegradaEjaNivelMedioParcial;

    /**
     * @var int
     */
    public $qtdMatEdProfIntegradaEjaNivelMedioIntegral;

    /**
     * @var int
     */
    public $qtdMatEdProfConcomitanteEjaNivelMedioParcial;

    /**
     * @var int
     */
    public $qtdMatEdProfConcomitanteEjaNivelMedioIntegral;

    /**
     * @var int
     */
    public $qtdMatEdProfIntercomentarEjaNivelMedioParcial;

    /**
     * @var int
     */
    public $qtdMatEdProfIntercomentarEjaNivelMedioIntegral;

    /**
     * @var int
     */
    public $qtdMatEdProfIntegradaEnsinoMedioParcial;

    /**
     * @var int
     */
    public $qtdMatEdProfIntegradaEnsinoMedioIntegral;

    /**
     * @var int
     */
    public $qtdMatEdProfConcomitenteEnsinoMedioParcial;

    /**
     * @var int
     */
    public $qtdMatEdProfConcomitenteEnsinoMedioIntegral;

    /**
     * @var int
     */
    public $qtdMatEdProfIntercomplementarEnsinoMedioParcial;

    /**
     * @var int
     */
    public $qtdMatEdProfIntercomplementarEnsinoMedioIntegral;

    /**
     * @var int
     */
    public $qtdMatEdProfTecnicaIntegradaEnsinoMedioParcial;

    /**
     * @var int
     */
    public $qtdMatEdProfTecnicaIntegradaEnsinoMedioIntegral;

    /**
     * @var int
     */
    public $qtdMatEdProfTecnicaConcomitanteEnsinoMedioParcial;

    /**
     * @var int
     */
    public $qtdMatEdProfTecnicaConcomitanteEnsinoMedioIntegral;

    /**
     * @var int
     */
    public $qtdMatEdProfTecnicaIntercomplementarEnsinoMedioParcial;

    /**
     * @var int
     */
    public $qtdMatEdProfTecnicaIntercomplementarEnsinoMedioItegral;

    /**
     * @var int
     */
    public $qtdMatEdProfTecnicaSubsequenteEnsinoMedio;

    /**
     * @var int
     */
    public $qtdMatEdProfTecnicaIntegradaEjaNivelMedioParcial;

    /**
     * @var int
     */
    public $qtdMatEdProfTecnicaIntegradaEjaNivelMedioIntegral;

    /**
     * @var int
     */
    public $qtdMatEdProfTecnicaConcomitanteEjaNivelMedioParcial;

    /**
     * @var int
     */
    public $qtdMatEdProfTecnicaConcomitanteEjaNivelMedioIntegral;

    /**
     * @var int
     */
    public $qtdMatEdProfTecnicaIntercomplementarEjaNivelMedioParcial;

    /**
     * @var int
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
     * @var int Campo usado na validação
     */
    public $idEscola;

    /**
     * @var int Campo usado na validação
     */
    public $idInstituicao;

    /**
     * @var int Campo usado na validação
     */
    public $idMunicipio;

    /**
     * @var int Campo usado na validação
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
}
