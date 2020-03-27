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
 * @group ID-6419
 * @see https://i-doit.atlassian.net/browse/ID-6419
 */
class ID6419Test extends BaseTest {

    /**
     * @return array
     */
    public function provideValidMACAddresses(): array {
        return [
            'first EUI-48 MAC address' => ['00:00:00:00:00:00'],
            'last EUI-48 MAC address' => ['FF:FF:FF:FF:FF:FF'],
            'random EUI-48 MAC address' => ['00:50:56:9A:2E:37'],
            'first EUI-64 MAC address' => ['00:00:00:00:00:00:00:00'],
            'last EUI-64 MAC address' => ['FF:FF:FF:FF:FF:FF:FF:FF'],
            'another EUI-64 MAC address' => ['00:00:00:00:00:00:00:E0']
        ];
    }

    /**
     * @return array
     */
    public function provideInvalidMACAddresses(): array {
        return [
            'random string' => [$this->generateRandomString()],
            'too few octets' => ['00:50:56'],
            'too much octets' => ['00:50:56:9A:2E:37:00:50:56:9A:2E:37'],
            'non 2-digit octets' => ['00:5:56:A:2E:37'],
            'no valid octets' => ['00:50:Z6:9A:2E:3Q']
        ];
    }

    /**
     * @param string $mac MAC address
     * @dataProvider provideValidMACAddresses
     * @throws Exception on error
     */
    public function testValidMACAddress(string $mac) {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        /**
         * Run tests:
         */

        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__NETWORK_PORT,
            [
                'mac' => $mac
            ]
        );

        $this->isID($entryID);

        /**
         * Double check:
         */

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__NETWORK_PORT
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        $this->assertArrayHasKey('mac', $entries[0]);
        $this->assertIsString($entries[0]['mac']);
        $this->assertSame($mac, $entries[0]['mac']);
    }

    /**
     * @param string $mac MAC address
     * @dataProvider provideInvalidMACAddresses
     * @throws Exception on error
     */
    public function testInvalidMACAddress(string $mac) {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        /**
         * Run tests:
         */

        $this->expectException(Exception::class);

        $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__NETWORK_PORT,
            [
                'mac' => $mac
            ]
        );
    }

}
