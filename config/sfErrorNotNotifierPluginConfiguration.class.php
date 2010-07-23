<?php

/**
 * sfErrorNotNotifierPlugin configuration.
 *
 * @package     sfErrorNotNotifierPlugin
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
     * @return Services_ErrorNot a configured hoptoad client.
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @see sfPluginConfiguration
     */
    public function configure()
    {
        // Load php-errornot client library
        require_once($this->getRootDir().'/lib/vendor/php-errornot/errornot.php');

        // TODO : find out why config key are not set on cli
        // Get API key
        $api_key = sfConfig::get('app_errornot_notifier_plugin_api_key', false);
        // Throw an exception if no key is defined
        if (false === $api_key)
        {
//            throw new InvalidArgumentException('ErrorNot API key is not defined');
        }

        // Get ErrorNot server URL
        $url = sfConfig::get('app_errornot_notifier_plugin_url', false);
        // Throw an exception if no key is defined
        if (false === $url)
        {
//            throw new InvalidArgumentException('ErrorNot server URL is not defined');
        }

        // Instanciate the service
        $this->client = new Services_ErrorNot($url, $api_key);

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

    public function initialize()
    {
        // in this method, config (app.yml) is not already loaded, so we must not initialize the service.
    }
}
