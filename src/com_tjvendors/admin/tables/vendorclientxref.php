<?php

/**
 * @version    SVN:
 * @package    Com_Tjvendors
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * vendor Table class
 *
 * @since  1.6
 */
class TjvendorsTablevendorclientxref extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		$tableName = 'TjvendorsTablevendorclientxref';
		JObserverMapper::addObserverClassToClass('JTableObserverContenthistory', $tableName, array('typeAlias' => 'com_tjvendors.vendorclientxref'));
		parent::__construct('#__vendor_client_xref', 'vendor_id', $db);
	}
}
