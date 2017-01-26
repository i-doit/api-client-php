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

use PHPUnit\Framework\TestCase;
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObjectTypeCategories;
use bheisig\idoitapi\CMDBObjectTypes;

class CMDBObjectTypeCategoriesTest extends TestCase {

    /**
     * @var \bheisig\idoitapi\API
     */
    protected $api;

    /**
     * @var \bheisig\idoitapi\CMDBObjectTypeCategories
     */
    protected $instance;

    protected $objectTypeIDs = [];
    protected $objectTypeConsts = [];

    public function setUp() {
        $this->api = new API([
            'url' => $GLOBALS['url'],
            'key' => $GLOBALS['key'],
            'username' => $GLOBALS['username'],
            'password' => $GLOBALS['password']
        ]);

        $this->instance = new CMDBObjectTypeCategories($this->api);

        $cmdbObjectTypes = new CMDBObjectTypes($this->api);
        $objectTypes = $cmdbObjectTypes->read();

        foreach ($objectTypes as $objectType) {
            $this->objectTypeIDs[] = (int) $objectType['id'];
            $this->objectTypeConsts[] = $objectType['const'];
        }
    }

    public function testReadByID() {
        $categories = [];

        foreach ($this->objectTypeIDs as $objectTypeID) {
            $categories[] = $this->instance->readByID($objectTypeID);
        }

        $this->checkAssignedCategories($categories);
    }

    public function testReadByConst() {
        $categories = [];

        foreach ($this->objectTypeConsts as $objectTypeConst) {
            $categories[] = $this->instance->readByConst($objectTypeConst);
        }

        $this->checkAssignedCategories($categories);
    }

    public function testBatchReadByID() {
        $batchResult = $this->instance->batchReadByID($this->objectTypeIDs);

        $this->assertInternalType('array', $batchResult);
        $this->assertNotCount(0, $batchResult);

        foreach ($batchResult as $categories) {
            $this->checkAssignedCategories($categories);
        }
    }

    public function testBatchReadByConst() {
        $batchResult = $this->instance->batchReadByID($this->objectTypeConsts);

        $this->assertInternalType('array', $batchResult);
        $this->assertNotCount(0, $batchResult);

        foreach ($batchResult as $categories) {
            $this->checkAssignedCategories($categories);
        }
    }

    protected function checkAssignedCategories(array $categories) {
        $this->assertInternalType('array', $categories);

        foreach ($categories as $category) {
            $this->assertInternalType('array', $category);
            $this->assertNotCount(0, $category);
        }
    }

}
