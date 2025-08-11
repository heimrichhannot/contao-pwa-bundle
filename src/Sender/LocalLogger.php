<?php

namespace HeimrichHannot\PwaBundle\Sender;

use Contao\CoreBundle\Monolog\ContaoContext;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
class LocalLogger implements LoggerInterface
{
    private Utils $utils;

    private array $errors = [];
    private array $warnings = [];
    private array $info = [];

    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }

    public function emergency($message, array $context = []): void
    {
        // TODO: Implement emergency() method.
    }

    public function alert($message, array $context = []): void
    {
        // TODO: Implement alert() method.
    }

    public function critical($message, array $context = []): void
    {
        // TODO: Implement critical() method.
    }

    public function error($message, array $context = []): void
    {
        $this->utils->container()->log(
            $message,
            $context['function'],
            ContaoContext::ERROR
        );
        $this->errors[] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function warning($message, array $context = []): void
    {
        $this->warnings[] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function notice($message, array $context = []): void
    {
        // TODO: Implement notice() method.
    }

    public function info($message, array $context = []): void
    {
        $this->info[] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function debug($message, array $context = []): void
    {
        // TODO: Implement debug() method.
    }

    public function log($level, $message, array $context = []): void
    {
        // TODO: Implement log() method.
    }

    public function getLastInfo()
    {
        return $this->info[count($this->info) - 1] ?? null;
    }

    public function getLastError()
    {
        return $this->errors[count($this->errors) - 1] ?? null;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}