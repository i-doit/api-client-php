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
use \BadMethodCallException;
use \RuntimeException;

/**
 * Requests for API namespace 'checkmk.statictag'
 */
class CheckMKStaticTag extends Request {

    /**
     * Create a new static host tag
     *
     * @param string $title Name
     * @param string $tag Tag ID
     * @param string $group Optional associated host group
     * @param string $description Optional description
     *
     * @return int Identifier
     *
     * @throws Exception on error
     */
    public function create(string $title, string $tag = null, string $group = null, string $description = null): int {
        $params = [
            'data' => [
                'title' => $title
            ]
        ];

        if (isset($tag)) {
            $params['data']['tag'] = $tag;
        }

        if (isset($group)) {
            $params['data']['group'] = $group;
        }

        if (isset($group)) {
            $params['data']['description'] = $description;
        }

        $result = $this->api->request(
            'checkmk.statictag.create',
            $params
        );

        if (!array_key_exists('id', $result) ||
            !is_numeric($result['id']) ||
            !array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new RuntimeException(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new RuntimeException('Bad result');
            }
        }

        return (int) $result['id'];
    }

    /**
     * Create one or more static host tags
     *
     * @param array $tags List of tags;
     * required attributes per tag: "title";
     * optional attributes per tag: "tag" (tag ID), "group", "description"
     *
     * @return array List of identifiers as integers
     *
     * @throws Exception on error
     */
    public function batchCreate(array $tags): array {
        $requests = [];

        $required = ['title'];

        foreach ($tags as $data) {
            foreach ($required as $attribute) {
                if (!array_key_exists($attribute, $data)) {
                    throw new BadMethodCallException(sprintf(
                        'Missing attribute "%s"',
                        $attribute
                    ));
                }
            }

            $requests[] = [
                'method' => 'checkmk.statictag.create',
                'params' => [
                    'data' => $data
                ]
            ];
        }

        $result = $this->api->batchRequest($requests);

        $tagIDs = [];

        foreach ($result as $tag) {
            // Do not check 'id' because in a batch request it is always NULL:
            if (!array_key_exists('success', $tag) ||
                $tag['success'] !== true) {
                if (array_key_exists('message', $tag)) {
                    throw new RuntimeException(sprintf('Bad result: %s', $tag['message']));
                } else {
                    throw new RuntimeException('Bad result');
                }
            }

            $tagIDs[] = $tag['id'];
        }

        return $tagIDs;
    }

    /**
     * Read all existing static host tags
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function read(): array {
        return $this->api->request(
            'checkmk.statictag.read'
        );
    }

    /**
     * Read a static host tag by its identifier
     *
     * @param int $id Identifier
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function readByID(int $id): array {
        return $this->api->request(
            'checkmk.statictag.read',
            [
                'id' => $id
            ]
        );
    }

    /**
     * Read all static hosts tags filtered by their identifiers
     *
     * @param array $ids List of identifiers as integers
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function readByIDs(array $ids): array {
        return $this->api->request(
            'checkmk.statictag.read',
            [
                'ids' => $ids
            ]
        );
    }

    /**
     * Read a static host tag by its tag
     *
     * @param string $tag Tag
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function readByTag(string $tag): array {
        return $this->api->request(
            'checkmk.statictag.read',
            [
                'tag' => $tag
            ]
        );
    }

    /**
     * Update a static host tag by its identifier
     *
     * @param int $id Identifier
     * @param array $tag Tag attributes which can be altered:
     * "tag", "title", "group", "description"
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function update(int $id, array $tag): self {
        $result = $this->api->request(
            'checkmk.statictag.update',
            [
                'id' => $id,
                'data' => $tag
            ]
        );

        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new RuntimeException(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new RuntimeException('Bad result');
            }
        }

        return $this;
    }

    /**
     * Delete a static host tag by its identifier
     *
     * @param int $id Identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function delete(int $id): self {
        $result = $this->api->request(
            'checkmk.statictag.delete',
            [
                'id' => $id
            ]
        );

        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new RuntimeException(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new RuntimeException('Bad result');
            }
        }

        return $this;
    }

    /**
     * Delete one or more static host tags be their identifiers
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
                'method' => 'checkmk.statictag.delete',
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
                    throw new RuntimeException(sprintf('Bad result: %s', $tag['message']));
                } else {
                    throw new RuntimeException('Bad result');
                }
            }
        }

        return $this;
    }

    /**
     * Delete all static host tags
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function deleteAll(): self {
        $tags = $this->read();

        $ids = [];

        foreach ($tags as $tag) {
            $ids[] = $tag['id'];
        }

        if (count($ids) > 0) {
            $this->batchDelete($ids);
        }

        return $this;
    }

}
