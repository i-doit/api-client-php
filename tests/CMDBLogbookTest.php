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

use bheisig\idoitapi\CMDBLogbook;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBLogbook
 */
class CMDBLogbookTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBLogbook
     */
    protected $instance;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBLogbook($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreate() {
        $objectID = $this->createServer();

        $result = $this->instance->create(
            $objectID,
            'Performed unit test',
            'This is just a unit test.'
        );

        $this->assertInstanceOf(CMDBLogbook::class, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchCreate() {
        $objectID = $this->createServer();

        $result = $this->instance->batchCreate(
            $objectID,
            [
                'Performed unit test 1',
                'Performed unit test 2',
                'Performed unit test 3'
            ]
        );

        $this->assertInstanceOf(CMDBLogbook::class, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testRead() {
        $result = $this->instance->read();

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $this->validateEntries($result);
    }

    /**
     * Provide dates and Unix timestamps
     *
     * @return array
     */
    public function provideDates(): array {
        return [
            // This will probably result in a HTTP status code 500:
//            '0' => ['0'],
            'today' => ['today'],
            'yesterday' => ['yesterday'],
            'now as Y-m-d' => [date('Y-m-d')],
            'now as Unix timestamp' => ['' . time() . ''],
            'now as c' => [date('c')]
        ];
    }

    /**
     * @dataProvider provideDates
     * @param string $date Date or Unix timestamp
     * @throws \Exception on error
     */
    public function testReadByDate(string $date) {
        $result = $this->instance->read($date);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $this->validateEntries($result);
    }

    public function provideLimits(): array {
        return [
            // This will probably result in a HTTP status code 500:
//            '0' => [0],
            'limit to 1' => [1],
            'limit to 10' => [10],
            'limit to 100' => [100],
            'limit to 1000' => [1000],
            'limit to 10000' => [10000]
        ];
    }

    /**
     * @dataProvider provideLimits
     * @param int $limit Limit
     * @throws \Exception on error
     */
    public function testReadWithLimit(int $limit) {
        $result = $this->instance->read(null, $limit);

        $this->assertInternalType('array', $result);
        $this->assertCount($limit, $result);

        $this->validateEntries($result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByObject() {
        $objectID = $this->createServer();

        $result = $this->instance->readByObject($objectID);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $this->validateEntries($result);
    }

    /**
     * @param string $date Date or Unix timestamp
     * @dataProvider provideDates
     * @throws \Exception on error
     */
    public function testReadByObjectAndDate(string $date) {
        $objectID = $this->createServer();

        $result = $this->instance->readByObject($objectID, $date);

        $this->assertInternalType('array', $result);

        $this->validateEntries($result);
    }

    /**
     * @param int $limit Limit
     * @dataProvider provideLimits
     * @throws \Exception on error
     */
    public function testReadByObjectAndLimit(int $limit) {
        $objectID = $this->createServer();

        $result = $this->instance->readByObject($objectID, null, $limit);

        $this->assertInternalType('array', $result);

        $this->validateEntries($result);
    }

    /**
     * @param array $entries
     *
     * @throws \Exception on error
     */
    protected function validateEntries(array $entries) {
        foreach ($entries as $entry) {
            $this->validateEntry($entry);
        }
    }

    /**
     * @param array $entry
     *
     * @throws \Exception on error
     */
    protected function validateEntry(array $entry) {
        $this->assertArrayHasKey('logbook_id', $entry);
        $this->isIDAsString($entry['logbook_id']);

        $this->assertArrayHasKey('logbook_catg_id', $entry);
        $this->isIDAsString($entry['logbook_catg_id']);

        $this->assertArrayHasKey('comment', $entry);
        $this->assertInternalType('string', $entry['comment']);

        $this->assertArrayHasKey('description', $entry);
        $this->assertInternalType('string', $entry['description']);

        $this->assertArrayHasKey('changes', $entry);
        if (isset($entry['changes']) && is_array($entry['changes'])) {
            // changes may be empty…

            foreach ($entry['changes'] as $index => $changeSet) {
                // changes may be nested or not…
                switch (gettype($index)) {
                    case 'integer':
                        $this->assertGreaterThanOrEqual(0, $index);

                        foreach ($changeSet as $source => $change) {
                            $this->assertInternalType('string', $source);
                            $this->assertNotEmpty($source);

                            $this->assertInternalType('array', $change);
                            $this->validateChange($change);
                        }
                        break;
                    case 'string':
                        $this->assertNotEmpty($index);

                        $this->assertInternalType('array', $changeSet);
                        $this->validateChange($changeSet);
                        break;
                    default:
                        throw new \DomainException('Invalid changeset');
                }
            }
        }

        $this->assertArrayHasKey('date', $entry);
        $this->isTime($entry['date']);

        $this->assertArrayHasKey('username', $entry);
        $this->assertInternalType('string', $entry['username']);

        $this->assertArrayHasKey('event', $entry);
        $this->assertInternalType('string', $entry['event']);

        $this->assertArrayHasKey('object_id', $entry);
        $this->isIDAsString($entry['object_id']);

        $this->assertArrayHasKey('object_title', $entry);
        $this->assertInternalType('string', $entry['object_title']);

        if (array_key_exists('object_title_static', $entry)) {
            $this->assertInternalType('string', $entry['object_title_static']);
        }

        $this->assertArrayHasKey('source', $entry);
        $this->assertInternalType('string', $entry['source']);

        $this->assertArrayHasKey('source_constant', $entry);
        $this->isConstant($entry['source_constant']);

        $this->assertArrayHasKey('level_id', $entry);
        $this->isIDAsString($entry['level_id']);
    }

    /**
     * Validate change
     *
     * @param array $change Change from … to …
     *
     * @throws \Exception on error
     */
    protected function validateChange(array $change) {
        $this->assertCount(2, $change);

        $this->assertArrayHasKey('from', $change);
        $this->assertInternalType('string', $change['from']);

        $this->assertArrayHasKey('to', $change);

        // 'to' is not consistent…
        switch (gettype($change['to'])) {
            case 'string':
                break;
            case 'array':
                break;
            default:
                throw new \DomainException('Invalid new value');
        }
    }

}
