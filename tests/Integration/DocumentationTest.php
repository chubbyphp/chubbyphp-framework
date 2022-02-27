<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Integration;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class DocumentationTest extends TestCase
{
    public function testDocumentation(): void
    {
        $documentationFiles = $this->getDocumentationFiles(realpath(__DIR__.'/../../doc'));
        $documentationFiles[] = realpath(__DIR__.'/../../README.md');

        $phpBlockCount = 0;
        foreach ($documentationFiles as $documentationFile) {
            foreach ($this->getPhpBlocks($documentationFile) as $phpBlock) {
                try {
                    ++$phpBlockCount;
                    \PhpToken::tokenize($phpBlock, TOKEN_PARSE);
                } catch (\Error $e) {
                    self::fail(
                        sprintf(
                            'Cannot parse the following code in file "%s", error "%s" : "%s"',
                            $documentationFile,
                            $e->getMessage(),
                            $phpBlock
                        )
                    );
                }
            }
        }

        self::assertSame(41, $phpBlockCount);
    }

    private function getDocumentationFiles(string $path): array
    {
        $files = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
            $file = (string) $file;
            if ('md' === pathinfo($file, PATHINFO_EXTENSION)) {
                $files[] = $file;
            }
        }

        sort($files);

        return $files;
    }

    private function getPhpBlocks(string $path): array
    {
        $blocks = [];
        $blockIndex = 0;
        $isBlock = false;
        foreach (explode(PHP_EOL, file_get_contents($path)) as $line) {
            if ('```php' === $line) {
                $isBlock = true;
                $blocks[$blockIndex] = '';
            } elseif ('```' === $line && $isBlock) {
                $isBlock = false;
                ++$blockIndex;
            } elseif ($isBlock) {
                $blocks[$blockIndex] .= $line.PHP_EOL;
            }
        }

        return $blocks;
    }
}
