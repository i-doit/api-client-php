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

namespace bheisig\idoitapi\tests\Console;

use bheisig\idoitapi\Console\Console;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group unreleased
 * @group API-57
 */
class ConsoleTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\Console\Console
     */
    protected $console;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->console = new Console($this->api);
    }

    /**
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testExecuteNothing() {
        $result = $this->console->execute('');

        $this->assertInternalType('array', $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testExecuteSimpleCommand() {
        $result = $this->console->execute(
            'console.search.query',
            [
                'searchString' => 'test'
            ]
        );

        $this->assertInternalType('array', $result);
        $this->isOutput($result);
    }

    /**
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testExecuteUnknownCommand() {
        $result = $this->console->execute(
            'console.commands.' . $this->generateRandomString()
        );

        $this->assertInternalType('array', $result);
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
     * @expectedException \RuntimeException
     * @param string $blacklistedCommand Blacklisted command
     * @throws \Exception on error
     */
    public function testExecuteBlacklistedCommand(string $blacklistedCommand) {
        $result = $this->console->execute($blacklistedCommand);
        $this->assertInternalType('array', $result);
    }

}
