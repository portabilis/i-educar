<?php

namespace App\Models\Concerns;

use App\Models\Relations\BelongsToOne;

trait HasBelongsToOne
{
    public function belongsToOne(
        string $related,
        ?string $table = null,
        ?string $foreignPivotKey = null,
        ?string $relatedPivotKey = null,
        ?string $parentKey = null,
        ?string $relatedKey = null,
        ?string $relation = null
    ): BelongsToOne {
        if (is_null($relation)) {
            $relation = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'];
        }

        $instance = $this->newRelatedInstance($related);

        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();
        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        return new BelongsToOne(
            $instance->newQuery(), $this, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey ?: $this->getKeyName(),
            $relatedKey ?: $instance->getKeyName(), $relation
        );
    }
}
