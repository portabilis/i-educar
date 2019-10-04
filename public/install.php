<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

set_time_limit(0);
ini_set('memory_limit','-1');

$rootDir = realpath(__DIR__ . '/../');

include $rootDir . '/vendor/autoload.php';

use Composer\Semver\Comparator;
use iEducar\Support\Installer;

$installer = new Installer($rootDir);
$command = $_GET['command'] ?? null;

if (!is_null($command)) {
    switch ($command) {
        case 'exec':
            $param = $_GET['param'] ?? '';
            $id = $_GET['id'] ?? 0;
            $id = (int) $id;
            $extra = $_GET['extra'] ?? '';
            $pid = $installer->exec($param, (int) $id, $extra);

            echo $pid;
            break;
        case 'consult':
            $pid = $_GET['pid'] ?? '';
            $id = $_GET['id'] ?? 0;
            $id = (int) $id;
            $status = $installer->consult($pid, $id);

            echo $status;
            break;
        default:
            echo 'NOK';
    }

    die();
}

function boolIcon(bool $bool): string
{
    if ($bool) {
        return '<i class="fas fa-check"></i>';
    } else {
        return '<i class="fas fa-times"></i>';
    }
}

$isInstalled = false;
$currIeducarVersion = $installer->composerData->version;
$latestIeducarVersion = $installer->getLatestRelease();
$isOld = Comparator::greaterThan(
    $latestIeducarVersion['version'],
    $currIeducarVersion
);
$minPhpVersion = str_replace(['~', '^'], '', $installer->composerData->require->php);
$phpVersionCheck = version_compare(PHP_VERSION, $minPhpVersion) >= 0;
$extensionsCheck = $installer->checkExtensions();
$extensionsReport = $installer->getExtensionsReport();
$envExists = file_exists($rootDir . '/.env');
$host = $_SERVER['HTTP_HOST'] ?? '';
$dbCheck = false;

if ($envExists) {
    Dotenv\Dotenv::create($rootDir)->load();
    $dbCheck = $installer->checkDatabaseConnection();
    $isInstalled = $installer->isInstalled();
}

$writablePaths = [
    $rootDir . '/.env',
    $rootDir . '/storage',
    $rootDir . '/bootstrap/cache',
];

$writablePathsCheck = $installer->checkWritablePaths($writablePaths);
$writablePathsReport = $installer->getWritablePathsReport($writablePaths);
$proceed = $phpVersionCheck
    && $extensionsCheck
    && $envExists
    && $dbCheck
    && $writablePathsCheck;

$user = posix_getpwuid(posix_getuid())['name'];
$group = posix_getgrgid(posix_getgid())['name'];
$needsUpdate = false;

if ($isInstalled) {
    $needsUpdate = $installer->needsUpdate();
}

?><!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Instalador do i-Educar</title>
        <meta name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet"
            href="https://use.fontawesome.com/releases/v5.5.0/css/all.css"
            crossorigin="anonymous">
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|PT+Mono">
        <link rel="stylesheet" href="css/install.css?version=<?php echo $currIeducarVersion ?>">
    </head>

    <body>
        <div class="container">
            <header>
                <p><img src="svg/logo_horizontal.svg" alt="i-Educar"></p>
                <h1>i-Educar</h1>
                <p>Bem-vindo ao instalador do i-Educar!<br>Siga os passos abaixo
                    para realizar a instalação.</p>
            </header>

            <?php if ($isInstalled): ?>
                <div class="module">
                    <h2><?= boolIcon(true) ?> Tudo ok</h2>
                    <p>O i-Educar ja está instalado!</p>
                    <p>A versão instalada é:
                        <strong><?= $currIeducarVersion ?></strong></p>

                    <?php if ($isOld): ?>
                        <p>A versão mais recente é:
                            <strong>
                                <?= $latestIeducarVersion['version'] ?>
                            </strong></p>
                        <p>
                            <a href="<?= $latestIeducarVersion['download'] ?>">
                                Clique aqui para fazer download da versão mais
                                recente
                            </a>
                        </p>
                    <?php else: ?>
                        <p>Você esta usando a versão mais recente.</p>
                        <p>
                            <a href="/intranet/index.php">
                                Clique aqui para acessar o i-Educar
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if ($needsUpdate): ?>
                        <p class="updating">
                            <i class="fas fa-spinner fa-spin"></i>
                            <strong>atualizando, aguarde...</strong>
                        </p>

                        <p><button id="update"><i class="fas fa-cogs"></i>
                            atualizar instalação</button></p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="module phpVersion">
                    <h2><?= boolIcon($phpVersionCheck) ?> Versão do PHP</h2>

                    <?php if ($phpVersionCheck): ?>
                        <p>A versão do PHP (<?= PHP_VERSION ?>) é igual ou
                            superior à versão requerida
                            (<?= $minPhpVersion ?>).</p>
                    <?php else: ?>
                        <p>A versão do PHP (<?= PHP_VERSION ?>) é menor que
                            a versão requerida (<?= $minPhpVersion ?>). É
                            necessário atualizar o PHP para prosseguir com a
                            instalação.</p>
                    <?php endif; ?>
                </div>

                <div class="module extensions">
                    <h2><?= boolIcon($extensionsCheck) ?> Extensões PHP</h2>

                    <p>O i-Educar necessita que as seguintes extensões estejam
                        presentes no sistema:</p>

                    <ul>
                        <?php foreach ($extensionsReport as $k => $v): ?>
                            <li><?= boolIcon($v) ?> <?= $k ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($extensionsCheck): ?>
                        <p>O sistema contém todas as extensões necessárias.</p>
                    <?php else: ?>
                        <p>Uma ou mais extensões não estão devidamente
                            instaladas. Verifique a lista acima e instale as
                            extensões de acordo com seu sistema operacional.</p>
                    <?php endif; ?>
                </div>

                <div class="module config">
                    <h2>
                        <?= boolIcon($envExists) ?>
                        Arquivo de configuração (.env)
                    </h2>

                    <?php if ($envExists): ?>
                        <p>O arquivo de configuração <code>.env</code> está
                            presente na raiz da aplicação.</p>
                    <?php else: ?>
                        <p>O arquivo de configuração <code>.env</code> não está
                            presente na raiz da aplicação. É necessário criá-lo.
                            Você pode executar o seguinte comando para isto:</p>

                        <pre>
cd <?= $rootDir . "\n" ?>
cp .env.example .env
vim .env # use seu editor de texto favorito
           # para configurar a aplicação
</pre>
                    <?php endif; ?>
                </div>

                <div class="module database">
                    <h2>
                        <?= boolIcon($dbCheck) ?>
                        Conexão com o banco de dados
                    </h2>

                    <?php if ($dbCheck): ?>
                        <p>O i-Educar consegue se comunicar com o banco de dados
                            corretamente.</p>
                    <?php else: ?>
                        <p>Não foi possível estabelecer comunicação com o banco
                            de dados. Verifique se os parâmetros abaixo estão
                            configurados corretamente no seu arquivo
                            <code>.env</code>:</p>

                        <pre>
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=ieducar
DB_USERNAME=ieducar
DB_PASSWORD=ieducar
</pre>
                    <?php endif; ?>
                </div>

                <div class="module permissions">
                    <h2>
                        <?= boolIcon($writablePathsCheck) ?>
                        Permissões de escrita
                    </h2>

                    <p>Os seguintes caminhos precisam ter permissão de
                        escrita:</p>

                    <ul>
                        <?php foreach ($writablePathsReport as $k => $v): ?>
                            <li><?= boolIcon($v) ?> <?= $k ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($writablePathsCheck): ?>
                        <p>Todos os caminhos estão devidamente configurados.</p>
                    <?php else: ?>
                        <p>Um ou mais caminhos precisam ser configurados para
                            escrita.</p>

                        <p>A forma mais segura de resolver este problema é
                            definindo o usuário e grupo dos diretórios do
                            projeto de acordo com o usuário e grupo responsáveis
                            pelos processos do PHP:</p>

                        <pre>
sudo chown -R <?= $user ?>:<?= $group ?> <?= $rootDir ?>
</pre>

                        <p>Uma outra forma (menos segura e não recomendada) é
                            liberando a permissão de escrita para qualquer
                            usuário ou grupo:</p>

                        <pre>
<?php foreach ($writablePaths as $path): ?>
chmod -R 777 <?= $path . "\n" ?>
<?php endforeach; ?>
</pre>
                    <?php endif; ?>
                </div>

                <div class="module install">
                    <?php if ($proceed): ?>
                        <h2>
                            <?= boolIcon(true) ?>
                            Tudo certo para instalação!
                        </h2>

                        <p>Para acessar o sistema após a instalação é necessário
                            fazer login com o usuário <code>admin</code>. Escola
                            uma senha <strong>segura</strong> no campo
                            abaixo:</p>

                        <div class="adminPassword">
                            <label for="password">Senha</label>
                            <input type="password" name="password"
                                id="password">
                        </div>

                        <p class="textCenter"><button id="install">
                            <i class="fas fa-download"></i>
                            instalar</button></p>
                    <?php else: ?>
                        <h2>
                            <?= boolIcon(false) ?>
                            Não é possível instalar ainda
                        </h2>

                        <p>Corrija os problemas descritos anteriormente para
                            poder instalar o i-Educar.</p>

                        <p>
                            <button onclick="document.location.reload(true);">
                                <i class="fas fa-sync"></i> recarregar
                            </button>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="module installing">
                    <h2><i class="fas fa-spinner fa-spin"></i> Instalando</h2>

                    <p id="taskDesc">Executando...</p>

                    <progress id="installProgress" max="0" value="0">
                </div>
            <?php endif; ?>
        </div>

        <script
            src="https://www.promisejs.org/polyfills/promise-7.0.4.min.js">
        </script>
        <script src="js/install.js?version=<?php echo $currIeducarVersion ?>"></script>
    </body>
</html>
