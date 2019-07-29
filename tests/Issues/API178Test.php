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

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\tests\Constants\Category;
use bheisig\idoitapi\tests\Constants\ObjectType;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-178
 * @see https://i-doit.atlassian.net/browse/API-178
 */
class API178Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testIssue() {
        /**
         * Create test data:
         */

        $rootID = $this->getRootLocation();
        $this->isID($rootID);

        $rackID = $this->useCMDBObject()->create(
            ObjectType::ENCLOSURE,
            $this->generateRandomString()
        );
        $this->isID($rackID);

        $rackLocationID = $this->addObjectToLocation($rackID, $rootID);
        $this->isID($rackLocationID);

        $segmentID = $this->useCMDBObject()->create(
            ObjectType::RACK_SEGMENT,
            $this->generateRandomString()
        );
        $this->isID($segmentID);

        $chassisViewID = $this->useCMDBCategory()->save(
            $segmentID,
            Category::CATS__CHASSIS_VIEW,
            [
                'front_x' => 2,
                'front_y' => 1,
                // Front size: 0 = XS, 1 = S, 2 = M, 3 = L, 4 = XL, 5 = XXL:
                'front_size' => 3
            ]
        );
        $this->isID($chassisViewID);

        $slot1ID = $this->useCMDBCategory()->save(
            $segmentID,
            Category::CATS__CHASSIS_SLOT,
            [
                'title' => 'Slot #1',
                'from_x' => 0,
                'to_x' => 0,
                'from_y' => 0,
                'to_y' => 0,
                'insertion' => 1 // 0: back; 1: front
            ]
        );
        $this->isID($slot1ID);

        $slot2ID = $this->useCMDBCategory()->save(
            $segmentID,
            Category::CATS__CHASSIS_SLOT,
            [
                'title' => 'Slot #2',
                'from_x' => 1,
                'to_x' => 1,
                'from_y' => 0,
                'to_y' => 0,
                'insertion' => 1 // 0: back; 1: front
            ]
        );
        $this->isID($slot2ID);

        $hostID = $this->createServer();
        $this->isID($hostID);

        $assignedDeviceID = $this->useCMDBCategory()->save(
            $segmentID,
            Category::CATS__CHASSIS_DEVICES,
            [
                'assigned_device' => $hostID,
                'assigned_slots' => [$slot1ID]
            ]
        );
        $this->isID($assignedDeviceID);

        /**
         * Run tests:
         */

        $assignedDevices = $this->useCMDBCategory()->read(
            $segmentID,
            Category::CATS__CHASSIS_DEVICES
        );

        $this->assertIsArray($assignedDevices);
        $this->assertCount(1, $assignedDevices);
        $this->assertArrayHasKey(0, $assignedDevices);
        $this->assertIsArray($assignedDevices[0]);
        $this->isCategoryEntry($assignedDevices[0]);
        $this->assertSame($assignedDeviceID, (int) $assignedDevices[0]['id']);
        $this->assertArrayHasKey('assigned_device', $assignedDevices[0]);
        $this->assertIsArray($assignedDevices[0]['assigned_device']);
        $this->assertArrayHasKey('id', $assignedDevices[0]['assigned_device']);
        $this->assertSame($hostID, (int) $assignedDevices[0]['assigned_device']['id']);
        $this->assertArrayHasKey('assigned_slots', $assignedDevices[0]);
        $this->assertIsArray($assignedDevices[0]['assigned_slots']);
        // This failed in the past:
        $this->assertCount(1, $assignedDevices[0]['assigned_slots']);
        $this->assertArrayHasKey(0, $assignedDevices[0]['assigned_slots']);
        $this->assertIsArray($assignedDevices[0]['assigned_slots'][0]);
        $this->assertArrayHasKey('id', $assignedDevices[0]['assigned_slots'][0]);
        $this->assertSame($slot1ID, (int) $assignedDevices[0]['assigned_slots'][0]['id']);
        $this->assertArrayHasKey('type', $assignedDevices[0]['assigned_slots'][0]);
        $this->assertSame(Category::CATS__CHASSIS_SLOT, $assignedDevices[0]['assigned_slots'][0]['type']);
        $this->assertArrayHasKey('title', $assignedDevices[0]['assigned_slots'][0]);
        $this->assertSame('Slot #1', $assignedDevices[0]['assigned_slots'][0]['title']);

        /**
         * Double checks:
         */

        $slots = $this->useCMDBCategory()->read(
            $segmentID,
            Category::CATS__CHASSIS_SLOT
        );

        $this->assertIsArray($slots);
        $this->assertCount(2, $slots);

        $this->assertArrayHasKey(0, $slots);
        $this->assertIsArray($slots[0]);
        $this->isCategoryEntry($slots[0]);
        $this->assertArrayHasKey('id', $slots[0]);
        $this->assertSame($slot1ID, (int) $slots[0]['id']);
        $this->assertSame($segmentID, (int) $slots[0]['objID']);
        $this->assertSame('Slot #1', $slots[0]['title']);
        $this->assertArrayHasKey('assigned_devices', $slots[0]);
        $this->assertIsArray($slots[0]['assigned_devices']);
        $this->assertCount(1, $slots[0]['assigned_devices']);
        $this->assertArrayHasKey(0, $slots[0]['assigned_devices']);
        $this->assertIsArray($slots[0]['assigned_devices'][0]);
        $this->assertArrayHasKey('id', $slots[0]['assigned_devices'][0]);
        $this->assertSame($assignedDeviceID, (int) $slots[0]['assigned_devices'][0]['id']);
        $this->assertArrayHasKey('type', $slots[0]['assigned_devices'][0]);
        $this->assertSame(Category::CATS__CHASSIS_DEVICES, $slots[0]['assigned_devices'][0]['type']);
        $this->assertArrayHasKey('title', $slots[0]['assigned_devices'][0]);
        $this->assertIsString($slots[0]['assigned_devices'][0]['title']);

        $this->assertArrayHasKey(1, $slots);
        $this->assertIsArray($slots[1]);
        $this->isCategoryEntry($slots[1]);
        $this->assertArrayHasKey('id', $slots[1]);
        $this->assertSame($slot2ID, (int) $slots[1]['id']);
        $this->assertSame($segmentID, (int) $slots[1]['objID']);
        $this->assertSame('Slot #2', $slots[1]['title']);
        $this->assertArrayHasKey('assigned_devices', $slots[1]);
        $this->assertIsArray($slots[1]['assigned_devices']);
        $this->assertCount(0, $slots[1]['assigned_devices']);

        $chassisView = $this->useCMDBCategory()->read(
            $segmentID,
            Category::CATS__CHASSIS_VIEW
        );

        $this->assertIsArray($chassisView);
        $this->assertCount(1, $chassisView);
        $this->assertArrayHasKey(0, $chassisView);
        $this->assertIsArray($chassisView[0]);
        $this->isCategoryEntry($chassisView[0]);
        $this->assertSame($chassisViewID, (int) $chassisView[0]['id']);
        $this->assertSame($segmentID, (int) $chassisView[0]['objID']);
    }

}
