#   i-doit API client

Simple PHP client for i-doit's JSON-RPC API


##  About

[i-doit](https://i-doit.com) is a software application for IT documentation and a CMDB (Configuration Management Database). This application is very useful to collect all your knowledge about the IT infrastructure you are dealing with. i-doit is a Web application and [has an exhausting API](https://kb.i-doit.com/pages/viewpage.action?pageId=37355644) which is very useful to automate your infrastructure.

This client provides a simple, but powerful abstraction layer to send requests to i-doit's API. It is written in PHP so you may use it in your own project.


##  Features

Why should you use this client? There are some good reasons:

*   Easy to use
*   There is a PHP function for each API method
*   Supports batch requests
*   Sends user-defined requests
*   Supports login and logout to save sessions
*   Follows redirects (HTTP 301/302)
*   Uses optional HTTP or SOCKS5 proxy
*   Verifies TLS certificate chains
*   Throws exception on error
*   Many examples
*   Well-documented
*   Easy to install via Composer
*   Unit tests!!!11


##  Download

You have several options to download (and kinda install) the API client:

*   Install any version via [Composer](https://getcomposer.org/)
*   Download any stable release manually
*   Clone the Git repository to fetch the (unstable) development branch

### Using Composer

####    Locally

Add a new dependency on `bheisig/idoitapi` to your project's `composer.json` file. Here is a minimal example to install the current development branch locally:

~~~ {.json}
{
    "require": {
        "bheisig/idoitapi": "@DEV"
    }
}
~~~

After that you need to call composer to install the API client (under `vendor/bheisig/idoitapi` by default):

~~~ {.bash}
composer install
~~~

As an alternative to the steps mentioned above just run:

~~~ {.bash}
composer require "bheisig/idoitapi=@DEV"
~~~

####    System-wide

For a system-wide installation you may use:

~~~ {.bash}
composer global require "bheisig/idoitapi=@DEV"
~~~

Make sure you have `~/.composer/vendor/bin/` in your path.


####    Updates

Composer has the great advantage (besides many other) that you can simply update the API client by running this command:

~~~ {.bash}
composer update
~~~

### Download Release

You will find [all releases on this site](https://github.com/bheisig/i-doit-api-client-php/releases).

To fetch the latest stable release:

~~~ {.bash}
wget FIXME // No releases at the moment ;-)
tar xvzf FIXME
cd i-doit-api-client-php/
~~~

### Using Git

Fetch the current (unstable) development branch:

~~~ {.bash}
git clone https://github.com/bheisig/i-doit-api-client-php.git
cd i-doit-api-client-php/
~~~


##  Usage

To use the API client just include it into your PHP script:

~~~ {.php}
require_once 'idoitapi.php';
~~~

That's it. All other files will be auto-loaded if needed.

If you use Composer you should use its own autoloader:

~~~ {.php}
require_once 'vendor/autoload.php';
~~~


##  Configuration

The API client class requires a configuration:

~~~ {.php}
use bheisig\idoitapi\API;

$api = new API([
    'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
    'port' => 443,
    'key' => 'c1ia5q',
    'username' => 'admin',
    'password' => 'admin',
    'language' => 'en',
    'proxy' => [
        'active' => false,
        'type' => 'HTTP', // 'HTTP' or 'SOCKS5'
        'host' => 'proxy.example.net',
        'port' => 8080,
        'username' => '',
        'password' => ''
    ]
]);
~~~

*   `url`: URL to i-doit's API, probably the base URL appended by `src/jsonrpc.php`
*   `port`: optional port on which the Web server listens; if not set port 80 will be used for HTTP and 443 for HTTPS
*   `key`: API key
*   `username` and `password`: optional credentials if needed, otherwise `System API` user will be used
*   `language`: requests to and responses from i-doit will be translated to this language (`de` and `en` supported); this is optional; defaults to user's prefered language
*   `proxy`: use a proxy between client and server
    *   `active`: if `true` proxy settings will be used
    *   `type`: use a HTTP or a SOCKS5 proxy
    *   `host`: FQDN or IP address to proxy
    *   `port`: port on which the proxy server listens
    *   `username` and `password`: optional credentials used to authenticate against the proxy


##  Examples

A basic example:

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\Idoit;

$api = new API([
    'apiURL' => 'https://demo.i-doit.com/src/jsonrpc.php',
    'key' => 'c1ia5q',
    'username' => 'admin',
    'password' => 'admin'
]);

$request = new Idoit($api);
$info = $request->readVersion();

var_dump($info);
~~~

It's simple like that. For more examples take a look at the next sub sections.


### Login and Logout

One sweet thing about i-doit's API you can (and should) use one user session for your stuff. This saves resources on the server side and allows you to perform a lot more calls in a short time.

The session handling is done by the API client. You just need to login. And if you are nice you want to logout after your work is done.

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


### Pre-defined Methods

For almost every case there is a remote procedure you may call to read from or manipulate i-doit's database through its API. Each remote procedure is assigned to a namespace to keep the API clean and smoothly. Furtunately, you do not need to call these remote procedures on your own. The API client provides for each namespace a class and for each remote procedure a method. Here is a quick overview:

| Namespace         | Remote Procedure      | API Client Class          | Method                                |
| ----------------- | --------------------- | ------------------------- | ------------------------------------- |
| `idoit`           | `idoit.version`       | `Idoit`                   | `readVersion()`                       |
|                   | `idoit.search`        |                           | `search()`                            |
|                   | `idoit.constants`     |                           | `readConstants()`                     |
|                   | `idoit.login`         | `API`                     | `login()`                             |
|                   | `idoit.logout`        |                           | `logout()`                            |
| `cmdb.object`     | `cmdb.object.create`  | `CMDBObject`              | `create()`                            |
|                   | `cmdb.object.read`    |                           | `read()`                              |
|                   | `cmdb.object.update`  |                           | `udpate()`                            |
|                   | `cmdb.object.delete`  |                           | `archive()`, `delete()`, `purge()`    |
| `cmdb.objects`    | `cmdb.objects.read`   | `CMDBObjects`             | `read()`                              |


####    Search in i-doit's database

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


####    Create a New Object

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


####    Read Common Information About an Object

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObject;

$api = new API([/* … */]);

$object = new CMDBObject($api);
$objectInfo = $object->read(42);

var_dump($objectInfo);
~~~


####    Update an Existing Object

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


####    Change Documentation Status of an Object

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


####    Build a Location Tree

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


####    Fetch Relations Between Objects

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


####    Reports

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


### Self-defined Request

Sometimes it is better to define a request on your own instead of using pre-defined methods provided by this client. Here is the way to perform a self-defined request:

~~~ {.php}
use bheisig\idoitapi\API;

$api = new API([/* … */]);

$result = $api->request('idoit.version');

var_dump($result);
~~~

`request()` takes the method and optional parameters.


### Self-defined Batch Request

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


### Read Information About i-doit Itself

~~~ {.php}
use bheisig\idoitapi\API;
use bheisig\idoitapi\Idoit;

$api = new API([/* … */]);

$idoit = new Idoit($api);
$version = $idoit->readVersion();
$constants = $idoit->readConstants();

var_dump($constants);
~~~


### Re-connect to Server

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


### Debugging API Calls

For debugging purposes it is great to fetch some details about your API calls. These methods may help you:

~~~ {.php}
use bheisig\idoitapi\API;

$api = new API([/* … */]);

// Just a simple API call:
$request = new Idoit($api);
$request->readVersion();

// Debugging methods:
var_dump($api->countRequests());
var_dump($api->getLastInfo());
var_dump($api->getLastRequestContent());
var_dump($api->getLastRequestHeaders());
var_dump($api->getLastResponseHeaders());
~~~


##  Contribute

Please, report any issues to [our issue tracker](https://github.com/bheisig/i-doit-api-client-php/issues). Pull requests are very welcomed.


##  Copyright & License

Copyright (C) 2016 [Benjamin Heisig](https://benjamin.heisig.name/)

Licensed under the [GNU Affero GPL version 3 or later (AGPLv3+)](https://gnu.org/licenses/agpl.html). This is free software: you are free to change and redistribute it. There is NO WARRANTY, to the extent permitted by law.
