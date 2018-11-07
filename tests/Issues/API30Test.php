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
 * @group API-30
 * @see https://i-doit.atlassian.net/browse/API-30
 */
class API30Test extends BaseTest {

    /**
     * @throws \Exception on error
     */
    public function testIssue() {
        $hostAID = $this->cmdbObject->create(
            'C__OBJTYPE__SERVER',
            'Host A'
        );
        $this->isID($hostAID);

        $hostBID = $this->cmdbObject->create(
            'C__OBJTYPE__SERVER',
            'Host B'
        );
        $this->isID($hostBID);

        $cableID = $this->cmdbObject->create(
            'C__OBJTYPE__CABLE',
            'Cabel A <=> B'
        );
        $this->isID($cableID);

        $rxID = $this->cmdbCategory->save(
            $cableID,
            'C__CATG__FIBER_LEAD',
            [
                'label' => 'rx',
                'color' => 'black',
                'description' => $this->generateDescription()
            ]
        );
        $this->isID($rxID);

        $txID = $this->cmdbCategory->save(
            $cableID,
            'C__CATG__FIBER_LEAD',
            [
                'label' => 'tx',
                'color' => 'white',
                'description' => $this->generateDescription()
            ]
        );
        $this->isID($txID);

        $connectorAID = $this->cmdbCategory->save(
            $hostAID,
            'C__CATG__CONNECTOR',
            [
                'title' => 'Port A/1'
            ]
        );
        $this->isID($connectorAID);

        $connectorBID = $this->cmdbCategory->save(
            $hostBID,
            'C__CATG__CONNECTOR',
            [
                'title' => 'Port B/1',
                // It's important to assign connector and cable first…
                'assigned_connector' => $connectorAID,
                'cable_connection' => $cableID
            ]
        );
        $this->isID($connectorBID);

        $result = $this->cmdbCategory->save(
            $hostAID,
            'C__CATG__CONNECTOR',
            [
                // … before selecting wich wire is rx/tx:
                'used_fiber_lead_rx' => $rxID,
                'used_fiber_lead_tx' => $txID
            ],
            $connectorAID
        );
        $this->isID($result);
        $this->assertSame($connectorAID, $result);

        // Verify both wires:
        $wires = $this->cmdbCategory->read($cableID, 'C__CATG__FIBER_LEAD');
        $this->assertInternalType('array', $wires);
        $this->assertCount(2, $wires);

        $this->assertArrayHasKey(0, $wires);
        $this->assertInternalType('array', $wires[0]);
        $this->assertArrayHasKey('id', $wires[0]);
        $this->isIDAsString($wires[0]['id']);
        $this->assertSame($rxID, (int) $wires[0]['id']);
        $this->assertArrayHasKey('objID', $wires[0]);
        $this->isIDAsString($wires[0]['objID']);
        $this->assertSame($cableID, (int) $wires[0]['objID']);
        $this->assertArrayHasKey('label', $wires[0]);
        $this->assertSame('rx', $wires[0]['label']);

        $this->assertArrayHasKey(1, $wires);
        $this->assertInternalType('array', $wires[1]);
        $this->assertArrayHasKey('id', $wires[1]);
        $this->isIDAsString($wires[1]['id']);
        $this->assertSame($txID, (int) $wires[1]['id']);
        $this->assertArrayHasKey('objID', $wires[1]);
        $this->isIDAsString($wires[1]['objID']);
        $this->assertSame($cableID, (int) $wires[1]['objID']);
        $this->assertArrayHasKey('label', $wires[1]);
        $this->assertSame('tx', $wires[1]['label']);

        // Verify first connector:
        $connectorA = $this->cmdbCategory->readOneByID(
            $hostAID,
            'C__CATG__CONNECTOR',
            $connectorAID
        );

        $this->assertInternalType('array', $connectorA);
        $this->assertArrayHasKey('id', $connectorA);
        $this->isIDAsString($connectorA['id']);
        $this->assertSame($connectorAID, (int) $connectorA['id']);
        $this->assertArrayHasKey('objID', $connectorA);
        $this->isIDAsString($connectorA['objID']);
        $this->assertSame($hostAID, (int) $connectorA['objID']);

        $this->assertArrayHasKey('used_fiber_lead_rx', $connectorA);
        $this->assertInternalType('array', $connectorA['used_fiber_lead_rx']);
        $this->assertArrayHasKey('id', $connectorA['used_fiber_lead_rx']);
        $this->assertSame($rxID, (int) $connectorA['used_fiber_lead_rx']['id']);

        $this->assertArrayHasKey('used_fiber_lead_tx', $connectorA);
        $this->assertInternalType('array', $connectorA['used_fiber_lead_tx']);
        $this->assertArrayHasKey('id', $connectorA['used_fiber_lead_tx']);
        $this->assertSame($txID, (int) $connectorA['used_fiber_lead_tx']['id']);

        // Verify second connector:
        $connectorB = $this->cmdbCategory->readOneByID(
            $hostBID,
            'C__CATG__CONNECTOR',
            $connectorBID
        );

        $this->assertInternalType('array', $connectorB);
        $this->assertArrayHasKey('id', $connectorB);
        $this->isIDAsString($connectorB['id']);
        $this->assertSame($connectorBID, (int) $connectorB['id']);
        $this->assertArrayHasKey('objID', $connectorB);
        $this->isIDAsString($connectorB['objID']);
        $this->assertSame($hostBID, (int) $connectorB['objID']);

        $this->assertArrayHasKey('used_fiber_lead_rx', $connectorB);
        $this->assertInternalType('array', $connectorB['used_fiber_lead_rx']);
        $this->assertArrayHasKey('id', $connectorB['used_fiber_lead_rx']);
        $this->assertSame($txID, (int) $connectorB['used_fiber_lead_rx']['id']);

        $this->assertArrayHasKey('used_fiber_lead_tx', $connectorB);
        $this->assertInternalType('array', $connectorB['used_fiber_lead_tx']);
        $this->assertArrayHasKey('id', $connectorB['used_fiber_lead_tx']);
        $this->assertSame($rxID, (int) $connectorB['used_fiber_lead_tx']['id']);
    }

}
