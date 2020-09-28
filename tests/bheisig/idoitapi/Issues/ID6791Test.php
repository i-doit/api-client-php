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

/**
 * @group issues
 * @group ID-6791
 * @see https://i-doit.atlassian.net/browse/ID-6791
 */
class ID6791Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testAddIPAddressToPortByEntryIdentifier() {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        $ip1ID = $this->addIPv4($objectID);
        $this->isID($ip1ID);

        $ip2ID = $this->addIPv4($objectID);
        $this->isID($ip2ID);

        /**
         * Run tests:
         */

        // This failed with an HTTP status code 500:
        $portID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__NETWORK_PORT,
            [
                'active' => 1,
                'title' => 'eth0',
                'addresses' => [
                    $ip1ID,
                    $ip2ID
                ]
            ]
        );
        $this->isID($portID);

        /**
         * Double check:
         */

        $ports = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__NETWORK_PORT
        );

        $this->assertIsArray($ports);
        $this->assertCount(1, $ports);
        $this->assertArrayHasKey(0, $ports);
        $this->assertIsArray($ports[0]);
        $this->isCategoryEntry($ports[0]);

        $this->assertArrayHasKey('addresses', $ports[0]);
        $this->assertIsArray($ports[0]['addresses']);
        $this->assertCount(2, $ports[0]['addresses']);

        $this->assertArrayHasKey(0, $ports[0]['addresses']);
        $this->assertIsArray($ports[0]['addresses'][0]);
        $this->isLinkedAddress($ports[0]['addresses'][0]);
        $this->assertSame($ip1ID, (int) $ports[0]['addresses'][0]['id']);
        $this->assertNull($ports[0]['addresses'][0]['hostname']);

        $this->assertArrayHasKey(1, $ports[0]['addresses']);
        $this->assertIsArray($ports[0]['addresses'][1]);
        $this->isLinkedAddress($ports[0]['addresses'][1]);
        $this->assertSame($ip2ID, (int) $ports[0]['addresses'][1]['id']);
        $this->assertNull($ports[0]['addresses'][1]['hostname']);
    }

    /**
     * @throws Exception on error
     */
    public function testAddIPAddressToPortByAddress() {
        /**
         * Create test data:
         */

        $ipv4Address1 = $this->generateIPv4Address();
        $ipv4Address2 = $this->generateIPv4Address();

        $objectID = $this->createServer();
        $this->isID($objectID);

        $ip1ID = $this->useCMDBCategory()->create(
            $objectID,
            Category::CATG__IP,
            [
                'net' => $this->getIPv4Net(),
                'active' => mt_rand(0, 1),
                'primary' => mt_rand(0, 1),
                'net_type' => 1, // IPv4
                'ipv4_assignment' => 2, // Static
                'ipv4_address' => $ipv4Address1,
                'description' => $this->generateDescription()
            ]
        );
        $this->isID($ip1ID);

        $ip2ID = $this->useCMDBCategory()->create(
            $objectID,
            Category::CATG__IP,
            [
                'net' => $this->getIPv4Net(),
                'active' => mt_rand(0, 1),
                'primary' => mt_rand(0, 1),
                'net_type' => 1, // IPv4
                'ipv4_assignment' => 2, // Static
                'ipv4_address' => $ipv4Address2,
                'description' => $this->generateDescription()
            ]
        );
        $this->isID($ip2ID);

        /**
         * Run tests:
         */

        // This failed with an HTTP status code 500:
        $portID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__NETWORK_PORT,
            [
                'active' => 1,
                'title' => 'eth0',
                'addresses' => [
                    $ipv4Address1,
                    $ipv4Address2
                ]
            ]
        );
        $this->isID($portID);

        /**
         * Double check:
         */

        $ports = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__NETWORK_PORT
        );

        $this->assertIsArray($ports);
        $this->assertCount(1, $ports);
        $this->assertArrayHasKey(0, $ports);
        $this->assertIsArray($ports[0]);
        $this->isCategoryEntry($ports[0]);

        $this->assertArrayHasKey('addresses', $ports[0]);
        $this->assertIsArray($ports[0]['addresses']);
        $this->assertCount(2, $ports[0]['addresses']);

        $this->assertArrayHasKey(0, $ports[0]['addresses']);
        $this->assertIsArray($ports[0]['addresses'][0]);
        $this->isLinkedAddress($ports[0]['addresses'][0]);
        $this->assertSame($ip1ID, (int) $ports[0]['addresses'][0]['id']);
        $this->assertSame($ipv4Address1, $ports[0]['addresses'][0]['title']);
        $this->assertNull($ports[0]['addresses'][0]['hostname']);

        $this->assertArrayHasKey(1, $ports[0]['addresses']);
        $this->assertIsArray($ports[0]['addresses'][1]);
        $this->isLinkedAddress($ports[0]['addresses'][1]);
        $this->assertSame($ip2ID, (int) $ports[0]['addresses'][1]['id']);
        $this->assertSame($ipv4Address2, $ports[0]['addresses'][1]['title']);
        $this->assertNull($ports[0]['addresses'][1]['hostname']);
    }

    protected function isLinkedAddress(array $address) {
        $this->assertArrayHasKey('id', $address);
        $this->assertIsString($address['id']);
        $this->isIDAsString($address['id']);

        $this->assertArrayHasKey('title', $address);
        $this->assertIsString($address['title']);

        $this->assertArrayHasKey('hostname', $address);

        $this->assertArrayHasKey('type', $address);
        $this->assertIsString($address['type']);
        $this->assertSame(Category::CATG__IP, $address['type']);
    }

}
