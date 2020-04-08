<?php

/**
 * Copyright (C) 2016-2020 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-2020 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\tests\Constants\Category;
use bheisig\idoitapi\tests\Constants\ObjectType;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group unreleased
 * @group issues
 * @group API-146
 * @see https://i-doit.atlassian.net/browse/API-146
 */
class API146Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testAddClusterMember() {
        /**
         * Create test data:
         */

        $hostID = $this->createServer();
        $this->isID($hostID);

        $clusterID = $this->useCMDBObject()->create(
            ObjectType::CLUSTER,
            $this->generateRandomString()
        );
        $this->isID($clusterID);

        $this->useCMDBCategory()->save(
            $clusterID,
            Category::CATG__CLUSTER_MEMBERS,
            [
                'member' => [
                    $hostID
                ]
            ]
        );

        /**
         * Run tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $clusterID,
            Category::CATG__CLUSTER_MEMBERS
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        $this->assertArrayHasKey('member', $entries[0]);
        $this->assertIsArray($entries[0]['member']);
        $this->isAssignedObject($entries[0]['member']);
        $this->assertArrayHasKey('id', $entries[0]['member']);
        $this->assertSame($hostID, (int) $entries[0]['member']['id']);
    }

}
