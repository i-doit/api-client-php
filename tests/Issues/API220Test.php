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
use bheisig\idoitapi\tests\Constants\ObjectType;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-220
 * @see https://i-doit.atlassian.net/browse/API-220
 */
class API220Test extends BaseTest {

    public function provideYesOrNo(): array {
        return [
            'Say yes' => [1, 'Yes'],
            'Say no' => [0, 'No']
        ];
    }

    /**
     * @param int $decision
     * @param string $translation
     * @throws Exception on error
     * @dataProvider provideYesOrNo
     */
    public function testUseRouterAsDefaultGateway(int $decision, string $translation) {
        /**
         * Create test data:
         */
        $networkID = $this->createSubnet();
        $this->isID($networkID);

        $routerID = $this->cmdbObject->create(
            ObjectType::ROUTER,
            $this->generateRandomString()
        );
        $this->isID($routerID);

        $ipAddressID = $this->addIPv4(
            $routerID,
            $networkID
        );
        $this->isID($ipAddressID);

        $entryID = $this->cmdbCategory->save(
            $routerID,
            Category::CATG__IP,
            [
                'use_standard_gateway' => $decision
            ],
            $ipAddressID
        );
        $this->isID($entryID);
        $this->assertSame($ipAddressID, $entryID);

        /**
         * Run tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $routerID,
            Category::CATG__IP
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        $this->assertArrayHasKey('use_standard_gateway', $entries[0]);
        $this->assertIsArray($entries[0]['use_standard_gateway']);
        $this->assertArrayHasKey('value', $entries[0]['use_standard_gateway']);
        $this->assertIsInt($entries[0]['use_standard_gateway']['value']);
        $this->assertSame($decision, $entries[0]['use_standard_gateway']['value']);
        $this->assertArrayHasKey('title', $entries[0]['use_standard_gateway']);
        $this->assertIsString($entries[0]['use_standard_gateway']['title']);
        $this->assertSame($translation, $entries[0]['use_standard_gateway']['title']);
    }

}
