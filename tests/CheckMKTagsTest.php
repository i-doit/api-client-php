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

namespace bheisig\idoitapi\tests;

use bheisig\idoitapi\CheckMKTags;

/**
 * @coversDefaultClass \bheisig\idoitapi\CheckMKTags
 * @group unreleased
 */
class CheckMKTagsTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CheckMKTags
     */
    protected $instance;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CheckMKTags($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByExistingCategory() {
        $objectID = $this->createServer();
        $this->cmdbCategory->create(
            $objectID,
            'C__CATG__CMK_TAG',
            [
                'description' => $this->generateDescription()
            ]
        );

        $result = $this->instance->read($objectID);

        $this->assertInternalType('array', $result);
        $this->assertTags($result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByEmptyCategory() {
        $objectID = $this->createServer();

        $result = $this->instance->read($objectID);

        $this->assertInternalType('array', $result);
        $this->assertTags($result);
    }

    /**
     * @expectedException \Exception
     * @throws \Exception on error
     */
    public function testReadByNonExistingObject() {
        // It's very unlikely that this object exists:
        $objectID = 422300001;

        $result = $this->instance->read($objectID);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @expectedException \Exception
     * @throws \Exception on error
     */
    public function testReadByBrokenObjectIdentifier() {
        $objectIDs = [
            -1,
            0
        ];

        foreach ($objectIDs as $objectID) {
            $result = $this->instance->read($objectID);

            $this->assertInternalType('array', $result);
            $this->assertNotCount(0, $result);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchReadExistingTags() {
        $objectIDs = [];
        $amount = 3;

        for ($i = 0; $i < $amount; $i++) {
            $objectID = $this->createServer();

            $this->cmdbCategory->create(
                $objectID,
                'C__CATG__CMK_TAG',
                [
                    'description' => $this->generateDescription()
                ]
            );

            $objectIDs[] = $objectID;
        }

        $result = $this->instance->batchRead($objectIDs);

        $this->assertInternalType('array', $result);
        $this->assertCount(count($objectIDs), $result);

        foreach ($result as $tags) {
            $this->assertInternalType('array', $tags);
            $this->assertTags($tags);
        }
    }

    /**
     * @expectedException \Exception
     * @throws \Exception on error
     */
    public function testBatchReadNonExistingObjects() {
        // It's very unlikely that these objects exist:
        $objectIDs = [
            422300001,
            422300002,
            422300003
        ];

        $result = $this->instance->batchRead($objectIDs);

        $this->assertInternalType('array', $result);
        $this->assertCount(count($objectIDs), $result);
    }

    /**
     * @expectedException \Exception
     * @throws \Exception on error
     */
    public function testBatchReadBrokenObjectIdentifiers() {
        $objectIDs = [
            -1,
            0
        ];

        $result = $this->instance->batchRead($objectIDs);

        $this->assertInternalType('array', $result);
        $this->assertCount(count($objectIDs), $result);
    }

    protected function assertTags(array $tags) {
        $this->assertArrayHasKey('id', $tags);
        if (isset($tags['id'])) {
            $this->isIDAsString($tags['id']);
        }

        $this->assertArrayHasKey('objID', $tags);
        $this->isID($tags['objID']);

        $this->assertArrayHasKey('tags', $tags);
        $this->assertInternalType('array', $tags['tags']);
        foreach ($tags['tags'] as $tag) {
            $this->assertTag($tag);
        }

        $this->assertArrayHasKey('cmdb_tags', $tags);
        $this->assertInternalType('array', $tags['cmdb_tags']);
        foreach ($tags['cmdb_tags'] as $tag) {
            $this->assertTag($tag);
        }

        $this->assertArrayHasKey('dynamic_tags', $tags);
        $this->assertInternalType('array', $tags['dynamic_tags']);
        foreach ($tags['dynamic_tags'] as $tag) {
            $this->assertInternalType('array', $tag);
            $this->assertTag($tag);
        }

        $this->assertArrayHasKey('description', $tags);
        if (isset($tags['description'])) {
            $this->assertInternalType('string', $tags['description']);
        }
    }

    protected function assertTag(array $tag) {
        $this->assertArrayHasKey('id', $tag);
        $this->assertNull($tag['id']);

        $this->assertArrayHasKey('const', $tag);
        $this->assertInternalType('string', $tag['const']);
        $this->assertNotEmpty($tag['const']);

        $this->assertArrayHasKey('val', $tag);
        $this->assertInternalType('string', $tag['val']);
        $this->assertNotEmpty($tag['val']);

        $this->assertArrayHasKey('sel', $tag);
        $this->assertInternalType('boolean', $tag['sel']);

        $this->assertArrayHasKey('group', $tag);
        $this->assertInternalType('string', $tag['group']);
        $this->assertNotEmpty($tag['group']);
    }

}
