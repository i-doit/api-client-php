<?php

/**
 * Copyright (C) 2016-17 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-17 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

namespace bheisig\idoitapi;

/**
 * Requests for API namespace 'cmdb.category'
 */
class CMDBCategory extends Request {

    /**
     * Creates a new category entry for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param array $attributes Attributes
     * @param bool $isGlobal (optional) Is category global, otherwise specific?
     *
     * @return int Entry identifier
     *
     * @throws \Exception on error
     */
    public function create($objectID, $categoryConst, array $attributes, $isGlobal = true) {
        $params = [
            'objID' => $objectID,
            'data' => $attributes
        ];

        if ($isGlobal === true) {
            $params['catgID'] = $categoryConst;
        } else {
            $params['catsID'] = $categoryConst;
        }

        $result = $this->api->request(
            'cmdb.category.create',
            $params
        );

        if (!array_key_exists('id', $result) ||
            !is_numeric($result['id']) ||
            !array_key_exists('success', $result) ||
            $result['success'] !== true) {
            throw new \Exception('Bad result');
        }

        return (int) $result['id'];
    }

    /**
     * Reads one or more category entries for a specific object (works with both single- and multi-valued categories)
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     *
     * @return array[] Indexed array of result sets (for both single- and multi-valued categories)
     *
     * @throws \Exception on error
     */
    public function read($objectID, $categoryConst) {
        return $this->api->request(
            'cmdb.category.read',
            [
                'objID' => $objectID,
                'category' => $categoryConst

            ]
        );
    }

    /**
     * Reads one specific category entry for a specific object (works with both single- and multi-valued categories)
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier
     *
     * @return array Associative array
     *
     * @throws \Exception on error
     */
    public function readOneByID($objectID, $categoryConst, $entryID) {
        $entries = $this->read($objectID, $categoryConst);

        foreach ($entries as $entry) {
            if (!array_key_exists('id', $entry)) {
                throw new \Exception(sprintf(
                    'Entries for category "%s" contain no identifier',
                    $categoryConst
                ));
            }

            $currentID = (int) $entry['id'];

            if ($currentID === $entryID) {
                return $entry;
            }
        }

        throw new \Exception(sprintf(
            'No entry with identifier %s found in category "%s" for object $s',
            $entryID,
            $categoryConst,
            $objectID
        ));
    }

    /**
     * Reads first category entry for a specific object (works with both single- and multi-valued categories)
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     *
     * @return array Associative array
     *
     * @throws \Exception on error
     */
    public function readFirst($objectID, $categoryConst) {
        $entries = $this->read($objectID, $categoryConst);

        return reset($entries);
    }

    /**
     * Updates a category entry for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param array $attributes Attributes
     * @param int $entryID Entry identifier (only needed for multi-valued categories)
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function update($objectID, $categoryConst, array $attributes, $entryID = null) {
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

        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            throw new \Exception('Bad result');
        }

        return $this;
    }

    /**
     * Archives a category entry for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier (only needed for multi-valued categories)
     * @param bool $isGlobal (optional) Is category global, otherwise specific?
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function archive($objectID, $categoryConst, $entryID = null, $isGlobal = true) {
        $params = [
            'objID' => $objectID
        ];

        if ($isGlobal === true) {
            $params['catgID'] = $categoryConst;
        } else {
            $params['catsID'] = $categoryConst;
        }

        if (isset($entryID)) {
            $params['cateID'] = $entryID;
        }

        $result = $this->api->request(
            'cmdb.category.delete',
            $params
        );

        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            throw new \Exception('Bad result');
        }

        return $this;
    }

    /**
     * Marks a category entry for a specific object as deleted
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier (only needed for multi-valued categories)
     * @param bool $isGlobal (optional) Is category global, otherwise specific?
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function delete($objectID, $categoryConst, $entryID = null, $isGlobal = true) {
        return $this
            ->archive($objectID, $categoryConst, $entryID, $isGlobal)
            ->archive($objectID, $categoryConst, $entryID, $isGlobal);
    }


    /**
     * Purges a category entry for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     * @param int $entryID Entry identifier (only needed for multi-valued categories)
     * @param bool $isGlobal (optional) Is category global, otherwise specific?
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function purge($objectID, $categoryConst, $entryID = null, $isGlobal = true) {
        return $this
            ->archive($objectID, $categoryConst, $entryID, $isGlobal)
            ->archive($objectID, $categoryConst, $entryID, $isGlobal)
            ->archive($objectID, $categoryConst, $entryID, $isGlobal);
    }

    /**
     * Creates multiple entries for a specific category and one or more objects
     *
     * @param int[] $objectIDs List of object identifiers
     * @param string $categoryConst Category constant
     * @param array[] $attributes Indexed array of attributes
     * @param bool $isGlobal (optional) Is category global, otherwise specific?
     *
     * @return int[] Entry identifiers
     *
     * @throws \Exception on error
     */
    public function batchCreate(array $objectIDs, $categoryConst, array $attributes, $isGlobal = true) {
        $entryIDs = [];

        $requests = [];

        foreach ($objectIDs as $objectID) {
            foreach ($attributes as $data) {
                $params = [
                    'objID' => $objectID,
                    'data' => $data
                ];

                if ($isGlobal === true) {
                    $params['catgID'] = $categoryConst;
                } else {
                    $params['catsID'] = $categoryConst;
                }

                $requests[] = [
                    'method' => 'cmdb.category.create',
                    'params' => $params
                ];
            }
        }

        $result = $this->api->batchRequest($requests);

        foreach ($result as $entry) {
            if (!array_key_exists('id', $entry) ||
                !is_numeric($entry['id']) ||
                !array_key_exists('success', $entry) ||
                $entry['success'] !== true) {
                throw new \Exception('Bad result');
            }

            $entryIDs[] = (int) $entry['id'];
        }

        return $entryIDs;
    }

    /**
     * Reads one or more category entries for one or more objects
     *
     * @param int[] $objectIDs List of object identifiers
     * @param string[] $categoryConsts Category constants
     *
     * @return array Indexed array of result sets (for both single- and multi-valued categories)
     *
     * @throws \Exception on error
     */
    public function batchRead($objectIDs, $categoryConsts) {
        $requests = [];

        foreach ($objectIDs as $objectID) {
            foreach ($categoryConsts as $categoryConst) {
                $requests[] = [
                    'method' => 'cmdb.category.read',
                    'params' => [
                        'objID' => $objectID,
                        'category' => $categoryConst
                    ]
                ];
            }
        }

        return $this->api->batchRequest($requests);
    }

    public function batchUpdate() {
        // @todo Implement it.
    }

    public function batchArchive() {
        // @todo Implement it.
    }

    public function batchDelete() {
        // @todo Implement it.
    }

    public function batchPurge() {
        // @todo Implement it.
    }

}
