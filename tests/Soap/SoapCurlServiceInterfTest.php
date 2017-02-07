<?php
use \NFePHP\Common\Services\AbstractServiceInterface;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;

class SoapCurlServiceInterfTest extends PHPUnit_Framework_TestCase
{

    public function testShouldInitAbstracServiceMock()
    {
        $mock = $this->getMockBuilder(NFePHP\Common\Soap\SoapBase::class)
            ->disableOriginalConstructor()
            ->setMethods(['send', 'makeEnvelopeSoap'])
            ->getMock();

        $mock->send(new ServiceStub);
    }

 
    public function testShouldGetExceptionRequestOnCurl()
    {
        $this->setExpectedException(NFePHP\Common\Exception\SoapException::class);

        $privateKey = new Certificate\PrivateKey(file_get_contents(__DIR__ . '/../fixtures/certs/x99999090910270_priKEY.pem'));
        $publicKey = new Certificate\PublicKey(file_get_contents(__DIR__ . '/../fixtures/certs/x99999090910270_pubKEY.pem'));

        $curl = new SoapCurl(new Certificate($privateKey, $publicKey));
        $curl->send(new ServiceStub);
    }
}

class ServiceStub extends AbstractServiceInterface
{

    /**
     * @override
     */
    public function __construct()
    {
        $this->soapver = SOAP_1_2;
        parent::__construct();
    }

    public function __toString()
    {
        return __CLASS__;
    }

    public function withAction()
    {
        $this->action = 'status';
    }

    public function withHeader()
    {
        $this->soapheader = '<nfeCabecMsg xmlns="http://www.portalfiscal.inf.br/nfe"><cUF>31</cUF><versaoDados>3.10</versaoDados></nfeCabecMsg>';
    }

    public function withNamespace()
    {
        $this->namespaces = ['NFeStatusServiceConsulta'];
    }

    public function withRequest()
    {
        $this->request = '<consStatServ xmlns="http://www.portalfiscal.inf.br/nfe" versao="3.10"><tpAmb>2</tpAmb><cUF>31</cUF><xServ>STATUS</xServ></consStatServ>';
    }

    public function withURL()
    {
        $this->url = 'http://localhost:80';
    }
}
