<?php

/**
 * Copyright (C) 2016 Benjamin Heisig
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
 * @copyright Copyright (C) 2016 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

namespace bheisig\idoitapi;

/**
 * Requests for API namespace 'cmdb.object'
 */
class CMDBObject extends Request {

    /**
     * Creates a new object
     *
     * @param int|string $type Object type identifier or its constant
     * @param string $title Object title
     * @param array $attributes (Optional) additional common attributes ('category', 'purpose', 'cmdb_status', 'description')
     *
     * @return int Object identifier
     *
     * @throws \Exception on error
     */
    public function create($type, $title, $attributes = []) {
        $attributes['type'] = $type;
        $attributes['title'] = $title;

        $result = $this->api->request(
            'cmdb.object.create',
            $attributes
        );

        if (array_key_exists('id', $result)) {
            return (int) $result['id'];
        } else {
            throw new \Exception('Unable to create object');
        } //if
    } //function

    /**
     * Reads an object
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
    } //function

    /**
     * Updates an existing object
     *
     * @param int $objectID Object identifier
     * @param array $attributes (Optional) common attributes (only 'title' is supported at the moment)
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function update($objectID, $attributes = []) {
        $params = [
            'id' => $objectID
        ];

        $supportedAttributes = [
            'title'
        ];

        foreach ($supportedAttributes as $supportedAttribute) {
            if (array_key_exists($supportedAttribute, $attributes)) {
                $params[$supportedAttribute] = $attributes[$supportedAttribute];
            } //if
        } //foreach

        $result = $this->api->request(
            'cmdb.object.update',
            $params
        );

        if (!is_array($result) ||
            !array_key_exists('success', $result) ||
            $result['success'] === false) {
            throw new \Exception(sprintf(
                'Unable to archive object %s',
                $objectID
            ));
        } //if

        return $this;
    } //function

    /**
     * Archives an object
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
            throw new \Exception(sprintf(
                'Unable to archive object %s',
                $objectID
            ));
        } //if

        return $this;
    } //function

    /**
     * Deletes an object
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
            throw new \Exception(sprintf(
                'Unable to delete object %s',
                $objectID
            ));
        } //if

        return $this;
    } //function

    /**
     * Purges an object
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
            throw new \Exception(sprintf(
                'Unable to purge object %s',
                $objectID
            ));
        } //if

        return $this;
    } //function

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
//            throw new \Exception(sprintf(
//                'Unable to restore object %s',
//                $objectID
//            ));
//        } //if
//    } //function

} //class
