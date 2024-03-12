<?php

namespace App\Contracts;

interface AssetServiceContract
{
    /**
     * AssetServiceContract constructor.
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
     */
    public function isAutomaticVersioning(): bool;

    /**
     * Get secure option.
     */
    public function getSecure(): ?bool;

    /**
     * Generate an asset path with version parameter for the application.
     */
    public function get(string $path, ?bool $secure = null): string;
}
