<?php

namespace Silverorange\PackageChecker\Console;

use Composer\Factory as ComposerFactory;
use Composer\Semver\Semver;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @phpstan-type Requirements array<string|string>
 * @phpstan-type Package object{
 *     name:string,
 *     version:string,
 *     require:Requirements,
 *     requireDev?:Requirements,
 *     phpRequirement:string,
 *     homepage?:string,
 *     support?:string,
 *  }
 */
#[AsCommand(
    name: 'check',
    description: 'Checks all the composer packages in a project for PHP compatibility.'
)]
class CheckCommand extends Command
{
    public const VALIDITY_OK = 'OK';
    public const VALIDITY_FAIL = 'Failures';
    public const VALIDITY_UNKNOWN = 'Unknown';

    protected function configure(): void
    {
        $this->setHelp('Checks all the composer packages in a project for PHP compatibility.');

        $this->addOption(
            'phpVersion',
            'p',
            InputOption::VALUE_REQUIRED,
            'Version of PHP to check against',
            phpversion()
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Checking composer packages</info>');

        $packages = $this->getLoadedPackages();
        if ($packages === null) {
            $output->writeln('<error>No composer packages found.</error>');

            return Command::FAILURE;
        }

        $targetPHPVersion = $input->getOption('phpVersion');

        ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] %message%');

        $progressBar = new ProgressBar($output, count($packages));
        $progressBar->setFormat('custom');
        $progressBar->setMessage('');

        $results = [
            self::VALIDITY_OK      => [],
            self::VALIDITY_FAIL    => [],
            self::VALIDITY_UNKNOWN => [],
        ];

        foreach ($progressBar->iterate($packages) as $package) {
            $progressBar->setMessage($package->name);

            $isValid = $this->isPackageValidForTarget($package, $targetPHPVersion);
            $results[$isValid][] = $package;
            usleep(10_000);
        }

        $progressBar->setMessage('');
        $progressBar->finish();

        $output->writeln('');

        $result = Command::SUCCESS;

        $output->writeln(
            array_map(
                fn (string $k, array $v) => sprintf('%-10s: %d', $k, count($v)),
                array_keys($results),
                array_values($results)
            )
        );

        if (count($results[self::VALIDITY_FAIL]) > 0) {
            $output->writeln([
                '',
                "<error>FAILURES:</error> These packages have PHP requirements that do not meet the target of {$targetPHPVersion}:",
                '',
            ]);
            $this->listPackages($output, $results[self::VALIDITY_FAIL], true);
            $result = Command::FAILURE;
        }

        if (count($results[self::VALIDITY_UNKNOWN]) > 0) {
            $output->writeln([
                '',
                '<comment>UNKNOWN:</comment> These packages have unknown PHP requirements; check their source code:',
                '',
            ]);
            $this->listPackages($output, $results[self::VALIDITY_UNKNOWN], false);
            $result = Command::FAILURE;
        }

        return $result;
    }

    /**
     * @return ?array<Package>
     */
    private function getLoadedPackages(): ?array
    {
        $data = file_get_contents(
            $this->getProjectRoot() . '/composer.lock'
        );

        if ($data === false) {
            return null;
        }

        $json = json_decode($data);

        $packages = array_merge(
            $json->packages,
            $json->{'packages-dev'}
        );

        foreach ($packages as $package) {
            $package->require = (array) ($package->require ?? []);
            $package->requireDev = (array) ($package->{'require-dev'} ?? []);
            unset($package->{'require-dev'});
            $package->phpRequirement = $package->require['php'] ?? null;
        }

        return $packages;
    }

    private function getProjectRoot(): string
    {
        return realpath(
            dirname(ComposerFactory::getComposerFile())
        ) ?: '';
    }

    /**
     * @param Package $package
     *
     * @return self::VALIDITY_*
     */
    private function isPackageValidForTarget(object $package, string $phpVersion): string
    {
        if ($package->phpRequirement === null) {
            return self::VALIDITY_UNKNOWN;
        }

        return Semver::satisfies($phpVersion, $package->phpRequirement)
            ? self::VALIDITY_OK
            : self::VALIDITY_FAIL;
    }

    /**
     * @param array<Package> $packages
     */
    private function listPackages(OutputInterface $output, array $packages, bool $showRequirement): void
    {
        foreach ($packages as $package) {
            $requirement = $showRequirement
                ? "(\"php\": \"{$package->phpRequirement}\")"
                : '';

            $homepage = $package->homepage ?? '<fg=gray>not given</>';
            $source = $package->support->source ?? '<fg=gray>not given</>';
            $version = str_starts_with($package->version, 'v') ? $package->version : 'v' . $package->version;

            $output->writeln("<options=bold,underscore>{$package->name}:{$package->version}</> {$requirement}");
            $output->writeln("   - Homepage:  {$homepage}");
            $output->writeln("   - Source:    {$source}");
            $output->writeln("   - Packagist: https://packagist.org/packages/{$package->name}#{$version}");
        }
    }
}
