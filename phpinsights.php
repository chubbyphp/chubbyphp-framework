<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh;
use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff;
use ObjectCalisthenics\Sniffs\Metrics\MaxNestingLevelSniff;
use ObjectCalisthenics\Sniffs\Metrics\MethodPerClassLimitSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UselessOverridingMethodSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\NoSilencedErrorsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Strings\UnnecessaryStringConcatSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousAbstractClassNamingSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousTraitNamingSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UnusedUsesSniff;

return [
    'preset' => 'default',
    'exclude' => [],
    'add' => [],
    'remove' => [
        ArrayIndentSniff::class,
        AssignmentInConditionSniff::class,
        DisallowYodaComparisonSniff::class,
        ForbiddenSetterSniff::class,
        NoSilencedErrorsSniff::class,
        SpaceAfterNotSniff::class,
        SuperfluousAbstractClassNamingSniff::class,
        SuperfluousExceptionNamingSniff::class,
        SuperfluousInterfaceNamingSniff::class,
        SuperfluousTraitNamingSniff::class,
        UnnecessaryStringConcatSniff::class,
        UselessOverridingMethodSniff::class, // cause visibility change on expections __construct,
    ],
    'config' => [
        CyclomaticComplexityIsHigh::class => [
            'maxComplexity' => 15,
        ],
        FunctionLengthSniff::class => [
            'maxLength' => 30,
        ],
        LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 120,
        ],
        MaxNestingLevelSniff::class => [
            'maxNestingLevel' => 5,
        ],
        MethodPerClassLimitSniff::class => [
            'maxCount' => 20,
        ],
        UnusedUsesSniff::class => [
            'searchAnnotations' => true,
        ],
    ],
];
