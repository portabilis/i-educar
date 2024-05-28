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
            '10',  // 1	Tipo de registro
            $data->codigoInep, // 2	Código de escola - Inep
            $data->predioEscolar() ? 1 : 0, // 3	Prédio escolar
            $data->salasOutraEscola() ? 1 : 0, // 4	Sala(s) em outra escola
            $data->galpao() ? 1 : 0, // 5	Galpão/ rancho/ paiol/ barracão
            $data->unidadeAtendimentoSocioeducativa() ? 1 : 0, // 6	Unidade de atendimento Socioeducativa
            $data->unidadePrisional() ? 1 : 0, // 7	Unidade Prisional
            $data->outros() ? 1 : 0, // 8	Outros
            $data->predioEscolar() ? ($data->condicao ?: 0) : null, // 9	Forma de ocupação do prédio
            $data->predioEscolar() ? $data->predioCompartilhadoOutraEscola : null, // 10	Prédio escolar compartilhado com outra escola
            $data->predioEscolar() && $data->predioCompartilhadoOutraEscola ? $data->codigoInepEscolaCompartilhada : null, // 11	Código da escola com a qual compartilha (1)
            $data->predioEscolar() && $data->predioCompartilhadoOutraEscola ? $data->codigoInepEscolaCompartilhada2 : null, // 12	Código da escola com a qual compartilha (2)
            $data->predioEscolar() && $data->predioCompartilhadoOutraEscola ? $data->codigoInepEscolaCompartilhada3 : null, // 13	Código da escola com a qual compartilha (3)
            $data->predioEscolar() && $data->predioCompartilhadoOutraEscola ? $data->codigoInepEscolaCompartilhada4 : null, // 14	Código da escola com a qual compartilha (4)
            $data->predioEscolar() && $data->predioCompartilhadoOutraEscola ? $data->codigoInepEscolaCompartilhada5 : null, // 15	Código da escola com a qual compartilha (5)
            $data->predioEscolar() && $data->predioCompartilhadoOutraEscola ? $data->codigoInepEscolaCompartilhada6 : null, // 16	Código da escola com a qual compartilha (6)
            $data->aguaPotavelConsumo, // 17	Fornece água potável para o consumo humano
            $data->aguaRedePublica, // 18	Rede pública
            $data->aguaPocoArtesiano, // 19	Poço artesiano
            $data->aguaCacimbaCisternaPoco, // 20	Cacimba/ cisterna / poço
            $data->aguaFonteRio, // 21	Fonte/ rio / igarapé/ riacho/ córrego.
            $data->aguaCarroPipa, // 22	Carro-pipa
            $data->aguaInexistente, // 23	Não há abastecimento de água
            $data->energiaRedePublica, // 24	Rede pública
            $data->energiaGerador, // 25	Gerador movido a combustível fóssil
            $data->energiaOutros, // 26	Fontes de energia renováveis ou alternativas (gerador a biocombustível e/ou biodigestores, eólica, solar, outras)
            $data->energiaInexistente, // 27	Não há energia elétrica
            $data->esgotoRedePublica, // 28	Rede pública
            $data->esgotoFossaComum, // 29	Fossa séptica
            $data->esgotoFossaRudimentar, // 30	Fossa rudimentar/comum
            $data->esgotoInexistente, // 31	Não há esgotamento sanitário
            $data->lixoColetaPeriodica, // 32	Serviço de coleta
            $data->lixoQueima, // 33	Queima
            $data->lixoEnterra, // 34	Enterra
            $data->lixoDestinacaoPoderPublico, // 35	Leva a uma destinação final licenciada pelo poder público
            $data->lixoJogaOutraArea, //36	Descarta em outra área
            $data->tratamentoLixoSeparacao() ?: 0, // 37	Separação do lixo/resíduos
            $data->tratamentoLixoReaproveitamento() ?: 0, // 38	Reaproveitamento/reutilização
            $data->tratamentoLixoReciclagem() ?: 0, // 39	Reciclagem
            $data->tratamentoLixoNaoFaz() ?: 0, // 40	Não faz tratamento
            $data->salasFuncionaisAlmoxarifado() ?: 0, // 41	Almoxarifado
            $data->areasExternasAreaVerde() ?: 0, // 42	Área verde
            $data->salasGeraisAuditorio() ?: 0, // 43 Auditório
            $data->banheirosBanheiro() ?: 0, // 44	Banheiro
            $data->banheirosBanheiroAcessivel() ?: 0, // 45	Banheiro acessível adequado ao uso de pessoas com deficiência ou mobilidade reduzida
            $data->banheirosBanheiroEducacaoInfantil() ?: 0, // 46	Banheiro adequado à educação infantil
            $data->banheirosBanheiroFuncionarios() ?: 0, // 47	Banheiro exclusivo para os funcionários
            $data->banheirosBanheiroChuveiro() ?: 0, // 48	Banheiro ou vestiário com chuveiro
            $data->salasGeraisBiblioteca() ?: 0, // 49	Biblioteca
            $data->salasFuncionaisCozinha() ?: 0, // 50	Cozinha
            $data->salasFuncionaisDespensa() ?: 0, // 51	Despensa
            $data->dormitoriosAluno() ?: 0, // 52	Dormitório de aluno(a)
            $data->dormitoriosProfessor() ?: 0, // 53	Dormitório de professor(a)
            $data->laboratoriosCiencias() ?: 0, // 54	Laboratório de ciências
            $data->laboratoriosInformatica() ?: 0, // 55	Laboratório de informática
            $data->laboratoriosEducacaoProfissional() ?: 0, // 56	Laboratório específico para a educação profissional
            $data->areasExternasParqueInfantil() ?: 0, // 57	Parque infantil
            $data->areasExternasPatioCoberto() ?: 0, // 58	Pátio coberto
            $data->areasExternasPatioDescoberto() ?: 0, // 59	Pátio descoberto
            $data->areasExternasPiscina() ?: 0, // 60	Piscina
            $data->areasExternasQuadraCoberta() ?: 0, // 61	Quadra de esportes coberta
            $data->areasExternasQuadraDescoberta() ?: 0, // 62	Quadra de esportes descoberta
            $data->salasFuncionaisRefeitorio() ?: 0, // 63	Refeitório
            $data->salasAtividadesRepousoAluno() ?: 0, // 64	Sala de repouso para aluno(a)
            $data->salasAtividadesAtelie() ?: 0, // 65	Sala/ateliê de artes
            $data->salasAtividadesMusica() ?: 0, // 66	Sala de música/coral
            $data->salasAtividadesEstudioDanca() ?: 0, // 67 Sala/estúdio de dança
            $data->salasAtividadesMultiuso() ?: 0, // 68 Sala multiuso (música, dança e artes)
            $data->areasExternasTerreirao() ?: 0, // 69	Terreirão (área para prática desportiva e recreação sem cobertura, sem piso e sem edificações)
            $data->areasExternasViveiro() ?: 0, // 70 Viveiro/criação de animais
            $data->salasGeraisSalaDiretoria() ?: 0, // 71 Sala de diretoria
            $data->salasAtividadesLeitura() ?: 0, // 72	Sala de Leitura
            $data->salasGeraisSalaProfessores() ?: 0, // 73	Sala de professores
            $data->salasAtividadesRecursosAee() ?: 0, // 74	Sala de recursos multifuncionais para atendimento educacional especializado (AEE)
            $data->salasGeraisSalaSecretaria() ?: 0, // 75 Sala de Secretaria
            $data->salasAtividadesEducacaoProfissional() ?: 0, // 76 Salas de oficinas da educação profissional
            $data->salasAtividadesEstudioGravacaoEdicao() ?: 0, // 77 Salas de Estúdio de gravação e edição
            $data->areasExternasHorta() ?: 0, // 78 Área de horta, plantio e/ou produção agrícola
            $data->naoPossuiDependencias() ?: 0, // 79	Nenhuma das dependências relacionadas
            $data->recursosAcessibilidadeCorrimao() ?: 0, // 80	Corrimão e guarda-corpos
            $data->recursosAcessibilidadeElevador() ?: 0, // 81	Elevador
            $data->recursosAcessibilidadePisosTateis() ?: 0, // 82 Pisos táteis
            $data->recursosAcessibilidadePortasVaoLivre() ?: 0, // 83 Portas com vão livre de no mínimo 80 cm
            $data->recursosAcessibilidadeRampas() ?: 0, // 84 Rampas
            $data->recursosAcessibilidadeAlarmeLuminoso() ?: 0, // 85 Sinalização/alarme luminoso
            $data->recursosAcessibilidadeSinalizacaoSonora() ?: 0, // 86 Sinalização sonora
            $data->recursosAcessibilidadeSinalizacaoTatil() ?: 0, // 87	Sinalização tátil
            $data->recursosAcessibilidadeSinalizacaoVisual() ?: 0, // 88 Sinalização visual (piso/paredes)
            $data->recursosAcessibilidadeNenhum() ?: 0, // 89 Nenhum dos recursos de acessibilidade listados
            $data->predioEscolar() ? $data->numeroSalasUtilizadasDentroPredio : '', // 90 Número de salas de aula utilizadas na escola dentro do prédio escolar
            $data->numeroSalasUtilizadasForaPredio, // 91 Número de salas de aula utilizadas na escola fora do prédio escolar
            $data->numeroSalasUtilizadasDentroPredio || $data->numeroSalasUtilizadasForaPredio ? $data->numeroSalasClimatizadas : null, // 92 Número de salas de aula climatizadas (ar condicionado, aquecedor ou climatizador)
            $data->numeroSalasUtilizadasDentroPredio || $data->numeroSalasUtilizadasForaPredio ? $data->numeroSalasAcessibilidade : null, // 93	Número de salas de aula com acessibilidade para pessoas com deficiência ou mobilidade reduzida
            $data->possuiAntenaParabolica() ?: 0, // 94	Antena parabólica
            $data->possuiComputadores() ?: 0, // 95	Computadores
            $data->possuiCopiadora() ?: 0, // 96 Copiadora
            $data->possuiImpressoras() ?: 0, // 97 Impressora
            $data->possuiImpressorasMultifuncionais() ?: 0, // 98 Impressora Multifuncional
            $data->possuiScanner() ?: 0, // 99 Scanner
            $data->nenhumEquipamentoNaEscola() ?: 0, // 100 Nenhum dos equipamentos listados
            $data->dvds ?: null, // 101 Aparelho de DVD/Blu-ray
            $data->aparelhosDeSom ?: null, // 102 Aparelho de som
            $data->televisoes ?: null, // 103 Aparelho de Televisão
            $data->lousasDigitais ?: null, // 104 Lousa digital
            $data->projetoresDigitais ?: null, // 105 Projetor Multimídia (Data show)
            $data->possuiComputadores() ? $data->quantidadeComputadoresAlunosMesa : null, // 106 Computadores de mesa (desktop)
            $data->possuiComputadores() ? $data->quantidadeComputadoresAlunosPortateis : null, // 107 Computadores portáteis
            $data->possuiComputadores() ? $data->quantidadeComputadoresAlunosTablets : null, // 108 Tablets
            $data->usoInternetAdministrativo() ?: 0, // 109	Para uso administrativo
            $data->usoInternetProcessosEnsino() ?: 0, // 110 Para uso no processo de ensino e aprendizagem
            $data->usoInternetAlunos() ?: 0, // 111	Para uso dos aluno(a)s
            $data->usoInternetComunidade() ?: 0, // 112	Para uso da comunidade
            $data->usoInternetNaoPossui() ?: 0, // 113	Não possui acesso à internet
            $data->equipamentosAcessoInternetComputadorMesa() ?: 0, // 114 Computadores de mesa, portáteis e tablets da escola (no laboratório de informática, biblioteca, sala de aula etc.)
            $data->equipamentosAcessoInternetDispositivosPessoais() ?: 0, // 115 Dispositivos pessoais (computadores portáteis, celulares, tablets etc.)
            $data->usoInternetNaoPossui() ? null : ($data->acessoInternet ?: 0), // 116 Internet banda larga
            ($data->possuiComputadores() || $data->possuiComputadoresDeMesaTabletsEPortateis()) ? ($data->redeLocalACabo() ?: 0) : null, // 117	A cabo
            ($data->possuiComputadores() || $data->possuiComputadoresDeMesaTabletsEPortateis()) ? ($data->redeLocalWireless() ?: 0) : null, // 118	Wireless
            ($data->possuiComputadores() || $data->possuiComputadoresDeMesaTabletsEPortateis()) ? ($data->redeLocalNenhuma() ?: 0) : null, // 119 Não há rede local interligando computadores
            $data->semFuncionariosParaFuncoes ? null : $data->qtdAgronomosHorticultores, // 120	Agrônomos(as), horticultores(as), técnicos ou monitores(as) responsáveis pela gestão da área de horta, plantio e/ou produção agrícola
            $data->semFuncionariosParaFuncoes ? null : $data->qtdAuxiliarAdministrativo, // 121	Auxiliares de secretaria ou auxiliares administrativos, atendentes
            $data->semFuncionariosParaFuncoes ? null : $data->qtdAuxiliarServicosGerais, // 122	Auxiliar de serviços gerais, porteiro(a), zelador(a), faxineiro(a), jardineiro(a)
            $data->semFuncionariosParaFuncoes ? null : $data->qtdBibliotecarios, // 123	Bibliotecário(a), auxiliar de biblioteca ou monitor(a) da sala de leitura
            $data->semFuncionariosParaFuncoes ? null : $data->qtdBombeiro, // 124 Bombeiro(a) brigadista, profissionais de assistência a saúde (urgência e emergência), enfermeiro(a), técnico(a) de enfermagem e socorrista
            $data->semFuncionariosParaFuncoes ? null : $data->qtdCoordenadorTurno, // 125 Coordenador(a) de turno/disciplinar
            $data->semFuncionariosParaFuncoes ? null : $data->qtdFonoaudiologo, // 126 Fonoaudiólogo(a)
            $data->semFuncionariosParaFuncoes ? null : $data->qtdNutricionistas, // 127 Nutricionista
            $data->semFuncionariosParaFuncoes ? null : $data->qtdPsicologo, // 128 Psicólogo(a) escolar
            $data->semFuncionariosParaFuncoes ? null : $data->qtdProfissionaisPreparacao, // 129 Profissionais de preparação e segurança alimentar, cozinheiro(a), merendeira e auxiliar de cozinha
            $data->semFuncionariosParaFuncoes ? null : $data->qtdApoioPedagogico, // 130 Profissionais de apoio e supervisão pedagógica: (pedagogo(a), coordenador(a) pedagógico(a), orientador(a) educacional, supervisor(a) escolar e coordenador(a) de área de ensino
            $data->semFuncionariosParaFuncoes ? null : $data->qtdSecretarioEscolar, // 131 Secretário(a) escolar
            $data->semFuncionariosParaFuncoes ? null : $data->qtdSegurancas, // 132 Segurança, guarda ou segurança patrimonial
            $data->semFuncionariosParaFuncoes ? null : $data->qtdTecnicos, // 133 Técnicos(as), monitores(as), supervisores(as) ou auxiliares de laboratório(s), de apoio a tecnologias educacionais ou em multimeios/multimídias eletrônico-digitais.
            $data->semFuncionariosParaFuncoes ? null : $data->qtdViceDiretor, // 134 Vice-diretor(a) ou diretor(a) adjunto(a), profissionais responsáveis pela gestão administrativa e/ou financeira
            $data->semFuncionariosParaFuncoes ? null : $data->qtdOrientadorComunitario, // 135 Orientador(a) comunitário(a) ou assistente social
            $data->semFuncionariosParaFuncoes ? null : $data->qtdTradutorInterpreteLibrasOutroAmbiente, // 136 Tradutor e Intérprete de Libras para atendimento em outros ambientes da escola que não seja sala de aula
            $data->semFuncionariosParaFuncoes ? null : $data->qtdRevisorBraile, // 137 Revisor de texto Braile, assistente vidente (assistente de revisão do texto em Braille)
            $data->semFuncionariosParaFuncoes ? 1 : null, //138	Não há funcionários para as funções listadas
            $data->alimentacaoEscolarAlunos, // 139	Alimentação escolar para os aluno(a)s
            $data->instrumentosPedagogicosAcervoMultimidia() ?: 0, // 140 Acervo multimídia
            $data->instrumentosPedagogicosBrinquedrosEducacaoInfantil() ?: 0, // 141 Brinquedos para educação infantil
            $data->instrumentosPedagogicosMateriaisCientificos() ?: 0, // 142 Conjunto de materiais científicos
            $data->instrumentosPedagogicosAmplificacaoDifusaoSom() ?: 0, // 143 Equipamento para amplificação e difusão de som/áudio
            $data->instrumentosPedagogicosAreaHorta() ?: 0, // 144 Equipamentos e instrumentos para atividades em área de horta, plantio e/ou produção agrícola
            $data->instrumentosPedagogicosInstrumentosMusicais() ?: 0, // 145 Instrumentos musicais para conjunto, banda/fanfarra e/ou aulas de música
            $data->instrumentosPedagogicosJogosEducativos() ?: 0, // 146 Jogos educativos
            $data->instrumentosPedagogicosMateriaisAtividadesCulturais() ?: 0, // 147 Materiais para atividades culturais e artísticas
            $data->instrumentosPedagogicosMateriaisEducacaoProfissional() ?: 0, // 148 Materiais para educação profissional
            $data->instrumentosPedagogicosMateriaisPraticaDesportiva() ?: 0, // 149 Materiais para prática desportiva e recreação
            $data->instrumentosPedagogicosMateriaisEducacaoSurdos() ?: 0, // 150 Materiais pedagógicos para a educação bilíngue de surdos
            $data->instrumentosPedagogicosMateriaisEducacaoIndigena() ?: 0, // 151 Materiais pedagógicos para a educação escolar indígena
            $data->instrumentosPedagogicosMateriaisRelacoesEtnicosRaciais() ?: 0, // 152 Materiais pedagógicos para a educação das relações étnicos raciais
            $data->instrumentosPedagogicosMateriaisEducacaoCampo() ?: 0, // 153	Materiais pedagógicos para a educação do campo
            $data->instrumentosPedagogicosEducacaoQuilombola() ?: 0, // 154 Materiais pedagógicos para a educação escolar quilombola
            $data->instrumentosPedagogicosEducacaoEspecial() ?: 0, // 155 Materiais pedagógicos para a educação especial
            $data->instrumentosPedagogicosNenhum() ?: 0, // 156	Nenhum dos instrumentos listados
            $data->educacaoIndigena, // 157	Educação escolar indígena
            $data->educacaoIndigena ? ($data->linguaMinistradaIndigena() ?: 0) : null, // 158 Língua indígena
            $data->educacaoIndigena ? ($data->linguaMinistradaPortugues() ?: 0) : null, // 159 Língua portuguesa
            $data->educacaoIndigena && $data->linguaMinistradaIndigena() ? ($data->codigoLinguaIndigena[0] ?? null) : null, // 160 Código da língua indígena 1
            $data->educacaoIndigena && $data->linguaMinistradaIndigena() ? ($data->codigoLinguaIndigena[1] ?? null) : null, // 161 Código da língua indígena 2
            $data->educacaoIndigena && $data->linguaMinistradaIndigena() ? ($data->codigoLinguaIndigena[2] ?? null) : null, // 162 Código da língua indígena 3
            $data->exameSelecaoIngresso ?: 0, // 163 A escola faz exame de seleção para ingresso de seus aluno(a)s (avaliação por prova e /ou analise curricular)
            $data->exameSelecaoIngresso ? ($data->reservaVagasCotasAutodeclaracaoPpi() ?: 0) : null, // 164	Autodeclarado preto, pardo ou indígena (PPI)
            $data->exameSelecaoIngresso ? ($data->reservaVagasCotasCondicaoRenda() ?: 0) : null, // 165 Condição de renda
            $data->exameSelecaoIngresso ? ($data->reservaVagasCotasEscolaPublica() ?: 0) : null, // 166 Oriundo de escola pública
            $data->exameSelecaoIngresso ? ($data->reservaVagasCotasPcd() ?: 0) : null, // 167 Pessoa com deficiência (PCD)
            $data->exameSelecaoIngresso ? ($data->reservaVagasCotasOutros() ?: 0) : null, // 168 Outros grupos que não os listados
            $data->exameSelecaoIngresso ? ($data->reservaVagasCotasNaoPossui() ?: 0) : null, // 169 Sem reservas de vagas para sistema de cotas (ampla concorrência)
            empty($data->url) ? 0 : 1, // 170 A escola possui site ou blog ou página em redes sociais para comunicação institucional
            $data->compartilhaEspacosAtividadesIntegracao ?: 0, // 171 A escola compartilha espaços para atividades de integração escola-comunidade
            $data->usaEspacosEquipamentosAtividadesRegulares ?: 0, // 172 A escola usa espaços e equipamentos do entorno escolar para atividades regulares com os aluno(a)s
            $data->orgaosColegiadosAssociacaoPais() ?: 0, // 173 Associação de Pais
            $data->orgaosColegiadosAssociacaoPaisEMestres() ?: 0, // 174 Associação de pais e mestres
            $data->orgaosColegiadosConselhoEscolar() ?: 0, // 175 Conselho escolar
            $data->orgaosColegiadosGremioEstudantil() ?: 0, // 176 Grêmio estudantil
            $data->orgaosColegiadosOutros() ?: 0, // 177 Outros
            $data->orgaosColegiadosNenhum() ?: 0, // 178 Não há órgãos colegiados em funcionamento
            $data->projetoPoliticoPedagogico ?: 0, // 179 O projeto político pedagógico ou a proposta pedagógica da escola (conforme art. 12 da LDB) foi atualizada nos últimos 12 meses até a data de referência
            $data->acaoAreaAmbiental ?: 0, // 180 A escola desenvolve ações na área de educação ambiental?
            $data->acaoConteudoComponente, // 181 Como conteúdo dos componentes/campos de experiências presentes no currículo
            $data->acaoConteudoCurricular, // 182 Como um componente curricular especial, específico, flexível ou eletivo
            $data->acaoEixoCurriculo, // 183 Como um eixo estruturante do currículo
            $data->acaoEventos, // 184 Em eventos
            $data->acaoProjetoInterdisciplinares, //185 Em projetos transversais ou interdisciplinares
            $data->acaoAmbientalNenhuma, // 186 Nenhuma das opções listadas
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
