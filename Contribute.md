#   Contribution

Thank you very much for your interest in this project! There are plenty of ways you can support us. :-)


##  Use it

The best and (probably) easiest way is to use the API client library for your own projects. It would be very nice to share your thoughts with us. We love to hear from you.

If you have questions how to use it properly read the [documentation](Readme.md) carefully.


##  Report bugs

If you find something strange please report it to [our issue tracker](https://github.com/bheisig/i-doit-api-client-php/issues).


##  Make a wish

Of course, there are some features in the pipeline. However, if you have good ideas how to improve the API client library please let us know! Write a feature request [in our issue tracker](https://github.com/bheisig/i-doit-cli/issues).


##  Setup a development environment

If you like to contribute source code, documentation snippets, self-explaining examples or other useful bits, fork this repository, setup the environment and make a pull request.

~~~ {.bash}
git clone https://github.com/bheisig/i-doit-api-client-php.git
~~~

If you have a GitHub account create a fork first and then clone the repository.

After that, setup the environment with Composer:

~~~ {.bash}
composer install
~~~

Now it is the time to do your stuff. Do not forget to commit your changes. When you are done consider to make a pull requests.

Notice, that any of your contributions merged into this repository will be [licensed under the AGPLv3](COPYING).


##  Requirements

This projects has some dependencies:

*   [PHP](https://php.net/), version 5.6+
*   [Composer](https://getcomposer.org/)
*   One or more working copies of [i-doit](https://i-doit.com/) (otherwise this API client library is senseless)

Developers must meet some more requirements:

*   [Git](https://git-scm.com/)
*   [Pandoc](https://pandoc.org/)
*   make


##  Run unit tests

Unit tests are located under `tests/`. Just call `make phpunit` to execute all of them.


##  Create a distribution tarball

*   Bump version in file `project.json`
*   Call `make`
*   Call `make dist`

The last call creates a file caleld `i-doit-api-client-php-<VERSION>.tar.gz`.


##  Donate

Last but not least, if you think this script is useful for your daily work, consider a donation. What about a beer?
