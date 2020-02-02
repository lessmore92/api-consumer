<?php
/**
 * Created by PhpStorm.
 * User: Mojtaba
 * Date: 12/14/2019
 * Time: 2:15 PM
 */

namespace Lessmore92\ApiConsumer\Foundation;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

class Collection implements ArrayAccess, IteratorAggregate, Countable
{
    private $items;

    public function __construct($items = [])
    {
        $this->items = $items;
    }

    public function filter(callable $callback)
    {
        return array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);
    }

    public function offsetExists($key)
    {
        return array_key_exists($key, $this->items);
    }

    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    public function offsetSet($key, $value)
    {
        if ($key === null)
        {
            $this->items[] = $value;
        }
        else
        {
            $this->items[$key] = $value;
        }
    }

    public function offsetUnset($key)
    {
        unset($this->items[$key]);
    }


    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    public function count()
    {
        return count($this->items);
    }
}
