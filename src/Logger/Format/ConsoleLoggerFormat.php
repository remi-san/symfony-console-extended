<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Logger\Format;

final class ConsoleLoggerFormat
{
    public const FORMAT_SHORT    = 'short';
    public const FORMAT_NORMAL   = 'normal';
    public const FORMAT_EXTENDED = 'extended';
    public const ALLOWED_FORMATS = [self::FORMAT_NORMAL, self::FORMAT_SHORT, self::FORMAT_EXTENDED];

    private const LOG_FORMAT_SHORT    = '%datetime% %start_tag%%level_name%%end_tag% <comment>[%channel%]</> <fg=cyan>%message%</>';
    private const LOG_FORMAT_NORMAL   = self::LOG_FORMAT_SHORT.'%context%';
    private const LOG_FORMAT_EXTENDED = self::LOG_FORMAT_NORMAL.'%extra%';

    /**
     * @return array{format: string, multiline: bool}
     */
    public static function getConsoleFormatterOptions(string $loggerFormatName): array
    {
        $loggerFormat = match ($loggerFormatName) {
            self::FORMAT_SHORT    => self::LOG_FORMAT_SHORT,
            self::FORMAT_EXTENDED => self::LOG_FORMAT_EXTENDED,
            default               => self::LOG_FORMAT_NORMAL,
        };

        $loggerMultiline = match ($loggerFormatName) {
            self::FORMAT_EXTENDED => true,
            default               => false,
        };

        return ['format' => $loggerFormat.\PHP_EOL, 'multiline' => $loggerMultiline];
    }
}
