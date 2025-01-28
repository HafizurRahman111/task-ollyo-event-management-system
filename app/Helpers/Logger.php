<?php

namespace App\Helpers;

class Logger
{
    private $logFile;

    public function __construct($logFile)
    {
        $this->logFile = $logFile;
    }

    public function log($message)
    {
        $date = new \DateTime(); // Use the global DateTime class
        $formattedDate = $date->format('d-m-Y H:i:s');
        $logMessage = "[$formattedDate] $message" . PHP_EOL;

        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}
