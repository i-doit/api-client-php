<?php

/**
 * Copyright (C) 2016-17 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-17 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

use PHPUnit\Framework\TestCase;
use bheisig\idoitapi\API;
use bheisig\idoitapi\File;

class FileTest extends TestCase {

    /**
     * @var \bheisig\idoitapi\API
     */
    protected $api;

    /**
     * @var \bheisig\idoitapi\File
     */
    protected $instance;

    protected $files = [];

    public function setUp() {
        $this->api = new API([
            'url' => $GLOBALS['url'],
            'key' => $GLOBALS['key'],
            'username' => $GLOBALS['username'],
            'password' => $GLOBALS['password']
        ]);

        $this->instance = new File($this->api);

        for ($i = 1; $i <= 3; $i++) {
            $filePath = sprintf(
                '/tmp/file%s.txt',
                $i
            );
            $description = sprintf(
                'API Test %s @ %s',
                $i,
                microtime(true)
            );
            $this->files[$filePath] = $description;
        }
    }

    public function testAdd() {
        foreach ($this->files as $filePath => $description) {
            $status = file_put_contents($filePath, $description);

            if ($status === false) {
                throw new \Exception('Unable to create test file');
            }

            $this->assertInstanceOf(
                File::class,
                $this->instance->add(9, $filePath, $description)
            );
        }
    }

    public function testBatchAdd() {
        $this->assertInstanceOf(
            File::class,
            $this->instance->batchAdd(10, $this->files)
        );
    }

    public function testEncode() {
        foreach ($this->files as $filePath => $description) {
            $status = file_put_contents($filePath, $description);

            if ($status === false) {
                throw new \Exception('Unable to create test file');
            }

            $fileAsString = $this->instance->encode($filePath);

            $this->assertInternalType('string', $fileAsString);
            $this->assertNotEmpty($fileAsString);
        }
    }

}
