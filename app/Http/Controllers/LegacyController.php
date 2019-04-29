<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LegacyController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * LegacyController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
     * Return i-Educar original bootstrap file.
     *
     * @return string
     */
    private function getLegacyBootstrapFile()
    {
        return $this->getLegacyPath() . '/includes/bootstrap.php';
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
     * Load bootstrap file, if not found, throw a HttpException with HTTP error
     * code 500 Server Internal Error.
     *
     * @return void
     *
     * @throws HttpException
     * @throws Exception
     */
    private function loadLegacyBootstrapFile()
    {
        $filename = $this->getLegacyBootstrapFile();

        if (false === file_exists($filename)) {
            throw new HttpException(500, 'Legacy bootstrap file not found.');
        }

        $this->loadFileOrAbort($filename);
    }

    /**
     * Load legacy route file, if not found, throw a HttpException with HTTP
     * error code 404 Not Found.
     *
     * @param string $filename
     *
     * @return void
     *
     * @throws NotFoundHttpException
     * @throws Exception
     */
    private function loadLegacyFile($filename)
    {
        $legacyFile = $this->getLegacyPath() . '/' . $filename;

        if (false === file_exists($legacyFile)) {
            throw new NotFoundHttpException('Legacy file not found.');
        }

        $this->loadFileOrAbort($legacyFile);
    }

    /**
     * Load a file or abort the application.
     *
     * @param string $filename
     *
     * @return void
     *
     * @throws Exception
     */
    private function loadFileOrAbort($filename)
    {
        try {
            require_once $filename;
            return;
        } catch (Exception $exception) {

            // A maioria das vezes será pega a Exception neste catch, apenas
            // será pega por Throwable quando for ErrorException ou uma exceção
            // customizada que implementa apenas Throwable e não extende a
            // Exception nativa.
            //
            // http://php.net/manual/en/class.throwable.php

        } catch (Throwable $throwable) {

            // Converte uma exceção que implementa apenas Throwable para
            // Exception nativa do PHP. Isto é feito devido o Exception
            // Handler do Laravel aceitar apenas exceções nativas.

            $exception = new Exception(
                $throwable->getMessage(), $throwable->getCode(), $throwable
            );
        }

        throw $exception;
    }

    /**
     * Return all HTTP headers created during this request that will returned
     * in response.
     *
     * @return array
     */
    private function getHttpHeaders()
    {
        $headers = [];

        foreach (headers_list() as $header) {
            $header = explode(':', $header);

            $name = trim($header[0]);
            $value = trim($header[1]);

            $headers[$name] = $value;
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
     * Start session, configure errors and exceptions and load necessary files
     * to run legacy code.
     *
     * @param string $filename
     *
     * @return Response
     *
     * @throws HttpResponseException
     * @throws Exception
     */
    private function requireFileFromLegacy($filename)
    {
        ob_start();

        $this->overrideGlobals();
        $this->configureErrorsAndExceptions();
        $this->loadLegacyBootstrapFile();
        $this->loadLegacyFile($filename);

        $content = ob_get_contents();

        ob_end_clean();

        return new Response(
            $content, $this->getHttpStatusCode(), $this->getHttpHeaders()
        );
    }

    /**
     * Override PHP Globals if not exists.
     *
     * @return void
     */
    private function overrideGlobals()
    {
        $_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? $this->request->getRequestUri();

        $_GET = empty($_GET) ? $this->request->query->all() : $_GET;
        $_POST = (empty($_POST) && $this->request->isMethod('post')) ? $this->request->request->all() : $_POST;
        $_FILES = empty($_FILES) ? $this->request->files->all() : $_FILES;
        $_COOKIE = empty($_COOKIE) ? $this->request->cookies->all() : $_COOKIE;
    }

    /**
     * Load intranet route file and generate a response.
     *
     * @param string $uri
     *
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
     *
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
