# Json RPC server
Built according to json rpc standard 2.0 described in https://www.jsonrpc.org/specification
with batch requests support

## Requirements
- PHP >= 8.0
- ext-json
- any implementation of PSR-7 RequestInterface, ResponseInterface and ResponseFactoryInterface

## Installation
``composer install makedo/json-rpc``

## Usage

### Basic Example
```php
use Makedo\JsonRpc\Handler\Request\CallbackHandler;
use Makedo\JsonRpc\Request;
use Makedo\JsonRpc\Response;
use Makedo\JsonRpc\Handler\HandlerBuilder;

//Creating a json rpc request handler, implementation of RequestHandler
$requestHandler = new CallbackHandler(function (Request $request): Response {
    return Response\JsonRpcResponse::success('Hello world');
});

$responseFactory = new Psr7HttpResponseFactory();

//If set to true in case of error response will have stack trace and debug information.
//Set to true in development environment
$debug = false; 

$httpRequestHandler = (new HandlerBuilder())->buildHttpRequestHandler(
    $requestHandler,
    $responseFactory,
    $debug
);

$httpRequest = new Psr7HttpRequest(
'{"jsonrpc":"2.0", "method":"hello", "params": {"world": true}, "id": 1}'
);
$httpResponse = $httpRequestHandler->handle($httpRequest);

echo $httpResponse->getBody()->getContents();
//{"jsonrpc":"2.0", "result":"Hello world", "id": "1"}
```

### Error example with debug
```php
use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Handler\Request\CallbackHandler;
use Makedo\JsonRpc\Request;
use Makedo\JsonRpc\Response;
use Makedo\JsonRpc\Handler\HandlerBuilder;

$requestHandler = new CallbackHandler(function (Request $request): Response {
    throw JsonRpcError::methodNotFound('An error occurred');
});
$responseFactory = new Psr7HttpResponseFactory();
$debug = true; 

$httpRequestHandler = (new HandlerBuilder())->buildHttpRequestHandler(
    $requestHandler,
    $responseFactory,
    $debug
);

$httpRequest = new Psr7HttpRequest(
'{"jsonrpc":"2.0", "method":"hello", "params": {"world": true}, "id": 1}'
);
$httpResponse = $httpRequestHandler->handle($httpRequest);

echo $httpResponse->getBody()->getContents();
//{"jsonrpc":"2.0", "error":{"code":-32601, "message":"Method not found", "data": {"debug":{"debugMessage":"An error occurred", "previousMessage":null, "trace":[...]}}}, "id": "1"}
```

### Advanced Usage

#### Custom Json Encoder
```php
use Makedo\JsonRpc\Handler\HandlerBuilder;
use Makedo\JsonRpc\Json\DefaultEncoder;

//These params will be used in json_encode($options, $depth).
//Defaults are kept, only difference is JSON_THROW_ON_ERROR is always set.
$options = JSON_PRETTY_PRINT;
$depth = 1024;
$builder = (new HandlerBuilder())
    ->setJsonEncoder(new DefaultEncoder($options, $depth))
;
```

#### Custom Json Decoder
```php
use Makedo\JsonRpc\Handler\HandlerBuilder;
use Makedo\JsonRpc\Json\DefaultDecoder;

//Param $assoc is always set to true, so decoder should always decode to array

//These params will be used in json_decoe($options,true,$depth).
//Defaults are kept, only difference is JSON_THROW_ON_ERROR is always set.
$options = JSON_PRESERVE_ZERO_FRACTION;
$depth = 1024;
$builder = (new HandlerBuilder())
    ->setJsonDecoder(new DefaultDecoder($options, $depth))
;
```

#### Json rpc request extra validation and Invalid params error (Analogue of 401 in REST)
```php
use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Handler\HandlerBuilder;
use Makedo\JsonRpc\Request\Factory\JsonRpcRequestFactory;

class MyRequestFactory extends JsonRpcRequestFactory {
    protected function getValidParams(array $requestData) : array
    {
        $params = parent::getValidParams($requestData);
        if (!isset($params['param1'])) {
            throw JsonRpcError::invalidParams('Invalid param1', ['param1' => 'Not present']);
        }
        return $params;
    }
}
$builder = (new HandlerBuilder())
    ->setRequestFactory(new MyRequestFactory())
;
```
#### Json rpc response result serialization
```php
use Makedo\JsonRpc\Handler\HandlerBuilder;
use Makedo\JsonRpc\Response\Serializer\Result\ResultSerializer;

class MyResultSerializer implements ResultSerializer {
    public function serialize($result)
    {
        return [];   
    }
}
$builder = (new HandlerBuilder())
    ->setResultSerializer(new MyResultSerializer())
;
```

#### Customising error response data in debug mode
```php
use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Exception\Serializer\ErrorSerializer;
use Makedo\JsonRpc\Handler\HandlerBuilder;

class MyErrorSerializer implements ErrorSerializer {
    public function serialize(JsonRpcError $e): array
    {
        return [
            'traceAsString' => $e->getTraceAsString(),
        ];   
    }
}
$builder = (new HandlerBuilder())
    ->setErrorSerializer(new MyErrorSerializer())
;
```
#### Procedure handling Using PSR-11 container
```php
use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Handler\HandlerBuilder;
use Makedo\JsonRpc\Handler\Request\ContainerHandler;
use Makedo\JsonRpc\Handler\Request\RequestHandler;
use Makedo\JsonRpc\Request;
use Makedo\JsonRpc\Response;

class GetUserHandler implements RequestHandler {

    private $userRepository;

    public function __construct($userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(Request $request) : Response
    {
        $params = $request->getParams();
        $userId = $params['userId'] ?? null;
        if (!$userId) {
            throw JsonRpcError::invalidParams('No userId in request');
        }

        $user = $this->userRepository->findById($userId);
    
        return Response\JsonRpcResponse::success(['user' => $user]);
    }
}

$container = new Psr11Container();
$container->set('user.get', function() {
    return new GetUserHandler($userRepository);
});

$requestHandler = new ContainerHandler($container);
$builder = (new HandlerBuilder())->buildHttpRequestHandler(
    $requestHandler,
    new Psr7ResponseFactory()
);
```


### Plans and TODOS
- Async handling of batch requests

