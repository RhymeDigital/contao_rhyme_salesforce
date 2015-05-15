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
$GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'][]		= 'useSalesforce';
$GLOBALS['TL_DCA']['tl_form']['palettes']['default'] = str_replace('{expert_legend:hide}', '{salesforce_legend:hide},useSalesforce;{expert_legend:hide}', $GLOBALS['TL_DCA']['tl_form']['palettes']['default']);


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['useSalesforce'] = 'salesforceAPIConfig,salesforceSObject'; 


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_form']['fields']['useSalesforce'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form']['useSalesforce'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr', 'submitOnChange'=>true),
	'sql'					  => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_form']['fields']['salesforceAPIConfig'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form']['salesforceAPIConfig'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'eval'                    => array('tl_class'=>'clr w50', 'submitOnChange'=>true),
	'foreignKey'		  	  => 'tl_salesforce_apiconfig.name',
	'sql'					  => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_form']['fields']['salesforceSObject'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form']['salesforceSObject'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'default'				  => '',
	'eval'                    => array('tl_class'=>'w50', 'includeBlankOption'=>true),
	'options_callback'		  => array('Rhyme\Salesforce\Backend\Form\Callbacks', 'getSObjectOptions'),
	'sql'					  => "varchar(255) NOT NULL default ''"
);