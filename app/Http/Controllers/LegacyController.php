<?php

namespace App\Http\Controllers;

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
        return base_path('ieducar');
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
     */
    private function loadLegacyBootstrapFile()
    {
        $filename = $this->getLegacyBootstrapFile();

        if (false === file_exists($filename)) {
            throw new HttpException(500, 'Legacy bootstrap file not found.');
        }

        require_once $filename;
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
     */
    private function loadLegacyFile($filename)
    {
        $legacyFile = $this->getLegacyPath() . '/' . $filename;

        if (false === file_exists($legacyFile)) {
            throw new NotFoundHttpException('Legacy file not found.');
        }

        require_once $legacyFile;
    }

    /**
     * Start session, configure errors and exceptions and load necessary files
     * to run legacy code.
     *
     * @param string $filename
     *
     * @return void
     */
    private function requireFileFromLegacy($filename)
    {
        $this->startLegacySession();
        $this->configureErrorsAndExceptions();
        $this->loadLegacyBootstrapFile();
        $this->loadLegacyFile($filename);
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
     * Load intranet route file.
     *
     * @param string $uri
     *
     * @return void
     */
    public function intranet($uri)
    {
        $this->requireFileFromLegacy('intranet/' . $uri);
    }

    /**
     * Load module route file.
     *
     * @return void
     */
    public function module()
    {
        $this->requireFileFromLegacy('module/index.php');
    }

    /**
     * Load modules route file.
     *
     * @param string $uri
     *
     * @return void
     */
    public function modules($uri)
    {
        $this->requireFileFromLegacy('modules/' . $uri);
    }
}
