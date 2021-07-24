<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Handler\Request;

use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Request;
use Makedo\JsonRpc\Response;

interface RequestHandler
{
    /**
     * @param Request $request
     * @return Response
     * @throws JsonRpcError
     */
    public function handle(Request $request): Response;
}
