<?php

/**
 * sfErrorNotNotifierPlugin configuration.
 *
 * @package SfErrorNotNotifierPlugin
 */
class sfErrorNotNotifierPluginConfiguration extends sfPluginConfiguration
{
    /**
     * ErrorNot client instance
     *
     * @var Services_ErrorNot
     * @see http://github.com/francois2metz/php-errornot
     */
    private $client;

    /**
     * Returns current configured ErrorNot client instance.
     *
     * @return Services_ErrorNot
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Initializes ErrorNot client.
     *
     * @see sfPluginConfiguration
     */
    public function initialize()
    {
        if (sfConfig::get('app_errornot_notifier_plugin_enabled'))
        {
            // Load PEAR dependencies
            include_once('HTTP/Request2.php');

            // Load php-errornot client library
            include_once($this->getRootDir().'/lib/vendor/php-errornot/errornot.php');

            // Instanciate the service
            $this->client = new Services_ErrorNot(sfConfig::get('app_errornot_notifier_plugin_url'), sfConfig::get('app_errornot_notifier_plugin_api_key'));

            // Handle exceptions
            $this->dispatcher->connect(
                'application.throw_exception',
                array('sfErrorNotNotifier', 'handleExceptionEvent')
            );

            // Handle log errors
            $this->dispatcher->connect(
                'application.log',
                array('sfErrorNotNotifier', 'handleLogEvent')
            );
        }
    }
}
