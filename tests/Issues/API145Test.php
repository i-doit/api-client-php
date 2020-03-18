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
 * @group unreleased
 * @group issues
 * @group API-145
 * @see https://i-doit.atlassian.net/browse/API-145
 */
class API145Test extends BaseTest {

    /**
     * @return array
     */
    public function provideAttributes(): array {
        return [
            ['C__CATG__ASSIGNED_CARDS', 'connected_obj'],
            ['C__CATG__ASSIGNED_LOGICAL_UNIT', 'assigned_object'],
            ['C__CATG__ASSIGNED_SIM_CARDS', 'isys_catg_cards_list__id'],
            ['C__CATG__CLUSTER_ADM_SERVICE', 'connected_object'],
            ['C__CATG__CLUSTER_MEMBERS', 'member'],
            ['C__CATG__CLUSTER_MEMBERSHIPS', 'connected_object'],
            ['C__CATG__CONTACT', 'contact_object'],
            ['C__CATG__GUEST_SYSTEMS', 'connected_object'],
            ['C__CATG__IT_SERVICE_COMPONENTS', 'connected_object'],
            ['C__CATG__IT_SERVICE', 'connected_object'],
            ['C__CATG__QINQ_CE', 'spvlan'],
            ['C__CATG__RM_CONTROLLER_BACKWARD', 'connected_object'],
            ['C__CATG__STACKING', 'assigned_object'],
            ['C__CATG__OBJECT', 'assigned_object'],
            ['C__CATS__CONTRACT_ALLOCATION', 'assigned_object'],
            ['C__CATS__DATABASE_ACCESS', 'access'],
            ['C__CATS__GROUP', 'object'],
            ['C__CATS__LAYER2_NET_ASSIGNED_LOGICAL_PORTS', 'isys_catg_log_port_list__id'],
            ['C__CATS__LAYER2_NET_ASSIGNED_PORTS', 'isys_catg_port_list__id'],
            ['C__CATS__ORGANIZATION_PERSONS', 'object'],
            ['C__CATS__PERSON_ASSIGNED_GROUPS', 'connected_object'],
            ['C__CATS__PERSON_GROUP_MEMBERS', 'connected_object'],
            ['C__CATS__WS_ASSIGNMENT', 'connected_object']
        ];
    }

    /**
     * @dataProvider provideAttributes
     * @param string $categoryConstant Category constant
     * @param string $attributeTitle Attribute title
     * @throws Exception on error
     */
    public function testAssignSingleObject(string $categoryConstant, string $attributeTitle) {
        /**
         * Create test data:
         */

        $objectAID = $this->createServer();
        $this->isID($objectAID);

        $objectBID = $this->createServer();
        $this->isID($objectBID);

        /**
         * Run tests:
         */

        $entryID = $this->useCMDBCategory()->save(
            $objectAID,
            $categoryConstant,
            [
                $attributeTitle => $objectBID
            ]
        );

        $this->isID($entryID);
    }

    /**
     * @dataProvider provideAttributes
     * @param string $categoryConstant Category constant
     * @param string $attributeTitle Attribute title
     * @throws Exception on error
     */
    public function testAssignMultipleObjects(string $categoryConstant, string $attributeTitle) {
        /**
         * Create test data:
         */

        $objectAID = $this->createServer();
        $this->isID($objectAID);

        $objectBID = $this->createServer();
        $this->isID($objectBID);

        $objectCID = $this->createServer();
        $this->isID($objectCID);

        /**
         * Run tests:
         */

        $this->expectException(Exception::class);

        $this->useCMDBCategory()->save(
            $objectAID,
            $categoryConstant,
            [
                $attributeTitle => [
                    $objectBID,
                    $objectCID
                ]
            ]
        );
    }

}
