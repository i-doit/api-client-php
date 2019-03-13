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

namespace bheisig\idoitapi;

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
     * @throws \Exception on error
     */
    public function createTCPConnection($title, $address = '127.0.0.1', $port = 6557, $active = true) {
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

        if (!array_key_exists('id', $result) ||
            !is_numeric($result['id']) ||
            !array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new \RuntimeException(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new \RuntimeException('Bad result');
            }
        }

        return (int) $result['id'];
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
     * @throws \Exception on error
     */
    public function createUNIXSocketConnection($title, $path, $active = true) {
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

        if (!array_key_exists('id', $result) ||
            !is_numeric($result['id']) ||
            !array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new \RuntimeException(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new \RuntimeException('Bad result');
            }
        }

        return (int) $result['id'];
    }

    /**
     * Read all existing monitoring instances
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function read() {
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
     * @throws \Exception on error
     */
    public function readByID($id) {
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
     * @throws \Exception on error
     */
    public function readByIDs($ids) {
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
     * @throws \Exception on error
     */
    public function readByTitle($title) {
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
     * @throws \Exception on error
     */
    public function update($id, array $attributes) {
        $result = $this->api->request(
            'monitoring.livestatus.update',
            [
                'id' => $id,
                'data' => $attributes
            ]
        );

        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new \RuntimeException(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new \RuntimeException('Bad result');
            }
        }

        return $this;
    }

    /**
     * Delete a monitoring instance by its identifier
     *
     * @param int $id Identifier
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function deleteByID($id) {
        $result = $this->api->request(
            'monitoring.livestatus.delete',
            [
                'id' => $id
            ]
        );

        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new \RuntimeException(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new \RuntimeException('Bad result');
            }
        }

        return $this;
    }

    /**
     * Delete a monitoring instance by its title
     *
     * @param string $title Title
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function deleteByTitle($title) {
        $result = $this->api->request(
            'monitoring.livestatus.delete',
            [
                'title' => $title
            ]
        );

        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new \RuntimeException(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new \RuntimeException('Bad result');
            }
        }

        return $this;
    }

    /**
     * Delete one or more monitoring instances be their identifiers
     *
     * @param array $ids List of identifiers as integers
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function batchDelete($ids) {
        $requests = [];

        foreach ($ids as $id) {
            $requests[] = [
                'method' => 'monitoring.livestatus.delete',
                'params' => [
                    'id' => $id
                ]
            ];
        }

        $result = $this->api->batchRequest($requests);

        foreach ($result as $tag) {
            // Do not check 'id' because in a batch request it is always NULL:
            if (!array_key_exists('success', $tag) ||
                $tag['success'] !== true) {
                if (array_key_exists('message', $tag)) {
                    throw new \RuntimeException(sprintf('Bad result: %s', $tag['message']));
                } else {
                    throw new \RuntimeException('Bad result');
                }
            }
        }

        return $this;
    }

    /**
     * Delete all monitoring instances
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function deleteAll() {
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
