<?php

namespace NFePHP\Common\Soap;

/**
 *
 * @author Luis Paulo
 */
interface ClientRequest
{

    public function send(\NFePHP\Common\ServiceRequest $service);

    public function response();

}
