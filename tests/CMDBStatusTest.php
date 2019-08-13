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
use bheisig\idoitapi\CMDBStatus;

/**
 * @group unreleased
 * @group open
 */
class CMDBStatusTest extends BaseTest {

    /**
     * @var CMDBStatus
     */
    protected $instance;

    /**
     * @throws Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBStatus($this->api);
    }

    /**
     * @throws Exception on error
     */
    public function testReadAllStates() {
        /**
         * Run tests:
         */

        $states = $this->instance->read();

        $this->assertIsArray($states);
        $this->assertNotCount(0, $states);

        foreach ($states as $index => $status) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsArray($status);

            $this->isStatus($status);
        }
    }

    protected function isStatus(array $status) {
        $this->assertArrayHasKey(CMDBStatus::ATTRIBUTE_ID, $status);
        $this->isID($status[CMDBStatus::ATTRIBUTE_ID]);

        $this->assertArrayHasKey(CMDBStatus::ATTRIBUTE_TITLE, $status);
        $this->assertIsString($status[CMDBStatus::ATTRIBUTE_TITLE]);
        $this->assertGreaterThan(0, strlen($status[CMDBStatus::ATTRIBUTE_TITLE]));

        $this->assertArrayHasKey(CMDBStatus::ATTRIBUTE_CONSTANT, $status);
        $this->assertIsString($status[CMDBStatus::ATTRIBUTE_CONSTANT]);
        $this->isConstant($status[CMDBStatus::ATTRIBUTE_CONSTANT]);

        $this->assertArrayHasKey(CMDBStatus::ATTRIBUTE_COLOR, $status);
        $this->assertIsString($status[CMDBStatus::ATTRIBUTE_COLOR]);
        $this->assertEquals(6, strlen($status[CMDBStatus::ATTRIBUTE_COLOR]));

        $this->assertArrayHasKey(CMDBStatus::ATTRIBUTE_EDITABLE, $status);
        $this->assertIsBool($status[CMDBStatus::ATTRIBUTE_EDITABLE]);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateNewStatus() {
        /**
         * Generate test data:
         */

        $title = $this->generateRandomString();
        $constant = 'C__' . strtoupper($title);
        $color = $this->generateRGB();

        /**
         * Run tests:
         */

        $result = $this->instance->save(
            $title,
            $constant,
            $color
        );

        $this->isID($result);

        /**
         * Double check:
         */

        $found = $this->isExisting($title, $constant, $color, true);

        $this->assertSame(true, $found);
    }

    /**
     * @param string $title
     * @param string $constant
     * @param string $color
     * @param bool $editable
     *
     * @return bool
     *
     * @throws Exception on error
     */
    protected function isExisting(string $title, string $constant, string $color, bool $editable): bool {
        $states = $this->instance->read();
        $this->assertIsArray($states);

        foreach ($states as $status) {
            $this->isStatus($status);

            if ($status[CMDBStatus::ATTRIBUTE_TITLE] !== $title) {
                continue;
            }

            if ($status[CMDBStatus::ATTRIBUTE_CONSTANT] !== $constant) {
                continue;
            }

            if ($status[CMDBStatus::ATTRIBUTE_COLOR] !== $color) {
                continue;
            }

            if ($status[CMDBStatus::ATTRIBUTE_EDITABLE] !== $editable) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * @throws Exception on error
     */
    public function testUpdateExistingStatus() {
        /**
         * Generate test data:
         */

        $title = $this->generateRandomString();
        $constant = 'C__' . strtoupper($title);
        $color = $this->generateRGB();

        $identifier = $this->instance->save(
            $title,
            $constant,
            $color
        );

        $this->isID($identifier);

        /**
         * Run tests:
         */

        $newTitle = $this->generateRandomString();
        $newConstant = 'C__' . strtoupper($newTitle);
        $newColor = $this->generateRGB();

        $result = $this->instance->save(
            $newTitle,
            $newConstant,
            $newColor,
            $identifier
        );

        $this->isID($result);
        $this->assertSame($identifier, $result);

        /**
         * Double check:
         */

        $notFound = $this->isExisting($title, $constant, $color, true);
        $found = $this->isExisting($newTitle, $newConstant, $newColor, true);

        $this->assertSame(false, $notFound);
        $this->assertSame(true, $found);
    }

    /**
     * @throws Exception on error
     */
    public function testUpdateNonExistingStatus() {
        /**
         * Generate test data:
         */

        $nonExistingIdentifier = $this->generateRandomID();

        /**
         * Run tests:
         */

        $newTitle = $this->generateRandomString();
        $newConstant = 'C__' . strtoupper($newTitle);
        $newColor = $this->generateRGB();

        $this->expectException(Exception::class);

        $this->instance->save(
            $newTitle,
            $newConstant,
            $newColor,
            $nonExistingIdentifier
        );
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteExistingStatus() {
        /**
         * Generate test data:
         */

        $title = $this->generateRandomString();
        $constant = 'C__' . strtoupper($title);
        $color = $this->generateRGB();

        $statusID = $this->instance->save(
            $title,
            $constant,
            $color
        );

        $this->isID($statusID);

        /**
         * Run tests:
         */

        $result = $this->instance->delete($statusID);

        $this->assertInstanceOf(CMDBStatus::class, $result);

        /**
         * Double check:
         */

        $found = $this->isExisting($title, $constant, $color, true);

        $this->assertSame(false, $found);
    }

    /**
     * @throws Exception on error
     */
    public function testDeleteNonExistingStatus() {
        /**
         * Run tests:
         */

        $this->expectException(Exception::class);
        $this->instance->delete($this->generateRandomID());
    }

}
