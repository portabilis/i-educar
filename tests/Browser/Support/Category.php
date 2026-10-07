<?php

namespace Tests\Browser\Support;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\DocumentAbsenceReason;
use App\Models\LegacyAbandonmentType;
use App\Models\LegacyBenefit;
use App\Models\LegacyBondType;
use App\Models\LegacyDeficiency;
use App\Models\LegacyDisciplinaryOccurrenceType;
use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;
use App\Models\LegacyExemptionType;
use App\Models\LegacyProject;
use App\Models\LegacyRace;
use App\Models\LegacyRegimeType;
use App\Models\LegacyRole;
use App\Models\LegacySchoolClassType;
use App\Models\LegacySchoolingDegree;
use App\Models\LegacyStageType;
use App\Models\LegacyTransferType;
use App\Models\Religion;
use App\Models\State;
use App\Models\WithdrawalReason;
use Illuminate\Database\Eloquent\Model;
use Pest\Browser\Api\AwaitableWebpage;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Api\Webpage;

/**
 * Uma tela de catálogo: as dos menus "Tipos" e as de Endereçamento.
 *
 * São as telas mais simples do sistema, e todas iguais: um clsListagem com um
 * campo de busca e um clsCadastro com um campo de texto obrigatório, mais um ou
 * outro campo acessório. É justamente por serem iguais que aqui elas são dados,
 * e não vinte e três arquivos de teste: o que cada teste faz é sempre o mesmo, o
 * que muda é o nome da tela, o do campo e o do modelo.
 *
 * Nada disso dá para deduzir do modelo, e é por isso que cada peça vem
 * declarada: em educar_religiao o parâmetro da edição é `cod_religiao`, o campo
 * de texto `nm_religiao` e a coluna gravada `name`; em funcionario_vinculo o
 * campo oculto se chama `cod_vinculo`, mas quem abre a edição é
 * `cod_funcionario_vinculo`, que é o nome da chave primária.
 */
final class Category
{
    /**
     * @param  string  $screen  Nome do par de telas, sem o `_lst`/`_cad`.
     * @param  string  $heading  Título que a listagem escreve no topo.
     * @param  string  $field  Campo de texto obrigatório do formulário.
     * @param  string  $name  Valor gravado no cadastro.
     * @param  string  $renamed  Valor gravado na edição.
     * @param  class-string<Model>  $model  Modelo que lê a tabela.
     * @param  string  $column  Coluna onde o `field` é gravado.
     * @param  string  $key  Nome do parâmetro que a tela de cadastro lê do GET.
     * @param  array<int, string>  $context  `<select>` cujo valor só existe em
     *                                       tempo de execução — a instituição do
     *                                       cenário, o país, o estado —, na ordem
     *                                       em que a tela os encadeia.
     * @param  array<string, string>  $fields  Demais campos de texto obrigatórios.
     * @param  array<string, string>  $selects  Demais `<select>`, pelo rótulo.
     * @param  bool  $removable  A tela exclui de verdade pela interface.
     */
    public function __construct(
        public readonly string $screen,
        public readonly string $heading,
        public readonly string $field,
        public readonly string $name,
        public readonly string $renamed,
        public readonly string $model,
        public readonly string $column,
        public readonly string $key,
        public readonly array $context = [],
        public readonly array $fields = [],
        public readonly array $selects = [],
        public readonly bool $removable = true,
    ) {}

    /**
     * Todas as telas de catálogo, com o menu no nome para o relatório do Pest
     * dizer de onde cada uma vem.
     *
     * @return array<string, self>
     */
    public static function all(): array
    {
        $catalogue = [];

        $menus = [
            'Endereçamento' => self::addressing(),
            'Pessoas' => self::people(),
            'Escola' => self::school(),
            'Servidores' => self::employees(),
        ];

        foreach ($menus as $menu => $categories) {
            foreach ($categories as $title => $category) {
                $catalogue["{$menu} > {$title}"] = $category;
            }
        }

        return $catalogue;
    }

    /**
     * Endereçamento > Cadastros.
     *
     * Não são telas de "Tipos", mas são do mesmo feitio — e são as únicas em
     * cascata: o estado só aparece depois do país e o município só depois do
     * estado, por AJAX (public_uf_xml.php e public_municipio_xml.php). Os quatro
     * valores encadeados vêm do endereçamento que o cenário monta.
     *
     * Nenhuma delas exclui: as quatro usam o iEducar\Legacy\InteractWithDatabase,
     * cujo delete() faz `$model->findOrFail($id)->delete` — acesso a propriedade,
     * não chamada de método. O botão "Excluir" volta para a listagem como se
     * tivesse funcionado e o registro continua lá. É defeito de hoje; quando for
     * corrigido, basta tirar o `removable: false`.
     *
     * @return array<string, self>
     */
    public static function addressing(): array
    {
        return [
            // O formulário de país desenha `cod_ibge` duas vezes, pelo
            // inputsHelper()->integer() e pelo campoNumero() logo abaixo. Só o
            // segundo é obrigatório, e é o que o Form preenche.
            'Países' => new self(
                screen: 'public_pais',
                heading: 'Pais - Listagem',
                field: 'nome',
                name: 'Uruguai',
                renamed: 'República Oriental do Uruguai',
                model: Country::class,
                column: 'name',
                key: 'idpais',
                fields: ['cod_ibge' => '858'],
                removable: false,
            ),
            'Estados' => new self(
                screen: 'public_uf',
                heading: 'Uf - Listagem',
                field: 'nome',
                name: 'Paraná',
                renamed: 'Estado do Paraná',
                model: State::class,
                column: 'name',
                key: 'id',
                context: ['idpais'],
                fields: ['sigla_uf' => 'PR'],
                removable: false,
            ),
            'Municípios' => new self(
                screen: 'public_municipio',
                heading: 'Município - Listagem',
                field: 'nome',
                name: 'Criciúma',
                renamed: 'Criciúma do Sul',
                model: City::class,
                column: 'name',
                key: 'idmun',
                context: ['idpais', 'iduf'],
                removable: false,
            ),
            'Distritos' => new self(
                screen: 'public_distrito',
                heading: 'Distrito - Listagem',
                field: 'nome',
                name: 'Rio Maina',
                renamed: 'Distrito do Rio Maina',
                model: District::class,
                column: 'name',
                key: 'iddis',
                context: ['idpais', 'iduf', 'idmun'],
                removable: false,
            ),
        ];
    }

    /**
     * Pessoas > Cadastros > Tipos.
     *
     * @return array<string, self>
     */
    public static function people(): array
    {
        return [
            'Tipos de cor ou raça' => new self(
                screen: 'educar_raca',
                heading: 'Raça - Listagem',
                field: 'nm_raca',
                name: 'Amarela',
                renamed: 'Amarela ou oriental',
                model: LegacyRace::class,
                column: 'nm_raca',
                key: 'cod_raca',
            ),
            // O educar-deficiencia-cad.js mostra um segundo select conforme o
            // tipo — "Deficiência Educacenso" ou "Transtorno Educacenso" — e o
            // acaoEnviar() barra o envio, com um alert(), se o que está à vista
            // não estiver preenchido. Daí os dois campos, nesta ordem.
            'Tipos de deficiência' => new self(
                screen: 'educar_deficiencia',
                heading: 'Deficiência e transtorno - Listagem',
                field: 'nm_deficiencia',
                name: 'Dislexia',
                renamed: 'Dislexia do desenvolvimento',
                model: LegacyDeficiency::class,
                column: 'nm_deficiencia',
                key: 'cod_deficiencia',
                selects: [
                    'deficiency_type_id' => 'Transtorno',
                    'transtorno_educacenso' => 'Dislexia',
                ],
            ),
            'Tipos de religião' => new self(
                screen: 'educar_religiao',
                heading: 'Religiao - Listagem',
                field: 'nm_religiao',
                name: 'Budismo',
                renamed: 'Budismo tibetano',
                model: Religion::class,
                column: 'name',
                key: 'cod_religiao',
            ),
            // Não tem Excluir(): a tela desativa pelo campo "Situação", que é o
            // que DocumentAbsenceReasonTest exercita.
            'Motivos de ausência de documentação' => new self(
                screen: 'educar_motivo_ausencia_documentacao',
                heading: 'Motivos de ausência de documentação - Listagem',
                field: 'name',
                name: 'Documento em processo de segunda via',
                renamed: 'Segunda via em processamento',
                model: DocumentAbsenceReason::class,
                column: 'name',
                key: 'id',
                selects: ['requires_observation' => 'Sim', 'ativo' => 'Ativo'],
                removable: false,
            ),
        ];
    }

    /**
     * Escola > Cadastros > Tipos.
     *
     * Ficam de fora as telas que não são deste feitio: "Fórmulas de cálculo de
     * média", "Tabelas de arredondamento" e "Áreas de conhecimento" são módulos
     * novos (/module/...), e "Componentes da série" e "Sequências de enturmação"
     * dependem de curso e série já cadastrados. "Tipos de evento do calendário"
     * também sai daqui, porque o cadastro exige uma escola: no CategoryTest ela
     * aparece só com a listagem.
     *
     * @return array<string, self>
     */
    public static function school(): array
    {
        return [
            'Tipos de benefício' => new self(
                screen: 'educar_aluno_beneficio',
                heading: 'Benefício Aluno - Listagem',
                field: 'nm_beneficio',
                name: 'Bolsa Escola',
                renamed: 'Bolsa Escola Municipal',
                model: LegacyBenefit::class,
                column: 'nm_beneficio',
                key: 'cod_aluno_beneficio',
            ),
            // A observação é opcional no formulário, mas pmieducar.projeto.observacao
            // é NOT NULL e o ConvertEmptyStringsToNull transforma o campo vazio
            // em null: gravar sem preencher estoura a constraint. É defeito de
            // hoje, não do rebranding.
            'Tipos de projeto' => new self(
                screen: 'educar_projeto',
                heading: 'Projetos - Listagem',
                field: 'nome',
                name: 'Mais Educação',
                renamed: 'Mais Educação Integral',
                model: LegacyProject::class,
                column: 'nome',
                key: 'cod_projeto',
                fields: ['observacao' => 'Projeto de educação em tempo integral'],
            ),
            'Tipos de abandono' => new self(
                screen: 'educar_abandono_tipo',
                heading: 'Motivo da situação deixou de frequentar - Listagem',
                field: 'nome',
                name: 'Mudança de município',
                renamed: 'Mudança de município de residência',
                model: LegacyAbandonmentType::class,
                column: 'nome',
                key: 'cod_abandono_tipo',
                context: ['ref_cod_instituicao'],
            ),
            'Tipos de ocorrência disciplinar' => new self(
                screen: 'educar_tipo_ocorrencia_disciplinar',
                heading: 'Tipo Ocorrência Disciplinar - Listagem',
                field: 'nm_tipo',
                name: 'Indisciplina em sala',
                renamed: 'Indisciplina em sala de aula',
                model: LegacyDisciplinaryOccurrenceType::class,
                column: 'nm_tipo',
                key: 'cod_tipo_ocorrencia_disciplinar',
                context: ['ref_cod_instituicao'],
            ),
            'Tipos de transferência' => new self(
                screen: 'educar_transferencia_tipo',
                heading: 'Motivo Transferência - Listagem',
                field: 'nm_tipo',
                name: 'Transferência para outra rede',
                renamed: 'Transferência para a rede estadual',
                model: LegacyTransferType::class,
                column: 'nm_tipo',
                key: 'cod_transferencia_tipo',
                context: ['ref_cod_instituicao'],
            ),
            'Tipos de dispensa' => new self(
                screen: 'educar_tipo_dispensa',
                heading: 'Tipo Dispensa - Listagem',
                field: 'nm_tipo',
                name: 'Dispensa por atestado médico',
                renamed: 'Dispensa por laudo médico',
                model: LegacyExemptionType::class,
                column: 'nm_tipo',
                key: 'cod_tipo_dispensa',
                context: ['ref_cod_instituicao'],
            ),
            'Tipos de ensino' => new self(
                screen: 'educar_tipo_ensino',
                heading: 'Tipo Ensino - Listagem',
                field: 'nm_tipo',
                name: 'Educação de Jovens e Adultos',
                renamed: 'Educação de Jovens e Adultos (EJA)',
                model: LegacyEducationType::class,
                column: 'nm_tipo',
                key: 'cod_tipo_ensino',
                context: ['ref_cod_instituicao'],
            ),
            'Tipos de nível ensino' => new self(
                screen: 'educar_nivel_ensino',
                heading: 'Nível Ensino - Listagem',
                field: 'nm_nivel',
                name: 'Etapa anual',
                renamed: 'Etapa anual do curso',
                model: LegacyEducationLevel::class,
                column: 'nm_nivel',
                key: 'cod_nivel_ensino',
                context: ['ref_cod_instituicao'],
            ),
            'Tipos de regime' => new self(
                screen: 'educar_tipo_regime',
                heading: 'Tipo Regime - Listagem',
                field: 'nm_tipo',
                name: 'Ciclado',
                renamed: 'Ciclado por idade',
                model: LegacyRegimeType::class,
                column: 'nm_tipo',
                key: 'cod_tipo_regime',
                context: ['ref_cod_instituicao'],
            ),
            'Tipos de etapas' => new self(
                screen: 'educar_modulo',
                heading: 'Etapa - Listagem',
                field: 'nm_tipo',
                name: 'Semestre',
                renamed: 'Semestre letivo',
                model: LegacyStageType::class,
                column: 'nm_tipo',
                key: 'cod_modulo',
                context: ['ref_cod_instituicao'],
                fields: ['num_etapas' => '2', 'num_meses' => '5', 'num_semanas' => '20'],
            ),
            'Tipos de turma' => new self(
                screen: 'educar_turma_tipo',
                heading: 'Turma Tipo - Listagem',
                field: 'nm_tipo',
                name: 'Turma Multisseriada',
                renamed: 'Turma Multisseriada do campo',
                model: LegacySchoolClassType::class,
                column: 'nm_tipo',
                key: 'cod_turma_tipo',
                context: ['ref_cod_instituicao'],
                fields: ['sgl_tipo' => 'MULTI'],
            ),
        ];
    }

    /**
     * Servidores > Cadastros > Tipos.
     *
     * @return array<string, self>
     */
    public static function employees(): array
    {
        return [
            'Funções' => new self(
                screen: 'educar_funcao',
                heading: 'Função - Listagem',
                field: 'nm_funcao',
                name: 'Coordenador pedagógico',
                renamed: 'Coordenação pedagógica',
                model: LegacyRole::class,
                column: 'nm_funcao',
                key: 'cod_funcao',
                context: ['ref_cod_instituicao'],
                fields: ['abreviatura' => 'COORD'],
            ),
            // A descrição é gravada com no máximo 60 caracteres: o Novo() da tela
            // recusa qualquer coisa maior antes de chegar ao banco.
            'Escolaridade' => new self(
                screen: 'educar_escolaridade',
                heading: 'Escolaridade - Listagem',
                field: 'descricao',
                name: 'Pós-graduação',
                renamed: 'Pós-graduação stricto sensu',
                model: LegacySchoolingDegree::class,
                column: 'descricao',
                key: 'idesco',
                selects: ['escolaridade' => 'Educação superior'],
            ),
            'Motivos de afastamento' => new self(
                screen: 'educar_motivo_afastamento',
                heading: 'Motivo Afastamento - Listagem',
                field: 'nm_motivo',
                name: 'Licença para tratamento de saúde',
                renamed: 'Licença para tratamento da própria saúde',
                model: WithdrawalReason::class,
                column: 'nm_motivo',
                key: 'cod_motivo_afastamento',
                context: ['ref_cod_instituicao'],
            ),
            // A abreviatura é `character varying(4)`, daí o valor curto. O
            // parâmetro que abre a edição é `cod_funcionario_vinculo`, e não o
            // `cod_vinculo` do campo oculto: esta é a única destas telas em que
            // os dois nomes não coincidem.
            'Vínculos da alocação' => new self(
                screen: 'funcionario_vinculo',
                heading: 'Vínculos',
                field: 'nm_vinculo',
                name: 'Contrato temporário',
                renamed: 'Contrato temporário anual',
                model: LegacyBondType::class,
                column: 'nm_vinculo',
                key: 'cod_funcionario_vinculo',
                fields: ['abreviatura' => 'TEMP'],
            ),
        ];
    }

    /**
     * Endereço da listagem.
     */
    public function list(): string
    {
        return "/intranet/{$this->screen}_lst.php";
    }

    /**
     * Endereço do formulário, em branco ou já no registro informado.
     */
    public function form(int|string|null $id = null): string
    {
        return "/intranet/{$this->screen}_cad.php" . ($id === null ? '' : "?{$this->key}={$id}");
    }

    /**
     * Preenche o formulário de cadastro.
     *
     * O `$context` é o mesmo mapa para todas as telas — o que o cenário montou e
     * só se sabe em tempo de execução —, e cada tela escolhe dali os campos que
     * o formulário dela tem, na ordem declarada: é isso que faz a cascata
     * país → estado → município funcionar sem cada descritor repetir de onde o
     * valor vem.
     *
     * Os `<select>` passam pelo Form::cascade mesmo quando não dependem de
     * nenhum outro campo: a espera explícita é o que evita que o teste trave
     * quando a opção não existe.
     *
     * @param  array<string, int|string>  $context
     */
    public function fill(
        PendingAwaitablePage|AwaitableWebpage|Webpage $page,
        string $name,
        array $context = [],
    ): AwaitableWebpage|Webpage {
        foreach ($this->context as $field) {
            $page = Form::cascade($page, [$field => $context[$field]]);
        }

        $page = Form::cascade($page, $this->selects);

        return Form::fill($page, [$this->field => $name] + $this->fields);
    }

    /**
     * O registro gravado com o nome informado, se existir.
     */
    public function record(string $name): ?Model
    {
        return $this->model::query()->where($this->column, $name)->first();
    }

    /**
     * Quantos registros a tabela tem.
     *
     * Algumas destas tabelas já vêm com os dados de referência do cenário
     * (cadastro.raca, pmieducar.religions, portal.funcionario_vinculo), então o
     * que se afirma é que o número não mudou, e não que a tabela está vazia.
     */
    public function count(): int
    {
        return $this->model::query()->count();
    }
}
