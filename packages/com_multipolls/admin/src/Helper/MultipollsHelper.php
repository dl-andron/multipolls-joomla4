<?php

namespace DL\Component\Multipolls\Administrator\Helper;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\ParameterType;

\defined('_JEXEC') or die;

class MultipollsHelper extends ContentHelper
{
    /**
     * Устанавливает новые языки
     *
     * Сверяет языки контента Joomla и языки компонента.
     * Если не совпадают - в таблицах БД добавляет недостающие поля
     *
     * @return void
     *
     */
    public static function installNewLanguages()
    {
        $app =  Factory::getApplication();
        $db = Factory::getDbo();

        $joomlaLanguages = LanguageHelper::getLanguages();

        $installedLanguages = self::getInstalledLanguages();

	    $db->transactionStart();

        foreach($joomlaLanguages as $lang)
        {
            if (!in_array($lang->lang_code, $installedLanguages))
            {
                $query = 'ALTER TABLE `#__multipolls_polls`
                    ADD `name_'.$lang->lang_code.'` varchar(500) NOT NULL,
                    ADD `text_'.$lang->lang_code.'` text NOT NULL';

                try
                {
                    $db->setQuery($query)->execute();
                }
                catch (\Exception $e)
                {
	                $db->transactionRollback();
                    $app->enqueueMessage(Text::sprintf('COM_MULTIPOLLS_LANGUAGE_INSTALLATION_ERROR', $e->getMessage()), 'warning');
                }

                $query = 'ALTER TABLE `#__multipolls_questions` 
                    ADD `name_'.$lang->lang_code.'` varchar(500) NOT NULL, 
                    ADD `name_own_'.$lang->lang_code.'` varchar(300)';

	            try
	            {
		            $db->setQuery($query)->execute();
	            }
	            catch (\Exception $e)
	            {
		            $db->transactionRollback();
		            $app->enqueueMessage(Text::sprintf('COM_MULTIPOLLS_LANGUAGE_INSTALLATION_ERROR', $e->getMessage()), 'warning');
	            }

				$query = 'ALTER TABLE `#__multipolls_answers` ADD `name_'.$lang->lang_code.'` varchar(500) NOT NULL';

	            try
	            {
		            $db->setQuery($query)->execute();
	            }
	            catch (\Exception $e)
	            {
		            $db->transactionRollback();
		            $app->enqueueMessage(Text::sprintf('COM_MULTIPOLLS_LANGUAGE_INSTALLATION_ERROR', $e->getMessage()), 'warning');
	            }

	            $query = $db->getQuery(true);

	            $values = [':language', ':name', 1];

	            $query->insert($db->quoteName('#__multipolls_langs'))
		            ->columns($db->quoteName(['language', 'name', 'published']))
		            ->values(implode(',', $values));
	            $query->bind(':language',$lang->lang_code, ParameterType::STRING)
		            ->bind(':name', $lang->title, ParameterType::STRING);

	            try
	            {
		            $db->setQuery($query)->execute();
	            }
	            catch (\Exception $e)
	            {
		            $db->transactionRollback();
		            $app->enqueueMessage(Text::sprintf('COM_MULTIPOLLS_LANGUAGE_INSTALLATION_ERROR', $e->getMessage()), 'warning');
	            }

                $app->enqueueMessage(Text::sprintf('COM_MULTIPOLLS_LANGUAGE_INSTALLED', $lang->title), 'notice');
            }
        }

	    $db->transactionCommit();
    }

	/**
	 * Получает установленные в компоненте языки
	 *
	 * @return array
	 *
	 */
	public static function getInstalledLanguages()
	{
		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select('language')
			->from($db->quoteName('#__multipolls_langs'));
		$db->setQuery($query);

		return $db->loadColumn();
	}

	/**
	 * Получает опубликованные в компоненте языки
	 *
	 * @return array
	 *
	 */
	public static function getPublishedLanguages()
	{
		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select('language')
			->from($db->quoteName('#__multipolls_langs'))
			->where($db->quoteName('published') . ' = 1');
		$db->setQuery($query);

		return $db->loadColumn();
	}
}
