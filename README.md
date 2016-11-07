#   i-doit API client

Simple PHP client for i-doit's JSON-RPC API


##  About

[i-doit](https://i-doit.com) is a software application for IT documentation and a CMDB (Configuration Management Database). This application is very useful to collect all your knowledge about the IT infrastructure you are dealing with. i-doit is a Web application and [has an exhausting API](https://kb.i-doit.com/pages/viewpage.action?pageId=37355644) which is very useful to automate your infrastructure.

This client provides an simple, but powerful abstraction layer to send requests to i-doit's API. It is written in PHP so you may use it in your own software application.


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
*   Unit tests!!11


##  Download

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


##  Configuration

The API client class requires a configuration:

~~~ {.php}
$apiClient = new net\benjaminheisig\idoitapi\API([
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
use net\benjaminheisig\idoitapi\API;
use net\benjaminheisig\idoitapi\Idoit;

$apiClient = new API([
    'apiURL' => 'https://demo.i-doit.com/src/jsonrpc.php',
    'key' => 'c1ia5q',
    'username' => 'admin',
    'password' => 'admin'
]);

$request = new Idoit($apiClient);
$info = $request->getVersion();

var_dump($info);
~~~

It's simple like that. For more examples take a look at the next sub sections.


### Login and Logout

One sweet thing about i-doit's API you can (and should) use one user session for your stuff. This saves resources on the server side and allows you to perform a lot more calls in a short time.

The session handling is done by the API client. You just need to login. And if you are nice you logout after your work are done.

~~~ {.php}
use net\benjaminheisig\idoitapi\API;
use net\benjaminheisig\idoitapi\Idoit;

$apiClient = new API([
    'apiURL' => 'https://demo.i-doit.com/src/jsonrpc.php',
    'key' => 'c1ia5q',
    'username' => 'admin',
    'password' => 'admin'
]);

$apiClient->login();
// Do your stuff…
$apiClient->logout();
~~~


##  Contribute

Please, report any issues to [our issue tracker](https://github.com/bheisig/i-doit-api-client-php/issues). Pull requests are very welcomed.


##  Copyright & License

Copyright (C) 2016 [Benjamin Heisig](https://benjamin.heisig.name/)

Licensed under the [GNU Affero GPL version 3 or later (AGPLv3+)](https://gnu.org/licenses/agpl.html). This is free software: you are free to change and redistribute it. There is NO WARRANTY, to the extent permitted by law.
