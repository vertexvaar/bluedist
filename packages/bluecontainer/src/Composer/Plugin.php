<?php

namespace VerteXVaaR\BlueContainer\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Symfony\Component\Process\Process;

use function CoStack\Lib\concat_paths;

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
        $this->io->write('Generating container');

        $phpBinary = $_ENV['_'];

        $binDir = $this->composer->getConfig()->get('bin-dir');
        $blueContainerBinary = concat_paths($binDir, 'bluecontainer');

        $cmd = [];
        $cmd[] = $phpBinary;
        $cmd[] = $blueContainerBinary;
        $cmd[] = 'compile';
        $process = new Process($cmd, $_ENV['PWD'], $_ENV, null, 60 * 60);
        $process->run();
        if (!$process->isSuccessful()) {
            $this->io->writeError('Failed generating DI container');
            $this->io->writeError($process->getOutput());
            $this->io->writeError($process->getErrorOutput());
            return;
        }
        $this->io->write('Generated DI container');
        $this->io->write($process->getOutput());
    }
}
