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

namespace bheisig\idoitapi\tests;

use \Exception;
use bheisig\idoitapi\CMDBObjectTypeCategories;
use bheisig\idoitapi\CMDBObjectTypes;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBObjectTypeCategories
 */
class CMDBObjectTypeCategoriesTest extends BaseTest {

    /**
     * @var CMDBObjectTypeCategories
     */
    protected $instance;

    /**
     * @var array List of object type identifiers as integers
     */
    protected $objectTypeIDs = [];

    /**
     * @var array List of object type constants as strings
     */
    protected $objectTypeConsts = [];

    /**
     * @throws Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBObjectTypeCategories($this->api);

        $cmdbObjectTypes = new CMDBObjectTypes($this->api);
        $objectTypes = $cmdbObjectTypes->read();

        foreach ($objectTypes as $objectType) {
            $this->objectTypeIDs[] = (int) $objectType['id'];
            $this->objectTypeConsts[] = $objectType['const'];
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadByIdentifier() {
        $categories = [];

        foreach ($this->objectTypeIDs as $objectTypeID) {
            $categories[] = $this->instance->readByID($objectTypeID);
        }

        $this->checkAssignedCategories($categories);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByConstant() {
        $categories = [];

        foreach ($this->objectTypeConsts as $objectTypeConst) {
            $categories[] = $this->instance->readByConst($objectTypeConst);
        }

        $this->checkAssignedCategories($categories);
    }

    /**
     * @throws Exception on error
     */
    public function testBatchReadByIdentifier() {
        $batchResult = $this->instance->batchReadByID($this->objectTypeIDs);

        $this->assertIsArray($batchResult);
        $this->assertNotCount(0, $batchResult);

        foreach ($batchResult as $categories) {
            $this->checkAssignedCategories($categories);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testBatchReadByConstant() {
        $batchResult = $this->instance->batchReadByConst($this->objectTypeConsts);

        $this->assertIsArray($batchResult);
        $this->assertNotCount(0, $batchResult);

        foreach ($batchResult as $categories) {
            $this->checkAssignedCategories($categories);
        }
    }

    /**
     * Validate assigned categories
     *
     * @param array $categories
     */
    protected function checkAssignedCategories(array $categories) {
        $this->assertIsArray($categories);

        foreach ($categories as $category) {
            $this->assertIsArray($category);
            $this->assertNotCount(0, $category);
        }
    }

}
