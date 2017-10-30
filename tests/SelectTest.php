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

use bheisig\idoitapi\Select;
use bheisig\idoitapi\CMDBObject;
use bheisig\idoitapi\CMDBCategory;

class SelectTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\Select
     */
    protected $instance;

    public function setUp() {
        parent::setUp();

        $this->instance = new Select($this->api);
    }

    public function testFind() {
        $title = $this->createRandomString();
        $serial = $this->createRandomString();
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
                'description' => 'API TEST'
            ]
        );

        $result = $this->instance->find('C__CATG__IP', 'hostaddress', $ip);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $result = $this->instance->find(
            'C__CATG__GLOBAL', 'title', $this->createRandomString()
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

}
