<?php
/**
 * User: Lessmore92
 * Date: 12/13/2019
 * Time: 3:28 PM
 */

namespace Lessmore92\ApiConsumer\Contracts;


use Lessmore92\ApiConsumer\Models\Api;

abstract class AbstractApiBuilder
{
    /**
     * @return Api
     */
    abstract public function getApi();
}
