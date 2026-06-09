<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Concerns\SupportsDefaultModels;

class BelongsToOne extends BelongsToMany
{
    use SupportsDefaultModels;

    public function getResults()
    {
        return $this->first() ?: $this->getDefaultFor($this->getRelated());
    }

    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->getDefaultFor($model));
        }

        return $models;
    }

    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = $this->buildDictionary($results);

        foreach ($models as $model) {
            if (isset($dictionary[$key = $model->{$this->parentKey}])) {
                $model->setRelation($relation, reset($dictionary[$key]));
            }
        }

        return $models;
    }

    public function newRelatedInstanceFor(Model $parent)
    {
        return $this->related->newInstance();
    }
}
