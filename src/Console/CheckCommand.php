<?php

namespace Silverorange\PackageChecker\Console;

use Composer\Factory as ComposerFactory;
use Silverorange\PackageChecker\Objects\Package;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'check',
    description: 'Checks all the composer packages in a project for PHP compatibility.'
)]
class CheckCommand extends Command
{
    protected function configure(): void
    {
        $this->setHelp('Checks all the composer packages in a project for PHP compatibility.');

        $this->addOption(
            'targetVersion',
            't',
            InputOption::VALUE_REQUIRED,
            'Target version of PHP against which to compare packages',
            phpversion()
        );

        $this->addOption(
            'direct',
            'D',
            InputOption::VALUE_NONE,
            'Shows only packages that are directly required by the root package'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $direct = (bool) $input->getOption('direct');

        try {
            $packages = $this->getLoadedPackages($direct);
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $targetPHPVersion = $input->getOption('targetVersion');

        $results = [
            Package::VALIDITY_OK      => [],
            Package::VALIDITY_FAIL    => [],
            Package::VALIDITY_UNKNOWN => [],
        ];

        $table = $io->createTable();
        $table->setHeaders(['Package', 'Version', 'PHP']);

        foreach ($packages as $package) {
            $isValid = $package->isValidForTarget($targetPHPVersion);
            $results[$isValid][] = $package;

            $table->addRow([
                $this->getStatusIndicator($isValid) . ' ' . $package->name,
                $package->version,
                $package->phpRequirement,
            ]);
        }

        if ($io->isVerbose()) {
            $table->render();
        }

        if (!$io->isQuiet()) {
            $io->section('SUMMARY');
            $summary = array_map(
                fn ($v) => count($v),
                $results,
            );

            $io->createTable()
                ->setVertical()
                ->setHeaders(array_keys($summary))
                ->addRow($summary)
                ->render();
            $io->newLine();
        }

        if ($io->isVeryVerbose()) {
            if (count($results[Package::VALIDITY_FAIL]) > 0) {
                $io->section('FAILURES');
                $io->writeln('This packages do not meet the target PHP requirement:');
                $this->showDetailsTableArray($io, $results[Package::VALIDITY_FAIL]);
            }

            if (count($results[Package::VALIDITY_UNKNOWN]) > 0) {
                $io->section('UNKNOWN');
                $io->writeln('These packages do not have a PHP requirement, so may or may not be valid:');
                $this->showDetailsTableArray($io, $results[Package::VALIDITY_UNKNOWN]);
            }
        }

        return count($results[Package::VALIDITY_OK]) === count($packages)
            ? Command::SUCCESS
            : Command::FAILURE;
    }

    /**
     * @return array<Package>
     *
     * @throws \Exception
     */
    private function getLoadedPackages(bool $directOnly): array
    {
        $data = file_get_contents(
            $this->getProjectRoot() . '/composer.lock'
        );

        if ($data === false) {
            throw new \Exception('Could not read composer.lock.');
        }

        $json = json_decode($data);

        $packages = array_merge(
            $json->packages,
            $json->{'packages-dev'}
        );

        if ($directOnly) {
            $data = file_get_contents(
                $this->getProjectRoot() . '/composer.json'
            );
            if ($data === false) {
                throw new \Exception('Could not read composer.json to find direct packages.');
            }

            $json = json_decode($data);
            $directPackages = array_keys(array_merge(
                (array) $json->require,
                (array) $json->{'require-dev'}
            ));

            $packages = array_filter(
                $packages,
                fn ($p) => in_array($p->name, $directPackages)
            );
        }

        $result = array_map(
            fn ($p) => Package::fromRawData($p),
            $packages
        );

        usort($result, fn ($a, $b) => strcmp($a->name, $b->name));

        return $result;
    }

    private function getProjectRoot(): string
    {
        return realpath(
            dirname(ComposerFactory::getComposerFile())
        ) ?: '';
    }

    /**
     * @phpstan-param Package::VALIDITY_* $isValid
     */
    private function getStatusIndicator(string $isValid): string
    {
        return match ($isValid) {
            Package::VALIDITY_OK      => '<fg=green>✔</>',
            Package::VALIDITY_FAIL    => '<fg=red>✘</>',
            Package::VALIDITY_UNKNOWN => '<fg=yellow>?</>',
        };
    }

    /**
     * @param array<Package> $packages
     */
    private function showDetailsTableArray(SymfonyStyle $io, array $packages): void
    {
        $io->newLine();

        $table = $io->createTable()
            ->setVertical()
            ->setHeaders(['Package', 'Version', 'PHP', 'Links']);

        foreach ($packages as $package) {
            $packageLinks = $package->getLinks();
            $links = array_map(
                fn ($k, $v) => "{$k}: {$v}",
                array_keys($packageLinks),
                array_values($packageLinks),
            );
            $table->addRow([
                $package->name,
                $package->version,
                $package->phpRequirement ?? '<fg=gray>none</>',
                join("\n", $links),
            ]);
        }

        $table->render();

        $io->newLine();
    }
}
