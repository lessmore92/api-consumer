<?php
/**
 * User: Lessmore92
 * Date: 12/20/2019
 * Time: 9:55 PM
 */

namespace Lessmore92\ApiConsumer\Foundation;


class ModelObjectCache extends Singleton
{
    private $objects = [];

    public function getCache($class, $key)
    {
        if (isset($this->objects[$class][$key]))
        {
            return $this->objects[$class][$key];
        }

        return null;
    }

    public function setCache($class, $key, $obj)
    {
        $this->objects[$class][$key] = $obj;
    }
}
