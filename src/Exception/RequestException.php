<?php

namespace NFePHP\Common\Exception;

/**
 * Description of RequestException
 *
 * @author Luis Paulo
 */
class RequestException extends \RuntimeException
{

    public static function curlError(\ResourceBundle $curl)
    {
        return new static('Curl Request error, ' . curl_error($curl));
    }
}
