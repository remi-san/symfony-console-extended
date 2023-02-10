<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Tests\Option;

use Monolog\Extended\Formatter\Factory\FormatterFactory;
use Monolog\Formatter\FormatterInterface;
use Monolog\Logger;
use Monolog\LogRecord;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Extended\Option\ConsoleLoggerFormatApplicationOption;
use Symfony\Component\Console\Extended\OptionAwareApplication;
use Symfony\Component\Console\Extended\Tests\Helper\InMemoryOutput;
use Symfony\Component\Console\Input\StringInput;

final class ConsoleLoggerFormatApplicationOptionTest extends TestCase
{
    protected function tearDown(): void
    {
        \Mockery::close();
    }

    #[Test]
    public function it_should_output_the_logged_error(): void
    {
        $logger           = new Logger('TEST');
        $formatterFactory = new TestFormatterFactory();

        $option           = ConsoleLoggerFormatApplicationOption::build($logger, $formatterFactory);
        $application      = new OptionAwareApplication('TEST', '1.0.0');
        $application->registerOption($option);

        $input  = new StringInput('');
        $output = new InMemoryOutput();

        self::assertEquals(0, $application->doRun($input, $output));
        $output->reset();

        $logger->error('error');
        self::assertCount(1, $output->lines);
        self::assertEquals('error', $output->lines[0]);
    }
}

final class TestFormatterFactory implements FormatterFactory
{
    public function create(mixed $options): FormatterInterface
    {
        return new TestFormatter();
    }
}

final class TestFormatter implements FormatterInterface
{
    public function format(LogRecord $record): string
    {
        return $record->message;
    }

    /**
     * @param array<LogRecord> $records
     *
     * @return array<string>
     */
    public function formatBatch(array $records): array
    {
        return array_map(
            fn (LogRecord $record): string => $record->message,
            $records
        );
    }
}
