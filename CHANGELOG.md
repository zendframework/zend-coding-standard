# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.0.0-alpha.4 - TBD

### Added

- Nothing.

### Changed

- [#22](https://github.com/zendframework/zend-coding-standard/pull/22) refactors 
  documentation. It now follows the PHP-FIG PSR-12 style.
- [#22](https://github.com/zendframework/zend-coding-standard/pull/22) refactors
  the ruleset. It uses PSR-12 as the base ruleset with these additional rules:

  - The short open tag SHOULD NOT be used.
  - Deprecated features SHOULD be avoided.
  - The backtick operator MUST NOT be used.
  - The `goto` language construct SHOULD NOT be used.
  - The `global` keyword MUST NOT be used.
  - The constant `PHP_SAPI` SHOULD be used instead of the `php_sapi_name()` 
    function.
  - Aliases SHOULD NOT be used.
  - There MUST NOT be a space before a semicolon. Redundant semicolons SHOULD
    be avoided.
  - Non executable code MUST be removed.
  - There MUST be a single space after language constructs.
  - Parentheses MUST be omitted where possible.
  - PHP function calls MUST be in lowercase.
  - There MAY NOT be any content before the opening tag. Inline HTML in PHP code
    SHOULD be avoided. All code MUST be executable and non executable code SHOULD
    be removed.
  - The `declare(strict_types=1)` directive MUST be declared and be the first 
    statement in a file.
  - There MAY be maximum one blank line to improve readability and to indicate 
    related blocks of code except where explicitly forbidden.
  - There MAY NOT be any blank line after opening braces and before closing braces.
  - There MUST NOT be a space before a semicolon. Redundant semicolons SHOULD be 
    avoided.
  - Encapsed strings MAY be used instead of concatenating strings. When 
    concatenating strings, there MUST be a single whitespace before and after the
    concatenation operator. The concatenation operator MUST NOT be the at the end 
    of a line. If multi-line concatenation is used there MUST be an indent of 4 
    spaces.
  - Variable names MUST be declared in camelCase.
  - The short array syntax MUST be used to define arrays.
  - All values in multiline arrays must be indented with 4 spaces.
  - All array values must be followed by a comma, including the last value.
  - There MUST NOT be whitespace around the opening bracket or before the closing 
    bracket when referencing an array.
  - All double arrow symbols MUST be aligned to one space after the longest array
    key.
  - The short list syntax `[...]` SHOULD be used instead of `list(...)`.
  - There MUST be a single space after the namespace keyword and there MAY NOT be
    a space around a namespace separator.
  - Import statements MUST be alphabetically sorted.
  - Unused import statements SHOULD be removed.
  - Fancy group import statements MUST NOT be used.
  - Each import statement MUST be on its own line.
  - Import statement aliases for classes, traits, functions and constants MUST
    be useful, meaning that aliases SHOULD only be used if a class with the same
    name is imported.
  - Classes, traits, interfaces, constants and functions MUST be imported.
  - There MUST NOT be duplicate class names.
  - The file name MUST match the case of the terminating class name.
  - PHP 4 style constructors MUST NOT be used.
  - The correct class names MUST be used.
  - Abstract classes MUST have a `Abstract` prefix.
  - Exception classes MUST have a `Exception` suffix.
  - Interface classes MUST have a `Interface` suffix.
  - Trait classes MUST have a `Trait` suffix.
  - For self-reference a class lower-case `self::` MUST be used without spaces 
    around the scope resolution operator.
  - Class name resolution via `::class` MUST be used instead of `__CLASS__`, 
    `get_class()`, `get_class($this)`, `get_called_class()`, `get_parent_class()` 
    and string reference.
  - There MAY NOT be any whitespace around the double colon operator.
  - Unused private methods, constants and properties MUST be removed.
  - Traits MUST be sorted alphabetically.
  - Default null values MUST be omitted for class properties.
  - There MUST be a single empty line between methods in a class.
  - The pseudo-variable `$this` MUST NOT be called inside a static method or 
    function.
  - Returned variables SHOULD be useful and SHOULD NOT be assigned to a value and 
    returned on the next line.
  - The question mark MUST be used when the default argument value is null.
  - The `final` keyword on methods MUST be omitted in final declared classes.
  - There MUST be one single space after `break` and `continue` structures with 
    a numeric argument argument.
  - Statements MUST NOT be empty, except for catch statements.
  - The `continue` control structure MUST NOT be used in switch statements, 
    `break` SHOULD be used instead.
  - All catch blocks MUST be reachable.
  - There MUST be at least one space on either side of an equals sign used
    to assign a value to a variable. In case of a block of related
    assignments, more spaces MUST be inserted before the equal sign to
    promote readability.
  - There MUST NOT be any white space around the object operator UNLESS
    multilines are used.
  - Loose comparison operators SHOULD NOT be used, use strict comparison
    operators instead. e.g. use `===` instead of `==`.
  - The null coalesce operator SHOULD be used when possible. 
  - Assignment operators SHOULD be used when possible.
  - The `&&` and `||` operators SHOULD be used instead of `and` and `or`.
  - There MUST be one whitespace after a type casting operator.
  - There MUST be one whitespace after unary not.
  - Inherited variables passed via `use` MUST be used in closures.
  - Code SHOULD be written so it explains itself. DocBlocks and comments 
    SHOULD only be used if necessary. They MUST NOT start with `#` and MUST 
    NOT be empty. They SHOULD NOT be used for already typehinted arguments, 
    except arrays.
  - The asterisks in a DocBlock should align, and there should be one
    space between the asterisk and tag.
  - PHPDoc tags `@param`, `@return` and `@throws` SHOULD not be aligned or
    contain multiple spaces between the tag, type and description.
  - If a function throws any exceptions, it SHOULD be documented with
    `@throws` tags.
  - DocBlocks MUST follow a specific order of annotations with empty
    newline between specific groups.
  - The annotations `@api`, `@author`, `@category`, `@created`, `@package`,
    `@subpackage` and `@version` MUST NOT be used in comments. Git commits
    provide accurate information.
  - The words _private_, _protected_, _static_, _constructor_, _deconstructor_,
    _Created by_, _getter_ and _setter_, MUST NOT be used in comments.
  - The `@var` tag MAY be used in inline comments to document the _Type_
    of properties. Single-line property comments with a `@var` tag SHOULD
    be written as one-liners. The `@var` MAY NOT be used for constants.
  - The correct tag case of PHPDocs and PHPUnit tags MUST be used.
  - Inline DocComments MAY be used at the end of the line, with at least a
    single space preceding. Inline DocComments MUST NOT be placed after curly 
    brackets.
  - Heredoc and nowdoc tags MUST be uppercase without spaces.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.0.0-alpha.3 - 2019-01-01

### Added

- [#8](https://github.com/zendframework/zend-coding-standard/pull/8) adds some sniffs from webimpress/coding-standard.

  - Forbid null values for class properties
  - Comments at the end of the line, with at least single space
  - Requires one space after namespace keyword
  - One space after break/continue with argument, remove redundant 1
  - Forbid continue in switch; use break instead
  - Require camelCase variable names
  - Detects for-loops that can be simplified to a while-loop
  - Detects unconditional if- and elseif-statements
  - Forbid goto instruction
  - Forbid multiple traits by declaration
  - Require lowercase function and const keywords in imports with one space after
  - Forbid superfluous leading backslash in use statements
  - Forbid whitespace around double colon operator
  - Forbid whitespace around double colon operator

- [#10](https://github.com/zendframework/zend-coding-standard/pull/10) adds additional sniffs.

  - Added AnonymousClassDeclaration sniff
  - Added ScopeIndent sniff
  - Added sniff to disallow spaces before/after incrementation/decrementation operators
  - Added sniff to disallow space after nullable operator

### Changed

- [#8](https://github.com/zendframework/zend-coding-standard/pull/8) replaces sniffs in favor of webimpress/coding-standard as these are more reliable or fixes more cases.

- [#10](https://github.com/zendframework/zend-coding-standard/pull/10) adds additional sniffs.

  - Improve absolute class name references sniffs
  - Improve sniff for useless alias for classes, constants, functions and traits
  - Use PSR-12 sniff for class instantiation parenthesis
  - Use PEAR.Commenting.FunctionComment instead of Squiz 
  - Updated to PHP_CodeSniffer 3.4.0 

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.0.0-alpha.2 - 2018-11-26

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#7](https://github.com/zendframework/zend-coding-standard/pull/7) updates to composer-installer 0.5 which fixes a 'Class not found' error during installation.

## 2.0.0-alpha.1 - 2018-11-18

### Added

- [#5](https://github.com/zendframework/zend-coding-standard/pull/5) adds
  online documentation: https://docs.zendframework.com/zend-coding-standard/
- [#5](https://github.com/zendframework/zend-coding-standard/pull/5) adds
  PSR-12 rules.

  *NOTE:* PSR-12 is not finalized. e.g. The `!` operator and `:` placement for
  return values are still under discussion. We will change these rules, and,
  when PSR-12 is finalized, adapt them.
- [#5](https://github.com/zendframework/zend-coding-standard/pull/5) extends
  PSR-12 with Zend Framework specific rules:

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
  - Comment tags `@param`, `@throws` and `@return` should not be aligned or
    contain multiple spaces between the tag, type and description.
  - If a function throws any exceptions, they should be documented in `@throws`
    tags.
  - The `@var` tag may be used in inline comments to document the _Type_ of
    properties.
  - Single-line comments with a `@var` tag should be written as one-liners.
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
  - Class and Interface names should be unique in a project and must have a
    unique fully qualified name.
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
