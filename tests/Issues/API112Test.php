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

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group unreleased
 * @see https://i-doit.atlassian.net/browse/API-112
 */
class API112Test extends BaseTest {

    /**
     * @throws \Exception
     */
    public function testIssue() {
        $subnetID = $this->createSubnet();

        $amount = 5;
        $objectIDs = [];

        for ($index = 0; $index < $amount; $index++) {
            $objectID = $this->createServer();
            $this->addIPv4($objectID, $subnetID);
            $objectIDs[] = $objectID;
        }

        $ipList = $this->cmdbCategory->read($subnetID, 'C__CATS__NET_IP_ADDRESSES');

        $this->assertInternalType('array', $ipList);

        // This failed because there were more entries in list than expected:
        $this->assertCount($amount, $ipList);

        foreach ($ipList as $ipAddress) {
            $this->assertInternalType('array', $ipAddress);

            $this->assertArrayHasKey('objID', $ipAddress);
            $objID = (int) $ipAddress['objID'];
            $this->assertSame($subnetID, $objID);

            // This failed because of wrong object relations:
            $this->assertArrayHasKey('object', $ipAddress);
            $this->assertInternalType('array', $ipAddress['object']);
            $this->assertArrayHasKey('id', $ipAddress['object']);
            $id = (int) $ipAddress['object']['id'];
            $this->assertContains($id, $objectIDs);

            $this->assertArrayHasKey('title', $ipAddress['object']);
            $this->assertArrayHasKey('sysid', $ipAddress['object']);
            $this->assertArrayHasKey('type', $ipAddress['object']);
            $this->assertArrayHasKey('type_title', $ipAddress['object']);

            // 'assigned_object' and 'object' are the same:
            $this->assertArrayHasKey('assigned_object', $ipAddress);
            $this->assertInternalType('array', $ipAddress['assigned_object']);
            $this->isAssignedObject($ipAddress['assigned_object']);
            $this->assertSame($ipAddress['object']['id'], $ipAddress['assigned_object']['id']);
            $this->assertSame($ipAddress['object']['title'], $ipAddress['assigned_object']['title']);
            $this->assertSame($ipAddress['object']['sysid'], $ipAddress['assigned_object']['sysid']);
            $this->assertSame($ipAddress['object']['type'], $ipAddress['assigned_object']['type']);
            $this->assertSame($ipAddress['object']['type_title'], $ipAddress['assigned_object']['type_title']);
        }
    }

}
