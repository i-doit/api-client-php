<?php

/**
 * Copyright (C) 2016-2020 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-2020 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi\Extension;

class Statistics {

    const API_REQUEST_COUNTER = 'API requests';

    protected static $instance;

    protected $statistics = [];

    protected function __construct() {
        // Make constructor non-public!
    }

    final private function __clone() {
        // Prohibit cloning object!
    }

    /**
     * @return self Returns itself
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            $className = self::class;
            self::$instance = new $className();
        }

        return self::$instance;
    }

    /**
     * @param string $topic
     * @param int $add
     *
     * @return self Returns itself
     */
    public function update(string $topic, int $add): self {
        if (!array_key_exists($topic, $this->statistics)) {
            $this->statistics[$topic] = 0;
        }

        $this->statistics['API requests'] += $add;

        return $this;
    }

    /**
     * @return self Returns itself
     */
    public function print(): self {
        if (count($this->statistics) === 0) {
            return $this;
        }

        foreach ($this->statistics as $topic => $counter) {
            fwrite(STDOUT, PHP_EOL . sprintf('%s: %s', $topic, $counter));
        }

        return $this;
    }

}
