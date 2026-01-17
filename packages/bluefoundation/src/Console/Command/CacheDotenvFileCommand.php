<?php

namespace VerteXVaaR\BlueFoundation\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Dotenv\Exception\FormatException;
use Symfony\Component\Dotenv\Exception\FormatExceptionContext;

use function CoStack\Lib\concat_paths;
use function file_get_contents;
use function getenv;
use function putenv;
use function sprintf;
use function str_starts_with;
use function substr;
use function var_export;

use const PHP_EOL;

class CacheDotenvFileCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:dotenv-file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootPath = getenv('VXVR_BS_ROOT');

        $dotenvFile = concat_paths($rootPath, '.env');

        if (file_exists($dotenvFile)) {
            $dotenv = new Dotenv();

            $data = file_get_contents($dotenvFile);

            if ("\xEF\xBB\xBF" === substr($data, 0, 3)) {
                throw new FormatException(
                    'Loading files starting with a byte-order-mark (BOM) is not supported.',
                    new FormatExceptionContext($data, $dotenvFile, 1, 0),
                );
            }

            $values = $dotenv->parse($data, $dotenvFile);

            $code = '<?php' . PHP_EOL;

            foreach ($values as $name => $value) {
                $notHttpName = !str_starts_with($name, 'HTTP_');
                putenv("$name=$value");
                $value = match ($value) {
                    'true' => true,
                    'false' => false,
                    default => $value,
                };
                $code .= sprintf('$_ENV[\'%s\'] = %s;', $name, var_export($value, true)) . PHP_EOL;
                if ($notHttpName) {
                    $code .= sprintf('$_SERVER[\'%s\'] = %s;', $name, var_export($value, true)) . PHP_EOL;
                }
                $code .= PHP_EOL;
            }

            file_put_contents(concat_paths($rootPath, 'dotenv.php'), $code);
        }

        return Command::SUCCESS;
    }
}
