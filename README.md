# package-checker

Tool to check all the composer packages in a project for PHP compatibility.

The tool works by reading the `composer.lock` file in the root of your project, iterating over each of the installed packages, and checking to see if any of them have a `"require": { "php": "xxx" }` version that isn't satisfied by the target PHP version.

## Installation

```shell
composer require --dev silverorange/package-checker
```

## Run

```shell
./vendor/bin/check-packages
```

The tool will check installed packages against the version of PHP currently running. To test against a specific version of PHP, use the `--targetVersion` or `-t` option:

```shell
./vendor/bin/check-packages --targetVersion=8.2
./vendor/bin/check-packages -t 8.2
```

The command will return an exit code of `0` if all packages meet the target requirement.  Otherwise, it will exit with `1` if there are any packages that don't meet the target, or whose compatibility is unknown.

### Sample Output

```
> ./vendor/bin/check-packages -t 8.0

 99/99 [============================]

OK        : 79
Failures  : 17
Unknown   : 3

FAILURES: These packages have PHP requirements that do not meet the target of 8.0:

doctrine/lexer:3.0.1 (requires PHP ^8.1)
   - Homepage:  https://www.doctrine-project.org/projects/lexer.html
   - Source:    https://github.com/doctrine/lexer/tree/3.0.1
   - Packagist: https://packagist.org/packages/doctrine/lexer#v3.0.1
egulias/email-validator:4.0.2 (requires PHP >=8.1)
   - Homepage:  https://github.com/egulias/EmailValidator
   - Source:    https://github.com/egulias/EmailValidator/tree/4.0.2
   - Packagist: https://packagist.org/packages/egulias/email-validator#v4.0.2

UNKNOWN: These packages have unknown PHP requirements; check their source code:

pear/console_getopt:v1.4.3 
   - Homepage:  not given
   - Source:    https://github.com/pear/Console_Getopt
   - Packagist: https://packagist.org/packages/pear/console_getopt#v1.4.3
phenx/php-font-lib:0.5.6 
   - Homepage:  https://github.com/PhenX/php-font-lib
   - Source:    https://github.com/dompdf/php-font-lib/tree/0.5.6
   - Packagist: https://packagist.org/packages/phenx/php-font-lib#v0.5.6
sentry/sdk:3.6.0 
   - Homepage:  http://sentry.io
   - Source:    https://github.com/getsentry/sentry-php-sdk/tree/3.6.0
   - Packagist: https://packagist.org/packages/sentry/sdk#v3.6.0               
```
