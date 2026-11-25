<?php

declare(strict_types=1);

namespace KayStrobach\DyncssLess\Tests\Functional;

use KayStrobach\Dyncss\Configuration\BeRegistry;
use KayStrobach\DyncssLess\Parser\LessParser;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Runs with the bundled less.php and with wikimedia/less.php (LESS_PHP=wikimedia Build/Scripts/runTests.sh).
 */
class LessParserTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = ['install'];

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/dyncss',
        'typo3conf/ext/dyncss_less',
        __DIR__ . '/Fixtures/Extensions/dyncss_less_fixture_importer',
        __DIR__ . '/Fixtures/Extensions/dyncss_less_fixture_imported',
    ];

    protected function tearDown(): void
    {
        GeneralUtility::rmdir(Environment::getPublicPath() . '/typo3temp/assets/DynCss', true);
        GeneralUtility::rmdir(Environment::getPublicPath() . '/typo3temp/DynCss', true);
        parent::tearDown();
    }

    private function compile(string $file, array $overrides = []): string
    {
        $parser = new LessParser();
        $parser->setOverrides($overrides);
        $compiledFile = $parser->compileFile(ExtensionManagementUtility::extPath('dyncss_less_fixture_importer') . 'Resources/Private/Less/' . $file);
        self::assertFileExists($compiledFile);
        return (string)file_get_contents($compiledFile);
    }

    public function testParserIsRegisteredForLessFiles(): void
    {
        self::assertInstanceOf(LessParser::class, BeRegistry::get()->getFileHandler('less'));
    }

    public function testImportOfAnotherExtensionWrittenForTypo3confExtIsResolved(): void
    {
        $css = $this->compile('style.less');
        self::assertMatchesRegularExpression('/\.text\s*\{\s*color:\s*#123456;?\s*\}/', $css);
        self::assertStringNotContainsString('not found', $css);
    }

    public function testOverridesReplaceVariables(): void
    {
        $css = $this->compile('style.less', ['text-color' => '#abcdef']);
        self::assertStringContainsString('#abcdef', $css);
        self::assertStringNotContainsString('#123456', $css);
    }

    public function testErrorsEndUpAsCommentInTheStylesheet(): void
    {
        $css = $this->compile('broken.less');
        self::assertStringStartsWith('/* ', $css);
        self::assertStringContainsString('undefined-color', $css);
    }

    public function testStringIsCompiled(): void
    {
        $css = (new LessParser())->compile('@a: 2px; .b { width: (@a * 2); }');
        self::assertMatchesRegularExpression('/\.b\s*\{\s*width:\s*4px;?\s*\}/', $css);
    }
}
