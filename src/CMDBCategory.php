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

namespace Idoit\APIClient;

use \Exception;
use \BadMethodCallException;
use \RuntimeException;

/**
 * Requests for API namespace 'cmdb.category'
 */
class CMDBCategory extends Request {

    /**
     * Create new or update existing category entry for a specific object
     *
     * Suitable for single- and multi-value categories
     *
     * @param int $objectID Object identifier
     * @param string $categoryConstant Category constant
     * @param array $attributes Attributes as key-value pairs
     * @param int $entryID Entry identifier (only needed for multi-valued categories)
     *
     * @return int Entry identifier
     *
     * @throws Exception on error
     */
    public function save(int $objectID, string $categoryConstant, array $attributes, int $entryID = null): int {
        $params = [
            'object' => $objectID,
            'data' => $attributes,
            'category' => $categoryConstant
        ];

        if (isset($entryID)) {
            $params['entry'] = $entryID;
        }

        $result = $this->api->request(
            'cmdb.category.save',
            $params
        );

        if (!array_key_exists('entry', $result) ||
            !is_int($result['entry']) ||
            !array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new RuntimeException(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new RuntimeException('Bad result');
            }
        }

        return $result['entry'];
    }

    /**
     * Create new category entry for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param array $attributes Attributes as key-value pairs
     *
     * @return int Entry identifier
     *
     * @throws Exception on error
     */
    public function create(int $objectID, string $categoryConst, array $attributes): int {
        $params = [
            'objID' => $objectID,
            'data' => $attributes,
            'category' => $categoryConst
        ];

        $result = $this->api->request(
            'cmdb.category.create',
            $params
        );

        return $this->requireSuccessFor($result);
    }

    /**
     * Read one or more category entries for a specific object (works with both single- and multi-valued categories)
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $status Filter entries by status:
     * 2 = normal;
     * 3 = archived;
     * 4 = deleted,
     * -1 = combination of all;
     * defaults to: 2 = normal;
     * note: a status != 2 is only suitable for multi-value categories
     *
     * @return array Indexed array of result sets (for both single- and multi-valued categories)
     *
     * @throws Exception on error
     */
    public function read(int $objectID, string $categoryConst, int $status = 2): array {
        return $this->api->request(
            'cmdb.category.read',
            [
                'objID' => $objectID,
                'category' => $categoryConst,
                'status' => $status
            ]
        );
    }

    /**
     * Read one specific category entry for a specific object (works with both single- and multi-valued categories)
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier
     * @param int $status Filter entry by status:
     * 2 = normal;
     * 3 = archived;
     * 4 = deleted,
     * -1 = combination of all;
     * defaults to: 2 = normal;
     * note: a status != 2 is only suitable for multi-value categories
     *
     * @return array Associative array
     *
     * @throws Exception on error
     */
    public function readOneByID(int $objectID, string $categoryConst, int $entryID, int $status = 2): array {
        $entries = $this->read($objectID, $categoryConst, $status);

        foreach ($entries as $entry) {
            if (!array_key_exists('id', $entry)) {
                throw new RuntimeException(sprintf(
                    'Entries for category "%s" contain no identifier',
                    $categoryConst
                ));
            }

            $currentID = (int) $entry['id'];

            if ($currentID === $entryID) {
                return $entry;
            }
        }

        throw new RuntimeException(sprintf(
            'No entry with identifier %s found in category "%s" for object %s',
            $entryID,
            $categoryConst,
            $objectID
        ));
    }

    /**
     * Read first category entry for a specific object (works with both single- and multi-valued categories)
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     *
     * @return array Associative array, otherwise empty array when there is no entry
     *
     * @throws Exception on error
     */
    public function readFirst(int $objectID, string $categoryConst): array {
        $entries = $this->read($objectID, $categoryConst);

        if (count($entries) === 0) {
            return [];
        }

        return reset($entries);
    }

    /**
     * Update category entry for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param array $attributes Attributes as key-value pairs
     * @param int $entryID Entry identifier (only needed for multi-value categories)
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function update(int $objectID, string $categoryConst, array $attributes, int $entryID = null): self {
        if (isset($entryID)) {
            $attributes['category_id'] = $entryID;
        }

        $result = $this->api->request(
            'cmdb.category.update',
            [
                'objID' => $objectID,
                'category' => $categoryConst,
                'data' => $attributes
            ]
        );

        $this->requireSuccessWithoutIdentifier($result);

        return $this;
    }

    /**
     * Archive entry in a multi-value category for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function archive(int $objectID, string $categoryConst, int $entryID): self {
        $this->api->request(
            'cmdb.category.archive',
            [
                'object' => $objectID,
                'category' => $categoryConst,
                'entry' => $entryID
            ]
        );

        return $this;
    }

    /**
     * Marks entry in a multi-value category for a specific object as deleted
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function delete(int $objectID, string $categoryConst, int $entryID): self {
        $this->api->request(
            'cmdb.category.delete',
            [
                'object' => $objectID,
                'category' => $categoryConst,
                'entry' => $entryID
            ]
        );

        return $this;
    }

    /**
     * Purge entry in a single- or multi-value category for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier (only needed for multi-value categories)
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function purge(int $objectID, string $categoryConst, int $entryID = null): self {
        $params = [
            'object' => $objectID,
            'category' => $categoryConst
        ];

        if (isset($entryID)) {
            $params['entry'] = $entryID;
        }

        $this->api->request(
            'cmdb.category.purge',
            $params
        );

        return $this;
    }

    /**
     * Restore entry in a multi-value category for a specific object to "normal" state
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function recycle(int $objectID, string $categoryConst, int $entryID): self {
        $this->api->request(
            'cmdb.category.recycle',
            [
                'object' => $objectID,
                'category' => $categoryConst,
                'entry' => $entryID
            ]
        );

        return $this;
    }

    /**
     * Purge entry in a multi-value category for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function quickPurge(int $objectID, string $categoryConst, int $entryID): self {
        $result = $this->api->request(
            'cmdb.category.quickpurge',
            [
                'objID' => $objectID,
                'category' => $categoryConst,
                'cateID' => $entryID
            ]
        );

        $this->requireSuccessWithoutIdentifier($result);

        return $this;
    }

    /**
     * Create multiple entries for a specific category and one or more objects
     *
     * @param array $objectIDs List of object identifiers as integers
     * @param string $categoryConst Category constant
     * @param array $attributes Indexed array of attributes as key-value pairs
     *
     * @return array List of entry identifiers as integers
     *
     * @throws Exception on error
     */
    public function batchCreate(array $objectIDs, string $categoryConst, array $attributes): array {
        $entryIDs = [];

        $requests = [];

        foreach ($objectIDs as $objectID) {
            foreach ($attributes as $data) {
                $params = [
                    'objID' => $objectID,
                    'data' => $data,
                    'category' => $categoryConst
                ];

                $requests[] = [
                    'method' => 'cmdb.category.create',
                    'params' => $params
                ];
            }
        }

        $result = $this->api->batchRequest($requests);

        foreach ($result as $entry) {
            // Do not check 'id' because in a batch request it is always NULL:
            if (!array_key_exists('success', $entry) ||
                $entry['success'] !== true) {
                if (array_key_exists('message', $entry)) {
                    throw new RuntimeException(sprintf('Bad result: %s', $entry['message']));
                } else {
                    throw new RuntimeException('Bad result');
                }
            }

            $entryIDs[] = (int) $entry['id'];
        }

        return $entryIDs;
    }

    /**
     * Read one or more category entries for one or more objects
     *
     * @param array $objectIDs List of object identifiers as integers
     * @param array $categoryConstants List of category constants as strings
     * @param int $status Filter entries by status:
     * 2 = normal;
     * 3 = archived;
     * 4 = deleted,
     * -1 = combination of all;
     * defaults to: 2 = normal;
     * note: a status != 2 is only suitable for multi-value categories
     *
     * @return array Indexed array of result sets (for both single- and multi-valued categories)
     *
     * @throws Exception on error
     */
    public function batchRead(array $objectIDs, array $categoryConstants, int $status = 2): array {
        if (count($objectIDs) === 0) {
            throw new BadMethodCallException('Needed at least one object identifier');
        }

        if (count($categoryConstants) === 0) {
            throw new BadMethodCallException('Needed at least one category constant');
        }

        $requests = [];

        foreach ($objectIDs as $objectID) {
            if (!is_int($objectID) || $objectID <= 0) {
                throw new BadMethodCallException(
                    'Each object identifier must be a positive integer'
                );
            }

            foreach ($categoryConstants as $categoryConstant) {
                if (!is_string($categoryConstant) || strlen($categoryConstant) === 0) {
                    throw new BadMethodCallException(
                        'Each category constant must be a non-empty string'
                    );
                }

                $requests[] = [
                    'method' => 'cmdb.category.read',
                    'params' => [
                        'objID' => $objectID,
                        'category' => $categoryConstant,
                        'status' => $status
                    ]
                ];
            }
        }

        $results = $this->api->batchRequest($requests);

        $expectedAmountOfResults = count($objectIDs) * count($categoryConstants);
        $actualAmountOfResults = count($results);

        if ($expectedAmountOfResults !== $actualAmountOfResults) {
            throw new RuntimeException(sprintf(
                'Requested entries for %s object(s) and %s category/categories but got %s result(s)',
                count($objectIDs),
                count($categoryConstants),
                $actualAmountOfResults
            ));
        }

        return $results;
    }

    /**
     * Update single-value category for one or more objects
     *
     * @param array $objectIDs List of object identifiers as integers
     * @param string $categoryConst Category constant
     * @param array $attributes Attributes as key-value pairs
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function batchUpdate(array $objectIDs, string $categoryConst, array $attributes): self {
        $requests = [];

        foreach ($objectIDs as $objectID) {
            $requests[] = [
                'method' => 'cmdb.category.update',
                'params' => [
                    'objID' => $objectID,
                    'category' => $categoryConst,
                    'data' => $attributes
                ]
            ];
        }

        $results = $this->api->batchRequest($requests);

        $this->requireSuccessforAll($results);

        return $this;
    }

    /**
     * Archive category entries for a specific object
     *
     * @param int $objectID Object identifier
     * @param array $categoryConsts List of category constants as strings
     *
     * @return int Number of purged category entries
     *
     * @throws Exception on error
     */
    public function clear(int $objectID, array $categoryConsts): int {
        $batch = $this->batchRead([$objectID], $categoryConsts);

        $requests = [];
        $counter = 0;
        $index = 0;

        foreach ($batch as $entries) {
            $categoryConst = $categoryConsts[$index];

            foreach ($entries as $entry) {
                $requests[] = [
                    'method' => 'cmdb.category.archive',
                    'params' => [
                        'object' => $objectID,
                        'category' => $categoryConst,
                        'entry' => (int) $entry['id']
                    ]
                ];

                $counter++;
            }

            $index++;
        }

        if ($counter === 0) {
            return 0;
        }

        $results = $this->api->batchRequest($requests);

        $this->requireSuccessforAll($results);

        return $counter;
    }

}
