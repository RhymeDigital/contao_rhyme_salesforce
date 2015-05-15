<?php

/**
 * Copyright (C) 2015 Rhyme Digital, LLC.
 * 
 * @author		Blair Winans <blair@rhyme.digital>
 * @author		Adam Fisher <adam@rhyme.digital>
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Salesforce\Backend\Form;

use Rhyme\Salesforce\Salesforce;


class Callbacks extends \Backend
{
	
	/**
	 * Get all SObject types for the form
	 * @return array
	 */
	public function getSObjectOptions()
	{
		$arrOptions = array();
		
		foreach ((array)$GLOBALS['TL_SOBJECTS'] as $key=>$val)
		{
			$arrOptions[$key] = $val['label'] ?: $key;
		}
		
		return $arrOptions;
	}
}