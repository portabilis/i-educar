<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LegacyController extends Controller
{
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
     * @throws HttpException
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

        app(ExceptionHandler::class)->report($exception);

        if (config('app.debug')) {
            throw $exception;
        }

        throw new HttpException(500, 'Error in legacy code.', $exception);
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
        return http_response_code();
    }

    /**
     * Start session, configure errors and exceptions and load necessary files
     * to run legacy code.
     *
     * @param string $filename
     *
     * @return Response
     */
    private function requireFileFromLegacy($filename)
    {
        ob_start();

        $this->startLegacySession();
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
     * Start session.
     *
     * @return void
     */
    private function startLegacySession()
    {
        session_start();
    }

    /**
     * Load intranet route file and generate a response.
     *
     * @param string $uri
     *
     * @return Response
     */
    public function intranet($uri)
    {
        return $this->requireFileFromLegacy('intranet/' . $uri);
    }

    /**
     * Load module route file and generate a response.
     *
     * @return Response
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
     */
    public function modules($uri)
    {
        return $this->requireFileFromLegacy('modules/' . $uri);
    }
}
