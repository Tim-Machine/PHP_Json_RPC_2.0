
#DO NOT USE IS NOT PRODUCTION READY

#Router
the router allows you to map a string to a method inside of the class. The @ symbol is used to separate the class from the method name. It really doesn't do much on its own, but is a requirement for the Listener
```php
$router = new \Timmachine\PhpJsonRpc\Router();
$router->add("math.subtract", 'BaseController@subtract');
$router->add("math.add", 'BaseController@add');

```


#Listener
The listener is the brains of the operation. This bad boy will take your Json request twist it around and execute the method you want to call and then return the data to you in a properly formatted JsonRPC 2.0 format
```php

    $json = '{"jsonrpc": "2.0", "method": "subtract", "params": [42, 23], "id": 1}';

    $listener = new \Timmachine\PhpJsonRpc\Listener($routes);

    try {

        //validate our json
        $listener->validateJson($json);

        // process the method request
        $listener->processRequest();

        // return our json response
        return $listener->getResponse();
    } catch (\Timmachine\PhpJsonRpc\RpcExceptions $e) {

        // even if there is an error you send a response back to your client that is properly formatted
        return $listener->getResponse();
    }

```

#New JsonRPC versions?
lets make sure that we are forward thinking a little
```php
    $listener = new \Timmachine\PhpJsonRpc\Listener($routes,'2.1');
```

##Custom Requirements ?
Maybe your application has some custom requirements ?
```php

$customRequirements = [
    [
        'key'          => 'myCustomKey',
        'value'        => null //no defined required value,
        'errorMessage' => 'my custom error message',
        'errorCode'    => -32600 // Invalid params
    ],
    [
        'key'          => 'myCustomKey2',
        'value'        => '1237485' //defined required value,
        'errorMessage' => 'myCustomKey2 is not set correctly or missing',
        'errorCode'    => -32600 // Invalid params
    ]
];

$mySpec = '3.0'

$listener = new \Timmachine\PhpJsonRpc\Listener($routes,$mySpec, $customRequirements);


```
