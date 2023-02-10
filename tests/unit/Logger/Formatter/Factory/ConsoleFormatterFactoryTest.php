<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Tests\Logger\Formatter\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Monolog\Formatter\ConsoleFormatter;
use Symfony\Component\Console\Extended\Logger\Formatter\Factory\ConsoleFormatterFactory;

final class ConsoleFormatterFactoryTest extends TestCase
{
    #[Test]
    public function it_should_create_a_console_formatter(): void
    {
        $factory = new ConsoleFormatterFactory();

        $formatter = $factory->create([]);

        self::assertInstanceOf(ConsoleFormatter::class, $formatter);
    }
}
