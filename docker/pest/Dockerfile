# Imagem dedicada aos testes de browser do Pest.
#
# Não substitui o container `php`, que continua sendo o ambiente da aplicação e
# da suíte tradicional. O que muda aqui é a base: a imagem oficial do Playwright
# já traz Node e os browsers instalados, e sobre ela entra o PHP (via PPA
# ondrej/php, já que o Ubuntu Noble só oferece o 8.3).
#
# Roda apenas headless, de propósito: sem X virtual e sem VNC, a imagem fica
# pequena e não há servidor nenhum de pé.
#
# O Xdebug entra desligado (`xdebug.mode=off`) e é ligado por chamada, pela
# variável XDEBUG_MODE — `coverage` para medir cobertura, `debug` para depurar
# um teste de browser. Ele mede o legado porque o servidor HTTP do Pest é
# in-process: o `ieducar/` carregado para atender o browser roda no mesmo
# processo do teste.
#
# O pcov fica ligado para o `pest --tia`: o TIA usa um driver de cobertura para
# saber quais arquivos cada teste executa e prefere o pcov, bem mais leve que o
# Xdebug. Ele só coleta quando um teste pede, então não pesa nas rodadas comuns.

ARG PHP_VERSION=8.5
ARG COMPOSER_VERSION=2.10.3
ARG PLAYWRIGHT_VERSION=1.62.1

FROM composer:${COMPOSER_VERSION} AS composer

FROM mcr.microsoft.com/playwright:v${PLAYWRIGHT_VERSION}-noble

ARG PHP_VERSION
ARG PLAYWRIGHT_VERSION

# Chave de assinatura do PPA ondrej/php.
ARG ONDREJ_PHP_KEY=14AA40EC0831756756D7F66C4F4EA0AAE5267A6C

# Só interessa durante o build; como ARG não fica no ambiente do container.
ARG DEBIAN_FRONTEND=noninteractive

# O container roda como root, herdado da imagem base — é como o Chromium da
# imagem do Playwright funciona.
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_PROCESS_TIMEOUT=900

# ---------------------------------------------------------------------------
# PHP
# ---------------------------------------------------------------------------

# O repositório é adicionado à mão em vez de pelo add-apt-repository: aquele
# consulta a api.launchpad.net, que costuma dar timeout, enquanto isto aqui só
# precisa de HTTPS para o apt.
#
# O ext-sockets, que o pest-plugin-browser exige, já é builtin no php8.5-cli.
RUN apt-get update && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        gnupg \
    && install -d -m 0755 /etc/apt/keyrings \
    && curl --proto "=https" --tlsv1.2 -fsSL "https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x${ONDREJ_PHP_KEY}" \
        | gpg --dearmor -o /etc/apt/keyrings/ondrej-php.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/ondrej-php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu noble main" \
        > /etc/apt/sources.list.d/ondrej-php.list \
    && apt-get update && apt-get install -y --no-install-recommends \
        "php${PHP_VERSION}-bcmath" \
        "php${PHP_VERSION}-cli" \
        "php${PHP_VERSION}-common" \
        "php${PHP_VERSION}-curl" \
        "php${PHP_VERSION}-gd" \
        "php${PHP_VERSION}-intl" \
        "php${PHP_VERSION}-mbstring" \
        "php${PHP_VERSION}-pcov" \
        "php${PHP_VERSION}-pgsql" \
        "php${PHP_VERSION}-readline" \
        "php${PHP_VERSION}-redis" \
        "php${PHP_VERSION}-xdebug" \
        "php${PHP_VERSION}-xml" \
        "php${PHP_VERSION}-zip" \
        unzip \
    && rm -rf /var/lib/apt/lists/* \
    #
    # O pacote do Xdebug entra em `develop`, que cobra um pedágio pequeno mas
    # permanente em toda rodada. O arquivo é numerado 99 para ser lido depois do
    # 20-xdebug.ini do pacote, e desliga a extensão por padrão; quem precisa dela
    # passa XDEBUG_MODE na chamada, que tem precedência sobre o ini.
    && echo 'xdebug.mode=off' > "/etc/php/${PHP_VERSION}/cli/conf.d/99-xdebug-off.ini" \
    #
    # O pcov olha só o código do projeto: sem o diretório explícito, ele adivinha
    # um só (app/, src/ ou lib/) e o TIA não veria o legado em ieducar/.
    && printf '%s\n' \
        'pcov.enabled=1' \
        'pcov.directory=/var/www/ieducar' \
        'pcov.exclude="~/(vendor|node_modules|storage)/~"' \
        > "/etc/php/${PHP_VERSION}/cli/conf.d/99-pcov.ini"

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# ---------------------------------------------------------------------------
# Playwright (npm)
# ---------------------------------------------------------------------------

# O plugin de browser do Pest procura o binário em
# ./node_modules/.bin/playwright, relativo ao diretório do package.json. Ele é
# instalado aqui, e não no package.json da raiz, para que um `npm install` no
# host não passe a baixar browsers. O volume nomeado do compose herda o
# conteúdo deste diretório na primeira subida.
#
# A versão precisa ser >= 1.59.1, exigida pelo plugin, e igual à da imagem, para
# reaproveitar os browsers já presentes em /ms-playwright.
RUN PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1 \
    npm install --no-save --ignore-scripts --prefix /var/www/ieducar "playwright@${PLAYWRIGHT_VERSION}"

WORKDIR /var/www/ieducar

# Sem comando o container só fica de pé, e os testes são disparados via
# `docker compose exec`.
CMD ["tail", "-f", "/dev/null"]
