<?php

namespace NFePHP\Common;

/**
 *
 * @author Luis Paulo
 */
interface ServiceRequest
{
    public function withUri($uri);
    public function withHeader($headers);
    public function withBody($body);
    public function getConfiguration();
}
