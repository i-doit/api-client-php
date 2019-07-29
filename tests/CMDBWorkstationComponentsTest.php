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
use bheisig\idoitapi\CMDBWorkstationComponents;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBWorkstationComponents
 */
class CMDBWorkstationComponentsTest extends BaseTest {

    /**
     * @var CMDBWorkstationComponents
     */
    protected $cmdbWorkstationComponents;

    /**
     * @throws Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->cmdbWorkstationComponents = new CMDBWorkstationComponents($this->api);
    }

    /**
     * @throws Exception on error
     */
    public function testRead() {
        $person = $this->createPerson();
        $workstationID = $this->createWorkstation();
        $this->addWorkstationComponent($workstationID, ObjectType::CLIENT);
        $this->addWorkstationComponent($workstationID, ObjectType::MONITOR);
        $this->addWorkstationComponent($workstationID, ObjectType::MONITOR);
        $this->addWorkstationComponent($workstationID, ObjectType::VOIP_PHONE);
        $this->addPersonToWorkstation($person['id'], $workstationID);

        $result = $this->cmdbWorkstationComponents->read($person['id']);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        $this->checkResult($result);
    }

    /**
     * @throws Exception on error
     */
    public function testBatchRead() {
        $person1 = $this->createPerson();
        $workstation1ID = $this->createWorkstation();
        $this->addWorkstationComponent($workstation1ID, ObjectType::CLIENT);
        $this->addWorkstationComponent($workstation1ID, ObjectType::MONITOR);
        $this->addWorkstationComponent($workstation1ID, ObjectType::MONITOR);
        $this->addWorkstationComponent($workstation1ID, ObjectType::VOIP_PHONE);
        $this->addPersonToWorkstation($person1['id'], $workstation1ID);

        $person2 = $this->createPerson();
        $workstation2ID = $this->createWorkstation();
        $this->addWorkstationComponent($workstation2ID, ObjectType::CLIENT);
        $this->addWorkstationComponent($workstation2ID, ObjectType::MONITOR);
        $this->addWorkstationComponent($workstation2ID, ObjectType::MONITOR);
        $this->addWorkstationComponent($workstation2ID, ObjectType::VOIP_PHONE);
        $this->addPersonToWorkstation($person2['id'], $workstation2ID);

        $result = $this->cmdbWorkstationComponents->batchRead([$person1['id'], $person2['id']]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $this->checkResult($result);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByEmail() {
        $person = $this->createPerson();
        $workstationID = $this->createWorkstation();
        $this->addWorkstationComponent($workstationID, ObjectType::CLIENT);
        $this->addWorkstationComponent($workstationID, ObjectType::MONITOR);
        $this->addWorkstationComponent($workstationID, ObjectType::MONITOR);
        $this->addWorkstationComponent($workstationID, ObjectType::VOIP_PHONE);
        $this->addPersonToWorkstation($person['id'], $workstationID);

        $result = $this->cmdbWorkstationComponents->readByEmail($person['email']);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        $this->checkResult($result);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByEmails() {
        $person1 = $this->createPerson();
        $workstation1ID = $this->createWorkstation();
        $this->addWorkstationComponent($workstation1ID, ObjectType::CLIENT);
        $this->addWorkstationComponent($workstation1ID, ObjectType::MONITOR);
        $this->addWorkstationComponent($workstation1ID, ObjectType::MONITOR);
        $this->addWorkstationComponent($workstation1ID, ObjectType::VOIP_PHONE);
        $this->addPersonToWorkstation($person1['id'], $workstation1ID);

        $person2 = $this->createPerson();
        $workstation2ID = $this->createWorkstation();
        $this->addWorkstationComponent($workstation2ID, ObjectType::CLIENT);
        $this->addWorkstationComponent($workstation2ID, ObjectType::MONITOR);
        $this->addWorkstationComponent($workstation2ID, ObjectType::MONITOR);
        $this->addWorkstationComponent($workstation2ID, ObjectType::VOIP_PHONE);
        $this->addPersonToWorkstation($person2['id'], $workstation2ID);

        $result = $this->cmdbWorkstationComponents->readByEmails([$person1['email'], $person2['email']]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $this->checkResult($result);
    }

    /**
     * Validate result
     *
     * @param array $result
     */
    protected function checkResult(array $result) {
        foreach ($result as $person) {
            $this->assertIsArray($person);

            $this->assertArrayHasKey('data', $person);
            $this->assertIsArray($person['data']);
            $this->assertNotCount(0, $person['data']);

            $this->assertArrayHasKey('children', $person);
            $this->assertIsArray($person['children']);
            $this->assertCount(1, $person['children']);

            foreach ($person['children'] as $workstation) {
                $this->assertArrayHasKey('data', $workstation);
                $this->assertIsArray($workstation['data']);
                $this->assertNotCount(0, $workstation['data']);

                $this->assertArrayHasKey('children', $workstation);
                $this->assertIsArray($workstation['children']);
                $this->assertNotCount(0, $workstation['children']);

                foreach ($workstation['children'] as $id => $component) {
                    $this->assertIsInt($id);
                    $this->assertGreaterThan(0, $id);

                    $this->assertIsArray($component);

                    $this->assertArrayHasKey('data', $component);
                    $this->assertIsArray($component['data']);
                    $this->assertNotCount(0, $component['data']);

                    $this->assertArrayHasKey('children', $component);
                    $this->assertIsBool($component['children']);
                    $this->assertFalse($component['children']);
                }
            }
        }
    }

}
