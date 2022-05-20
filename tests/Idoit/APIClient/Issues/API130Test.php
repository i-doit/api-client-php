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

namespace Idoit\APIClient\Issues;

use \Exception;
use Idoit\APIClient\BaseTest;
use Idoit\APIClient\Constants\Category;
use Idoit\APIClient\Constants\ObjectType;

/**
 * @group issues
 * @group API-130
 * @group API-108
 * @see https://i-doit.atlassian.net/browse/API-130
 * @see https://i-doit.atlassian.net/browse/API-108
 */
class API130Test extends BaseTest {

    /**
     * @throws Exception
     */
    public function testIssue() {
        $objectID = $this->useCMDBObject()->create(ObjectType::SERVER, 'My host');
        $applicationID = $this->useCMDBObject()->create(ObjectType::APPLICATION, 'My app');
        $licenseID = $this->useCMDBObject()->create(ObjectType::LICENCE, 'My license');
        $licenseKeyID = $this->useCMDBCategory()->create($licenseID, Category::CATS__LICENCE_LIST, [
            'key' => '123-xyz',
            'type' => 2, // Volume
            'amount' => 1000
        ]);
        // This failed because of last two attributes:
        $entryID = $this->useCMDBCategory()->create($objectID, Category::CATG__APPLICATION, [
            'application' => $applicationID,
            'assigned_license' => $licenseKeyID
        ]);
        $this->assertIsInt($entryID);
        $this->assertGreaterThan(0, $entryID);
    }

}
