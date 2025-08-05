<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\Manifest;

class Manifest implements \JsonSerializable
{
    public const DISPLAY_VALUES = [
        'standalone',
        'fullscreen',
        'minimal-ui',
        'browser',
    ];
    public const ICONS_VALUE = [
        "src" => "",
        "type" => "",
        "sizes" => "",
    ];
    public const DIR_VALUES = ["ltr", "rtl", "auto"];
    public const ORIENTATION_VALUES = [
        'any',
        'natural',
        'landscape',
        'landscape-primary',
        'landscape-secondary',
        'portrait',
        'portrait-primary',
        'portrait-secondary',
    ];
    public const RELATED_APPLICATIONS_VALUES = [
        'platform',
        'url',
        'id',
        'min_version',
        'sequence',
    ];

    public ?string $name;
    public ?string $short_name;
    public ?string $description;
    public ?string $dir;
    public ?string $lang;
    public ?string $orientation;
    public bool $prefer_related_applications = false;
    private array $related_applications = [];
    public ?string $start_url;
    public ?string $scope;
    public ?ManifestIcon $icons;
    /** @var string Valid CSS color. RGB-Colors with # at the beginning */
    public ?string $background_color;
    /** @var string Valid CSS color. RGB-Colors with # at the beginning */
    public ?string $theme_color;
    public ?string $display;

    /**
     * Specify data which should be serialized to JSON
     *
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * which is a value of any type other than a resource.
     * @throws \ReflectionException
     */
    public function jsonSerialize(): ?array
    {
        $reflectionClass = new \ReflectionClass($this);
        $classProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);

        $manifestProperties = [];
        foreach ($classProperties as $property)
        {
            if ($this->{$property->getName()})
            {
                $manifestProperties[$property->getName()] = $this->{$property->getName()};
            }
        }

        $classMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($classMethods as $method)
        {
            if (!\str_starts_with($method->getName(), 'get')) {
                continue;
            }

            if ($method->getNumberOfParameters() > 0) {
                continue;
            }

            $value = $this->{$method->getName()}();

            if (!\is_null($value)) {
                $manifestProperties[\lcfirst(\substr($method->getName(), 3))] = $value;
            }
        }

        if ($this->icons)
        {
            $manifestProperties['icons'] = $this->icons->toArray();
        }

        return $manifestProperties;
    }

    public function getRelatedApplications(): ?array
    {
        return $this->related_applications;
    }

    /**
     * @param string $plattform
     * @param string|null $url
     * @param null|string $id
     * @param null|string $min_version
     * @param array|null $fingerprints
     * @return bool
     */
    public function addRelatedApplication(
        string  $plattform,
        string  $url = null,
        ?string $id = null,
        ?string $min_version = null,
        ?array  $fingerprints = null
    ): bool {
        if (empty($plattform)) {
            return false;
        }

        if (empty($url) && empty($id)) {
            return false;
        }

        $application = [];
        $application['plattform'] = $plattform;

        if ($url) $application['url'] = $url;
        if ($id) $application['id'] = $id;
        if ($min_version) $application['min_version'] = $min_version;
        if ($fingerprints) $application['fingerprints'] = $fingerprints;

        $this->related_applications[] = $application;

        return true;
    }
}