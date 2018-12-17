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
 * @group API-104
 * @see https://i-doit.atlassian.net/browse/API-104
 */
class API104Test extends BaseTest {

    /**
     * @throws \Exception on error
     */
    public function testIssue() {
        $subnetID = $this->createSubnet();

        $hostID = $this->createServer();
        $entryID = $this->addIPv4($hostID, $subnetID);

        $result = $this->cmdbCategory->readOneByID($hostID, 'C__CATG__IP', $entryID);
        $this->assertArrayHasKey('zone', $result);
        // This failed because "zone" was an empty array:
        $this->assertNull($result['zone']);

        // Just an additional check:
        $this->createNetZone($subnetID);
        $result = $this->cmdbCategory->readOneByID($hostID, 'C__CATG__IP', $entryID);
        $this->assertArrayHasKey('zone', $result);
        // Now it's an associative array in PHP/object in JSON:
        $this->assertInternalType('array', $result['zone']);
        $this->isAssignedObject($result['zone']);
    }

    /**
     * Create network zone and assign it to subnet
     *
     * @param int $subnetID Object identifier of subnet
     *
     * @return int Object identifier of net zone
     *
     * @throws \Exception on error
     */
    protected function createNetZone(int $subnetID): int {
        $netZoneID = $this->cmdbObject->create('C__OBJTYPE__NET_ZONE', 'Reserved IP addresses');

        $this->cmdbCategory->create($subnetID, 'C__CATS__NET_ZONE', [
            'zone' => $netZoneID,
            'range_from' => '10.0.0.1',
            'range_to' => '10.255.255.254',
            'description' => $this->generateDescription()
        ]);

        return $netZoneID;
    }

}
