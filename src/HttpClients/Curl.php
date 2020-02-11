<?php
/**
 * User: Lessmore92
 * Date: 12/13/2019
 * Time: 4:16 PM
 * @noinspection CurlSslServerSpoofingInspection
 */

namespace Lessmore92\ApiConsumer\HttpClients;


use Lessmore92\ApiConsumer\Contracts\HttpClientInterface;
use Lessmore92\ApiConsumer\Contracts\RequestModelInterface;
use Lessmore92\ApiConsumer\Exceptions\RequestException;
use Lessmore92\ApiConsumer\Models\RawResponse;
use Lessmore92\ApiConsumer\Models\Request;

class Curl implements HttpClientInterface
{
    /**
     * @var resource
     */
    private $handler;
    private $ssl_verify = true;
    private $options    = [];

    public function __construct()
    {
        $this->handler = curl_init();
    }

    /**
     * @param Request $request
     * @return RawResponse
     */
    public function request($request)
    {
        $options = $this->prepareRequest($request);

        curl_setopt_array($this->handler, $options);

        $_response = curl_exec($this->handler);
        $_error_no = curl_errno($this->handler);
        $_error    = curl_error($this->handler);
        $_info     = curl_getinfo($this->handler);

        //TODO if debug return this
        //var_dump($options, $_info);

        if ($_error_no)
        {
            throw new RequestException($_error);
        }

        $raw_response = new RawResponse([
            'response' => $_response,
            'info'     => $_info,
            'error'    => $_error,
        ]);

        return $raw_response;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function prepareRequest($request)
    {
        $_options = (array)$this->getCurlOptions();

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => strtoupper($request->method),
            CURLOPT_HTTPHEADER     => $request->getHeaders(),
            CURLOPT_HEADER         => true,
            CURLOPT_URL            => $request->getFullUrl(),

            CURLOPT_FOLLOWLOCATION => true, // Follow Redirects If 302
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_ENCODING       => '',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT => 15,   // Time out for a single connection
            CURLOPT_TIMEOUT        => 30,   // Curl Process Timeout
            CURLOPT_MAXREDIRS      => 10,    // Max Redirects Allowed
        ];

        if (
            $request->method === RequestModelInterface::REQUEST_METHOD_POST
            || $request->method === RequestModelInterface::REQUEST_METHOD_PUT
            || $request->method === RequestModelInterface::REQUEST_METHOD_PATCH
        )
        {
            $post_fields = $request->body;
            if ($request->is_json)
            {
                $post_fields = json_encode($request->json_body);
            }
            $options[CURLOPT_POSTFIELDS] = $post_fields;
        }

        return $_options + $options;
    }

    /**
     * @return array
     */
    private function getCurlOptions()
    {
        $options = $this->getOptions();

        //prevent to change the necessary options
        foreach ([
                     CURLOPT_RETURNTRANSFER,
                     CURLOPT_CUSTOMREQUEST,
                     CURLOPT_HTTPHEADER,
                     CURLOPT_HEADER,
                     CURLOPT_URL,
                 ] as $item)
        {
            if (isset($options[$item]))
            {
                unset($options[$item]);
            }
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * @param string $key
     * @return void
     */
    public function removeOption($key)
    {
        if (isset($this->options[$key]))
        {
            unset($this->options[$key]);
        }
    }

    /**
     * @return void
     */
    public function clearOptions()
    {
        $this->options = [];
    }
}
