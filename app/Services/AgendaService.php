<?php

namespace App\Services;

use App\Models\Builders\LegacyAgendaBuilder;
use App\Models\LegacyAgenda;
use App\Models\LegacyAgendaCommitment;
use App\Models\LegacyPerson;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class AgendaService
{
    private const ERROR_TITLE_OR_DESCRIPTION = 'Você deve preencher o campo Título ou o campo Descrição<br>';

    private const ERROR_DATE = 'Você deve preencher o campo Data corretamente. Formato data: dd/mm/aaaa<br>';

    private const ERROR_START_TIME = 'Você deve preencher o campo Hora de Início corretamente. Formato hora: hh:mm<br>';

    private const ERROR_END_TIME = 'Você deve preencher o campo Hora de Fim corretamente. Formato hora: hh:mm<br>';

    /**
     * Retorna a agenda da pessoa, criando uma nova quando ela ainda não possui
     */
    public function firstOrCreate(int $person): LegacyAgenda
    {
        return LegacyAgenda::query()->whereOwner($person)->first() ?? $this->createForPerson($person);
    }

    public function find(int $agenda): ?LegacyAgenda
    {
        return LegacyAgenda::query()->find($agenda);
    }

    /**
     * Permite o acesso à agenda ao dono e aos responsáveis por ela
     */
    public function hasPermission(int $editor, ?LegacyAgenda $agenda): bool
    {
        if ($agenda === null) {
            return false;
        }

        if ($editor === 0) {
            return true;
        }

        return LegacyAgenda::query()
            ->whereKey($agenda->getKey())
            ->where(function (LegacyAgendaBuilder $query) use ($editor) {
                $query->whereOwner($editor)
                    ->orWhere(fn (LegacyAgendaBuilder $query) => $query->whereResponsible($editor));
            })
            ->exists();
    }

    /**
     * Cadastra um compromisso e retorna as mensagens de erro encontradas
     */
    public function createCommitment(
        LegacyAgenda $agenda,
        int $editor,
        ?int $commitment,
        ?string $title,
        ?string $description,
        ?string $date,
        ?string $startTime,
        ?string $endTime = null,
        bool $public = false,
        bool $important = false,
        ?string $repeatDays = null,
        ?string $repeatCount = null,
    ): string {
        $error = $this->validate($title, $description, $date, $startTime);

        if ($error !== '') {
            return $error;
        }

        $attributes = [
            'ref_cod_agenda' => $agenda->getKey(),
            'ref_ref_cod_pessoa_cad' => $editor,
            'titulo' => $title ?: null,
            'descricao' => $description ?: null,
            'data_cadastro' => DB::raw('NOW()'),
        ];

        if ($public) {
            $attributes['publico'] = 1;
        }

        if ($important) {
            $attributes['importante'] = 1;
        }

        $time = strtotime($this->parseDate($date));

        DB::beginTransaction();

        if (is_numeric($repeatDays) && is_numeric($repeatCount) && $repeatDays && $repeatCount) {
            for ($i = 0; $i < $repeatCount; $i++) {
                $this->storeCommitment($attributes, $time, $startTime, $endTime, null);
                $time += 86400 * $repeatDays;
            }
        } else {
            $this->storeCommitment($attributes, $time, $startTime, $endTime, $commitment);
        }

        DB::commit();

        return '';
    }

    /**
     * Inativa as versões anteriores do compromisso e cadastra a versão editada
     */
    public function updateCommitment(
        LegacyAgenda $agenda,
        int $editor,
        int $commitment,
        ?string $title,
        ?string $description,
        ?string $date,
        ?string $startTime,
        ?string $endTime = null,
        bool $public = false,
        bool $important = false,
    ): string {
        if (!$this->commitmentBelongsToAgenda($commitment, $agenda)) {
            return '';
        }

        $error = $this->validate($title, $description, $date, $startTime);

        if ($error !== '') {
            return $error;
        }

        DB::beginTransaction();

        $this->deactivateCommitment($commitment);
        $error = $this->createCommitment($agenda, $editor, $commitment, $title, $description, $date, $startTime, $endTime, $public, $important);

        DB::commit();

        return $error;
    }

    /**
     * Transforma uma anotação em compromisso, atribuindo a ela uma hora de fim
     */
    public function saveNoteAsCommitment(LegacyAgenda $agenda, int $editor, int $commitment, ?string $endTime): string
    {
        if (!$this->commitmentBelongsToAgenda($commitment, $agenda)) {
            return '';
        }

        if (!$this->isValidTime($endTime)) {
            return self::ERROR_END_TIME;
        }

        $note = LegacyAgendaCommitment::query()
            ->whereCommitment($commitment)
            ->whereVersion($this->maxVersion($commitment))
            ->first(['data_inicio', 'titulo', 'descricao', 'importante', 'publico']);

        if ($note === null) {
            return '';
        }

        return $this->updateCommitment(
            agenda: $agenda,
            editor: $editor,
            commitment: $commitment,
            title: $note->titulo,
            description: $note->descricao,
            date: date('d/m/Y', strtotime($note->data_inicio)),
            startTime: date('H:i', strtotime($note->data_inicio)),
            endTime: $endTime,
            public: (bool) $note->publico,
            important: (bool) $note->importante,
        );
    }

    /**
     * Torna ativa apenas a versão informada do compromisso
     */
    public function restoreVersion(LegacyAgenda $agenda, int $commitment, int $version): string
    {
        if (!$this->commitmentBelongsToAgenda($commitment, $agenda)) {
            return '';
        }

        DB::beginTransaction();

        $this->deactivateCommitment($commitment);

        LegacyAgendaCommitment::query()
            ->whereCommitment($commitment)
            ->whereVersion($version)
            ->update(['ativo' => 1]);

        DB::commit();

        return "Versão {$version} restaurada com sucesso.<br>";
    }

    public function deleteCommitment(LegacyAgenda $agenda, int $commitment): void
    {
        if ($this->commitmentBelongsToAgenda($commitment, $agenda)) {
            $this->deactivateCommitment($commitment);
        }
    }

    /**
     * Lista os compromissos do dia, no formato dd/mm/aaaa, ignorando as anotações
     */
    public function getDayCommitments(LegacyAgenda $agenda, string $date): Collection
    {
        $dbDate = $this->parseDate($date);

        if ($dbDate === null) {
            return new Collection;
        }

        return LegacyAgendaCommitment::query()
            ->whereAgenda($agenda->getKey())
            ->active()
            ->withEndDate()
            ->betweenStartDates("{$dbDate} 00:00:00", "{$dbDate} 23:59:59")
            ->orderBy('data_inicio')
            ->orderBy('versao')
            ->orderBy('data_fim')
            ->orderBy('cod_agenda_compromisso')
            ->get(['data_inicio', 'cod_agenda_compromisso', 'versao', 'data_fim', 'titulo', 'descricao', 'importante', 'publico']);
    }

    private function commitmentBelongsToAgenda(int $commitment, LegacyAgenda $agenda): bool
    {
        if (!$commitment) {
            return false;
        }

        return LegacyAgendaCommitment::query()
            ->whereCommitment($commitment)
            ->whereAgenda($agenda->getKey())
            ->exists();
    }

    private function maxVersion(int $commitment): int
    {
        return (int) LegacyAgendaCommitment::query()->whereCommitment($commitment)->max('versao');
    }

    /**
     * Retorna a maior versão de cada compromisso, indexada pelo código do compromisso
     */
    public function getMaxVersions(Collection $commitments): SupportCollection
    {
        return LegacyAgendaCommitment::query()
            ->whereIn('cod_agenda_compromisso', $commitments->pluck('cod_agenda_compromisso'))
            ->groupBy('cod_agenda_compromisso')
            ->selectRaw('cod_agenda_compromisso, MAX(versao) AS max_versao')
            ->pluck('max_versao', 'cod_agenda_compromisso');
    }

    private function createForPerson(int $person): LegacyAgenda
    {
        return LegacyAgenda::query()->create([
            'ref_ref_cod_pessoa_cad' => $person,
            'ref_ref_cod_pessoa_own' => $person,
            'nm_agenda' => $this->buildAgendaName($person),
            'publica' => 0,
            'envia_alerta' => 0,
            'data_cad' => DB::raw('NOW()'),
        ]);
    }

    /**
     * Nomeia a agenda com o primeiro e o último nome da pessoa
     */
    private function buildAgendaName(int $person): string
    {
        $name = rtrim((string) LegacyPerson::query()->whereKey($person)->value('nome'));
        $nameParts = explode(' ', $name);
        $name = $nameParts[0];

        if (count($nameParts) > 1) {
            $name .= ' ' . $nameParts[count($nameParts) - 1];
        }

        return $name;
    }

    private function storeCommitment(array $attributes, int $time, string $startTime, ?string $endTime, ?int $commitment): void
    {
        $date = date('Y-m-d', $time);
        $attributes['data_inicio'] = "{$date} {$startTime}";

        if ($this->isValidTime($endTime)) {
            $attributes['data_fim'] = "{$date} {$endTime}";
        }

        if ($commitment) {
            $attributes['cod_agenda_compromisso'] = $commitment;
            $attributes['versao'] = $this->maxVersion($commitment) + 1;
        } else {
            $attributes['cod_agenda_compromisso'] = $this->nextCommitmentCode();
            $attributes['versao'] = 1;
        }

        LegacyAgendaCommitment::query()->create($attributes);
    }

    /**
     * A tabela de compromissos não possui sequence, o código é obtido a partir do maior já gravado
     */
    private function nextCommitmentCode(): int
    {
        return (int) LegacyAgendaCommitment::query()->max('cod_agenda_compromisso') + 1;
    }

    private function validate(?string $title, ?string $description, ?string $date, ?string $startTime): string
    {
        if (!$title && !$description) {
            return self::ERROR_TITLE_OR_DESCRIPTION;
        }

        if ($this->parseDate($date) === null) {
            return self::ERROR_DATE;
        }

        if (!$this->isValidTime($startTime)) {
            return self::ERROR_START_TIME;
        }

        return '';
    }

    /**
     * Converte a data do formato dd/mm/aaaa para o formato do banco, retornando null quando inválida
     */
    private function parseDate(?string $date): ?string
    {
        $parts = explode('/', (string) $date);

        if (count($parts) !== 3 || strlen($parts[2]) !== 4 || !checkdate((int) $parts[1], (int) $parts[0], (int) $parts[2])) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);
    }

    /**
     * Valida o horário no formato hh:mm
     */
    private function isValidTime(?string $time): bool
    {
        return (bool) preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', (string) $time);
    }

    private function deactivateCommitment(int $commitment): void
    {
        LegacyAgendaCommitment::query()->whereCommitment($commitment)->update(['ativo' => 0]);
    }
}
