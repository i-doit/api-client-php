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

use bheisig\idoitapi\CMDBImpact;

class CMDBImpactTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBImpact
     */
    protected $instance;

    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBImpact($this->api);
    }

    /**
     * @throws \Exception
     */
    public function testReadByID() {
        $relationType = 10; // Location

        $objectID = $this->createServer();
        $locationID = $this->getRootLocation();
        $this->addObjectToLocation($objectID, $locationID);

        $result = $this->instance->readByID(
            $locationID,
            $relationType
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws \Exception
     */
    public function testBatchByConst() {
        $relationType = 'C__RELATION_TYPE__LOCATION';

        $objectID = $this->createServer();
        $locationID = $this->getRootLocation();
        $this->addObjectToLocation($objectID, $locationID);

        $result = $this->instance->readByConst(
            $locationID,
            $relationType
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

}
