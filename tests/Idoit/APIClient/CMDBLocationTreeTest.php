<?php

/**
 * Copyright (C) 2022 synetics GmbH
 * Copyright (C) 2016-2022 Benjamin Heisig
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
 * @copyright Copyright (C) 2022 synetics GmbH
 * @copyright Copyright (C) 2016-2022 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/i-doit/api-client-php
 */

declare(strict_types=1);

namespace Idoit\APIClient;

use \Exception;

/**
 * @todo Test various self-defined location trees
 * @todo Test various object states (normal, archive, deleted, …)
 * @todo Test more root nodes than only root location object
 */
class CMDBLocationTreeTest extends BaseTest {

    /**
     * @var CMDBLocationTree
     */
    protected $instance;

    /**
     * @throws Exception on error
     */
    public function setUp(): void {
        parent::setUp();

        $this->instance = new CMDBLocationTree($this->api);
    }

    /**
     * @throws Exception on error
     */
    public function testRead() {
        $result = $this->instance->read($this->getRootLocation());

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);
        $this->isLocationTree($result);
    }

    /**
     * @throws Exception on error
     * @todo Test various levels
     */
    public function testReadRecursively() {
        $result = $this->instance->readRecursively($this->getRootLocation());

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);
        $this->isLocationTree($result);
    }

}
