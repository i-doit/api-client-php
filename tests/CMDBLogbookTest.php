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

namespace bheisig\idoitapi\tests;

use bheisig\idoitapi\CMDBLogbook;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBLogbook
 */
class CMDBLogbookTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBLogbook
     */
    protected $instance;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBLogbook($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreate() {
        $objectID = $this->createServer();

        $result = $this->instance->create(
            $objectID,
            'Performed unit test',
            'This is just a unit test.'
        );

        $this->assertInstanceOf(CMDBLogbook::class, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchCreate() {
        $objectID = $this->createServer();

        $result = $this->instance->batchCreate(
            $objectID,
            [
                'Performed unit test 1',
                'Performed unit test 2',
                'Performed unit test 3'
            ]
        );

        $this->assertInstanceOf(CMDBLogbook::class, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testRead() {
        $result = $this->instance->read();

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $result = $this->instance->read('today');

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $result = $this->instance->read(date('Y-m-d'));

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByObject() {
        $objectID = $this->createServer();

        $result = $this->instance->readByObject($objectID);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $result = $this->instance->readByObject($objectID, 'today');

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $result = $this->instance->readByObject($objectID, date('Y-m-d'));

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

}
