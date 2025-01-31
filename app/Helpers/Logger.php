<?php

namespace App\Utils;

class Logger
{
    private string $logFile;

    public function __construct(string $logFile)
    {
        $this->logFile = $logFile;
    }

    public function info(string $message, array $context = [])
    {
        $this->writeLog('INFO', $message, $context);
    }

    public function error(string $message, array $context = [])
    {
        $this->writeLog('ERROR', $message, $context);
    }

    private function writeLog(string $level, string $message, array $context = [])
    {
        $timestamp = date('d-m-Y H:i:s');
        $contextString = json_encode($context);
        $logMessage = "[$timestamp] [$level] $message $contextString" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

}
