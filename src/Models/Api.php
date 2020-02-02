<?php
/**
 * User: Lessmore92
 * Date: 12/13/2019
 * Time: 12:44 AM
 */

namespace Lessmore92\ApiConsumer\Models;

use Lessmore92\ApiConsumer\Foundation\Model;

/**
 * Class Api
 * @package Lessmore92\ApiConsumer\Models
 * @property string base_url
 * @property string api_key
 * @property string api_key_param_name
 * @property string api_key_place
 */
class Api extends Model
{
    const API_KEY_IN_HEADER       = 'header';
    const API_KEY_IN_QUERY_STRING = 'query_string';
}
