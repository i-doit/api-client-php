<?php

/**
 * Copyright (C) 2022 synetics GmbH
 * Copyright (C) 2016-2022 Benjamin Heisig
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
 * @copyright Copyright (C) 2022 synetics GmbH
 * @copyright Copyright (C) 2016-2022 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi\Issues;

use \Exception;
use bheisig\idoitapi\BaseTest;
use bheisig\idoitapi\Constants\Category;
use bheisig\idoitapi\Constants\ObjectType;

/**
 * @group issues
 * @group API-129
 * @see https://i-doit.atlassian.net/browse/API-129
 */
class API129Test extends BaseTest {

    /**
     * @throws Exception
     */
    public function testIssue() {
        $serverID = $this->useCMDBObject()->create(ObjectType::SERVER, 'My little server');
        $this->assertIsInt($serverID);
        $this->assertGreaterThan(0, $serverID);

        $rmcID = $this->useCMDBObject()->create(ObjectType::RM_CONTROLLER, 'RMC for my little server');
        $this->assertIsInt($rmcID);
        $this->assertGreaterThan(0, $rmcID);

        $entryID = $this->useCMDBCategory()->create(
            $serverID,
            Category::CATG__RM_CONTROLLER,
            ['connected_object' => $rmcID]
        );
        $this->assertIsInt($entryID);
        $this->assertGreaterThan(0, $entryID);

        $assignedController = $this->useCMDBCategory()->readOneByID($serverID, Category::CATG__RM_CONTROLLER, $entryID);
        $this->assertIsArray($assignedController);
        $this->assertArrayHasKey('id', $assignedController);
        $id = (int) $assignedController['id'];
        $this->assertGreaterThan(0, $id);
        $this->assertArrayHasKey('objID', $assignedController);
        $objID = (int) $assignedController['objID'];
        $this->assertGreaterThan(0, $objID);
        $this->assertSame($serverID, $objID);
        $this->assertArrayHasKey('connected_object', $assignedController);
        $this->assertIsArray($assignedController['connected_object']);
        $this->assertArrayHasKey('id', $assignedController['connected_object']);
        $connectedObject = (int) $assignedController['connected_object']['id'];
        $this->assertGreaterThan(0, $connectedObject);
        $this->assertSame($rmcID, $connectedObject);

        $assignedObjects = $this->useCMDBCategory()->read($rmcID, Category::CATG__RM_CONTROLLER_BACKWARD);
        $this->assertIsArray($assignedObjects);
        $this->assertCount(1, $assignedObjects);
        $this->assertArrayHasKey(0, $assignedObjects);
        $this->assertIsArray($assignedObjects[0]);
        $this->assertArrayHasKey('connected_object', $assignedObjects[0]);
        // This failed because 'connected_object' is null:
        $this->assertIsArray($assignedObjects[0]['connected_object']);
        $this->assertArrayHasKey('id', $assignedObjects[0]['connected_object']);
        $connectedObject = (int) $assignedObjects[0]['connected_object']['id'];
        $this->assertGreaterThan(0, $connectedObject);
        $this->assertSame($serverID, $connectedObject);
    }

}
