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

use bheisig\idoitapi\CheckMKStaticTag;

/**
 * @coversDefaultClass \bheisig\idoitapi\CheckMKStaticTag
 */
class CheckMKStaticTagTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CheckMKStaticTag
     */
    protected $instance;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CheckMKStaticTag($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreateMinimal() {
        $id = $this->instance->create(
            $this->generateRandomString(),
            $this->generateRandomString()
        );

        $this->assertInternalType('int', $id);
        $this->assertGreaterThan(0, $id);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreateExtended() {
        $id = $this->instance->create(
            $this->generateRandomString(),
            $this->generateRandomString(),
            $this->generateRandomString(),
            false,
            $this->generateDescription()
        );

        $this->assertInternalType('int', $id);
        $this->assertGreaterThan(0, $id);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreateDublicate() {
        $tag = $this->generateRandomString();
        $title = $this->generateRandomString();

        $ids = [];

        for ($i = 0; $i < 2; $i++) {
            $id = $this->instance->create(
                $tag,
                $title
            );

            $this->assertInternalType('int', $id);
            $this->assertGreaterThan(0, $id);
            $this->assertNotContains($id, $ids);

            $ids[] = $id;
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchCreate() {
        $tags = [];

        $amount = 3;

        for ($i = 0; $i < $amount; $i++) {
            $tags[] = [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ];
        }

        $ids = $this->instance->batchCreate($tags);

        $this->assertInternalType('array', $ids);
        $this->assertCount($amount, $ids);

        foreach ($ids as $id) {
            $this->assertInternalType('int', $id);
            $this->assertGreaterThan(0, $id);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testReadExisting() {
        // We need at least 1 host tag:
        $tag = $this->generateRandomString();
        $title = $this->generateRandomString();
        $group = $this->generateRandomString();
        $export = false;
        $description = $this->generateDescription();

        $this->instance->create($tag, $title, $group, $export, $description);

        $tags = $this->instance->read();

        $this->assertInternalType('array', $tags);
        $this->assertNotCount(0, $tags);

        foreach ($tags as $tag) {
            $this->assertInternalType('array', $tag);

            $this->assertArrayHasKey('id', $tag);
            $this->assertInternalType('int', $tag['id']);
            $this->assertGreaterThan(0, $tag['id']);

            $this->assertArrayHasKey('tag', $tag);
            $this->assertInternalType('string', $tag['tag']);
            $this->assertNotEmpty($tag['tag']);

            $this->assertArrayHasKey('title', $tag);
            $this->assertInternalType('string', $tag['title']);
            $this->assertNotEmpty($tag['title']);

            $this->assertArrayHasKey('group', $tag);
            // 'group' is null or array

            $this->assertArrayHasKey('export', $tag);
            $this->assertInternalType('bool', $tag['export']);

            // Description is totally optional:
            if (array_key_exists('description', $tag)) {
                $this->assertInternalType('string', $tag['description']);
            }
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testReadNonExisting() {
        // Make sure there are no host tags:
        $this->instance->deleteAll();

        $result = $this->instance->read();

        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByExistingIdentifier() {
        // We need at least 1 host tag:
        $tag = $this->generateRandomString();
        $title = $this->generateRandomString();
        $group = $this->generateRandomString();
        $export = false;
        $description = $this->generateDescription();

        $id = $this->instance->create($tag, $title, $group, $export, $description);

        $tags = $this->instance->readByID($id);

        $this->assertInternalType('array', $tags);
        $this->assertCount(1, $tags);

        $this->assertArrayHasKey(0, $tags);
        $this->assertInternalType('array', $tags[0]);

        $this->assertSame($id, $tags[0]['id']);
        $this->assertSame($tag, $tags[0]['tag']);
        $this->assertSame($title, $tags[0]['title']);
        $this->assertSame($export, $tags[0]['export']);
        $this->assertSame($description, $tags[0]['description']);

        $this->assertInternalType('array', $tags[0]['group']);
        $this->assertArrayHasKey('id', $tags[0]['group']);
        $this->assertInternalType('int', $tags[0]['group']['id']);
        $this->assertArrayHasKey('title', $tags[0]['group']);
        $this->assertInternalType('string', $tags[0]['group']['title']);
        $this->assertArrayHasKey('const', $tags[0]['group']);
        // 'const' is null or string
        $this->assertArrayHasKey('title_lang', $tags[0]['group']);
        $this->assertInternalType('string', $tags[0]['group']['title_lang']);
        $this->assertSame($group, $tags[0]['group']['title']);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByNonExistingIdentifier() {
        // It is unlikely to produce such high IDs but this *could* fail:
        $id = 99999999;

        $result = $this->instance->readByID($id);

        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByExistingIdentifiers() {
        $tags = [];

        $amount = 3;

        for ($i = 0; $i < $amount; $i++) {
            $tags[] = [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ];
        }

        $ids = $this->instance->batchCreate($tags);

        $result = $this->instance->readByIDs($ids);

        $this->assertInternalType('array', $result);
        $this->assertCount($amount, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByNonExistingIdentifiers() {
        // It is unlikely to produce such high IDs but this *could* fail:
        $ids = [
            99999999,
            99999998,
            99999997
        ];

        $result = $this->instance->readByIDs($ids);

        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByExistingTag() {
        $tag = $this->generateRandomString();

        $id = $this->instance->create(
            $tag,
            $this->generateRandomString()
        );

        $result = $this->instance->readByTag($tag);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertSame($tag, $result[0]['tag']);
        $this->assertSame($id, $result[0]['id']);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByNonExistingTag() {
        $tag = $this->generateRandomString();

        $result = $this->instance->readByTag($tag);

        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testUpdateExisting() {
        $orig = [
            'tag' => $this->generateRandomString(),
            'title' => $this->generateRandomString(),
            'group' => $this->generateRandomString(),
            'export' => false,
            'description' => $this->generateDescription()
        ];

        $id = $this->instance->create(
            (string) $orig['tag'],
            (string) $orig['title'],
            (string) $orig['group'],
            (bool) $orig['export'],
            (string) $orig['description']
        );

        $altered = [
            'tag' => $this->generateRandomString(),
            'title' => $this->generateRandomString(),
            'group' => $this->generateRandomString(),
            'export' => true,
            'description' => $this->generateRandomString()
        ];

        $result = $this->instance->update($id, $altered);

        $this->assertInstanceOf(CheckMKStaticTag::class, $result);

        $alteredTag = $this->instance->readByID($id);

        $this->assertArrayHasKey(0, $alteredTag);
        $this->assertInternalType('array', $alteredTag[0]);

        foreach ($alteredTag[0] as $key => $value) {
            if ($key === 'id') {
                $this->assertSame($id, $value);
            } else {
                $this->assertNotEquals($orig[$key], $value);
            }
        }
    }

    /**
     * @expectedException \Exception
     * @throws \Exception on error
     */
    public function testUpdateNonExisting() {
        // It is unlikely to produce such high IDs but this *could* fail:
        $id = 99999999;

        $tag = [
            'tag' => $this->generateRandomString(),
            'title' => $this->generateRandomString(),
            'group' => $this->generateRandomString(),
            'export' => false,
            'description' => $this->generateDescription()
        ];

        // Bad:
        $this->instance->update($id, $tag);
    }

    /**
     * @throws \Exception on error
     */
    public function testDeleteExisting() {
        $id = $this->instance->create(
            $this->generateRandomString(),
            $this->generateRandomString()
        );

        $result = $this->instance->delete($id);

        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testDeleteDeletedOne() {
        $id = $this->instance->create(
            $this->generateRandomString(),
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
     * @throws \Exception on error
     */
    public function testDeleteNonExisting() {
        // It is unlikely to produce such high IDs but this *could* fail:
        $id = 99999999;

        // i-doit API says this is a valid operation :-(
        $result = $this->instance->delete($id);
        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchDeleteExisting() {
        $ids = $this->instance->batchCreate([
            [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ],
            [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ],
            [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ]
        ]);

        $result = $this->instance->batchDelete($ids);

        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchDeleteDeletedOnes() {
        $ids = $this->instance->batchCreate([
            [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ],
            [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ],
            [
                'tag' => $this->generateRandomString(),
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
     * @throws \Exception on error
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
     * @throws \Exception on error
     */
    public function testDeleteAllExisting() {
        $this->instance->batchCreate([
            [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ],
            [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ],
            [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ]
        ]);

        $result = $this->instance->deleteAll();

        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testDeleteAllNonExisting() {
        $this->instance->batchCreate([
            [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ],
            [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ],
            [
                'tag' => $this->generateRandomString(),
                'title' => $this->generateRandomString()
            ]
        ]);

        $this->instance->deleteAll();
        $result = $this->instance->deleteAll();

        $this->assertInstanceOf(CheckMKStaticTag::class, $result);
    }

}
