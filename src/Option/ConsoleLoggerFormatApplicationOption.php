<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Option;

use Monolog\Extended\Formatter\Factory\FormatterFactory;
use Monolog\Logger;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Extended\Logger\Format\ConsoleLoggerFormat;
use Symfony\Component\Console\Extended\Option\Configuration\ApplicationOptionConfiguration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final readonly class ConsoleLoggerFormatApplicationOption implements ApplicationOption
{
    private const OPTION_NAME     = 'logger-format';
    private const OPTION_SHORTCUT = 'l';
    private const DEFAULT_VALUE   = 'normal';
    private const ALLOWED_VALUES  = ConsoleLoggerFormat::ALLOWED_FORMATS;
    private const DESCRIPTION     = 'Tell the command how to format the output [ <info>short</> | <info>normal</> | <info>extended</> ].';

    private function __construct(
        private Logger $logger,
        private FormatterFactory $formatterFactory,
        private ApplicationOptionConfiguration $configuration
    ) {
    }

    /**
     * @param array<string>                             $optionShortcuts
     * @param array<mixed>|bool|float|int|string        $defaultValue
     * @param array<array<mixed>|bool|float|int|string> $allowedValues
     */
    public static function build(
        Logger $logger,
        FormatterFactory $formatterFactory,
        string $optionName = self::OPTION_NAME,
        array $optionShortcuts = [self::OPTION_SHORTCUT],
        array|bool|float|int|string $defaultValue = self::DEFAULT_VALUE,
        array $allowedValues = self::ALLOWED_VALUES,
        ?int $mode = InputOption::VALUE_REQUIRED,
        string $description = self::DESCRIPTION
    ): self {
        $config = new ApplicationOptionConfiguration(
            $optionName,
            $optionShortcuts,
            $mode,
            $description,
            $defaultValue,
            $allowedValues
        );

        return new self($logger, $formatterFactory, $config);
    }

    public function registerOption(Application $application): void
    {
        $this->configuration->register($application);
    }

    public function handleOptionValue(InputInterface $input, OutputInterface $output): void
    {
        /** @var string $logFormat */
        $logFormat = $this->configuration->getValue($input);

        $handler = new ConsoleHandler($output);
        $handler->setFormatter(
            $this->formatterFactory->create(
                ConsoleLoggerFormat::getConsoleFormatterOptions($logFormat)
            )
        );

        $this->logger->pushHandler($handler);
    }
}
