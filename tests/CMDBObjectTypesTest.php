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

use bheisig\idoitapi\tests\Constants\ObjectType;
use \Exception;
use bheisig\idoitapi\CMDBObjectTypes;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBObjectTypes
 */
class CMDBObjectTypesTest extends BaseTest {

    /**
     * @var CMDBObjectTypes
     */
    protected $instance;

    /**
     * @throws Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBObjectTypes($this->api);
    }

    /**
     * @throws Exception on error
     */
    public function testRead() {
        $result = $this->instance->read();

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testReadOne() {
        $result = $this->instance->readOne(ObjectType::SERVER);

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testBatchRead() {
        $result = $this->instance->batchRead([
            ObjectType::SERVER,
            ObjectType::VIRTUAL_SERVER
        ]);

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByTitle() {
        $result = $this->instance->readByTitle('LC__CMDB__OBJTYPE__SERVER');

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);
    }

}
