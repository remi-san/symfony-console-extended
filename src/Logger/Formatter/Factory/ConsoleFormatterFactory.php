<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Logger\Formatter\Factory;

use Assert\Assert;
use Monolog\Extended\Formatter\Factory\FormatterFactory;
use Monolog\Formatter\FormatterInterface;
use Symfony\Bridge\Monolog\Formatter\ConsoleFormatter;

final class ConsoleFormatterFactory implements FormatterFactory
{
    public function create(mixed $options): FormatterInterface
    {
        Assert::that($options)->isArray();

        return new ConsoleFormatter($options);
    }
}
