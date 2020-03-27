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

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\CMDBCategory;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group ID-7100
 * @see https://i-doit.atlassian.net/browse/ID-7100
 */
class ID7100Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testSaveCustomCategoryEntry() {
        $categoryConstant = 'C__CATG__CUSTOM_FIELDS_ID_7100';
        $attributeID = 'f_wysiwyg_c_1570463000473';
        // We need more than 255 characters:
        $value = <<<EOF
Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. 
Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Quis aute 
iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat 
non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
EOF;

        $this->markTestSkipped(
            'Custom category needed!'
        );

        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        /**
         * Run tests:
         */

        // This failed in the past:
        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            $categoryConstant,
            [
                $attributeID => $value
            ]
        );

        $this->isID($entryID);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateCustomCategoryEntry() {
        $categoryConstant = 'C__CATG__CUSTOM_FIELDS_ID_7100';
        $attributeID = 'f_wysiwyg_c_1570463000473';
        // We need more than 255 characters:
        $value = <<<EOF
Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. 
Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Quis aute 
iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat 
non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
EOF;

        $this->markTestSkipped(
            'Custom category needed!'
        );

        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        /**
         * Run tests:
         */

        // This failed in the past:
        $entryID = $this->useCMDBCategory()->create(
            $objectID,
            $categoryConstant,
            [
                $attributeID => $value
            ]
        );

        $this->isID($entryID);
    }

    /**
     * @throws Exception on error
     */
    public function testUpdateCustomCategoryEntry() {
        $categoryConstant = 'C__CATG__CUSTOM_FIELDS_ID_7100';
        $attributeID = 'f_wysiwyg_c_1570463000473';
        // We need more than 255 characters:
        $value = <<<EOF
Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. 
Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Quis aute 
iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat 
non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
EOF;

        $this->markTestSkipped(
            'Custom category needed!'
        );

        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        /**
         * Run tests:
         */

        // This failed in the past:
        $entryID = $this->useCMDBCategory()->create(
            $objectID,
            $categoryConstant,
            [
                $attributeID => $value
            ]
        );

        $this->isID($entryID);

        // This failed in the past:
        $result = $this->useCMDBCategory()->update(
            $objectID,
            $categoryConstant,
            [
                $attributeID => $value
            ]
        );

        $this->assertInstanceOf(CMDBCategory::class, $result);
    }

}
