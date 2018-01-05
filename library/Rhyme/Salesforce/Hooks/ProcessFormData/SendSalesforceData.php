<?php

/**
 * Copyright (C) 2014 HB Agency
 * 
 * @author		Blair Winans <bwinans@hbagency.com>
 * @author		Adam Fisher <afisher@hbagency.com>
 * @link		http://www.hbagency.com
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Salesforce\Hooks\ProcessFormData;

use Rhyme\Salesforce\Salesforce;


class SendSalesforceData extends \Frontend
{

	/**
	 * Salesforce client
	 * @var object
	 */
	protected $objClient;

	/**
	 * Connect to Salesforce.com 
	 *
	 * @access		protected
	 * @return		void
	 */
	protected function connectToSalesforce($intConfig)
	{
		try
		{
			if ($this->objClient === null)
			{
				\System::log('Send data to Salesforce: CONNECT', __METHOD__, TL_FORMS);
	    		$this->objClient = Salesforce::getClient($intConfig);
			}
    		return true;
        } 
        catch(\Exception $e)
        {
            \System::log('Salesforce.com error: ' . $e->getMessage(), __METHOD__, TL_ERROR);
        }
        
        return false;
	}
	
	

	/**
	 * Send data submissions to Salesforce
	 * 
	 * Class:		Form
	 * Method:		processFormData
	 * Hook:		$GLOBALS['TL_HOOKS']['processFormData']
	 *
	 * @access		public
	 * @param		array
	 * @param		array
	 * @param		array
	 * @param		array
	 * @return		void
	 */
	public function run($arrSubmitted, $arrFormData, $arrFiles, $arrLabels, $objForm)
	{
	    try
		{
			if ($objForm->useSalesforce && $objForm->salesforceAPIConfig && $objForm->salesforceSObject)
			{
			    if ($this->connectToSalesforce($objForm->salesforceAPIConfig))
	            {
	            	$arrSObjectConfig = $GLOBALS['TL_SOBJECTS'][$objForm->salesforceSObject];
	            	
	            	if (is_array($arrSObjectConfig) && count($arrSObjectConfig) && $arrSObjectConfig['class'] && class_exists($arrSObjectConfig['class']))
	            	{
	            		$strMethod = 'create'; // Todo: Maybe we make this an option?  create, update, etc.
	            		$strClass = $arrSObjectConfig['class'];
	            		$arrData = static::getSalesforceFields($arrSubmitted, $objForm->id);
	            		
	            		// todo: check mandatory fields for different object types
	            		
				        // !HOOK: alter Salesforce data before sending
				        if (isset($GLOBALS['TL_HOOKS']['preSendSalesforceData']) && is_array($GLOBALS['TL_HOOKS']['preSendSalesforceData'])) {
				            foreach ($GLOBALS['TL_HOOKS']['preSendSalesforceData'] as $callback) {
				                $objCallback = \System::importStatic($callback[0]);
				                $arrData = $objCallback->{$callback[1]}($strClass, $arrData, $arrSubmitted, $arrFormData, $arrFiles, $arrLabels, $objForm, $this->objClient, $strMethod);
				            }
				        }
	            		
	            		// Create the Salesforce object
		    			$objSObject = new $strClass($arrData);
	            		
	            		// Send the data to Salesforce
		    			$response = $this->objClient->$strMethod(array($objSObject), $strClass::getType());
	            		
				        // !HOOK: execute custom actions after sending data to Salesforce
				        if (isset($GLOBALS['TL_HOOKS']['postSendSalesforceData']) && is_array($GLOBALS['TL_HOOKS']['postSendSalesforceData'])) {
				            foreach ($GLOBALS['TL_HOOKS']['postSendSalesforceData'] as $callback) {
				                $objCallback = \System::importStatic($callback[0]);
				                $objCallback->{$callback[1]}($response, $objSObject, $arrData, $arrSubmitted, $arrFormData, $arrFiles, $arrLabels, $objForm, $this->objClient, $strMethod);
				            }
				        }
		    			
		    			if ($response[0]->isSuccess())
		    			{
		    				\System::log('Salesforce '.$objForm->salesforceSObject.' (ID '.$response[0]->getId().') successful.', __METHOD__, TL_FORMS);
		    			}
		    			else
		    			{
							$errors = $response[0]->getErrors();
		    				\System::log('Salesforce object creation failed: ' . $errors[0]->getMessage(), __METHOD__, TL_ERROR);
		    			}
	            	}
	            }
			}
			
        }
        catch (\Exception $e)
		{
			\System::log('Failed to send data to Salesforce: ' . $e->getMessage(), __METHOD__, TL_ERROR);
		}
		
		return false;
    }

	/**
	 * Get the data ready for Salesforce
	 *
	 * @access	protected
	 * @param 	array
	 * @param 	integer
	 * @return 	array
	 */
	protected static function getSalesforceFields($arrSubmitted, $intFormId)
	{
		$arrReturn = array();
		$objFormFields = \FormFieldModel::findPublishedByPid($intFormId);
		
		if ($objFormFields !== null)
		{
			while ($objFormFields->next())
			{
				if ($objFormFields->current()->salesforceField)
				{
					$arrReturn[$objFormFields->current()->salesforceField] = $arrSubmitted[$objFormFields->current()->name];
				}
			}
		}
		
		return $arrReturn;
	}

	/**
	 * Use output buffer to var dump to a string
	 * 
	 * @param	string
	 * @return	string 
	 */
	public static function varDumpToString($var)
	{
		ob_start();
		var_dump($var);
		$result = ob_get_clean();
		return $result;
	}
	
	
	

}
