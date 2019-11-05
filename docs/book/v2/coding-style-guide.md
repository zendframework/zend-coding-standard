# Zend Framework Coding Style Guide

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
"SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
interpreted as described in [RFC 2119][].

[RFC 2119]: http://tools.ietf.org/html/rfc2119

## 1. Overview

This specification extends [PSR-12][], the coding style guide and
requires adherence to [PSR-1][], the basic coding standard.

Like [PSR-12][], the intent of this specification is to reduce cognitive friction when
scanning code from different authors contributing to Zend Framework. It does so by 
enumerating a shared set of rules and expectations about how to format PHP code.

### 1.1 Previous language versions

Throughout this document, any instructions MAY be ignored if they do not exist in versions
of PHP supported by your project.

### 1.2 Example

This example encompasses some of the rules below as a quick overview:

```php
<?php
/**
 * @see       https://github.com/zendframework/zend-expressive for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Router\RouteCollector;
use Zend\HttpHandlerRunner\RequestHandlerRunner;
use Zend\Stratigility\MiddlewarePipeInterface;

use function Zend\Stratigility\path;

class Application implements MiddlewareInterface, RequestHandlerInterface
{
    /** @var MiddlewareFactory */
    private $factory;
    
    /** @var MiddlewarePipeInterface */
    private $pipeline;

    /** @var RouteCollector */
    private $routes;

    /** @var RequestHandlerRunner */
    private $runner;

    public function __construct(
        MiddlewareFactory $factory,
        MiddlewarePipeInterface $pipeline,
        RouteCollector $routes,
        RequestHandlerRunner $runner
    ) {
        $this->factory = $factory;
        $this->pipeline = $pipeline;
        $this->routes = $routes;
        $this->runner = $runner;
    }

    /**
     * Proxies to composed pipeline to handle.
     * {@inheritDocs}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        return $this->pipeline->handle($request);
    }

    /**
     * Run the application.
     *
     * Proxies to the RequestHandlerRunner::run() method.
     */
    public function run() : void
    {
        $this->runner->run();
    }
}
```

## 2. General

### 2.1 Basic Coding Standard

Code MUST follow all rules outlined in [PSR-1].

The term 'StudlyCaps' in PSR-1 MUST be interpreted as PascalCase where the first letter of
each word is capitalized including the very first letter.

> ### Additional Zend Framework rules
>
> For consistency a bunch of older PHP features SHOULD NOT be used:
>
> - The [short open tag][] SHOULD NOT be used.
> - Deprecated features SHOULD be avoided ([[7.0]][70.deprecated], 
>   [[7.1]][71.deprecated], [[7.2]][72.deprecated], [[7.3]][73.deprecated],
>   [[7.4]][74.deprecated])
> - The [backtick operator][] MUST NOT be used.
> - The [goto][] language construct SHOULD NOT be used.
> - The [global][] keyword MUST NOT be used.
> - The constant [PHP_SAPI][] SHOULD be used instead of the `php_sapi_name()` function.
> - [aliases][] SHOULD NOT be used.
>
> There MUST NOT be a space before a semicolon. Redundant semicolons SHOULD be avoided.
>
> Non executable code MUST be removed.
>
> There MUST be a single space after language constructs.
>
> Parentheses MUST be omitted where possible.
>
> PHP function calls MUST be in lowercase.

### 2.2 Files

> ### Additional Zend Framework rules
>
> There MAY NOT be any content before the opening tag. Inline HTML in PHP code
> SHOULD be avoided. All code MUST be executable and non executable code SHOULD
> be removed.
>
> The `declare(strict_types=1)` directive MUST be declared and be the first 
> statement in a file.
>

All PHP files MUST use the Unix LF (linefeed) line ending only.

All PHP files MUST end with a non-blank line, terminated with a single LF.

The closing `?>` tag MUST be omitted from files containing only PHP.

### 2.3 Lines

There MUST NOT be a hard limit on line length.

The soft limit on line length MUST be 120 characters.

Lines SHOULD NOT be longer than 80 characters; lines longer than that SHOULD
be split into multiple subsequent lines of no more than 80 characters each.

There MUST NOT be trailing whitespace at the end of lines.

Blank lines MAY be added to improve readability and to indicate related
blocks of code except where explicitly forbidden.

> ### Additional Zend Framework rules
>
> There MAY be maximum one blank line to improve readability and to indicate 
> related blocks of code except where explicitly forbidden.
>
> There MAY NOT be any blank line after opening braces and before closing braces.

There MUST NOT be more than one statement per line.

> ### Additional Zend Framework rules
>
> There MUST NOT be a space before a semicolon. Redundant semicolons SHOULD be 
> avoided.

### 2.4 Indenting and Spacing

Code MUST use an indent of 4 spaces for each indent level, and MUST NOT use
tabs for indenting.

> ### Additional Zend Framework rules
>
> Encapsed strings MAY be used instead of concatenating strings. When 
> concatenating strings, there MUST be a single whitespace before and after the
> concatenation operator. The concatenation operator MUST NOT be the at the end 
> of a line. If multi-line concatenation is used there MUST be an indent of 4 
> spaces.

```php
<?php

// Encapsed strings
$a = 'foo';
$b = 'bar';

$c = "I like $a and $b";

// Concatenating
$a = 'Hello ';
$b = $a
   . 'World!';
```

### 2.5 Keywords and Types

All PHP reserved keywords and types [[1]][keywords][[2]][types] MUST be in lower case.

Any new types and keywords added to future PHP versions MUST be in lower case.

Short form of type keywords MUST be used i.e. `bool` instead of `boolean`,
`int` instead of `integer` etc.

### 2.6 Variables

> ### Additional Zend Framework rules
>
> Variable names MUST be declared in camelCase.

### 2.7 Arrays

> ### Additional Zend Framework rules
>
> The short array syntax MUST be used to define arrays.
>
> All values in multiline arrays must be indented with 4 spaces.
>
> All array values must be followed by a comma, including the last value.
>
> There MUST NOT be whitespace around the opening bracket or before the closing 
> bracket when referencing an array.
>
> All double arrow symbols MUST be aligned to one space after the longest array
> key.

```php
<?php

$array2 = [
    'one'    => function () {
        $foo    = [1, 2, 3];
        $barBar = [
            1,
            2,
            3,
        ];
    },
    'longer' => 2,
    3        => 'three',
];
```

> The short list syntax `[...]` SHOULD be used instead of `list(...)`.

```php
<?php

[$a, $b, $c] = [1, 2, 3];
```

## 3. Declare Statements, Namespace, and Import Statements

The header of a PHP file may consist of a number of different blocks. If present,
each of the blocks below MUST be separated by a single blank line, and MUST NOT contain
a blank line. Each block MUST be in the order listed below, although blocks that are
not relevant may be omitted.

* Opening `<?php` tag.
* File-level docblock.
* One or more declare statements.
* The namespace declaration of the file.
* One or more class-based `use` import statements.
* One or more function-based `use` import statements.
* One or more constant-based `use` import statements.
* The remainder of the code in the file.

When a file contains a mix of HTML and PHP, any of the above sections may still
be used. If so, they MUST be present at the top of the file, even if the
remainder of the code consists of a closing PHP tag and then a mixture of HTML and
PHP.

When the opening `<?php` tag is on the first line of the file, it MUST be on its
own line with no other statements unless it is a file containing markup outside of PHP
opening and closing tags.

Import statements MUST never begin with a leading backslash as they
must always be fully qualified.

> ### Additional Zend Framework rules
>
> There MUST be a single space after the namespace keyword and there MAY NOT be
> a space around a namespace separator.
>
> Import statements MUST be alphabetically sorted.
>
> Unused import statements SHOULD be removed.
>
> Fancy group import statements MUST NOT be used.
>
> Each import statement MUST be on its own line.
>
> Import statement aliases for classes, traits, functions and constants MUST
> be useful, meaning that aliases SHOULD only be used if a class with the same
> name is imported.
>
> Classes, traits, interfaces, constants and functions MUST be imported.

The following example illustrates a complete list of all blocks:

```php
<?php

/**
 * This file contains an example of coding styles.
 */

declare(strict_types=1);

namespace Vendor\Package;

use Vendor\Package\ClassA as A;
use Vendor\Package\ClassB; 
use Vendor\Package\ClassC as C;
use Vendor\Package\SomeNamespace\ClassD as D;
use Vendor\Package\AnotherNamespace\ClassE as E;

use function Vendor\Package\functionA;
use function Vendor\Package\functionB;
use function Another\Vendor\functionC;

use const Vendor\Package\CONSTANT_A;
use const Vendor\Package\CONSTANT_B;
use const Another\Vendor\CONSTANT_C;

/**
 * FooBar is an example class.
 */
class FooBar
{
    // ... additional PHP code ...
}

```

Compound namespaces with a depth of more than two MUST NOT be used. Therefore the
following is the maximum compounding depth allowed:

```php
<?php

use Vendor\Package\SomeNamespace\{
    SubnamespaceOne\ClassA,
    SubnamespaceOne\ClassB,
    SubnamespaceTwo\ClassY,
    ClassZ,
};
```

And the following would not be allowed:

```php
<?php

use Vendor\Package\SomeNamespace\{
    SubnamespaceOne\AnotherNamespace\ClassA,
    SubnamespaceOne\ClassB,
    ClassZ,
};
```

When wishing to declare strict types in files containing markup outside PHP
opening and closing tags, the declaration MUST be on the first line of the file
and include an opening PHP tag, the strict types declaration and closing tag.

For example:

```php
<?php declare(strict_types=1) ?>
<html>
<body>
    <?php
        // ... additional PHP code ...
    ?>
</body>
</html>
```

Declare statements MUST contain no spaces and MUST be exactly `declare(strict_types=1)`
(with an optional semi-colon terminator).

Block declare statements are allowed and MUST be formatted as below. Note position of
braces and spacing:

```php
declare(ticks=1) {
    // some code
}
```

## 4. Classes, Properties, and Methods

The term "class" refers to all classes, interfaces, and traits.

Any closing brace MUST NOT be followed by any comment or statement on the
same line.

When instantiating a new class, parentheses MUST always be present even when
there are no arguments passed to the constructor.

```php
new Foo();
```

> ### Additional Zend Framework rules
>
> There MUST NOT be duplicate class names.
>
> The file name MUST match the case of the terminating class name.
>
> PHP 4 style constructors MUST NOT be used.
>
> Correct class name casing MUST be used.
>
> Abstract classes MUST have a `Abstract` prefix.
>
> Exception classes MUST have a `Exception` suffix.
>
> Interface classes MUST have a `Interface` suffix.
>
> Trait classes MUST have a `Trait` suffix.
>
> For self-reference a class lower-case `self::` MUST be used without spaces 
> around the scope resolution operator.
>
> Class name resolution via `::class` MUST be used instead of `__CLASS__`, 
> `get_class()`, `get_class($this)`, `get_called_class()`, `get_parent_class()` 
> and string reference.
>
> There MAY NOT be any whitespace around the double colon operator.
>
> Unused private methods, constants and properties MUST be removed.

### 4.1 Extends and Implements

The `extends` and `implements` keywords MUST be declared on the same line as
the class name.

The opening brace for the class MUST go on its own line; the closing brace
for the class MUST go on the next line after the body.

Opening braces MUST be on their own line and MUST NOT be preceded or followed
by a blank line.

Closing braces MUST be on their own line and MUST NOT be preceded by a blank
line.

```php
<?php

namespace Vendor\Package;

use FooClass;
use BarClass as Bar;
use OtherVendor\OtherPackage\BazClass;

class ClassName extends ParentClass implements \ArrayAccess, \Countable
{
    // constants, properties, methods
}
```

Lists of `implements` and, in the case of interfaces, `extends` MAY be split
across multiple lines, where each subsequent line is indented once. When doing
so, the first item in the list MUST be on the next line, and there MUST be only
one interface per line.

```php
<?php

namespace Vendor\Package;

use FooClass;
use BarClass as Bar;
use OtherVendor\OtherPackage\BazClass;

class ClassName extends ParentClass implements
    \ArrayAccess,
    \Countable,
    \Serializable
{
    // constants, properties, methods
}
```

### 4.2 Using traits

The `use` keyword used inside the classes to implement traits MUST be
declared on the next line after the opening brace.

```php
<?php

namespace Vendor\Package;

use Vendor\Package\FirstTrait;

class ClassName
{
    use FirstTrait;
}
```

Each individual trait that is imported into a class MUST be included
one-per-line and each inclusion MUST have its own `use` import statement.

> ### Additional Zend Framework rules
>
> Traits MUST be sorted alphabetically.

```php
<?php

namespace Vendor\Package;

use Vendor\Package\FirstTrait;
use Vendor\Package\SecondTrait;
use Vendor\Package\ThirdTrait;

class ClassName
{
    use FirstTrait;
    use SecondTrait;
    use ThirdTrait;
}
```

When the class has nothing after the `use` import statement, the class
closing brace MUST be on the next line after the `use` import statement.

```php
<?php

namespace Vendor\Package;

use Vendor\Package\FirstTrait;

class ClassName
{
    use FirstTrait;
}
```

Otherwise, it MUST have a blank line after the `use` import statement.

```php
<?php

namespace Vendor\Package;

use Vendor\Package\FirstTrait;

class ClassName
{
    use FirstTrait;

    private $property;
}
```

When using the `insteadof` and `as` operators they must be used as follows taking
note of indentation, spacing, and new lines.

```php
<?php

class Talker
{
    use A, B, C {
        B::smallTalk insteadof A;
        A::bigTalk insteadof C;
        C::mediumTalk as FooBar;
    }
}
```

### 4.3 Properties and Constants

Visibility MUST be declared on all properties.

Visibility MUST be declared on all constants if your project PHP minimum
version supports constant visibilities (PHP 7.1 or later).

The `var` keyword MUST NOT be used to declare a property.

There MUST NOT be more than one property declared per statement.

Property names MUST NOT be prefixed with a single underscore to indicate
protected or private visibility. That is, an underscore prefix explicitly has
no meaning.

There MUST be a space between type declaration and property name.

> ### Additional Zend Framework rules
>
> Default null values MUST be omitted for class properties.

A property declaration looks like the following:

```php
<?php

namespace Vendor\Package;

class ClassName
{
    public $foo; // `= null` should be omitted
    public static int $bar = 0;
}
```

### 4.4 Methods and Functions

Visibility MUST be declared on all methods.

Method names MUST NOT be prefixed with a single underscore to indicate
protected or private visibility. That is, an underscore prefix explicitly has
no meaning.

Method and function names MUST NOT be declared with space after the method name. 
The opening brace MUST go on its own line, and the closing brace MUST go on the
next line following the body. There MUST NOT be a space after the opening
parenthesis, and there MUST NOT be a space before the closing parenthesis.

> ### Additional Zend Framework rules
>
> There MUST be a single empty line between methods in a class.
>
> The pseudo-variable `$this` MUST NOT be called inside a static method or 
> function.
>
> Returned variables SHOULD be useful and SHOULD NOT be assigned to a value and 
> returned on the next line.

A method declaration looks like the following. Note the placement of
parentheses, commas, spaces, and braces:

```php
<?php

namespace Vendor\Package;

class ClassName
{
    public function fooBarBaz($arg1, &$arg2, $arg3 = [])
    {
        // method body
    }
}
```

A function declaration looks like the following. Note the placement of
parentheses, commas, spaces, and braces:

```php
<?php

function fooBarBaz($arg1, &$arg2, $arg3 = [])
{
    // function body
}
```

### 4.5 Method and Function Arguments

In the argument list, there MUST NOT be a space before each comma, and there
MUST be one space after each comma.

Method and function arguments with default values MUST go at the end of the argument
list.

```php
<?php

namespace Vendor\Package;

class ClassName
{
    public function foo(int $arg1, &$arg2, $arg3 = [])
    {
        // method body
    }
}
```

Argument lists MAY be split across multiple lines, where each subsequent line
is indented once. When doing so, the first item in the list MUST be on the
next line, and there MUST be only one argument per line.

When the argument list is split across multiple lines, the closing parenthesis
and opening brace MUST be placed together on their own line with one space
between them.

```php
<?php

namespace Vendor\Package;

class ClassName
{
    public function aVeryLongMethodName(
        ClassTypeHint $arg1,
        &$arg2,
        array $arg3 = []
    ) {
        // method body
    }
}
```

When you have a return type declaration present, there MUST be one space after
the colon followed by the type declaration. The colon and declaration MUST be
on the same line as the argument list closing parenthesis with no spaces between
the two characters.

```php
<?php

declare(strict_types=1);

namespace Vendor\Package;

class ReturnTypeVariations
{
    public function functionName(int $arg1, $arg2): string
    {
        return 'foo';
    }

    public function anotherFunction(
        string $foo,
        string $bar,
        int $baz
    ): string {
        return 'foo';
    }
}
```

In nullable type declarations, there MUST NOT be a space between the question mark
and the type.

> ### Additional Zend Framework rules
>
> The question mark MUST be used when the default argument value is null.

```php
<?php

declare(strict_types=1);

namespace Vendor\Package;

class ReturnTypeVariations
{
    public function functionName(?string $arg1, ?int &$arg2 = null): ?string
    {
        return 'foo';
    }
}
```

When using the reference operator `&` before an argument, there MUST NOT be
a space after it, like in the previous example.

There MUST NOT be a space between the variadic three dot operator and the argument
name:

```php
public function process(string $algorithm, ...$parts)
{
    // processing
}
```

When combining both the reference operator and the variadic three dot operator,
there MUST NOT be any space between the two of them:

```php
public function process(string $algorithm, &...$parts)
{
    // processing
}
```

### 4.6 `abstract`, `final`, and `static`

When present, the `abstract` and `final` declarations MUST precede the
visibility declaration.

> ### Additional Zend Framework rules
>
> The `final` keyword on methods MUST be omitted in final declared classes.

When present, the `static` declaration MUST come after the visibility
declaration.

```php
<?php

namespace Vendor\Package;

abstract class ClassName
{
    protected static $foo;

    abstract protected function zim();

    final public static function bar()
    {
        // method body
    }
}
```

### 4.7 Method and Function Calls

When making a method or function call, there MUST NOT be a space between the
method or function name and the opening parenthesis, there MUST NOT be a space
after the opening parenthesis, and there MUST NOT be a space before the
closing parenthesis. In the argument list, there MUST NOT be a space before
each comma, and there MUST be one space after each comma.

```php
<?php

bar();
$foo->bar($arg1);
Foo::bar($arg2, $arg3);
```

Argument lists MAY be split across multiple lines, where each subsequent line
is indented once. When doing so, the first item in the list MUST be on the
next line, and there MUST be only one argument per line. A single argument being
split across multiple lines (as might be the case with an anonymous function or
array) does not constitute splitting the argument list itself.

```php
<?php

$foo->bar(
    $longArgument,
    $longerArgument,
    $muchLongerArgument
);
```

```php
<?php

somefunction($foo, $bar, [
  // ...
], $baz);

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello ' . $app->escape($name);
});
```

## 5. Control Structures

The general style rules for control structures are as follows:

- There MUST be one space after the control structure keyword
- There MUST NOT be a space after the opening parenthesis
- There MUST NOT be a space before the closing parenthesis
- There MUST be one space between the closing parenthesis and the opening
  brace
- The structure body MUST be indented once
- The body MUST be on the next line after the opening brace
- The closing brace MUST be on the next line after the body

The body of each structure MUST be enclosed by braces. This standardizes how
the structures look and reduces the likelihood of introducing errors as new
lines get added to the body.

> ### Additional Zend Framework rules
>
> There MUST be one single space after `break` and `continue` structures with 
> a numeric argument argument.
>
> Statements MUST NOT be empty, except for catch statements.

### 5.1 `if`, `elseif`, `else`

An `if` structure looks like the following. Note the placement of parentheses,
spaces, and braces; and that `else` and `elseif` are on the same line as the
closing brace from the earlier body.

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

The keyword `elseif` SHOULD be used instead of `else if` so that all control
keywords look like single words.

Expressions in parentheses MAY be split across multiple lines, where each
subsequent line is indented at least once. When doing so, the first condition
MUST be on the next line. The closing parenthesis and opening brace MUST be
placed together on their own line with one space between them. Boolean
operators between conditions MUST always be at the beginning or at the end of
the line, not a mix of both.

```php
<?php

if (
    $expr1
    && $expr2
) {
    // if body
} elseif (
    $expr3
    && $expr4
) {
    // elseif body
}
```

### 5.2 `switch`, `case`

A `switch` structure looks like the following. Note the placement of
parentheses, spaces, and braces. The `case` statement MUST be indented once
from `switch`, and the `break` keyword (or other terminating keywords) MUST be
indented at the same level as the `case` body. There MUST be a comment such as
`// no break` when fall-through is intentional in a non-empty `case` body.

> ### Additional Zend Framework rules
>
> The `continue` control structure MUST NOT be used in switch statements, 
> `break` SHOULD be used instead.

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

Expressions in parentheses MAY be split across multiple lines, where each
subsequent line is indented at least once. When doing so, the first condition
MUST be on the next line. The closing parenthesis and opening brace MUST be
placed together on their own line with one space between them. Boolean
operators between conditions MUST always be at the beginning or at the end of
the line, not a mix of both.

```php
<?php

switch (
    $expr1
    && $expr2
) {
    // structure body
}
```

### 5.3 `while`, `do while`

A `while` statement looks like the following. Note the placement of
parentheses, spaces, and braces.

```php
<?php

while ($expr) {
    // structure body
}
```

Expressions in parentheses MAY be split across multiple lines, where each
subsequent line is indented at least once. When doing so, the first condition
MUST be on the next line. The closing parenthesis and opening brace MUST be
placed together on their own line with one space between them. Boolean
operators between conditions MUST always be at the beginning or at the end of
the line, not a mix of both.

```php
<?php

while (
    $expr1
    && $expr2
) {
    // structure body
}
```

Similarly, a `do while` statement looks like the following. Note the placement
of parentheses, spaces, and braces.

```php
<?php

do {
    // structure body;
} while ($expr);
```

Expressions in parentheses MAY be split across multiple lines, where each
subsequent line is indented at least once. When doing so, the first condition
MUST be on the next line. Boolean operators between conditions MUST
always be at the beginning or at the end of the line, not a mix of both.

```php
<?php

do {
    // structure body;
} while (
    $expr1
    && $expr2
);
```

### 5.4 `for`

A `for` statement looks like the following. Note the placement of parentheses,
spaces, and braces.

```php
<?php

for ($i = 0; $i < 10; $i++) {
    // for body
}
```

Expressions in parentheses MAY be split across multiple lines, where each
subsequent line is indented at least once. When doing so, the first expression
MUST be on the next line. The closing parenthesis and opening brace MUST be
placed together on their own line with one space between them.

```php
<?php

for (
    $i = 0;
    $i < 10;
    $i++
) {
    // for body
}
```

### 5.5 `foreach`

A `foreach` statement looks like the following. Note the placement of
parentheses, spaces, and braces.

```php
<?php

foreach ($iterable as $key => $value) {
    // foreach body
}
```

### 5.6 `try`, `catch`, `finally`

A `try-catch-finally` block looks like the following. Note the placement of
parentheses, spaces, and braces.

```php
<?php

try {
    // try body
} catch (FirstThrowableType $e) {
    // catch body
} catch (OtherThrowableType | AnotherThrowableType $e) {
    // catch body
} finally {
    // finally body
}
```

> ### Additional Zend Framework rules
>
> All catch blocks MUST be reachable.

## 6. Operators

Style rules for operators are grouped by arity (the number of operands they 
take).

When space is permitted around an operator, multiple spaces MAY be
used for readability purposes.

> ### Additional Zend Framework rules
>
> There MUST be at least one space on either side of an equals sign used
> to assign a value to a variable. In case of a block of related
> assignments, more spaces MUST be inserted before the equal sign to
> promote readability.
>
> There MUST NOT be any white space around the object operator UNLESS
> multilines are used.
>
> Loose comparison operators SHOULD NOT be used, use strict comparison
> operators instead. e.g. use `===` instead of `==`.
>
> The null coalesce operator SHOULD be used when possible. 
>
> Assignment operators SHOULD be used when possible.
>
> The `&&` and `||` operators SHOULD be used instead of `and` and `or`.

All operators not described here are left undefined.

### 6.1. Unary operators

The increment/decrement operators MUST NOT have any space between
the operator and operand.

```php
$i++;
++$j;
```

Type casting operators MUST NOT have any space within the parentheses.

> ### Additional Zend Framework rules
>
> There MUST be one whitespace after a type casting operator.

```php
$intValue = (int) $input;
```

> ### Additional Zend Framework rules
>
> There MUST be one whitespace after unary not.

```php
<?php

if (! true) {
    return false;
}
```

### 6.2. Binary operators

All binary [arithmetic][], [comparison][], [assignment][], [bitwise][],
[logical][], [string][], and [type][] operators MUST be preceded and
followed by at least one space:

```php
if ($a === $b) {
    $foo = $bar ?? $a ?? $b;
} elseif ($a > $b) {
    $foo = $a + $b * $c;
}
```

### 6.3. Ternary operators

The conditional operator, also known simply as the ternary operator, MUST be
preceded and followed by at least one space around both the `?`
and `:` characters:

```php
$variable = $foo ? 'foo' : 'bar';
```

When the middle operand of the conditional operator is omitted, the operator
MUST follow the same style rules as other binary [comparison][] operators:
```php
$variable = $foo ?: 'bar';
```

## 7. Closures

Closures MUST be declared with a space after the `function` keyword, and a
space before and after the `use` keyword.

The opening brace MUST go on the same line, and the closing brace MUST go on
the next line following the body.

There MUST NOT be a space after the opening parenthesis of the argument list
or variable list, and there MUST NOT be a space before the closing parenthesis
of the argument list or variable list.

In the argument list and variable list, there MUST NOT be a space before each
comma, and there MUST be one space after each comma.

Closure arguments with default values MUST go at the end of the argument
list.

If a return type is present, it MUST follow the same rules as with normal
functions and methods; if the `use` keyword is present, the colon MUST follow
the `use` list closing parentheses with no spaces between the two characters.

> ### Additional Zend Framework rules
>
> Inherited variables passed via `use` MUST be used in closures.

A closure declaration looks like the following. Note the placement of
parentheses, commas, spaces, and braces:

```php
<?php

$closureWithArgs = function ($arg1, $arg2) {
    // body
};

$closureWithArgsAndVars = function ($arg1, $arg2) use ($var1, $var2) {
    // body
};

$closureWithArgsVarsAndReturn = function ($arg1, $arg2) use ($var1, $var2): bool {
    // body
};
```

Argument lists and variable lists MAY be split across multiple lines, where
each subsequent line is indented once. When doing so, the first item in the
list MUST be on the next line, and there MUST be only one argument or variable
per line.

When the ending list (whether of arguments or variables) is split across
multiple lines, the closing parenthesis and opening brace MUST be placed
together on their own line with one space between them.

The following are examples of closures with and without argument lists and
variable lists split across multiple lines.

```php
<?php

$longArgs_noVars = function (
    $longArgument,
    $longerArgument,
    $muchLongerArgument
) {
   // body
};

$noArgs_longVars = function () use (
    $longVar1,
    $longerVar2,
    $muchLongerVar3
) {
   // body
};

$longArgs_longVars = function (
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

$longArgs_shortVars = function (
    $longArgument,
    $longerArgument,
    $muchLongerArgument
) use ($var1) {
   // body
};

$shortArgs_longVars = function ($arg) use (
    $longVar1,
    $longerVar2,
    $muchLongerVar3
) {
   // body
};
```

Note that the formatting rules also apply when the closure is used directly
in a function or method call as an argument.

```php
<?php

$foo->bar(
    $arg1,
    function ($arg2) use ($var1) {
        // body
    },
    $arg3
);
```

## 8. Anonymous Classes

Anonymous Classes MUST follow the same guidelines and principles as closures
in the above section.

```php
<?php

$instance = new class {};
```

The opening brace MAY be on the same line as the `class` keyword so long as
the list of `implements` interfaces does not wrap. If the list of interfaces
wraps, the brace MUST be placed on the line immediately following the last
interface.

```php
<?php

// Brace on the same line
$instance = new class extends \Foo implements \HandleableInterface {
    // Class content
};

// Brace on the next line
$instance = new class extends \Foo implements
    \ArrayAccess,
    \Countable,
    \Serializable
{
    // Class content
};
```

## 9. Commenting and DocBlocks

> ### Additional Zend Framework rules
>
> Code SHOULD be written so it explains itself. DocBlocks and comments 
> SHOULD only be used if necessary. They MUST NOT start with `#` and MUST 
> NOT be empty. They SHOULD NOT be used for already typehinted arguments, 
> except arrays.
>
> The asterisks in a DocBlock should align, and there should be one
> space between the asterisk and tag.
>
> PHPDoc tags `@param`, `@return` and `@throws` SHOULD not be aligned or
> contain multiple spaces between the tag, type and description.
>
> If a function throws any exceptions, it SHOULD be documented with
> `@throws` tags.
>
> DocBlocks MUST follow this specific order of annotations with empty
> newline between specific groups:
>
> ```php
> /**
>  * <Summary>
>  *
>  * <Description>
>  *
>  * @internal
>  * @deprecated
>  *
>  * @link
>  * @see
>  * @uses
>  *
>  * @param
>  * @return
>  * @throws
>  */
> ```
>
> The annotations `@api`, `@author`, `@category`, `@created`, `@package`,
> `@subpackage` and `@version` MUST NOT be used in comments. Git commits
> provide accurate information.
> 
> The words _private_, _protected_, _static_, _constructor_, _deconstructor_,
> _Created by_, _getter_ and _setter_, MUST NOT be used in comments.
>
> The `@var` tag MAY be used in inline comments to document the _Type_
> of properties. Single-line property comments with a `@var` tag SHOULD
> be written as one-liners. The `@var` MAY NOT be used for constants.
>
> The correct tag case of PHPDocs and PHPUnit tags MUST be used.
>
> Inline DocComments MAY be used at the end of the line, with at least a
> single space preceding. Inline DocComments MUST NOT be placed after curly 
> brackets.
>
> Heredoc and nowdoc tags MUST be uppercase without spaces.

[PSR-1]: https://www.php-fig.org/psr/psr-1/
[PSR-2]: https://www.php-fig.org/psr/psr-2/
[PSR-12]: https://www.php-fig.org/psr/psr-12/
[keywords]: https://www.php.net/manual/en/reserved.keywords.php
[types]: https://www.php.net/manual/en/reserved.other-reserved-words.php
[arithmetic]: https://www.php.net/manual/en/language.operators.arithmetic.php
[assignment]: https://www.php.net/manual/en/language.operators.assignment.php
[comparison]: https://www.php.net/manual/en/language.operators.comparison.php
[bitwise]: https://www.php.net/manual/en/language.operators.bitwise.php
[logical]: https://www.php.net/manual/en/language.operators.logical.php
[string]: https://www.php.net/manual/en/language.operators.string.php
[type]: https://www.php.net/manual/en/language.operators.type.php
[short open tag]: https://www.php.net/manual/en/language.basic-syntax.phptags.php
[70.deprecated]: https://www.php.net/manual/en/migration70.deprecated.php
[71.deprecated]: https://www.php.net/manual/en/migration71.deprecated.php
[72.deprecated]: https://www.php.net/manual/en/migration72.deprecated.php
[73.deprecated]: https://www.php.net/manual/en/migration73.deprecated.php
[74.deprecated]: https://www.php.net/manual/en/migration74.deprecated.php
[backtick operator]: https://www.php.net/manual/en/language.operators.execution.php 
[goto]: https://www.php.net/manual/en/control-structures.goto.php 
[global]: https://www.php.net/manual/en/language.variables.scope.php#language.variables.scope.global 
[PHP_SAPI]: https://www.php.net/manual/en/function.php-sapi-name.php#refsect1-function.php-sapi-name-notes 
[aliases]: https://www.php.net/manual/en/aliases.php 
