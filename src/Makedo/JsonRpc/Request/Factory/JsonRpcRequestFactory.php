<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Request\Factory;

use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Request;
use Makedo\JsonRpc\Request\JsonRpcRequest;
use Makedo\JsonRpc\Version;

class JsonRpcRequestFactory implements RequestFactory
{
    private const KEY_VERSION = 'jsonrpc';
    private const KEY_METHOD  = 'method';
    private const KEY_PARAMS  = 'params';
    private const KEY_ID      = 'id';

    private const KEYS = [
        self::KEY_VERSION,
        self::KEY_METHOD,
        self::KEY_PARAMS,
        self::KEY_ID
    ];

    public function createRequest(array $requestData): Request
    {
        $this->validateKeys($requestData);

        return new JsonRpcRequest(
            $this->getValidVersion($requestData),
            $this->getValidMethod($requestData),
            $this->getValidParams($requestData),
            $this->getValidId($requestData),
        );
    }

    /**
     * @param array $requestData
     * @throws JsonRpcError
     */
    protected function validateKeys(array $requestData): void
    {
        $requestKeys = array_keys($requestData);
        $invalidKeys = array_diff($requestKeys, self::KEYS);

        if (!empty($invalidKeys)) {
            throw JsonRpcError::invalidRequest(sprintf(
                'Json request contains invalid keys: %s',
                join(',', $invalidKeys)
            ));
        }
    }

    /**
     * @param array $requestData
     * @return string
     * @throws JsonRpcError
     */
    protected function getValidVersion(array $requestData): string
    {
        if (!array_key_exists(self::KEY_VERSION, $requestData)) {
            throw JsonRpcError::invalidRequest(sprintf(
                'Invalid %s key', self::KEY_VERSION
            ));
        }

        if ($requestData[self::KEY_VERSION] === Version::number()) {
            return $requestData[self::KEY_VERSION];
        }

        throw JsonRpcError::invalidRequest(sprintf(
            'Invalid %s key. Value should be %s',
            self::KEY_VERSION,
            Version::number(),
        ));
    }

    /**
     * @param array $requestData
     * @return string
     * @throws JsonRpcError
     */
    protected function getValidMethod(array $requestData): string
    {
        if (!array_key_exists(self::KEY_METHOD, $requestData)) {
            throw JsonRpcError::invalidRequest(sprintf(
                'Key %s is required',
                self::KEY_METHOD,
            ));
        }

        $method = $requestData[self::KEY_METHOD];
        if (!empty($method) && is_string($method)) {
            return $method;
        }

        throw JsonRpcError::invalidRequest(sprintf(
            'Key %s should be a non-empty string',
            self::KEY_METHOD,
        ));
    }

    /**
     * @param array $requestData
     * @return array
     * @throws JsonRpcError
     */
    protected function getValidParams(array $requestData): array
    {
        if (!array_key_exists(self::KEY_PARAMS, $requestData)) {
            return [];
        }

        $params = $requestData[self::KEY_PARAMS];
        if (is_array($params)) {
            return $params;
        }

        throw JsonRpcError::invalidRequest(sprintf(
            'Key %s should be an associative or non-associative array',
            self::KEY_PARAMS,
        ));
    }

    /**
     * @param array $requestData
     * @return int|string|null
     * @throws JsonRpcError
     */
    protected function getValidId(array $requestData): string|int|null
    {
        if (!isset($requestData[self::KEY_ID])) {
           return null;
        }

        $id = $requestData[self::KEY_ID];

        if (is_int($id)) {
            return $id;
        }

        if (is_string($id)) {
            return $id;
        }

        throw JsonRpcError::invalidRequest(sprintf(
            'Key %s  MUST contain a String, Number without fractional parts, or NULL value if included',
            self::KEY_ID,
        ));
    }
}
