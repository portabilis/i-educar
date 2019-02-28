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
    public $cpfGestor;

    /**
     * @var string Campo 4
     */
    public $nomeGestor;

    /**
     * @var string Campo 5
     */
    public $cargoGestor;

    /**
     * @var string Campo 6
     */
    public $emailGestor;

    /**
     * @var string Campo 7
     */
    public $situacaoFuncionamento;

    /**
     * @var string Campo 8
     */
    public $inicioAnoLetivo;

    /**
     * @var string Campo 9
     */
    public $fimAnoLetivo;

    /**
     * @var string Campo 10
     */
    public $nome;

    /**
     * @var string Campo 11
     */
    public $latitude;

    /**
     * @var string Campo 12
     */
    public $longitude;

    /**
     * @var string Campo 13
     */
    public $cep;

    /**
     * @var string Campo 14
     */
    public $logradouro;

    /**
     * @var string Campo 15
     */
    public $numero;

    /**
     * @var string Campo 16
     */
    public $complemento;

    /**
     * @var string Campo 17
     */
    public $bairro;

    /**
     * @var string Campo 18
     */
    public $codigoIbgeEstado;

    /**
     * @var string Campo 19
     */
    public $codigoIbgeMunicipio;

    /**
     * @var string Campo 20
     */
    public $codigoIbgeDistrito;

    /**
     * @var string Campo 21
     */
    public $ddd;

    /**
     * @var string Campo 22
     */
    public $telefone;

    /**
     * @var string Campo 23
     */
    public $telefoneOutro;

    /**
     * @var string Campo 24
     */
    public $telefoneContato;

    /**
     * @var string Campo 25
     */
    public $fax;

    /**
     * @var string Campo 26
     */
    public $email;

    /**
     * @var string Campo 27
     */
    public $orgaoRegional;

    /**
     * @var string Campo 28
     */
    public $dependenciaAdministrativa;

    /**
     * @var string Campo 29
     */
    public $zonaLocalizacao;

    /**
     * @var string Campo 30
     */
    public $categoriaEscolaPrivada;

    /**
     * @var string Campo 31
     */
    public $conveniadaPoderPublico;

    /**
     * @var string Campo 32
     */
    public $mantenedoraEmpresa;

    /**
     * @var string Campo 33
     */
    public $mantenedoraSindicato;

    /**
     * @var string Campo 34
     */
    public $mantenedoraOng;

    /**
     * @var string Campo 35
     */
    public $mantenedoraInstituicoes;

    /**
     * @var string Campo 36
     */
    public $mantenedoraSistemaS;

    /**
     * @var string Campo 31
     */
    public $mantenedoraOscip;

    /**
     * @var string Campo 37
     */
    public $cnpjMantenedoraPrincipal;

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
     * @var string Campo 38
     */
    public $cnpjEscolaPrivada;

    /**
     * @var string Campo 39
     */
    public $regulamentacao;

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

    public $localizacaoDiferenciada;

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

}
