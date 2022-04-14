<?php

namespace App\Http\Controllers;

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
     *
     * @throws NotFoundHttpException
     * @throws Exception
     *
     * @return void
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
     * @throws Exception
     *
     * @return void
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

            // Converte uma exceção que implementa apenas Throwable para
            // Exception nativa do PHP. Isto é feito devido o Exception
            // Handler do Laravel aceitar apenas exceções nativas.

            $exception = new Exception(
                $throwable->getMessage(),
                $throwable->getCode(),
                $throwable
            );
        }

        throw $exception;
    }

    /**
     * @param object $viewToRender
     *
     * @return clsBase
     */
    private function processProperty(object $viewToRender): clsBase
    {
        $class = new clsBase();

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
     *
     * @throws HttpResponseException
     * @throws Exception
     *
     * @return Response
     */
    private function requireFileFromLegacy($filename)
    {
        ob_start();

        $this->overrideGlobals();
        $this->configureErrorsAndExceptions();
        $this->changeDirectory();
        $this->loadLegacyFile($filename);

        $content = ob_get_contents();

        ob_end_clean();

        return new Response(
            $content,
            $this->getHttpStatusCode(),
            $this->getHttpHeaders()
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
     * @throws HttpResponseException
     * @throws Exception
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
     * @throws HttpResponseException
     * @throws Exception
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
     * @throws HttpResponseException
     * @throws Exception
     *
     * @return Response
     */
    public function modules($uri)
    {
        return $this->requireFileFromLegacy('modules/' . $uri);
    }

    /**
     * Load module route file and generate a response for API.
     *
     * @throws HttpResponseException
     * @throws Exception
     *
     * @return Response
     */
    public function api()
    {
        return $this->requireFileFromLegacy('module/index.php');
    }
}
