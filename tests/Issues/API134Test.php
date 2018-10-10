<?php

/**
 * Copyright (C) 2016-18 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-18 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group unreleased
 * @see https://i-doit.atlassian.net/browse/API-134
 */
class API134Test extends BaseTest {

    /**
     * @throws \Exception on error
     */
    public function testCreate() {
        $this->performCall(
            'cmdb.category.create',
            'Category entry successfully created. [This method is deprecated und will be removed in a feature release. Use \'cmdb.category.save\' instead.]'
        );
    }

    /**
     * @throws \Exception on error
     */
    public function testUpdate() {
        $this->performCall(
            'cmdb.category.update',
            'update: Category entry successfully created. [This method is deprecated und will be removed in a feature release. Use \'cmdb.category.save\' instead.'
        );
    }

    /**
     * @param string $method
     * @param string $expectedMessage
     *
     * @throws \Exception on error
     */
    protected function performCall(string $method, string $expectedMessage) {
        $objectID = $this->createServer();

        $request = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => [
                'apikey' => getenv('KEY'),
                'objID' => $objectID,
                'category' => 'C__CATG__MODEL',
                'data' => [
                    'manufacturer' => $this->generateRandomString(),
                    'title' => $this->generateRandomString()
                ]
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isValidResponse($response, $request);
        $this->assertInternalType('array', $response['result']);

        $this->assertArrayHasKey('message', $response['result']);
        $this->assertSame($expectedMessage, $response['result']['message']);
    }

}
