<?php


namespace Glpi\Console\Build;

use Html;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CompileScssCommand extends Command
{
    /**
     * Error code returned if unable to write compiled CSS.
     *
     * @var integer
     */
    public const ERROR_UNABLE_TO_WRITE_COMPILED_FILE = 1;

    protected function configure()
    {
        parent::configure();

        $this->setName('build:compile_scss');
        $this->setDescription('Compile SCSS file.');

        $this->addOption(
            'file',
            'f',
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'File to compile (compile all SCSS files by default)'
        );

        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Simulate compilation without actually save compiled CSS files'
        );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {

        $compile_directory = Html::getScssCompileDir();

        if (!@is_dir($compile_directory) && !@mkdir($compile_directory)) {
            throw new \RuntimeException(
                sprintf(
                    'Destination directory "%s" cannot be accessed.',
                    $compile_directory
                )
            );
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $files = $input->getOption('file');
        $dry_run = $input->getOption('dry-run');

        if (empty($files)) {
            $root_path = realpath(GLPI_ROOT);

            $css_dir_iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($root_path . '/css'),
                RecursiveIteratorIterator::SELF_FIRST
            );
            /** @var \SplFileInfo $file */
            foreach ($css_dir_iterator as $file) {
                if (
                    !$file->isReadable() || !$file->isFile() || $file->getExtension() !== 'scss'
                     || preg_match('/^' . preg_quote(GLPI_ROOT . '/css/lib/', '/') . '/', $file->getPath()) === 1
                     || preg_match('/^_/', $file->getBasename()) === 1
                ) {
                    continue;
                }

                $files[] = str_replace($root_path . '/', '', dirname($file->getRealPath()))
                . '/'
                . preg_replace('/^_?(.*)\.scss$/', '$1', $file->getBasename());
            }
        }

        foreach ($files as $file) {
            $output->writeln(
                '<comment>' . sprintf('Processing "%s".', $file) . '</comment>',
                OutputInterface::VERBOSITY_VERBOSE
            );

            $compiled_path = Html::getScssCompilePath($file);
            $css = Html::compileScss(
                [
                    'file'    => $file,
                    'nocache' => true,
                ]
            );

            if ($dry_run) {
                $message = sprintf('"%s" compiled successfully.', $file);
                $output->writeln(
                    '<info>' . $message . '</info>',
                    OutputInterface::VERBOSITY_NORMAL
                );
            } elseif (strlen($css) === @file_put_contents($compiled_path, $css)) {
                $message = sprintf('"%s" compiled successfully in "%s".', $file, $compiled_path);
                $output->writeln(
                    '<info>' . $message . '</info>',
                    OutputInterface::VERBOSITY_NORMAL
                );
            } else {
                $message = sprintf('Unable to write compiled CSS in "%s".', $compiled_path);
                $output->writeln(
                    '<error>' . $message . '</error>',
                    OutputInterface::VERBOSITY_QUIET
                );
                return self::ERROR_UNABLE_TO_WRITE_COMPILED_FILE;
            }
        }

        return 0; // Success
    }
}
