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

            $data->laboratoriosEducacaoProfissional() ?: 0, // 55

            $data->areasExternasParqueInfantil() ?: 0, // 56
            $data->areasExternasPatioCoberto() ?: 0, // 57
            $data->areasExternasPatioDescoberto() ?: 0, // 58
            $data->areasExternasPiscina() ?: 0, // 59
            $data->areasExternasQuadraCoberta() ?: 0, // 60
            $data->areasExternasQuadraDescoberta() ?: 0, // 61
            $data->salasFuncionaisRefeitorio() ?: 0, // 62
            $data->salasAtividadesRepousoAluno() ?: 0, // 63
            $data->salasAtividadesAtelie() ?: 0, // 64
            $data->salasAtividadesMusica() ?: 0, // 65
            $data->salasAtividadesEstudioDanca() ?: 0, // 66
            $data->salasAtividadesMultiuso() ?: 0, // 67
            $data->areasExternasTerreirao() ?: 0, // 68
            $data->areasExternasViveiro() ?: 0, // 69
            $data->salasGeraisSalaDiretoria() ?: 0, // 70
            $data->salasAtividadesLeitura() ?: 0, // 71
            $data->salasGeraisSalaProfessores() ?: 0, // 72
            $data->salasAtividadesRecursosAee() ?: 0, // 73
            $data->salasGeraisSalaSecretaria() ?: 0, // 74

            $data->salasAtividadesEducacaoProfissional() ?: 0, // 74

            $data->naoPossuiDependencias() ?: 0, // 76
            $data->recursosAcessibilidadeCorrimao() ?: 0, // 77
            $data->recursosAcessibilidadeElevador() ?: 0, // 78
            $data->recursosAcessibilidadePisosTateis() ?: 0, // 79
            $data->recursosAcessibilidadePortasVaoLivre() ?: 0, // 80
            $data->recursosAcessibilidadeRampas() ?: 0, // 81
            $data->recursosAcessibilidadeSinalizacaoSonora() ?: 0, // 82
            $data->recursosAcessibilidadeSinalizacaoTatil() ?: 0, // 83
            $data->recursosAcessibilidadeSinalizacaoVisual() ?: 0, // 84
            $data->recursosAcessibilidadeNenhum() ?: 0, // 85
            $data->predioEscolar() ? $data->numeroSalasUtilizadasDentroPredio : '', // 86
            $data->numeroSalasUtilizadasForaPredio, // 87
            $data->numeroSalasUtilizadasDentroPredio || $data->numeroSalasUtilizadasForaPredio ? $data->numeroSalasClimatizadas : null, // 88
            $data->numeroSalasUtilizadasDentroPredio || $data->numeroSalasUtilizadasForaPredio ? $data->numeroSalasAcessibilidade : null, // 89
            $data->possuiAntenaParabolica() ?: 0, // 90
            $data->possuiComputadores() ?: 0, // 91
            $data->possuiCopiadora() ?: 0, // 92
            $data->possuiImpressoras() ?: 0, // 93
            $data->possuiImpressorasMultifuncionais() ?: 0, // 94
            $data->possuiScanner() ?: 0, // 95

            $data->nenhumEquipamentoNaEscola() ?: 0, // 96

            $data->dvds ?: null, // 97
            $data->aparelhosDeSom ?: null, // 98
            $data->televisoes ?: null, // 99
            $data->lousasDigitais ?: null, // 100
            $data->projetoresDigitais ?: null, // 101
            $data->possuiComputadores() ? $data->quantidadeComputadoresAlunosMesa : null, // 102
            $data->possuiComputadores() ? $data->quantidadeComputadoresAlunosPortateis : null, // 103
            $data->possuiComputadores() ? $data->quantidadeComputadoresAlunosTablets : null, // 104
            $data->usoInternetAdministrativo() ?: 0, // 105
            $data->usoInternetProcessosEnsino() ?: 0, // 106
            $data->usoInternetAlunos() ?: 0, // 107
            $data->usoInternetComunidade() ?: 0, // 108
            $data->usoInternetNaoPossui() ?: 0, // 109
            $data->equipamentosAcessoInternetComputadorMesa() ?: 0, // 110
            $data->equipamentosAcessoInternetDispositivosPessoais() ?: 0, // 111
            $data->usoInternetNaoPossui() ? null : ($data->acessoInternet ?: 0), // 112
            ($data->possuiComputadores() || $data->possuiComputadoresDeMesaTabletsEPortateis()) ? ($data->redeLocalACabo() ?: 0) : null, // 113
            ($data->possuiComputadores() || $data->possuiComputadoresDeMesaTabletsEPortateis()) ? ($data->redeLocalWireless() ?: 0) : null, // 114
            ($data->possuiComputadores() || $data->possuiComputadoresDeMesaTabletsEPortateis()) ? ($data->redeLocalNenhuma() ?: 0) : null, // 115

            $data->semFuncionariosParaFuncoes ? null : $data->qtdAuxiliarAdministrativo, // 116
            $data->semFuncionariosParaFuncoes ? null : $data->qtdAuxiliarServicosGerais, // 117
            $data->semFuncionariosParaFuncoes ? null : $data->qtdBibliotecarios, // 118
            $data->semFuncionariosParaFuncoes ? null : $data->qtdBombeiro, // 119
            $data->semFuncionariosParaFuncoes ? null : $data->qtdCoordenadorTurno, // 120
            $data->semFuncionariosParaFuncoes ? null : $data->qtdFonoaudiologo, // 121
            $data->semFuncionariosParaFuncoes ? null : $data->qtdNutricionistas, // 122
            $data->semFuncionariosParaFuncoes ? null : $data->qtdPsicologo, // 123
            $data->semFuncionariosParaFuncoes ? null : $data->qtdProfissionaisPreparacao, // 124
            $data->semFuncionariosParaFuncoes ? null : $data->qtdApoioPedagogico, // 125
            $data->semFuncionariosParaFuncoes ? null : $data->qtdSecretarioEscolar, // 126
            $data->semFuncionariosParaFuncoes ? null : $data->qtdSegurancas, // 127
            $data->semFuncionariosParaFuncoes ? null : $data->qtdTecnicos, // 128
            $data->semFuncionariosParaFuncoes ? null : $data->qtdViceDiretor, // 129
            $data->semFuncionariosParaFuncoes ? null : $data->qtdOrientadorComunitario, // 130

            (int) $data->semFuncionariosParaFuncoes, //131

            $data->alimentacaoEscolarAlunos, // 132

            $data->instrumentosPedagogicosAcervoMultimidia() ?: 0, // 133
            $data->instrumentosPedagogicosBrinquedrosEducacaoInfantil() ?: 0, // 134
            $data->instrumentosPedagogicosMateriaisCientificos() ?: 0, // 135
            $data->instrumentosPedagogicosAmplificacaoDifusaoSom() ?: 0, // 136
            $data->instrumentosPedagogicosInstrumentosMusicais() ?: 0, // 137
            $data->instrumentosPedagogicosJogosEducativos() ?: 0, // 138
            $data->instrumentosPedagogicosMateriaisAtividadesCulturais() ?: 0, // 139
            $data->instrumentosPedagogicosMateriaisEducacaoProfissional() ?: 0, // 140
            $data->instrumentosPedagogicosMateriaisPraticaDesportiva() ?: 0, // 141
            $data->instrumentosPedagogicosMateriaisEducacaoIndigena() ?: 0, // 142
            $data->instrumentosPedagogicosMateriaisRelacoesEtnicosRaciais() ?: 0, // 143
            $data->instrumentosPedagogicosMateriaisEducacaoCampo() ?: 0, // 144            
            $data->instrumentosPedagogicosNenhum() ?: 0, // 145

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
