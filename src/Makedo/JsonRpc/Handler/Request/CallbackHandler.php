<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Handler\Request;

use Makedo\JsonRpc\Handler\Request\RequestHandler;
use Makedo\JsonRpc\Request;
use Makedo\JsonRpc\Response;

class CallbackHandler implements RequestHandler
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request): Response
    {
        return call_user_func($this->callback, $request);
    }
}
