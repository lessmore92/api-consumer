<?php
/**
 * User: Lessmore92
 * Date: 12/12/2019
 * Time: 11:47 PM
 */

namespace Lessmore92\ApiConsumer;

use Lessmore92\ApiConsumer\Builders\ApiBuilder;
use Lessmore92\ApiConsumer\Builders\RequestBuilder;
use Lessmore92\ApiConsumer\Builders\ResponseBuilder;
use Lessmore92\ApiConsumer\Contracts\HttpClientInterface;
use Lessmore92\ApiConsumer\Exceptions\ConfigApiNotReturnApiBuilder;
use Lessmore92\ApiConsumer\Foundation\RequestDirector;
use Lessmore92\ApiConsumer\HttpClients\Curl;
use Lessmore92\ApiConsumer\Models\Api;

abstract class ApiConsumer
{
    /**
     * @var Api
     */
    protected $api;
    /**
     * @var HttpClientInterface
     */
    protected $http;
    /**
     * @var RequestBuilder
     */
    protected $request_builder;
    /**
     * @var ResponseBuilder
     */
    protected $response_builder;

    /**
     * @var RequestDirector
     */
    private $request_director;

    /**
     * ApiConsumer constructor.
     * @param HttpClientInterface|null $httpClient
     */
    public function __construct(HttpClientInterface $httpClient = null)
    {
        if ($httpClient === null)
        {
            $httpClient = new Curl();
        }
        $this->http             = $httpClient;
        $this->request_builder  = new RequestBuilder();
        $this->response_builder = new ResponseBuilder();

        $api = $this->ConfigApi();

        if (!($api instanceof ApiBuilder))
        {
            throw new ConfigApiNotReturnApiBuilder('ConfigApi() must return an instance of \'Lessmore92\ApiConsumer\Builders\ApiBuilder\'');
        }

        $this->api = $api->getApi();

        $this->request_builder->setApi($this->api);

        $this->request_director = new RequestDirector($this->request_builder, $this->response_builder, $httpClient);
    }

    /**
     * @return ApiBuilder
     */
    abstract protected function ConfigApi();

    /**
     * @return RequestDirector
     */
    public function Request()
    {
        return $this->request_director;
    }
}
