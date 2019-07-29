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

use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-7
 * @see https://i-doit.atlassian.net/browse/API-7
 */
class API7Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testIssue() {
        $objectType = 'C__OBJTYPE__SWITCH';
        $categoryConstant = 'C__CATG__NETWORK_PORT';

        // Create switch A…
        $switchATitle = 'Switch A';
        $switchAID = $this->useCMDBObject()->create($objectType, $switchATitle);
        $this->isID($switchAID);

        // …with network port…
        $portATitle = 'Port A/01';
        $portAID = $this->useCMDBCategory()->create($switchAID, $categoryConstant, [
            'title' => $portATitle
        ]);
        $this->isID($portAID);

        // …and verify network port:
        $portA = $this->useCMDBCategory()->readOneByID($switchAID, $categoryConstant, $portAID);
        $this->assertIsArray($portA);
        $this->assertArrayHasKey('id', $portA);
        $this->isIDAsString($portA['id']);
        $this->assertSame($portAID, (int) $portA['id']);
        $this->assertArrayHasKey('objID', $portA);
        $this->isIDAsString($portA['objID']);
        $this->assertSame($switchAID, (int) $portA['objID']);
        $this->assertArrayHasKey('title', $portA);
        $this->assertSame($portATitle, $portA['title']);
        $this->assertArrayHasKey('connector', $portA);
        $this->isIDAsString($portA['connector']);

        // Create switch B…
        $switchBTitle = 'Switch B';
        $switchBID = $this->useCMDBObject()->create($objectType, $switchBTitle);
        $this->isID($switchBID);

        // …with network port…
        $portBTitle = 'Port B/01';
        $portBID = $this->useCMDBCategory()->create($switchBID, $categoryConstant, [
            'title' => $portBTitle
        ]);
        $this->isID($portBID);

        //…and verify network port:
        $portB = $this->useCMDBCategory()->readOneByID($switchBID, $categoryConstant, $portBID);
        $this->assertIsArray($portB);
        $this->assertArrayHasKey('id', $portB);
        $this->isIDAsString($portB['id']);
        $this->assertSame($portBID, (int) $portB['id']);
        $this->assertArrayHasKey('objID', $portB);
        $this->isIDAsString($portB['objID']);
        $this->assertSame($switchBID, (int) $portB['objID']);
        $this->assertArrayHasKey('title', $portB);
        $this->assertSame($portBTitle, $portB['title']);
        $this->assertArrayHasKey('connector', $portB);
        $this->isIDAsString($portB['connector']);

        // Connect both ports…
        $connectorBID = (int) $portB['connector'];
        $this->useCMDBCategory()->update($switchAID, $categoryConstant, [
            'assigned_connector' => $connectorBID
        ], $portAID);

        // …and verify connection between them on switch A…
        $portsA = $this->useCMDBCategory()->read($switchAID, $categoryConstant);

        $this->assertIsArray($portA);
        $this->assertCount(1, $portsA);
        $this->assertArrayHasKey(0, $portsA);
        $this->assertIsArray($portsA[0]);
        $this->assertArrayHasKey('id', $portsA[0]);
        $this->isIDAsString($portsA[0]['id']);
        $this->assertSame($portAID, (int) $portsA[0]['id']);
        $this->assertArrayHasKey('objID', $portsA[0]);
        $this->isIDAsString($portsA[0]['objID']);
        $this->assertSame($switchAID, (int) $portsA[0]['objID']);
        $this->assertArrayHasKey('title', $portsA[0]);
        $this->assertSame($portATitle, $portsA[0]['title']);
        $this->assertArrayHasKey('connector', $portsA[0]);
        $this->isIDAsString($portsA[0]['connector']);
        $this->assertSame($portA['connector'], $portsA[0]['connector']);
        $this->assertArrayHasKey('assigned_connector', $portsA[0]);
        $this->assertIsArray($portsA[0]['assigned_connector']);
        $this->assertArrayHasKey(0, $portsA[0]['assigned_connector']);
        $this->assertIsArray($portsA[0]['assigned_connector'][0]);
        $this->assertArrayHasKey('name', $portsA[0]['assigned_connector'][0]);
        $this->assertIsString($portsA[0]['assigned_connector'][0]['name']);
        $this->assertSame($portBTitle, $portsA[0]['assigned_connector'][0]['name']);
        $this->assertArrayHasKey('id', $portsA[0]['assigned_connector'][0]);
        $this->isIDAsString($portsA[0]['assigned_connector'][0]['id']);
        $this->assertSame($switchBID, (int) $portsA[0]['assigned_connector'][0]['id']);
        $this->assertArrayHasKey('title', $portsA[0]['assigned_connector'][0]);
        $this->assertIsString($portsA[0]['assigned_connector'][0]['title']);
        $this->assertSame($switchBTitle, $portsA[0]['assigned_connector'][0]['title']);
        $this->assertArrayHasKey('type', $portsA[0]['assigned_connector'][0]);
        $this->assertIsString($portsA[0]['assigned_connector'][0]['type']);
        $this->assertSame($objectType, $portsA[0]['assigned_connector'][0]['type']);
        $this->assertArrayHasKey('assigned_category', $portsA[0]['assigned_connector'][0]);
        $this->assertIsString($portsA[0]['assigned_connector'][0]['assigned_category']);
        $this->assertSame($categoryConstant, $portsA[0]['assigned_connector'][0]['assigned_category']);

        $connectorsA = $this->useCMDBCategory()->read($switchAID, 'C__CATG__CONNECTOR');

        $this->assertIsArray($connectorsA);
        $this->assertCount(1, $connectorsA);
        $this->assertArrayHasKey(0, $connectorsA);
        $this->assertIsArray($connectorsA[0]);
        $this->assertArrayHasKey('id', $connectorsA[0]);
        $this->isIDAsString($connectorsA[0]['id']);
        $this->assertSame($portA['connector'], $connectorsA[0]['id']);
        $this->assertArrayHasKey('objID', $connectorsA[0]);
        $this->isIDAsString($connectorsA[0]['objID']);
        $this->assertSame($switchAID, (int) $connectorsA[0]['objID']);
        $this->assertArrayHasKey('assigned_category', $connectorsA[0]);
        $this->assertIsArray($connectorsA[0]['assigned_category']);
        $this->assertArrayHasKey('value', $connectorsA[0]['assigned_category']);
        $this->isIDAsString($connectorsA[0]['assigned_category']['value']);
        $this->assertArrayHasKey('assigned_connector', $connectorsA[0]);
        $this->assertIsArray($connectorsA[0]['assigned_connector']);
        $this->assertArrayHasKey(0, $connectorsA[0]['assigned_connector']);
        $this->assertIsArray($connectorsA[0]['assigned_connector'][0]);
        $this->assertArrayHasKey('name', $connectorsA[0]['assigned_connector'][0]);
        $this->assertIsString($connectorsA[0]['assigned_connector'][0]['name']);
        $this->assertSame($portBTitle, $connectorsA[0]['assigned_connector'][0]['name']);
        $this->assertArrayHasKey('id', $connectorsA[0]['assigned_connector'][0]);
        $this->isIDAsString($connectorsA[0]['assigned_connector'][0]['id']);
        $this->assertSame($switchBID, (int) $connectorsA[0]['assigned_connector'][0]['id']);
        $this->assertArrayHasKey('title', $connectorsA[0]['assigned_connector'][0]);
        $this->assertIsString($connectorsA[0]['assigned_connector'][0]['title']);
        $this->assertSame($switchBTitle, $connectorsA[0]['assigned_connector'][0]['title']);
        $this->assertArrayHasKey('type', $connectorsA[0]['assigned_connector'][0]);
        $this->assertIsString($connectorsA[0]['assigned_connector'][0]['type']);
        $this->assertSame($objectType, $connectorsA[0]['assigned_connector'][0]['type']);
        $this->assertArrayHasKey('assigned_category', $connectorsA[0]['assigned_connector'][0]);
        $this->assertIsString($connectorsA[0]['assigned_connector'][0]['assigned_category']);
        $this->assertSame($categoryConstant, $connectorsA[0]['assigned_connector'][0]['assigned_category']);

        // …and switch B:
        $portsB = $this->useCMDBCategory()->read($switchBID, $categoryConstant);

        $this->assertIsArray($portB);
        $this->assertCount(1, $portsB);
        $this->assertArrayHasKey(0, $portsB);
        $this->assertIsArray($portsB[0]);
        $this->assertArrayHasKey('id', $portsB[0]);
        $this->isIDAsString($portsB[0]['id']);
        $this->assertSame($portBID, (int) $portsB[0]['id']);
        $this->assertArrayHasKey('objID', $portsB[0]);
        $this->isIDAsString($portsB[0]['objID']);
        $this->assertSame($switchBID, (int) $portsB[0]['objID']);
        $this->assertArrayHasKey('title', $portsB[0]);
        $this->assertSame($portBTitle, $portsB[0]['title']);
        $this->assertArrayHasKey('connector', $portsB[0]);
        $this->isIDAsString($portsB[0]['connector']);
        $this->assertSame($portB['connector'], $portsB[0]['connector']);
        $this->assertArrayHasKey('assigned_connector', $portsB[0]);
        $this->assertIsArray($portsB[0]['assigned_connector']);
        $this->assertArrayHasKey(0, $portsB[0]['assigned_connector']);
        $this->assertIsArray($portsB[0]['assigned_connector'][0]);
        $this->assertArrayHasKey('name', $portsB[0]['assigned_connector'][0]);
        $this->assertIsString($portsB[0]['assigned_connector'][0]['name']);
        $this->assertSame($portATitle, $portsB[0]['assigned_connector'][0]['name']);
        $this->assertArrayHasKey('id', $portsB[0]['assigned_connector'][0]);
        $this->isIDAsString($portsB[0]['assigned_connector'][0]['id']);
        $this->assertSame($switchAID, (int) $portsB[0]['assigned_connector'][0]['id']);
        $this->assertArrayHasKey('title', $portsB[0]['assigned_connector'][0]);
        $this->assertIsString($portsB[0]['assigned_connector'][0]['title']);
        $this->assertSame($switchATitle, $portsB[0]['assigned_connector'][0]['title']);
        $this->assertArrayHasKey('type', $portsB[0]['assigned_connector'][0]);
        $this->assertIsString($portsB[0]['assigned_connector'][0]['type']);
        $this->assertSame($objectType, $portsB[0]['assigned_connector'][0]['type']);
        $this->assertArrayHasKey('assigned_category', $portsB[0]['assigned_connector'][0]);
        $this->assertIsString($portsB[0]['assigned_connector'][0]['assigned_category']);
        $this->assertSame($categoryConstant, $portsB[0]['assigned_connector'][0]['assigned_category']);

        $connectorsB = $this->useCMDBCategory()->read($switchBID, 'C__CATG__CONNECTOR');

        $this->assertIsArray($connectorsB);
        $this->assertCount(1, $connectorsB);
        $this->assertArrayHasKey(0, $connectorsB);
        $this->assertIsArray($connectorsB[0]);
        $this->assertArrayHasKey('id', $connectorsB[0]);
        $this->isIDAsString($connectorsB[0]['id']);
        $this->assertSame($portB['connector'], $connectorsB[0]['id']);
        $this->assertArrayHasKey('objID', $connectorsB[0]);
        $this->isIDAsString($connectorsB[0]['objID']);
        $this->assertSame($switchBID, (int) $connectorsB[0]['objID']);
        $this->assertArrayHasKey('assigned_category', $connectorsB[0]);
        $this->assertIsArray($connectorsB[0]['assigned_category']);
        $this->assertArrayHasKey('value', $connectorsB[0]['assigned_category']);
        $this->isIDAsString($connectorsB[0]['assigned_category']['value']);
        $this->assertArrayHasKey('assigned_connector', $connectorsB[0]);
        $this->assertIsArray($connectorsB[0]['assigned_connector']);
        $this->assertArrayHasKey(0, $connectorsB[0]['assigned_connector']);
        $this->assertIsArray($connectorsB[0]['assigned_connector'][0]);
        $this->assertArrayHasKey('name', $connectorsB[0]['assigned_connector'][0]);
        $this->assertIsString($connectorsB[0]['assigned_connector'][0]['name']);
        $this->assertSame($portATitle, $connectorsB[0]['assigned_connector'][0]['name']);
        $this->assertArrayHasKey('id', $connectorsB[0]['assigned_connector'][0]);
        $this->isIDAsString($connectorsB[0]['assigned_connector'][0]['id']);
        $this->assertSame($switchAID, (int) $connectorsB[0]['assigned_connector'][0]['id']);
        $this->assertArrayHasKey('title', $connectorsB[0]['assigned_connector'][0]);
        $this->assertIsString($connectorsB[0]['assigned_connector'][0]['title']);
        $this->assertSame($switchATitle, $connectorsB[0]['assigned_connector'][0]['title']);
        $this->assertArrayHasKey('type', $connectorsB[0]['assigned_connector'][0]);
        $this->assertIsString($connectorsB[0]['assigned_connector'][0]['type']);
        $this->assertSame($objectType, $connectorsB[0]['assigned_connector'][0]['type']);
        $this->assertArrayHasKey('assigned_category', $connectorsB[0]['assigned_connector'][0]);
        $this->assertIsString($connectorsB[0]['assigned_connector'][0]['assigned_category']);
        $this->assertSame($categoryConstant, $connectorsB[0]['assigned_connector'][0]['assigned_category']);
    }

}
