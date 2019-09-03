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

use bheisig\idoitapi\tests\Constants\Category;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-203
 * @see https://i-doit.atlassian.net/browse/API-203
 */
class API203Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testDecryptedPassword() {
        /**
         * Create test data:
         */

        $password = $this->generateRandomString();

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__PASSWD,
            [
                'title' => $this->generateRandomString(),
                'username' => $this->generateRandomString(),
                'password' => $password,
                'description' => $this->generateDescription()
            ]
        );
        $this->isID($entryID);

        /**
         * Run tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__PASSWD
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        $this->assertArrayHasKey('password', $entries[0]);
        $this->assertIsString($entries[0]['password']);
        // This failed:
        $this->assertSame($password, $entries[0]['password']);
    }

}
