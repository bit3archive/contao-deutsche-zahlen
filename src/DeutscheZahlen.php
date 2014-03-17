<?php

/**
 * DeutscheZahlen extension for the Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG <http://bit3.de>
 *
 * @copyright bit3 UG 2013
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @package   bit3/contao-deutsche-zahlen
 * @license   LGPL-3.0+
 * @link      http://bit3.de
 */

namespace Bit3\Contao\DeutscheZahlen;

/**
 * Class DeutscheZahlen
 *
 * Replaces all digit field with dezimal fields.
 */
class DeutscheZahlen
{
	/**
	 * @var DeutscheZahlen
	 */
	protected static $objInstance = null;

	/**
	 * Get singleton object.
	 *
	 * @return DeutscheZahlen
	 */
	public static function getInstance()
	{
		if (self::$objInstance === null) {
			self::$objInstance = new DeutscheZahlen();
		}
		return self::$objInstance;
	}

	/**
	 * Add the custom regexp "dezimal" to Contao.
	 */
	public function hookAddCustomRegexp($strRegexp, $varValue, \Widget $objWidget)
	{
		if ($strRegexp == 'dezimal')
		{
			if (!preg_match('/^\-?\d+(,\d+)?$/', trim($varValue)))
			{
				$objWidget->addError(sprintf($GLOBALS['TL_LANG']['ERR']['digit'], $objWidget->label));
			}
			return true;
		}
		return false;
	}

	/**
	 * Add the onload callback to a loaded data container.
	 */
	public function hookLoadDataContainer($strName)
	{
		if (TL_MODE == 'BE' && $GLOBALS['TL_LANGUAGE'] == 'de') {
			$GLOBALS['TL_DCA'][$strName]['config']['onload_callback'][] = array('Bit3\Contao\DeutscheZahlen\DeutscheZahlen', 'onload_callback');
		}
	}

	/**
	 * Replace all digit fields with dezimal and add the save and load callbacks.
	 */
	public function onload_callback($dc)
	{
		if (isset($GLOBALS['TL_DCA'][$dc->table]) &&
			isset($GLOBALS['TL_DCA'][$dc->table]['fields']) &&
			is_array($GLOBALS['TL_DCA'][$dc->table]['fields']))
		{
			foreach ($GLOBALS['TL_DCA'][$dc->table]['fields'] as $strField=>$arrField)
			{
				if (isset($arrField['eval']) && isset($arrField['eval']['rgxp']) && $arrField['eval']['rgxp']=='digit')
				{
					$GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['eval']['rgxp'] = 'dezimal';
					if (is_array($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['save_callback'])) {
						array_unshift($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['save_callback'], array('Bit3\Contao\DeutscheZahlen\DeutscheZahlen', 'save_dezimal'));
					}
					else {
						$GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['save_callback'][] = array('Bit3\Contao\DeutscheZahlen\DeutscheZahlen', 'save_dezimal');
					}

					$GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['load_callback'][] = array('Bit3\Contao\DeutscheZahlen\DeutscheZahlen', 'load_dezimal');
				}
			}
		}
	}

	/**
	 * Save a dezimal by converting it into a digit value.
	 */
	public function save_dezimal($strValue)
	{
		return str_replace(',', '.', $strValue);
	}

	/**
	 * Load a dezimal by converting it from a digit value.
	 */
	public function load_dezimal($strValue)
	{
		return str_replace('.', ',', $strValue);
	}
}
