<?php

/**
 * Copyright (C) 2015 Rhyme Digital, LLC.
 * 
 * @author		Blair Winans <blair@rhyme.digital>
 * @author		Adam Fisher <adam@rhyme.digital>
 * @link		http://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD'], 1, array(
	'salesforce' => array(
		'salesforce_apiconfigs' => array
		(
			'tables' => array('tl_salesforce_apiconfig'),
			'icon'   => 'system/modules/salesforce/assets/img/api-config.png'
		)
	)
));


/**
 * Models
 */
$GLOBALS['TL_MODELS'][\Rhyme\Salesforce\Model\ApiConfig::getTable()] 		= 'Rhyme\Salesforce\Model\ApiConfig';


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['processFormData'][]			= array('Rhyme\Salesforce\Hooks\ProcessFormData\SendSalesforceData', 'run');


/**
 * Salesforce objects
 */
$GLOBALS['TL_SOBJECTS']['Account'] = array
(
	'class'			=> 'Rhyme\Salesforce\SObject\Account',
	'label'			=> &$GLOBALS['TL_LANG']['MSC']['salesforceObjectAccount'],
);
$GLOBALS['TL_SOBJECTS']['Case'] = array
(
	'class'			=> 'Rhyme\Salesforce\SObject\Case',
	'label'			=> &$GLOBALS['TL_LANG']['MSC']['salesforceObjectCase'],
);
$GLOBALS['TL_SOBJECTS']['Contact'] = array
(
	'class'			=> 'Rhyme\Salesforce\SObject\Contact',
	'label'			=> &$GLOBALS['TL_LANG']['MSC']['salesforceObjectContact'],
);
$GLOBALS['TL_SOBJECTS']['Lead'] = array
(
	'class'			=> 'Rhyme\Salesforce\SObject\Lead',
	'label'			=> &$GLOBALS['TL_LANG']['MSC']['salesforceObjectLead'],
);
$GLOBALS['TL_SOBJECTS']['User'] = array
(
	'class'			=> 'Rhyme\Salesforce\SObject\User',
	'label'			=> &$GLOBALS['TL_LANG']['MSC']['salesforceObjectUser'],
);
$GLOBALS['TL_SOBJECTS']['Profile'] = array
(
    'class'			=> 'Rhyme\Salesforce\SObject\Profile',
    'label'			=> &$GLOBALS['TL_LANG']['MSC']['salesforceObjectProfile'],
);