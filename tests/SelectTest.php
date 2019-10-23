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

use bheisig\idoitapi\tests\Constants\Category;
use bheisig\idoitapi\tests\Constants\ObjectType;
use \Exception;
use bheisig\idoitapi\Select;
use bheisig\idoitapi\CMDBObject;
use bheisig\idoitapi\CMDBCategory;

/**
 * @coversDefaultClass \bheisig\idoitapi\Select
 */
class SelectTest extends BaseTest {

    /**
     * @var Select
     */
    protected $instance;

    /**
     * @throws Exception on error
     */
    public function setUp(): void {
        parent::setUp();

        $this->instance = new Select($this->api);
    }

    /**
     * @throws Exception on error
     */
    public function testFindByTitle() {
        $title = $this->generateRandomString();

        $cmdbObject = new CMDBObject($this->api);
        $objectID = $cmdbObject->create(ObjectType::SERVER, $title);

        $result = $this->instance->find(Category::CATG__GLOBAL, 'title', $title);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($objectID, $result[0]);
    }

    /**
     * @throws Exception on error
     */
    public function testFindNothing() {
        $result = $this->instance->find(
            Category::CATG__GLOBAL,
            'title',
            $this->generateRandomString()
        );

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testFindBySerialNumber() {
        $title = $this->generateRandomString();
        $serial = $this->generateRandomString();

        $cmdbObject = new CMDBObject($this->api);
        $objectID = $cmdbObject->create(ObjectType::SERVER, $title);

        $cmdbCategory = new CMDBCategory($this->api);

        $cmdbCategory->create(
            $objectID,
            Category::CATG__MODEL,
            [
                'serial' => $serial
            ]
        );

        $result = $this->instance->find(Category::CATG__MODEL, 'serial', $serial);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($objectID, $result[0]);
    }

    /**
     * @throws Exception on error
     */
    public function testFindByHostaddress() {
        $title = $this->generateRandomString();
        $ip = $this->generateIPv4Address();

        $cmdbObject = new CMDBObject($this->api);
        $objectID = $cmdbObject->create(ObjectType::SERVER, $title);

        $cmdbCategory = new CMDBCategory($this->api);

        $cmdbCategory->create(
            $objectID,
            Category::CATG__IP,
            [
                'net' => $this->getIPv4Net(),
                'active' => 0,
                'primary' => 0,
                'net_type' => 1,
                'ipv4_assignment' => 2,
                "ipv4_address" =>  $ip,
                'description' => $this->generateDescription()
            ]
        );

        $result = $this->instance->find(Category::CATG__IP, 'hostaddress', $ip);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($objectID, $result[0]);
    }

}
