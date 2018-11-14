<?php

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
     * @inheritdoc
     */
    public function setSettings($config)
    {
        $this->url = $config->report->remote_factory->url;
        $this->sourcePath = $config->report->source_path;
        $this->token = $config->report->remote_factory->token;
        $this->logo = $config->report->logo_file_name;
    }

    /**
     * Renderiza o relatório.
     *
     * @param Portabilis_Report_ReportCore $report
     * @param array $options
     *
     * @return string
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
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

        $client = new \GuzzleHttp\Client([
            'http_errors' => false
        ]);

        $response = $client->request('POST', $this->url, [
            'json' => [
                'report' => $report->templateName(),
                'url' => $this->sourcePath,
                'data' => $report->getJsonData(),
                'params' => $report->args,
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
            throw new Exception($json['error']);
        }

        $report = base64_decode($json['data']['file']);

        return $report;
    }
}
