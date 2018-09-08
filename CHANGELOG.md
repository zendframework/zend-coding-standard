# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.0.0 - TBD

### Added

- [#5](https://github.com/zendframework/zend-coding-standard/pull/5) adds 
  online documentation: https://docs.zendframework.com/zend-coding-standard/
- [#5](https://github.com/zendframework/zend-coding-standard/pull/5) adds 
  PSR-12 rules.
  
  *NOTE:* PSR-12 is not finalized. e.g. The `!` operator and `:` placement for
  return values are still under discussion. We will change these rules, and, 
  when PSR-12 is finalized, adapt them.
- [#5](https://github.com/zendframework/zend-coding-standard/pull/5) extends 
  PSR-12 with ZendFramework specific rules:
  
  *NOTE:* Most of these rules should look familiar as they are already being
  used in components rewritten for PHP 7.1. 
    
  - There should be one space on either side of an equals sign used to assign 
    a value to a variable. In case of a block of related assignments, more 
    space may be inserted before the equal sign to promote readability.
  - Short array syntax must be used to define arrays. 
  - All values in multiline arrays must be indented with 4 spaces. 
  - All array values must be followed by a comma, including the last value. 
  - Whitespace is not allowed around the opening bracket or before the 
    closing bracket when referencing an array.
  - All double arrow symbols must be aligned to one space after the longest 
    array key.
  - The `global` keyword may not be used.
  - The `PHP_SAPI` constant must be used instead of the `php_sapi_name()` 
    function.
  - PHP function calls must be in lowercase.
  - PHP functions which are an alias may not be used.
  - Deprecated functions should not be used.
  - Comments may be omitted and should not be used for typehinted arguments.
  - Comments may not start with `#`.
  - Comments may not be empty.
  - The words _private_, _protected_, _static_, _constructor_, _deconstructor_,
    _Created by_, _getter_ and _setter_, may not be used in comments.
  - The annotations `@api`, `@author`, `@category`, `@created`, `@package`,
    `@subpackage` and `@version` may not be used in comments. Git commits 
    provide accurate information.
  - The asterisks in a doc comment should align, and there should be one space 
    between the asterisk and tag.
  - If a function throws any exceptions, they should be documented in `@throws` 
    tags.
  - The `@var` tag may be used in inline comments to document the _Type_ of 
    properties.
  - Comments with single-line content should be written as one-liners.
  - Shorthand scalar typehint variants must be used in docblocks.
  - Each PHP file should have a page level docblock with `@see`, `@copyright` 
    and `@license`. The copyright date should only be adjusted if the file has 
    changed.
  - Each PHP file should have a strict type declaration at the top after the 
    page level docblock.
  - Import statements should be alphabetically sorted.
  - Import statements should not be grouped.
  - Each import statement should be on its own line.
  - Absolute class name references, functions and constants should be imported.
  - Unused import statements are not allowed.
  - Classes and function within the same namespace should not be imported.
  - Imports should not have an alias with the same name.
  - A class should not have unused private constants, (or write-only) 
    properties and methods.
  - Class name resolution via `::class` should be used instead of 
    `__CLASS__`, `get_class()`, `get_class($this)`, `get_called_class()` and 
    `get_parent_class()`.
  - Methods may not have the final declaration in classes declared as final.
  - The colon used with return type declarations MUST be surrounded with 1 
    space.
  - Nullable and optional arguments, which are marked as `= null`, must have 
    the `?` symbol present.
  - Control Structures must have at least one statement inside of the body.    
  - Catch blocks may be empty.
  - Catch blocks must be reachable.
  - Catch blocks must use `Throwable` instead of `Exception`.    
  - The not (`!`) operator must be followed by exactly one space.
  - The `&&` and `||` operators must be used instead of `AND` and `OR`.
  - The null  coalescing operator `??` should be used when possible.
  - Assignment operators (eg `+=`, `.=`) should be used when possible.
  - Unused variables should not be passed to closures via `use`.    
  - The code may not contain unreachable code.
  - The backtick operator may not be used for execution of shell commands.
  - Class and Interface names should be unique in a project. They should 
    never be duplicated.
  - Methods that only call the parent method should not be defined.
  - Files that contain PHP code should only have PHP code and should not have
    any _"inline HTML"_.
  - There must be exactly one space after a type cast. 
  - Constructors should be named `__construct`, not after the class.
  - The opening PHP tag should be the first item in the file. 
  - Strings should not be concatenated together unless used in multiline for 
    readability. 
  - Loose `==` and `!=` comparison operators should not be used. Use `===` 
    and `!==` instead.
  - An early exit strategy should be used where possible to reduce the level 
    of control structures.
  - Language constructs must be used without parentheses where possible.
  - Short list syntax `[...]` should be used instead of `list(...)`.
  - Short form of type keywords must be used. i.e. `bool` instead of 
    `boolean`, `int` instead of `integer`, etc. The `binary` and `unset` cast 
    operators are not allowed.
  - Parentheses should not be used if they can be omitted.
  - Semicolons `;` should not be used if they can be omitted.
  - Variables should be returned directly instead of assigned to a variable 
    which is not used.
  - The `self` keyword should be used instead of the current class name, and 
    should not have spaces around `::`.
  - Global functions should not be used.
  - Static methods should not use `$this`. 
  - Double quote strings may only be used if they contain variables. 
  - Strings should not be enclosed in parentheses when being echoed.
  - Type casts should not have whitespace inside the parentheses.
  - The opening brace for functions should be on a new line with no blank 
    lines surrounding it.
  - The PHP constructs `echo`, `print`, `return`, `include`, `include_once`, 
    `require`, `require_once`, and `new`, should have one space after them.
  - The object operator `->` should not have any spaces around it.
  - Semicolons should not have spaces before them.
  - The code should not contain superfluous whitespaces. e.g. multiple empty
    lines, trailing spaces, etc.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.0 - 2016-11-09

Initial public release. Incorporates rules for:

- PSR-2
- disallow long array syntax
- space after not operator
- whitespace around operators
- disallow superfluous whitespace

and enables color reporting by default.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
