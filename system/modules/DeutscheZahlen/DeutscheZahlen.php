<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * DeutscheZahlen
 * Copyright (C) 2010,2011 Tristan Lins
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    DeutscheZahlen
 * @license    LGPL
 * @filesource
 */


/**
 * Class DeutscheZahlen
 *
 * Replaces all digit field with dezimal fields.
 *
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    DeutscheZahlen
 */
class DeutscheZahlen extends System
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
	public function hookAddCustomRegexp($strRegexp, $varValue, Widget $objWidget)
	{
		if ($strRegexp == 'dezimal')
		{
			if (!preg_match('/^[\d \,-]*$/', $varValue))
			{
				$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['digit'], $this->strLabel));
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
		if (TL_MODE == 'BE') {
			$GLOBALS['TL_DCA'][$strName]['config']['onload_callback'][] = array('DeutscheZahlen', 'onload_callback');
		}
	}


	/**
	 * Replace all digit fields with dezimal and add the save and load callbacks.
	 */
	public function onload_callback($dc)
	{
		if (isset($GLOBALS['TL_DCA'][$dc->table]))
		{
			foreach ($GLOBALS['TL_DCA'][$dc->table]['fields'] as $strField=>$arrField)
			{
				if (isset($arrField['eval']) && isset($arrField['eval']['rgxp']) && $arrField['eval']['rgxp']=='digit')
				{
					$GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['eval']['rgxp'] = 'dezimal';
					if (is_array($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['save_callback'])) {
						array_unshift($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['save_callback'], array('DeutscheZahlen', 'save_dezimal'));
					}
					else {
						$GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['save_callback'][] = array('DeutscheZahlen', 'save_dezimal');
					}
					
					$GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['load_callback'][] = array('DeutscheZahlen', 'load_dezimal');
				}
			}
		}
	}


	/**
	 *
	 */
	public function hookCatalogDca($objFields, &$dca, $objCatalog, $objModuleCatalog)
	{
		if (TL_MODE == 'FE') {
			if (!isset($GLOBALS['BE_MOD']['content']['catalog']['fieldTypes']['dezimal'])) {
				$GLOBALS['BE_MOD']['content']['catalog']['fieldTypes']['dezimal'] = array
				(
					'fieldDef'     => array
					(
						'inputType' => 'text',
						'eval'      => array('rgxp' => 'dezimal'),
						'load_callback' => array(array('DeutscheZahlen', 'load_dezimal')),
						'save_callback' => array(array('DeutscheZahlen', 'save_dezimal'))
					)
				);
			}

			foreach ($dca['fields'] as $strField=>$arrField)
			{
				if (isset($arrField['eval']) && isset($arrField['eval']['rgxp']) && $arrField['eval']['rgxp']=='digit')
				{
					$dca['fields'][$strField]['eval']['rgxp']            = 'dezimal';
					$dca['fields'][$strField]['eval']['catalog']['type'] = 'dezimal';
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
