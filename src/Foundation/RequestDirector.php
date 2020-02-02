<?php
/**
 * User: Lessmore92
 * Date: 12/22/2019
 * Time: 11:43 PM
 */

namespace Lessmore92\ApiConsumer\Foundation;


use Lessmore92\ApiConsumer\Builders\RequestBuilder;
use Lessmore92\ApiConsumer\Builders\ResponseBuilder;
use Lessmore92\ApiConsumer\Contracts\HttpClientInterface;
use Lessmore92\ApiConsumer\Contracts\RequestModelInterface;
use Lessmore92\ApiConsumer\Exceptions\BadResponseException;
use Lessmore92\ApiConsumer\Exceptions\ClientException;
use Lessmore92\ApiConsumer\Exceptions\ServerException;
use Lessmore92\ApiConsumer\Models\Response;

class RequestDirector
{
    /**
     * @var RequestBuilder
     */
    private $request_builder;


    /**
     * @var ResponseBuilder
     */
    private $response_builder;

    /**
     * @var HttpClientInterface
     */
    private $http;

    public function __construct(RequestBuilder $requestBuilder, ResponseBuilder $responseBuilder, HttpClientInterface $httpClient)
    {
        $this->request_builder  = $requestBuilder;
        $this->response_builder = $responseBuilder;
        $this->http             = $httpClient;
    }

    /**
     * @return Response
     * @throws BadResponseException
     * @throws ClientException
     * @throws ServerException
     */
    public function Get()
    {
        $this->request_builder->setMethod(RequestModelInterface::REQUEST_METHOD_GET);
        $raw_response = $this->http->Request($this->request_builder->buildRequest());
        $response     = $this->response_builder->fromRawResponse($raw_response);
        return $response;
    }

    /**
     * @return Response
     * @throws BadResponseException
     * @throws ClientException
     * @throws ServerException
     */
    public function Post()
    {
        $this->request_builder->setMethod(RequestModelInterface::REQUEST_METHOD_POST);
        $raw_response = $this->http->Request($this->request_builder->buildRequest());
        $response     = $this->response_builder->fromRawResponse($raw_response);
        return $response;
    }

    /**
     * @return Response
     * @throws BadResponseException
     * @throws ClientException
     * @throws ServerException
     */
    public function Patch()
    {
        $this->request_builder->setMethod(RequestModelInterface::REQUEST_METHOD_PATCH);
        $raw_response = $this->http->Request($this->request_builder->buildRequest());
        $response     = $this->response_builder->fromRawResponse($raw_response);
        return $response;
    }


    /**
     * @return Response
     * @throws BadResponseException
     * @throws ClientException
     * @throws ServerException
     */
    public function Put()
    {
        $this->request_builder->setMethod(RequestModelInterface::REQUEST_METHOD_PUT);
        $raw_response = $this->http->Request($this->request_builder->buildRequest());
        $response     = $this->response_builder->fromRawResponse($raw_response);
        return $response;
    }

    /**
     * @return Response
     * @throws BadResponseException
     * @throws ClientException
     * @throws ServerException
     */
    public function Delete()
    {
        $this->request_builder->setMethod(RequestModelInterface::REQUEST_METHOD_DELETE);
        $raw_response = $this->http->Request($this->request_builder->buildRequest());
        $response     = $this->response_builder->fromRawResponse($raw_response);
        return $response;
    }

    /**
     * @param array $body
     * @return $this
     */
    public function JsonBody($body)
    {
        $this->ContentTypeJson(true);
        $this->request_builder->setJsonBody($body);
        return $this;
    }

    /**
     * @param bool $onetime
     * @return RequestDirector
     */
    public function ContentTypeJson($onetime = false)
    {
        $this->request_builder->addHeader('content-type', 'application/json', $onetime);
        return $this;
    }

    /**
     * @param string $endpoint
     * @return $this
     */
    public function Endpoint($endpoint)
    {
        $this->request_builder->endPoint($endpoint);
        return $this;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function Body($body)
    {
        $this->request_builder->setBody($body);
        return $this;
    }

    /**
     * Set Http Client Options
     * @param array $options
     * @return $this
     */
    public function SetHttpOptions(array $options)
    {
        $this->http->setOptions($options);
        return $this;
    }

    /**
     * @param string $user_agent
     * @return $this
     */
    public function SetUserAgent($user_agent)
    {
        $this->http->addOption(CURLOPT_USERAGENT, $user_agent);
        return $this;
    }

    /**
     * @param string $user_name
     * @param string $password
     * @return $this
     */
    public function BasicAuth($user_name, $password)
    {
        $this->http->addOption(CURLOPT_USERPWD, $user_name . ':' . $password);
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param bool $onetime
     * @return RequestDirector
     */
    public function AddQueryString($key, $value, $onetime = false)
    {
        $this->request_builder->addQueryString($key, $value, $onetime);
        return $this;
    }

    /**
     * @param string $key
     * @param bool $onetime
     * @return RequestDirector
     */
    public function RemoveQueryString($key, $onetime = false)
    {
        $this->request_builder->removeQueryString($key, $onetime);
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param bool $onetime
     * @return RequestDirector
     */
    public function AddHeader($key, $value, $onetime = false)
    {
        $this->request_builder->addHeader($key, $value, $onetime);
        return $this;
    }

    /**
     * @param string $key
     * @param bool $onetime
     * @return RequestDirector
     */
    public function RemoveHeader($key, $onetime = false)
    {
        $this->request_builder->removeHeader($key, $onetime);
        return $this;
    }

    /**
     * @param $accept
     * @param bool $onetime
     * @return RequestDirector
     */
    public function AcceptHeader($accept, $onetime = false)
    {
        $this->request_builder->addHeader('accept', $accept, $onetime);
        return $this;
    }

    /**
     * @param bool $onetime
     * @return RequestDirector
     */
    public function AcceptJson($onetime = false)
    {
        $this->request_builder->addHeader('accept', 'application/json', $onetime);
        return $this;
    }
}
