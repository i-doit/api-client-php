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
 * @link https://github.com/i-doit/api-client-php
 */

declare(strict_types=1);

namespace Idoit\APIClient\Issues;

use \Exception;
use Idoit\APIClient\BaseTest;
use Idoit\APIClient\Constants\Category;

/**
 * @group issues
 * @group API-181
 * @see https://i-doit.atlassian.net/browse/API-181
 */
class API181Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testByTitle() {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        $vendor = $this->generateRandomString();
        // Avoid same random string:
        usleep(10);
        $model = $this->generateRandomString();

        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__STORAGE_DEVICE,
            [
                'manufacturer' => $vendor,
                'model' => $model
            ]
        );
        $this->isID($entryID);

        /**
         * Run tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__STORAGE_DEVICE
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        $this->assertArrayHasKey('manufacturer', $entries[0]);
        $this->assertIsArray($entries[0]['manufacturer']);
        $this->isDialog($entries[0]['manufacturer']);
        $this->assertSame($vendor, $entries[0]['manufacturer']['title']);

        $this->assertArrayHasKey('model', $entries[0]);
        $this->assertIsArray($entries[0]['model']);
        $this->isDialog($entries[0]['model']);
        $this->assertSame($model, $entries[0]['model']['title']);
    }

    /**
     * @throws Exception on error
     */
    public function testByIdentifier() {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        $vendor = $this->generateRandomString();

        $vendorID = $this->useCMDBDialog()->create(
            Category::CATG__STORAGE_DEVICE,
            'manufacturer',
            $vendor
        );
        $this->isID($vendorID);

        $model = $this->generateRandomString();

        $modelID = $this->useCMDBDialog()->create(
            Category::CATG__STORAGE_DEVICE,
            'model',
            $model,
            $vendorID
        );
        $this->isID($modelID);

        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__STORAGE_DEVICE,
            [
                'manufacturer' => $vendorID,
                'model' => $modelID
            ]
        );

        $this->isID($entryID);

        /**
         * Run tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__STORAGE_DEVICE
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        $this->assertArrayHasKey('manufacturer', $entries[0]);
        $this->assertIsArray($entries[0]['manufacturer']);
        $this->isDialog($entries[0]['manufacturer']);
        $this->assertSame($vendor, $entries[0]['manufacturer']['title']);
        $this->assertSame($vendorID, (int) $entries[0]['manufacturer']['id']);

        $this->assertArrayHasKey('model', $entries[0]);
        $this->assertIsArray($entries[0]['model']);
        $this->isDialog($entries[0]['model']);
        $this->assertSame($model, $entries[0]['model']['title']);
        $this->assertSame($modelID, (int) $entries[0]['model']['id']);
    }

}
