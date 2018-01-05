<?php

/**
 * Copyright (C) 2015 Rhyme Digital, LLC.
 * 
 * @author		Blair Winans <blair@rhyme.digital>
 * @author		Adam Fisher <adam@rhyme.digital>
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Salesforce\Backend\FormField;

use Rhyme\Salesforce\Salesforce;


class Callbacks extends \Backend
{
	
	/**
	 * Get all SObject types for the form
	 * @param object
	 * @return array
	 */
	public function loadSalesforceFields($dc)
	{
		$objFormField = \FormFieldModel::findByPk($dc->id);
		if ($objFormField === null) return;
		
		$objForm = \FormModel::findByPk($objFormField->pid);
		if ($objForm === null) return;
		
		if ($objForm->useSalesforce && $objForm->salesforceAPIConfig && $objForm->salesforceSObject)
		{
			foreach ($GLOBALS['TL_DCA']['tl_form_field']['palettes'] as $key=>$val)
			{
				if ($key == '__selector__') continue;
				
				$GLOBALS['TL_DCA']['tl_form_field']['palettes'][$key] .= ';{salesforce_legend},salesforceField';
			}
		}
	}
	
	/**
	 * Get fields for the form's SObject type
	 * @param object
	 * @return array
	 */
	public function getSObjectFields($dc)
	{
		$arrOptions = array();
		
		$objFormField = \FormFieldModel::findByPk($dc->id);
		if ($objFormField === null) return $arrOptions;
		
		$objForm = \FormModel::findByPk($objFormField->pid);
		if ($objForm === null) return $arrOptions;
		
		if ($objForm->useSalesforce && $objForm->salesforceAPIConfig && $objForm->salesforceSObject)
		{
			try
			{
				$objClient = Salesforce::getClient($objForm->salesforceAPIConfig);
				$arrConfig = $GLOBALS['TL_SOBJECTS'][$objForm->salesforceSObject];
				$strClass = $arrConfig['class'];
				
				if (class_exists($strClass))
				{
					$objResults = $objClient->describeSObjects(array($strClass::getType()));
					$objResult = $objResults[0];
					$arrFields = $objResult->getFields();
					
					foreach ($arrFields as $field)
					{
						if (!$field->isCreateable()) continue;
						
						$arrOptions[$field->getName()] = $field->getName();
					}
				}
			}
			catch (\Exception $e)
			{
				
			}
		}
		
		return $arrOptions;
	}
}