<?php
/**
 * User: Lessmore92
 * Date: 12/12/2019
 * Time: 11:49 PM
 */

namespace Lessmore92\ApiConsumer\Foundation;

use Lessmore92\ApiConsumer\Contracts\RequestModelInterface;

/**
 * Class Request
 * @package Lessmore92\ApiConsumer\Models
 * @property string method
 * @property string url
 * @property string path
 * @property string body
 * @property array json_body
 * @property bool is_json
 * @property array query_string
 * @property array onetime_query_string
 * @property array headers
 * @property array onetime_headers
 */
abstract class RequestModel extends Model
{
    private $cached_headers = [];

    /**
     * @param string $key
     * @param string $value
     * @return RequestModel
     */
    public function addHeader($key, $value)
    {
        $key           = strtolower($key);
        $headers       = (array)$this->headers;
        $headers[$key] = $value;
        $this->headers = $headers;
        $this->clear_cached_headers();
        return $this;
    }

    private function clear_cached_headers()
    {
        $this->cached_headers = [];
    }

    /**
     * @param string $key
     * @return RequestModel
     */
    public function removeHeader($key)
    {
        $key     = strtolower($key);
        $headers = (array)$this->headers;
        if (isset($headers[$key]))
        {
            unset($headers[$key]);
            $this->headers = $headers;
        }
        $this->clear_cached_headers();
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return RequestModel
     */
    public function addOnetimeHeader($key, $value)
    {
        $key                   = strtolower($key);
        $headers               = (array)$this->onetime_headers;
        $headers[$key]         = $value;
        $this->onetime_headers = $headers;
        $this->clear_cached_headers();
        return $this;
    }

    /**
     * @param string $key
     * @return RequestModel
     */
    public function removeOnetimeHeader($key)
    {
        $key     = strtolower($key);
        $headers = (array)$this->onetime_headers;
        if (isset($headers[$key]))
        {
            unset($headers[$key]);
            $this->onetime_headers = $headers;
        }
        $this->clear_cached_headers();
        return $this;
    }

    /**
     * @return RequestModel
     */
    public function clearOnetimeHeaders()
    {
        $this->onetime_headers = [];
        $this->clear_cached_headers();
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return RequestModel
     */
    public function addQueryString($key, $value)
    {
        $queries            = (array)$this->query_string;
        $queries[$key]      = $value;
        $this->query_string = $queries;
        return $this;
    }

    /**
     * @param string $key
     * @return RequestModel
     */
    public function removeQueryString($key)
    {
        $queries = (array)$this->query_string;
        if (isset($queries[$key]))
        {
            unset($queries[$key]);
            $this->query_string = $queries;
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return RequestModel
     */
    public function addOnetimeQueryString($key, $value)
    {
        $queries                    = (array)$this->onetime_query_string;
        $queries[$key]              = $value;
        $this->onetime_query_string = $queries;
        return $this;
    }

    /**
     * @param string $key
     * @return RequestModel
     */
    public function removeOnetimeQueryString($key)
    {
        $queries = (array)$this->onetime_query_string;
        if (isset($queries[$key]))
        {
            unset($queries[$key]);
            $this->onetime_query_string = $queries;
        }
        return $this;
    }

    /**
     * @return RequestModel
     */
    public function clearOnetimeQueryStrings()
    {
        $this->onetime_query_string = [];
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        if (count($this->cached_headers) > 0)
        {
            return $this->cached_headers;
        }

        $headers = [];
        foreach ((array)$this->headers as $key => $header)
        {
            $headers[] = $this->prepare_header_item($key, $header);
        }
        foreach ((array)$this->onetime_headers as $key => $header)
        {
            $headers[] = $this->prepare_header_item($key, $header);
        }
        $this->set_cached_headers($headers);
        return $headers;
    }

    /**
     * @param $key
     * @param $value
     * @return string
     */
    private function prepare_header_item($key, $value)
    {
        $key = strtolower($key);
        return "{$key}: {$value}";
    }

    private function set_cached_headers(array $headers)
    {
        $this->cached_headers = $headers;
    }

    /**
     * @return string
     */
    public function getFullUrl()
    {
        $url          = $this->strip_trailing_slash($this->url) . '/' . $this->path;
        $query_string = $this->getQueryString();
        if (trim($query_string))
        {
            $url = strpos($url, '?') !== false ? $url . '&' . $query_string : $url . '?' . $query_string;
        }
        return $url;
    }

    private function strip_trailing_slash($url)
    {
        return rtrim($url, '/');
    }

    /**
     * @return string
     */
    public function getQueryString()
    {
        return http_build_query(array_merge((array)$this->onetime_query_string, (array)$this->query_string));
    }

    public function getMethod($value)
    {
        if (trim($value) === '')
        {
            return RequestModelInterface::REQUEST_METHOD_GET;
        }

        return $value;
    }
}
