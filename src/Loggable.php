<?php

namespace Log\SDK;

trait Loggable
{
    /**
     * The attributes that are logged.
     *
     * @var array
     */
    protected $loggable = [];

    /**
     * Get the loggable array for the model.
     *
     * @param string[]|null $relations
     * @param string $keyPrefix
     * @return array
     */
    public function toLoggableArray($relations = null, $keyPrefix = '')
    {
        $entries = [];

        foreach ($this->loggable as $attribute) {
            $entries[] = [
                'key' => $keyPrefix . ':' . $attribute,
                'value' => $this->getAttribute($attribute)
            ];
        }

        if (!empty($relations)) {
            $this->loadMissing($relations);

            foreach ($relations as $relation) {
                $entries = array_merge($entries, $this->getRelatedLoggableArray($relation));
            }
        }

        return $entries;
    }

    /**
     * @param string $relation
     * @return array
     */
    private function getRelatedLoggableArray($relation)
    {
        $relatedInstance = object_get($this, $relation);
        if ($relatedInstance) {
            return $relatedInstance->toLoggableArray(null, $relation);
        } else {
            return $this->makeDummyLoggableArray($relation);
        }
    }

    /**
     * @param string $relation
     * @return array
     */
    private function makeDummyLoggableArray($relation)
    {
        $segments = explode('.', $relation);

        $relatedInstance = $this;
        foreach ($segments as $segment) {
            $relatedInstance = $relatedInstance->$segment()->getRelated();
        }

        return array_map(function ($attribute) use ($relation) {
            return [
                'key' => $relation . ':' . $attribute,
                'value' => null,
            ];
        }, $relatedInstance->loggable);
    }
}
