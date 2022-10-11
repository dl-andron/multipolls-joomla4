<?php

use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Factory;

class com_multipollsInstallerScript
{
    /**
     * Constructor
     *
     * @param   InstallerAdapter  $adapter  The object responsible for running this script
     */
    public function __construct(InstallerAdapter $adapter)
    {
    }
    
    /**
     * Called before any type of action
     *
     * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
     * @param   InstallerAdapter  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function preflight($route, InstallerAdapter $adapter)
    {
        return true;
    }
    
    /**
     * Called after any type of action
     *
     * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
     * @param   InstallerAdapter  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function postflight($route, $adapter)
    {
        return true;
    }
    
    /**
     * Called on installation
     *
     * @param   InstallerAdapter  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function install(InstallerAdapter $adapter)
    {
        $app = Factory::getApplication();
        $joomlaLanguages = LanguageHelper::getLanguages();

        $db = Factory::getDbo();

        $sqlPolls = [];
        $sqlQuestionAnswers = [];
        $sqlQuestionOwnAnswers = [];
        $values = [];

        foreach ($joomlaLanguages as $lang)
        {
            $values[] = "{$db->quote($lang->lang_code)}, {$db->quote($lang->title)}, 1";
            $sqlPolls[] = 'ADD `name_'.$lang->lang_code.'` varchar(500) NOT NULL, ADD `text_'.$lang->lang_code.'` text NOT NULL';
            $sqlQuestionAnswers[] = 'ADD `name_'.$lang->lang_code.'` varchar(500) NOT NULL';
            $sqlQuestionOwnAnswers[] = 'ADD `name_own_'.$lang->lang_code.'` varchar(300)';
        }

        $query = 'ALTER TABLE `#__multipolls_polls` '.implode(",", $sqlPolls);

        try
        {
            $db->setQuery($query)->execute();
        }
        catch (Exception $e)
        {
            $app->enqueueMessage("Error install new languages:<br>". $e->getMessage(), 'warning');
        }

        $query = 'ALTER TABLE `#__multipolls_questions` '.
            implode(",", $sqlQuestionAnswers).','.
            implode(",", $sqlQuestionOwnAnswers);

        try
        {
            $db->setQuery($query)->execute();
        }
        catch (Exception $e)
        {
            $app->enqueueMessage("Error install new languages:<br>". $e->getMessage(), 'warning');
        }

        $query = 'ALTER TABLE `#__multipolls_answers` '.implode(",", $sqlQuestionAnswers);

        try
        {
            $db->setQuery($query)->execute();
        }
        catch (Exception $e)
        {
            $app->enqueueMessage("Error install new languages:<br>". $e->getMessage(), 'warning');
        }

        $query = $db->getQuery(true);

        $query->insert($db->quoteName('#__multipolls_langs'))
            ->columns($db->quoteName(['language', 'name', 'published']))
            ->values($values);

        try
        {
            $db->setQuery($query)->execute();
        }
        catch (Exception $e)
        {
            $app->enqueueMessage($e->getMessage(), 'warning');
        }

        return true;
    }
    
    /**
     * Called on update
     *
     * @param   InstallerAdapter  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function update(InstallerAdapter $adapter)
    {
        return true;
    }
    
    /**
     * Called on uninstallation
     *
     * @param   InstallerAdapter  $adapter  The object responsible for running this script
     */
    public function uninstall(InstallerAdapter $adapter)
    {
        return true;
    }
}

?>