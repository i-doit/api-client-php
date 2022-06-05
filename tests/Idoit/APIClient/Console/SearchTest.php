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

namespace Idoit\APIClient\Console;

use Idoit\APIClient\Constants\ObjectType;
use \Exception;
use Idoit\APIClient\BaseTest;

/**
 * @group API-57
 */
class SearchTest extends BaseTest {

    /**
     * @var Search
     */
    protected $search;

    /**
     * @throws Exception on error
     */
    public function setUp(): void {
        parent::setUp();

        $this->search = new Search($this->api);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateIndex() {
        $result = $this->search->createIndex();

        $this->assertIsArray($result);
        $this->isOutput($result);
    }

    /**
     * @throws Exception on error
     */
    public function testUpdateIndex() {
        $result = $this->search->updateIndex();

        $this->assertIsArray($result);
        $this->isOutput($result);
    }

    /**
     * @throws Exception on error
     */
    public function testQuery() {
        $title = $this->generateRandomString();

        $this->useCMDBObject()->create(
            ObjectType::SERVER,
            $title
        );

        $result = $this->search->query($title);

        $this->assertIsArray($result);
        $this->isOutput($result);
    }

}
