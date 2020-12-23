<?php

namespace App\Logging;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

trait Logger
{
    public function logEmergency($message, array $context = [])
    {
        $this->log('emergency', $message, $context);
    }

    public function logAlert($message, array $context = [])
    {
        $this->log('alert', $message, $context);
    }

    public function logError($message, array $context = [])
    {
        $this->log('error', $message, $context);
    }

    public function logNotice($message, array $context = [])
    {
        $this->log('notice', $message, $context);
    }

    public function logInfo($message, array $context = [])
    {
        $this->log('info', $message, $context);
    }

    public function logDebug($message, array $context = [])
    {
        $this->log('debug', $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        Log::log($level, sprintf(
            '%s - - [%s] "%s %s %s" - %s',
            Request::getClientIp(),
            Carbon::now(),
            Request::method(),
            Request::fullUrl(),
            Request::userAgent(),
            $message
        ), $context);
    }

    public static function logAny($level, $message, array $context = [])
    {
        Log::log($level, sprintf(
            '%s - - [%s] "%s %s %s" - %s',
            Request::getClientIp(),
            Carbon::now(),
            Request::method(),
            Request::fullUrl(),
            Request::userAgent(),
            $message
        ), $context);
    }

    public function logCritical($message, array $context = [])
    {
        $this->log('critical', $message, $context);
    }

    public function logWarning($message, array $context = [])
    {
        $this->log('warning', $message, $context);
    }
}
