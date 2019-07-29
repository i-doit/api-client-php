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
 * @group API-42
 * @see https://i-doit.atlassian.net/browse/API-42
 */
class API42Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testIssue() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        // Add 1st connector:
        $connectorAID = $this->useCMDBCategory()->create(
            $objectID,
            'C__CATG__CONNECTOR',
            [
                'title' => $this->generateRandomString(),
                'description' => $this->generateDescription()
            ]
        );
        $this->isID($connectorAID);

        $result = $this->useCMDBCategory()->readOneByID(
            $objectID,
            'C__CATG__CONNECTOR',
            $connectorAID
        );
        $this->assertIsArray($result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($connectorAID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);

        $this->assertArrayHasKey('cable_connection', $result);
        $this->assertIsArray($result['cable_connection']);
        $this->assertCount(0, $result['cable_connection']);

        // Add 2nd connector:
        $connectorBID = $this->useCMDBCategory()->create(
            $objectID,
            'C__CATG__CONNECTOR',
            [
                'title' => $this->generateRandomString(),
                'description' => $this->generateDescription()
            ]
        );
        $this->isID($connectorBID);

        $result = $this->useCMDBCategory()->readOneByID(
            $objectID,
            'C__CATG__CONNECTOR',
            $connectorBID
        );
        $this->assertIsArray($result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($connectorBID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);

        $this->assertArrayHasKey('cable_connection', $result);
        $this->assertIsArray($result['cable_connection']);
        $this->assertCount(0, $result['cable_connection']);

        // Combine both connectors:
        $this->useCMDBCategory()->update(
            $objectID,
            'C__CATG__CONNECTOR',
            [
                'connector_sibling' => $connectorBID
            ],
            $connectorAID
        );

        // Verify 1st connector has no cable:
        $result = $this->useCMDBCategory()->readOneByID(
            $objectID,
            'C__CATG__CONNECTOR',
            $connectorAID
        );
        $this->assertIsArray($result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($connectorAID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);

        $this->assertArrayHasKey('cable_connection', $result);
        $this->assertIsArray($result['cable_connection']);
        // This failed because an object was accidently assigned:
        $this->assertCount(0, $result['cable_connection']);

        // Verify 2nd connector has no cable:
        $result = $this->useCMDBCategory()->readOneByID(
            $objectID,
            'C__CATG__CONNECTOR',
            $connectorBID
        );
        $this->assertIsArray($result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($connectorBID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);

        $this->assertArrayHasKey('cable_connection', $result);
        $this->assertIsArray($result['cable_connection']);
        // This failed because an object was accidently assigned:
        $this->assertCount(0, $result['cable_connection']);
    }

}
