<?php

/**
 * Copyright (C) 2015 Rhyme Digital, LLC.
 * 
 * @author		Blair Winans <blair@rhyme.digital>
 * @author		Adam Fisher <adam@rhyme.digital>
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Salesforce\Client;

use Phpforce\SoapClient\Plugin\LogPlugin as Phpforce_LogPlugin;
use Phpforce\SoapClient\ClientBuilder as Phpforce_ClientBuilder;
use Rhyme\Salesforce\Soap\SoapClientFactory as SF_SoapClientFactory;

require_once(TL_ROOT.'/composer/vendor/phpforce/soap-client/src/Phpforce/SoapClient/ClientBuilder.php');


class ClientBuilder extends Phpforce_ClientBuilder
{

    /**
     * Custom client class
     * @var string
     */
    protected $strClientClass = '\Rhyme\Salesforce\Client\Client';
    

    /**
     * Build the Salesforce SOAP client
     * OVERRIDE FOR CUSTOM CLIENT CLASS
     *
     * @return Client
     */
    public function build()
    {
        $soapClientFactory = new SF_SoapClientFactory();
        $soapClient = $soapClientFactory->factory($this->wsdl);
		
		$strClass = $this->strClientClass;
        $client = new $strClass($soapClient, $this->username, $this->password, $this->token);

        if ($this->log) {
            $logPlugin = new Phpforce_LogPlugin($this->log);
            $client->getEventDispatcher()->addSubscriber($logPlugin);
        }

        return $client;
    }
}