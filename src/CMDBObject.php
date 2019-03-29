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

use \Exception;
use \RuntimeException;

/**
 * Requests for API namespace 'cmdb.object'
 */
class CMDBObject extends Request {

    /**
     * Create new object
     *
     * @param int|string $type Object type identifier or its constant
     * @param string $title Object title
     * @param array $attributes (Optional) additional common attributes:
     *                          string|int "category",
     *                          string|int "cmdb_status",
     *                          0|1 "defaultTemplate",
     *                          string "description",
     *                          string|int "purpose",
     *                          int "status",
     *                          string "sysid"
     *
     * @return int Object identifier
     *
     * @throws Exception on error
     */
    public function create($type, $title, array $attributes = []) {
        $attributes['type'] = $type;
        $attributes['title'] = $title;

        $result = $this->api->request(
            'cmdb.object.create',
            $attributes
        );

        if (array_key_exists('id', $result)) {
            return $result['id'];
        } else {
            throw new RuntimeException('Unable to create object');
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
     * @param array $attributes (Optional) additional common attributes:
     *                          string|int "category",
     *                          string|int "cmdb_status",
     *                          0|1 "defaultTemplate",
     *                          string "description",
     *                          string|int "purpose",
     *                          int "status",
     *                          string "sysid"
     *
     * @return array Result with object identifier ('id') and
     * key-value pairs of category constants and array of category entry identifiers as integers
     *
     * @throws Exception on error
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
     * @throws Exception on error
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
     * @throws Exception on error
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
            throw new RuntimeException(sprintf(
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
     * @throws Exception on error
     */
    public function archive($objectID) {
        $this->api->request(
            'cmdb.object.archive',
            [
                'object' => $objectID
            ]
        );

        return $this;
    }

    /**
     * Mark object as deleted (it's still available)
     *
     * @param int $objectID Object identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function delete($objectID) {
        $this->api->request(
            'cmdb.object.delete',
            [
                'object' => $objectID
            ]
        );

        return $this;
    }

    /**
     * Purge object (delete it irrevocable)
     *
     * @param int $objectID Object identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function purge($objectID) {
        $this->api->request(
            'cmdb.object.purge',
            [
                'object' => $objectID
            ]
        );

        return $this;
    }

    /**
     * Convert object to template
     *
     * Works only for "normal objects" and "mass change templates"
     *
     * @param int $objectID Object identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function markAsTemplate($objectID) {
        $this->api->request(
            'cmdb.object.markAsTemplate',
            [
                'object' => $objectID
            ]
        );

        return $this;
    }

    /**
     * Convert object to mass change template
     *
     * Works only for "normal objects" and "templates"
     *
     * @param int $objectID Object identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function markAsMassChangeTemplate($objectID) {
        $this->api->request(
            'cmdb.object.markAsMassChangeTemplate',
            [
                'object' => $objectID
            ]
        );

        return $this;
    }

    /**
     * Restore object to "normal" status
     *
     * Works with archived and deleted objects, templates and mass change templates
     *
     * @param int $objectID Object identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function recycle($objectID) {
        $this->api->request(
            'cmdb.object.recycle',
            [
                'object' => $objectID
            ]
        );

        return $this;
    }

    /**
     * Load all data about object
     *
     * @param int $objectID Object identifier
     *
     * @return array Multi-dimensional array
     *
     * @throws Exception on error
     *
     * @deprecated This method is pretty slow. Use readAll() instead!
     */
    public function load($objectID) {
        $object = $this->read($objectID);

        if (count($object) === 0) {
            throw new RuntimeException('Object not found');
        }

        if (!array_key_exists('objecttype', $object)) {
            throw new RuntimeException(sprintf(
                'Object %s has no type',
                $objectID
            ));
        }

        $cmdbObjectTypeCategories = new CMDBObjectTypeCategories($this->api);

        $object += $cmdbObjectTypeCategories->readByID(
            $object['objecttype']
        );

        $cmdbCategory = new CMDBCategory($this->api);

        $categoryTypes = ['catg', 'cats', 'custom'];

        $cmdbCategoryInfo = new CMDBCategoryInfo($this->api);
        $blacklistedCategoryConstants = $cmdbCategoryInfo->getVirtualCategoryConstants();

        foreach ($categoryTypes as $categoryType) {
            if (!array_key_exists($categoryType, $object)) {
                continue;
            }

            $categoryConstants = [];

            for ($i = 0; $i < count($object[$categoryType]); $i++) {
                if (!array_key_exists('const', $object[$categoryType][$i])) {
                    throw new RuntimeException(
                        'Information about categories is broken. Constant is missing.'
                    );
                }

                $categoryConstant = $object[$categoryType][$i]['const'];

                if (in_array($categoryConstant, $blacklistedCategoryConstants)) {
                    continue;
                }

                $object[$categoryType][$i]['entries'] = [];

                $categoryConstants[] = $categoryConstant;
            }

            $categoryEntries = $cmdbCategory->batchRead([$objectID], $categoryConstants);

            for ($i = 0; $i < count($categoryConstants); $i++) {
                $index = -1;
                $entries = [];

                foreach ($object[$categoryType] as $key => $category) {
                    if ($category['const'] === $categoryConstants[$i]) {
                        $index = $key;
                        $entries = $categoryEntries[$i];
                        break;
                    }
                }

                $object[$categoryType][$index]['entries'] = $entries;
            }
        }

        return $object;
    }

    /**
     * Read all information about object including category entries
     *
     * @param int $objectID Object identifier
     *
     * @return array Multi-dimensional array
     *
     * @throws Exception on error
     */
    public function readAll($objectID) {
        $objects = (new CMDBObjects($this->api))
            ->read(['ids' => [$objectID]], null, null, null, null, true);

        switch (count($objects)) {
            case 0:
                throw new RuntimeException(sprintf(
                    'Object not found by identifier %s',
                    $objectID
                ));
            case 1:
                return end($objects);
            default:
                throw new RuntimeException(sprintf(
                    'Found multiple objects by identifier %s',
                    $objectID
                ));
        }
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
     * @throws Exception on error
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
                if (!array_key_exists(0, $result) ||
                    !array_key_exists('id', $result[0])) {
                    throw new RuntimeException('Bad result');
                }

                return $result[0]['id'];
            default:
                throw new RuntimeException(sprintf(
                    'Found %s objects',
                    count($result)
                ));
        }
    }

}
