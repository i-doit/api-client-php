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

namespace bheisig\idoitapi\Issues;

use \Exception;
use bheisig\idoitapi\BaseTest;
use bheisig\idoitapi\Constants\Category;
use bheisig\idoitapi\Constants\ObjectType;

/**
 * @group issues
 * @group API-228
 * @see https://i-doit.atlassian.net/browse/API-228
 */
class API228Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testSave() {
        /**
         * Create test data:
         */

        $vhostID = $this->useCMDBObject()->create(
            ObjectType::VIRTUAL_HOST,
            $this->generateRandomString()
        );
        $this->isID($vhostID);

        $licenseHostID = $this->useCMDBObject()->create(
            ObjectType::HOST,
            $this->generateRandomString()
        );
        $this->isID($licenseHostID);

        $appID = $this->useCMDBObject()->create(
            ObjectType::APPLICATION,
            $this->generateRandomString()
        );
        $this->isID($appID);

        $vmID = $this->useCMDBObject()->create(
            ObjectType::VIRTUAL_SERVER,
            $this->generateRandomString()
        );
        $this->isID($vmID);

        $installationID = $this->useCMDBCategory()->save(
            $vmID,
            Category::CATG__APPLICATION,
            [
                'application' => $appID
            ]
        );
        $this->isID($installationID);

        $relations = $this->useCMDBCategory()->read(
            $vmID,
            Category::CATG__RELATION
        );

        $this->assertIsArray($relations);
        $this->assertCount(1, $relations);
        $this->assertArrayHasKey(0, $relations);
        $this->assertIsArray($relations[0]);

        $this->assertArrayHasKey('object1', $relations[0]);
        $this->assertIsArray($relations[0]['object1']);
        $this->assertArrayHasKey('id', $relations[0]['object1']);
        $this->assertSame($vmID, (int) $relations[0]['object1']['id']);
        $this->assertSame(ObjectType::VIRTUAL_SERVER, $relations[0]['object1']['type']);

        $this->assertArrayHasKey('object2', $relations[0]);
        $this->assertIsArray($relations[0]['object2']);
        $this->assertArrayHasKey('id', $relations[0]['object2']);
        $this->assertSame($appID, (int) $relations[0]['object2']['id']);
        $this->assertSame(ObjectType::APPLICATION, $relations[0]['object2']['type']);

        $this->assertArrayHasKey('relation_type', $relations[0]);
        $this->assertIsArray($relations[0]['relation_type']);
        $this->assertArrayHasKey('const', $relations[0]['relation_type']);
        $this->assertSame('C__RELATION_TYPE__SOFTWARE', $relations[0]['relation_type']['const']);

        $serviceID = (int) $relations[0]['objID'];

        $entryID = $this->useCMDBCategory()->save(
            $vhostID,
            Category::CATG__VIRTUAL_HOST,
            [
                'virtual_host' => 1,
                'license_server' => $licenseHostID,
                'administration_service' => $serviceID
            ]
        );
        $this->isID($entryID);

        /**
         * Run tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $vhostID,
            Category::CATG__VIRTUAL_HOST
        );

        $this->assertIsArray($entries);
        $this->validateEntries($entries, $vhostID, $licenseHostID, $vmID, $appID);

        /**
         * Update entry:
         */

        $this->useCMDBCategory()->save(
            $vhostID,
            Category::CATG__VIRTUAL_HOST,
            [
                'description' => $this->generateDescription()
            ],
            $entryID
        );

        /**
         * Run tests again:
         */

        $entries = $this->useCMDBCategory()->read(
            $vhostID,
            Category::CATG__VIRTUAL_HOST
        );

        $this->assertIsArray($entries);
        $this->validateEntries($entries, $vhostID, $licenseHostID, $vmID, $appID);
    }

    /**
     * @throws Exception on error
     */
    public function testUpdate() {
        /**
         * Create test data:
         */

        $vhostID = $this->useCMDBObject()->create(
            ObjectType::VIRTUAL_HOST,
            $this->generateRandomString()
        );
        $this->isID($vhostID);

        $licenseHostID = $this->useCMDBObject()->create(
            ObjectType::HOST,
            $this->generateRandomString()
        );
        $this->isID($licenseHostID);

        $appID = $this->useCMDBObject()->create(
            ObjectType::APPLICATION,
            $this->generateRandomString()
        );
        $this->isID($appID);

        $vmID = $this->useCMDBObject()->create(
            ObjectType::VIRTUAL_SERVER,
            $this->generateRandomString()
        );
        $this->isID($vmID);

        $installationID = $this->useCMDBCategory()->create(
            $vmID,
            Category::CATG__APPLICATION,
            [
                'application' => $appID
            ]
        );
        $this->isID($installationID);

        $relations = $this->useCMDBCategory()->read(
            $vmID,
            Category::CATG__RELATION
        );

        $this->assertIsArray($relations);
        $this->assertCount(1, $relations);
        $this->assertArrayHasKey(0, $relations);
        $this->assertIsArray($relations[0]);

        $this->assertArrayHasKey('object1', $relations[0]);
        $this->assertIsArray($relations[0]['object1']);
        $this->assertArrayHasKey('id', $relations[0]['object1']);
        $this->assertSame($vmID, (int) $relations[0]['object1']['id']);
        $this->assertSame(ObjectType::VIRTUAL_SERVER, $relations[0]['object1']['type']);

        $this->assertArrayHasKey('object2', $relations[0]);
        $this->assertIsArray($relations[0]['object2']);
        $this->assertArrayHasKey('id', $relations[0]['object2']);
        $this->assertSame($appID, (int) $relations[0]['object2']['id']);
        $this->assertSame(ObjectType::APPLICATION, $relations[0]['object2']['type']);

        $this->assertArrayHasKey('relation_type', $relations[0]);
        $this->assertIsArray($relations[0]['relation_type']);
        $this->assertArrayHasKey('const', $relations[0]['relation_type']);
        $this->assertSame('C__RELATION_TYPE__SOFTWARE', $relations[0]['relation_type']['const']);

        $serviceID = (int) $relations[0]['objID'];

        $entryID = $this->useCMDBCategory()->create(
            $vhostID,
            Category::CATG__VIRTUAL_HOST,
            [
                'virtual_host' => 1,
                'license_server' => $licenseHostID,
                'administration_service' => $serviceID
            ]
        );
        $this->isID($entryID);

        /**
         * Run tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $vhostID,
            Category::CATG__VIRTUAL_HOST
        );

        $this->assertIsArray($entries);
        $this->validateEntries($entries, $vhostID, $licenseHostID, $vmID, $appID);

        /**
         * Update entry:
         */

        $this->useCMDBCategory()->update(
            $vhostID,
            Category::CATG__VIRTUAL_HOST,
            [
                'description' => $this->generateDescription()
            ],
            $entryID
        );

        /**
         * Run tests again:
         */

        $entries = $this->useCMDBCategory()->read(
            $vhostID,
            Category::CATG__VIRTUAL_HOST
        );

        $this->assertIsArray($entries);
        $this->validateEntries($entries, $vhostID, $licenseHostID, $vmID, $appID);
    }

    protected function validateEntries(array $entries, int $vhostID, int $licenseHostID, $vmID, $appID) {
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        $this->assertArrayHasKey('objID', $entries[0]);
        $this->assertSame($vhostID, (int) $entries[0]['objID']);

        $this->assertArrayHasKey('virtual_host', $entries[0]);
        $this->assertIsArray($entries[0]['virtual_host']);
        $this->assertArrayHasKey('value', $entries[0]['virtual_host']);
        $this->assertSame('1', $entries[0]['virtual_host']['value']);

        $this->assertArrayHasKey('license_server', $entries[0]);
        $this->assertIsArray($entries[0]['license_server']);
        $this->assertArrayHasKey('id', $entries[0]['license_server']);
        $this->assertSame($licenseHostID, (int) $entries[0]['license_server']['id']);

        $this->assertArrayHasKey('administration_service', $entries[0]);
        $this->assertIsArray($entries[0]['administration_service']);
        $this->assertCount(3, $entries[0]['administration_service']);

        $this->assertArrayHasKey(0, $entries[0]['administration_service']);
        $this->assertIsArray($entries[0]['administration_service'][0]);
        $this->assertArrayHasKey('id', $entries[0]['administration_service'][0]);
        $this->assertSame($vmID, (int) $entries[0]['administration_service'][0]['id']);
        $this->assertArrayHasKey('type', $entries[0]['administration_service'][0]);
        $this->assertSame(ObjectType::VIRTUAL_SERVER, $entries[0]['administration_service'][0]['type']);

        $this->assertArrayHasKey(1, $entries[0]['administration_service']);
        $this->assertIsArray($entries[0]['administration_service'][1]);
        $this->assertArrayHasKey('id', $entries[0]['administration_service'][1]);
        $this->assertSame($appID, (int) $entries[0]['administration_service'][1]['id']);
        $this->assertArrayHasKey('type', $entries[0]['administration_service'][1]);
        $this->assertSame(ObjectType::APPLICATION, $entries[0]['administration_service'][1]['type']);

        $this->assertArrayHasKey(2, $entries[0]['administration_service']);
        $this->assertIsArray($entries[0]['administration_service'][2]);
        $this->assertArrayHasKey('id', $entries[0]['administration_service'][2]);
        $this->assertArrayHasKey('title_lang', $entries[0]['administration_service'][2]);
        $this->assertSame('LC__CMDB__CATG__APPLICATION', $entries[0]['administration_service'][2]['title_lang']);
    }

}
