<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

require_once 'lib/Portabilis/Report/ReportFactory.php';

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
     * @inheritdoc
     */
    public function setSettings($config)
    {
        $this->url = $config->report->remote_factory->url;
        $this->sourcePath = $config->report->source_path;
        $this->token = $config->report->remote_factory->token;
        $this->logo = $config->report->logo_file_name;
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
     *
     * @return string
     *
     * @throws GuzzleException
     * @throws Exception
     */
    public function dumps($report, $options = [])
    {
        $options = self::mergeOptions($options, [
            'add_logo_name_arg' => true,
            'encoding' => 'uncoded'
        ]);

        if ($options['add_logo_name_arg'] and !$this->logo) {
            throw new Exception('The option \'add_logo_name_arg\' is true, but no logo_name defined in configurations!');
        } elseif ($options['add_logo_name_arg']) {
            $report->addArg('logo', $this->logo);
        }

        $client = new Client([
            'http_errors' => false
        ]);

        $templateName = $report->templateName();
        $params = $report->args;

        if ($report->useJson()) {
            $params['datasource'] = 'json';
        } else {
            $params['datasource'] = 'database';
            $params['connection'] = 'postgresql';

            $params += $this->connection;
        }

        $url = $this->sourcePath;
        $data = $report->getJsonData();

        $response = $client->request('POST', $this->url, [
            'json' => [
                'report' => $templateName,
                'url' => $url,
                'data' => $data,
                'params' => $params,
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Token ' . $this->token,
            ]
        ]);

        $json = json_decode($response->getBody()->getContents(), true);

        if (is_null($json)) {
            throw new Exception('Não foi possível analisar a resposta do serviço.');
        }

        if ($json['success'] == false) {
            throw new Exception($json['error'] ?? $json['message']);
        }

        $report = base64_decode($json['data']['file']);

        return $report;
    }
}
