<?php

namespace App\Http\Controllers;

use Closure;
use clsBase;
use CoreExt_Exception_FileNotFoundException;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class LegacyController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    private static array $resolvers = [];

    /**
     * LegacyController constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function resolver(Closure $resolver): void
    {
        static::$resolvers[] = $resolver;
    }

    public static function resolve(string $uri, string $path): string
    {
        foreach (static::$resolvers as $resolver) {
            $path = $resolver($uri) ?? $path;
        }

        return $path;
    }

    /**
     * Return i-Educar legacy code path.
     *
     * @return string
     */
    private function getLegacyPath()
    {
        return base_path(config('legacy.path'));
    }

    /**
     * Define which errors and exceptions are shown.
     *
     * @return void
     */
    private function configureErrorsAndExceptions()
    {
        if (config('legacy.display_errors')) {
            return;
        }

        ini_set('display_errors', 'off');

        error_reporting(0);

        restore_error_handler();

        restore_exception_handler();
    }

    /**
     * Load legacy route file, if not found, throw a HttpException with HTTP
     * error code 404 Not Found.
     *
     * @param string $filename
     * @return void
     *
     * @throws NotFoundHttpException
     * @throws Exception
     */
    private function loadLegacyFile($filename)
    {
        $legacyFile = static::resolve($filename, $this->getLegacyPath() . '/' . $filename);

        if (file_exists($legacyFile) === false) {
            throw new NotFoundHttpException('Legacy file not found.');
        }

        $this->loadFileOrAbort($legacyFile);
    }

    /**
     * Load a file or abort the application.
     *
     * @param string $filename
     * @return void
     *
     * @throws Exception
     */
    private function loadFileOrAbort($filename)
    {
        try {
            $viewToRender = require $filename;

            if (is_object($viewToRender)) {
                $class = $this->processProperty($viewToRender);

                $class->addForm($viewToRender);
                $class->MakeAll();
            }

            return;
        } catch (HttpResponseException $exception) {
            // Para evitar encerrar a aplicação com `die` ou `exit`, é lançada
            // uma exceção do tipo `HttpResponseException` com uma `Response`
            // interna que será a resposta devolvida pela aplicação.

            throw $exception;
        } catch (CoreExt_Exception_FileNotFoundException $exception) {
            // Caso a página não seja encontrada no módulo legado, lança um erro 404
            // ao invés de 500

            throw new NotFoundHttpException($exception->getMessage(), $exception);
        } catch (Exception $exception) {
            // A maioria das vezes será pega a Exception neste catch, apenas
            // será pega por Throwable quando for ErrorException ou uma exceção
            // customizada que implementa apenas Throwable e não extende a
            // Exception nativa.
            //
            // http://php.net/manual/en/class.throwable.php
        } catch (Throwable $throwable) {
            throw $throwable;
        }

        throw $exception;
    }

    private function processProperty(object $viewToRender): clsBase
    {
        $class = new clsBase;

        if (method_exists($viewToRender, 'Formular')) {
            $viewToRender->Formular();
        }

        if (property_exists($viewToRender, 'title')) {
            $class->SetTitulo($viewToRender->title);
        }

        if (property_exists($viewToRender, 'processoAp')) {
            $class->processoAp = $viewToRender->processoAp;
        }

        if (property_exists($viewToRender, 'renderMenu')) {
            $class->renderMenu = $viewToRender->renderMenu;
        }

        if (property_exists($viewToRender, 'renderMenuSuspenso')) {
            $class->renderMenuSuspenso = $viewToRender->renderMenuSuspenso;
        }

        return $class;
    }

    /**
     * Return all HTTP headers created during this request that will returned
     * in response.
     *
     * @return array
     */
    private function getHttpHeaders(string $content = '')
    {
        $headers = [];

        // No SAPI de linha de comando, onde rodam os testes, headers_list() volta
        // sempre vazia e o corpo do laço não é executado.
        foreach (headers_list() as $header) {
            // @codeCoverageIgnoreStart
            // O limite 2 mantém inteiro um valor que contenha ":", como uma URL.
            [$name, $value] = array_pad(explode(':', $header, 2), 2, '');

            $headers[trim($name)] = trim($value);
            // @codeCoverageIgnoreEnd
        }

        $hasContentType = array_any(
            array_keys($headers),
            static fn (string $name) => strcasecmp($name, 'Content-Type') === 0,
        );

        // No SAPI de linha de comando — que é como roda o servidor in-process dos
        // testes de browser — header() não tem efeito e headers_list() volta
        // vazia. Sem o `Content-Type: text/xml` das telas *_xml.php e sem o
        // `application/json` do ApiCoreController, o navegador trata as duas
        // coisas como HTML: o responseXML fica nulo, as cascatas de select do
        // legado não têm o que ler e o $.get do jQuery entrega a resposta da API
        // como texto, deixando o autocomplete sem sugestões — tudo em silêncio.
        // A dedução só vale nesse SAPI: sob php-fpm headers_list() traz o que as
        // telas declararam, e farejar o corpo mudaria o Content-Type de páginas
        // que hoje respondem sem declarar nenhum.
        if (PHP_SAPI === 'cli' && !$hasContentType) {
            $body = ltrim($content);

            if (str_starts_with($body, '<?xml')) {
                $headers['Content-Type'] = 'text/xml; charset=utf-8';
            } elseif (str_starts_with($body, '{') || str_starts_with($body, '[')) {
                $headers['Content-Type'] = 'application/json; charset=utf-8';
            }
        }

        return $headers;
    }

    /**
     * Return the current HTTP status code.
     *
     * @return int
     */
    private function getHttpStatusCode()
    {
        return http_response_code() ?: Response::HTTP_OK;
    }

    /**
     * Change directory.
     *
     * @return void
     */
    private function changeDirectory()
    {
        chdir(base_path('ieducar/intranet'));
    }

    /**
     * Start session, configure errors and exceptions and load necessary files
     * to run legacy code.
     *
     * @param string $filename
     * @return Response
     *
     * @throws HttpResponseException
     * @throws Exception
     */
    private function requireFileFromLegacy($filename)
    {
        // O nível de buffer é guardado e restaurado porque o código legado pode
        // deixar a pilha desequilibrada: um redirecionamento por permissão sai
        // daqui por exceção, com o ob_start() abaixo ainda aberto. Sob php-fpm o
        // processo morre no fim da requisição e isso se resolve sozinho; no
        // servidor in-process dos testes de browser o processo é reaproveitado, e
        // o que sobra vaza para a requisição seguinte.
        $level = ob_get_level();

        ob_start();

        try {
            $this->overrideGlobals();
            $this->forgetLegacyAssets();
            $this->forgetLegacyStatusCode();
            $this->configureErrorsAndExceptions();
            $this->changeDirectory();
            $this->loadLegacyFile($filename);

            // Só lê o próprio buffer: se o legado já fechou o nosso, ob_get_contents()
            // devolveria o conteúdo de quem chamou.
            $content = ob_get_level() > $level ? (string) ob_get_contents() : '';
        } finally {
            // A condição inclui o retorno do ob_end_clean() para o laço terminar
            // se o buffer não puder ser removido, em vez de girar para sempre.
            while (ob_get_level() > $level && ob_end_clean()) {
                continue;
            }
        }

        return new Response(
            $content,
            $this->getHttpStatusCode(),
            $this->getHttpHeaders($content)
        );
    }

    /**
     * Zera o registro de assets do código legado.
     *
     * Portabilis_View_Helper_Application guarda em propriedade estática o que já
     * emitiu, para não repetir <script> e <link> na mesma página. Isso só
     * funciona porque, sob php-fpm, o processo atende uma requisição e morre;
     * num processo reaproveitado a lista atravessa requisições e as páginas
     * seguintes ficam sem os assets.
     *
     * @return void
     */
    private function forgetLegacyAssets()
    {
        // Com autoload: a classe vem do classmap do código legado e pode ainda
        // não ter sido carregada neste ponto da requisição.
        if (class_exists(\Portabilis_View_Helper_Application::class)) {
            \Portabilis_View_Helper_Application::forgetLoadedAssets();
        }
    }

    /**
     * Devolve o código de resposta ao padrão antes de entrar no legado.
     *
     * getHttpStatusCode() lê http_response_code(), que é global de processo. Sob
     * php-fpm ele nasce em 200 a cada requisição; num processo reaproveitado o
     * código que a tela anterior definiu continua valendo, e uma página que
     * renderizou bem sairia com o status de erro da anterior.
     *
     * @return void
     */
    private function forgetLegacyStatusCode()
    {
        http_response_code(Response::HTTP_OK);
    }

    /**
     * Completa as superglobais a partir da requisição corrente.
     *
     * Sob um SAPI de servidor as superglobais são as que o próprio PHP montou, e
     * é delas que o código legado depende: $_FILES como o array cru de upload, e
     * $_GET/$_POST sem a limpeza dos middleware globais TrimStrings e
     * ConvertEmptyStringsToNull, que reescrevem os bags da requisição do Laravel
     * mas não as superglobais. Aqui só se preenche o que estiver faltando.
     *
     * No SAPI de linha de comando não há superglobais de requisição, e o
     * processo é reaproveitado entre requisições — é o caso do servidor
     * in-process dos testes de browser. Aí elas são reescritas por inteiro, ou o
     * legado lê o que sobrou da requisição anterior.
     *
     * @return void
     */
    private function overrideGlobals()
    {
        // Só roda fora do SAPI de linha de comando, onde rodam os testes.
        if (PHP_SAPI !== 'cli') {
            // @codeCoverageIgnoreStart
            $_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? $this->request->getRequestUri();

            $_GET = empty($_GET) ? $this->request->query->all() : $_GET;
            $_POST = (empty($_POST) && $this->request->isMethod('post')) ? $this->request->request->all() : $_POST;
            $_FILES = empty($_FILES) ? $this->request->files->all() : $_FILES;
            $_COOKIE = empty($_COOKIE) ? $this->request->cookies->all() : $_COOKIE;

            return;
            // @codeCoverageIgnoreEnd
        }

        // A requisição vem do container, e não de $this->request: a rota guarda
        // a instância do controller, e num processo reaproveitado ela continua
        // sendo a da primeira requisição — com o GET e o POST daquela.
        $request = request();

        $_SERVER['REQUEST_URI'] = $request->getRequestUri();

        $_GET = $request->query->all();
        $_POST = $request->isMethod('post') ? $request->request->all() : [];
        $_FILES = $request->files->all();
        $_COOKIE = $request->cookies->all();
    }

    /**
     * Load intranet route file and generate a response.
     *
     * @param string $uri
     * @return Response
     *
     * @throws HttpResponseException
     * @throws Exception
     */
    public function intranet($uri)
    {
        return $this->requireFileFromLegacy('intranet/' . $uri);
    }

    /**
     * Load module route file and generate a response.
     *
     * @return Response
     *
     * @throws HttpResponseException
     * @throws Exception
     */
    public function module()
    {
        return $this->requireFileFromLegacy('module/index.php');
    }

    /**
     * Load modules route file and generate a response.
     *
     * @param string $uri
     * @return Response
     *
     * @throws HttpResponseException
     * @throws Exception
     */
    public function modules($uri)
    {
        return $this->requireFileFromLegacy('modules/' . $uri);
    }

    /**
     * Load module route file and generate a response for API.
     *
     * @return Response
     *
     * @throws HttpResponseException
     * @throws Exception
     */
    public function api()
    {
        return $this->requireFileFromLegacy('module/index.php');
    }
}
