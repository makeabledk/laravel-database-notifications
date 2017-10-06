<?php

namespace Makeable\DatabaseNotifications;

class Message
{
    protected $type, $notifiable, $payload, $available_at;

    /**
     * @param $payload
     */
    public function __construct($payload)
    {
        $this->data = $payload;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getNotifiable()
    {
        return $this->notifiable;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getAvailableAt()
    {
        return $this->available_at;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param mixed $notifiable
     * @return $this
     */
    public function setNotifiable($notifiable)
    {
        $this->notifiable = $notifiable;

        return $this;
    }

    /**
     * @param mixed $payload
     * @return $this
     */
    public function setData($payload)
    {
        $this->data = $payload;

        return $this;
    }

    /**
     * @param mixed $available_at
     * @return $this
     */
    public function setAvailableAt($available_at)
    {
        $this->available_at = $available_at;

        return $this;
    }

    /**
     * @param $callable
     * @return $this
     */
    public function transformData($callable)
    {
        $this->data = call_user_func($callable, $this->data);

        return $this;
    }
}