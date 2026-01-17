<?php

namespace VerteXVaaR\BlueFoundation\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Symfony\Component\Process\PhpSubprocess;
use Symfony\Component\Process\Process;

use function CoStack\Lib\concat_paths;
use function getcwd;
use function str_ends_with;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private Composer $composer;
    private IOInterface $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'post-autoload-dump' => 'postAutoloadDump',
        ];
    }

    public function postAutoloadDump(): void
    {
        $this->io->write('Compiling bootstrap');

        $binDir = $this->composer->getConfig()->get('bin-dir');
        $blueContainerBinary = concat_paths($binDir, 'bluefoundation');

        $cmd = [];
        $cmd[] = $blueContainerBinary;
        $cmd[] = 'compile';
        $process = new PhpSubprocess($cmd, $_ENV['PWD'] ?? getcwd(), $_ENV, 60 * 60);
        $process->run();
        if (!$process->isSuccessful()) {
            $this->io->writeError('Bootstrap compilation failed');
            $this->io->writeError($process->getOutput());
            $this->io->writeError($process->getErrorOutput());
            return;
        }
        $this->io->write('Compiled bootstrap');
        $this->io->write($process->getOutput());
    }
}
