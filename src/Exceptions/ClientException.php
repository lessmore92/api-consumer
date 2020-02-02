<?php
/**
 * User: Lessmore92
 * Date: 12/13/2019
 * Time: 3:53 PM
 */

namespace Lessmore92\ApiConsumer\Exceptions;

class ClientException extends BadResponseException
{
    private $body;

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
}
