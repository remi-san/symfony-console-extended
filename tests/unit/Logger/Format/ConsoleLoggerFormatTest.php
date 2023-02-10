<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Tests\Logger\Format;

use Monolog\Logger;
use Monolog\LogRecord;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Monolog\Formatter\ConsoleFormatter;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\Console\Extended\Logger\Format\ConsoleLoggerFormat;
use Symfony\Component\Console\Extended\Tests\Helper\InMemoryOutput;

final class ConsoleLoggerFormatTest extends TestCase
{
    private const LOG_PATTERN                   = '\d{2}:\d{2}:\d{2} <.*>%s *<\/> <comment>\[%s\]<\/> <fg=cyan>%s<\/>';
    private const LOG_PATTERN_NORMAL_ADDITION   = '.*\[.*".*context.*".* => .*".*%s.*".*\]';
    private const LOG_PATTERN_EXTENDED_ADDITION = '.*\[.*".*custom.*".* => .*".*customvalue.*".*\].*';

    private const COMPLETE_LOG_PATTERN_SHORT    = '/^'.self::LOG_PATTERN.'[^\n\]]*\n$/';
    private const COMPLETE_LOG_PATTERN_NORMAL   = '/^'.self::LOG_PATTERN.'[^\n]*'.self::LOG_PATTERN_NORMAL_ADDITION.'[^\n\]]*\n$/';
    private const COMPLETE_LOG_PATTERN_EXTENDED = '/^'.self::LOG_PATTERN.'.*\n'.self::LOG_PATTERN_NORMAL_ADDITION.'.*\n'.self::LOG_PATTERN_EXTENDED_ADDITION.'\n$/s';

    private InMemoryOutput $output;

    protected function setUp(): void
    {
        $this->output  = new InMemoryOutput();
    }

    #[Test]
    public function it_logs_to_console_from_info_using_normal_formatter(): void
    {
        $logger = $this->buildLogger(ConsoleLoggerFormat::FORMAT_NORMAL);

        $logger->debug('info', ['context' => 'info']);
        self::assertCount(0, $this->output->lines);
        $this->output->reset();

        $logger->info('info message', ['context' => 'info context']);
        self::assertCount(1, $this->output->lines);
        self::assertMatchesRegularExpression(sprintf(self::COMPLETE_LOG_PATTERN_NORMAL, 'INFO', 'TEST', 'info message', 'info context'), $this->output->lines[0]);
        $this->output->reset();

        $logger->emergency('emergency message', ['context' => 'emergency context']);
        self::assertCount(1, $this->output->lines);
        self::assertMatchesRegularExpression(sprintf(self::COMPLETE_LOG_PATTERN_NORMAL, 'EMERGENCY', 'TEST', 'emergency message', 'emergency context'), $this->output->lines[0]);
        $this->output->reset();
    }

    #[Test]
    public function it_logs_to_console_from_info_using_short_formatter(): void
    {
        $logger = $this->buildLogger(ConsoleLoggerFormat::FORMAT_SHORT);

        $logger->debug('info', ['context' => 'info']);
        self::assertCount(0, $this->output->lines);
        $this->output->reset();

        $logger->info('info message', ['context' => 'info context']);
        self::assertCount(1, $this->output->lines);
        self::assertMatchesRegularExpression(sprintf(self::COMPLETE_LOG_PATTERN_SHORT, 'INFO', 'TEST', 'info message'), $this->output->lines[0]);
        $this->output->reset();

        $logger->emergency('emergency message', ['context' => 'emergency context']);
        self::assertCount(1, $this->output->lines);
        self::assertMatchesRegularExpression(sprintf(self::COMPLETE_LOG_PATTERN_SHORT, 'EMERGENCY', 'TEST', 'emergency message'), $this->output->lines[0]);
        $this->output->reset();
    }

    #[Test]
    public function it_logs_to_console_from_info_using_extended_formatter(): void
    {
        $logger = $this->buildLogger(ConsoleLoggerFormat::FORMAT_EXTENDED);

        $logger->debug('info', ['context' => 'info']);
        self::assertCount(0, $this->output->lines);
        $this->output->reset();

        $logger->info('info message', ['context' => 'info context']);
        self::assertCount(1, $this->output->lines);
        self::assertMatchesRegularExpression(sprintf(self::COMPLETE_LOG_PATTERN_EXTENDED, 'INFO', 'TEST', 'info message', 'info context'), $this->output->lines[0]);
        $this->output->reset();

        $logger->emergency('emergency message', ['context' => 'emergency context']);
        self::assertCount(1, $this->output->lines);
        self::assertMatchesRegularExpression(sprintf(self::COMPLETE_LOG_PATTERN_EXTENDED, 'EMERGENCY', 'TEST', 'emergency message', 'emergency context'), $this->output->lines[0]);
        $this->output->reset();
    }

    public function buildLogger(string $format): LoggerInterface
    {
        $logger  = new Logger('TEST');
        $handler = new ConsoleHandler($this->output);
        $handler->setFormatter(new ConsoleFormatter(ConsoleLoggerFormat::getConsoleFormatterOptions($format)));
        $logger->pushHandler($handler);

        $logger->pushProcessor(static function (LogRecord $record): LogRecord {
            $record->extra['custom'] = 'customvalue';

            return $record;
        });

        return $logger;
    }
}
