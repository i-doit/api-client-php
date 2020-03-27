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

use bheisig\idoitapi\tests\Constants\Category;
use bheisig\idoitapi\tests\Constants\ObjectType;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-132
 * @see https://i-doit.atlassian.net/browse/API-132
 */
class API132Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testIssue() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $fileName = $this->generateRandomString() . '.test';

        $fileObjectID = $this->useCMDBObject()->create(
            ObjectType::FILE,
            $fileName
        );
        $this->isID($fileObjectID);

        // This failed because category constant was re-named in i-doit 1.11:
        $versionEntryID = $this->useCMDBCategory()->create(
            $fileObjectID,
            'C__CMDB__SUBCAT__FILE_VERSIONS',
            [
                'file_content' => base64_encode($this->generateDescription()),
                'file_physical' => $fileName,
                'file_title' => $fileName,
                'version_description' => $fileName
            ]
        );
        $this->isID($versionEntryID);

        $fileEntryID = $this->useCMDBCategory()->create(
            $objectID,
            Category::CATG__FILE,
            [
                'file' => $fileObjectID
            ]
        );
        $this->isID($fileEntryID);
    }

}
