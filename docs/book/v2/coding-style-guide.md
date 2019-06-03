# Zend Framework Coding Style Guide

This specification extends and expands [PSR-12](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md),
the extended coding style guide and requires adherence to [PSR-1](https://www.php-fig.org/psr/psr-1),
the basic coding standard.

> Note: PSR-12 is not finalized. e.g. The `!` operator and `:` placement for return values are still under discussion.
We will change these rules, and, when PSR-12 is finalized, adapt them.

## General

### Basic Coding Standard

Code MUST follow all rules outlined in [PSR-1](https://www.php-fig.org/psr/psr-1) and
[PSR-12](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md), except in
specific cases outlined in this specification, until PSR-12 is accepted as a standard.

### Indenting and Alignment

- There should be one space on either side of an equals sign used to assign a value to a variable. In case of a
  block of related assignments, more space may be inserted before the equal sign to promote readability.
  [*](ruleset.md#genericformattingmultiplestatementalignment)

```php
<?php

$shortVar        = (1 + 2);
$veryLongVarName = 'string';
$var             = foo($bar, $baz, $quux);
```

- Short array syntax must be used to define arrays. [*](ruleset.md#genericarraysdisallowlongarraysyntax)
- All values in multiline arrays must be indented with 4 spaces. [*](ruleset.md#genericarraysarrayindent)
- All array values must be followed by a comma, including the last value. [*](ruleset.md#slevomatcodingstandardarraystrailingarraycomma)
- Whitespace is not allowed around the opening bracket or before the closing bracket when referencing an array.
  [*](ruleset.md#squizarraysarraybracketspacing)
- All double arrow symbols must be aligned to one space after the longest array key. [*](ruleset.md#squizarraysarraydeclaration)

```php
<?php

$array = [
    'key1'      => 'value1',
    'key2'      => 'value2',
    'keyTwenty' => 'value3',
];

$var = [
    'one'   => function() {
        $foo    = [1,2,3];
        $barBar = [
            1,
            2,
            3,
        ];
    },
    'longer' => 2,
    /* three */ 3 => 'three',
];
```

### PHP Keywords, Types, Constants and Functions

- The `global` keyword may not be used. [*](ruleset.md#squizphpglobalkeyword)
- The `PHP_SAPI` constant must be used instead of the `php_sapi_name()` function. [*](ruleset.md#genericphpsapiusage)
- PHP function calls must be in lowercase. [*](ruleset.md#squizphplowercasephpfunctions)
- PHP functions which are an alias may not be used. [*](ruleset.md#genericphpforbiddenfunctions)
- Deprecated functions should not be used. [*](ruleset.md#genericphpdeprecatedfunctions)

### Commenting

- Comments may be omitted and should not be used for typehinted arguments.
- Comments may not start with `#`. [*](ruleset.md#pearcommentinginlinecomment)
- Comments may not be empty. [*](ruleset.md#slevomatcodingstandardcommentingemptycomment)
- The words _private_, _protected_, _static_, _constructor_, _deconstructor_, _Created by_, _getter_ and _setter_,
  may not be used in comments. [*](ruleset.md#slevomatcodingstandardcommentingforbiddencomments)
- The annotations `@api`, `@author`, `@category`, `@created`, `@package`, `@subpackage` and `@version` may not
  be used in comments. Git commits provide accurate information. [*](ruleset.md#slevomatcodingstandardcommentingforbiddenannotations)
- The asterisks in a doc comment should align, and there should be one space between the asterisk and tag.
  [*](ruleset.md#squizcommentingdoccommentalignment)
- Comment tags `@param`, `@throws` and `@return` should not be aligned or contain multiple spaces between the tag,
  type and description. [*](ruleset.md#squizcommentingfunctioncomment)
- If a function throws any exceptions, they should be documented in `@throws` tags.
  [*](ruleset.md#squizcommentingfunctioncomment)
- The `@var` tag may be used in inline comments to document the _Type_ of properties. [*](ruleset.md#slevomatcodingstandardcommentinginlinedoccommentdeclaration)
- Single-line comments with a `@var` tag should be written as one-liners. [*](ruleset.md#slevomatcodingstandardcommentingrequireonelinepropertydoccomment)
- Shorthand scalar typehint variants must be used in docblocks. [*](ruleset.md#slevomatcodingstandardtypehintslongtypehints)

## Declare Statements, Namespace, and Import Statements

In addition to [PSR-12](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md#3-declare-statements-namespace-and-import-statements):

- Each PHP file should have a page level docblock with `@see`, `@copyright` and `@license`. The copyright date should
  only be adjusted if the file has changed.
- Each PHP file should have a strict type declaration at the top after the page level docblock. [*](ruleset.md#slevomatcodingstandardtypehintsdeclarestricttypes)
- Import statements should be alphabetically sorted. [*](ruleset.md#webimpresscodingstandardphpinstantiatingparenthesis)
- Import statements should not be grouped. [*](ruleset.md#slevomatcodingstandardnamespacesdisallowgroupuse)
- Each import statement should be on its own line. [*](ruleset.md#slevomatcodingstandardnamespacesmultipleusesperline)
- Absolute class name references, functions and constants should be imported. [*](ruleset.md#slevomatcodingstandardnamespacesreferenceusednamesonly)
- Unused import statements are not allowed. [*](ruleset.md#webimpresscodingstandardnamespacesunusedusestatement)
- Classes and function within the same namespace should not be imported. [*](ruleset.md#webimpresscodingstandardnamespacesunusedusestatement)
- Imports should not have an alias with the same name. [*](ruleset.md#slevomatcodingstandardnamespacesuselessalias)
- A class should not have unused private constants, (or write-only) properties and methods. [*](ruleset.md#slevomatcodingstandardclassesunusedprivateelements)

```php
<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright Copyright (c) 2016-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Vendor\Package;

Use InvalidArgumentException;
use Vendor\Package\ClassA;
use Vendor\Package\SomeNamespace\ClassB as B;

use function Another\Vendor\functionB;
use function file_get_contents;
use function Vendor\Package\functionA;

use const Another\Vendor\CONSTANT_B;
use const PASSWORD_DEFAULT;
use const Vendor\Package\CONSTANT_A;

/** FooBar is an example class. */
class FooBar
{
    // ... additional PHP code ...
}
```

## Classes, Properties, and Methods

In addition to [PSR-12](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md#4-classes-properties-and-methods):

- Class name resolution via `::class` should be used instead of `__CLASS__`, `get_class()`, `get_class($this)`,
  `get_called_class()` and `get_parent_class()`. [*](ruleset.md#slevomatcodingstandardclassesmodernclassnamereference)
- Methods may not have the final declaration in classes declared as final. [*](ruleset.md#genericcodeanalysisunnecessaryfinalmodifier)
- The colon used with return type declarations MUST be surrounded with 1 space. [*](ruleset.md#webimpresscodingstandardformattingreturntype)
- Nullable and optional arguments, which are marked as `= null`, must have the `?` symbol present. [*](ruleset.md#slevomatcodingstandardtypehintsnullabletypefornulldefaultvalue)

```php
<?php

namespace Vendor\Package;

use ArrayAccess;
use Countable;
use Vendor\AnotherPackage\FirstInterface;
use Vendor\AnotherPackage\Traits\ThirdTrait;

class ClassName extends ParentClass implements ArrayAccess, Countable
{
    use FirstTrait;
    use SecondTrait;
    use ThirdTrait;

    public const CONSTANT = 'constant';

    private $property;

    public function fooBarBaz(int $arg1, &$arg2, array $arg3 = [], ?$arg4 = null) : void
    {
        return;
    }

    public function aVeryLongMethodName(
        FirstInterface $arg1,
        &$arg2,
        array $arg3 = [],
        ?$arg4 = null
    ) : string {
        return 'foo';
    }
}
```

## Control Structures

In addition to [PSR-12](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md#5-control-structures):

- Control Structures must have at least one statement inside of the body. [*](ruleset.md#genericcodeanalysisemptystatement)

### if, elseif, else
```php
<?php

if ($expr1) {
    // if body
} elseif ($expr2) {
    // elseif body
} else {
    // else body;
}
```

### switch, case
```php
<?php

switch ($expr) {
    case 0:
        echo 'First case, with a break';
        break;
    case 1:
        echo 'Second case, which falls through';
        // no break
    case 2:
    case 3:
    case 4:
        echo 'Third case, return instead of break';
        return;
    default:
        echo 'Default case';
        break;
}
```

### while, do while
```php
<?php

while ($expr) {
    // structure body
}
```

```php
<?php

do {
    // structure body;
} while ($expr);
```

### for
```php
<?php

for ($i = 0; $i < 10; $i++) {
    // for body
}
```

### foreach
```php
<?php

foreach ($iterable as $key => $value) {
    // foreach body
}
```

### try, catch, finally

- Catch blocks may be empty. [*](ruleset.md#genericcodeanalysisemptystatementdetectedcatch)
- Catch blocks must be reachable. [*](ruleset.md#slevomatcodingstandardexceptionsdeadcatch)
- Catch blocks must use `Throwable` instead of `Exception`. [*](ruleset.md#slevomatcodingstandardexceptionsreferencethrowableonly)

```php
<?php

try {
    // May not be empty
} catch (FirstThrowableType $e) {
} catch (OtherThrowableType $e) {
    // Optionally do something
} finally {
    // Finally body
}
```

## Operators

In addition to [PSR-12](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md#6-operators):

- The not (`!`) operator must be followed by exactly one space. [*](ruleset.md#genericformattingspaceafternot)
- The `&&` and `||` operators must be used instead of `AND` and `OR`. [*](ruleset.md#squizoperatorsvalidlogicaloperators)
- The null  coalescing operator `??` should be used when possible. [*](ruleset.md#slevomatcodingstandardcontrolstructuresrequirenullcoalesceoperator)
- Assignment operators (eg `+=`, `.=`) should be used when possible. [*](ruleset.md#slevomatcodingstandardoperatorsrequirecombinedassignmentoperator)

```php
<?php

if ($a === $b) {
    $foo = $bar ?? $a ?? $b;
} elseif (! $a || $b > $c) {
    $variable = $foo ? 'foo' : 'bar';
}

$var           = 'foo';
$aVeryLongName = 'bar';
$function      = function ($arg1, $arg2) {};
```

## Closures

In addition to [PSR-12](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md#7-closures):

- Unused variables should not be passed to closures via `use`. [*](ruleset.md#slevomatcodingstandardfunctionsunusedinheritedvariablepassedtoclosure)

```php
<?php

$closureWithArgs = function ($arg1, $arg2) {
    // body
};

$closureWithArgsAndVars = function ($arg1, $arg2) use ($var1, $var2) {
    // body
};

$longArgsAndLongVars = function (
    $longArgument,
    $longerArgument,
    $muchLongerArgument
) use (
    $longVar1,
    $longerVar2,
    $muchLongerVar3
) {
   // body
};
```

## Anonymous Classes

In addition to [PSR-12](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md#8-anonymous-classes):

```php
<?php

$instance = new class {};

// Brace on the same line
$instance = new class extends Foo implements HandleableInterface {
    // Class content
};

// Brace on the next line
$instance = new class extends Foo implements
    ArrayAccess,
    Countable,
    Serializable
{
    // Class content
};
```

## Miscellaneous

- The code may not contain unreachable code. [*](ruleset.md#squizphpnonexecutablecode)
- The backtick operator may not be used for execution of shell commands. [*](ruleset.md#genericphpbacktickoperator)
- Class and Interface names should be unique in a project and must have a unique fully qualified name. [*](ruleset.md#genericclassesduplicateclassname)
- Files that contain PHP code should only have PHP code and should not have any _"inline HTML"_. [*](ruleset.md#genericfilesinlinehtml)
- There must be exactly one space after a type cast. [*](ruleset.md#genericformattingspaceaftercast)
- Constructors should be named `__construct`, not after the class. [*](ruleset.md#genericnamingconventionsconstructorname)
- The opening PHP tag should be the first item in the file. [*](ruleset.md#genericphpcharacterbeforephpopeningtag)
- Strings should not be concatenated together unless used in multiline for readability. [*](ruleset.md#genericstringsunnecessarystringconcat)
- Loose `==` and `!=` comparison operators should not be used. Use `===` and `!==` instead. [*](ruleset.md#slevomatcodingstandardcontrolstructuresdisallowequaloperators)
- Language constructs must be used without parentheses where possible. [*](ruleset.md#slevomatcodingstandardcontrolstructureslanguageconstructwithparentheses)
- Short list syntax `[...]` should be used instead of `list(...)`. [*](ruleset.md#slevomatcodingstandardphpshortlist)
- Short form of type keywords must be used. i.e. `bool` instead of `boolean`, `int` instead of `integer`, etc.
  The `binary` and `unset` cast operators are not allowed. [*](ruleset.md#slevomatcodingstandardphptypecast)
- Parentheses should not be used if they can be omitted. [*](ruleset.md#webimpresscodingstandardformattingunnecessaryparentheses)
- Semicolons `;` should not be used if they can be omitted. [*](ruleset.md#slevomatcodingstandardphpuselesssemicolon)
- Variables should be returned directly instead of assigned to a variable which is not used. [*](ruleset.md#slevomatcodingstandardvariablesuselessvariable)
- The `self` keyword should be used instead of the current class name, and should not have spaces around `::`.
  [*](ruleset.md#squizclassesselfmemberreference)
- Static methods should not use `$this`. [*](ruleset.md#squizscopestaticthisusage)
- Double quote strings may only be used if they contain variables. [*](ruleset.md#squizstringsdoublequoteusage)
- Strings should not be enclosed in parentheses when being echoed. [*](ruleset.md#squizstringsechoedstrings)
- Type casts should not have whitespace inside the parentheses. [*](ruleset.md#squizwhitespacecastspacing)
- The opening brace for functions should be on a new line with no blank lines surrounding it. [*](ruleset.md#webimpresscodingstandardwhitespacebraceblankline)
- The PHP constructs `echo`, `print`, `return`, `include`, `include_once`, `require`, `require_once`, and `new`, should
  have one space after them. [*](ruleset.md#squizwhitespacelanguageconstructspacing)
- The object operator `->` should not have any spaces around it. [*](ruleset.md#squizwhitespaceobjectoperatorspacing)
- Semicolons should not have spaces before them. [*](ruleset.md#squizwhitespacesemicolonspacing)
- The code should not contain superfluous whitespaces. e.g. multiple empty lines, trailing spaces, etc. [*](ruleset.md#squizwhitespacesuperfluouswhitespace)
