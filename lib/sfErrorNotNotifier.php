<?php

class sfErrorNotNotifier
{
    /**
     * Listens raised exceptions.
     *
     * @param sfEvent An sfEvent instance
     */
    static public function handleExceptionEvent(sfEvent $event)
    {
        $client = sfProjectConfiguration::getActive()->getPluginConfiguration('sfErrorNotNotifierPlugin')->getClient();
        $exception = $event->getSubject();
        $client->notifyException($exception);
    }

    /**
     * Listens log messages.
     *
     * @param sfEvent An sfEvent instance
     */
    static public function handleLogEvent(sfEvent $event)
    {
        // a raised exception may generate 2 events: 1 exception event (which is handled by the method
        // handleExceptionEvent), and 1 log event (handled by this method, but we don't want it,
        // so we must chech the origin of the event)
        if ($event->getSubject() instanceof Exception)
        {
            return;
        }

        // Check if there is a log level
        $params = $event->getParameters();
        if (isset($params['priority']))
        {
            // Is level high enough ?
            $log_level_str = sfConfig::get('app_sf_errornot_notifier_plugin_log_level', 'err');
            $log_level = constant('sfLogger::'.strtoupper($log_level_str));
            if ($params['priority'] <= $log_level)
            {
                $client = sfProjectConfiguration::getActive()->getPluginConfiguration('sfErrorNotNotifierPlugin')->getClient();
                $priority_str = sfLogger::getPriorityName($params['priority']);
                $exception = new sfException("'[$priority_str] " . $params[0] . "'");
                $client->notifyException($exception);
            }
        }
    }
}