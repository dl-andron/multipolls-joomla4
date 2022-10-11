<?php

namespace DL\Component\Multipolls\Administrator\Model;

use Exception;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

\defined('_JEXEC') or die;

class LangModel extends AdminModel
{
	public function getForm($data = array(), $loadData = false)
	{

	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  bool|Table  A Table object
	 *
	 * @since   4.0.0

	 * @throws  Exception
	 */
	public function getTable( $type = 'langs', $prefix = '', $config = array( ) ) {
		return parent::getTable($type, $prefix, $config);
	}
}