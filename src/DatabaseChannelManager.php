<?php

namespace Makeable\DatabaseNotifications;

use Exception;

class DatabaseChannelManager
{
    /**
     * @var array
     */
    protected $aliases = [];

    /**
     * @param $alias
     * @return mixed
     * @throws Exception
     */
    public function channel($alias)
    {
        if (! array_get($this->aliases, $alias)) {
            throw new Exception('Unknown channel '.$alias);
        }

        return app()->make($this->aliases[$alias]);
    }

    /**
     * @param $alias
     * @param $channel
     */
    public function extend($alias, $channel)
    {
        $this->aliases[$alias] = $channel;
    }

    /**
     * @param $channel
     * @return false|int|string
     * @throws Exception
     */
    public function getAlias($channel)
    {
        $channel = is_string($channel) ? $channel : get_class($channel);
        $alias = array_search($channel, $this->aliases);

        if ($alias === false) {
            throw new Exception('No alias registered for '.$channel);
        }

        return $alias;
    }
}
