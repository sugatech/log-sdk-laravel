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
     * Get the log entries for the model.
     *
     * @param string[]|null $relations
     * @param string $keyPrefix
     * @return array
     */
    public function toLogEntries($relations = null, $keyPrefix = '')
    {
        $entries = [];

        foreach ($this->loggable as $attribute) {
            $entries[] = [
                'key' => $keyPrefix . ':' . $attribute,
                'value' => $this->getAttribute($attribute),
            ];
        }

        if (!empty($relations)) {
            $this->loadMissing($relations);

            foreach ($relations as $relation) {
                $entries = array_merge($entries, $this->getRelatedLogEntries($relation));
            }
        }

        return $entries;
    }

    /**
     * @param string $relation
     * @return array
     */
    private function getRelatedLogEntries($relation)
    {
        $relatedInstance = object_get($this, $relation);
        if ($relatedInstance) {
            return $relatedInstance->toLogEntries(null, $relation);
        } else {
            return $this->makeDummyLogEntries($relation);
        }
    }

    /**
     * @param string $relation
     * @return array
     */
    private function makeDummyLogEntries($relation)
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
