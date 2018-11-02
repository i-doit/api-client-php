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
                'title' => 'Port B/1'
            ]
        );
        $this->isID($connectorBID);

        $result = $this->cmdbCategory->save(
            $hostAID,
            'C__CATG__CONNECTOR',
            [
                'assigned_connector' => $connectorBID,
                'cable_connection' => $cableID,
                'used_fiber_lead_rx' => $rxID,
                'used_fiber_lead_tx' => $txID
            ],
            $connectorAID
        );
        $this->isID($result);
        $this->assertSame($connectorAID, $result);

        $wires = $this->cmdbCategory->read($cableID, 'C__CATG__FIBER_LEAD');
        $this->assertInternalType('array', $wires);
        $this->assertCount(2, $wires);

        // @todo Check whether both missing attributes are set properly!
    }

}
