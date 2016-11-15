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
        } //if

        $result = $this->api->request(
            'cmdb.category.create',
            $params
        );

        if (!array_key_exists('id', $result) ||
            !is_numeric($result['id']) ||
            !array_key_exists('success', $result) ||
            $result['success'] !== true) {
            throw new \Exception('Bad result');
        } //if

        return (int) $result['id'];
    } //function

    /**
     * Reads one or more category entries for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $categoryConst Category constant
     *
     * @return array Indexed array of result sets (for both single- and multi-valued categories)
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
    } //function

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
        } //if

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
        } //if

        return $this;
    } //function

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
        } //if

        if (isset($entryID)) {
            $params['cateID'] = $entryID;
        } //if

        $result = $this->api->request(
            'cmdb.category.delete',
            $params
        );

        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            throw new \Exception('Bad result');
        } //if

        return $this;
    } //function

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
        $this
            ->archive($objectID, $categoryConst, $entryID, $isGlobal)
            ->archive($objectID, $categoryConst, $entryID, $isGlobal);
    } //function


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
        $this
            ->archive($objectID, $categoryConst, $entryID, $isGlobal)
            ->archive($objectID, $categoryConst, $entryID, $isGlobal)
            ->archive($objectID, $categoryConst, $entryID, $isGlobal);
    } //function

    public function batchCreate() {
        // @todo Implement it.
    } //function

    /**
     * Reads one or more category entries for a specific object
     *
     * @param int[] $objectIDs List of object identifiers
     * @param string $categoryConst Category constant
     *
     * @return array Indexed array of result sets (for both single- and multi-valued categories)
     *
     * @throws \Exception on error
     */
    public function batchRead($objectIDs, $categoryConst) {
        $requests = [];

        foreach ($objectIDs as $objectID) {
            $requests[] = [
                'method' => 'cmdb.category.read',
                'params' => [
                    'objID' => $objectID,
                    'category' => $categoryConst
                ]
            ];
        } //foreach

        return $this->api->batchRequest($requests);
    } //function

    public function batchUpdate() {
        // @todo Implement it.
    } //function

    public function batchArchive() {
        // @todo Implement it.
    } //function

    public function batchDelete() {
        // @todo Implement it.
    } //function

    public function batchPurge() {
        // @todo Implement it.
    } //function

} //class
