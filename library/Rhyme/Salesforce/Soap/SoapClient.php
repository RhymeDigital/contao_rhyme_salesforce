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

use Phpforce\SoapClient\Soap\SoapClient as Phpforce_SoapClient;

require_once(TL_ROOT.'/composer/vendor/phpforce/soap-client/src/Phpforce/SoapClient/Soap/SoapClient.php');


class SoapClient extends Phpforce_SoapClient
{

    /**
     * Get SOAP elements for a complexType
     * !! Workaround for missing Id on update bug !!
     *
     * @param string $complexType Name of SOAP complexType
     *
     * @return array  Names of elements and their types
     */
    public function getSoapElements($complexType)
    {
        $types = $this->getSoapTypes();
        if (isset($types[$complexType])) {
	        //if (isset($types['sObject']['Id'])) {
	        //    $types[$complexType]['Id'] = $types['sObject']['Id'];
	        //}
            return $types[$complexType];
        }
    }

    /**
     * Get a SOAP type’s element
     * !! Workaround for missing Id on update bug !!
     *
     * @param string $complexType Name of SOAP complexType
     * @param string $element     Name of element belonging to SOAP complexType
     *
     * @return string
     */
    public function getSoapElementType($complexType, $element)
    {
        $elements = $this->getSoapElements($complexType);
        $elements['Id'] = 'string';
        if ($elements && isset($elements[$element])) {
            return $elements[$element];
        }
    }
}