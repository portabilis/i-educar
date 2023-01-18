<?php

namespace App\Services;

use App\Contracts\AssetServiceContract;

class AssetService implements AssetServiceContract
{
    protected ?string $version;
    protected ?bool $secure;
    protected bool $automaticVersioning;

    public function __construct(?string $version, ?bool $secure = null, bool $automaticVersioning = false)
    {
        $this->version = $version;
        $this->secure = $secure;
        $this->automaticVersioning = $automaticVersioning;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function isAutomaticVersioning(): bool
    {
        return $this->automaticVersioning;
    }

    public function getSecure(): ?bool
    {
        return $this->secure;
    }

    public function get(string $path, bool $secure = null): string
    {
        if ($secure === null) {
            $secure = $this->getSecure();
        }

        return asset($this->appendVersionToPath($path), $secure);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function appendVersionToPath(string $path): string
    {
        $dataUrl = explode('.', $path);
        if ($dataUrl !== false && ! in_array(last($dataUrl), ['css','js'], true)) {
            return $path;
        }

        if (!$this->automaticVersioning) {
            return ($this->version) ? ($path . '?v=' . $this->version) : ($path);
        }

        $pathUrl = parse_url($path);
        $version = $this->getFileVersion($path, $pathUrl);

        if ($version) {
            if (!isset($pathUrl['query']) || empty($pathUrl['query'])) {
                $path = sprintf('%s?v=%s', $pathUrl['path'], $version);
            } else {
                $path = sprintf('%s?%s&v=%s', $pathUrl['path'], $pathUrl['query'], $version);
            }
        }

        return $path;
    }

    protected function getFileVersion(string $path, array $pathUrl): string
    {
        if (preg_match('#^(//|http)#i', $path)) {
            return ($this->version ?: '');
        }

        //Apenas no caso de estar faltando / no início do caminho
        if (!preg_match('#^/#', $pathUrl['path'])) {
            $pathUrl['path'] = '/' . $pathUrl['path'];
        }

        $relPath = $this->findRealPath($pathUrl['path']);

        if ($relPath) {
            return (string) filemtime($relPath);
        }

        if ($this->version) {
            return $this->version;
        }

        return env('APP_ENV') === 'local' ? bin2hex(random_bytes(5)) : '';
    }

    /**
     * @param string $path
     *
     * @return null|string
     */
    public function findRealPath(string $path): ?string
    {
        $relPath = public_path($path);
        if (is_readable($relPath)) {
            return $relPath;
        }

        return null;
    }
}
