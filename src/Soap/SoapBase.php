<?php

namespace NFePHP\Common\Soap;

/**
 * Soap base class
 *
 * @category  NFePHP
 * @package   NFePHP\Common\Soap\SoapBase
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapInterface;
use Psr\Log\LoggerInterface;
use \NFePHP\Common\Services\AbstractServiceInterface;

abstract class SoapBase implements SoapInterface
{

    //soap parameters
    protected $connection;
    protected $soapprotocol = self::SSL_DEFAULT;
    protected $soaptimeout = 20;
    protected $proxyIP = '';
    protected $proxyPort = '';
    protected $proxyUser = '';
    protected $proxyPass = '';
    protected $prefixes = [1 => 'soapenv', 2 => 'soap'];
    //certificat parameters
    protected $certificate;
    protected $tempdir = '';
    protected $prifile = '';
    protected $pubfile = '';
    protected $certfile = '';
    //log info
    public $responseHead = '';
    public $responseBody = '';
    public $requestHead = '';
    public $requestBody = '';
    public $soaperror = '';
    public $soapinfo = [];
    public $debugmode = false;

    /**
     * Constructor
     * @param Certificate $certificate
     * @param LoggerInterface $logger
     */
    public function __construct(Certificate $certificate = null, LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->certificate = $certificate;
        $this->saveTemporarilyKeyFiles();
    }

    /**
     * Set debug mode, this mode will save soap envelopes in temporary directory
     * @param bool $value
     */
    public function setDebugMode($value = false)
    {
        $this->debugmode = $value;
    }

    /**
     * Set certificate class for SSL comunications
     * @param Certificate $certificate
     */
    public function loadCertificate(Certificate $certificate)
    {
        $this->certificate = $certificate;
        $this->saveTemporarilyKeyFiles();
    }

    /**
     * Set logger class
     * @param LoggerInterface $logger
     */
    public function loadLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set timeout for communication
     * @param int $timesecs
     */
    public function timeout($timesecs)
    {
        $this->soaptimeout = $timesecs;
    }

    /**
     * Set security protocol
     * @param int $protocol
     */
    public function protocol($protocol = self::SSL_DEFAULT)
    {
        $this->soapprotocol = $protocol;
    }

    public function setSoapPrefix($prefixes)
    {
        $this->prefixes = $prefixes;
    }

    /**
     * Set proxy parameters
     * @param string $ip
     * @param int $port
     * @param string $user
     * @param string $password
     */
    public function proxy($ip, $port, $user, $password)
    {
        $this->proxyIP = $ip;
        $this->proxyPort = $port;
        $this->proxyUser = $user;
        $this->proxyPass = $password;
    }

    abstract public function send(AbstractServiceInterface $service);

    /**
     * Mount soap envelope
     * @param string $request
     * @param string $operation
     * @param array $namespaces
     * @param \SOAPHeader $header
     * @return string
     */
    protected function makeEnvelopeSoap(AbstractServiceInterface $service)
    {
        $prefix = $this->prefixes[$service->soapver];
        $content = <<<XML
            <?xml version="1.0" encoding="utf-8"?><$prefix:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:$prefix="http://www.w3.org/2003/05/soap-envelope"><$prefix:Header>$service->soapheader</$prefix:Header><$prefix:Body>$service->request</$prefix:Body></$prefix:Envelope>
XML;
        return $content;
    }

    /**
     * Temporarily saves the certificate keys for use cURL or SoapClient
     */
    protected function saveTemporarilyKeyFiles()
    {
        if (is_object($this->certificate)) {
            $this->tempdir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'certs' . DIRECTORY_SEPARATOR;
            if (!is_dir($this->tempdir)) {
                mkdir($this->tempdir);
            }
            $this->prifile = tempnam($this->tempdir, 'Pri') . '.pem';
            $this->pubfile = tempnam($this->tempdir, 'Pub') . '.pem';
            $this->certfile = tempnam($this->tempdir, 'Cert') . '.pem';
            file_put_contents($this->prifile, $this->certificate->privateKey);
            file_put_contents($this->pubfile, $this->certificate->publicKey);
            file_put_contents($this->certfile, $this->certificate->privateKey . $this->certificate->publicKey);
        }
    }

    /**
     * Deletes the certificate keys
     */
    protected function removeTemporarilyKeyFiles()
    {
        unlink($this->prifile);
        unlink($this->pubfile);
        unlink($this->certfile);
        unlink(substr($this->prifile, 0, strlen($this->prifile) - 4));
        unlink(substr($this->pubfile, 0, strlen($this->pubfile) - 4));
        unlink(substr($this->certfile, 0, strlen($this->certfile) - 4));
    }

    /**
     * Save request envelope and response for debug reasons
     * @param string $operation
     * @param string $request
     * @param string $response
     * @return void
     */
    protected function saveDebugFiles($operation, $request, $response)
    {
        if (!$this->debugmode) {
            return;
        }
        $tempdir = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'soap'
            . DIRECTORY_SEPARATOR;
        if (!is_dir($tempdir)) {
            mkdir($tempdir, 0777);
        }
        $num = date('mdHis');
        file_put_contents($tempdir . "req_" . $operation . "_" . $num . "txt", $request);
        file_put_contents($tempdir . "res_" . $operation . "_" . $num . "txt", $response);
    }
}
