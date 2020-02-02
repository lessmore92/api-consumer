<?php
/**
 * User: Lessmore92
 * Date: 12/13/2019
 * Time: 2:16 PM
 */

namespace Lessmore92\ApiConsumer\Contracts;


interface RequestModelInterface extends ModelInterface
{
    const REQUEST_METHOD_POST   = 'POST';
    const REQUEST_METHOD_GET    = 'GET';
    const REQUEST_METHOD_PUT    = 'PUT';
    const REQUEST_METHOD_PATCH  = 'PATCH';
    const REQUEST_METHOD_DELETE = 'DELETE';
    const REQUEST_METHOD_HEADER = 'HEADER';
}
