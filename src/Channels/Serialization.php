<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Contracts\Database\ModelIdentifier;
use Illuminate\Contracts\Queue\QueueableCollection;
use Illuminate\Contracts\Queue\QueueableEntity;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;

trait Serialization
{
    use SerializesAndRestoresModelIdentifiers;

    /**
     * @param $data
     * @return mixed
     */
    public function serialize($data)
    {
        if ($data instanceof QueueableCollection || $data instanceof QueueableEntity) {
            return $this->getSerializedPropertyValue($data);
        }

        if (is_object($data)) {
            return $this->serialize(get_object_vars($data));
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->serialize($value, $key);
            }
            return $data;
        }

        return $data;
    }

    /**
     * @param $properties
     * @param $object
     * @return mixed
     */
    protected function restoreObject($object, $properties)
    {
        return tap($object, function ($object) use ($properties) {
            foreach ($properties as $property => $value) {
                $object->{$property} = $this->restoreValue($value);
            }
        });
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function restoreValue($value)
    {
        if ($this->isQueueableEntity($value)) {
            return $this->restoreQueueableEntity($value);
        }

        if (is_array($value)) {
            return array_map(function ($value) {
                return $this->restoreValue($value);
            }, $value);
        }

        return $value;
    }

    /**
     * @param $value
     * @return ModelIdentifier|mixed
     */
    protected function restoreQueueableEntity($value)
    {
        return $this->getRestoredPropertyValue(new ModelIdentifier($value['class'], $value['id'], $value['connection']));
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isQueueableEntity($value)
    {
        return is_array($value) && isset($value['class'], $value['id'], $value['connection']);
    }
}