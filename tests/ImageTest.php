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

use bheisig\idoitapi\Image;

/**
 * @coversDefaultClass \bheisig\idoitapi\Image
 */
class ImageTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\Image
     */
    protected $instance;

    /**
     * @var array
     */
    protected $files = [];

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new Image($this->api);

        foreach (['bmp', 'gif', 'jpg', 'png', 'svg', 'tif'] as $format) {
            $path = __DIR__ . '/data/test.' . $format;
            $this->files[$path] = strtoupper($format);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testAdd() {
        $objectID = $this->createServer();

        foreach ($this->files as $filePath => $caption) {
            $this->assertInstanceOf(
                Image::class,
                $this->instance->add($objectID, $filePath, $caption)
            );
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchAdd() {
        $objectID = $this->createServer();

        $this->assertInstanceOf(
            Image::class,
            $this->instance->batchAdd($objectID, $this->files)
        );
    }

    /**
     * @throws \Exception on error
     */
    public function testEncode() {
        foreach ($this->files as $filePath => $description) {
            $fileAsString = $this->instance->encode($filePath);

            $this->assertInternalType('string', $fileAsString);
            $this->assertNotEmpty($fileAsString);
        }
    }

}
