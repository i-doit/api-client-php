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

use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-166
 * @group unreleased
 * @see https://i-doit.atlassian.net/browse/API-166
 */
class API166Test extends BaseTest {

    /**
     * @throws \Exception on error
     */
    public function testArchiveSingleValueCategory() {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);
        $entryID = $this->generateRandomID();

        /**
         * Run tests:
         */

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'cmdb.category.archive',
            'params' => [
                'object' => $objectID,
                'category' => 'C__CATG__ACCOUNTING',
                'entry' => $entryID,
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);

        $message = sprintf(
            'Invalid parameters: Object %s does not own an entry in category \'%s\'.',
            $objectID,
            'Accounting'
        );

        $this->assertSame($message, $response['error']['message']);
    }

    /**
     * @throws \Exception on error
     */
    public function testDeleteSingleValueCategory() {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);
        $entryID = $this->generateRandomID();

        /**
         * Run tests:
         */

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'cmdb.category.delete',
            'params' => [
                'object' => $objectID,
                'category' => 'C__CATG__ACCOUNTING',
                'entry' => $entryID,
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);

        $message = sprintf(
            'Invalid parameters: Object %s does not own an entry in category \'%s\'.',
            $objectID,
            'Accounting'
        );

        $this->assertSame($message, $response['error']['message']);
    }

    /**
     * @throws \Exception on error
     */
    public function testPurgeSingleValueCategory() {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);
        $entryID = $this->generateRandomID();

        /**
         * Run tests:
         */

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'cmdb.category.purge',
            'params' => [
                'object' => $objectID,
                'category' => 'C__CATG__ACCOUNTING',
                'entry' => $entryID,
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);

        $message = sprintf(
            'Invalid parameters: Object %s does not own an entry in category \'%s\'.',
            $objectID,
            'Accounting'
        );

        $this->assertSame($message, $response['error']['message']);
    }

    /**
     * @throws \Exception on error
     */
    public function testQuickPurgeSingleValueCategory() {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);
        $entryID = $this->generateRandomID();

        /**
         * Run tests:
         */

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'cmdb.category.quickpurge',
            'params' => [
                'objID' => $objectID,
                'category' => 'C__CATG__ACCOUNTING',
                'cateID' => $entryID,
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);

        $message = sprintf(
            'Invalid parameters: Object %s does not own an entry in category \'%s\'.',
            $objectID,
            'Accounting'
        );

        $this->assertSame($message, $response['error']['message']);
    }

    /**
     * @throws \Exception on error
     */
    public function testArchiveMultiValueCategory() {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);
        $entryID = $this->generateRandomID();

        /**
         * Run tests:
         */

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'cmdb.category.archive',
            'params' => [
                'object' => $objectID,
                'category' => 'C__CATG__NETWORK_PORT',
                'entry' => $entryID,
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);

        $message = sprintf(
            'Invalid parameters: Unable to find entry for id %s. Please ensure ' .
            'that entry is owned by object %s and has a valid status.',
            $entryID,
            $objectID
        );

        $this->assertSame($message, $response['error']['message']);
    }

    /**
     * @throws \Exception on error
     */
    public function testDeleteMultiValueCategory() {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);
        $entryID = $this->generateRandomID();

        /**
         * Run tests:
         */

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'cmdb.category.delete',
            'params' => [
                'object' => $objectID,
                'category' => 'C__CATG__NETWORK_PORT',
                'entry' => $entryID,
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);

        $message = sprintf(
            'Invalid parameters: Unable to find entry for id %s. Please ensure ' .
            'that entry is owned by object %s and has a valid status.',
            $entryID,
            $objectID
        );

        $this->assertSame($message, $response['error']['message']);
    }

    /**
     * @throws \Exception on error
     */
    public function testPurgeMultiValueCategory() {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);
        $entryID = $this->generateRandomID();

        /**
         * Run tests:
         */

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'cmdb.category.purge',
            'params' => [
                'object' => $objectID,
                'category' => 'C__CATG__NETWORK_PORT',
                'entry' => $entryID,
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);

        $message = sprintf(
            'Invalid parameters: Unable to find entry for id %s. Please ensure ' .
            'that entry is owned by object %s and has a valid status.',
            $entryID,
            $objectID
        );

        $this->assertSame($message, $response['error']['message']);
    }

    /**
     * @throws \Exception on error
     */
    public function testQuickPurgeMultiValueCategory() {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);
        $entryID = $this->generateRandomID();

        /**
         * Run tests:
         */

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'cmdb.category.quickpurge',
            'params' => [
                'objID' => $objectID,
                'category' => 'C__CATG__NETWORK_PORT',
                'cateID' => $entryID,
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);

        $message = sprintf(
            'Invalid parameters: Unable to find entry for id %s. Please ensure ' .
            'that entry is owned by object %s and has a valid status.',
            $entryID,
            $objectID
        );

        $this->assertSame($message, $response['error']['message']);
    }

}
