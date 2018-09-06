# Ruleset

## Use PSR-2 coding standard as a base
*PSR2*

## Force array element indentation with 4 spaces
*Generic.Arrays.ArrayIndent*

## Forbid `array(...)`
*Generic.Arrays.DisallowLongArraySyntax*

## Forbid backtick operator
*Generic.PHP.BacktickOperator*

## Forbid duplicate classes
*Generic.Classes.DuplicateClassName*

## Forbid empty statements, but allow empty catch
*Generic.CodeAnalysis.EmptyStatement, Generic.CodeAnalysis.EmptyStatement.DetectedCatch*

## Forbid final methods in final classes
*Generic.CodeAnalysis.UnnecessaryFinalModifier*

## Forbid useless empty method overrides
*Generic.CodeAnalysis.UselessOverridingMethod*

## Forbid short open tag, but allow short echo tags
*Generic.PHP.DisallowShortOpenTag*

## Forbid inline HTML in PHP code
*Generic.Files.InlineHTML*

## Align corresponding assignment statement tokens
*Generic.Formatting.MultipleStatementAlignment*

## Force whitespace after a type cast
*Generic.Formatting.SpaceAfterCast*

## Force whitespace after `!`
*Generic.Formatting.SpaceAfterNot*

## Forbid PHP 4 constructors
*Generic.NamingConventions.ConstructorName*

## Forbid any content before opening tag
*Generic.PHP.CharacterBeforePHPOpeningTag*

## Forbid deprecated functions
*Generic.PHP.DeprecatedFunctions*

## Forbid alias functions, i.e. `sizeof()`, `delete()`
*Generic.PHP.ForbiddenFunctions*

Alias functions should not be used. This can't be fixed automatically and need to be done by hand.

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

## Force PHP 7 param and return types to be lowercased
*Generic.PHP.LowerCaseType*

## Forbid `php_sapi_name()` function, use `PHP_SAPI`
*Generic.PHP.SAPIUsage*

## Forbid inline string concatenation, unless used in multiline for readability
*Generic.Strings.UnnecessaryStringConcat*

## Forbid comments starting with `#`
*PEAR.Commenting.InlineComment*

## Disallow `else if` in favor of `elseif`
*PSR2.ControlStructures.ElseIfDeclaration.NotAllowed*

## Require comma after last element in multi-line array
*SlevomatCodingStandard.Arrays.TrailingArrayComma*

## Require presence of constant visibility
*SlevomatCodingStandard.Classes.ClassConstantVisibility*

## Forbid uses of multiple traits separated by comma
*SlevomatCodingStandard.Classes.TraitUseDeclaration*

## Require no spaces before trait use, between trait uses and one space after trait uses

## Forbid dead code
*SlevomatCodingStandard.Classes.UnusedPrivateElements*

## Forbid useless annotations
*SlevomatCodingStandard.Commenting.ForbiddenAnnotations*

Git commits provide accurate information for these forbidden annotations: @api, @author, @category, @created, @package, @subpackage, @version.

## Forbid empty comments
*SlevomatCodingStandard.Commenting.EmptyComment*

## Forbid useless comments
*SlevomatCodingStandard.Commenting.ForbiddenComments*

Forbidden comments words: private, protected, static, constructor, deconstructor, Created by, getter, setter.

## Report invalid format of inline phpDocs with `@var`
*SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration*

## Require comments with single line written as one-liners
*SlevomatCodingStandard.Commenting.RequireOneLinePropertyDocComment*

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

## Forbid weak comparisons
*SlevomatCodingStandard.ControlStructures.DisallowEqualOperators*

## Require usage of early exit
*SlevomatCodingStandard.ControlStructures.EarlyExit*

## Require language constructs without parentheses
*SlevomatCodingStandard.ControlStructures.LanguageConstructWithParentheses*

## Require new instances with parentheses
*SlevomatCodingStandard.ControlStructures.NewWithParentheses*

## Require usage of null coalesce operator when possible
*SlevomatCodingStandard.ControlStructures.RequireNullCoalesceOperator*

Use `$foo = $bar['id'] ?? '1';` where possbile.

## Forbid useless unreachable catch blocks
*SlevomatCodingStandard.Exceptions.DeadCatch*

## Require using Throwable instead of Exception
*SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly*

## Forbid unused variables passed to closures via `use`
*SlevomatCodingStandard.Functions.UnusedInheritedVariablePassedToClosure*

## Require use statements to be alphabetically sorted
*SlevomatCodingStandard.Namespaces.AlphabeticallySortedUses*

## Forbid fancy group uses
*SlevomatCodingStandard.Namespaces.DisallowGroupUse*

## Forbid multiple use statements on same line
*SlevomatCodingStandard.Namespaces.MultipleUsesPerLine*

## Require newlines around namespace declaration
*SlevomatCodingStandard.Namespaces.NamespaceSpacing*

## Forbid using absolute class name references (except global ones)
*SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly*

## Forbid unused use statements
*SlevomatCodingStandard.Namespaces.UnusedUses*

## Forbid superfluous leading backslash in use statements
*SlevomatCodingStandard.Namespaces.UseDoesNotStartWithBackslash*

## Forbid useless uses of the same namespace
*SlevomatCodingStandard.Namespaces.UseFromSameNamespace*

## Require empty newlines before and after uses
*SlevomatCodingStandard.Namespaces.UseSpacing*

## Forbid useless alias for classes, constants and functions
*SlevomatCodingStandard.Namespaces.UselessAlias*

## Require the usage of assignment operators, eg `+=`, `.=` when possible
*SlevomatCodingStandard.Operators.RequireCombinedAssignmentOperator*

## Forbid `list(...)` syntax, use `[...]` instead
*SlevomatCodingStandard.PHP.ShortList*

## Forbid use of longhand cast operators
*SlevomatCodingStandard.PHP.TypeCast*

## Require presence of `declare(strict_types=1)`
*SlevomatCodingStandard.TypeHints.DeclareStrictTypes*

## Forbid useless parentheses
*SlevomatCodingStandard.PHP.UselessParentheses*

## Forbid useless semicolon `;`
*SlevomatCodingStandard.PHP.UselessSemicolon*

## Require use of short versions of scalar types (i.e. int instead of integer)
*SlevomatCodingStandard.TypeHints.LongTypeHints*

## Require `?` when default value is `null`
*SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue*

Checks whether the nullablity ? symbol is present before each nullable and optional parameter 
(which are marked as = null):
```php
function foo(
	int $foo = null, // ? missing
	?int $bar = null // correct
) {

}
```

## Require one space between typehint and variable, require no space between nullability sign and typehint
*SlevomatCodingStandard.TypeHints.ParameterTypeHintSpacing*

## Require space around colon in return types
*SlevomatCodingStandard.TypeHints.ReturnTypeHintSpacing*

```php
public function foo($bar) : array;
```

## Forbid empty lines around type declarations
*SlevomatCodingStandard.Types.EmptyLinesAroundTypeBraces*

## Forbid useless variables
*SlevomatCodingStandard.Variables.UselessVariable*

## Forbid spaces around square brackets
*Squiz.Arrays.ArrayBracketSpacing*

## Force array declaration structure
*Squiz.Arrays.ArrayDeclaration*

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

## Forbid class being in a file with different name
*Squiz.Classes.ClassFileName*

## Force `self::` for self-reference, force lower-case self, forbid spaces around `::`
*Squiz.Classes.SelfMemberReference*

```php
$foo = self::fromPayload($payload);
```

## Force phpDoc alignment
*Squiz.Commenting.DocCommentAlignment*

## Force rules for function phpDoc
*Squiz.Commenting.FunctionComment*

## Forbid global functions
*Squiz.Functions.GlobalFunction*

## Forbid `AND` and `OR`, require `&&` and `||`
*Squiz.Operators.ValidLogicalOperators*

## Forbid `global`
*Squiz.PHP.GlobalKeyword*

## Require PHP function calls in lowercase
*Squiz.PHP.LowercasePHPFunctions*

## Forbid dead code
*Squiz.PHP.NonExecutableCode*

## Forbid `$this` inside static function
*Squiz.Scope.StaticThisUsage*

## Force whitespace before and after concatenation
*Squiz.Strings.ConcatenationSpacing*

## Forbid strings in `"` unless necessary
*Squiz.Strings.DoubleQuoteUsage, Squiz.Strings.DoubleQuoteUsage.ContainsVar*

## Forbid braces around string in `echo`
*Squiz.Strings.EchoedStrings*

## Forbid spaces in type casts
*Squiz.WhiteSpace.CastSpacing*

## Forbid blank line after function opening brace
*Squiz.WhiteSpace.FunctionOpeningBraceSpace*

## Require space after language constructs
*Squiz.WhiteSpace.LanguageConstructSpacing*

## Require space around logical operators
*Squiz.WhiteSpace.LogicalOperatorSpacing*

## Forbid spaces around `->` operator
*Squiz.WhiteSpace.ObjectOperatorSpacing*

## Forbid spaces before semicolon `;`
*Squiz.WhiteSpace.SemicolonSpacing*

## Forbid superfluous whitespaces
*Squiz.WhiteSpace.SuperfluousWhitespace, Squiz.WhiteSpace.SuperfluousWhitespace.EmptyLines*
