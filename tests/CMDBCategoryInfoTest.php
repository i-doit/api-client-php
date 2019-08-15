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

namespace bheisig\idoitapi\tests;

use bheisig\idoitapi\tests\Constants\Category;
use \Exception;
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBCategoryInfo;

/**
 * @group API-204
 * @group unreleased
 * @see https://i-doit.atlassian.net/browse/API-204
 */
class CMDBCategoryInfoTest extends BaseTest {

    /**
     * @var CMDBCategoryInfo
     */
    protected $instance;

    protected $categories = [];

    /**
     * @throws Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBCategoryInfo($this->api);

        $this->categories = [
            Category::CATG__GLOBAL,
            Category::CATG__IP,
            Category::CATS__PERSON_MASTER
        ];
    }

    /**
     * @throws Exception on error
     */
    public function testRead() {
        foreach ($this->categories as $categoryConst) {
            $result = $this->instance->read($categoryConst);

            $this->assertIsArray($result);
            $this->assertNotCount(0, $result);

            $this->isCategoryInfo($result);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testBatchRead() {
        $result = $this->instance->batchRead($this->categories);

        $this->assertIsArray($result);
        $this->assertCount(count($this->categories), $result);

        foreach ($result as $categoryInfo) {
            $this->assertIsArray($categoryInfo);
            $this->assertNotCount(0, $categoryInfo);

            $this->isCategoryInfo($categoryInfo);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadAll() {
        $result = $this->instance->readAll();

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);

        foreach ($result as $categoryConst => $categoryInfo) {
            $this->assertIsString($categoryConst);
            $this->assertIsArray($categoryInfo);

            $this->isCategoryInfo($categoryInfo);
        }
    }

    public function testGetVirtualCategoryConstants() {
        $categoryConstants = $this->instance->getVirtualCategoryConstants();
        $this->assertIsArray($categoryConstants);
        foreach ($categoryConstants as $categoryConstant) {
            $this->assertIsString($categoryConstant);
            $this->isConstant($categoryConstant);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function provideVirtualCategories(): array {
        $cmdbCategoryInfo = new CMDBCategoryInfo(new API([
            API::URL => 'https://example.com/src/json.rpc',
            API::KEY => '123'
        ]));

        $virtualCategoryConstants = $cmdbCategoryInfo->getVirtualCategoryConstants();

        $ignoreFromAddOns = [
            'C__CATG__FLOORPLAN',
            'C__CATG__VIRTUAL_RELOCATE_CI'
        ];

        $parameters = [];

        foreach ($virtualCategoryConstants as $virtualCategoryConstant) {
            if (in_array($virtualCategoryConstant, $ignoreFromAddOns)) {
                continue;
            }

            $parameters[$virtualCategoryConstant] = [$virtualCategoryConstant];
        }

        return $parameters;
    }

    /**
     * @group API-72
     * @throws Exception on error
     * @dataProvider provideVirtualCategories
     * @param string $categoryConstant Category constant
     */
    public function testReadVirtualCategoryInfo(string $categoryConstant) {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'cmdb.category_info',
            'params' => array(
                'category' => $categoryConstant,
                'apikey' => getenv('KEY')
            ),
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32099, $response['error']['code'], $categoryConstant);
    }

    /**
     * @group API-189
     * @group unreleased
     * @throws Exception on error
     * @dataProvider provideVirtualCategories
     * @param string $categoryConstant Category constant
     */
    public function testReadVirtualCategoryEntries(string $categoryConstant) {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'cmdb.category.read',
            'params' => array(
                'category' => $categoryConstant,
                'objID' => $objectID,
                'apikey' => getenv('KEY')
            ),
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32099, $response['error']['code'], $categoryConstant);
    }

    protected function isCategoryInfo(array $category) {
        foreach ($category as $attributeTitle => $attribute) {
            $this->assertIsString($attributeTitle);
            $this->assertGreaterThan(0, strlen($attributeTitle));

            $this->assertIsArray($attribute);
            $this->isAttributeInfo($attribute);
        }
    }

    protected function isAttributeInfo(array $attribute) {
        /**
         * "title":
         */

        $this->assertArrayHasKey('title', $attribute);
        $this->assertIsString($attribute['title']);
        $this->assertGreaterThan(0, strlen($attribute['title']));

        /**
         * "info":
         */

        $this->assertArrayHasKey('info', $attribute);
        $this->assertIsArray($attribute['info']);
        // "info.primary_field" is optional:
        if (array_key_exists('primary_field', $attribute['info'])) {
            $this->assertIsBool($attribute['info']['primary_field']);
        }

        $this->assertArrayHasKey('type', $attribute['info']);
        $this->assertIsString($attribute['info']['type']);
        $this->assertGreaterThan(0, strlen($attribute['info']['type']));

        // "info.backward" is optional:
        if (array_key_exists('backward', $attribute['info'])) {
            $this->assertIsBool($attribute['info']['backward']);
        }

        $this->assertArrayHasKey('title', $attribute['info']);
        $this->assertIsString($attribute['info']['title']);
        $this->assertGreaterThan(0, strlen($attribute['info']['title']));

        // "info.description" is optional:
        if (array_key_exists('description', $attribute['info'])) {
            $this->assertIsString($attribute['info']['description']);
            $this->assertGreaterThan(0, strlen($attribute['info']['description']));
        }

        /**
         * "data":
         */

        $this->assertArrayHasKey('data', $attribute);
        $this->assertIsArray($attribute['data']);

        $this->assertArrayHasKey('type', $attribute['data']);
        $this->assertIsString($attribute['data']['type']);
        $this->assertGreaterThan(0, strlen($attribute['data']['type']));

        // "data.readonly" is optional:
        if (array_key_exists('readonly', $attribute['data'])) {
            $this->assertIsBool($attribute['data']['readonly']);
        }

        $this->assertArrayHasKey('index', $attribute['data']);
        $this->assertIsBool($attribute['data']['index']);

        // "data.field" is optional:
        if (array_key_exists('field', $attribute['data'])) {
            $this->assertIsString($attribute['data']['field']);
            $this->assertGreaterThan(0, strlen($attribute['data']['field']));
        }

        // "data.table_alias" is optional:
        if (array_key_exists('table_alias', $attribute['data'])) {
            $this->assertIsString($attribute['data']['table_alias']);
            $this->assertGreaterThan(0, strlen($attribute['data']['table_alias']));
        }

        /**
         * "ui":
         */

        $this->assertArrayHasKey('ui', $attribute);
        $this->assertIsArray($attribute['ui']);

        $this->assertArrayHasKey('type', $attribute['ui']);
        $this->assertIsString($attribute['ui']['type']);
        $this->assertGreaterThan(0, strlen($attribute['ui']['type']));

        // "ui.default" is optional and is mixed type (even null): nothing we can do!

        // "ui.params" is optional:
        if (array_key_exists('params', $attribute['ui'])) {
            $this->assertIsArray($attribute['ui']['params']);
        }

        // "ui.id" is optional:
        if (array_key_exists('id', $attribute['ui'])) {
            $this->assertIsString($attribute['ui']['id']);
            // String may be empty.
        }

        /**
         * "format":
         */

        // format is optional and may be null:
        if (array_key_exists('format', $attribute) && isset($attribute['format'])) {
            $this->assertArrayHasKey('format', $attribute);
            // Inside this array there could be anything.
        }

        /**
         * "check":
         */

        $this->assertArrayHasKey('check', $attribute);
        $this->assertIsArray($attribute['check']);

        $this->assertArrayHasKey('mandatory', $attribute['check']);

        // "check.mandatory" may be set to null:
        if (isset($attribute['check']['mandatory'])) {
            $this->assertIsBool($attribute['check']['mandatory']);
        }
    }

}
