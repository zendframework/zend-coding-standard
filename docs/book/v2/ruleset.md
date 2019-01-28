# Ruleset

## PSR2
Use PSR-2 coding standard as a base

### PSR2.ControlStructures.ElseIfDeclaration.NotAllowed
_PSR-12:_ The keyword `elseif` should be used instead of `else if` so that all control keywords look like single words.



## Generic

### Generic.Arrays.ArrayIndent
All values in multiline arrays must be indented with 4 spaces.

### Generic.Arrays.DisallowLongArraySyntax
Short array syntax must be used to define arrays.

```php
<?php
$foo = ['foo' => 'bar'];
```

### Generic.Classes.DuplicateClassName
Class and Interface names should be unique in a project and must have a unique fully qualified name. They should never
be duplicated.

*Valid: Unique class names.*
```php
<?php
// src/Vendor/Package/Foo.php
namespace Vendor\Package {
    class Foo
    {
    }
}

// src/Vendor/AnotherPackage/Foo.php
namespace Vendor\AnotherPackage {
    class Foo
    {
    }
}
```

*Invalid: A class duplicated across multiple files.*
```php
<?php
// src/Vendor/Package/Foo.php
namespace Vendor\Package {
    class Foo
    {
    }
}

// app/Vendor/Package/Foo.php
namespace Vendor\Package {
    class Foo
    {
    }
}
```

### Generic.CodeAnalysis.EmptyStatement
Control Structures must have at least one statement inside of the body.

*Valid:*
```php
<?php
for ($i; $i > 0; $i--) {
    echo 'hello';
}
```

*Invalid:*
```php
<?php
if ($something) echo 'hello';
```
#### Generic.CodeAnalysis.EmptyStatement.DetectedCatch
Catch blocks may be empty.

```php
<?php
try {
    // Do something
} catch (SomeThrowableType $e) {
}
```

### Generic.CodeAnalysis.UnnecessaryFinalModifier
Methods may not have the final declaration classes declared as final.

*Valid: Only the class is marked as final.*
```php
<?php
final class Foo
{
    public function bar()
    {
    }
}
```

*Invalid: A method in a final class is also marked final.*
```php
<?php
final class Foo
{
    final public function bar()
    {
    }
}
```

### Generic.Files.InlineHTML
Files that contain PHP code should only have PHP code and should not have any _"inline HTML"_.

*Valid: A php file with only php code in it.*
```php
<?php
$foo = 'bar';
echo $foo . 'baz';
```

*Invalid: A php file with html in it outside of the php tags.*
```php
<strong>some string here</strong>
<?php
$foo = 'bar';
echo $foo . 'baz';
```

### Generic.Files.LineEndings
_PSR-12:_ All PHP files MUST use the Unix LF (linefeed) line ending only.

### Generic.Formatting.MultipleStatementAlignment
There should be one space on either side of an equals sign used to assign a value to a variable.

*Valid: Equal signs are surrounded by one space.*
```php
<?php
$shortVar = (1 + 2);
$veryLongVarName = 'string';

$foo = $bar;
$result = foo($bar, $baz, $quux);
```

*Invalid: Equal signs are aligned.*
```php
<?php
$shortVar        = (1 + 2);
$veryLongVarName = 'string';

$foo    = $bar;
$result = foo($bar, $baz, $quux);
```

### Generic.Formatting.SpaceAfterCast
There must be exactly one space after a type cast.

*Valid: A cast operator is followed by one space.*
```php
<?php
$foo = (string) 1;
```

*Invalid: A cast operator is not followed by whitespace.*
```php
<?php
$foo = (string)1;
```

### Generic.Formatting.SpaceAfterNot
The not `!` operator must be followed by exactly one space.

*Valid: One space after `!`.*
```php
<?php
if (! foo() && (! $x || true)) {}
$var = ! ($x || $y);
```

*Invalid: No space after `!` or a space before.*
```php
<?php
if (!foo() && ( ! $x || true)) {}
$var = !($x || $y);
```

### Generic.NamingConventions.ConstructorName
Constructors should be named `__construct`, not after the class.

*Valid: The constructor is named __construct.*
```php
<?php
class Foo
{
    function __construct()
    {
    }
}
```

*Invalid: The PHP4 style class name constructor is used.*
```php
<?php
class Foo
{
    function Foo()
    {
    }
}
```

### Generic.PHP.BacktickOperator
The backtick operator may not be used for execution of shell commands.

*Invalid: Using the backtick operator.*
```php
<?php
$output = `ls -al`;
```

### Generic.PHP.CharacterBeforePHPOpeningTag
The opening PHP tag should be the first item in the file.

*Valid: A file starting with an opening php tag.*
```php
<?php
echo 'Foo';
```

*Invalid: A file with content before the opening php tag.*
```php
<strong>Beginning content</em>
<?php
echo 'Foo';
```

### Generic.PHP.DeprecatedFunctions
Deprecated functions should not be used.

*Valid: A non-deprecated function is used.*
```php
<?php
$foo = explode('a', $bar);
```

*Invalid: A deprecated function is used.*
```php
<?php
$foo = split('a', $bar);
```

### Generic.PHP.DisallowShortOpenTag
#### Generic.PHP.DisallowShortOpenTag.EchoFound
_PSR-1:_ PHP code must use the long `<?php ?>` tags or the short-echo `<?= ?>` tags; it must not use the other tag
variations.

### Generic.PHP.DiscourageGoto
Forbid goto instruction.

### Generic.PHP.ForbiddenFunctions
PHP functions which are an alias may not be used. _This can't be fixed automatically and need to be done manually._

| Alias        | Replace with     |
| ------------ | ---------------- |
| chop         | rtrim            |
| close        | closedir         |
| compact      |                  |
| delete       | unset            |
| doubleval    | floatval         |
| extract      |                  |
| fputs        | fwrite           |
| ini_alter    | ini_set          |
| is_integer   | is_int           |
| is_long      | is_int           |
| is_null      | null ===         |
| is_real      | is_float         |
| is_writeable | is_writable      |
| join         | implode          |
| key_exists   | array_key_exists |
| pos          | current          |
| settype      |                  |
| show_source  | highlight_file   |
| sizeof       | count            |
| strchr       | strstr           |

### Generic.PHP.LowerCaseType
_PSR-12:_ Any new types and keywords added to future PHP versions must be in lower case.

### Generic.PHP.SAPIUsage
The `PHP_SAPI` constant must be used instead of the `php_sapi_name()` function.

*Valid: PHP_SAPI is used.*
```php
<?php
if (PHP_SAPI === 'cli') {
    echo 'Hello, CLI user.';
}
```

*Invalid: php_sapi_name() is used.*
```php
<?php
if (php_sapi_name() === 'cli') {
    echo 'Hello, CLI user.';
}
```

### Generic.Strings.UnnecessaryStringConcat
Strings should not be concatenated together unless used in multiline for readability.

*Valid: A string can be concatenated with an expression.*
```php
<?php
echo '5 + 2 = ' . (5 + 2);
```

*Invalid: Strings should not be concatenated together.*
```php
<?php
echo 'Hello' . ' ' . 'World';
```

## PEAR

### PEAR.Commenting.InlineComment
Perl-style `#` comments are not allowed.

*Valid: A `//` style comment.*
```php
<?php
// A comment.
```

*Invalid: A `#` style comment.*
```php
<?php
# An invalid comment.
```

## SlevomatCodingStandard

### SlevomatCodingStandard.Arrays.TrailingArrayComma
All array values must be followed by a comma, including the last value. Commas after last element in an array make
adding a new element easier and result in a cleaner versioning diff.

### SlevomatCodingStandard.Classes.ClassConstantVisibility
_PSR-12:_ Visibility MUST be declared on all constants if your project PHP minimum version supports constant visibilities
(PHP 7.1 or later).

### SlevomatCodingStandard.Classes.ModernClassNameReference
Class name resolution via `::class` should be used instead of `__CLASS__`, `get_class()`, `get_class($this)`,
`get_called_class()` and `get_parent_class()`.

### SlevomatCodingStandard.Classes.UnusedPrivateElements
A class should not have unused private constants, (or write-only) properties and methods.

### SlevomatCodingStandard.Commenting.ForbiddenAnnotations
The annotations `@api`, `@author`, `@category`, `@created`, `@package`, `@subpackage` and `@version` may not
be used in comments. Git commits provide accurate information.

### SlevomatCodingStandard.Commenting.EmptyComment
Comments may not be empty.

### SlevomatCodingStandard.Commenting.ForbiddenComments
To keep comments clean, the words _private_, _protected_, _static_, _constructor_, _deconstructor_, _Created by_,
_getter_ and _setter_ may not be used in comments

### SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration
The `@var` tag may be used in inline comments to document the _Type_ of properties, sometimes called class variables.

```php
<?php
class Foo
{
	/** @var string */
	private $foo;

	public function __construct()
	{
        /** @var string $f */
        foreach ($e as $f) {
            // ...
        }

        /** @var string[] $a */
        $a = $this->get();
    }
}
```

### SlevomatCodingStandard.Commenting.RequireOneLinePropertyDocComment
Single-line comments with a `@var` tag should be written as one-liners.

*Valid: One-line comment*
```php
<?php
/** @var array */
private $foo;
```

*Invalid: Multiple lines for a single comment*
```php
<?php
/**
 * @var array
 */
private $foo;
```

### SlevomatCodingStandard.ControlStructures.DisallowEqualOperators
Loose `==` and `!=` comparison operators should not be used. Use strict comparison `===` and `!==` instead, they are
much more secure and predictable.

*Valid: Strict comparison is used.*
```php
<?php
if ($a === $b || $c !== $d) {
    // ...
}
```

*Invalid: Loose comparison used.*
```php
<?php
if ($a == $b || $c != $d) {
    // ...
}
```

### SlevomatCodingStandard.ControlStructures.LanguageConstructWithParentheses
Language constructs must be used without parentheses where possible.

*Valid: Use language constructs without parentheses.*
```php
<?php
continue 1;
break 1;
echo 'a';
print 'b';
include 'file.php';
return 'foo';
yield [];
throw new Exception();
exit;
```

*Invalid: Language constructs with parentheses.*
```php
<?php
continue(1);
break(1);
echo('a');
print('b');
include('file.php');
return('foo');
yield([]);
throw(new Exception());
exit();
```

*Valid: Parenthesis are used for instantiating a new class.*
```php
<?php
new Foo();
```

*Invalid: Missing parenthesis.*
```php
<?php
new Foo;
```

### SlevomatCodingStandard.ControlStructures.RequireNullCoalesceOperator
The null coalescing operator `??` should be used when possible.

*Valid: Used the null coalescing operator.*
```php
<?php
$username = $user['name'] ?? 'nobody';
```

*Invalid: Using isset to check if a variable is set and return it.*
```php
<?php
$username = isset($user['name']) ? $user['name'] : 'nobody';
```

### SlevomatCodingStandard.Exceptions.DeadCatch
Catch blocks must be reachable.

*Valid: All catch blocks are reachable.*
```php
<?php
try {
	doStuff();
} catch (InvalidArgumentException $e) {
	log($e);
} catch (Throwable $e) {
	// Reachable
}
```

*Invalid: Last catch block is unreachable.*
```php
<?php
try {
	doStuff();
} catch (Throwable $e) {
	log($e);
} catch (InvalidArgumentException $e) {
	// Unreachable because `Throwable` catches everything!
}
```

### SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly
Catch blocks must use `Throwable` instead of `Exception`.

*Valid: Throwable is used to catch all errors and exceptions.*
```php
<?php
try {
	// ...
} catch (Throwable $e) {
    // ...
}
```

*Invalid: Exception is used and will not catch other Throwables. e.g. internal PHP errors*
```php
<?php
try {
	// ...
} catch (Exception $e) {
    // ...
}
```

### SlevomatCodingStandard.Functions.UnusedInheritedVariablePassedToClosure
Unused variables should not be passed to closures via `use`.

*Valid: All variables passed via `use` are used.*
```php
<?php
$closure = function (string $arg1, string $arg2) use ($var1) : string {
    return $arg1 . $arg2 . $var1;
};
```

*Invalid: Un unused variable `$var2` is passed via `use`.*
```php
<?php
$closure = function (string $arg1, string $arg2) use ($var1, $var2) : string {
    return $arg1 . $arg2 . $var1;
};
```

### SlevomatCodingStandard.Namespaces.DisallowGroupUse
Import statements should not be grouped.

*Valid: Each import statement on its own line.*
```php
<?php
use Example\A;
use Example\B;
use Vendor\Package\C;
```

*Invalid: Using import groups.*
```php
<?php
use Example\{A, B};
use Vendor\Package\C;
```

### SlevomatCodingStandard.Namespaces.MultipleUsesPerLine
Each import statement should be on its own line.

*Valid: Each import statement on its own line.*
```php
<?php
use Bar;
use Foo;
```

*Invalid: Multiple imports on one line.*
```php
<?php
use Foo, Bar;
```

### SlevomatCodingStandard.Namespaces.NamespaceSpacing
Require newlines around namespace declaration

### SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly
Absolute class name references, functions and constants should be imported.

*Valid: `Bar` is not imported.*
```php
<?php
namespace Example;

use Example\More\Baz;

class Foo extends Bar
{
}
```

*Invalid: `Bar` is in the same namespace as `Foo`.*
```php
<?php
namespace Example;

use Example\Bar;
use Example\More\Baz;

class Foo extends Bar
{
}
```

### SlevomatCodingStandard.Namespaces.UselessAlias
Imports should not have an alias with the same name.

*Valid: The alias and imported class name are different.*
```php
<?php
use Foo\Bar as MyBar;
```

*Invalid: Alias has same name as imported class.*
```php
<?php
use Foo\Bar as Bar;
```

### SlevomatCodingStandard.Operators.RequireCombinedAssignmentOperator
Assignment operators (eg `+=`, `.=`) should be used when possible.

```php
<?php
self::$a &= 2;
static::$a |= 4;
self::$$parameter .= '';
parent::${'a'} /= 10;
self::${'a'}[0] -= 100;
Anything::$a **= 2;
Something\Anything::$a %= 2;
\Something\Anything::$a *= 1000;
self::$a::$b += 4;
$this::$a <<= 2;
$this->a >>= 2;
$this->$$parameter ^= 10;
$this->{'a'} += 10;
```

### SlevomatCodingStandard.PHP.ShortList
Short list syntax `[...]` should be used instead of `list(...)`.

*Valid: The short list syntax is used for array destructuring assignment.*
```php
<?php
[$a, $b, $c] = $array;
['a' => $a, 'b' => $b, 'c' => $c] = $array;
```

*Invalid: Usage of `list`.*
```php
<?php
list($a, $b, $c) = $array;
list('a' => $a, 'b' => $b, 'c' => $c) = $array;
```

### SlevomatCodingStandard.PHP.TypeCast
_PSR-12:_ Short form of type keywords must be used. i.e. `bool` instead of `boolean`, `int` instead of `integer`, etc.

The `binary` and `unset` cast operators are not allowed.

### SlevomatCodingStandard.TypeHints.DeclareStrictTypes
Each PHP file should have a strict type declaration at the top after the page level docblock.

_PSR-12:_ Declare statements MUST contain no spaces and MUST be exactly `declare(strict_types=1)`.

```php
<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright Copyright (c) 2016-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

// ...
```

### SlevomatCodingStandard.PHP.UselessSemicolon
Semicolons `;` should not be used if they can be omitted.

*Valid: Semicolon used where needed.*
```php
<?php
foo();

class Whatever {
}
```

*Invalid: Unneeded semicolons used.*
```php
<?php
foo();;

class Whatever {
};
```

### SlevomatCodingStandard.TypeHints.LongTypeHints
Shorthand scalar typehint variants must be used in docblocks: `bool` instead of `boolean`, `int` instead of `integer`,
etc. This is for consistency with _PSR-12_ native scalar typehints which also allow shorthand variants only.

### SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue
Nullable and optional arguments, which are marked as `= null`, must have the `?` symbol present.

*Valid: Nullable argument has the `?` symbol.*
```php
<?php
function foo(?int $bar = null) {
}
```

*Invalid: Missing `?` before `int`*
```php
<?php
function foo(int $foo = null) {
}
```

### SlevomatCodingStandard.TypeHints.ParameterTypeHintSpacing
_PSR-12:_ Method and function arguments must have one space between typehint and variable. Between the nullability sign
and typehint may not be a space.

### SlevomatCodingStandard.Variables.UselessVariable
Variables should be returned directly instead of assigned to a variable which is not used.

*Valid: The value is returned directly.*
```php
<?php
function () {
	return true;
};
```

*Invalid: An extra variable is used where its value could be returned directly.*
```php
<?php
function () {
	$a = true;
	return $a;
};
```



## Squiz

### Squiz.Arrays.ArrayBracketSpacing
Whitespace is not allowed around the opening bracket or before the closing bracket when referencing an array.

*Valid: No extra spaces used.*
```php
<?php
$foo['bar'];
```

*Invalid: Extra spaces.*
```php
<?php
$foo [ 'bar' ] ;
```

### Squiz.Arrays.ArrayDeclaration
All double arrow symbols must be aligned to one space after the longest array key.

*Valid: Array is aligned.*
```php
<?php
return [
    ConfigAggregator::ENABLE_CACHE => true,
    'debug'                        => false,
    'zend-expressive' => [
        'raise_throwables'      => true,
        'programmatic_pipeline' => true,
        'error_handler'         => [
            'template_404'   => 'error::404',
            'template_error' => 'error::error',
        ],
    ],
];
```

*Invalid: Double arrow symbols are not aligned.*
```php
<?php
return [
    ConfigAggregator::ENABLE_CACHE => true,
    'debug' => false,
    'zend-expressive' => [
        'raise_throwables' => true,
        'programmatic_pipeline' => true,
        'error_handler' => [
            'template_404' => 'error::404',
            'template_error' => 'error::error',
        ],
    ],
];
```

### Squiz.Classes.ClassFileName
_PSR-4:_ The class name must correspond to a file name ending in .php. The file name MUST match the case of the
terminating class name.

### Squiz.Classes.SelfMemberReference
The `self` keyword should be used instead of the current class name, and should not have spaces around `::`.

*Valid:*
```php
<?php
class Foo
{
    public static function bar()
    {
    }

    public static function baz()
    {
        self::bar();
    }
}
```

### Squiz.Commenting.DocCommentAlignment
The asterisks in a doc comment should align, and there should be one space between the asterisk and tag.

*Valid:*
```php
<?php
/**
 * These lines are aligned.
 *
 * @var array
 */
```

### Squiz.Commenting.FunctionComment
A function comment should be kept to a bare minimum. If the function and its arguments are self describing and have
proper typehinting, tags like `@param`, `@throws`, and `@return` can be omitted.

There should be no multiple spaces between the tag, type and description.

*Valid:*
```php
<?php
/**
 * This is a summary
 *
 * This is a description.
 *
 * @see http://example.com/my/bar URL to documentation.
 *
 * @param array $argument1 This is the description.
 * @param string $arg2 This is the description.
 * @param null|string $longerArgument3 This is the description.
 * @throws InvalidArgumentException on any invalid element.
 * @return null|int Indicates the number of items.
 */
```

### Squiz.Operators.ValidLogicalOperators
The `&&` and `||` operators must be used instead of `AND` and `OR`.

### Squiz.PHP.GlobalKeyword
The `global` keyword may not be used.

### Squiz.PHP.LowercasePHPFunctions
PHP function calls must be in lowercase.

### Squiz.PHP.NonExecutableCode
The code may not contain unreachable code.

### Squiz.Scope.StaticThisUsage
Static methods should not use `$this`.

*Valid: Using `self::` to access static variables.*
```php
<?php
class Foo
{
    public static function bar()
    {
        return self::$staticMember;
    }
}
```

*Invalid: Using `$this->` to access static variables.*
```php
<?php
class Foo
{
    public static function bar()
    {
        return $this->$staticMember;
    }
}
```

### Squiz.Strings.ConcatenationSpacing
Force whitespace before and after concatenation

### Squiz.Strings.DoubleQuoteUsage
#### Squiz.Strings.DoubleQuoteUsage.ContainsVar
Double quote strings may only be used if they contain variables. SQL queries containing single quotes are an exception
to the rule.

*Valid: Double quote strings are only used when it contains a variable.*
```php
<?php
$string = "Hello There\r\n";
$string = "Hello $there";
$string = 'Hello There';
$string = 'Hello'.' There'."\n";
$string = '\$var';
$query = "SELECT * FROM table WHERE name =''";
```

*Invalid: There are no variables inside double quote strings.*
```php
<?php
$string = "Hello There";
$string = "Hello"." There"."\n";
$string = "\$var";
```

### Squiz.Strings.EchoedStrings
Strings should not be enclosed in parentheses when being echoed.

*Valid: Using echo without parentheses.*
```php
<?php
echo 'Hello';
```

*Invalid: Using echo with parentheses.*
```php
<?php
echo('Hello');
```

### Squiz.WhiteSpace.CastSpacing
Type casts should not have whitespace inside the parentheses.

*Valid: No spaces.*
```php
<?php
$foo = (int) '42';
```

*Invalid: Whitespace used inside parentheses.*
```php
<?php
$foo = ( int ) '42';
```

### Squiz.WhiteSpace.LanguageConstructSpacing
The PHP constructs `echo`, `print`, `return`, `include`, `include_once`, `require`, `require_once`, and `new` should
have one space after them.

*Valid: echo statement with a single space after it.*
```php
<?php
echo 'hi';
```

*Invalid: echo statement with no space after it.*
```php
<?php
echo'hi';
```

### Squiz.WhiteSpace.LogicalOperatorSpacing
_PSR-12:_ There must be one space around logical operators.

### Squiz.WhiteSpace.ObjectOperatorSpacing
The object operator `->` should not have any spaces around it.

*Valid: No spaces around the object operator.*
```php
<?php
$foo->bar();
```

*Invalid: Whitespace surrounding the object operator.*
```php
<?php
$foo -> bar();
```

### Squiz.WhiteSpace.ObjectOperatorSpacing
There should be one space before and after an operators.

*Valid: One space around the operator.*
```php
<?php
$foo = 'bar';
```

*Invalid: Multiple or none spaces around the operator.*
```php
<?php
$foo  =  'bar';
$bar='foo';
```

### Squiz.WhiteSpace.SemicolonSpacing
Semicolons should not have spaces before them.

*Valid: No space before the semicolon.*
```php
<?php
echo 'hi';
```

*Valid: Invalid: Space before the semicolon.*
```php
<?php
echo 'hi' ;
```

### Squiz.WhiteSpace.SuperfluousWhitespace
#### Squiz.WhiteSpace.SuperfluousWhitespace.EmptyLines
The code should not contain superfluous whitespaces. e.g. multiple empty lines, trailing spaces, etc.



## WebimpressCodingStandard

### WebimpressCodingStandard.PHP.ImportInternalConstant
Import internal constants.

### WebimpressCodingStandard.PHP.ImportInternalConstant
Import internal functions.

### WebimpressCodingStandard.Classes.NoNullValues
Forbid null values for class properties

### WebimpressCodingStandard.Commenting.Placement
Comments at the end of the line, with at least single space.

### WebimpressCodingStandard.WhiteSpace.Namespace
Requires one space after namespace keyword.

### WebimpressCodingStandard.ControlStructures.BreakAndContinue
One space after break/continue with argument, remove redundant 1.

### WebimpressCodingStandard.ControlStructures.ContinueInSwitch
Forbid continue in switch; use break instead.

### WebimpressCodingStandard.NamingConventions.ValidVariableName
Require camelCase variable names.

### Generic.CodeAnalysis.ForLoopShouldBeWhileLoop
Detects for-loops that can be simplified to a while-loop.

### Generic.CodeAnalysis.UnconditionalIfStatement
Detects unconditional if- and elseif-statements.

### WebimpressCodingStandard.Classes.AlphabeticallySortedTraits
Sort traits alphabetically.

### WebimpressCodingStandard.Classes.TraitUsage
_PSR-12:_ Each individual Trait that is imported into a class MUST be included one-per-line.

### WebimpressCodingStandard.PHP.InstantiatingParenthesis
_PSR-12:_ When instantiating a new class, parenthesis MUST always be present even when there are no arguments passed to
the constructor.

### WebimpressCodingStandard.Namespaces.AlphabeticallySortedUses
Import statements should be alphabetically sorted.

*Valid: The import statements are sorted alphabetically.*
```php
<?php
use Example\A;
use Example\B;
use Vendor\Package\C;
```

*Invalid: The import statements are in a random order.*
```php
<?php
use Example\A;
use Vendor\Package\C;
use Example\B;
```

### WebimpressCodingStandard.Namespaces.ConstAndFunctionKeywords
Require lowercase function and const keywords in imports with one space after.

### WebimpressCodingStandard.Namespaces.UnusedUseStatement
Forbid unused use statements.
Forbid useless uses of the same namespace.

### WebimpressCodingStandard.Namespaces.UseDoesNotStartWithBackslash
_PSR-12:_ Import statements MUST never begin with a leading backslash as they must always be fully qualified.

### WebimpressCodingStandard.Formatting.UnnecessaryParentheses
Parentheses should not be used if they can be omitted.

*Valid: No parentheses used.*
```php
<?php
$x = $y !== null ? true : false;
$a = $b ? 1 : 0;
$c = $d ? 1 : 0;
$x = self::$a;
$x = Something\Anything::$a;
$x = self::$a::$b;
```

*Invalid: Unneeded parentheses.*
```php
<?php
$x = ($y !== null) ? true : false;
$a = ($b) ? 1 : 0;
$c = (   $d    ) ? 1 : 0;
$x = (self::$a);
$x = (Something\Anything::$a);
$x = (self::$a::$b);
```

### WebimpressCodingStandard.Formatting.ReturnType
The colon used with return type declarations MUST be surrounded with 1 space. This is different with the draft PSR-12
proposal and will be adjusted if needed when PSR-12 is accepted.
```php
public function foo($bar) : array;
```

### WebimpressCodingStandard.WhiteSpace.BraceBlankLine
- _PSR-12:_ The opening brace for the class MUST go on its own line; the closing brace for the class MUST go on the
  next line after the body.
- _PSR-12:_ Any closing brace MUST NOT be followed by any comment or statement on the same line.
- _PSR-12:_ Opening braces MUST be on their own line and MUST NOT be preceded or followed by a blank line.
- _PSR-12:_ Closing braces MUST be on their own line and MUST NOT be preceded by a blank line.
- The opening brace for functions should be on a new line with no blank lines surrounding it.

### SlevomatCodingStandard.Namespaces.UseSpacing
_PSR-12:_ Require empty newlines before and after uses

### WebimpressCodingStandard.Formatting.DoubleColon
Forbid whitespace around double colon operator.
