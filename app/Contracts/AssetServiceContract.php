<?php

namespace App\Contracts;

interface AssetServiceContract
{
    /**
     * AssetServiceContract constructor.
     *
     * @param string    $version
     * @param bool|null $secure
     * @param bool      $automatic_versioning
     */
    public function __construct(string $version, ?bool $secure = null, bool $automatic_versioning = false);

    /**
     * Get assets version number.
     *
     * @return ?string
     */
    public function getVersion(): ?string;

    /**
     * Get assets version number.
     *
     * @return bool
     */
    public function isAutomaticVersioning(): bool;

    /**
     * Get secure option.
     *
     * @return bool|null
     */
    public function getSecure(): ?bool;

    /**
     * Generate an asset path with version parameter for the application.
     *
     * @param string    $path
     * @param bool|null $secure
     *
     * @return string
     */
    public function get(string $path, bool $secure = null): string;
}
