<?php

namespace Makeable\DatabaseNotifications;

use Illuminate\Database\Eloquent\Builder;

trait InteractsWithPolymorphism
{
    /**
     * @param $relation
     * @param $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function setMorph($relation, $model)
    {
        return $this->forceFill([
            "{$relation}_id" => optional($model)->getKey(),
            "{$relation}_type" => optional($model)->getMorphClass(),
        ]);
    }

    /**
     * @param Builder $query
     * @param $relation
     * @param $model
     * @return Builder
     */
    public function scopeWhereMorph($query, $relation, $model)
    {
        return $query
            ->where("{$relation}_id", optional($model)->getKey())
            ->where("{$relation}_type", optional($model)->getMorphClass());
    }

    /**
     * @param Builder $query
     * @param $relation
     * @param $model
     * @return Builder
     */
    public function scopeWhereMorphNot($query, $relation, $model)
    {
        return $query
            ->where("{$relation}_id", "!=", optional($model)->getKey())
            ->orWhere("{$relation}_type", "!=", optional($model)->getMorphClass());
    }
}
