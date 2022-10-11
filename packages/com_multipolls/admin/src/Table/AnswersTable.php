<?php

namespace DL\Component\Multipolls\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

\defined('JPATH_PLATFORM') or die;

class AnswersTable extends Table
{
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__multipolls_answers', 'id', $db);
	}
}