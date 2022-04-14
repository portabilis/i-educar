<?php

namespace iEducar\Support;

class Installer
{
    protected $rootDir;

    protected $extensions = [
        'bcmath',
        'ctype',
        'curl',
        'dom',
        'fileinfo',
        'gd',
        'iconv',
        'json',
        'libxml',
        'mbstring',
        'openssl',
        'PDO',
        'pgsql',
        'Phar',
        'SimpleXML',
        'tokenizer',
        'xml',
        'xmlreader',
        'xmlwriter',
        'zip',
        'zlib',
        'pcre',
    ];

    protected $githubApiEndpoint = 'https://api.github.com/repos/portabilis/i-educar/releases/latest';

    protected $commandsMap = [
        'key' => 'key:generate',
        'link' => 'legacy:link',
        'reports-link' => 'community:reports:link',
        'migrate' => 'migrate --force',
        'password' => 'admin:password',
        'reports' => 'reports:install --no-compile',
    ];

    public $composerData;

    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
        $composerJson = file_get_contents($this->rootDir . '/composer.json');
        $this->composerData = json_decode($composerJson);
    }

    public function checkExtensions(): bool
    {
        foreach ($this->extensions as $ext) {
            if (extension_loaded($ext)) {
                continue;
            }

            return false;
        }

        return true;
    }

    public function getExtensionsReport(): array
    {
        $report = [];

        foreach ($this->extensions as $ext) {
            $loaded = false;

            if (extension_loaded($ext)) {
                $loaded = true;
            }

            $report[$ext] = $loaded;
        }

        return $report;
    }

    public function checkWritablePaths(array $paths): bool
    {
        foreach ($paths as $path) {
            if (is_writable($path)) {
                continue;
            }

            return false;
        }

        return true;
    }

    public function getWritablePathsReport(array $paths): array
    {
        $report = [];

        foreach ($paths as $path) {
            $writable = false;

            if (is_writable($path)) {
                $writable = true;
            }

            $report[$path] = $writable;
        }

        return $report;
    }

    public function checkDatabaseConnection(): bool
    {
        try {
            $conn = $this->getConnection();

            if ($conn) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function exec(string $command, int $id, string $extra = ''): int
    {
        chdir($this->rootDir);

        if (empty($this->commandsMap[$command])) {
            return 0;
        }

        $tmpFile = 'install-' . $id . '.tmp';
        $command = $this->commandsMap[$command];

        if (!empty($extra)) {
            $command .= ' ' . $extra;
        }

        exec('touch ./storage/framework/cache/' . $tmpFile);

        $command = './scripts/install_helper "' . $command
            . '" ./storage/framework/cache/' . $tmpFile
            . ' > /dev/null 2>&1 & echo $!;';

        return (int) exec($command);
    }

    public function consult(int $pid, int $id): int
    {
        chdir($this->rootDir);

        $tmpFile = 'install-' . $id . '.tmp';
        $status = posix_getpgid($pid);

        if ((int) $status > 0) {
            return -1; // rodando
        } else {
            sleep(1);

            $status = file_get_contents('./storage/framework/cache/' . $tmpFile);

            if ($status == '') {
                return 1; // erro
            }

            return (int) $status; // 0 é sucesso
        }

        return 1; // erro
    }

    public function isInstalled(): bool
    {
        try {
            $conn = $this->getConnection();
            $query = $conn->prepare('SELECT 1 AS installed FROM portal.funcionario WHERE matricula = ?');
            $query->execute(['admin']);
            $result = $query->fetch(\PDO::FETCH_ASSOC);

            if (empty($result)) {
                return false;
            }

            return $result['installed'] === 1;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getConnection(): \PDO
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s',
            env('DB_HOST'),
            env('DB_PORT'),
            env('DB_DATABASE'),
            env('DB_USERNAME'),
            env('DB_PASSWORD')
        );

        return new \PDO($dsn);
    }

    public function getLatestRelease(): array
    {
        $opts = ['http' => [
            'method' => 'GET',
            'header' => ['User-Agent: PHP']
        ]];

        $context = stream_context_create($opts);
        $content = file_get_contents($this->githubApiEndpoint, false, $context);
        $json = json_decode($content);

        if (isset($json->name)) {
            return [
                'version' => $json->name,
                'download' => $json->html_url
            ];
        }

        return [
            'version' => '0',
            'download' => ''
        ];
    }

    public function needsUpdate(): bool
    {
        chdir($this->rootDir);
        exec('php artisan migrate:status', $output);

        $output = join("\n", $output);

        return strpos($output, '| No') !== false;
    }
}
