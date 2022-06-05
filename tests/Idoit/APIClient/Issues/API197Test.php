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
use Idoit\APIClient\Constants\ObjectType;

/**
 * @group issues
 * @group API-197
 * @see https://i-doit.atlassian.net/browse/API-197
 */
class API197Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testSalutation() {
        /**
         * Create test data:
         */

        $personID = $this->useCMDBObject()->create(
            ObjectType::PERSON,
            $this->generateRandomString()
        );

        /**
         * Run tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $personID,
            Category::CATS__PERSON_MASTER
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->isCategoryEntry($entries[0]);
        $this->assertSame($personID, (int) $entries[0]['objID']);

        $this->assertArrayHasKey('salutation', $entries[0]);
        $this->assertNull($entries[0]['salutation']);
    }

}
