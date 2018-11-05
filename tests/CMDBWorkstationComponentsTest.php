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

declare(strict_types=1);

namespace bheisig\idoitapi\tests;

use bheisig\idoitapi\CMDBWorkstationComponents;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBWorkstationComponents
 */
class CMDBWorkstationComponentsTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBWorkstationComponents
     */
    protected $cmdbWorkstationComponents;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->cmdbWorkstationComponents = new CMDBWorkstationComponents($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testRead() {
        $person = $this->createPerson();
        $workstationID = $this->createWorkstation();
        $this->addWorkstationComponent($workstationID, 'C__OBJTYPE__CLIENT');
        $this->addWorkstationComponent($workstationID, 'C__OBJTYPE__MONITOR');
        $this->addWorkstationComponent($workstationID, 'C__OBJTYPE__MONITOR');
        $this->addWorkstationComponent($workstationID, 'C__OBJTYPE__VOIP_PHONE');
        $this->addPersonToWorkstation($person['id'], $workstationID);

        $result = $this->cmdbWorkstationComponents->read($person['id']);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->checkResult($result);
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchRead() {
        $person1 = $this->createPerson();
        $workstation1ID = $this->createWorkstation();
        $this->addWorkstationComponent($workstation1ID, 'C__OBJTYPE__CLIENT');
        $this->addWorkstationComponent($workstation1ID, 'C__OBJTYPE__MONITOR');
        $this->addWorkstationComponent($workstation1ID, 'C__OBJTYPE__MONITOR');
        $this->addWorkstationComponent($workstation1ID, 'C__OBJTYPE__VOIP_PHONE');
        $this->addPersonToWorkstation($person1['id'], $workstation1ID);

        $person2 = $this->createPerson();
        $workstation2ID = $this->createWorkstation();
        $this->addWorkstationComponent($workstation2ID, 'C__OBJTYPE__CLIENT');
        $this->addWorkstationComponent($workstation2ID, 'C__OBJTYPE__MONITOR');
        $this->addWorkstationComponent($workstation2ID, 'C__OBJTYPE__MONITOR');
        $this->addWorkstationComponent($workstation2ID, 'C__OBJTYPE__VOIP_PHONE');
        $this->addPersonToWorkstation($person2['id'], $workstation2ID);

        $result = $this->cmdbWorkstationComponents->batchRead([$person1['id'], $person2['id']]);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->checkResult($result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByEmail() {
        $person = $this->createPerson();
        $workstationID = $this->createWorkstation();
        $this->addWorkstationComponent($workstationID, 'C__OBJTYPE__CLIENT');
        $this->addWorkstationComponent($workstationID, 'C__OBJTYPE__MONITOR');
        $this->addWorkstationComponent($workstationID, 'C__OBJTYPE__MONITOR');
        $this->addWorkstationComponent($workstationID, 'C__OBJTYPE__VOIP_PHONE');
        $this->addPersonToWorkstation($person['id'], $workstationID);

        $result = $this->cmdbWorkstationComponents->readByEmail($person['email']);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->checkResult($result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByEmails() {
        $person1 = $this->createPerson();
        $workstation1ID = $this->createWorkstation();
        $this->addWorkstationComponent($workstation1ID, 'C__OBJTYPE__CLIENT');
        $this->addWorkstationComponent($workstation1ID, 'C__OBJTYPE__MONITOR');
        $this->addWorkstationComponent($workstation1ID, 'C__OBJTYPE__MONITOR');
        $this->addWorkstationComponent($workstation1ID, 'C__OBJTYPE__VOIP_PHONE');
        $this->addPersonToWorkstation($person1['id'], $workstation1ID);

        $person2 = $this->createPerson();
        $workstation2ID = $this->createWorkstation();
        $this->addWorkstationComponent($workstation2ID, 'C__OBJTYPE__CLIENT');
        $this->addWorkstationComponent($workstation2ID, 'C__OBJTYPE__MONITOR');
        $this->addWorkstationComponent($workstation2ID, 'C__OBJTYPE__MONITOR');
        $this->addWorkstationComponent($workstation2ID, 'C__OBJTYPE__VOIP_PHONE');
        $this->addPersonToWorkstation($person2['id'], $workstation2ID);

        $result = $this->cmdbWorkstationComponents->readByEmails([$person1['email'], $person2['email']]);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->checkResult($result);
    }

    /**
     * @group unreleased
     * @group API-71
     * @throws \Exception on error
     */
    public function testReadByStatusNormal() {
        $person = $this->createPerson();
        $workstationID = $this->createWorkstation();
        $this->addWorkstationComponent($workstationID, 'C__OBJTYPE__CLIENT');
        $this->addPersonToWorkstation($person['id'], $workstationID);

        $result = $this->cmdbWorkstationComponents->read($person['id'], 2);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->checkResult($result);
    }

    /**
     * @group unreleased
     * @group API-71
     * @throws \Exception on error
     */
    public function testReadByStatusArchived() {
        $person = $this->createPerson();
        $workstationID = $this->createWorkstation();

        $componentID = $this->cmdbObject->create(
            'C__OBJTYPE__CLIENT',
            $this->generateRandomString()
        );
        $workstationComponentID = $this->cmdbCategory->create(
            $componentID,
            'C__CATG__ASSIGNED_WORKSTATION',
            [
                'parent' => $workstationID,
                'description' => $this->generateDescription()
            ]
        );
        $this->cmdbCategory->archive($componentID, 'C__CATG__ASSIGNED_WORKSTATION', $workstationComponentID);

        $assignedWorkstationID = $this->addPersonToWorkstation($person['id'], $workstationID);
        $this->cmdbCategory->archive($workstationID, 'C__CATG__LOGICAL_UNIT', $assignedWorkstationID);

        $result = $this->cmdbWorkstationComponents->read($person['id'], 3);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->checkResult($result);

        $result = $this->cmdbWorkstationComponents->read($person['id'], 2);

        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

    /**
     * @group unreleased
     * @group API-71
     * @throws \Exception on error
     */
    public function testReadByStatusDeleted() {
        $person = $this->createPerson();
        $workstationID = $this->createWorkstation();

        $componentID = $this->cmdbObject->create(
            'C__OBJTYPE__CLIENT',
            $this->generateRandomString()
        );
        $workstationComponentID = $this->cmdbCategory->create(
            $componentID,
            'C__CATG__ASSIGNED_WORKSTATION',
            [
                'parent' => $workstationID,
                'description' => $this->generateDescription()
            ]
        );
        $this->cmdbCategory->delete($componentID, 'C__CATG__ASSIGNED_WORKSTATION', $workstationComponentID);

        $assignedWorkstationID = $this->addPersonToWorkstation($person['id'], $workstationID);
        $this->cmdbCategory->delete($workstationID, 'C__CATG__LOGICAL_UNIT', $assignedWorkstationID);

        $result = $this->cmdbWorkstationComponents->read($person['id'], 4);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->checkResult($result);

        $result = $this->cmdbWorkstationComponents->read($person['id'], 2);

        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

    /**
     * @return array
     */
    public function getInvalidStatus(): array {
        return [
            'negative' => [-1],
            'zero' => [0],
            'unfinished' => [1],
            'purged' => [5],
            'template' => [6],
            'mass change template' => [7]
        ];
    }

    /**
     * @group unreleased
     * @group API-71
     * @dataProvider getInvalidStatus
     * @param int $status
     * @expectedException \RuntimeException
     * @throws \Exception on error
     * @doesNotPerformAssertions
     */
    public function testReadByInvalidStatus(int $status) {
        $person = $this->createPerson();
        $workstationID = $this->createWorkstation();
        $this->addWorkstationComponent($workstationID, 'C__OBJTYPE__CLIENT');
        $this->addPersonToWorkstation($person['id'], $workstationID);

        $this->cmdbWorkstationComponents->read($person['id'], $status);
    }

    /**
     * Validate result
     *
     * @param array $result
     */
    protected function checkResult(array $result) {
        foreach ($result as $person) {
            $this->assertInternalType('array', $person);

            $this->assertArrayHasKey('data', $person);
            $this->assertInternalType('array', $person['data']);
            $this->assertNotCount(0, $person['data']);

            $this->assertArrayHasKey('children', $person);
            $this->assertInternalType('array', $person['children']);
            $this->assertCount(1, $person['children']);

            foreach ($person['children'] as $workstation) {
                $this->assertArrayHasKey('data', $workstation);
                $this->assertInternalType('array', $workstation['data']);
                $this->assertNotCount(0, $workstation['data']);

                $this->assertArrayHasKey('children', $workstation);
                $this->assertInternalType('array', $workstation['children']);
                $this->assertCount(4, $workstation['children']);

                foreach ($workstation['children'] as $id => $component) {
                    $this->assertInternalType('integer', $id);
                    $this->assertGreaterThan(0, $id);

                    $this->assertInternalType('array', $component);

                    $this->assertArrayHasKey('data', $component);
                    $this->assertInternalType('array', $component['data']);
                    $this->assertNotCount(0, $component['data']);

                    $this->assertArrayHasKey('children', $component);
                    $this->assertInternalType('boolean', $component['children']);
                    $this->assertFalse($component['children']);
                }
            }
        }
    }

}
