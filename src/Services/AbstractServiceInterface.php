<?php

namespace NFePHP\Common\Services;

/**
 * Soap base class
 *
 * @category  NFePHP
 * @package   NFePHP\Common\Services
 * @copyright NFePHP Copyright (c) 2017
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */
abstract class AbstractServiceInterface
{

    public $url,
        $operation = '',
        $action = '',
        $soapver = SOAP_1_2,
        $parameters = [],
        $namespaces = [],
        $request,
        $soapheader = null;

    public function __construct()
    {
        $this->withAction();
        $this->withHeader();
        $this->withRequest();
        $this->withURL();
        $this->withNamespace();
        
        if (empty($this->request)) {
            throw new \InvalidArgumentException("{$this->request}, Can't empty!");
        }
    }

    abstract public function withRequest();

    abstract public function withURL();

    abstract public function withAction();

    abstract public function withNamespace();

    abstract public function withHeader();
}
