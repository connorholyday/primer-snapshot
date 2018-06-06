<?php namespace Rareloop\Primer\Snapshot\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Rareloop\Primer\Primer;

class Snapshot extends Command
{
    protected function configure()
    {
        $this->workingDirectory = __DIR__.'/../../../../';

        $this
            ->setName('snapshot')
            ->setDescription('Run snapshot tests')
            ->addOption(
                'port',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set the port Primer is using',
                8080
            )
            ->addOption(
                'update',
                null,
                InputOption::VALUE_NONE,
                'Update the snapshots'
            )
            ->addOption(
                'elements',
                null,
                InputOption::VALUE_NONE,
                'Include all elements'
            )
            ->addOption(
                'components',
                null,
                InputOption::VALUE_OPTIONAL,
                'Include all components',
                true
            )
            ->addOption(
                'templates',
                null,
                InputOption::VALUE_NONE,
                'Include all templates'
            )
            ->addOption(
                'url',
                null,
                InputOption::VALUE_REQUIRED,
                'Full ID of a single pattern (e.g. components/group/name)',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();

        $this->generatePatternfile($options);

        if ($options['update']) {
            $this->updateSnapshots($output);
        } else {
            $this->runSnapshots($output);
        }
    }

    protected function generatePatternFile(Array $options)
    {
        $data = [];

        if ($options['port']) {
            $data['port'] = $options['port'];
        }
        if ($options['templates']) {
            foreach (glob(Primer::$PATTERN_PATH.'/templates/*', GLOB_ONLYDIR) as $dir) {
                $path = $this->getCleanPath($dir, 2);

                $data['patterns'][] = [
                    'name' => $this->getFormattedPatternName($path),
                    'url' => $this->getFormattedPatternURL($path),
                ];
            }
        }
        if ($options['components'] !== 'false') {
            foreach (glob(Primer::$PATTERN_PATH.'/components/**/*', GLOB_ONLYDIR) as $dir) {
                $path = $this->getCleanPath($dir);

                $data['patterns'][] = [
                    'name' => $this->getFormattedPatternName($path),
                    'url' => $this->getFormattedPatternURL($path),
                ];
            }
        }
        if ($options['elements']) {
            foreach (glob(Primer::$PATTERN_PATH.'/elements/**/*', GLOB_ONLYDIR) as $dir) {
                $path = $this->getCleanPath($dir);

                $data['patterns'][] = [
                    'name' => $this->getFormattedPatternName($path),
                    'url' => $this->getFormattedPatternURL($path),
                ];
            }
        }
        if ($options['url']) {
            $data['patterns'][] = [
                'name' => $this->getFormattedPatternName($options['url']),
                'url' => $this->getFormattedPatternURL($options['url']),
            ];
        }

        file_put_contents($this->workingDirectory.'patternsToTest.json', json_encode($data));
    }

    protected function updateSnapshots(OutputInterface $output)
    {
        $output->writeln('<info>Updating snapshots</info>');
        system('npm run update-snapshots --prefix '.$this->workingDirectory);
    }

    protected function runSnapshots(OutputInterface $output)
    {
        $output->writeln('<info>Running snapshot tests</info>');
        system('npm run snapshot --prefix '.$this->workingDirectory);
    }

    protected function getCleanPath(String $path, $depth = 3)
    {
        $explodedPath = explode('/', $path);
        $requiredSegment = array_slice($explodedPath, -$depth, $depth);
        $cleanPath = join('/', $requiredSegment);

        return $cleanPath;
    }

    protected function getFormattedPatternName(String $pattern)
    {
        return str_replace('/', '-', $pattern);
    }

    protected function getFormattedPatternURL(String $pattern)
    {
        return $pattern.'?minimal';
    }
}
