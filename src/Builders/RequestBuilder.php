<?php
/**
 * User: Lessmore92
 * Date: 12/13/2019
 * Time: 3:21 PM
 */

namespace Lessmore92\ApiConsumer\Builders;


use Lessmore92\ApiConsumer\Contracts\AbstractRequestBuilder;
use Lessmore92\ApiConsumer\Exceptions\RequestMethodNotSupported;
use Lessmore92\ApiConsumer\Models\Api;
use Lessmore92\ApiConsumer\Models\Request;

class RequestBuilder extends AbstractRequestBuilder
{
    /**
     * @var Api
     */
    private $api;

    /**
     * @var Request
     */
    private $request;

    /**
     * RequestBuilder constructor.
     * @param Api|null $api
     */
    public function __construct(Api $api = null)
    {
        $this->request = new Request();
        $this->api     = $api;

        $this->init();
    }

    private function init()
    {
        if ($this->api === null)
        {
            return;
        }

        if ($this->api->base_url !== '')
        {
            $this->request->url = $this->api->base_url;
        }

        if ($this->api->api_key !== '')
        {
            if ($this->api->api_key_place === Api::API_KEY_IN_QUERY_STRING)
            {
                $this->request->addQueryString($this->api->api_key_param_name, $this->api->api_key);
            }
            else if ($this->api->api_key_place === Api::API_KEY_IN_HEADER)
            {
                $this->request->addHeader($this->api->api_key_param_name, $this->api->api_key);
            }
            //TODO throw exception api key place not valid
        }
    }

    public function setApi($api)
    {
        $this->api = $api;
        $this->init();
    }

    /**
     * @return Request
     */
    public function buildRequest()
    {
        $request = unserialize(serialize($this->request));
        $this->reset();
        return $request;
    }

    private function reset()
    {
        $this->endPoint('');
        $this->setBody('');
        $this->request->clearOnetimeHeaders();
        $this->request->clearOnetimeQueryStrings();
    }

    /**
     * @param string $endpoint
     * @return RequestBuilder
     */
    public function endPoint($endpoint)
    {
        $this->request->path = $endpoint;
        return $this;
    }

    /**
     * @param string $body
     * @return RequestBuilder
     */
    public function setBody($body)
    {
        $this->request->is_json = false;
        $this->request->body    = $body;
        return $this;
    }

    /**
     * @param array $body
     * @return RequestBuilder
     */
    public function setJsonBody($body)
    {
        $this->request->is_json   = true;
        $this->request->json_body = $body;
        return $this;
    }

    /**
     *
     * @param string $key
     * @param string $value
     * @param bool $onetime if true header remove after buildRequest() called
     * @return RequestBuilder
     */
    public function addHeader($key, $value, $onetime = false)
    {
        if ($onetime)
        {
            $this->request->addOnetimeHeader($key, $value);
        }
        else
        {
            $this->request->addHeader($key, $value);
        }
        return $this;
    }

    /**
     *
     * @param string $key
     * @param bool $onetime if true header removed from onetime headers
     * @return RequestBuilder
     */
    public function removeHeader($key, $onetime = false)
    {
        if ($onetime)
        {
            $this->request->removeOnetimeHeader($key);
        }
        else
        {
            $this->request->removeHeader($key);
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @param bool $onetime if true query_string remove after buildRequest() called
     * @return RequestBuilder
     */
    public function addQueryString($key, $value, $onetime = false)
    {
        if ($onetime)
        {
            $this->request->addOnetimeQueryString($key, $value);
        }
        else
        {
            $this->request->addQueryString($key, $value);
        }
        return $this;
    }

    /**
     *
     * @param string $key
     * @param bool $onetime if true query_string removed from onetime query_strings
     * @return RequestBuilder
     */
    public function removeQueryString($key, $onetime = false)
    {
        if ($onetime)
        {
            $this->request->removeOnetimeQueryString($key);
        }
        else
        {
            $this->request->removeQueryString($key);
        }
        return $this;
    }

    /**
     * @param string $method
     * @return RequestBuilder
     */
    public function setMethod($method)
    {
        if (!defined('\Lessmore92\ApiConsumer\Contracts\RequestModelInterface::REQUEST_METHOD_' . strtoupper($method)))
        {
            throw new RequestMethodNotSupported(sprintf('Request method %s not supported.', $method));
        }
        $this->request->method = $method;
        return $this;
    }
}
