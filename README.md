
#example
```php


 $routes = new \Timmachine\PhpJsonRpc\Router();

    $routes->add("subtract", 'BaseController@subtract');

    $listener = new \Timmachine\PhpJsonRpc\Listener($routes);

    $json = '{"jsonrpc": "2.0", "method": "subtract", "params": [42, 23], "id": 1}';

    try {
        $listener->validateJson($json);
        $listener->processRequest();
    } catch (\Timmachine\PhpJsonRpc\RpcExceptions $e) {

    }

```