<?php

namespace App\Models\Concerns\SoftDeletes;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder;
use Illuminate\Events\QueuedClosure;
use Illuminate\Support\Collection;
use Illuminate\Support\Collection as BaseCollection;

/**
 * Soft delete legado usando campo `ativo` (inteiro 0/1) em vez de `deleted_at` (timestamp).
 *
 * @method static static|\Illuminate\Database\Eloquent\Builder|Builder withTrashed(bool $withTrashed = true)
 * @method static static|\Illuminate\Database\Eloquent\Builder|Builder onlyTrashed()
 * @method static static|\Illuminate\Database\Eloquent\Builder|Builder withoutTrashed()
 */
trait LegacySoftDeletes
{
    /**
     * Indica se o modelo está sendo forçado a deletar permanentemente.
     *
     * @var bool
     */
    protected $forceDeleting = false;

    /**
     * Inicializa o trait para o modelo.
     *
     * @return void
     */
    public static function bootLegacySoftDeletes()
    {
        static::addGlobalScope(new LegacySoftDeletesScope);
    }

    /**
     * Inicializa o trait para uma instância.
     *
     * @return void
     */
    public function initializeLegacySoftDeletes()
    {
        if (!isset($this->casts[$this->getDeletedAtColumn()])) {
            $this->casts[$this->getDeletedAtColumn()] = 'integer';
        }

        $this->legacy = array_unique(array_merge($this->legacy, [
            'active' => 'ativo',
        ]));
    }

    /**
     * Força a exclusão permanente de um registro soft-deleted.
     *
     * @return bool|null
     */
    public function forceDelete()
    {
        if ($this->fireModelEvent('forceDeleting') === false) {
            return false;
        }

        $this->forceDeleting = true;

        return tap($this->delete(), function ($deleted) {
            $this->forceDeleting = false;

            if ($deleted) {
                $this->fireModelEvent('forceDeleted', false);
            }
        });
    }

    /**
     * Força a exclusão permanente sem disparar eventos.
     *
     * @return bool|null
     */
    public function forceDeleteQuietly()
    {
        return static::withoutEvents(fn () => $this->forceDelete());
    }

    /**
     * Força a exclusão permanente dos registros com os IDs informados.
     *
     * @param  Collection|array|int|string  $ids
     * @return int
     */
    public static function forceDestroy($ids)
    {
        if ($ids instanceof EloquentCollection) {
            $ids = $ids->modelKeys();
        }

        if ($ids instanceof BaseCollection) {
            $ids = $ids->all();
        }

        $ids = is_array($ids) ? $ids : func_get_args();

        if (count($ids) === 0) {
            return 0;
        }

        // Busca os registros individualmente para que os eventos sejam disparados
        // corretamente com os atributos completos do modelo.
        $key = ($instance = new static)->getKeyName();

        $count = 0;

        foreach ($instance->withTrashed()->whereIn($key, $ids)->get() as $model) {
            if ($model->forceDelete()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Executa a query de exclusão no modelo.
     *
     * @return mixed
     */
    protected function performDeleteOnModel()
    {
        if ($this->forceDeleting) {
            return tap($this->setKeysForSaveQuery($this->newModelQuery())->forceDelete(), function () {
                $this->exists = false;
            });
        }

        return $this->runSoftDelete();
    }

    /**
     * Executa o soft delete no modelo (ativo = 0).
     *
     * @return void
     */
    protected function runSoftDelete()
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        $time = $this->freshTimestamp();

        $columns = [$this->getDeletedAtColumn() => 0];

        $this->{$this->getDeletedAtColumn()} = 0;

        if ($this->usesTimestamps() && !is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));

        $this->fireModelEvent('trashed', false);
    }

    /**
     * Restaura um registro soft-deleted (ativo = 1).
     *
     * @return bool
     */
    public function restore()
    {
        // Se o evento restoring retornar false, cancela a operação.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getDeletedAtColumn()} = 1;

        // Após salvar, dispara o evento restored para que o desenvolvedor
        // possa executar ações adicionais após a restauração.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * Restaura um registro soft-deleted sem disparar eventos.
     *
     * @return bool
     */
    public function restoreQuietly()
    {
        return static::withoutEvents(fn () => $this->restore());
    }

    /**
     * Determina se o registro foi soft-deleted (ativo = 0).
     *
     * @return bool
     */
    public function trashed()
    {
        return $this->{$this->getDeletedAtColumn()} === 0;
    }

    /**
     * Registra um callback para o evento softDeleted.
     *
     * @param  QueuedClosure|callable|class-string  $callback
     * @return void
     */
    public static function softDeleted($callback)
    {
        static::registerModelEvent('trashed', $callback);
    }

    /**
     * Registra um callback para o evento restoring.
     *
     * @param  QueuedClosure|callable|class-string  $callback
     * @return void
     */
    public static function restoring($callback)
    {
        static::registerModelEvent('restoring', $callback);
    }

    /**
     * Registra um callback para o evento restored.
     *
     * @param  QueuedClosure|callable|class-string  $callback
     * @return void
     */
    public static function restored($callback)
    {
        static::registerModelEvent('restored', $callback);
    }

    /**
     * Registra um callback para o evento forceDeleting.
     *
     * @param  QueuedClosure|callable|class-string  $callback
     * @return void
     */
    public static function forceDeleting($callback)
    {
        static::registerModelEvent('forceDeleting', $callback);
    }

    /**
     * Registra um callback para o evento forceDeleted.
     *
     * @param  QueuedClosure|callable|class-string  $callback
     * @return void
     */
    public static function forceDeleted($callback)
    {
        static::registerModelEvent('forceDeleted', $callback);
    }

    /**
     * Determina se o modelo está sendo forçado a deletar permanentemente.
     *
     * @return bool
     */
    public function isForceDeleting()
    {
        return $this->forceDeleting;
    }

    /**
     * Retorna o nome da coluna de soft delete.
     *
     * @return string
     */
    public function getDeletedAtColumn()
    {
        return 'ativo';
    }

    /**
     * Retorna o nome qualificado da coluna de soft delete.
     *
     * @return string
     */
    public function getQualifiedDeletedAtColumn()
    {
        return $this->qualifyColumn($this->getDeletedAtColumn());
    }
}
