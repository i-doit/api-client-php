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
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-143
 * @group ID-6784
 * @see https://i-doit.atlassian.net/browse/API-143
 * @see https://i-doit.atlassian.net/browse/ID-6784
 */
class API143Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testIssue() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->useCMDBCategory()->create(
            $objectID,
            Category::CATG__CPU,
            [
                'title' => $this->generateRandomString(),
                'manufacturer' => 'Amdtel'
            ]
        );
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->readOneByID($objectID, Category::CATG__CPU, $entryID);
        $this->assertIsArray($result);

        $this->assertArrayHasKey('frequency', $result);
        $this->assertNull($result['frequency']);

        $this->assertArrayHasKey('frequency_unit', $result);
        $this->assertNull($result['frequency_unit']);

        $this->useCMDBCategory()->update(
            $objectID,
            Category::CATG__CPU,
            [
                'manufacturer' => 'Amdtel'
            ],
            $entryID
        );

        $result = $this->useCMDBCategory()->readOneByID($objectID, Category::CATG__CPU, $entryID);
        $this->assertIsArray($result);

        $this->assertArrayHasKey('frequency', $result);
        $this->assertNull($result['frequency']);

        $this->assertArrayHasKey('frequency_unit', $result);
        $this->assertNull($result['frequency_unit']);
    }

}
