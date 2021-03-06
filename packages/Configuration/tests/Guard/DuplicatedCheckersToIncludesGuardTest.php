<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Tests\Guard;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\Exception\Guard\DuplicatedCheckersLoadedException;
use Symplify\EasyCodingStandard\Configuration\Guard\DuplicatedCheckersToIncludesGuard;

final class DuplicatedCheckersToIncludesGuardTest extends TestCase
{
    /**
     * @var DuplicatedCheckersToIncludesGuard
     */
    private $duplicatedCheckersToIncludesGuard;

    protected function setUp(): void
    {
        $this->duplicatedCheckersToIncludesGuard = new DuplicatedCheckersToIncludesGuard(
            new CheckerConfigurationNormalizer()
        );
    }

    /**
     * @dataProvider provideConflictingConfigFiles()
     * @param string[] $conflictingCheckers
     */
    public function testConflicting(string $configFile, array $conflictingCheckers): void
    {
        $this->expectException(DuplicatedCheckersLoadedException::class);

        $this->expectExceptionMessage(sprintf(
            'Duplicated checkers found in "%s" config: "%s"',
            $configFile,
            implode('", "', $conflictingCheckers)
        ));

        $this->duplicatedCheckersToIncludesGuard->processConfigFile($configFile);
    }

    /**
     * @return string[][]|string[][][]
     */
    public function provideConflictingConfigFiles(): array
    {
        return [
           [__DIR__ . '/DuplicatedCheckersToIncludesGuardSource/config-with-conflict.1.neon', ['SomeChecker']],
           [__DIR__ . '/DuplicatedCheckersToIncludesGuardSource/config-with-conflict.2.neon', ['SomeOtherChecker']],
           [__DIR__ . '/DuplicatedCheckersToIncludesGuardSource/config-with-conflict.3.neon', ['SomeOtherChecker']],
       ];
    }

    /**
     * @dataProvider provideValidConfigFiles()
     * @doesNotPerformAssertions
     */
    public function testValid(string $configFile): void
    {
        $this->duplicatedCheckersToIncludesGuard->processConfigFile($configFile);
    }

    /**
     * @return string[][]
     */
    public function provideValidConfigFiles(): array
    {
        return [
            [__DIR__ . '/DuplicatedCheckersToIncludesGuardSource/valid-config.1.neon'],
            [__DIR__ . '/DuplicatedCheckersToIncludesGuardSource/valid-config.2.neon'],
        ];
    }
}
