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
use bheisig\idoitapi\MonitoringLivestatus;

/**
 * @coversDefaultClass \bheisig\idoitapi\MonitoringLivestatus
 */
class MonitoringLivestatusTest extends BaseTest {

    /**
     * @var MonitoringLivestatus
     */
    protected $instance;

    /**
     * @throws Exception on error
     */
    public function setUp(): void {
        parent::setUp();

        $this->instance = new MonitoringLivestatus($this->api);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateTCPConnection() {
        $result = $this->instance->createTCPConnection(
            $this->generateRandomString()
        );

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);

        $result = $this->instance->createTCPConnection(
            $this->generateRandomString(),
            $this->generateIPv4Address()
        );

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);

        $result = $this->instance->createTCPConnection(
            $this->generateRandomString(),
            $this->generateIPv4Address(),
            1234
        );

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);

        $result = $this->instance->createTCPConnection(
            $this->generateRandomString(),
            $this->generateIPv4Address(),
            1234,
            false
        );

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateUNIXSocketConnection() {
        $result = $this->instance->createUNIXSocketConnection(
            $this->generateRandomString(),
            '/var/run/livestatus'
        );

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);

        $result = $this->instance->createUNIXSocketConnection(
            $this->generateRandomString(),
            '/var/run/livestatus',
            false
        );

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testReadExisting() {
        // We need at least one instance:
        $this->instance->createTCPConnection(
            $this->generateRandomString()
        );
        $this->instance->createUNIXSocketConnection(
            $this->generateRandomString(),
            '/var/run/livestatus'
        );

        $result = $this->instance->read();

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);

        foreach ($result as $instance) {
            $this->assertArrayHasKey('id', $instance);
            $this->assertIsInt($instance['id']);
            $this->assertGreaterThan(0, $instance['id']);

            $this->assertArrayHasKey('title', $instance);
            $this->assertIsString($instance['title']);

            $this->assertArrayHasKey('active', $instance);
            $this->assertIsBool($instance['active']);

            $this->assertArrayHasKey('connection', $instance);
            $this->assertIsString($instance['connection']);

            $this->assertContains($instance['connection'], ['tcp', 'unix']);

            switch ($instance['connection']) {
                case 'tcp':
                    $this->assertArrayHasKey('address', $instance);
                    $this->assertIsString($instance['address']);

                    $this->assertArrayHasKey('port', $instance);
                    $this->assertIsInt($instance['port']);
                    $this->assertGreaterThan(0, $instance['port']);
                    $this->assertLessThanOrEqual(65535, $instance['port']);
                    break;
                case 'unix':
                    $this->assertArrayHasKey('path', $instance);
                    $this->assertIsString($instance['path']);
                    break;
            }
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadNonExisting() {
        // Make sure there are no instances:
        $this->instance->deleteAll();

        $result = $this->instance->read();

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByExistingIdentifier() {
        $title = $this->generateRandomString();

        $id = $this->instance->createTCPConnection(
            $title
        );

        $result = $this->instance->readByID($id);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        $this->assertArrayHasKey(0, $result);
        $this->assertIsArray($result[0]);

        $this->assertSame($id, $result[0]['id']);
        $this->assertSame($title, $result[0]['title']);
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
        $amount = 3;
        $ids = [];

        for ($i = 0; $i < $amount; $i++) {
            $ids[] = $this->instance->createTCPConnection(
                $this->generateRandomString()
            );
        }

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
    public function testReadByExistingTitle() {
        $title = $this->generateRandomString();

        $id = $this->instance->createTCPConnection(
            $title
        );

        $result = $this->instance->readByTitle($title);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($title, $result[0]['title']);
        $this->assertSame($id, $result[0]['id']);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByNonExistingTitle() {
        $title = $this->generateRandomString();

        $result = $this->instance->readByTitle($title);

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testUpdateExisting() {
        $orig = [
            'title' => $this->generateRandomString(),
            'address' => $this->generateIPv4Address(),
            'port' => 6557,
            'active' => false
        ];

        $id = $this->instance->createTCPConnection(
            (string) $orig['title'],
            (string) $orig['address'],
            (int) $orig['port'],
            (bool) $orig['active']
        );

        $altered = [
            'title' => $this->generateRandomString(),
            'address' => $this->generateIPv4Address(),
            'port' => 7556,
            'active' => true
        ];

        $result = $this->instance->update(
            $id,
            $altered
        );

        $this->assertInstanceOf(MonitoringLivestatus::class, $result);

        $alteredInstance = $this->instance->readByID($id);

        $this->assertIsArray($alteredInstance);
        $this->assertCount(1, $alteredInstance);
        $this->assertSame($id, $alteredInstance[0]['id']);

        $this->assertNotEquals($orig['title'], $alteredInstance[0]['title']);
        $this->assertNotEquals($orig['address'], $alteredInstance[0]['address']);
        $this->assertNotEquals($orig['port'], $alteredInstance[0]['port']);
        $this->assertNotEquals($orig['active'], $alteredInstance[0]['active']);
    }

    /**
     * @throws Exception on error
     */
    public function testUpdateNonExisting() {
        $this->expectException(Exception::class);

        // It is unlikely to produce such high IDs but this *could* fail:
        $id = 99999999;

        $attributes = [
            'title' => $this->generateRandomString(),
            'address' => $this->generateIPv4Address(),
            'port' => 7556,
            'active' => true
        ];

        // Bad:
        $this->instance->update($id, $attributes);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteByExistingIdentifier() {
        $id = $this->instance->createTCPConnection(
            $this->generateRandomString()
        );

        $result = $this->instance->deleteByID($id);

        $this->assertInstanceOf(MonitoringLivestatus::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteByNonExistingIdentifier() {
        // It is unlikely to produce such high IDs but this *could* fail:
        $id = 99999999;

        // Bad:
        // i-doit API says this is a valid operation :-(
        $result = $this->instance->deleteByID($id);
        $this->assertInstanceOf(MonitoringLivestatus::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteByExistingTitle() {
        $title = $this->generateRandomString();
        $this->instance->createTCPConnection(
            $title
        );

        $result = $this->instance->deleteByTitle($title);

        $this->assertInstanceOf(MonitoringLivestatus::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteByNonExistingTitle() {
        $title = $this->generateRandomString();

        // Bad:
        // i-doit API says this is a valid operation :-(
        $result = $this->instance->deleteByTitle($title);
        $this->assertInstanceOf(MonitoringLivestatus::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteDeletedOne() {
        $id = $this->instance->createTCPConnection(
            $this->generateRandomString()
        );

        // Good:
        $result = $this->instance->deleteByID($id);
        $this->assertInstanceOf(MonitoringLivestatus::class, $result);

        // Bad:
        // i-doit API says this is a valid operation :-(
        $result = $this->instance->deleteByID($id);
        $this->assertInstanceOf(MonitoringLivestatus::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testBatchDeleteExisting() {
        $amount = 3;
        $ids = [];

        for ($i = 0; $i < $amount; $i++) {
            $ids[] = $this->instance->createTCPConnection(
                $this->generateRandomString()
            );
        }

        $result = $this->instance->batchDelete($ids);

        $this->assertInstanceOf(MonitoringLivestatus::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testBatchDeleteDeletedOnes() {
        $amount = 3;
        $ids = [];

        for ($i = 0; $i < $amount; $i++) {
            $ids[] = $this->instance->createTCPConnection(
                $this->generateRandomString()
            );
        }

        // Good:
        $result = $this->instance->batchDelete($ids);
        $this->assertInstanceOf(MonitoringLivestatus::class, $result);

        // Bad:
        // i-doit API says this is a valid operation :-(
        $result = $this->instance->batchDelete($ids);
        $this->assertInstanceOf(MonitoringLivestatus::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteAllExisting() {
        $amount = 3;

        for ($i = 0; $i < $amount; $i++) {
            $this->instance->createTCPConnection(
                $this->generateRandomString()
            );
        }

        $result = $this->instance->deleteAll();

        $this->assertInstanceOf(MonitoringLivestatus::class, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteAllNonExisting() {
        $amount = 3;

        for ($i = 0; $i < $amount; $i++) {
            $this->instance->createTCPConnection(
                $this->generateRandomString()
            );
        }

        // Good:
        $result = $this->instance->deleteAll();
        $this->assertInstanceOf(MonitoringLivestatus::class, $result);

        // Bad:
        // i-doit API says this is a valid operation :-(
        $result = $this->instance->deleteAll();
        $this->assertInstanceOf(MonitoringLivestatus::class, $result);
    }

}
