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
 * Load tl_content language file
 */
\System::loadLanguageFile('tl_content');



/**
 * Table tl_salesforce_apiconfig
 */
$GLOBALS['TL_DCA']['tl_salesforce_apiconfig'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'alias' => 'index'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'flag'					  => 1,
			'fields'                  => array('name'),
			'panelLayout'             => 'filter;sort,search,limit',
		),
		'label' => array
		(
			'fields'                  => array('name'),
			'format'                  => '%s'
		),
		'global_operations' => array
		(
			/*'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"',
			)*/
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_salesforce_apiconfig']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_salesforce_apiconfig']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif',
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_salesforce_apiconfig']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_salesforce_apiconfig']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{general_legend},name,alias;{salesforce_apiconfig_legend},api_config,username,password,token,wsdlpath'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql' 					  => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'					  => "int(10) unsigned NOT NULL default '0'"
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_salesforce_apiconfig']['name'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_salesforce_apiconfig']['alias'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'alnum', 'unique'=>true, 'spaceToUnderscore'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'save_callback' => array
			(
				array('Rhyme\Salesforce\Backend\ApiConfig\Callbacks', 'generateAlias')
			),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'username' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_salesforce_apiconfig']['username'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'password' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_salesforce_apiconfig']['password'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'hideInput'=>true, 'preserveTags'=>true, 'minlength'=>Config::get('minPasswordLength'), 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''",
			'save_callback' => array
			(
				array('Rhyme\Salesforce\Backend\ApiConfig\Callbacks', 'savePassword')
			),
			'load_callback' => array
			(
				array('Rhyme\Salesforce\Backend\ApiConfig\Callbacks', 'loadPassword')
			),
		),
		'token' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_salesforce_apiconfig']['token'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'wsdlpath' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_salesforce_apiconfig']['wsdlpath'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'clr long'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
	)
);



?>