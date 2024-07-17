<?php

namespace App\Models\Concerns\SoftDeletes;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Query\Builder;

/**
 * @method static static|\Illuminate\Database\Eloquent\Builder|Builder withTrashed(bool $withTrashed = true)
 * @method static static|\Illuminate\Database\Eloquent\Builder|Builder onlyTrashed()
 * @method static static|\Illuminate\Database\Eloquent\Builder|Builder withoutTrashed()
 */
trait LegacySoftDeletes
{
    use SoftDeletes;

    public static function bootSoftDeletes(): void
    {
        static::withoutGlobalScope(new SoftDeletingScope());
        static::addGlobalScope(new LegacySoftDeletesScope());
    }

    /**
     * Initialize the soft deleting trait for an instance.
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

    public function getDeletedAtColumn()
    {
        return 'ativo';
    }

    public function forceDelete()
    {
        $this->forceDeleting = true;

        return tap($this->delete(), function ($deleted) {
            $this->forceDeleting = false;

            if ($deleted) {
                $this->fireModelEvent('forceDeleted', false);
            }
        });
    }

    public function restore()
    {
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

    public function trashed()
    {
        return $this->{$this->getDeletedAtColumn()} === 0;
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return mixed
     */
    protected function performDeleteOnModel()
    {
        if ($this->forceDeleting) {
            $this->exists = false;

            return $this->setKeysForSaveQuery($this->newModelQuery())->forceDelete();
        }

        $this->runSoftDelete();

        return null;
    }

    protected function runSoftDelete()
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        $time = $this->freshTimestamp();

        $columns = [$this->getDeletedAtColumn() => 0];

        $this->{$this->getDeletedAtColumn()} = 0;

        if ($this->timestamps && !is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));

        $this->fireModelEvent('trashed', false);
    }
}
