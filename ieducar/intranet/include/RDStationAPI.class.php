<?php
/**
Authors:
JÃºlio Paulillo <julio@agendor.com.br>
Tulio Monte Azul <tulio@agendor.com.br>

DOCS
#############
Guia de integraÃ§Ãµes
http://ajuda.rdstation.com.br/hc/pt-br/articles/200310549-Guia-de-integra%C3%A7%C3%B5es-com-o-RD-Station

Marcar venda e lost via formulÃ¡rio prÃ³prio ou sistema (API)
http://ajuda.rdstation.com.br/hc/pt-br/articles/202640385-Marcar-venda-e-lost-via-formul%C3%A1rio-pr%C3%B3prio-ou-sistema-API-

Alterar estado do Lead no funil do RD Station (API)
http://ajuda.rdstation.com.br/hc/pt-br/articles/200310699-Alterar-estado-do-Lead-no-funil-do-RD-Station-API-

Integrar formulÃ¡rio no site ou sistema prÃ³prio para CriaÃ§Ã£o de Lead (API)
http://ajuda.rdstation.com.br/hc/pt-br/articles/200310589-Integrar-formul&aacute;rio-no-site-ou-sistema-pr&oacute;prio-para-Cria&ccedil;&atilde;o-de-Lead-API-
**/

class RDStationAPI
{
    public $token;
    public $privateToken;
    public $baseURL = 'https://www.rdstation.com.br/api/';
    public $apiVersion = '1.3';
    public $defaultIdentifier = 'Usuário no produto i-Educar';

    public function __construct($privateToken=null, $token=null)
    {
        if (empty($privateToken)) {
            throw new Exception('Inform RDStationAPI.privateToken as the first argument.');
        }

        $this->token = $token;
        $this->privateToken = $privateToken;
    }

    /**
    $type:  (String) generic, leads, conversions
    **/
    protected function getURL($type='generic')
    {
        //(POST) https://www.rdstation.com.br/api/1.2/services/PRIVATE_TOKEN/generic //USED TO CHANGE A LEAD STATUS
        //(PUT) https://www.rdstation.com.br/api/1.2/leads/:lead_email //USED TO UPDATE A LEAD
        //(POST) https://www.rdstation.com.br/api/1.2/conversions //USED TO SEND A NEW LEAD
        switch ($type) {
      case 'generic':     return $this->baseURL.$this->apiVersion.'/services/'.$this->privateToken.'/generic';
      case 'leads':       return $this->baseURL.$this->apiVersion.'/leads/';
      case 'conversions': return $this->baseURL.$this->apiVersion.'/conversions';
    }
    }

    protected function validateToken()
    {
        if (empty($this->token)) {
            throw new Exception('Inform RDStation.token as the second argument when instantiating a new RDStationAPI object.');
        }
    }

    /**
    $method:  (String) POST, PUT
    $url:     (String) RD Station endpoint returned by $this->getURL()
    $data:    (Array)
    **/
    protected function request($method, $url, $data=[])
    {
        $data['token_rdstation'] = $this->token;
        $JSONData = json_encode($data);
        $URLParts = parse_url($url);

        $fp = fsockopen(
            $URLParts['host'],
            isset($URLParts['port'])?$URLParts['port']:80,
            $errno,
            $errstr,
            30
        );

        $out = $method.' '.$URLParts['path']." HTTP/1.1\r\n";
        $out .= 'Host: '.$URLParts['host']."\r\n";
        $out .= "Content-Type: application/json\r\n";
        $out .= 'Content-Length: '.strlen($JSONData)."\r\n";
        $out .= "Connection: Close\r\n\r\n";
        $out .= $JSONData;

        $written = fwrite($fp, $out);
        fclose($fp);

        return ($written==false)?false:true;
    }

    /**
    $email: (String) The email of the lead
    $data:  (Array) Custom data array, example:
      array(
        "identificador" => "contact-form",
        "nome" => "JÃºlio Paulillo",
        "empresa" => "Agendor",
        "cargo" => "Cofounder",
        "telefone" => "(11) 3280-8090",
        "celular" => "(11) 99999-9999",
        "website" => "www.agendor.com.br",
        "twitter" => "twitter.com/paulillo",
        "facebook" => "facebook.com/paulillo",
        "c_utmz" => "",
        "created_at" => "",
        "tags" => "cofounder, hotlead"
      );
    **/
    public function sendNewLead($email, $data=[])
    {
        $this->validateToken();
        if (empty($email)) {
            throw new Exception('Inform at least the lead email as the first argument.');
        }
        if (empty($data['identificador'])) {
            $data['identificador'] = $this->defaultIdentifier;
        }
        if (empty($data['client_id']) && !empty($_COOKIE['rdtrk'])) {
            $data['client_id'] = json_decode($_COOKIE['rdtrk'])->{'id'};
        }
        if (empty($data['traffic_source']) && !empty($_COOKIE['__trf_src'])) {
            $data['traffic_source'] = $_COOKIE['__trf_src'];
        }

        $data['email'] = $email;

        return $this->request('POST', $this->getURL('conversions'), $data);
    }

    /**
    Helper function to update lead properties
    **/
    public function updateLead($email, $data=[])
    {
        return $this->sendNewLead($email, $data);
    }

    /**
    $email: (String) Lead email
    $newStage: (Integer) 0 - Lead, 1 - Qualified Lead, 2 - Customer
    $opportunity: (Integer) true or false
    **/
    public function updateLeadStage($email, $newStage=0)
    {
        if (empty($email)) {
            throw new Exception('Inform lead email as the first argument.');
        }

        $url = $this->getURL('leads').$email;

        $data = [
      'auth_token' => $this->privateToken,
      'lead' => [
        'lifecycle_stage' => $newStage
      ]
    ];

        return $this->request('PUT', $url, $data);
    }

    /**
    $emailOrLeadId: (String / Integer) Lead email OR Lead unique custom ID
    $status: (String) won / lost
    $value: (Integer/Decimal) Purchase value
    $lostReason: (String)
    **/
    public function updateLeadStatus($emailOrLeadId, $status, $value=null, $lostReason=null)
    {
        if (empty($emailOrLeadId)) {
            throw new Exception('Inform lead email or unique custom ID as the first argument.');
        }
        if (empty($status)) {
            throw new Exception('Inform lead status as the second argument.');
        } elseif ($status!='won'&&$status!='lost') {
            throw new Exception('Lead status (second argument) should be \'won\' or \'lost\'.');
        }

        $data = [
      'status' => $status,
      'value' => $value,
      'lost_reason' => $lostReason,
    ];

        if (is_integer($emailOrLeadId)) {
            $data['lead_id'] = $emailOrLeadId;
        } else {
            $data['email'] = $emailOrLeadId;
        }

        return $this->request('POST', $this->getURL('generic'), $data);
    }
}
