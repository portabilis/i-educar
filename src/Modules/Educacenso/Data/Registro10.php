<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro10 as Registro10Model;
use iEducar\Modules\Educacenso\Formatters;
use Portabilis_Utils_Database;

class Registro10 extends AbstractRegistro
{
    use Formatters;

    /**
     * @var Registro10Model
     */
    protected $model;

    /**
     * @param $escola
     *
     * @return Registro10Model
     */
    public function getData($escola)
    {
        $data = $this->processData($this->repository->getDataForRecord10($escola)[0]);
        $this->hydrateModel($data);

        return $this->model;
    }

    public function getExportFormatData($escola)
    {
        $data = $this->getData($escola);

        $exportData = [
            '10',  // 1
            $data->codigoInep,// 2
            $data->predioEscolar() ? 1 : 0,// 3
            $data->salasOutraEscola() ? 1 : 0, // 4
            $data->galpao() ? 1 : 0, // 5
            $data->unidadeAtendimentoSocioeducativa() ? 1 : 0, // 6
            $data->unidadePrisional() ? 1 : 0, // 7
            $data->outros() ? 1 : 0, // 8
            $data->predioEscolar() ? ($data->condicao ?: 0) : null, // 9
            $data->predioEscolar() ? $data->predioCompartilhadoOutraEscola : null, // 10
            $data->predioEscolar() && $data->predioCompartilhadoOutraEscola ? $data->codigoInepEscolaCompartilhada : null, // 11
            $data->predioEscolar() && $data->predioCompartilhadoOutraEscola ? $data->codigoInepEscolaCompartilhada2 : null, // 12
            $data->predioEscolar() && $data->predioCompartilhadoOutraEscola ? $data->codigoInepEscolaCompartilhada3 : null, // 13
            $data->predioEscolar() && $data->predioCompartilhadoOutraEscola ? $data->codigoInepEscolaCompartilhada4 : null, // 14
            $data->predioEscolar() && $data->predioCompartilhadoOutraEscola ? $data->codigoInepEscolaCompartilhada5 : null, // 15
            $data->predioEscolar() && $data->predioCompartilhadoOutraEscola ? $data->codigoInepEscolaCompartilhada6 : null, // 16
            $data->aguaPotavelConsumo, // 17
            $data->aguaRedePublica, // 18
            $data->aguaPocoArtesiano, // 19
            $data->aguaCacimbaCisternaPoco, // 20
            $data->aguaFonteRio, // 21
            $data->aguaInexistente, // 22
            $data->energiaRedePublica, // 23
            $data->energiaGerador, // 24
            $data->energiaOutros, // 25
            $data->energiaInexistente, // 26
            $data->esgotoRedePublica, // 27
            $data->esgotoFossaComum, // 28
            $data->esgotoFossaRudimentar, // 29
            $data->esgotoInexistente, // 30
            $data->lixoColetaPeriodica, // 31
            $data->lixoQueima, // 32
            $data->lixoEnterra, // 33
            $data->lixoDestinacaoPoderPublico, // 34
            $data->lixoJogaOutraArea, // 35
            $data->tratamentoLixoSeparacao() ?: 0, // 36
            $data->tratamentoLixoReaproveitamento() ?: 0, // 37
            $data->tratamentoLixoReciclagem() ?: 0, // 38
            $data->tratamentoLixoNaoFaz() ?: 0, // 39
            $data->salasFuncionaisAlmoxarifado() ?: 0, // 40
            $data->areasExternasAreaVerde() ?: 0, // 41
            $data->salasGeraisAuditorio() ?: 0, // 42
            $data->banheirosBanheiro() ?: 0, // 43
            $data->banheirosBanheiroAcessivel() ?: 0, // 44
            $data->banheirosBanheiroEducacaoInfantil() ?: 0, // 45
            $data->banheirosBanheiroFuncionarios() ?: 0, // 46
            $data->banheirosBanheiroChuveiro() ?: 0, // 47
            $data->salasGeraisBiblioteca() ?: 0, // 48
            $data->salasFuncionaisCozinha() ?: 0, // 49
            $data->salasFuncionaisDespensa() ?: 0, // 50
            $data->dormitoriosAluno() ?: 0, // 51
            $data->dormitoriosProfessor() ?: 0, // 52
            $data->laboratoriosCiencias() ?: 0, // 53
            $data->laboratoriosInformatica() ?: 0, // 54
            $data->areasExternasParqueInfantil() ?: 0, // 55
            $data->areasExternasPatioCoberto() ?: 0, // 56
            $data->areasExternasPatioDescoberto() ?: 0, // 57
            $data->areasExternasPiscina() ?: 0, // 58
            $data->areasExternasQuadraCoberta() ?: 0, // 59
            $data->areasExternasQuadraDescoberta() ?: 0, // 60
            $data->salasFuncionaisRefeitorio() ?: 0, // 61
            $data->salasAtividadesRepousoAluno() ?: 0, // 62
            $data->salasAtividadesAtelie() ?: 0, // 63
            $data->salasAtividadesMusica() ?: 0, // 64
            $data->salasAtividadesEstudioDanca() ?: 0, // 65
            $data->salasAtividadesMultiuso() ?: 0, // 66
            $data->areasExternasTerreirao() ?: 0, // 67
            $data->areasExternasViveiro() ?: 0, // 68
            $data->salasGeraisSalaDiretoria() ?: 0, // 69
            $data->salasAtividadesLeitura() ?: 0, // 70
            $data->salasGeraisSalaProfessores() ?: 0, // 71
            $data->salasAtividadesRecursosAee() ?: 0, // 72
            $data->salasGeraisSalaSecretaria() ?: 0, // 73
            $data->naoPossuiDependencias() ?: 0, // 74
            $data->recursosAcessibilidadeCorrimao() ?: 0, // 75
            $data->recursosAcessibilidadeElevador() ?: 0, // 76
            $data->recursosAcessibilidadePisosTateis() ?: 0, // 77
            $data->recursosAcessibilidadePortasVaoLivre() ?: 0, // 78
            $data->recursosAcessibilidadeRampas() ?: 0, // 79
            $data->recursosAcessibilidadeSinalizacaoSonora() ?: 0, // 80
            $data->recursosAcessibilidadeSinalizacaoTatil() ?: 0, // 81
            $data->recursosAcessibilidadeSinalizacaoVisual() ?: 0, // 82
            $data->recursosAcessibilidadeNenhum() ?: 0, // 83
            $data->numeroSalasUtilizadasDentroPredio, // 84
            $data->numeroSalasUtilizadasForaPredio, // 85
            $data->numeroSalasUtilizadasDentroPredio || $data->numeroSalasUtilizadasForaPredio ? $data->numeroSalasClimatizadas : null, // 86
            $data->numeroSalasUtilizadasDentroPredio || $data->numeroSalasUtilizadasForaPredio ? $data->numeroSalasAcessibilidade : null, // 87
            $data->possuiAntenaParabolica() ?: 0, // 88
            $data->possuiComputadores() ?: 0, // 89
            $data->possuiCopiadora() ?: 0, // 90
            $data->possuiImpressoras() ?: 0, // 91
            $data->possuiImpressorasMultifuncionais() ?: 0, // 92
            $data->possuiScanner() ?: 0, // 93
            $data->dvds ?: null, // 94
            $data->aparelhosDeSom ?: null, // 95
            $data->televisoes ?: null, // 96
            $data->lousasDigitais ?: null, // 97
            $data->projetoresDigitais ?: null, // 98
            $data->possuiComputadores() ? $data->quantidadeComputadoresAlunosMesa : null, // 99
            $data->possuiComputadores() ? $data->quantidadeComputadoresAlunosPortateis : null, // 100
            $data->possuiComputadores() ? $data->quantidadeComputadoresAlunosTablets : null, // 101
            $data->usoInternetAdministrativo() ?: 0, // 102
            $data->usoInternetProcessosEnsino() ?: 0, // 103
            $data->usoInternetAlunos() ?: 0, // 104
            $data->usoInternetComunidade() ?: 0, // 105
            $data->usoInternetNaoPossui() ?: 0, // 106
            $data->equipamentosAcessoInternetComputadorMesa() ?: 0, // 107
            $data->equipamentosAcessoInternetDispositivosPessoais() ?: 0, // 108
            $data->usoInternetNaoPossui() ? null : ($data->acessoInternet ?: 0), // 109
            ($data->possuiComputadores() || $data->possuiComputadoresDeMesaTabletsEPortateis()) ? ($data->redeLocalACabo() ?: 0) : null, // 110
            ($data->possuiComputadores() || $data->possuiComputadoresDeMesaTabletsEPortateis()) ? ($data->redeLocalWireless() ?: 0) : null, // 111
            ($data->possuiComputadores() || $data->possuiComputadoresDeMesaTabletsEPortateis()) ? ($data->redeLocalNenhuma() ?: 0) : null, // 112
            $data->qtdAuxiliarAdministrativo ?: null, // 113
            $data->qtdAuxiliarServicosGerais ?: null, // 114
            $data->qtdBibliotecarios ?: null, // 115
            $data->qtdBombeiro ?: null, // 116
            $data->qtdCoordenadorTurno ?: null, // 117
            $data->qtdFonoaudiologo ?: null, // 118
            $data->qtdNutricionistas ?: null, // 119
            $data->qtdPsicologo ?: null, // 120
            $data->qtdProfissionaisPreparacao ?: null, // 121
            $data->qtdApoioPedagogico ?: null, // 122
            $data->qtdSecretarioEscolar ?: null, // 123
            $data->qtdSegurancas ?: null, // 124
            $data->qtdTecnicos ?: null, // 125
            $data->qtdViceDiretor ?: null, // 126
            $data->qtdOrientadorComunitario ?: null, // 127
            $data->alimentacaoEscolarAlunos, // 128
            $data->organizacaoEnsinoSerieAno() ?: 0, // 129
            $data->organizacaoEnsinoPeriodosSemestrais() ?: 0, // 130
            $data->organizacaoEnsinoCliclosEnsinoFundamental() ?: 0, // 131
            $data->organizacaoEnsinoGruposNaoSeriados() ?: 0, // 132
            $data->organizacaoEnsinoModulos() ?: 0, // 133
            $data->organizacaoEnsinoAlternanciaRegular() ?: 0, // 134
            $data->instrumentosPedagogicosAcervoMultimidia() ?: 0, // 135
            $data->instrumentosPedagogicosBrinquedrosEducacaoInfantil() ?: 0, // 136
            $data->instrumentosPedagogicosMateriaisCientificos() ?: 0, // 137
            $data->instrumentosPedagogicosAmplificacaoDifusaoSom() ?: 0, // 138
            $data->instrumentosPedagogicosInstrumentosMusicais() ?: 0, // 139
            $data->instrumentosPedagogicosJogosEducativos() ?: 0, // 140
            $data->instrumentosPedagogicosMateriaisAtividadesCulturais() ?: 0, // 141
            $data->instrumentosPedagogicosMateriaisPraticaDesportiva() ?: 0, // 142
            $data->instrumentosPedagogicosMateriaisEducacaoIndigena() ?: 0, // 143
            $data->instrumentosPedagogicosMateriaisRelacoesEtnicosRaciais() ?: 0, // 144
            $data->instrumentosPedagogicosMateriaisEducacaoCampo() ?: 0, // 145
            $data->educacaoIndigena, // 146
            $data->educacaoIndigena ? ($data->linguaMinistradaIndigena() ?: 0) : null, // 147
            $data->educacaoIndigena ? ($data->linguaMinistradaPortugues() ?: 0) : null, // 148
            $data->educacaoIndigena && $data->linguaMinistradaIndigena() ? ($data->codigoLinguaIndigena[0] ?? null) : null, // 149
            $data->educacaoIndigena && $data->linguaMinistradaIndigena() ? ($data->codigoLinguaIndigena[1] ?? null) : null, // 150
            $data->educacaoIndigena && $data->linguaMinistradaIndigena() ? ($data->codigoLinguaIndigena[2] ?? null) : null, // 151
            $data->exameSelecaoIngresso ?: 0, // 152
            $data->exameSelecaoIngresso ? ($data->reservaVagasCotasAutodeclaracaoPpi() ?: 0) : null, // 153
            $data->exameSelecaoIngresso ? ($data->reservaVagasCotasCondicaoRenda() ?: 0) : null, // 154
            $data->exameSelecaoIngresso ? ($data->reservaVagasCotasEscolaPublica() ?: 0) : null, // 155
            $data->exameSelecaoIngresso ? ($data->reservaVagasCotasPcd() ?: 0) : null, // 156
            $data->exameSelecaoIngresso ? ($data->reservaVagasCotasOutros() ?: 0) : null, // 157
            $data->exameSelecaoIngresso ? ($data->reservaVagasCotasNaoPossui() ?: 0) : null, // 158
            empty($data->url) ? 0 : 1, // 159
            $data->compartilhaEspacosAtividadesIntegracao ?: 0, // 160
            $data->usaEspacosEquipamentosAtividadesRegulares ?: 0, // 161
            $data->orgaosColegiadosAssociacaoPais() ?: 0, // 162
            $data->orgaosColegiadosAssociacaoPaisEMestres() ?: 0, // 163
            $data->orgaosColegiadosConselhoEscolar() ?: 0, // 164
            $data->orgaosColegiadosGremioEstudantil() ?: 0, // 165
            $data->orgaosColegiadosOutros() ?: 0, // 166
            $data->orgaosColegiadosNenhum() ?: 0, // 167
            $data->projetoPoliticoPedagogico ?: 0, // 168
        ];

        return $exportData;
    }

    private function processData($data)
    {
        $data->localFuncionamento = Portabilis_Utils_Database::pgArrayToArray($data->localFuncionamento);
        $data->tratamentoLixo = Portabilis_Utils_Database::pgArrayToArray($data->tratamentoLixo);
        $data->recursosAcessibilidade = Portabilis_Utils_Database::pgArrayToArray($data->recursosAcessibilidade);
        $data->usoInternet = Portabilis_Utils_Database::pgArrayToArray($data->usoInternet);
        $data->equipamentosAcessoInternet = Portabilis_Utils_Database::pgArrayToArray($data->equipamentosAcessoInternet);
        $data->equipamentos = Portabilis_Utils_Database::pgArrayToArray($data->equipamentos);
        $data->redeLocal = Portabilis_Utils_Database::pgArrayToArray($data->redeLocal);
        $data->orgaosColegiados = Portabilis_Utils_Database::pgArrayToArray($data->orgaosColegiados);
        $data->reservaVagasCotas = Portabilis_Utils_Database::pgArrayToArray($data->reservaVagasCotas);
        $data->salasGerais = Portabilis_Utils_Database::pgArrayToArray($data->salasGerais);
        $data->salasFuncionais = Portabilis_Utils_Database::pgArrayToArray($data->salasFuncionais);
        $data->banheiros = Portabilis_Utils_Database::pgArrayToArray($data->banheiros);
        $data->laboratorios = Portabilis_Utils_Database::pgArrayToArray($data->laboratorios);
        $data->salasAtividades = Portabilis_Utils_Database::pgArrayToArray($data->salasAtividades);
        $data->dormitorios = Portabilis_Utils_Database::pgArrayToArray($data->dormitorios);
        $data->areasExternas = Portabilis_Utils_Database::pgArrayToArray($data->areasExternas);
        $data->organizacaoEnsino = Portabilis_Utils_Database::pgArrayToArray($data->organizacaoEnsino);
        $data->instrumentosPedagogicos = Portabilis_Utils_Database::pgArrayToArray($data->instrumentosPedagogicos);
        $data->codigoLinguaIndigena = Portabilis_Utils_Database::pgArrayToArray($data->codigoLinguaIndigena);
        $data->nomeEscola = mb_strtoupper($data->nomeEscola);

        return $data;
    }
}
