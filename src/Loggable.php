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
     * @return array
     */
    public function toLoggableArray($relations = null)
    {
        $pairs = [];

        foreach($this->loggable as $attribute) {
            $pairs[] = [$attribute => $this->getAttribute($attribute)];
        }

        if(!empty($relations)) {
            $this->loadMissing($relations);

            foreach ($relations as $relation) {
                $pairs = array_merge($pairs, $this->getRelatedLoggableArray($relation));
            }
        }

        return $pairs;
    }

    /**
     * @param string $relation
     * @return array
     */
    private function getRelatedLoggableArray($relation)
    {
        $relatedInstance = object_get($this, $relation);
        if($relatedInstance) {
            return $relatedInstance->toLoggableArray();
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

        return array_fill(0, count($relatedInstance->loggable), null);
    }
}
