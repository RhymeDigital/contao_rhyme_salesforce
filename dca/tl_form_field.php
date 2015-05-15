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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_form_field']['config']['onload_callback'][] = array('Rhyme\Salesforce\Backend\FormField\Callbacks', 'loadSalesforceFields');


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_form_field']['fields']['salesforceField'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['salesforceField'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'default'				  => '',
	'eval'                    => array('tl_class'=>'clr', 'chosen'=>true, 'includeBlankOption'=>true),
	'options_callback'		  => array('Rhyme\Salesforce\Backend\FormField\Callbacks', 'getSObjectFields'),
	'sql'					  => "varchar(255) NOT NULL default ''"
);