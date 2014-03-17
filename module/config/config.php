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


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['addCustomRegexp'][]   = array('Bit3\Contao\DeutscheZahlen\DeutscheZahlen', 'hookAddCustomRegexp');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Bit3\Contao\DeutscheZahlen\DeutscheZahlen', 'hookLoadDataContainer');
