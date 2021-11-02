<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Utility;

/**
 * Class Error
 */
class Error
{
    /**
     * @var bool
     */
    protected static $cssIncluded = false;

    /**
     *
     */
    protected static function includeCss()
    {
        if (false === self::$cssIncluded) {
            self::$cssIncluded = true;
            echo '<link href="/css/error.css" rel="stylesheet">';
        }
    }

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
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        self::includeCss();
        self::printErrorPage($errstr, $errno, $errfile, $errline, []);
        return true;
    }

    /**
     * @param string $message
     * @param int $code
     * @param string $file
     * @param int $line
     * @param array $callStack
     * @return void
     */
    protected static function printErrorPage(string $message, int $code, string $file, int $line, array $callStack = [])
    {
        self::includeCss();
        $additionalException = null;
        try {
            $context = (new Context())->getCurrentContext();
        } catch (\Exception $additionalException) {
            $context = Context::CONTEXT_PRODUCTION;
        }
        echo '<div class="c-error">';
        if ($additionalException !== null) {
            echo '<h1 class="c-error__title">An error occured. Additionally an exception was thrown.</h1>';
        } else {
            if ($context === Context::CONTEXT_PRODUCTION) {
                echo '<h1>An error occured. Please contact your administator</h1>';
            } else {
                echo '<h1>An error occured.</h1>';
                echo '<div class="c-error__report">';
                echo '<p>Message: </p><code class="c-error__message">' . $message . ' (Code: ' . $code . ')</code>';
                echo '<p>Error occured in:</p><code>' . $file . ' @ ' . $line . '</code>';
                echo '</div>';
                self::printCallStack($callStack);
                self::printHelp($code);
            }
        }
        echo '</div>';
    }

    /**
     * @param array $callStack
     * @return void
     */
    protected static function printCallStack(array $callStack)
    {
        if (!empty($callStack)) {
            echo '<h3>Call Stack:</h3>';
            echo '<ul class="c-error__call-stack">';
            foreach ($callStack as $trace) {
                echo '<li><code>';
                echo $trace['file'] . ' @ ' . $trace['line'] . '<br/>';
                echo ($trace['class'] ?? '') . ($trace['type'] ?? '') . $trace['function'] . '(';
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
                echo '</code></li>';
            }
            echo '</ul>';
        }
    }

    /**
     * @param int $code
     */
    protected static function printHelp(int $code)
    {
        $helpFile = __DIR__ . '/../../docs/exception/' . $code . '.md';
        if (file_exists($helpFile)) {
            $helpFileContents = file_get_contents($helpFile);
            echo '<h2>This might help you:</h2>';
            if (class_exists(\Parsedown::class)) {
                echo (new \Parsedown())->text($helpFileContents);
            } else {
                echo '<div class="c-error__md">';
                echo nl2br($helpFileContents);
                echo '</div>';
            }
        } else {
            echo '<span>No help file available</span>';
        }
    }

    /**
     * @param \Throwable $throwable
     */
    public static function handleException(\Throwable $throwable)
    {
        self::printErrorPage(
            $throwable->getMessage(),
            $throwable->getCode(),
            $throwable->getFile(),
            $throwable->getLine(),
            $throwable->getTrace()
        );
    }
}
