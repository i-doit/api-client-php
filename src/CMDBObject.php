<?php

/**
 * Copyright (C) 2016-18 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-18 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

namespace bheisig\idoitapi;

/**
 * Requests for API namespace 'cmdb.object'
 */
class CMDBObject extends Request {

    /**
     * Create new object
     *
     * @param int|string $type Object type identifier or its constant
     * @param string $title Object title
     * @param array $attributes (Optional) additional common attributes
     * ('category', 'purpose', 'cmdb_status', 'description')
     *
     * @return int Object identifier
     *
     * @throws \Exception on error
     */
    public function create($type, $title, array $attributes = []) {
        $attributes['type'] = $type;
        $attributes['title'] = $title;

        $result = $this->api->request(
            'cmdb.object.create',
            $attributes
        );

        if (array_key_exists('id', $result)) {
            return (int) $result['id'];
        } else {
            throw new \RuntimeException('Unable to create object');
        }
    }

    /**
     * Create new object with category entries
     *
     * @param int|string $type Object type identifier or its constant
     * @param string $title Object title
     * @param array $categories Also create category entries;
     * set category constant (string) as key and
     * one (array of attributes) entry or even several entries (array of arrays) as value
     * @param array $attributes (Optional) additional common attributes
     * ('category', 'purpose', 'cmdb_status', 'description')
     *
     * @return array Result with object identifier ('id') and
     * key-value pairs of category constants and array of category entry identifiers as integers
     *
     * @throws \Exception on error
     */
    public function createWithCategories($type, $title, array $categories = [], array $attributes = []) {
        $attributes['type'] = $type;
        $attributes['title'] = $title;

        if (count($categories) > 0) {
            $attributes['categories'] = $categories;
        }

        return $this->api->request(
            'cmdb.object.create',
            $attributes
        );
    }

    /**
     * Read common information about object
     *
     * @param int $objectID Object identifier
     *
     * @return array Associative array
     *
     * @throws \Exception on error
     */
    public function read($objectID) {
        return $this->api->request('cmdb.object.read', [
            'id' => $objectID
        ]);
    }

    /**
     * Update existing object
     *
     * @param int $objectID Object identifier
     * @param array $attributes (Optional) common attributes (only 'title' is supported at the moment)
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function update($objectID, array $attributes = []) {
        $params = [
            'id' => $objectID
        ];

        $supportedAttributes = [
            'title'
        ];

        foreach ($supportedAttributes as $supportedAttribute) {
            if (array_key_exists($supportedAttribute, $attributes)) {
                $params[$supportedAttribute] = $attributes[$supportedAttribute];
            }
        }

        $result = $this->api->request(
            'cmdb.object.update',
            $params
        );

        if (!is_array($result) ||
            !array_key_exists('success', $result) ||
            $result['success'] === false) {
            throw new \RuntimeException(sprintf(
                'Unable to archive object %s',
                $objectID
            ));
        }

        return $this;
    }

    /**
     * Archive object
     *
     * @param int $objectID Object identifier
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function archive($objectID) {
        $result = $this->api->request(
            'cmdb.object.delete',
            [
                'id' => $objectID,
                'status' => 'C__RECORD_STATUS__ARCHIVED'
            ]
        );

        if (!is_array($result) ||
            !array_key_exists('success', $result) ||
            $result['success'] === false) {
            throw new \RuntimeException(sprintf(
                'Unable to archive object %s',
                $objectID
            ));
        }

        return $this;
    }

    /**
     * Delete object
     *
     * @param int $objectID Object identifier
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function delete($objectID) {
        $result = $this->api->request(
            'cmdb.object.delete',
            [
                'id' => $objectID,
                'status' => 'C__RECORD_STATUS__DELETED'
            ]
        );

        if (!is_array($result) ||
            !array_key_exists('success', $result) ||
            $result['success'] === false) {
            throw new \RuntimeException(sprintf(
                'Unable to delete object %s',
                $objectID
            ));
        }

        return $this;
    }

    /**
     * Purge object
     *
     * @param int $objectID Object identifier
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function purge($objectID) {
        $result = $this->api->request(
            'cmdb.object.delete',
            [
                'id' => $objectID,
                'status' => 'C__RECORD_STATUS__PURGE'
            ]
        );

        if (!is_array($result) ||
            !array_key_exists('success', $result) ||
            $result['success'] === false) {
            throw new \RuntimeException(sprintf(
                'Unable to purge object %s',
                $objectID
            ));
        }

        return $this;
    }

// @todo Does not work:
//    public function restore($objectID) {
//        $result = $this->api->request(
//            'cmdb.category.update',
//            [
//                'objID' => $objectID,
//                'category' => 'C__CATG__GLOBAL',
//                'data' => [
//                    // C__RECORD_STATUS__NORMAL
//                    'status' => 2
//                ]
//            ]
//        );
//
//        if (!is_array($result) ||
//            !array_key_exists('success', $result) ||
//            $result['success'] === false) {
//            throw new \RuntimeException(sprintf(
//                'Unable to restore object %s',
//                $objectID
//            ));
//        }
//    }

    /**
     * Load all data about object
     *
     * @param int $objectID Object identifier
     *
     * @return array Multi-dimensional array
     *
     * @throws \Exception on error
     */
    public function load($objectID) {
        $object = $this->read($objectID);

        if (count($object) === 0) {
            throw new \RuntimeException('Object not found');
        }

        if (!array_key_exists('objecttype', $object)) {
            throw new \RuntimeException(sprintf(
                'Object %s has no type',
                $objectID
            ));
        }

        $cmdbObjectTypeCategories = new CMDBObjectTypeCategories($this->api);

        $object += $cmdbObjectTypeCategories->readByID(
            (int) $object['objecttype']
        );

        $cmdbCategory = new CMDBCategory($this->api);

        $categoryTypes = ['catg', 'cats'];

        foreach ($categoryTypes as $categoryType) {
            if (array_key_exists($categoryType, $object)) {
                $categoryConstants = [];

                for ($i = 0; $i < count($object[$categoryType]); $i++) {
                    if (!array_key_exists('const', $object[$categoryType][$i])) {
                        throw new \RuntimeException(
                            'Information about categories is broken. Constant is missing.'
                        );
                    }

                    $object[$categoryType][$i]['entries'] = [];

                    $categoryConstants[] = $object[$categoryType][$i]['const'];
                }

                $categoryEntries = $cmdbCategory->batchRead([$objectID], $categoryConstants);

                if (count($object[$categoryType]) !== count($categoryEntries)) {
                    throw new \RuntimeException(sprintf(
                        'Requested entries for %s categories, but received %s results',
                        count($object[$categoryType]),
                        count($categoryEntries)
                    ));
                }

                for ($i = 0; $i < count($object[$categoryType]); $i++) {
                    $object[$categoryType][$i]['entries'] = $categoryEntries[$i];
                }
            }
        }

        return $object;
    }

    /**
     * Create new object or fetch existing one based on its title and type
     *
     * @param int|string $type Object type identifier or its constant
     * @param string $title Object title
     * @param array $attributes (Optional) additional common attributes
     * ('category', 'purpose', 'cmdb_status', 'description')
     *
     * @return int Object identifier
     *
     * @throws \Exception on error
     */
    public function upsert($type, $title, array $attributes = []) {
        $cmdbObjects = new CMDBObjects($this->api);

        $filter = [
            'title' => $title,
            'type' => $type
        ];

        $result = $cmdbObjects->read($filter);

        switch (count($result)) {
            case 0:
                return $this->create($type, $title, $attributes);
            case 1:
                if (!array_key_exists('id', $result[0])) {
                    throw new \RuntimeException('Bad result');
                }

                return (int) $result[0]['id'];
            default:
                throw new \RuntimeException(sprintf(
                    'Found %s objects',
                    count($result)
                ));
        }
    }

}
