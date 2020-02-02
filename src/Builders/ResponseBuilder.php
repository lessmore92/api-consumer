<?php
/**
 * User: Lessmore92
 * Date: 12/13/2019
 * Time: 3:21 PM
 */

namespace Lessmore92\ApiConsumer\Builders;


use Lessmore92\ApiConsumer\Contracts\AbstractResponseBuilder;
use Lessmore92\ApiConsumer\Exceptions\BadResponseException;
use Lessmore92\ApiConsumer\Exceptions\ClientException;
use Lessmore92\ApiConsumer\Exceptions\ServerException;
use Lessmore92\ApiConsumer\Foundation\StatusCodes;
use Lessmore92\ApiConsumer\Models\RawResponse;
use Lessmore92\ApiConsumer\Models\Response;

class ResponseBuilder extends AbstractResponseBuilder
{
    /**
     * @var Response
     */
    private $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    /**
     * @param RawResponse $raw
     * @return Response
     * @throws BadResponseException
     * @throws ClientException
     * @throws ServerException
     */
    public function fromRawResponse(RawResponse $raw)
    {
        $header_size    = $raw->info['header_size'];
        $_header        = explode("\n", trim(mb_substr($raw->response, 0, $header_size)));
        $status_line    = array_shift($_header);
        $body           = trim(mb_substr($raw->response, $header_size));
        $status_code    = $raw->info['http_code'];
        $status_message = $this->get_http_status_message($status_line);

        $headers = array();
        foreach ($_header as $line)
        {
            //TODO handle redirected request headers
            list($key, $val) = explode(':', $line, 2);
            $headers[strtolower($key)] = trim($val);
        }

        if ($this->is_server_error($status_code))
        {
            throw new ServerException($this->build_exception_message($status_code, $status_message));
        }

        if ($this->is_client_error($status_code))
        {
            $exception = new ClientException($this->build_exception_message($status_code, $status_message));
            $exception->setBody($body);
            throw $exception;
        }

        if (!$this->is_success($status_code))
        {
            throw new BadResponseException($this->build_exception_message($status_code, $status_message));
        }

        $response = new Response();

        $response->body           = $body;
        $response->headers        = $headers;
        $response->status_code    = $status_code;
        $response->status_message = $status_message;


        if (isset($headers['content-type']))
        {
            $content_type = $headers['content-type'];
            $is_json      = stripos($content_type, 'json') !== false;
            if ($is_json)
            {
                $response->json_body = @json_decode($body, true);
            }
        }

        return $response;
    }

    /**
     * @param string $status_line
     * @return string
     */
    private function get_http_status_message($status_line)
    {
        $re = '/HTTP\/[0-9\.]+ ([0-9]+) ([0-9a-z ]+)?/mi';

        preg_match_all($re, $status_line, $matches, PREG_SET_ORDER, 0);

        return (is_array($matches) && count($matches) > 0 && count($matches[0]) > 2) ? $matches[0][2] : '';
    }

    private function is_server_error($status_code)
    {
        return $status_code >= 500 && $status_code <= 599;
    }

    private function build_exception_message($code, $message = '')
    {
        if (trim($message) === '')
        {
            $message = StatusCodes::getMessageForCode($code);
        }
        return sprintf('%s %s', $code, $message);
    }

    private function is_client_error($status_code)
    {
        return $status_code >= 400 && $status_code <= 499;
    }

    private function is_success($status_code)
    {
        return $status_code >= 200 && $status_code <= 299;
    }

    /**
     * @return response
     */
    public function buildResponse()
    {
        return $this->response;
    }
}
