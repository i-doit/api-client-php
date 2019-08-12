<?php

/**
 * Copyright (C) 2016-19 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-19 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi;

use \Exception;

/**
 * Requests for API namespace 'monitoring.livestatus'
 */
class MonitoringLivestatus extends Request {

    /**
     * Add a new monitoring instance listening on a TCP/IP port
     *
     * @param string $title Title
     * @param string $address Hostname, FQDN or IP address; defaults to 127.0.0.1
     * @param int $port TCP/IP port (1-65535); defaults to 6557
     * @param bool $active Enable this instance? Defaults to true
     *
     * @return int Identifier
     *
     * @throws Exception on error
     */
    public function createTCPConnection(
        string $title, string $address = '127.0.0.1', int $port = 6557, bool $active = true
    ): int {
        $result = $this->api->request(
            'monitoring.livestatus.create',
            [
                'data' => [
                    'connection' => 'tcp',
                    'title' => $title,
                    'address' => $address,
                    'port' => $port,
                    'active' => $active
                ]
            ]
        );

        return $this->requireSuccessFor($result);
    }

    /**
     * Add a new monitoring instance listening on a UNIX socket
     *
     * @param string $title Title
     * @param string $path Path to UNIX socket
     * @param bool $active Enable this instance? Defaults to true
     *
     * @return int Identifier
     *
     * @throws Exception on error
     */
    public function createUNIXSocketConnection(string $title, string $path, bool $active = true): int {
        $result = $this->api->request(
            'monitoring.livestatus.create',
            [
                'data' => [
                    'connection' => 'unix',
                    'title' => $title,
                    'path' => $path,
                    'active' => $active
                ]
            ]
        );

        return $this->requireSuccessFor($result);
    }

    /**
     * Read all existing monitoring instances
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function read(): array {
        return $this->api->request(
            'monitoring.livestatus.read'
        );
    }

    /**
     * Read a monitoring instance by its identifier
     *
     * @param int $id Identifier
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function readByID(int $id): array {
        return $this->api->request(
            'monitoring.livestatus.read',
            [
                'id' => $id
            ]
        );
    }

    /**
     * Read all monitoring instances filtered by their identifiers
     *
     * @param array $ids List of identifiers as integers
     *
     * @return array Result
     *
     * @throws Exception on error
     */
    public function readByIDs(array $ids): array {
        return $this->api->request(
            'monitoring.livestatus.read',
            [
                'ids' => $ids
            ]
        );
    }

    /**
     * Read a monitoring instance by its title
     *
     * @param string $title Title
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function readByTitle(string $title): array {
        return $this->api->request(
            'monitoring.livestatus.read',
            [
                'title' => $title
            ]
        );
    }

    /**
     * Update a monitoring instance by its identifier
     *
     * @param int $id Identifier
     * @param array $attributes Attributes which can be altered:
     * "title", "connection", "address", "port", "path" and "active"
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function update(int $id, array $attributes): self {
        $result = $this->api->request(
            'monitoring.livestatus.update',
            [
                'id' => $id,
                'data' => $attributes
            ]
        );

        $this->requireSuccessWithoutIdentifier($result);

        return $this;
    }

    /**
     * Delete a monitoring instance by its identifier
     *
     * @param int $id Identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function deleteByID(int $id): self {
        $result = $this->api->request(
            'monitoring.livestatus.delete',
            [
                'id' => $id
            ]
        );

        $this->requireSuccessWithoutIdentifier($result);

        return $this;
    }

    /**
     * Delete a monitoring instance by its title
     *
     * @param string $title Title
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function deleteByTitle(string $title): self {
        $result = $this->api->request(
            'monitoring.livestatus.delete',
            [
                'title' => $title
            ]
        );

        $this->requireSuccessWithoutIdentifier($result);

        return $this;
    }

    /**
     * Delete one or more monitoring instances be their identifiers
     *
     * @param array $ids List of identifiers as integers
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function batchDelete(array $ids): self {
        $requests = [];

        foreach ($ids as $id) {
            $requests[] = [
                'method' => 'monitoring.livestatus.delete',
                'params' => [
                    'id' => $id
                ]
            ];
        }

        $results = $this->api->batchRequest($requests);

        $this->requireSuccessforAll($results);

        return $this;
    }

    /**
     * Delete all monitoring instances
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function deleteAll(): self {
        $instances = $this->read();

        $ids = [];

        foreach ($instances as $instance) {
            $ids[] = $instance['id'];
        }

        if (count($ids) > 0) {
            $this->batchDelete($ids);
        }

        return $this;
    }

}
