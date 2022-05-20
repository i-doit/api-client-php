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

use \Exception;
use \RuntimeException;
use Idoit\APIClient\BaseTest;

/**
 * @group API-57
 */
class ConsoleTest extends BaseTest {

    /**
     * @var Console
     */
    protected $console;

    /**
     * @throws Exception on error
     */
    public function setUp(): void {
        parent::setUp();

        $this->console = new Console($this->api);
    }

    /**
     * @throws Exception on error
     */
    public function testExecuteNothing() {
        $this->expectException(Exception::class);

        $result = $this->console->execute('');

        $this->assertIsArray($result);
    }

    /**
     * @throws Exception on error
     */
    public function testExecuteSimpleCommand() {
        $result = $this->console->execute(
            'console.search.query',
            [
                'searchString' => 'test'
            ]
        );

        $this->assertIsArray($result);
        $this->isOutput($result);
    }

    /**
     * @throws Exception on error
     */
    public function testExecuteUnknownCommand() {
        $this->expectException(Exception::class);

        $result = $this->console->execute(
            'console.commands.' . $this->generateRandomString()
        );

        $this->assertIsArray($result);
    }

    /**
     * List of blacklisted commands
     * @return array Blacklisted commands
     */
    public function provideBlacklistedCommands(): array {
        return [
            'console.system.checkforupdates' => ['console.system.checkforupdates'],
            'console.system.update' => ['console.system.update'],
            '' => ['console.tenant.add'],
            'console.tenant.add' => ['console.tenant.disable'],
            'console.tenant.enable' => ['console.tenant.enable'],
            'console.tenant.list' => ['console.tenant.list'],
        ];
    }

    /**
     * @dataProvider provideBlacklistedCommands
     * @param string $blacklistedCommand Blacklisted command
     * @throws Exception on error
     */
    public function testExecuteBlacklistedCommand(string $blacklistedCommand) {
        $this->expectException(RuntimeException::class);

        $result = $this->console->execute($blacklistedCommand);
        $this->assertIsArray($result);
    }

}
