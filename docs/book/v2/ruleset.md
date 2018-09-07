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
$foo = [...];
```

### Generic.PHP.BacktickOperator
The backtick operator may not be used for execution of shell commands.

### Generic.Classes.DuplicateClassName
Class and Interface names should be unique in a project. They should never be duplicated.

### Generic.CodeAnalysis.EmptyStatement
Control Structures must have at least one statement inside of the body.

#### Generic.CodeAnalysis.EmptyStatement.DetectedCatch
Catch blocks may be empty.
```php
try {
    // Try block
} catch (SomeThrowableType $e) {
}
```

### Generic.CodeAnalysis.UnnecessaryFinalModifier
Methods may not have the final declaration classes declared as final.

### Generic.CodeAnalysis.UselessOverridingMethod
Methods should not be defined that only call the parent method.

```php
// Valid: A method that extends functionality on a parent method.
final class Foo
{
    public function bar() : void
    {
        parent::bar();
        $this->doSomethingElse();
    }
}

// Invalid: An overriding method that only calls the parent.
final class Foo
{
    public function bar() : void
    {
       parent::bar();
    }
}
```

### Generic.Files.InlineHTML
Files that contain php code should only have php code and should not have any _"inline html"_.
```php
<?php
// Valid: A php file with only php code in it.
$foo = 'bar';
echo $foo . 'baz';
```

```php
<!-- Invalid: A php file with html in it outside of the php tags. -->
<em>some string here</em>
<?php
$foo = 'bar';
echo $foo . 'baz';
```

### Generic.Formatting.MultipleStatementAlignment
There should be one space on either side of an equals sign used to assign a value to a variable. In the case of a 
block of related assignments, more space may be inserted before the equal sign to promote readability.
```php
<?php

$shortVar        = (1 + 2);
$veryLongVarName = 'string';
$var             = foo($bar, $baz, $quux);
```

### Generic.Formatting.SpaceAfterCast
There must be exactly one space after a cast.

```php
// Valid: A cast operator is followed by one space.
$foo = (string) 1;

// Invalid: A cast operator is not followed by whitespace.
$foo = (string)1;
```

### Generic.Formatting.SpaceAfterNot
The not (`!`) operator must be followed by exactly one space.

### Generic.NamingConventions.ConstructorName
Constructors should be named `__construct`, not after the class.

```php
// Valid: The constructor is named __construct.
class Foo
{
    function <em>__construct</em>()
    {
    }
}

// Invalid: The old style class name constructor is used.
class Foo
{
    function <em>Foo</em>()
    {
    }
}
```

### Generic.PHP.CharacterBeforePHPOpeningTag
The opening php tag should be the first item in the file.
```php
<?php // Valid: A file starting with an opening php tag.
echo 'Foo';
```

```php
// Invalid: A file with content before the opening php tag.
<em>Beginning content</em>
<?php
echo 'Foo';
```

### Generic.PHP.DeprecatedFunctions
Deprecated functions should not be used.

### Generic.PHP.DisallowShortOpenTag
#### Generic.PHP.DisallowShortOpenTag.EchoFound
_PSR-1:_ PHP code must use the long `<?php ?>` tags or the short-echo `<?= ?>` tags; it must not use the other tag 
variations.

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

### Generic.Strings.UnnecessaryStringConcat
Strings should not be concatenated together, unless used in multiline for readability.
```php
// Valid: A string can be concatenated with an expression.
echo '5 + 2 = ' . (5 + 2);

// Invalid: Strings should not be concatenated together.
echo 'Hello' . ' ' . 'World';
```

## PEAR

### PEAR.Commenting.InlineComment
Comments may not start with `#`.



## SlevomatCodingStandard

### SlevomatCodingStandard.Arrays.TrailingArrayComma
All array values must be followed by a comma, including the final value. Commas after last element in an array make 
adding a new element easier and result in a cleaner versioning diff.

### SlevomatCodingStandard.Classes.ClassConstantVisibility
_PSR-12:_ Visibility MUST be declared on all constants if your project PHP minimum version supports constant visibilities 
(PHP 7.1 or later).

### SlevomatCodingStandard.Classes.TraitUseDeclaration
_PSR-12:_ Each individual Trait that is imported into a class MUST be included one-per-line.

### SlevomatCodingStandard.Classes.UnusedPrivateElements
A class should not have unused private constants, (or write-only) properties and methods.

### SlevomatCodingStandard.Commenting.ForbiddenAnnotations
Comments may not contain useless annotations: `@api`, `@author`, `@category`, `@created`, `@package`, `@subpackage`,
`@version`. Git commits provide accurate information.

### SlevomatCodingStandard.Commenting.EmptyComment
Comments may not be empty.

### SlevomatCodingStandard.Commenting.ForbiddenComments
To keep comments clean, specific words in comments may not be used: _private_, _protected_, _static_, _constructor_, 
_deconstructor_, _Created by_, _getter_, _setter_.

### SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration
Use valid format of inline phpDocs with `@var`.

### SlevomatCodingStandard.Commenting.RequireOneLinePropertyDocComment
Comments with single-line content should be written as one-liners.
```
/**
 * @var array
 */
private $foo;
```
Should be written as
```
/** @var array */
private $foo;
```

### SlevomatCodingStandard.ControlStructures.DisallowEqualOperators
Loose `==` and `!=` comparison operators should not be used. Use `===` and `!==` instead, they are much more secure 
and predictable.

### SlevomatCodingStandard.ControlStructures.EarlyExit
An early exit strategy should be used where possible to reduce the level of control structures.
```php
// Valid: Exit early.
function () : bool {
	if (! true) {
		return false;
	}
	// Do something
};

// Invalid: unneeded control structure
function () : bool {
	if (true) {
		// Do something
	} else {
		return false;
	}
};
```

### SlevomatCodingStandard.ControlStructures.LanguageConstructWithParentheses
Language constructs must be used without parentheses where possible.

```php
// Valid: Use language constructs without parentheses.
continue 1;
break 1;
echo 'a';
print 'b';
include 'file.php';
return 'foo';
yield [];
throw new Exception();
exit;

// Invalid: Language constructs with parentheses.
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

### SlevomatCodingStandard.ControlStructures.NewWithParentheses
_PSR-12:_ When instantiating a new class, parenthesis MUST always be present even when there are no arguments passed to 
the constructor.
```php
new Foo();
```

### SlevomatCodingStandard.ControlStructures.RequireNullCoalesceOperator
The null coalesce operator should be used when possible.
```php
$foo = $bar['id'] ?? '1';
```

### SlevomatCodingStandard.Exceptions.DeadCatch
Catch blocks should be reachable.
```php
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
```php
try {
	// ...
} catch (Throwable $e) {

}
```

### SlevomatCodingStandard.Functions.UnusedInheritedVariablePassedToClosure
Unused variables should not be passed to closures via `use`.

### SlevomatCodingStandard.Namespaces.AlphabeticallySortedUses
Import statements should be alphabetically sorted.

### SlevomatCodingStandard.Namespaces.DisallowGroupUse
Import statements should not be grouped.

### SlevomatCodingStandard.Namespaces.MultipleUsesPerLine
Each import statement should be on its own line.

### SlevomatCodingStandard.Namespaces.NamespaceSpacing
Require newlines around namespace declaration

### SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly
Absolute class name references, functions and constants should be imported.

### SlevomatCodingStandard.Namespaces.UnusedUses
Unused import statements are not allowed.

### SlevomatCodingStandard.Namespaces.UseDoesNotStartWithBackslash
_PSR-12:_ Import statements MUST never begin with a leading backslash as they must always be fully qualified.

### SlevomatCodingStandard.Namespaces.UseFromSameNamespace
Classes and function within the same namespace should not be imported.
```php
use Foo\Bar as Bar; // Same name as imported class
use Foo\Bar;        // Correct
```

### SlevomatCodingStandard.Namespaces.UseSpacing
_PSR-12:_ Require empty newlines before and after uses

### SlevomatCodingStandard.Namespaces.UselessAlias
Imports should not have an alias with the same name.

### SlevomatCodingStandard.Operators.RequireCombinedAssignmentOperator
Assignment operators (eg `+=`, `.=`) should be used when possible.

```php
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

### SlevomatCodingStandard.PHP.TypeCast
Short form of type keywords must be used. i.e. `bool` instead of `boolean`, `int` instead of `integer`, etc.
The `binary` and `unset` cast operators are not allowed.

### SlevomatCodingStandard.TypeHints.DeclareStrictTypes
_PSR-12:_ Declare statements MUST contain no spaces and MUST be exactly `declare(strict_types=1)`.
Each PHP file should have a strict type declaration at the top after the page level docblock.
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

### SlevomatCodingStandard.PHP.UselessParentheses
Unneeded parentheses should not be used.

```php
// Valid: No parentheses used.
$x = $y !== null ? true : false;
$a = $b ? 1 : 0;
$c = $d ? 1 : 0;
$x = self::$a;
$x = Something\Anything::$a;
$x = self::$a::$b;

// Invalid: Unneeded parentheses.
$x = ($y !== null) ? true : false;
$a = ($b) ? 1 : 0;
$c = (   $d    ) ? 1 : 0;
$x = (self::$a);
$x = (Something\Anything::$a);
$x = (self::$a::$b);
```

### SlevomatCodingStandard.PHP.UselessSemicolon
Semicolons `;` should not be used if they are not needed.
```php
// Valid: Semicolon used where needed.
foo();
class Whatever {
}

// Invalid: Unneeded semicolons used.
foo();;
class Whatever {
};
```


### SlevomatCodingStandard.TypeHints.LongTypeHints
Shorthand scalar typehint variants must be used in docblocks: `bool` instead of `boolean`, `int` instead of `integer`, 
etc. This is for consistency with native scalar typehints which also allow shorthand variants only.

### SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue
Nullable and optional arguments, which are marked as `= null`, must have the nullablity `?` symbol present.
```php
function foo(
	int $foo = null, // ? missing
	?int $bar = null // correct
) {
}
```

### SlevomatCodingStandard.TypeHints.ParameterTypeHintSpacing
_PSR-12:_ Method and function arguments must have one space between typehint and variable. Between the nullability sign 
and typehint may not be a space. 

### SlevomatCodingStandard.TypeHints.ReturnTypeHintSpacing
The colon used with return type declarations MUST be surrounded with 1 space. This is different with the draft PSR-12 
proposal and will be adjusted if needed when PSR-12 is accepted.
```php
public function foo($bar) : array;
```

### SlevomatCodingStandard.Types.EmptyLinesAroundTypeBraces
- _PSR-12:_ The opening brace for the class MUST go on its own line; the closing brace for the class MUST go on the 
  next line after the body.
- _PSR-12:_ Any closing brace MUST NOT be followed by any comment or statement on the same line.
- _PSR-12:_ Opening braces MUST be on their own line and MUST NOT be preceded or followed by a blank line.
- _PSR-12:_ Closing braces MUST be on their own line and MUST NOT be preceded by a blank line.

### SlevomatCodingStandard.Variables.UselessVariable
Variables should be returned directly instead of assigned to a variable which is not used.
```php
// Valid: The value is returned directly.
function () {
	return true;
};

// Invalid: An extra variable is used where its value could be returned directly.
function () {
	$a = true;
	return $a;
};
```



## Squiz

### Squiz.Arrays.ArrayBracketSpacing
Whitespace is not allowed around the opening bracket or before the closing bracket when referencing an array.
```php
$foo['bar'];
```

### Squiz.Arrays.ArrayDeclaration
All double arrow symbols must be aligned to one space after the longest array key.
```php
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

### Squiz.Classes.ClassFileName
_PSR-4:_ The class name must correspond to a file name ending in .php. The file name MUST match the case of the 
terminating class name.

### Squiz.Classes.SelfMemberReference
The self keyword should be used instead of the current class name, and should not have spaces around `::`.
```php
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
```php
/**
 * These lines are aligned.
 * 
 * @var array
 */
```

### Squiz.Commenting.FunctionComment
If a function throws any exceptions, they should be documented in a `@throws` tag.
```php
/** @throws Exception all the time */
function foo() : void
{
    throw new Exception('Danger!');
}
```
### Squiz.Functions.GlobalFunction
Global functions should not be used.

### Squiz.Operators.ValidLogicalOperators
The `&&` and `||` operators must be used instead of `&&` and `||`.

### Squiz.PHP.GlobalKeyword
The `global` keyword may not be used.

### Squiz.PHP.LowercasePHPFunctions
PHP function calls must be in lowercase.

### Squiz.PHP.NonExecutableCode
The code may not contain unreachable code.

### Squiz.Scope.StaticThisUsage
Static methods should not use $this.
```php
// Valid: Using self:: to access static variables.
class Foo
{
    public static function bar()
    {
        return self::$staticMember;
    }
}

// Invalid: Using $this-> to access static variables.
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
Double quote strings may only be used if it contains variables.
```php
// Valid: Double quote strings are only used when it contains a variable.
$string = "Hello There\r\n";
$string = "Hello $there";
$string = 'Hello There';
$string = 'Hello'.' There'."\n";
$string = '\$var';

// Invalid: There are no variables inside double quote strings.
$string = "Hello There";
$string = "Hello"." There"."\n";
$string = "\$var";
```

### Squiz.Strings.EchoedStrings
Simple strings should not be enclosed in parentheses when being echoed.
```php
// "Valid: Using echo without parentheses.
echo 'Hello';

// Invalid: Using echo with parentheses.
echo('Hello');
```

### Squiz.WhiteSpace.CastSpacing
Casts should not have whitespace inside the parentheses.
```php
// Valid: No spaces.
$foo = (int) '42';

// Invalid: Whitespace used inside parentheses.
$foo = ( int ) '42';
```

### Squiz.WhiteSpace.FunctionOpeningBraceSpace
The opening brace for functions should be on a new line with no blank lines surrounding it.
```php
// Valid: Opening brace is on a new line.
function foo() : int
{
    return 42;
}

// Invalid: Opening brace is on the same line as the function declaration and a blank line after the opening brace.
function foo() {

    return 42;
}
```

### Squiz.WhiteSpace.LanguageConstructSpacing
The php constructs `echo`, `print`, `return`, `include`, `include_once`, `require`, `require_once`, and `new` should 
have one space after them.

```php
// Valid: echo statement with a single space after it.
echo 'hi';

// Invalid: echo statement with no space after it.
echo'hi';
```

### Squiz.WhiteSpace.LogicalOperatorSpacing
_PSR-12:_ There must be one space around logical operators.

### Squiz.WhiteSpace.ObjectOperatorSpacing
The object operator (`->`) should not have any space around it.
```php
// Valid: No spaces around the object operator.
$foo->bar();

// Invalid: Whitespace surrounding the object operator.
$foo -> bar();
```

### Squiz.WhiteSpace.SemicolonSpacing
Semicolons should not have spaces before them.
```php
// Valid: No space before the semicolon.
echo 'hi';

// Valid: Invalid: Space before the semicolon.
echo 'hi' ;
```

### Squiz.WhiteSpace.SuperfluousWhitespace
#### Squiz.WhiteSpace.SuperfluousWhitespace.EmptyLines
The code should not superfluous whitespaces. e.g. multiple empty lines, trailing spaces, etc.
