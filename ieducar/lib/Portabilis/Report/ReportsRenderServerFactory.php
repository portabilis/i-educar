<?php

use App\Events\ReportIssued;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use iEducar\Reports\Contracts\ReportRenderContract;
use Illuminate\Support\Facades\Log;

class Portabilis_Report_ReportsRenderServerFactory extends Portabilis_Report_ReportFactory
{
    /**
     * URL onde se encontra o serviço de renderização.
     *
     * @var string
     */
    private $url;

    /**
     * Caminho onde estão os arquivos .jasper.
     *
     * @var string
     */
    private $sourcePath;

    /**
     * Token para autorização do serviço.
     *
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $logo;

    /**
     * @var array
     */
    private $connection;

    /**
     * @var string
     */
    private $timezone;

    /**
     * Loga erros da requisição ao servidor de relatórios.
     *
     * @param array  $payload
     * @param string $response
     * @return void
     */
    protected function log($payload, $response)
    {
        $log = json_encode([
            'url' => $this->url,
            'payload' => $payload,
            'response' => $response,
        ]);

        Log::error($log);
    }

    /**
     * {@inheritdoc}
     */
    public function setSettings($config)
    {
        $this->url = $config->report->remote_factory->url;
        $this->sourcePath = $config->report->source_path;
        $this->token = $config->report->remote_factory->token;
        $this->logo = $config->report->logo_file_name;
        $this->timezone = $config->app->locale->timezone;
        $this->connection = [
            'host' => $config->app->database->hostname,
            'port' => $config->app->database->port,
            'database' => $config->app->database->dbname,
            'username' => $config->app->database->username,
            'password' => $config->app->database->password,
        ];
    }

    /**
     * Renderiza o relatório.
     *
     * @param Portabilis_Report_ReportCore $report
     * @param array                        $options
     * @return string
     *
     * @throws GuzzleException
     * @throws Exception
     */
    public function dumps($report, $options = [])
    {
        $options = self::mergeOptions($options, [
            'add_logo_name_arg' => true,
            'encoding' => 'uncoded',
        ]);

        if ($options['add_logo_name_arg'] and !$this->logo) {
            throw new Exception('The option \'add_logo_name_arg\' is true, but no logo_name defined in configurations!');
        } elseif ($options['add_logo_name_arg']) {
            $report->addArg('logo', $this->logo);
        }

        $client = new Client([
            'http_errors' => false,
        ]);

        $templateName = $report->templateName();
        $params = $report->args;

        $params['timezone'] = $this->timezone;

        $data = [];

        if ($report->useHtml()) {
            $data = $report->getHtmlData();
            $payload = [
                'view' => 'reports::' . $templateName,
                'parameters' => $data['main'],
                'orientation' => $data['orientation'] ?? null,
                'driver' => $data['driver'] ?? null,
            ];

            $success = true;
            $content = null;
            $error = null;

            try {
                $content = app(ReportRenderContract::class)->render($payload);
            } catch (Throwable $throwable) {
                $success = false;
                $error = $throwable;
            }

            if (is_array($data['main']) && array_key_exists('blade', $data['main'])) {
                $templateName = str_replace('reports::', '', $data['main']['blade']);
            }

            $event = new ReportIssued('html', $templateName, $success, $report->authenticate());

            if ($success) {
                $event->replace(base64_encode($content->content()));
            }

            event($event);

            if ($error) {
                throw $error;
            }

            return base64_decode($event->content());
        } elseif ($report->useJson()) {
            $params['datasource'] = 'json';
            $this->url = str_replace('/deprecated', '', $this->url);
            $this->sourcePath = str_replace('/deprecated', '', $this->sourcePath);

            $data = $report->getJsonData();
            $data = $report->modify($data);
        } else {
            $params['datasource'] = 'database';
            $params['connection'] = 'postgresql';

            $params += $this->connection;
        }

        $url = $this->sourcePath;

        $payload = [
            'json' => [
                'report' => $templateName,
                'url' => $url,
                'data' => $data,
                'params' => $params,
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Token ' . $this->token,
            ],
        ];

        $response = $client->request('POST', $this->url, $payload);

        $success = $response->getStatusCode() === 200;
        $render = $report->useJson() ? 'json' : 'jasper';
        $error = '';

        $json = json_decode($response->getBody()->getContents(), true);

        if (config('legacy.report.debug')) {
            $this->log($payload, $response->getBody()->getContents());
        }

        // Ao retornar um PDF em base64 um evento será disparado para garantir
        // que o conteúdo possa ser modificado caso haja a necessidade, desta
        // forma é possível extender o comportamento do renderizador de relatórios.

        $event = new ReportIssued($render, $templateName, $success);

        if (is_null($json)) {
            $this->log($payload, $response->getBody()->getContents());

            $error = 'Não foi possível analisar a resposta do serviço.';
        } elseif ($json['success'] == false) {
            $this->log($payload, $response->getBody()->getContents());

            $error = $json['error'] ?? $json['message'];
        }

        if (isset($json['data']['file'])) {
            $event->replace($json['data']['file']);
        }

        event($event);

        if ($error) {
            throw new Exception($error);
        }

        return base64_decode($event->content());
    }
}
