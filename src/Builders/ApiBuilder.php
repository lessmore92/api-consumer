<?php
/**
 * User: Lessmore92
 * Date: 12/13/2019
 * Time: 3:21 PM
 */

namespace Lessmore92\ApiConsumer\Builders;

use Lessmore92\ApiConsumer\Contracts\AbstractApiBuilder;
use Lessmore92\ApiConsumer\Exceptions\ApiKeyParamNameMustBeDefine;
use Lessmore92\ApiConsumer\Models\Api;

class ApiBuilder extends AbstractApiBuilder
{
    private $api;

    public function __construct()
    {
        $this->api = new Api();
    }

    /**
     * @return Api
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @param string $api_key
     * @param string $api_key_param_name
     * @return $this
     */
    public function setHeaderApiKey($api_key, $api_key_param_name = 'x-api-key')
    {
        return $this->setApiKey($api_key, $api_key_param_name, 'header');
    }

    private function setApiKey($api_key, $api_key_param_name = 'x-api-key', $api_key_place = 'header')
    {
        if (trim($api_key_param_name) === '')
        {
            throw new ApiKeyParamNameMustBeDefine('api_key_param_name must be defined, empty string not allowed');
        }
        $this->api->api_key_place      = $api_key_place;
        $this->api->api_key_param_name = $api_key_param_name;
        $this->api->api_key            = $api_key;
        return $this;
    }

    /**
     * @param string $api_key
     * @param string $api_key_param_name
     * @return $this
     */
    public function setQueryApiKey($api_key, $api_key_param_name = 'api_key')
    {
        return $this->setApiKey($api_key, $api_key_param_name, 'query_string');
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setBaseUrl($url)
    {
        $this->api->base_url = $url;
        return $this;
    }
}
