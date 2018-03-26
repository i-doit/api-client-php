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

use bheisig\idoitapi\CMDBObjectsByRelation;

class CMDBObjectsByRelationTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBObjectsByRelation
     */
    protected $relation;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->relation = new CMDBObjectsByRelation($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByID() {
        $relationType = 10; // Location

        $objectID = $this->createServer();
        $locationID = $this->getRootLocation();
        $this->addObjectToLocation($objectID, $locationID);

        $result = $this->relation->readByID(
            $objectID,
            $relationType
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $first = end($result);

        $this->assertArrayHasKey('data', $first);
        $this->assertInternalType('array', $first['data']);
        $this->assertArrayHasKey('children', $first);
        $this->assertInternalType('boolean', $first['children']);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByConst() {
        $relationType = 'C__RELATION_TYPE__LOCATION';

        $objectID = $this->createServer();
        $locationID = $this->getRootLocation();
        $this->addObjectToLocation($objectID, $locationID);

        $result = $this->relation->readByConst(
            $objectID,
            $relationType
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $first = end($result);

        $this->assertArrayHasKey('data', $first);
        $this->assertInternalType('array', $first['data']);
        $this->assertArrayHasKey('children', $first);
        $this->assertInternalType('boolean', $first['children']);
    }

}
