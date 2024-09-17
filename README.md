# package-checker

Tool to check all the composer packages in a project for PHP compatibility.

The tool works by reading the `composer.lock` file in the root of your project, iterating over each of the installed packages, and checking to see if any of them have a `"require": { "php": "xxx" }` version that isn't satisfied by the target PHP version.

## Installation

```shell
composer require --dev cviebrock/package-checker
```

## Run

```shell
./vendor/bin/check-packages
```

The tool will check all installed packages against the version of PHP currently running. To test against a specific version of PHP, use the `--targetVersion` or `-t` option:

```shell
./vendor/bin/check-packages --targetVersion=8.2
./vendor/bin/check-packages -t 8.2
```

To check only those packages required by the root project, use the `-D` or `--direct` flag.

You can also change the amount of output with the `-v` and `-q` flags:

|   Flag   | Output                                                              |
|:--------:|:--------------------------------------------------------------------|
|   `-q`   | No output; only return exit code.                                   |
| no flags | Outputs a summary of the ok, failed, and unknown packages.          |
|   `-v`   | Also includes a detailed listing of each package checked.           |
|  `-vv`   | Also includes a detailed report on the failed and unknown packages. |

The command will return an exit code of `0` if all packages meet the target requirement.  Otherwise, it will exit with `1` if there are any packages that don't meet the target, or whose compatibility is unknown.

### Sample Output

```
> ./vendor/bin/check-packages -t 8.0 -vv

❯ ./vendor/bin/check-packages -t 8.2 -vv
 ---------------------------------------------- ---------- ------------------------
  Package                                        Version    PHP
 ---------------------------------------------- ---------- ------------------------
  ✔ aws/aws-crt-php                              v1.2.6     >=5.5
  ✔ aws/aws-sdk-php                              3.321.8    >=7.2.5
  ✔ clue/ndjson-react                            v1.3.0     >=5.3
  ✔ clue/stream-filter                           v1.7.0     >=5.3
  ✔ codescale/ffmpeg-php                         3.2.2      >=7
  ✔ composer/ca-bundle                           1.5.1      ^7.2 || ^8.0
  ✔ composer/class-map-generator                 1.3.4      ^7.2 || ^8.0
  ✔ composer/composer                            2.7.9      ^7.2.5 || ^8.0
  ✔ composer/metadata-minifier                   1.0.0      ^5.3.2 || ^7.0 || ^8.0
  ✔ composer/pcre                                3.3.1      ^7.4 || ^8.0
  ✔ composer/semver                              3.4.2      ^5.3.2 || ^7.0 || ^8.0
  ✔ composer/spdx-licenses                       1.5.8      ^5.3.2 || ^7.0 || ^8.0
  ✔ composer/xdebug-handler                      3.0.5      ^7.2.5 || ^8.0
  ✔ doctrine/lexer                               3.0.1      ^8.1
  ✔ dompdf/dompdf                                v2.0.8     ^7.1 || ^8.0
  ✔ egulias/email-validator                      4.0.2      >=8.1
  ✔ evenement/evenement                          v3.0.2     >=7.0
  ✔ fidry/cpu-core-counter                       1.2.0      ^7.2 || ^8.0
  ✔ friendsofphp/php-cs-fixer                    v3.64.0    ^7.4 || ^8.0
  ✔ guzzlehttp/guzzle                            7.9.2      ^7.2.5 || ^8.0
  ✔ guzzlehttp/promises                          2.0.3      ^7.2.5 || ^8.0
  ✔ guzzlehttp/psr7                              2.7.0      ^7.2.5 || ^8.0
  ✔ http-interop/http-factory-guzzle             1.2.0      >=7.3
  ✔ jean85/pretty-package-versions               2.0.6      ^7.1|^8.0
  ✔ justinrainbow/json-schema                    5.3.0      >=7.1
  ✔ league/climate                               3.8.2      ^7.3 || ^8.0
  ✔ masterminds/html5                            2.9.0      >=5.3.0
  ✔ mtdowling/jmespath.php                       2.8.0      ^7.2.5 || ^8.0
  ✔ pear/console_commandline                     v1.2.6     >=5.3.0
  ? pear/console_getopt                          v1.4.3
  ✔ pear/pear-core-minimal                       v1.10.15   >=5.4
  ✔ pear/pear_exception                          v1.0.2     >=5.2.0
  ✔ pear/text_password                           1.2.2      >=5.2.1
  ? phenx/php-font-lib                           0.5.6
  ✔ phenx/php-svg-lib                            0.5.4      ^7.1 || ^8.0
  ✔ php-http/client-common                       2.7.1      ^7.1 || ^8.0
  ✔ php-http/discovery                           1.19.4     ^7.1 || ^8.0
  ✔ php-http/httplug                             2.4.0      ^7.1 || ^8.0
  ✔ php-http/message                             1.16.1     ^7.2 || ^8.0
  ✔ php-http/message-factory                     1.1.0      >=5.4
  ✔ php-http/promise                             1.3.1      ^7.1 || ^8.0
  ✔ phpstan/phpstan                              1.12.3     ^7.2|^8.0
  ✔ psr/container                                2.0.2      >=7.4.0
  ✔ psr/event-dispatcher                         1.0.0      >=7.2.0
  ✔ psr/http-client                              1.0.3      ^7.0 || ^8.0
  ✔ psr/http-factory                             1.1.0      >=7.1
  ✔ psr/http-message                             2.0        ^7.2 || ^8.0
  ✔ psr/log                                      3.0.1      >=8.0.0
  ✔ ralouphie/getallheaders                      3.0.3      >=5.6
  ✔ react/cache                                  v1.2.0     >=5.3.0
  ✔ react/child-process                          v0.6.5     >=5.3.0
  ✔ react/dns                                    v1.13.0    >=5.3.0
  ✔ react/event-loop                             v1.5.0     >=5.3.0
  ✔ react/promise                                v3.2.0     >=7.1.0
  ✔ react/socket                                 v1.16.0    >=5.3.0
  ✔ react/stream                                 v1.4.0     >=5.3.8
  ✔ sabberworm/php-css-parser                    v8.6.0     >=5.6.20
  ✔ sebastian/diff                               5.1.1      >=8.1
  ✔ seld/cli-prompt                              1.0.4      >=5.3
  ✔ seld/jsonlint                                1.11.0     ^5.3 || ^7.0 || ^8.0
  ✔ seld/phar-utils                              1.2.1      >=5.3
  ✔ seld/signal-handler                          2.0.2      >=7.2.0
  ? sentry/sdk                                   3.6.0
  ✔ sentry/sentry                                3.22.1     ^7.2|^8.0
  ✔ silverorange/admin                           6.1.5      >=5.3.0
  ✔ silverorange/ambiguous-class-name-detector   1.0.1      >=7.1.0
  ✔ silverorange/concentrate                     2.0.2      >=7.1.0
  ✔ silverorange/mdb2                            3.1.1      >=5.3.0
  ✔ silverorange/mdb2_driver_pgsql               2.2.0      >=5.3.0
  ✔ silverorange/package-checker                 dev-main   ^8.0
  ✔ silverorange/site                            14.4.0     >=5.5.0
  ✔ silverorange/swat                            6.1.5      >=5.6.0
  ✔ silverorange/xml_rpc_ajax                    3.1.1      >=5.2.1
  ✔ silverorange/yui                             1.0.12     >=5.2.1
  ✔ squizlabs/php_codesniffer                    3.10.2     >=5.4.0
  ✔ symfony/console                              v6.4.11    >=8.1
  ✔ symfony/deprecation-contracts                v3.5.0     >=8.1
  ✔ symfony/event-dispatcher                     v6.4.8     >=8.1
  ✔ symfony/event-dispatcher-contracts           v3.5.0     >=8.1
  ✔ symfony/filesystem                           v6.4.9     >=8.1
  ✔ symfony/finder                               v6.4.11    >=8.1
  ✔ symfony/http-client                          v6.4.11    >=8.1
  ✔ symfony/http-client-contracts                v3.5.0     >=8.1
  ✔ symfony/mailer                               v5.4.41    >=7.2.5
  ✔ symfony/mime                                 v6.4.11    >=8.1
  ✔ symfony/options-resolver                     v6.4.8     >=8.1
  ✔ symfony/polyfill-ctype                       v1.31.0    >=7.2
  ✔ symfony/polyfill-intl-grapheme               v1.31.0    >=7.2
  ✔ symfony/polyfill-intl-idn                    v1.31.0    >=7.2
  ✔ symfony/polyfill-intl-normalizer             v1.31.0    >=7.2
  ✔ symfony/polyfill-mbstring                    v1.31.0    >=7.2
  ✔ symfony/polyfill-php73                       v1.31.0    >=7.2
  ✔ symfony/polyfill-php80                       v1.31.0    >=7.2
  ✔ symfony/polyfill-php81                       v1.31.0    >=7.2
  ✔ symfony/process                              v6.4.8     >=8.1
  ✔ symfony/service-contracts                    v3.5.0     >=8.1
  ✔ symfony/stopwatch                            v6.4.8     >=8.1
  ✔ symfony/string                               v6.4.11    >=8.1
  ✔ symfony/yaml                                 v5.4.43    >=7.2.5
 ---------------------------------------------- ---------- ------------------------

SUMMARY
-------

 -------------
       OK: 96
     FAIL: 0
  UNKNOWN: 3
 -------------

UNKNOWN
-------

These packages do not have a PHP requirement, so may or may not be valid:

 -------------------------------------------------------------------------------
  Package: pear/console_getopt
  Version: v1.4.3
      PHP: none
    Links: Homepage: none
           Source: https://github.com/pear/Console_Getopt
           Packagist: https://packagist.org/packages/pear/console_getopt#v1.4.3
 -------------------------------------------------------------------------------
  Package: phenx/php-font-lib
  Version: 0.5.6
      PHP: none
    Links: Homepage: https://github.com/PhenX/php-font-lib
           Source: https://github.com/dompdf/php-font-lib/tree/0.5.6
           Packagist: https://packagist.org/packages/phenx/php-font-lib#v0.5.6
 -------------------------------------------------------------------------------
  Package: sentry/sdk
  Version: 3.6.0
      PHP: none
    Links: Homepage: http://sentry.io
           Source: https://github.com/getsentry/sentry-php-sdk/tree/3.6.0
           Packagist: https://packagist.org/packages/sentry/sdk#v3.6.0
 -------------------------------------------------------------------------------
 ```
