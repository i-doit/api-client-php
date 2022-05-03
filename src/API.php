<?php

/**
 * Copyright (C) 2016-2020 Benjamin Heisig
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Benjamin Heisig <https://benjamin.heisig.name/>
 * @copyright Copyright (C) 2016-2020 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi;

use CurlHandle;
use \Exception;
use \BadMethodCallException;
use \DomainException;
use \RuntimeException;

/**
 * API client
 */
class API {

    /**
     * Configuration: URL
     */
    const URL = 'url';

    /**
     * Configuration: Port
     */
    const PORT = 'port';

    /**
     * Lowest allowed port number:
     */
    const PORT_MIN = 1;

    /**
     * Highest allowed port number
     */
    const PORT_MAX = 65535;

    /**
     * Configuration: API key
     */
    const KEY = 'key';

    /**
     * Configuration: Username
     */
    const USERNAME = 'username';

    /**
     * Configuration: Password
     */
    const PASSWORD = 'password';

    /**
     * Configuration: Language
     */
    const LANGUAGE = 'language';

    /**
     * Configuration: Proxy settings
     */
    const PROXY = 'proxy';

    /**
     * Configuration: Activate proxy settings?
     */
    const PROXY_ACTIVE = 'active';

    /**
     * Configuration: Proxy type
     */
    const PROXY_TYPE = 'type';

    /**
     * HTTP proxy
     */
    const PROXY_TYPE_HTTP = 'HTTP';

    /**
     * SOCKS5 proxy
     */
    const PROXY_TYPE_SOCKS5 = 'SOCKS5';

    /**
     * Configuration: Proxy host
     */
    const PROXY_HOST = 'host';

    /**
     * Configuration: Proxy port
     */
    const PROXY_PORT = 'port';

    /**
     * Configuration: Proxy username
     */
    const PROXY_USERNAME = 'username';

    /**
     * Configuration: Proxy password
     */
    const PROXY_PASSWORD = 'password';

    /**
     * Configuration: Disable security-related cURL options
     */
    const BYPASS_SECURE_CONNECTION = 'bypassSecureConnection';

    /**
     * Configuration
     *
     * @var array Associative array
     */
    protected $config = [];

    /**
     * cURL resource
     *
     * @var resource
     */
    protected $resource;

    /**
     * Information about last client request
     *
     * @var array Associative array
     */
    protected $lastInfo = [];

    /**
     * HTTP headers of last server response
     *
     * @var string Multi-line string
     */
    protected $lastResponseHeaders;

    /**
     * Response for last request
     *
     * @var array Associative array
     */
    protected $lastResponse;

    /**
     * Last request content
     *
     * @var array Multi-dimensional associative array
     */
    protected $lastRequestContent;

    /**
     * Current session identifier
     *
     * @var string|null
     */
    protected $session;

    /**
     * cURL options
     *
     * @var array Associative array
     */
    protected $options = [];

    /**
     * Counter for JSON-RPC request identifiers
     *
     * @var int
     */
    protected $id = 0;

    /**
     * Information about this project
     *
     * @var array
     */
    protected $composer = [];

    /**
     * Constructor
     *
     * @param array $config Associative multi-dimensional array
     *
     * @throws Exception on error
     */
    public function __construct(array $config) {
        $this->config = $config;

        $this->testConfig();

        $composerFile = __DIR__ . '/../composer.json';

        if (is_readable($composerFile)) {
            $composerFileContent = file_get_contents($composerFile);

            if ($composerFileContent !== false) {
                $this->composer = json_decode($composerFileContent, true);
            }
        }

        $this->setCURLOptions();
    }

    /**
     * Destructor automatically logouts and disconnects from API if necessary
     */
    public function __destruct() {
        try {
            if ($this->isLoggedIn()) {
                $this->logout();
            }

            if ($this->isConnected()) {
                $this->disconnect();
            }
        } catch (Exception $e) {
            // Do nothing because this is a destructor.
        }
    }

    /**
     * Is client connected to API?
     *
     * @return bool
     */
    public function isConnected(): bool {
        return is_resource($this->resource) || $this->resource instanceof CurlHandle;
    }

    /**
     * Is client logged-in to API?
     *
     * @return bool
     */
    public function isLoggedIn(): bool {
        return isset($this->session);
    }

    /**
     * Set cURL options
     *
     * @return self Returns itself
     */
    protected function setCURLOptions(): self {
        $this->options = [
            CURLOPT_FAILONERROR => true,
            // Follow (only) 301s and 302s:
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POSTREDIR => (1 | 2),
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HEADER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_PORT => $this->config[self::PORT],
            CURLOPT_REDIR_PROTOCOLS => (CURLPROTO_HTTP | CURLPROTO_HTTPS),
            CURLOPT_ENCODING => 'application/json',
            CURLOPT_URL => $this->config[self::URL],
            // In seconds:
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Expect:'
            ]
        ];

        if (array_key_exists('name', $this->composer) &&
            is_string($this->composer['name']) &&
            array_key_exists('extra', $this->composer) &&
            is_array($this->composer['extra']) &&
            array_key_exists('version', $this->composer['extra']) &&
            is_string($this->composer['extra']['version'])) {
            $this->options[CURLOPT_USERAGENT] = $this->composer['name'] . ' ' . $this->composer['extra']['version'];
        }

        if (isset($this->config[self::PROXY]) &&
            $this->config[self::PROXY][self::PROXY_ACTIVE] === true) {
            $this->options[CURLOPT_PROXY] = $this->config[self::PROXY][self::PROXY_HOST];
            $this->options[CURLOPT_PROXYPORT] = $this->config[self::PROXY][self::PROXY_PORT];

            if (isset($this->config[self::PROXY][self::PROXY_USERNAME]) &&
                is_string($this->config[self::PROXY][self::PROXY_USERNAME]) &&
                !empty($this->config[self::PROXY][self::PROXY_USERNAME])) {
                $this->options[CURLOPT_PROXYUSERPWD] = $this->config[self::PROXY][self::PROXY_USERNAME] .
                    ':' . $this->config[self::PROXY][self::PROXY_PASSWORD];
            }

            switch ($this->config[self::PROXY][self::PROXY_TYPE]) {
                case self::PROXY_TYPE_HTTP:
                    $this->options[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
                    break;
                case self::PROXY_TYPE_SOCKS5:
                    $this->options[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
                    break;
                default:
                    throw new DomainException(sprintf(
                        'Unknown proxy type "%s"',
                        $this->config[self::PROXY][self::PROXY_TYPE]
                    ));
            }
        }

        if ($this->config[self::BYPASS_SECURE_CONNECTION] === true) {
            $this->options[CURLOPT_SSL_VERIFYPEER] = false;
            $this->options[CURLOPT_SSL_VERIFYHOST] = 0;
            $this->options[CURLOPT_SSLVERSION] = CURL_SSLVERSION_DEFAULT;
        } else {
            $this->options[CURLOPT_SSL_VERIFYPEER] = true;
            $this->options[CURLOPT_SSL_VERIFYHOST] = 2;
            $this->options[CURLOPT_SSLVERSION] = CURL_SSLVERSION_TLSv1_2;
        }

        return $this;
    }

    /**
     * Connects to API
     *
     * This method is optional and may be used for re-connects.
     *
     * @return self Returns itself
     *
     * @throws RuntimeException on error
     */
    public function connect(): self {
        $this->resource = curl_init();

        if (!is_resource($this->resource) && !$this->resource instanceof CurlHandle) {
            throw new RuntimeException('Unable to initiate cURL session');
        }

        return $this;
    }

    /**
     * Disconnects from API
     *
     * This method is optional and may be used for reconnects.
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function disconnect(): self {
        // Auto-connect:
        if ($this->isConnected() === false) {
            throw new BadMethodCallException('There is no connection.');
        }

        curl_close($this->resource);

        return $this;
    }

    /**
     * Logins to API
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function login(): self {
        if ($this->isLoggedIn()) {
            throw new BadMethodCallException('Client is already logged-in.');
        }

        // Auto-connect:
        if ($this->isConnected() === false) {
            $this->connect();
        }

        $response = $this->request(
            'idoit.login'
        );

        if (!array_key_exists('session-id', $response)) {
            throw new RuntimeException('Failed to login because i-doit responded without a session ID');
        }

        $this->session = $response['session-id'];

        return $this;
    }

    /**
     * Logouts from API
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function logout(): self {
        if ($this->isLoggedIn() === false) {
            throw new BadMethodCallException('Client is not logged-in.');
        }

        $this->request(
            'idoit.logout'
        );

        $this->session = null;

        return $this;
    }

    /**
     * Generates new JSON-RPC request identifier
     *
     * @return int
     */
    protected function genID(): int {
        $this->id++;

        return $this->id;
    }

    /**
     * How many requests were already send?
     *
     * @return int Positive integer
     */
    public function countRequests(): int {
        return $this->id;
    }

    /**
     * Sends request to API
     *
     * @param string $method JSON-RPC method
     * @param array $params Optional parameters
     *
     * @return array Result of request
     *
     * @throws Exception on error
     */
    public function request(string $method, array $params = []): array {
        $data = [
            'version' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => $this->genID()
        ];

        $data['params']['apikey'] = $this->config[self::KEY];

        if (array_key_exists(self::LANGUAGE, $this->config) &&
            !array_key_exists(self::LANGUAGE, $params)) {
            $data['params'][self::LANGUAGE] = $this->config[self::LANGUAGE];
        }

        $response = $this->execute($data);

        $this->evaluateResponse($response);

        return $response['result'];
    }

    /**
     * Performs multiple requests to API at once
     *
     * @param array $requests Batch requests with 'method' and optional 'params'
     *
     * @return array Result of request
     *
     * @throws Exception on error
     */
    public function batchRequest(array $requests): array {
        $data = [];

        foreach ($requests as $request) {
            if (!array_key_exists('method', $request)) {
                throw new BadMethodCallException(
                    'Missing method in one of the sub-requests of this batch request'
                );
            }

            $params = [];

            if (array_key_exists('params', $request)) {
                $params = $request['params'];
            }

            $params['apikey'] = $this->config[self::KEY];

            if (array_key_exists(self::LANGUAGE, $this->config)) {
                $params[self::LANGUAGE] = $this->config[self::LANGUAGE];
            }

            $data[] = [
                'version' => '2.0',
                'method' => $request['method'],
                'params' => $params,
                'id' => $this->genID()
            ];
        }

        $responses = $this->execute($data);

        $results = [];

        foreach ($responses as $response) {
            if (!is_array($response)) {
                throw new RuntimeException(sprintf(
                    'Found invalid result for request in batch: %s',
                    $response
                ));
            }

            $this->evaluateResponse($response);

            $results[] = $response['result'];
        }

        return $results;
    }

    /**
     * Sends request to API with headers and receives response
     *
     * @param array $data Payload
     *
     * @return array Result of request
     *
     * @throws Exception on error
     */
    protected function execute(array $data): array {
        // Auto-connect:
        if ($this->isConnected() === false) {
            $this->connect();
        }

        $this->lastRequestContent = $data;

        $dataAsString = json_encode($data);

        $options = $this->options;

        $options[CURLOPT_POSTFIELDS] = $dataAsString;

        if (isset($this->session)) {
            $options[CURLOPT_HTTPHEADER][] = 'X-RPC-Auth-Session: ' . $this->session;
        } elseif (array_key_exists(self::USERNAME, $this->config) &&
            is_string($this->config[self::USERNAME]) &&
            !empty($this->config[self::USERNAME]) &&
            array_key_exists(self::PASSWORD, $this->config) &&
            is_string($this->config[self::PASSWORD]) &&
            !empty($this->config[self::PASSWORD])) {
            $options[CURLOPT_HTTPHEADER][] = 'X-RPC-Auth-Username: ' . $this->config[self::USERNAME];
            $options[CURLOPT_HTTPHEADER][] = 'X-RPC-Auth-Password: ' . $this->config[self::PASSWORD];
        }

        curl_setopt_array($this->resource, $options);

        $responseString = curl_exec($this->resource);

        $this->lastInfo = curl_getinfo($this->resource);

        if ($responseString === false) {
            switch ($this->lastInfo['http_code']) {
                case 0:
                    $errorMessage = curl_error($this->resource);
                    $errorCode = curl_errno($this->resource);
                    $exceptionMessage = 'Connection to Web server failed [%s]: %s';

                    if (strlen($errorMessage) === 0) {
                        throw new RuntimeException(sprintf(
                            $exceptionMessage,
                            $errorCode,
                            '-'
                        ));
                    }

                    throw new RuntimeException(sprintf(
                        $exceptionMessage,
                        $errorCode,
                        $errorMessage
                    ));
                default:
                    throw new RuntimeException(sprintf(
                        'Web server responded with HTTP status code %s',
                        $this->lastInfo['http_code']
                    ));
            }
        } elseif (!is_string($responseString)) {
            throw new RuntimeException(sprintf(
                'No content from Web server [HTTP status code %s]',
                $this->lastInfo['http_code']
            ));
        }

        $headerLength = curl_getinfo($this->resource, CURLINFO_HEADER_SIZE);
        $this->lastResponseHeaders = substr($responseString, 0, $headerLength);

        $body = substr($responseString, $headerLength);

        $lastResponse = json_decode(trim($body), true);

        if (!is_array($lastResponse)) {
            if (is_string($body) && strlen($body) > 0) {
                throw new RuntimeException(sprintf(
                    'i-doit responded with an unknown message: %s',
                    $body
                ));
            } else {
                throw new RuntimeException('i-doit responded with an invalid JSON string.');
            }
        }

        $this->lastResponse = $lastResponse;

        return $this->lastResponse;
    }

    /**
     * Evaluates server response
     *
     * @param array $response Server response
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    protected function evaluateResponse(array $response): self {
        if (array_key_exists('error', $response) &&
            $response['error'] !== null) {
            // Validate error object:
            if (!is_array($response['error'])) {
                throw new RuntimeException('Invalid error object');
            }

            if (!array_key_exists('code', $response['error']) ||
                !is_int($response['error']['code']) ||
                $response['error']['code'] >= 0) {
                throw new RuntimeException('Invalid error code');
            }

            if (!array_key_exists('message', $response['error']) ||
                !is_string($response['error']['message']) ||
                strlen($response['error']['message']) === 0) {
                throw new RuntimeException('Invalid error message');
            }

            $details = [];

            if (array_key_exists('data', $response['error']) &&
                is_array($response['error']['data'])) {
                foreach ($response['error']['data'] as $topic => $description) {
                    $text = 'unknown';

                    switch (gettype($description)) {
                        case 'array':
                            $text = implode('; ', $description);
                            break;
                        case 'string':
                            $text = $description;
                            break;
                    }

                    $details[] = sprintf(
                        '%s: %s',
                        $topic,
                        $text
                    );
                }
            }

            if (count($details) > 0) {
                $message = sprintf(
                    'i-doit responded with an error: %s [%s]',
                    $response['error']['message'],
                    implode('; ', $details)
                );
            } else {
                $message = sprintf(
                    'i-doit responded with an error: %s',
                    $response['error']['message']
                );
            }

            throw new RuntimeException(
                $message,
                $response['error']['code']
            );
        }

        if (!array_key_exists('result', $response)) {
            throw new RuntimeException('i-doit forgot to add a result to its response.');
        }

        return $this;
    }

    /**
     * Perform a low level API request
     *
     * @param array $data JSON-RPC compatible payload
     * @param array $headers Additional headers as key-value pairs
     *
     * @return array Raw response from
     * @throws Exception on error
     */
    public function rawRequest(array $data = [], array $headers = []): array {
        foreach ($headers as $header => $value) {
            $this->options[CURLOPT_HTTPHEADER][] = $header . ': ' . $value;
        }

        return $this->execute($data);
    }

    /**
     * Get information about last client request
     *
     * These information may be very useful for debugging.
     *
     * @return array Associative array
     */
    public function getLastInfo(): array {
        return $this->lastInfo;
    }

    /**
     * Get HTTP headers of last client request
     *
     * These headers may be very useful for debugging.
     *
     * @return string Multi-line string
     */
    public function getLastRequestHeaders(): string {
        if (array_key_exists('request_header', $this->lastInfo)) {
            return $this->lastInfo['request_header'];
        }

        return '';
    }

    /**
     * Get HTTP headers of last server response
     *
     * These headers may be very useful for debugging.
     *
     * @return string Multi-line string
     */
    public function getLastResponseHeaders(): string {
        return $this->lastResponseHeaders;
    }

    /**
     * Response for last request
     *
     * @return array Associative array
     */
    public function getLastResponse(): array {
        return $this->lastResponse;
    }

    /**
     * Get last request content
     *
     * This is the last content which was sent as a request. This may be very useful for debugging.
     *
     * @return array Multi-dimensional associative array
     */
    public function getLastRequestContent(): array {
        return $this->lastRequestContent;
    }

    /**
     * Tests configuration settings
     *
     * @return bool Returns true if succeeded
     *
     * @throws Exception on any misconfigured setting
     */
    protected function testConfig(): bool {
        /**
         * Mandatory settings
         */

        $mandatorySettings = [
            self::URL,
            self::KEY
        ];

        foreach ($mandatorySettings as $mandatorySetting) {
            if (!array_key_exists($mandatorySetting, $this->config)) {
                throw new DomainException(sprintf(
                    'Configuration setting "%s" is mandatory.',
                    $mandatorySetting
                ));
            }
        }

        /**
         * Pre-checks
         */

        $config = $this->config;

        /**
         * @param string $key
         * @param string $subKey
         * @throws Exception
         */
        $checkString = function ($key, $subKey = null) use ($config) {
            $value = null;

            if (isset($subKey)) {
                $value = $config[$subKey][$key];
            } else {
                $value = $config[$key];
            }

            if (!is_string($value) || empty($value)) {
                throw new DomainException(sprintf(
                    'Configuration setting "%s" is invalid.',
                    $key
                ));
            }
        };

        /**
         * @param string $key
         * @param string $subKey
         * @throws Exception
         */
        $checkPort = function ($key, $subKey = null) use ($config) {
            $value = null;

            if (isset($subKey)) {
                $value = $config[$subKey][$key];
            } else {
                $value = $config[$key];
            }

            if (!is_int($value) || $value < self::PORT_MIN || $value > self::PORT_MAX) {
                throw new DomainException(sprintf(
                    'Configuration setting "%s" is not a valid port number between %s and %s.',
                    $key,
                    self::PORT_MIN,
                    self::PORT_MAX
                ));
            }
        };

        /**
         * URL
         */

        $checkString(self::URL);

        if (strpos($this->config[self::URL], 'https://') === false &&
            strpos($this->config[self::URL], 'http://') === false) {
            throw new DomainException(sprintf(
                'Unsupported protocol in API URL "%s"',
                $this->config[self::URL]
            ));
        }

        /**
         * Port
         */

        if (array_key_exists(self::PORT, $this->config)) {
            $checkPort(self::PORT);
        } elseif (strpos($this->config[self::URL], 'https://') === 0) {
            $this->config[self::PORT] = 443;
        } elseif (strpos($this->config[self::URL], 'http://') === 0) {
            $this->config[self::PORT] = 80;
        }

        /**
         * API key
         */

        $checkString(self::KEY);

        /**
         * Username and password
         */

        if (array_key_exists(self::USERNAME, $this->config)) {
            $checkString(self::USERNAME);

            if (!array_key_exists(self::PASSWORD, $this->config)) {
                throw new DomainException('Username has no password.');
            }

            $checkString(self::PASSWORD);
        } elseif (array_key_exists(self::PASSWORD, $this->config)) {
            throw new DomainException('There is no username.');
        }

        /**
         * Language
         */

        if (array_key_exists(self::LANGUAGE, $this->config)) {
            $checkString(self::LANGUAGE);

            // No further checks because i-doit can handle a wrong language setting itself.
        }

        /**
         * Proxy
         */

        if (array_key_exists(self::PROXY, $this->config)) {
            if (!is_array($this->config[self::PROXY])) {
                throw new DomainException('Proxy settings are invalid.');
            }

            $mandatorySettings = [
                self::PROXY_ACTIVE
            ];

            foreach ($mandatorySettings as $mandatorySetting) {
                if (!array_key_exists($mandatorySetting, $this->config[self::PROXY])) {
                    throw new DomainException(sprintf(
                        'Proxy setting "%s" is mandatory.',
                        $mandatorySetting
                    ));
                }
            }

            if (!is_bool($this->config[self::PROXY][self::PROXY_ACTIVE])) {
                throw new DomainException(sprintf(
                    'Proxy setting "%s" must be a boolean.',
                    self::PROXY_ACTIVE
                ));
            }

            if ($this->config[self::PROXY][self::PROXY_ACTIVE]) {
                $mandatorySettings = [
                    self::PROXY_TYPE,
                    self::PROXY_HOST,
                    self::PROXY_PORT
                ];

                foreach ($mandatorySettings as $mandatorySetting) {
                    if (!array_key_exists($mandatorySetting, $this->config[self::PROXY])) {
                        throw new DomainException(sprintf(
                            'Proxy setting "%s" is mandatory.',
                            $mandatorySetting
                        ));
                    }
                }

                $checkString(self::PROXY_TYPE, self::PROXY);
                $checkString(self::PROXY_HOST, self::PROXY);
                $checkPort(self::PROXY_PORT, self::PROXY);

                if (array_key_exists(self::PROXY_USERNAME, $this->config[self::PROXY])) {
                    $checkString(self::PROXY_USERNAME, self::PROXY);

                    if (!array_key_exists(self::PROXY_PASSWORD, $this->config[self::PROXY])) {
                        throw new DomainException('Proxy username has no password.');
                    }

                    $checkString(self::PROXY_PASSWORD, self::PROXY);
                } elseif (array_key_exists(self::PROXY_PASSWORD, $this->config[self::PROXY])) {
                    throw new DomainException('There is no proxy username.');
                }
            }
        }

        /**
         * Bypass secure connection
         */

        if (array_key_exists(self::BYPASS_SECURE_CONNECTION, $this->config)) {
            if (!is_bool($this->config[self::BYPASS_SECURE_CONNECTION])) {
                throw new DomainException(sprintf(
                    'Set "%s" to true to disable security-related cURL options, otherwise false to enable them',
                    self::BYPASS_SECURE_CONNECTION
                ));
            }
        } else {
            $this->config[self::BYPASS_SECURE_CONNECTION] = false;
        }

        return true;
    }

}
