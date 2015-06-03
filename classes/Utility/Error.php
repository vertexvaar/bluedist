<?php
namespace VerteXVaaR\BlueSprints\Utility;

/**
 * Class Error
 *
 * @package VerteXVaaR\BlueSprints\Utility
 */
class Error
{

    /**
     * @return void
     */
    public static function registerErrorHandler()
    {
        set_exception_handler([Error::class, 'handleException']);
        set_error_handler([Error::class, 'handleError'], E_ALL);
    }

    /**
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return bool
     */
    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        self::printErrorPage($errstr, $errno, $errfile, $errline, []);
        return false;
    }

    /**
     * @param string $message
     * @param int $code
     * @param string $file
     * @param int $line
     * @param array $callStack
     * @return void
     */
    protected static function printErrorPage($message, $code, $file, $line, array $callStack = [])
    {
        $additionalException = null;
        try {
            $context = Context::getCurrentContext();
        } catch (\Exception $additionalException) {
            $context = Context::CONTEXT_PRODUCTION;
        }
        if ($additionalException !== null) {
            echo '<h1>An error occured. Additionally an exception was thrown.</h1>';
        } else {
            if ($context === Context::CONTEXT_PRODUCTION) {
                echo '<h1>An error occured. Please contact your administator</h1>';
            } else {
                echo '<h1>An error occured.</h1>';
                echo '<p>Message: ' . $message . ' (Code: ' . $code . ')</p>';
                echo '<p>Error occured in: ' . $file . ' @ ' . $line . '</p>';
                self::printCallStack($callStack);
            }
        }
    }

    /**
     * @param array $callStack
     * @return void
     */
    protected static function printCallStack(array $callStack)
    {
        if (!empty($callStack)) {
            echo '<p>Call Stack:';
            echo '<ul>';
            foreach ($callStack as $trace) {
                echo '<li>';
                echo $trace['file'] . ' @ ' . $trace['line'] . '<br/>';
                echo $trace['class'] . $trace['type'] . $trace['function'] . '(';
                foreach ($trace['args'] as $argument) {
                    if (is_object($argument)) {
                        echo get_class($argument);
                    } elseif (is_array($argument)) {
                        $argumentArray = [];
                        foreach ($argument as $key => $value) {
                            $argumentArray[] = "'" . $key . "'" . ' => ' .
                                (is_object($value) ? get_class($value) : $value);
                        }
                        echo implode(', ', $argumentArray);
                    } elseif (is_string($argument)) {
                        echo $argument;
                    } else {
                        var_dump($argument);
                    }
                }
                echo ')';
            }
            echo '</ul>';
        }
    }

    /**
     * @param \Exception $exception
     * @return void
     */
    public static function handleException(\Exception $exception)
    {
        self::printErrorPage(
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTrace()
        );
    }
}
