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

    private $mock;

    private function getMockSelect()
    {
        if (! $this->mock) {
            $cat = 'C__CATG__GLOBAL';
            $filter = ['type' => 'C__OBJTYPE__SERVER'];
            $data = [
                ['id' => 123,'title' => 'Obj One'],
                ['id' => 124,'title' => 'Obj Two']
            ];
            $cats = [
                [['id' => 125,'objID' => 123,'title' => 'Cat One']],
                [['id' => 126,'objID' => 124,'title' => 'Cat Two']]
            ];

            $mObjects = $this->createMock(\bheisig\idoitapi\CMDBObjects::class);
            $mObjects->expects($this->atLeastOnce())
                ->method('read')
                ->with($filter, $this->greaterThanOrEqual(1), $this->greaterThanOrEqual(0))
                ->willReturn($data);

            $mCategory = $this->createMock(\bheisig\idoitapi\CMDBCategory::class);
            $mCategory->expects($this->atLeastOnce())
                ->method('batchRead')
                ->with([123,124], [$cat])
                ->willReturn($cats);

            $mSelect = $this->getMockBuilder(\bheisig\idoitapi\Select::class)
                ->setMethods([
                'getCMDBObjects',
                'getCMDBCategory'
            ])
                ->disableOriginalConstructor()
                ->getMock();
            $mSelect->expects($this->once())
                ->method('getCMDBObjects')
                ->willReturn($mObjects);
            $mSelect->expects($this->atLeastOnce())
                ->method('getCMDBCategory')
                ->willReturn($mCategory);
            $this->mock = $mSelect;
        }
        return $this->mock;
    }

    public function setUp() {
        parent::setUp();

        $this->instance = new Select($this->api);
    }

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
                'description' => 'API TEST'
            ]
        );

        $result = $this->instance->find('C__CATG__IP', 'hostaddress', $ip);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $result = $this->instance->find(
            'C__CATG__GLOBAL', 'title', $this->generateRandomString()
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

    public function testFindwithFilter()
    {
        $result = $this->getMockSelect()->find(
            'C__CATG__GLOBAL',
            'title',
            'Cat Two',
            ['type' => 'C__OBJTYPE__SERVER']
        );

        $this->assertInternalType('array', $result);
        $this->assertEquals([124], $result);
    }
}
