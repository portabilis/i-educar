<?php

namespace App\Models\Concerns\SoftDeletes;


use Closure;
use Illuminate\Database\Query\Builder;

/**
 * @method static static|\Illuminate\Database\Eloquent\Builder|Builder withTrashed(bool $withTrashed = true)
 * @method static static|\Illuminate\Database\Eloquent\Builder|Builder onlyTrashed()
 * @method static static|\Illuminate\Database\Eloquent\Builder|Builder withoutTrashed()
 */
trait LegacySoftDeletes {

    /**
     * Indicates if the model is currently force deleting.
     *
     * @var bool
     */
    protected $forceDeleting = false;

    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootLegacySoftDeletes(): void {
        static::addGlobalScope(new LegacySoftDeletesScope());
    }

    /**
     * Register a "restoring" model event callback with the dispatcher.
     *
     * @param Closure|string $callback
     *
     * @return void
     */
    public static function restoring($callback): void {
        static::registerModelEvent('restoring', $callback);
    }

    /**
     * Register a "restored" model event callback with the dispatcher.
     *
     * @param Closure|string $callback
     *
     * @return void
     */
    public static function restored($callback) {
        static::registerModelEvent('restored', $callback);
    }

    /**
     * Register a "forceDeleted" model event callback with the dispatcher.
     *
     * @param Closure|string $callback
     *
     * @return void
     */
    public static function forceDeleted($callback) {
        static::registerModelEvent('forceDeleted', $callback);
    }

    /**
     * Initialize the soft deleting trait for an instance.
     *
     * @return void
     */
    public function initializeLegacySoftDeletes() {
        if ( ! isset($this->casts[ $this->getDeletedAtColumn() ])) {
            $this->casts[ $this->getDeletedAtColumn() ] = 'integer';
        }
    }

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getDeletedAtColumn() {
        return 'ativo';
    }

    /**
     * Force a hard delete on a soft deleted model.
     *
     * @return bool|null
     */
    public function forceDelete() {
        $this->forceDeleting = true;

        return tap($this->delete(), function ($deleted) {
            $this->forceDeleting = false;

            if ($deleted) {
                $this->fireModelEvent('forceDeleted', false);
            }
        });
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore() {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getDeletedAtColumn()} = 1;

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * Determine if the model instance has been soft-deleted.
     *
     * @return bool
     */
    public function trashed() {
        return $this->{$this->getDeletedAtColumn()} === 0;
    }

    /**
     * Determine if the model is currently force deleting.
     *
     * @return bool
     */
    public function isForceDeleting() {
        return $this->forceDeleting;
    }

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedDeletedAtColumn() {
        return $this->qualifyColumn($this->getDeletedAtColumn());
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return mixed
     */
    protected function performDeleteOnModel() {
        if ($this->forceDeleting) {
            $this->exists = false;

            return $this->setKeysForSaveQuery($this->newModelQuery())->forceDelete();
        }

        return $this->runSoftDelete();
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function runSoftDelete() {

        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        $time = $this->freshTimestamp();

        $columns = [$this->getDeletedAtColumn() => 0];

        $this->{$this->getDeletedAtColumn()} = 0;

        if ($this->timestamps && ! is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[ $this->getUpdatedAtColumn() ] = $this->fromDateTime($time);
        }

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));

        $this->fireModelEvent('trashed', false);
    }
}
