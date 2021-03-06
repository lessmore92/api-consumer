<?php
/**
 * User: Lessmore92
 * Date: 12/13/2019
 * Time: 3:28 PM
 */

namespace Lessmore92\ApiConsumer\Contracts;


use Lessmore92\ApiConsumer\Models\Request;

abstract class AbstractRequestBuilder
{
    /**
     * @return Request
     */
    abstract public function buildRequest();
}
