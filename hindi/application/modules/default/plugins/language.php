<?php
class Application_Module_Default_Plugins_Language   extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $lang = $request->getParam('lang', null);

        $translate = Zend_Registry::get('Zend_Translate');

        if ($translate->isAvailable($lang)) {
            $translate->setLocale($lang);
        } else {
        	$translate->setLocale('en');
        }

        // Set language to global param so that our language route can
        // fetch it nicely.
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $router->setGlobalParam('lang', $lang);
    }
}