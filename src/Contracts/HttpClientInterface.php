<?php
/**
 * User: Lessmore92
 * Date: 12/13/2019
 * Time: 2:09 PM
 */

namespace Lessmore92\ApiConsumer\Contracts;


use Lessmore92\ApiConsumer\Models\RawResponse;
use Lessmore92\ApiConsumer\Models\Request;

interface HttpClientInterface
{
    /**
     * @param Request $request
     * @return RawResponse
     */
    public function request($request);

    /**
     * @param array $options
     * @return self
     */
    public function setOptions(array $options);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addOption($key, $value);

    /**
     * @param string $key
     * @return void
     */
    public function removeOption($key);

    /**
     * @return void
     */
    public function clearOptions();
}
