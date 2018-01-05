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

use Phpforce\SoapClient\Client as Phpforce_Client;

require_once(TL_ROOT.'/composer/vendor/phpforce/soap-client/src/Phpforce/SoapClient/Client.php');


class Client extends Phpforce_Client
{

	/**
	 * Soap namespace
	 * @var string
	 */
    protected $strSoapNamespace = 'urn:partner.soap.sforce.com';


    /**
     * Turn Sobjects into \SoapVars
     * OVERRIDE SOAP NAMESPACE AND ADD "any" LOGIC
     *
     * @param array  $objects Array of objects
     * @param string $type    Object type
     *
     * @return \SoapVar[]
     */
    protected function createSoapVars(array $objects, $type)
    {
        $soapVars = array();

        foreach ($objects as $object) {

            $sObject = $this->createSObject($object, $type);

            // REALLY IMPORTANT ALTERATION. (PARTNER WSDL COMPATIBILITY).
            if (isset($sObject->any)) {                
                foreach ($sObject->any as $key => $value) {
                    $sObject->{$key} = $value;
                }
                unset($sObject->any);
            }

            $xml = '';
            if (isset($sObject->fieldsToNull)) {
                foreach ($sObject->fieldsToNull as $fieldToNull) {
                    $xml .= '<fieldsToNull>' . $fieldToNull . '</fieldsToNull>';
                }
                $fieldsToNullVar = new \SoapVar(new \SoapVar($xml, XSD_ANYXML), SOAP_ENC_ARRAY);
                $sObject->fieldsToNull = $fieldsToNullVar;
            }

            $soapVar = new \SoapVar($sObject, SOAP_ENC_OBJECT, $type, $this->strSoapNamespace);
            $soapVars[] = $soapVar;
        }

        return $soapVars;
    }

    /**
     * Set soap headers
     * OVERRIDE SOAP NAMESPACE
     *
     * @param array $headers
     */
    protected function setSoapHeaders(array $headers)
    {
        $soapHeaderObjects = array();
        foreach ($headers as $key => $value) {
            $soapHeaderObjects[] = new \SoapHeader($this->strSoapNamespace, $key, $value);
        }

        $this->soapClient->__setSoapHeaders($soapHeaderObjects);
    }

    /**
     * Save session id to SOAP headers to be used on subsequent requests
     * OVERRIDE SOAP NAMESPACE
     *
     * @param string $sessionId
     */
    protected function setSessionId($sessionId)
    {
        $this->sessionHeader = new \SoapHeader(
            $this->strSoapNamespace,
            'SessionHeader',
            array(
                'sessionId' => $sessionId
            )
        );
    }

    /**
     * {@inheritdoc}
     * OVERRIDE SOAP NAMESPACE
     */
    public function merge(array $mergeRequests, $type)
    {
        foreach ($mergeRequests as $mergeRequest) {
            if (!($mergeRequest instanceof Request\MergeRequest)) {
                throw new \InvalidArgumentException(
                    'Each merge request must be an instance of MergeRequest'
                );
            }

            if (!$mergeRequest->masterRecord || !is_object($mergeRequest->masterRecord)) {
                throw new \InvalidArgumentException('masterRecord must be an object');
            }

            if (!$mergeRequest->masterRecord->Id) {
                throw new \InvalidArgumentException('Id for masterRecord must be set');
            }

            if (!is_array($mergeRequest->recordToMergeIds)) {
                throw new \InvalidArgumentException('recordToMergeIds must be an array');
            }

            $mergeRequest->masterRecord = new \SoapVar(
                $this->createSObject($mergeRequest->masterRecord, $type),
                SOAP_ENC_OBJECT,
                $type,
                $this->strSoapNamespace
            );
        }

        return $this->call(
            'merge',
            array('request' => $mergeRequests)
        );
    }

    /**
     * Create a Salesforce object
     * OVERRIDE TO FIX MISSING TYPES
     *
     * Converts PHP \DateTimes to their SOAP equivalents.
     *
     * @param object $object     Any object with public properties
     * @param string $objectType Salesforce object type
     *
     * @return object
     */
    protected function createSObject($object, $objectType)
    {
        $sObject = new \stdClass();

        foreach (get_object_vars($object) as $field => $value) {
            $type = $this->soapClient->getSoapElementType($objectType, $field);
            if (!$type) {
                //continue;
                $type = 'string';
            }

            if ($value === null) {
                $sObject->fieldsToNull[] = $field;
                continue;
            }

            // As PHP \DateTime to SOAP dateTime conversion is not done
            // automatically with the SOAP typemap for sObjects, we do it here.
            switch ($type) {
                case 'date':
                    if ($value instanceof \DateTime) {
                        $value  = $value->format('Y-m-d');
                    }
                    break;
                case 'dateTime':
                    if ($value instanceof \DateTime) {
                        $value  = $value->format('Y-m-d\TH:i:sP');
                    }
                    break;
                case 'base64Binary':
                    $value = base64_encode($value);
                    break;
            }

            $sObject->{$field} = $value;
        }

        return $sObject;
    }
}