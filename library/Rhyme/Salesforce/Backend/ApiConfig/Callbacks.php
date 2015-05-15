<?php

/**
 * Copyright (C) 2015 Rhyme Digital, LLC.
 * 
 * @author		Blair Winans <blair@rhyme.digital>
 * @author		Adam Fisher <adam@rhyme.digital>
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Salesforce\Backend\ApiConfig;

use Rhyme\Salesforce\Salesforce;


class Callbacks extends \Backend
{
	
	/**
	 * Generate an alias for the API configuration
	 * @param mixed
	 * @param object
	 * @return string
	 */
	public function generateAlias($varValue, \DataContainer $dc)
	{
		$autoAlias = false;

		// Generate alias if there is none
		if (!strlen($varValue))
		{
			$autoAlias = true;
			$varValue = standardize($dc->activeRecord->name);
		}

		$objAlias = \Database::getInstance()->prepare("SELECT id FROM tl_salesforce_apiconfig WHERE alias=?")
								   ->execute($varValue);

		// Check whether the alias exists
		if ($objAlias->numRows > 1 && !$autoAlias)
		{
			throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
		}

		// Add ID to alias
		if ($objAlias->numRows && $autoAlias)
		{
			$varValue .= '-' . $dc->id;
		}

		return $varValue;
	}
	
	/**
	 * Encrypt password on save
	 * @param mixed
	 * @param object
	 * @return string
	 */
	public function savePassword($varValue, \DataContainer $dc)
	{
		return $varValue ? \Encryption::encrypt($varValue) : $varValue;
	}
	
	/**
	 * Decrypt password on load
	 * @param mixed
	 * @param object
	 * @return string
	 */
	public function loadPassword($varValue, \DataContainer $dc)
	{
		return $varValue ? \Encryption::decrypt($varValue) : $varValue;
	}
}