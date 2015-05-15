<?php

/**
 * Copyright (C) 2015 Rhyme Digital, LLC.
 * 
 * @author		Blair Winans <blair@rhyme.digital>
 * @author		Adam Fisher <adam@rhyme.digital>
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\Salesforce;

use Rhyme\Salesforce\Client\Client as SF_Client;
use Rhyme\Salesforce\Client\ClientBuilder as SF_ClientBuilder;
use Rhyme\Salesforce\Model\ApiConfig as SF_ApiConfig;


class Salesforce extends \Controller
{

	/**
	 * Get a Salesforce client object by passing an API
	 *
	 * @access		public
	 * @param		mixed
	 * @return		object|null
	 */
	public static function getClient($varApi)
	{
		if ($varApi instanceof \Database\Result)
		{
			$varApi = new ApiConfig($varApi);
		}
		elseif (is_numeric($varApi))
		{
			$varApi = SF_ApiConfig::findByPk($varApi);
		}
		
		if ($varApi === null || !($varApi instanceof SF_ApiConfig))
		{
			return null;
		}

		$objBuilder = new SF_ClientBuilder(
		  $varApi->wsdlpath ?: TL_ROOT.'/system/modules/salesforce/vendorfiles/soapclient/partner.wsdl.xml', // todo: test custom WSDL path
		  $varApi->username,
		  \Encryption::decrypt($varApi->password),
		  $varApi->token
		);
		
		return $objBuilder->build();
	}


	/**
	 * Format phone numbers for Salesforce
	 *
	 * @access	public
	 * @param 	string
	 * @return 	string
	 */
	public static function formatPhoneNumber($strPhone)
	{
		// todo: check on phone number format in SF, also put this into Client::createSObject
		return '(' . substr($strPhone, 0, 3) . ') ' . substr($strPhone, 3, 3) . '-' . substr($strPhone, 6, 4); // todo: account for other characters and country code
	}

	
	/**
	 * Return a section of a string using a start and end (use "<input" and ">" to get any input elements)
	 * @param string
	 * @param string
	 * @param string
	 * @param boolean
	 * @param integer
	 * @return string
	 */
	public static function getSectionOfString($strSubject, $strStart, $strEnd, $blnCaseSensitive=true, $intSearchStart=0)
	{
		// First index of start string
		$varStart = $blnCaseSensitive ? strpos($strSubject, $strStart, $intSearchStart) : stripos($strSubject, $strStart, $intSearchStart);
		
		if ($varStart === false)
		{
			return false;
		}
		
		// First index of end string
		$varEnd = $blnCaseSensitive ? strpos($strSubject, $strEnd, ($varStart + strlen($strStart))) : stripos($strSubject, $strEnd, ($varStart + strlen($strStart)));
		
		if ($varEnd === false)
		{
			return false;
		}
		
		// Return the string including the start string, end string, and everything in between
		return substr($strSubject, $varStart, ($varEnd + strlen($strEnd) - $varStart));
	}

	
	/**
	 * Remove sections of a string using a start and end (use "[caption" and "]" to remove any caption blocks)
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return string
	 */
	public static function replaceSectionsOfString($strSubject, $strStart, $strEnd, $strReplace='', $blnCaseSensitive=true, $blnRecursive=true)
	{
		// First index of start string
		$varStart = $blnCaseSensitive ? strpos($strSubject, $strStart) : stripos($strSubject, $strStart);
		
		if ($varStart === false)
			return $strSubject;
		
		// First index of end string
		$varEnd = $blnCaseSensitive ? strpos($strSubject, $strEnd, $varStart+1) : stripos($strSubject, $strEnd, $varStart+1);
		
		// The string including the start string, end string, and everything in between
		$strFound = $varEnd === false ? substr($strSubject, $varStart) : substr($strSubject, $varStart, ($varEnd + strlen($strEnd) - $varStart));
		
		// The string after the replacement has been made
		$strResult = $blnCaseSensitive ? str_replace($strFound, $strReplace, $strSubject) : str_ireplace($strFound, $strReplace, $strSubject);
		
		// Check for another occurence of the start string
		$varStart = $blnCaseSensitive ? strpos($strSubject, $strStart) : stripos($strSubject, $strStart);
		
		// If this is recursive and there's another occurence of the start string, keep going
		if ($blnRecursive && $varStart !== false)
		{
			$strResult = static::replaceSectionsofString($strResult, $strStart, $strEnd, $strReplace, $blnCaseSensitive, $blnRecursive);
		}
		
		return $strResult;
	}
}