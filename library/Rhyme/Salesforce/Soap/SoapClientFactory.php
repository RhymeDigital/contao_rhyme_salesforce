<?php

/**
 * Copyright (C) 2015 Rhyme Digital, LLC.
 * 
 * @author		Blair Winans <blair@rhyme.digital>
 * @author		Adam Fisher <adam@rhyme.digital>
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Salesforce\Soap;

use Phpforce\SoapClient\Soap\SoapClientFactory as Phpforce_SoapClientFactory;

require_once(TL_ROOT.'/composer/vendor/phpforce/soap-client/src/Phpforce/SoapClient/Soap/SoapClientFactory.php');


class SoapClientFactory extends Phpforce_SoapClientFactory
{

    /**
     * Custom SOAP client class
     * @var string
     */
    protected $strSoapClientClass = '\Rhyme\Salesforce\Soap\SoapClient';


    /**
     * @param string $wsdl Some argument description
     *
     * @return void
     */
    public function factory($wsdl)
    {
		$strClass = $this->strSoapClientClass;
        return new $strClass($wsdl, array(
            'trace'     => 1,
            'features'  => \SOAP_SINGLE_ELEMENT_ARRAYS,
            'classmap'  => $this->classmap,
            'typemap'   => $this->getTypeConverters()->getTypemap(),
            'cache_wsdl' => \WSDL_CACHE_MEMORY
        ));
    }
}