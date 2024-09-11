<?php

namespace Silverorange\PackageChecker\Objects;

use Composer\Semver\Semver;

class Package
{
    public const VALIDITY_OK = 'OK';
    public const VALIDITY_FAIL = 'FAIL';
    public const VALIDITY_UNKNOWN = 'UNKNOWN';

    public string $name = '';

    public string $version = '';

    /**
     * @var array<string|string>
     */
    public array $require = [];

    /**
     * @var array<string|string>
     */
    public array $requireDev = [];

    public ?string $phpRequirement = null;

    public ?string $homepage = null;

    public ?string $source = null;

    public function getPackagistLink(): string
    {
        $version = str_starts_with($this->version, 'v')
            ? $this->version
            : 'v' . $this->version;

        return "https://packagist.org/packages/{$this->name}#{$version}";
    }

    /**
     * @return self::VALIDITY_*
     */
    public function isValidForTarget(string $phpVersion): string
    {
        if ($this->phpRequirement === null) {
            return self::VALIDITY_UNKNOWN;
        }

        return Semver::satisfies($phpVersion, $this->phpRequirement)
            ? self::VALIDITY_OK
            : self::VALIDITY_FAIL;
    }

    /**
     * @return array<string|string>
     */
    public function getLinks(): array
    {
        return [
            'Homepage'  => $this->homepage ?? '<fg=gray>none</>',
            'Source'    => $this->source ?? '<fg=gray>none</>',
            'Packagist' => $this->getPackagistLink(),
        ];
    }

    public static function fromRawData(object $package): self
    {
        $new = new self();
        $new->name = $package->name ?? '';
        $new->version = $package->version ?? '';
        $new->require = (array) ($package->require ?? []);
        $new->requireDev = (array) ($package->{'require-dev'} ?? []);
        $new->phpRequirement = $new->require['php'] ?? null;
        $new->homepage = $package->homepage ?? null;
        $new->source = $package->support->source ?? null;

        return $new;
    }
}
