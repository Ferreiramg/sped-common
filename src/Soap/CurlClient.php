<?php

namespace NFePHP\Common\Soap;

use \NFePHP\Common\Exception\RequestException;

/**
 * Description of CurlClient
 *
 * @author Luis Paulo
 */
class CurlClient implements ClientRequest
{

    public function response()
    {
        return $this->response;
    }

    public function send(\NFePHP\Common\ServiceRequest $service)
    {
        $config = $service->getConfiguration();
        $curl = curl_init((string) $service->uri); //url

        $options = $this->loadOptions($config);
        $options[CURLOPT_POSTFIELDS] = (string) $service->body;
        $options[CURLOPT_HTTPHEADER] = (string) $service->headers;

        curl_setopt_array($curl, $options);
        $this->response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw RequestException::curlError($curl);
        }

        return $this->response;
    }

    private function loadOptions(array $options)
    {
        if (isset($options['curl'])) {
            //validate options
            return $options;
        }
        return array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => (int) $options['timeout'],
            CURLOPT_PORT => (int) $options['port'],
            CURLOPT_HEADER => false,
            CURLOPT_SSLVERSION => (int) 3,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CERTINFO => 1,
            CURLOPT_SSLCERT => (string) $options['sslcert'],
            CURLOPT_SSLKEY => (string) $options['sslkey'],
            CURLOPT_POST => true
        );
    }
}
