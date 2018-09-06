# zend-coding-standard

This component provides the coding standard ruleset for Zend Framework components.

## Installation

1. Install the module via composer by running:

   ```bash
   $ composer require --dev zendframework/zend-coding-standard
   ```

2. Add composer scripts into your `composer.json`:

   ```json
   "scripts": {
     "cs-check": "phpcs",
     "cs-fix": "phpcbf"
   }
   ```

3. Create file `phpcs.xml` on base path of your repository with content:

   ```xml
   <?xml version="1.0"?>
   <ruleset name="Zend Framework Coding Standard">
       <rule ref="ZendCodingStandard"/>

       <!-- Paths to check -->
       <file>config</file>
       <file>src</file>
       <file>test</file>
   </ruleset>
   ```

You can add or exclude some locations in that file.
For a reference please see: https://github.com/squizlabs/PHP_CodeSniffer/wiki/Annotated-ruleset.xml

## Usage

* To run checks only:

  ```bash
  $ composer cs-check
  ```

* To automatically fix many CS issues:

  ```bash
  $ composer cs-fix
  ```

## Ignoring parts of a File

> Note: Before PHP_CodeSniffer version 3.2.0, `// @codingStandardsIgnoreStart` and `// @codingStandardsIgnoreEnd` were
> used. These will be removed in PHP_CodeSniffer version 4.0.

Disable parts of a file:
```php
$xmlPackage = new XMLPackage;
// phpcs:disable
$xmlPackage['error_code'] = get_default_error_code_value();
$xmlPackage->send();
// phpcs:enable
```

Disable a specific rule 
```php
// phpcs:disable Generic.Commenting.Todo.Found
$xmlPackage = new XMLPackage;
$xmlPackage['error_code'] = get_default_error_code_value();
// TODO: Add an error message here.
$xmlPackage->send();
// phpcs:enable
```
