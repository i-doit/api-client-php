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
 * @group API-138
 * @see https://i-doit.atlassian.net/browse/API-138
 */
class API138Test extends BaseTest {

    public function provideGenders(): array {
        return [
            'female' => ['f'],
            'male' => ['m']
        ];
    }

    /**
     * @dataProvider provideGenders
     * @param string $gender
     * @throws Exception on error
     */
    public function testSalutation(string $gender) {
        /**
         * Create test data:
         */

        $personID = $this->useCMDBObject()->create(
            ObjectType::PERSON,
            $this->generateRandomString()
        );

        $entryID = $this->useCMDBCategory()->save(
            $personID,
            Category::CATS__PERSON_MASTER,
            [
                'salutation' => $gender
            ]
        );
        $this->isID($entryID);

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
        $this->assertSame($entryID, (int) $entries[0]['id']);
        $this->assertSame($personID, (int) $entries[0]['objID']);

        $this->assertArrayHasKey('salutation', $entries[0]);
        $this->assertSame($gender, $entries[0]['salutation']);
    }

}
