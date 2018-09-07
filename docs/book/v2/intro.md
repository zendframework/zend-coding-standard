# zend-coding-standard

This component provides the coding standard ruleset for Zend Framework components.

PSR-1, PSR-2 and PSR-12 are _minimal_ sets and don't address a lot of factors, including things like:

- whitespace around operators
- alignment of array keys and operators
- alignment of object operations
- how to format multi-line conditionals
- what and what not to import, and how
- etc.

Contributors have different coding styles and so do the maintainers. During code reviews there are regularly 
discussions about spaces and alignments, where and when was said that a function needs to be imported. And 
that's where this coding standard comes in: To have internal consistency in a component and between components.

> Note: PSR-12 is not finalized. e.g. The `!` operator and `:` placement for return values are still under  discussion. 
We will change these rules, and, when PSR-12 is finalized, adapt them.

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

3. Create file `phpcs.xml.dist` on base path of your repository with content:

   ```xml
   <?xml version="1.0"?>
   <ruleset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="vendor/squizlabs/php_codesniffer/phpcs.xsd">
   
       <arg name="basepath" value="."/>
       <arg name="cache" value=".phpcs-cache"/>
       <arg name="colors"/>
       <arg name="extensions" value="php"/>
       <arg name="parallel" value="80"/>
       
       <!-- Show progress -->
       <arg value="p"/>
   
       <!-- Paths to check -->
       <file>config</file>
       <file>src</file>
       <file>test</file>
   
       <!-- Include all rules from the Zend Coding Standard -->
       <rule ref="ZendCodingStandard"/>
   </ruleset>
   ```

You can add your own rules or exclude rules. For a reference please see: 
[PHP_CodeSniffer Annotated Ruleset](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Annotated-Ruleset)

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
> used. These are deprecated and will be removed in PHP_CodeSniffer version 4.0.

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
