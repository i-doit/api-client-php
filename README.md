# i-doit API client library

Easy-to-use, but feature-rich client library for i-doit's JSON-RPC API

[![Latest stable version](https://img.shields.io/packagist/v/bheisig/idoitapi.svg)](https://packagist.org/packages/bheisig/idoitapi)
[![Minimum PHP version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![Build status](https://github.com/bheisig/i-doit-api-client-php/actions/workflows/main.yml/badge.svg?branch=master)](https://github.com/bheisig/i-doit-api-client-php/actions)

## About

[i-doit](https://i-doit.com) is a software application for IT documentation and a CMDB (Configuration Management Database). This application is very useful to collect all your knowledge about the IT infrastructure you are dealing with. i-doit is a Web application and [has an exhausting API](https://kb.i-doit.com/pages/viewpage.action?pageId=37355644) which is very useful to automate your infrastructure.

This API client library provides a simple, but powerful abstraction layer to send requests to i-doit's API. It is written in pure PHP.

## Features

Why should you use this API client library? There are some good reasons:

-   Easy to use
-   There is a PHP function for each API method
-   Supports batch requests (much faster)
-   Sends user-defined requests
-   Uploads files and images
-   Supports login and logout methods to save sessions
-   Follows redirects (HTTP 301/302) automatically
-   Uses optional HTTP or SOCKS5 proxy
-   Verifies TLS certificate chains
-   Throws exception on error
-   Many examples
-   Well-documented
-   Easy to install via Composer
-   Well-tested with tons of integration tests

What's new? Take a look at the [changelog](CHANGELOG.md).

## Requirements

Meet these simple requirements before using the client:

-   A running instance of i-doit pro/open, version `1.18.1` or higher (older versions may work but are not supported)
-   i-doit API add-on, version `1.12.3` or higher (older versions may work but are not supported)
-   PHP, version `7.4` or higher (`8.0` is recommended)
-   PHP modules `curl`, `date`, `json`, `openssl` and `zlib`

As a rule of thumb, always use the latest stable releases to benefit from new features, improvements and bug fixes.

## Installation

It is recommended to install this client via [Composer](https://getcomposer.org/). Change to your project's root directory and fetch the latest stable version:

~~~ {.bash}
composer require bheisig/idoitapi
~~~

Instead of sticking to a specific/minimum version you may switch to the current development branch by using `@DEV`:

~~~ {.bash}
composer require "bheisig/idoitapi=@DEV"
~~~

## Updates

Composer has the great advantage (besides many others) that you can simply update the API client library by running:

~~~ {.bash}
composer update
~~~

## Usage

If you use Composer you should use its own autoloader, too:

~~~ {.php}
require_once 'vendor/autoload.php';
~~~

This is it. All other files will be auto-loaded on-the-fly if needed.

## Configuration

The API client library class requires a configuration:

~~~ {.php}
use bheisig\idoitapi\API;

$api = new API([
    API::URL => 'https://demo.i-doit.com/src/jsonrpc.php',
    API::PORT => 443,
    API::KEY => 'c1ia5q',
    API::USERNAME => 'admin',
    API::PASSWORD => 'admin',
    API::LANGUAGE => 'en',
    API::PROXY => [
        API::PROXY_ACTIVE => false,
        API::PROXY_TYPE => 'HTTP', // 'HTTP' or 'SOCKS5'
        API::PROXY_HOST => 'proxy.example.net',
        API::PROXY_PORT => 8080,
        API::PROXY_USERNAME => '',
        API::PROXY_PASSWORD => ''
    ],
    API::BYPASS_SECURE_CONNECTION => false
]);
~~~

-   `API::URL`: URL to i-doit's API, probably the base URL appended by `src/jsonrpc.php`
-   `API::PORT`: optional port on which the Web server listens; if not set port 80 will be used for HTTP and 443 for HTTPS
-   `API::KEY`: API key
-   `API::USERNAME` and `API::PASSWORD`: optional credentials if needed, otherwise `System API` user will be used
-   `API::LANGUAGE`: requests to and responses from i-doit will be translated to this language (`de` and `en` supported); this is optional; defaults to user's prefered language
-   `API::PROXY`: use a proxy between client and server
    -   `API::PROXY_ACTIVE`: if `true` proxy settings will be used
    -   `API::PROXY_TYPE`: use a HTTP (`API::PROXY_TYPE_HTTP`) or a SOCKS5 (`API::PROXY_TYPE_SOCKS5`) proxy
    -   `API::PROXY_HOST`: FQDN or IP address to proxy
    -   `API::PROXY_PORT`: port on which the proxy server listens
    -   `API::PROXY_USERNAME` and `API::PROXY_PASSWORD`: optional credentials used to authenticate against the proxy
-   `API::BYPASS_SECURE_CONNECTION`: Set to `true` to disable security-related cURL options; defaults to `false`; do not set this in production!

## Examples

A basic "Hello, World!" example is to fetch some basic information about your i-doit instance:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\Idoit;

require_once 'vendor/autoload.php';

$api = new API([
    API::URL => 'https://demo.i-doit.com/src/jsonrpc.php',
    API::KEY => 'c1ia5q',
    API::USERNAME => 'admin',
    API::PASSWORD => 'admin'
]);

$request = new Idoit($api);
$info = $request->readVersion();

var_dump($info);
~~~

It is simple like that. For more examples take a look at the next sub sections.

### Login and logout

One sweet thing about i-doit's API you can (and should) use one user session for your stuff. This saves resources on the server side and allows you to perform a lot more calls in a short time.

The session handling is done by the API client library. You just need to login. And if you are nice you want to logout after your work is done.

~~~ {.php}
use bheisig\idoitapi\API;

$api = new API([/* … */]);

$api->login();
// Do your stuff…
$api->logout();
~~~

If you are unsure in which condition your session is try `isLoggedIn()`:

~~~ {.php}
$api->isLoggedIn(); // Returns true or false
~~~

### Pre-defined methods

For almost every case there is a remote procedure you may call to read from or manipulate i-doit's database through its API. Each remote procedure is assigned to a namespace to keep the API clean and smoothly. Furtunately, you do not need to call these remote procedures on your own. The API client library provides for each namespace a class and for each remote procedure a method. Here is a quick overview:

| Namespace                     | Remote Procedure Call (RPC)           | Class in API Client Library   | Method                                                    |
| ----------------------------- | ------------------------------------- | ----------------------------- | --------------------------------------------------------- |
| `idoit`                       | `idoit.addons`                        | `Idoit`                       | `getAddOns()`                                             |
|                               | `idoit.constants`                     |                               | `readConstants()`                                         |
|                               | `idoit.license`                       |                               | `getLicense()`                                            |
|                               | `idoit.search`                        |                               | `search()`                                                |
|                               | `idoit.version`                       |                               | `readVersion()`                                           |
|                               | `idoit.login`                         | `API`                         | `login()`                                                 |
|                               | `idoit.logout`                        |                               | `logout()`                                                |
| `cmdb.object`                 | `cmdb.object.create`                  | `CMDBObject`                  | `create()`                                                |
|                               | `cmdb.object.read`                    |                               | `read()`                                                  |
|                               | `cmdb.object.update`                  |                               | `update()`                                                |
|                               | `cmdb.object.archive`                 |                               | `archive()`                                               |
|                               | `cmdb.object.delete`                  |                               | `delete()`                                                |
|                               | `cmdb.object.purge`                   |                               | `purge()`                                                 |
| `cmdb.objects`                | `cmdb.objects.read`                   | `CMDBObjects`                 | `read()`                                                  |
| `cmdb.category`               | `cmdb.category.create`                | `CMDBCategory`                | `create()`                                                |
|                               | `cmdb.category.read`                  |                               | `read()`, `readOneByID()`, `readFirst()`                  |
|                               | `cmdb.category.update`                |                               | `update()`                                                |
|                               | `cmdb.category.save`                  |                               | `save()`                                                  |
|                               | `cmdb.category.archive`               |                               | `archive()`                                               |
|                               | `cmdb.category.delete`                |                               | `delete()`                                                |
|                               | `cmdb.category.purge`                 |                               | `purge()`                                                 |
| `cmdb.category_info`          | `cmdb.category_info.read`             | `CMDBCategoryInfo`            | `read()`                                                  |
| `cmdb.dialog`                 | `cmdb.dialog.create`                  | `CMDBDialog`                  | `create()`                                                |
|                               | `cmdb.dialog.read`                    |                               | `read()`                                                  |
|                               | `cmdb.dialog.delete`                  |                               | `delete()`                                                |
| `cmdb.impact`                 | `cmdb.impact.read`                    | `CMDBImpact`                  | `readByID()`, `readByConst()`                             |
| `cmdb.location_tree`          | `cmdb.location_tree.read`             | `CMDBLocationTree`            | `read()`, `readRecursively()`                             |
| `cmdb.logbook`                | `cmdb.logbook.create`                 | `CMDBLogbook`                 | `create()`                                                |
|                               | `cmdb.logbook.read`                   |                               | `read()`                                                  |
| `cmdb.objects_by_relation`    | `cmdb.objects_by_relation.read`       | `CMDBObjectsByRelation`       | `readByID()`, `readByConst()`                             |
| `cmdb.object_type_categories` | `cmdb.object_type_categories.read`    | `CMDBObjectTypeCategories`    | `readByID()`, `readByConst()`                             |
| `cmdb.object_type_groups`     | `cmdb.object_type_groups.read`        | `CMDBObjectTypeGroups`        | `read()`                                                  |
| `cmdb.object_types`           | `cmdb.object_types.read`              | `CMDBObjectTypes`             | `read()`, `readOne()`, `readByTitle()`                    |
| `cmdb.reports`                | `cmdb.reports.read`                   | `CMDBReports`                 | `read()`, `listReports()`                                 |
| `cmdb.workstation_components` | `cmdb.workstation_components.read`    | `CMDBWorkstationComponents`   | `read()`, `readByEMail()`, `readByEMails()`               |
| `checkmk.statictag`           | `checkmk.statictag.create`            | `CheckMKStaticTag`            | `create()`                                                |
|                               | `checkmk.statictag.read`              |                               | `read()`, `readByID()`, `readByIDs()`, `readByTag()`      |
|                               | `checkmk.statictag.update`            |                               | `update()`                                                |
|                               | `checkmk.statictag.delete`            |                               | `delete()`                                                |
| `checkmk.tags`                | `checkmk.tags.read`                   | `CheckMKTags`                 | `read()`                                                  |
| `monitoring.livestatus`       | `monitoring.livestatus.create`        | `MonitoringLivestatus`        | `createTCPConnection`, `createUNIXSocketConnection`       |
|                               | `monitoring.livestatus.read`          |                               | `read()`, `readByID()`, `readByIDs()`, `readByTitle()`    |
|                               | `monitoring.livestatus.update`        |                               | `update()`                                                |
|                               | `monitoring.livestatus.delete`        |                               | `deleteByID()`, `deleteByTitle()`                         |

Additionally, this API client library is shipped with methods as workarounds for remote procedure calls you probably miss. The RPC `cmdb.objects.create` does not exist but you may use `CMDBObjects::create()`. It simulates the missing RPC and gives you an easier and faster way to manipulate your CMDB.

If it makes sense there are methods to perform batch requests for most RPCs. For example, `CMDBCategory::batchRead()` fetches multiple category entries at once.

### Examples

#### Search in i-doit's database

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\Idoit;

$api = new API([/* … */]);

$idoit = new Idoit($api);
$result = $idoit->search('Server XY');

var_dump($result);
~~~

Perform more than one search at once:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\Idoit;

$api = new API([/* … */]);

$idoit = new Idoit($api);
$result = $idoit->batchSearch([
    'Server XY',
    'Client A',
    'John Doe'
]);

var_dump($result);
~~~

#### Create a new object

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObject;

$api = new API([/* … */]);

$object = new CMDBObject($api);
$objectID = $object->create(
    'C__OBJTYPE__SERVER',
    'Server XY'
);

var_dump($objectID);
~~~

#### Read common information about an object

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObject;

$api = new API([/* … */]);

$object = new CMDBObject($api);
$objectInfo = $object->read(42);

var_dump($objectInfo);
~~~

#### Load all data of an object

This will fetch everything about an object: common data, assigned categories and category entries as well.

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObject;

$api = new API([/* … */]);

$object = new CMDBObject($api);
$objectInfo = $object->load(42);

var_dump($objectInfo);
~~~

The method `load()` triggers round about 4 API calls. So be aware if it is heavily used.

#### Update an existing object

Currently, you are able to update an object's title:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObject;

$api = new API([/* … */]);

$object = new CMDBObject($api);
$object->update(
    42,
    [
        'title' => 'A shiny new object title'
    ]
);
~~~

#### Create or update an object ("upsert")

You you like to get an identifier of an object but you are unsure whether or not it exists, try an upsert. This is an "update" and an "insert" at the same time. This means, if the object exists you will get its identifier directly. If not the object will be created and then you will get its identifier. Objects must match against type and title. Additional attributes will be stored.

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObject;

$api = new API([/* … */]);

$object = new CMDBObject($api);
$object->upsert(
    'C__OBJTYPE__SERVER',
    'My little server',
    [
        'purpose' => 'Private stuff'
    ]
);
~~~

#### Fetch an object identifier

Fetch an object identifier by object title and (optional) type:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObjects;

$api = new API([/* … */]);

$object = new CMDBObjects($api);
$objectID = $object->getID('My little server');
$objectID = $object->getID('My little server', 'C__OBJTYPE__SERVER');
~~~

An exception error will be thrown if there is either no object or more than one.

#### Change documentation status of an object

i-doit has the concept of archiving your IT documentation. Each object has an status (`normal`, `archived`, marked as `deleted`). And last but not least, an object may be purged from the database.

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObject;

$api = new API([/* … */]);

$object = new CMDBObject($api);
$objectID = 42;
// Archive:
$object->archive($objectID);
// Mark as deleted:
$object->delete($objectID);
// Purge from database:
$object->purge($objectID);
~~~

#### Create multiple objects

Create multiple objects at once:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObjects;

$api = new API([/* … */]);

$cmdbObjects = new CMDBObjects($api);

$objectIDs = $cmdbObjects->create(
    [
        ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Server No. One'],
        ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Server No. Two'],
        ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Server No. Three']
    ]
);

var_dump($objectIDs);
~~~

#### Read multiple objects

Reading multiple objects at once is provided by several methods. Let's see:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObjects;

$api = new API([/* … */]);

$cmdbObjects = new CMDBObjects($api);

// Fetch every object:
$objects = $cmdbObjects->read();
var_dump($objects);

// Fetch max. 10 servers and sort them descending by title:
$objects = $cmdbObjects->read(['type' => 'C__OBJTYPE__SERVER'], 10, 0, 'title', CMDBObjects::SORT_DESCENDING);
var_dump($objects);

// Get them by their identifiers:
$objects = $cmdbObjects->readByIDs([1, 2, 3]);
var_dump($objects);

// Get all servers:
$objects = $cmdbObjects->readByType('C__OBJTYPE__SERVER');
var_dump($objects);

// Get archived clients:
$objects = $cmdbObjects->readArchived('C__OBJTYPE__CLIENT');
var_dump($objects);

// Get clients marked as deleted:
$objects = $cmdbObjects->readDeleted('C__OBJTYPE__CLIENT');
var_dump($objects);
~~~

#### Update multiple objects

Update multiple objects at once:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObjects;

$api = new API([/* … */]);

$cmdbObjects = new CMDBObjects($api);

// Rename objects 1, 2, 3:
$cmdbObjects->update([
  ['id' => 1, 'title' => 'New name'],
  ['id' => 2, 'title' => 'Another name'],
  ['id' => 3, 'title' => 'Just a name'],
]);
~~~

#### Archive/delete/purge multiple objects

Archive objects, mark them as deleted or even purge them from database:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObjects;

$api = new API([/* … */]);

$cmdbObjects = new CMDBObjects($api);

$cmdbObjects
    ->archive([1, 2, 3])
    ->delete([1, 2, 3])
    ->purge([1, 2, 3]);
~~~

#### Create category entries with attributes

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBCategory;

$api = new API([/* … */]);

$category = new CMDBCategory($api);
$entryID = $this->category->save(
    42,
    'C__CATG__IP',
    [
        'net' => 123,
        'active' => 1,
        'primary' => 0,
        'net_type' => 1,
        'ipv4_assignment' => 2,
        'ipv4_address' =>  '10.20.10.100',
        'description' => 'API TEST'
    ]
);

var_dump($entryID);
~~~

Alternatively, use method `CMDBCategory::batchCreate()` for batch requests.

#### Read categories and attributes

Read one or more category entries for one specific object:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBCategory;

$api = new API([/* … */]);

$category = new CMDBCategory($api);
$result = $category->read(42, 'C__CATG__IP');

var_dump($result);
~~~

Read one specific categoy entry for one specific object:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBCategory;

$api = new API([/* … */]);

$category = new CMDBCategory($api);
$result = $category->readOneByID(42, 'C__CATG__IP', 23);

var_dump($result);
~~~

Read just one category entry (easier than `read()` when using single-valued categories):

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBCategory;

$api = new API([/* … */]);

$category = new CMDBCategory($api);
$result = $category->readFirst(42, 'C__CATG__IP');

var_dump($result);
~~~

Read data for multiple objects and categories at once:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBCategory;

$api = new API([/* … */]);

$category = new CMDBCategory($api);
$result = $category->batchRead(
    [23, 42],
    ['C__CATG__IP', 'C__CATG__MODEL']
);

var_dump($result);
~~~

#### Update categories and attributes

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBCategory;

$api = new API([/* … */]);

$category = new CMDBCategory($api);
$category->save(
    42,
    'C__CATG__GLOBAL',
    [
        'cmdb_status' => 10
    ]
);
~~~

Alternatively, use method `CMDBCategory::batchUpdate()` for batch requests.

#### Change documentation status of a category and its attributes

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBCategory;

$api = new API([/* … */]);

$category = new CMDBCategory($api);
// Archive:
$category->archive(42, 'C__CATG__CPU', 1);
// Mark as deleted:
$category->delete(42, 'C__CATG__CPU', 2);
// Purge from database:
$category->purge(42, 'C__CATG__CPU', 3);
~~~

#### Create values in drop-down menus

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBDialog;

$api = new API([/* … */]);

$dialog = new CMDBDialog($api);

$entryID = $dialog->create('C__CATG__MODEL', 'title', 'My model');
var_dump($entryID);

$entryIDs = $dialog->batchCreate([
    'C__CATG__MODEL' => [
        'manufacturer' => 'My manufacturer',
        'title' => 'My model'
    ],
    'C__CATG__GLOBAL' => [
        'category' => [
            'cat 1',
            'cat 2',
            'cat 3'
        ],
        'purpose' => 'API TEST'
    ]
]);
var_dump($entryIDs);
~~~

#### Fetch values from drop-down menus

Drop-down menus in i-doit are called "dialog" (read-only) or "dialog+" (editable).

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBDialog;

$api = new API([/* … */]);

$dialog = new CMDBDialog($api);

$models = $dialog->read('C__CATG__MODEL', 'title');
var_dump($models);

$modelsAndManufacturers = $dialog->batchRead([
    'C__CATG__MODEL' => [
        'manufacturer',
        'title'
    ]
]);
var_dump($modelsAndManufacturers);
~~~

#### Build a location tree

Read objects located directly under an object:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBLocationTree;

$api = new API([/* … */]);

$locationTree = new CMDBLocationTree($api);
$result = $locationTree->read(1);

var_dump($result);
~~~

Read recursively objects located under an object:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBLocationTree;

$api = new API([/* … */]);

$locationTree = new CMDBLocationTree($api);
$result = $locationTree->readRecursively(1);

var_dump($result);
~~~

#### Fetch relations between objects

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObjectsByRelation;

$api = new API([/* … */]);
$relation = new CMDBObjectsByRelation($api);

$result = $relation->read(
    10,
    'C__RELATION_TYPE__PERSON_ASSIGNED_GROUPS'
);

var_dump($result);
~~~

#### Fetch workplace components for a person

A Person may be assigned to a workplace with several components like a PC, a monitor and a telephone. These components can be fetched by the person. You either need the object ID or the email address. Even more than one workplaces are supported.

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBWorkstationComponents;

$api = new API([/* … */]);
$components = new CMDBWorkstationComponents($api);

$result = $components->read(111); // Person object with ID 111
var_dump($result);

$result = $components->batchRead([111, 222]); // Person objects with IDs 111 and 222
var_dump($result);

$result = $components->readByEMail('alice@example.org'); // Person object with email address
var_dump($result);

$result = $components->readByEMails(['alice@example.org', 'bob@example.org']); // Person objects with email addresses
var_dump($result);
~~~

#### Reports

List all reports:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBReports;

$api = new API([/* … */]);
$reports = new CMDBReports($api);

$result = $reports->listReports();

var_dump($result);
~~~

Fetch the result of a report:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBReports;

$api = new API([/* … */]);
$reports = new CMDBReports($api);

$result = $reports->read(1);

var_dump($result);
~~~

Fetch the result of one or more reports:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBReports;

$api = new API([/* … */]);
$reports = new CMDBReports($api);

$result = $reports->batchRead([1, 2]);

var_dump($result);
~~~

### Fetch next free IP address from subnet

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\Subnet;

$api = new API([/* … */]);

$subnet = new Subnet($api);
// Load subnet object by its identifier:
$nextIP = $subnet->load(123)->next();

echo 'Next IP address: ' . $nextIP . PHP_EOL;
~~~

### Upload files

This API client library is able to upload a file, create a new "File" object an assigned it to an existing object identified by its ID:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\File;

$api = new API([/* … */]);

$file = new File($api);

// Assign one file to object with identifier 100:
$file->add(100, '/path/to/file', 'my file');

// Assign many files to this object:
$file->batchAdd(
    100,
    [
        'file1.txt' => 'File 1',
        'file2.txt' => 'File 2',
        'file3.txt' => 'File 3'
    ]
);
~~~

### Upload images to a gallery

Each object may have an image gallery provided by assigned category "images". This is the way to upload image files and assign them to an existing object:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\Image;

$api = new API([/* … */]);

$image = new Image($api);

// Assign one image with a caption to object's gallery with identifier 100:
$image->add(100, '/path/to/flowers.jpg', 'nice picture of flowers');

// Assign many images to this object:
$file->batchAdd(
    100,
    [
        'file1.jpg' => 'JPEG file',
        'file2.png' => 'PNG file',
        'file3.bmp' => 'BMP file',
        'file3.gif' => 'Animated GIF file'
    ]
);
~~~

### Self-defined request

Sometimes it is better to define a request on your own instead of using pre-defined methods provided by this API client library. Here is the way to perform a self-defined request:

~~~ {.php}
use bheisig\idoitapi\API;

$api = new API([/* … */]);

$result = $api->request('idoit.version');

var_dump($result);
~~~

`request()` takes the method and optional parameters.

### Self-defined batch request

Similar to a simple requests you may perform a batch requests with many sub-requests as you need:

~~~ {.php}
use bheisig\idoitapi\API;

$api = new API([/* … */]);

$result = $api->batchRequest([
    [
        'method' => 'idoit.version'
    ],
    [
       'method' => 'cmdb.object.read',
       'params' => ['id' => 1]
    ]
]);

var_dump($result);
~~~

### Read information about your CMDB design

Fetch information about object types, object types per group, categories assigned to object types, and attributes available in categories:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObjectTypes;
use bheisig\idoitapi\CMDBObjectTypeGroups;
use bheisig\idoitapi\CMDBObjectTypeCategories;
use bheisig\idoitapi\CMDBCategoryInfo;

$api = new API([/* … */]);

// Object types:
$objectTypes = new CMDBObjectTypes($api);
$allObjectTypes = $objectTypes->read();
var_dump($allObjectTypes);
$server = $objectTypes->readOne('C__OBJTYPE__SERVER');
var_dump($server);
$someObjectTypes = $objectTypes->batchRead('C__OBJTYPE__SERVER', 'C__OBJTYPE__CLIENT');
var_dump($someObjectTypes);
$client = $objectTypes->readByTitle('LC__CMDB__OBJTYPE__CLIENT');
var_dump($client);

// Object types per group:
$objectTypesPerGroup = new CMDBObjectTypeGroups($api);
$objectTypes = $objectTypesPerGroup->read();
var_dump($objectTypes);

// Categories assigned to object types:
$assignedCategory = new CMDBObjectTypeCategories($api);
$serverCategories = $assignedCategory->readByConst('C__OBJTYPE__SERVER');
var_dump($serverCategories);
// Read by identifiers is also possible. And there are methods for batch requests.

// Attributes available in categories:
$categoryInfo = new CMDBCategoryInfo($api);
$modelCategory = $categoryInfo->read('C__CATG__MODEL');
var_dump($modelCategory);
$categories = $categoryInfo->batchRead([
    'C__CATG__MODEL',
    'C__CATG__FORMFACTOR',
    'C__CATS__PERSON_MASTER'
]);
var_dump($categories);
~~~

### Read information about i-doit itself

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\Idoit;

$api = new API([/* … */]);
$idoit = new Idoit($api);

$version = $idoit->readVersion();
$constants = $idoit->readConstants();
$addOns = $idoit->getAddOns();
$license = $idoit->getLicense();

var_dump($version, $constants, $addOns, $license);
~~~

### Re-connect to server

Sometimes you need a fresh connection. You may explicitly disconnect from the i-doit server and re-connect to it:

~~~ {.php}
use bheisig\idoitapi\API;

$api = new API([/* … */]);

// Do your stuff…
$api->disconnect();
$api->isConnected(); // Returns false
$api->connect();
$api->isConnected(); // Returns true
~~~

### Debug API calls

For debugging purposes it is great to fetch some details about your API calls. This script uses some useful methods:

~~~ {.php}
#!/usr/bin/env php
<?php

use bheisig\idoitapi\API;
use bheisig\idoitapi\Idoit;

$start = time();

require_once 'vendor/autoload.php';

$api = new API([/* … */]);

// @todo Insert your code here, for example:
$request = new Idoit($api);
$request->readVersion();

fwrite(STDERR, 'Last request:' . PHP_EOL);
fwrite(STDERR, '=============' . PHP_EOL);
fwrite(STDERR, $api->getLastRequestHeaders() . PHP_EOL);
fwrite(STDERR, json_encode($api->getLastRequestContent(), JSON_PRETTY_PRINT) . PHP_EOL);
fwrite(STDERR, PHP_EOL);
fwrite(STDERR, '--------------------------------------------------------------------------------' . PHP_EOL);
fwrite(STDERR, 'Last response:' . PHP_EOL);
fwrite(STDERR, '==============' . PHP_EOL);
fwrite(STDERR, $api->getLastResponseHeaders() . PHP_EOL);
fwrite(STDERR, json_encode($api->getLastResponse(), JSON_PRETTY_PRINT) . PHP_EOL);
fwrite(STDERR, PHP_EOL);
fwrite(STDERR, '--------------------------------------------------------------------------------' . PHP_EOL);
fwrite(STDERR, 'Last connection:' . PHP_EOL);
fwrite(STDERR, '================' . PHP_EOL);
$info = $api->getLastInfo();
unset($info['request_header']);
foreach ($info as $key => $value) {
    if (is_array($value)) {
        $value = '…';
    }
    fwrite(STDERR, $key . ': ' . $value . PHP_EOL);
}
fwrite(STDERR, '--------------------------------------------------------------------------------' . PHP_EOL);
fwrite(STDERR, 'Amount of requests: ' . $api->countRequests() . PHP_EOL);
$memoryUsage = memory_get_peak_usage(true);
fwrite(STDERR, sprintf('Memory usage: %s bytes', $memoryUsage) . PHP_EOL);
$duration = time() - $start;
fwrite(STDERR, sprintf('Duration: %s seconds', $duration) . PHP_EOL);
~~~

## Contribute

Please, report any issues to [our issue tracker](https://github.com/bheisig/i-doit-api-client-php/issues). Pull requests are very welcomed. If you like to get involved see file [`CONTRIBUTING.md`](CONTRIBUTING.md) for details.

## Projects using this API client library

-   [i-doit CLI Tool](https://github.com/bheisig/i-doit-cli) – "Access your CMDB on the command line interface"
-   [i-doit Check_MK 2 add-on](https://www.i-doit.com/en/i-doit/add-ons/check-mk-add-on-2/) – "Share information between i-doit and Check_MK"

Send pull requests to add yours.

## Copyright & License

Copyright (C) 2016-2020 [Benjamin Heisig](https://benjamin.heisig.name/)

Licensed under the [GNU Affero GPL version 3 or later (AGPLv3+)](https://gnu.org/licenses/agpl.html). This is free software: you are free to change and redistribute it. There is NO WARRANTY, to the extent permitted by law.
