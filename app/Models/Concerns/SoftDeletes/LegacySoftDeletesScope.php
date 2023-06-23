<?php

namespace App\Models\Concerns\SoftDeletes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LegacySoftDeletesScope extends SoftDeletingScope
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected $extensions = ['Restore', 'WithTrashed', 'WithoutTrashed', 'OnlyTrashed'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where($model->getQualifiedDeletedAtColumn(), 1);
    }

    /**
     * Extend the query builder with the needed functions.
     *
     *
     * @return void
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }

        $builder->onDelete(function (Builder $builder) {
            $column = $this->getDeletedAtColumn($builder);

            return $builder->update([
                $column => 0,
            ]);
        });
    }

    /**
     * Get the "deleted at" column for the builder.
     *
     *
     * @return string
     */
    protected function getDeletedAtColumn(Builder $builder)
    {
        if (count((array) $builder->getQuery()->joins) > 0) {
            return $builder->getModel()->getQualifiedDeletedAtColumn();
        }

        return $builder->getModel()->getDeletedAtColumn();
    }

    /**
     * Add the restore extension to the builder.
     *
     *
     * @return void
     */
    protected function addRestore(Builder $builder)
    {
        $builder->macro('restore', function (Builder $builder) {
            $builder->withTrashed();

            return $builder->update([$builder->getModel()->getDeletedAtColumn() => 1]);
        });
    }

    /**
     * Add the with-trashed extension to the builder.
     *
     *
     * @return void
     */
    protected function addWithTrashed(Builder $builder)
    {
        $builder->macro('withTrashed', function (Builder $builder, $withTrashed = true) {
            if (!$withTrashed) {
                return $builder->withoutTrashed();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-trashed extension to the builder.
     *
     *
     * @return void
     */
    protected function addWithoutTrashed(Builder $builder)
    {
        $builder->macro('withoutTrashed', function (Builder $builder) {
            $model = $builder->getModel();
            $builder->withoutGlobalScope(SoftDeletingScope::class)->withoutGlobalScope($this)->where($model->getQualifiedDeletedAtColumn(), 1);

            return $builder;
        });
    }

    /**
     * Add the only-trashed extension to the builder.
     *
     *
     * @return void
     */
    protected function addOnlyTrashed(Builder $builder)
    {
        $builder->macro('onlyTrashed', function (Builder $builder) {
            $model = $builder->getModel();
            $builder->withoutGlobalScope(SoftDeletingScope::class)->withoutGlobalScope($this)->where($model->getQualifiedDeletedAtColumn(), 0);

            return $builder;
        });
    }

    protected function addActive(Builder $builder)
    {
        $builder->macro('active', function (Builder $builder) {
            $model = $builder->getModel();
            $builder->withoutGlobalScope(SoftDeletingScope::class)->withoutGlobalScope($this)->where($model->getQualifiedDeletedAtColumn(), 1);

            return $builder;
        });
    }
}
