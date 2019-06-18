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

use \Exception;
use bheisig\idoitapi\CheckMKStaticTag;

/**
 * @group addon
 */
class CheckMKStaticTagTest extends BaseTest {

    /**
     * @var CheckMKStaticTag
     */
    protected $instance;

    /**
     * @throws Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CheckMKStaticTag($this->api);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateMinimal() {
        $id = $this->instance->create(
            $this->generateRandomString()
        );

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateExtended() {
        $id = $this->instance->create(
            $this->generateRandomString(),
            $this->generateRandomString(),
            $this->generateRandomString(),
            $this->generateDescription()
        );

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateDublicate() {
        $title = $this->generateRandomString();
        $tag = $this->generateRandomString();

        $ids = [];

        for ($i = 0; $i < 2; $i++) {
            $id = $this->instance->create(
                $title,
                $tag
            );

            $this->assertIsInt($id);
            $this->assertGreaterThan(0, $id);
            $this->assertNotContains($id, $ids);

            $ids[] = $id;
        }
    }

    /**
     * @throws Exception on error
     */
    public function testSimpleBatchCreate() {
        $tags = [];

        $amount = 3;

        for ($i = 0; $i < $amount; $i++) {
            $tags[] = [
                'title' => $this->generateRandomString()
            ];
        }

        $ids = $this->instance->batchCreate($tags);

        $this->assertIsArray($ids);
        $this->assertCount($amount, $ids);

        foreach ($ids as $id) {
            $this->assertIsInt($id);
            $this->assertGreaterThan(0, $id);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testExtendedBatchCreate() {
        $tags = [];

        $amount = 3;

        for ($i = 0; $i < $amount; $i++) {
            $tags[] = [
                'title' => $this->generateRandomString(),
                'tag' => $this->generateRandomString(),
                'group' => $this->generateRandomString(),
                'description' => $this->generateRandomString()
            ];
        }

        $ids = $this->instance->batchCreate($tags);

        $this->assertIsArray($ids);
        $this->assertCount($amount, $ids);

        foreach ($ids as $id) {
            $this->assertIsInt($id);
            $this->assertGreaterThan(0, $id);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadExisting() {
        // We need at least 1 host tag:
        $title = $this->generateRandomString();
        $tagID = $this->generateRandomString();
        $group = $this->generateRandomString();
        $description = $this->generateDescription();

        $this->instance->create($title, $tagID, $group, $description);

        $tags = $this->instance->read();

        $this->assertIsArray($tags);
        $this->assertNotCount(0, $tags);

        foreach ($tags as $tag) {
            $this->assertIsArray($tag);
            $this->isStaticTag($tag);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadNonExisting() {
        // Make sure there are no host tags:
        $this->instance->deleteAll();

        $result = $this->instance->read();

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByExistingIdentifier() {
        // We need at least 1 host tag:
        $title = $this->generateRandomString();
        $tag = $this->generateRandomString();
        $group = $this->generateRandomString();
        $description = $this->generateDescription();

        $id = $this->instance->create($title, $tag, $group, $description);

        $tags = $this->instance->readByID($id);

        $this->assertIsArray($tags);
        $this->assertCount(1, $tags);

        $this->assertArrayHasKey(0, $tags);
        $this->assertIsArray($tags[0]);

        $this->assertSame($id, $tags[0]['id']);
        $this->assertSame($title, $tags[0]['title']);
        $this->assertSame($tag, $tags[0]['tag']);
        $this->assertSame($description, $tags[0]['description']);

        $this->assertIsArray($tags[0]['group']);
        $this->assertArrayHasKey('id', $tags[0]['group']);
        $this->assertIsInt($tags[0]['group']['id']);
        $this->assertArrayHasKey('title', $tags[0]['group']);
        $this->assertIsString($tags[0]['group']['title']);
        $this->assertArrayHasKey('const', $tags[0]['group']);
        // 'const' is null or string
        $this->assertArrayHasKey('title_lang', $tags[0]['group']);
        $this->assertIsString($tags[0]['group']['title_lang']);
        $this->assertSame($group, $tags[0]['group']['title']);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByNonExistingIdentifier() {
        // It is unlikely to produce such high IDs but this *could* fail:
        $id = 99999999;

        $result = $this->instance->readByID($id);

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByExistingIdentifiers() {
        $tags = [];

        $amount = 3;

        for ($i = 0; $i < $amount; $i++) {
            $tags[] = [
                'title' => $this->generateRandomString()
            ];
        }

        $ids = $this->instance->batchCreate($tags);

        $result = $this->instance->readByIDs($ids);

        $this->assertIsArray($result);
        $this->assertCount($amount, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByNonExistingIdentifiers() {
        // It is unlikely to produce such high IDs but this *could* fail:
        $ids = [
            99999999,
            99999998,
            99999997
        ];

        $result = $this->instance->readByIDs($ids);

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByExistingTag() {
        $tag = $this->generateRandomString();

        $id = $this->instance->create(
            $tag,
            $this->generateRandomString()
        );

        $result = $this->instance->readByTag($tag);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($tag, $result[0]['tag']);
        $this->assertSame($id, $result[0]['id']);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByNonExistingTag() {
        $tag = $this->generateRandomString();

        $result = $this->instance->readByTag($tag);

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testUpdateExisting() {
        $orig = [
            'title' => $this->generateRandomString(),
            'tag' => $this->generateRandomString(),
            'group' => $this->generateRandomString(),
            'description' => $this->generateDescription()
        ];

        $id = $this->instance->create(
            $orig['title'],
            $orig['tag'],
            $orig['group'],
            $orig['description']
        );

        $altered = [
            'title' => $this->generateRandomString(),
            'tag' => $this->generateRandomString(),
            'group' => $this->generateRandomString(),
            'description' => $this->generateRandomString()
        ];

        $result = $this->instance->update($id, $altered);

        $this->assertInstanceOf(CheckMKStaticTag::class, $result);

        $alteredTag = $this->instance->readByID($id);

        $this->assertArrayHasKey(0, $alteredTag);
        $this->assertIsArray($alteredTag[0]);

        foreach ($alteredTag[0] as $key => $value) {
            if ($key === 'id') {
                $this->assertSame($id, $value);
            } else {
                $this->assertNotEquals($orig[$key], $value);
            }
        }
    }

    /**
     * @throws Exception on error
     */
    public function testUpdateNonExisting() {
        $this->expectException(Exception::class);

        // It is unlikely to produce such high IDs but this *could* fail:
        $id = 99999999;

        $tag = [
            'title' => $this->generateRandomString(),
            'tag' => $this->generateRandomString(),
            'group' => $this->generateRandomString(),
            'description' => $this->generateDescription()
        ];

        // Bad:
        $this->instance->update($id, $tag);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteExisting() {
        $id = $this->instance->create(
            $this->generateRandomString()
        );

        $result = $this->instance->delete($id);

        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteDeletedOne() {
        $id = $this->instance->create(
            $this->generateRandomString()
        );

        // Good:
        $result = $this->instance->delete($id);
        $this->assertInstanceOf(CheckMKStaticTag::class, $result);

        // Bad:
        // i-doit API says this is a valid operation :-(
        $result = $this->instance->delete($id);
        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteNonExisting() {
        // It is unlikely to produce such high IDs but this *could* fail:
        $id = 99999999;

        // i-doit API says this is a valid operation :-(
        $result = $this->instance->delete($id);
        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testBatchDeleteExisting() {
        $ids = $this->instance->batchCreate([
            [
                'title' => $this->generateRandomString()
            ],
            [
                'title' => $this->generateRandomString()
            ],
            [
                'title' => $this->generateRandomString()
            ]
        ]);

        $result = $this->instance->batchDelete($ids);

        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testBatchDeleteDeletedOnes() {
        $ids = $this->instance->batchCreate([
            [
                'title' => $this->generateRandomString()
            ],
            [
                'title' => $this->generateRandomString()
            ],
            [
                'title' => $this->generateRandomString()
            ]
        ]);

        $result = $this->instance->batchDelete($ids);
        $this->assertInstanceOf(CheckMKStaticTag::class, $result);

        // i-doit API says this is a valid operation :-(
        $result = $this->instance->batchDelete($ids);
        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testBatchDeleteNonExisting() {
        // It is unlikely to produce such high IDs but this *could* fail:
        $ids = [
            99999999,
            99999998,
            99999997
        ];

        // i-doit API says this is a valid operation :-(
        $result = $this->instance->batchDelete($ids);
        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteAllExisting() {
        $this->instance->batchCreate([
            [
                'title' => $this->generateRandomString()
            ],
            [
                'title' => $this->generateRandomString()
            ],
            [
                'title' => $this->generateRandomString()
            ]
        ]);

        $result = $this->instance->deleteAll();

        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteAllNonExisting() {
        $this->instance->batchCreate([
            [
                'title' => $this->generateRandomString()
            ],
            [
                'title' => $this->generateRandomString()
            ],
            [
                'title' => $this->generateRandomString()
            ]
        ]);

        $this->instance->deleteAll();
        $result = $this->instance->deleteAll();

        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

    protected function isStaticTag(array $tag) {
        $this->assertArrayHasKey('id', $tag);
        $this->assertIsInt($tag['id']);
        $this->assertGreaterThan(0, $tag['id']);

        $this->assertArrayHasKey('title', $tag);
        $this->assertIsString($tag['title']);
        $this->assertNotEmpty($tag['title']);

        if (array_key_exists('tag', $tag)) {
            $this->assertIsString($tag['tag']);
            $this->assertNotEmpty($tag['tag']);
        }

        $this->assertArrayHasKey('group', $tag);
        // 'group' is null or array

        // Description is totally optional:
        if (array_key_exists('description', $tag)) {
            $this->assertIsString($tag['description']);
        }
    }

}
