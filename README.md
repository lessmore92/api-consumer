# api-consumer
Build REST API consumer (client) easier than ever

### Installing

Easily install it through Composer:

```
composer require lessmore92/api-consumer
```

### Support

```
PHP >=5.5
```

## Usage

Easily extends your class from ```ApiConsumer``` and impalements ```ConfigApi``` method, and builds your awesome API Client.

## Example:
#### Below is the minimum requirement to start building API client: **_ConfigApi_**
```
use Lessmore92\ApiConsumer\ApiConsumer;
use Lessmore92\ApiConsumer\Builders\ApiBuilder;

class MyApi extends ApiConsumer
{
    /**
     * @return ApiBuilder
     */
    protected function ConfigApi()
    {
        $api = new ApiBuilder();
        $api->setHeaderApiKey('API-TOKEN','X-API-Key');
        $api->setBaseUrl('https://MY-API-BASE-URL.COM/');
        return $api;
    }
}
```
If the api key needs to be placed in the query string use  `setQueryApiKey` instead of `setHeaderApiKey`
for example `$api->setQueryApiKey('API-TOKEN','TOKEN');`
___

### The Magic of `$this->Request()`
By inheriting ```ApiConsumer``` your class will be able to utilize ```$this->Request()``` method, which supports chaining.
With ```$this->Request()``` you will be able to access all the features and functionalities to make your request.


#### Define your first method to receive data from api: **_Simple GET Request_**
To specify an endpoint to be called, you must use `->Endpoint()` method. After that by chaining `->Get()` method at the end, REQUEST METHOD is specified as `GET`.

```
use Lessmore92\ApiConsumer\ApiConsumer;
use Lessmore92\ApiConsumer\Builders\ApiBuilder;

class MyApi extends ApiConsumer
{
    /**
     * @return ApiBuilder
     */
    protected function ConfigApi()
    {
        $api = new ApiBuilder();
        $api->setHeaderApiKey('API-TOKEN','X-API-Key');
        $api->setBaseUrl('https://MY-API-BASE-URL.COM/');
        return $api;
    }

    public function Users()
    {
        $users = $this->Request()
                      ->Endpoint('users')
                      ->Get()
        ;

        return $users->body;
    }
}
```
In the above example we defined a method to `GET` `Users` list from server.

_By calling `Users()` method, in fact we are getting `https://MY-API-BASE-URL.COM/users`_
___

#### Make another request: Add **_Query String_**

To pass data in query string (e.g to search, order or filter) you can use `->AddQueryString()` method.

```
public function SearchUsers($search)
{
    $users = $this->Request()
                  ->Endpoint('users')
                  ->AddQueryString('search', $search)
                  ->Get()
    ;

    return $users->json_body;
}
```
In the above example we defined a method to search in users.

_By calling `SearchUsers('alex')` method, in fact we are getting `https://MY-API-BASE-URL.COM/users?search=alex`_

---

#### Make another request: Get result in **_json_** format
To receive data as `json`, you must use `->AcceptJson()` method.

```
public function SearchUsers($search)
{
    $users = $this->Request()
                  ->Endpoint('users')
                  ->AddQueryString('search', $search)
                  ->Get()
    ;

    return $users->json_body;
}
```

As you can see in the code above, by chaining `->AcceptJson()` in request we are telling to api server that we accept `json`, then in the `return` line we are returning a json formatted search result.

_For `json` data format, your api server must be able to provide json formatted response and support HEADER `'accept : application/json'`_
