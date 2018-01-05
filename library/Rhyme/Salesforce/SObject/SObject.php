<?php

/**
 * Copyright (C) 2015 Rhyme Digital, LLC.
 * 
 * @author		Blair Winans <blair@rhyme.digital>
 * @author		Adam Fisher <adam@rhyme.digital>
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Salesforce\SObject;

use Rhyme\Salesforce\Salesforce;


class SObject
{

	/**
	 * Object type
	 * @var string
	 */
	protected static $strType = '';


	/**
	 * Load the object
	 * @param	array
	 */
	public function __construct($varData=null, $blnPhpforceObject=false)
	{
		if ($varData)
		{
			if ($blnPhpforceObject)
			{
				$varData = static::parsePhpforceObject($varData);
			}
			
			if (is_object($varData))
			{
				$arrProperties = get_object_vars($varData);
				
				foreach ($arrProperties as $property=>$value)
				{
					$this->{$property} = is_string($value) ? str_replace('&', '+', $value) : $value;
				}
			}
			elseif (is_array($varData))
			{
				foreach ($varData as $property=>$value)
				{
					$this->{$property} = is_string($value) ? str_replace('&', '+', $value) : $value;
				}
			}
		}
	}


	/**
	 * Return the data
	 *
	 * @return 	array The data array
	 */
	public function getData()
	{
		return get_object_vars($this);
	}


	/**
	 * Return the obejct type
	 *
	 * @return 	string The obejct type
	 */
	public static function getType()
	{
		$arrClass = explode('\\', get_called_class());
		return static::$strType ?: $arrClass[count($arrClass)-1];
	}


	/**
	 * Parse a query result to create an SObject
	 *
	 * @access	public
	 * @param 	string
	 * @param 	string
	 * @return 	string
	 */
	public static function parsePhpforceObject($varResult)
	{
		$objSObject = new \stdClass();
		$varResult = (array)get_object_vars($varResult);
		
		foreach ($varResult as $key=>$val)
		{
			switch (strtolower($key))
			{
				case 'id':
					$objSObject->Id = strval(is_array($val) ? $val[0] : $val);
					break;
					
				case 'any':
					$arrAny = !is_array($val) ? array($val) : $val;
					foreach ($arrAny as $anykey=>$anyval)
					{
						try
						{
							// Objects or compound fields such as "Owner" or "MailingAddress" have keys that are strings
							if (!is_numeric($anykey))
							{
								if (is_string($anyval))
								{
									$objSObject->{$anykey} = static::getAllPropertiesFromAnyString($anyval);
								}
								elseif (is_object($anyval))
								{
									$objSObject->{$anykey} = static::parsePhpforceObject($anyval);
								}
							}
							// Regular return values have keys that are integers
							else
							{
								$objSObject = static::getAllPropertiesFromAnyString($anyval, $objSObject);
							}
						}
						catch (\Exception $e) {}
					}
					break;
					
				default:
					$objSObject->{$key} = $val;
					break;
			}
		}
		
		return $objSObject;
	}


	/**
	 * Get a property value from an "any" XML property
	 *
	 * @access	public
	 * @param 	string
	 * @param 	string
	 * @return 	string
	 */
	public static function getAllPropertiesFromAnyString($strAny, $objSObject=null)
	{
	    $intOffset = 0;
	    $arrPositions = array();
		$objSObject = $objSObject ?: new \stdClass();
    
	    while (($pos = strpos($strAny, '<sf:', $intOffset)) !== false)
	    {
	        $intOffset = $pos + 1;
	        $arrPositions[] = $pos;
	    }
	    
	    for ($i = 0; $i < count($arrPositions); $i++)
	    {
	    	$strField = str_replace(array('<sf:',' xsi:nil="true"/','>'), '', strval(Salesforce::getSectionOfString($strAny, "<sf:", ">", true, $arrPositions[$i])));
	    	
	    	if (trim($strField))
	    	{
			    $objSObject->{$strField} = static::getPropertyValueFromAnyString($strAny, $strField);
	    	}
	    }
		
		return $objSObject;
	}


	/**
	 * Get a property value from an "any" XML property
	 *
	 * @access	public
	 * @param 	string
	 * @param 	string
	 * @return 	string
	 */
	public static function getPropertyValueFromAnyString($strAny, $strTagName)
	{
		return str_ireplace(array("<sf:$strTagName>", "</sf:$strTagName>"), '', strval(Salesforce::getSectionOfString($strAny, "<sf:$strTagName>", "</sf:$strTagName>")));
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