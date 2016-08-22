<?php

/**
 * Description of ClientRequestTest
 *
 * @author Luis
 */
class ClientRequestTest extends PHPUnit_Framework_TestCase
{

    public function testSendStructureMock()
    {
        $client = $this->getMockBuilder(NFePHP\Common\Soap\ClientRequest::class)
            ->getMock();

        $service = $this->getMockBuilder(NFePHP\Common\ServiceRequest::class)
            ->getMock();

        $service->expects($this->once())->method('getConfiguration')
            ->will($this->returnValue(array()));

        $client->expects($this->once())->method('send')->will(
            $this->returnCallback([$service, 'getConfiguration'])
        );
        
       $response = $client->send($service);
       
    }
}
