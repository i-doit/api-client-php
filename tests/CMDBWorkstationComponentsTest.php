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

use bheisig\idoitapi\CMDBWorkstationComponents;

class CMDBWorkstationComponentsTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBWorkstationComponents
     */
    protected $instance;

    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBWorkstationComponents($this->api);
    }

    /**
     * @throws \Exception
     */
    public function testRead() {
        $person = $this->createPerson();
        $workstationID = $this->createWorkstation();
        $this->addPersonToWorkstation($person['id'], $workstationID);

        $result = $this->instance->read($person['id']);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->checkResult($result);
    }

    /**
     * @throws \Exception
     */
    public function testBatchRead() {
        $person1 = $this->createPerson();
        $workstation1ID = $this->createWorkstation();
        $this->addPersonToWorkstation($person1['id'], $workstation1ID);

        $person2 = $this->createPerson();
        $workstation2ID = $this->createWorkstation();
        $this->addPersonToWorkstation($person2['id'], $workstation2ID);

        $result = $this->instance->batchRead([$person1['id'], $person2['id']]);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->checkResult($result);
    }

    /**
     * @throws \Exception
     */
    public function testReadByEMail() {
        $person = $this->createPerson();
        $workstationID = $this->createWorkstation();
        $this->addPersonToWorkstation($person['id'], $workstationID);

        $result = $this->instance->readByEMail($person['email']);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        $this->checkResult($result);
    }

    /**
     * @throws \Exception
     */
    public function testReadByEMails() {
        $person1 = $this->createPerson();
        $workstation1ID = $this->createWorkstation();
        $this->addPersonToWorkstation($person1['id'], $workstation1ID);

        $person2 = $this->createPerson();
        $workstation2ID = $this->createWorkstation();
        $this->addPersonToWorkstation($person2['id'], $workstation2ID);

        $result = $this->instance->readByEMails([$person1['email'], $person2['email']]);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->checkResult($result);
    }

    protected function checkResult($result) {
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
