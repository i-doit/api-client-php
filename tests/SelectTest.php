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

use bheisig\idoitapi\Select;
use bheisig\idoitapi\CMDBObject;
use bheisig\idoitapi\CMDBCategory;

class SelectTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\Select
     */
    protected $instance;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new Select($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testFind() {
        $title = $this->generateRandomString();
        $serial = $this->generateRandomString();
        $ip = $this->generateIPv4Address();

        $cmdbObject = new CMDBObject($this->api);
        $objectID = $cmdbObject->create('C__OBJTYPE__SERVER', $title);

        $cmdbCategory = new CMDBCategory($this->api);

        $result = $this->instance->find('C__CATG__GLOBAL', 'title', $title);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $cmdbCategory->create(
            $objectID,
            'C__CATG__MODEL',
            [
                'serial' => $serial
            ]
        );

        $result = $this->instance->find('C__CATG__MODEL', 'serial', $serial);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $cmdbCategory->create(
            $objectID,
            'C__CATG__IP',
            [
                'net' => $this->getIPv4Net(),
                'active' => false,
                'primary' => false,
                'net_type' => 1,
                'ipv4_assignment' => 2,
                "ipv4_address" =>  $ip,
                'description' => $this->generateDescription()
            ]
        );

        $result = $this->instance->find('C__CATG__IP', 'hostaddress', $ip);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $result = $this->instance->find(
            'C__CATG__GLOBAL',
            'title',
            $this->generateRandomString()
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

}
