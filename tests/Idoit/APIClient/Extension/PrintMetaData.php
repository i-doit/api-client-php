<?php

/**
 * Copyright (C) 2022 synetics GmbH
 * Copyright (C) 2016-2022 Benjamin Heisig
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
 * @copyright Copyright (C) 2022 synetics GmbH
 * @copyright Copyright (C) 2016-2022 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/i-doit/api-client-php
 */

declare(strict_types=1);

namespace Idoit\APIClient\Extension;

use \Exception;
use PHPUnit\Runner\BeforeFirstTestHook;
use Symfony\Component\Dotenv\Dotenv;
use Idoit\APIClient\API;
use Idoit\APIClient\Idoit;

final class PrintMetaData implements BeforeFirstTestHook {

    /**
     * @var API
     */
    protected $api;

    /**
     * @var Idoit
     */
    protected $idoit;

    /**
     * @var array
     */
    protected $composer = [];

    /**
     * @var array
     */
    protected $idoitInfo = [];

    /**
     * @var array
     */
    protected $apiInfo = [];

    /**
     * @throws Exception on error
     */
    public function executeBeforeFirstTest(): void {
        $this
            ->loadEnvironment()
            ->loadComposer()
            ->connectToAPI()
            ->getIdoitVersion()
            ->getAPIVersion()
            ->printMetaData();
    }

    /**
     * @return self Returns itself
     */
    protected function loadEnvironment(): self {
        $dotenv = new Dotenv();
        $dotenv
            ->usePutenv(true)
            ->load(__DIR__ . '/../../../../.env');
        return $this;
    }

    /**
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    protected function loadComposer(): self {
        $composerFile = __DIR__ . '/../../../../composer.json';
        $composerFileContent = file_get_contents($composerFile);
        if (!is_string($composerFileContent)) {
            throw new Exception(sprintf(
                'Unable to read file "%s"',
                $composerFile
            ));
        }
        $this->composer = json_decode($composerFileContent, true);
        return $this;
    }

    /**
     * @return self Returns itself
     */
    protected function connectToAPI(): self {
        try {
            $config = [
                API::URL => getenv('IDOIT_URL'),
                API::KEY => getenv('IDOIT_KEY'),
                API::BYPASS_SECURE_CONNECTION => getenv('IDOIT_BYPASS_SECURE_CONNECTION')
            ];

            if (getenv('IDOIT_PORT') !== false) {
                $config[API::PORT] = (int) getenv('IDOIT_PORT');
            }

            if (getenv('IDOIT_LANGUAGE') !== false) {
                $config[API::LANGUAGE] = getenv('IDOIT_LANGUAGE');
            }

            if (getenv('IDOIT_USERNAME') !== false && getenv('IDOIT_PASSWORD') !== false) {
                $config[API::USERNAME] = getenv('IDOIT_USERNAME');
                $config[API::PASSWORD] = getenv('IDOIT_PASSWORD');
            }

            if (getenv('IDOIT_BYPASS_SECURE_CONNECTION') !== false) {
                $config[API::BYPASS_SECURE_CONNECTION] = filter_var(
                    getenv('IDOIT_BYPASS_SECURE_CONNECTION'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                );
            }

            $this->api = new API($config);
            $this->idoit = new Idoit($this->api);
        } catch (Exception $e) {
            // Suppress any exception…
        }

        return $this;
    }

    /**
     * @return self Returns itself
     */
    protected function getIdoitVersion(): self {
        try {
            $this->idoitInfo = $this->idoit->readVersion();
        } catch (Exception $e) {
            // Suppress any exception…
        }
        return $this;
    }

    /**
     * @return self Returns itself
     */
    protected function getAPIVersion(): self {
        try {
            $addOns = $this->idoit->getAddOns();

            foreach ($addOns as $addOn) {
                if ($addOn['key'] === 'api') {
                    $this->apiInfo = $addOn;
                    break;
                }
            }
        } catch (Exception $e) {
            // Suppress any exception…
        }

        return $this;
    }

    /**
     * @return self Returns itself
     */
    protected function printMetaData(): self {
        $url = getenv('IDOIT_URL');
        $libName = $this->composer['name'];
        $libVersion = $this->composer['extra']['version'];
        $phpVersion = PHP_VERSION;
        $idoitVersion = 'unknown';
        $user = 'unknown';
        $apiVersion = 'unknown';
        $date = date('c');
        $os = PHP_OS;

        if (array_key_exists('version', $this->idoitInfo) &&
            array_key_exists('type', $this->idoitInfo)) {
            $idoitVersion = sprintf(
                '%s %s',
                $this->idoitInfo['version'],
                $this->idoitInfo['type']
            );
        }

        if (array_key_exists('login', $this->idoitInfo) &&
            is_array($this->idoitInfo['login']) &&
            array_key_exists('username', $this->idoitInfo['login']) &&
            array_key_exists('language', $this->idoitInfo['login']) &&
            array_key_exists('tenant', $this->idoitInfo['login'])) {
            $user = sprintf(
                '%s (%s) @ %s',
                $this->idoitInfo['login']['username'],
                $this->idoitInfo['login']['language'],
                $this->idoitInfo['login']['tenant']
            );
        }

        if (array_key_exists('version', $this->apiInfo)) {
            $apiVersion = sprintf(
                '%s',
                $this->apiInfo['version']
            );
        }

        fwrite(STDOUT, <<< EOF
Server-side information:
    URL:            $url
    i-doit:         $idoitVersion
    API:            $apiVersion
    User/tenant:    $user

Client-side information:
    Library:        $libName $libVersion
    PHP:            $phpVersion
    OS:             $os
    Date:           $date


EOF
        );

        return $this;
    }

}
