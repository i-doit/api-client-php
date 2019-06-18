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

namespace bheisig\idoitapi\tests\Issues;

use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group ID-6888
 * @group unreleased
 * @see https://i-doit.atlassian.net/browse/ID-6888
 */
class ID6888Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testIssue() {
        $amountOfObjects = 500;
        $amountOfEntries = 1000;

        for ($objectIndex = 1; $objectIndex <= $amountOfObjects; $objectIndex++) {
            $objectID = $this->createServer();
            $this->isID($objectID);

            for ($entryIndex = 1; $entryIndex <= $amountOfEntries; $entryIndex++) {
                $entryID = $this->cmdbCategory->save(
                    $objectID,
                    'C__CATG__CUSTOM_FIELDS_ID_6888',
                    [
                        'f_popup_c_1560862284753' => $this->generateRandomString(),
                        'f_popup_c_1560862328326' => $this->generateDate(),
                        'f_popup_c_1560862336873' => $this->generateDate(),
                        'f_text_c_1560862347721' => $this->generateRandomString(),
                        'f_text_c_1560862353509' => $this->generateRandomString(),
                        'f_text_c_1560862358903' => $this->generateRandomString(),
                        'f_text_c_1560862364012' => $this->generateRandomString(),
                        'f_popup_c_1560862370314' => $this->generateRandomString(),
                        'f_popup_c_1560862383519' => $this->generateDate()
                    ]
                );
                $this->isID($entryID);
            }
        }
    }

}
