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
use bheisig\idoitapi\CMDBObject;
use bheisig\idoitapi\CMDBObjects;
use bheisig\idoitapi\CMDBCategory;

abstract class BaseTest extends TestCase {

    /**
     * @var \bheisig\idoitapi\API
     */
    protected $api;

    public function setUp() {
        $this->api = new API([
            'url' => $GLOBALS['url'],
            'key' => $GLOBALS['key'],
            'username' => $GLOBALS['username'],
            'password' => $GLOBALS['password']
        ]);
    }

    protected function createObject() {
        $cmdbObject = new CMDBObject($this->api);

        return $cmdbObject->create(
            'C__OBJTYPE__SERVER',
            $this->createRandomString()
        );
    }

    protected function getIPv4Net() {
        $cmdbObjects = new CMDBObjects($this->api);

        return $cmdbObjects->getID('Global v4', 'C__OBJTYPE__LAYER3_NET');
    }

    protected function createIP($objectID) {
        $cmdbCategory = new CMDBCategory($this->api);

        return $cmdbCategory->create(
            $objectID,
            'C__CATG__IP',
            [
                'net' => $this->getIPv4Net(),
                'active' => false,
                'primary' => false,
                'net_type' => 1,
                'ipv4_assignment' => 2,
                "ipv4_address" =>  $this->generateIPv4Address(),
                'description' => 'API TEST'
            ]
        );
    }

    protected function createModel($objectID) {
        $cmdbCategory = new CMDBCategory($this->api);

        return $cmdbCategory->create(
            $objectID,
            'C__CATG__MODEL',
            [
                'manufacturer' => $this->createRandomString(),
                'title' => $this->createRandomString(),
                'serial' => $this->createRandomString(),
                'description' => 'API TEST'
            ]
        );
    }

    protected function createRandomString() {
        return hash('sha256', microtime(true));
    }

    protected function generateIPv4Address() {
        return sprintf(
            '10.%s.%s.%s',
            mt_rand(2, 254),
            mt_rand(2, 254),
            mt_rand(2, 254)
        );
    }

    protected function getRootLocation() {
        $cmdbObjects = new CMDBObjects($this->api);

        return $cmdbObjects->getID('Root location', 'C__OBJTYPE__LOCATION_GENERIC');
    }

}
