# Testing

The package is tested with smoke tests (in `testing/`), rather than PHPUnit, so there's no test framework to install
or keep working on every version of PHP. A test fails if it throws, fails an `assert()`, or raises any warning, notice
or deprecation.

## Running them on every supported version of PHP
This lints every file and runs the tests on PHP 8.2, 8.3, 8.4, 8.5 and 8.6, each in its own Docker container, then runs
PHPStan once, on the lowest version. It's what the GitHub workflow runs, so a pass here means a pass there.

```bash
composer install
testing/run-all-php-versions.sh
```

- To test only some versions, pass them, e.g. `testing/run-all-php-versions.sh 8.2 8.5`.
- To rebuild the Docker images first, e.g. to pick up the latest patch releases, pass `--rebuild`.

## Running them on your own PHP
```bash
php -d zend.assertions=1 testing/main.php
```

`zend.assertions` must be 1, or the `assert()` checks would silently do nothing, so the tests refuse to run without it.
To run only the tests whose name contains some text, set `ABSTRACT_VIEW_TEST_ONLY`.

## PHPStan
```bash
vendor/bin/phpstan
```

It runs at the highest level, 10, set in `phpstan.neon.dist`.

## The documentation's examples
`testing/tests/DocsTest.php` runs the PHP examples in the README and every page in `docs/`, so the documentation can't
drift from what the code does. See `testing/DocsExampleRunner.php`.
