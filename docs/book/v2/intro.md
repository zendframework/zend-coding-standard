# zend-coding-standard

The coding standard ruleset for Zend Framework components.

This specification extends and expands [PSR-12](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md), 
the extended coding style guide and requires adherence to [PSR-1](https://www.php-fig.org/psr/psr-1), 
the basic coding standard. These are minimal specifications and don't address all factors, including things like:

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

3. Create file `phpcs.xml` on base path of your repository with this content:

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
> used. These are deprecated and will be removed in PHP_CodeSniffer version 4.0.

Disable parts of a file:
```php
$xmlPackage = new XMLPackage;
// phpcs:disable
$xmlPackage['error_code'] = get_default_error_code_value();
$xmlPackage->send();
// phpcs:enable
```

Disable a specific rule:
```php
// phpcs:disable Generic.Commenting.Todo.Found
$xmlPackage = new XMLPackage;
$xmlPackage['error_code'] = get_default_error_code_value();
// TODO: Add an error message here.
$xmlPackage->send();
// phpcs:enable
```

## Development

> **New rules or Sniffs may not be introduced in minor or bugfix releases and should always be based on the develop 
branch and queued for the next major release, unless considered a bugfix for existing rules.**

If you want to test changes against ZendFramework components or your own projects, install your forked 
zend-coding-standard globally with composer: 
```bash
$ composer global config repositories.zend-coding-standard vcs git@github.com:<FORK_NAMESPACE>/zend-coding-standard.git
$ composer global require --dev zendframework/zend-coding-standard:dev-<FORKED_BRANCH>

# For this to work, add this to your path: ~/.composer/vendor/bin
# Using `-s` prints the rules that triggered the errors so they can be reviewed easily. `-p` is for progress display.
$ phpcs -sp --standard=ZendCodingStandard src test
```
Make sure you remove the global installation after testing from your global composer.json file!!!

Documentation can be previewed locally by installing [MkDocs](https://www.mkdocs.org/#installation) and run 
`mkdocs serve`. This will start a server where you can read the docs.

## Reference

Rules can be added, excluded or tweaked locally, depending on your preferences. More information on how to do this can
be found here:

- [Coding Standard Tutorial](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Coding-Standard-Tutorial)
- [Configuration Options](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Configuration-Options)
- [Selectively Applying Rules](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Annotated-Ruleset#selectively-applying-rules)
- [Customisable Sniff Properties](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Customisable-Sniff-Properties)
- [Slevomat Coding Standard](https://github.com/slevomat/coding-standard)
