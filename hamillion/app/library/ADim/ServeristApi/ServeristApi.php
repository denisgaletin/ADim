<?php

namespace ADim\ServeristApi;


use Phalcon;
use Phalcon\Config;
use Phalcon\DI\Injectable;

class ServeristApi extends Injectable
{

    /**
     * @var array
     */
    protected $config = [];

    /**
     * Create a new ServeristApi component using $config for configuring
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = new Phalcon\Config(array(
            'operator' => array(
                'login' => '',
                'password' => '',
            ),
            'admin' => array(
                'login' => '',
                'password' => '',
            ),
            'url' => 'http://demo.serverist.ru',
            'apiPath' => '/api2/',
        ));
        $this->config->merge($config);
    }

    /**
     * Log in with selected user
     *
     * @param string $type
     * @param string $login
     * @param string $password
     * @return mixed
     */
    public function login($type = 'card', $login = '', $password = '')
    {
        $class = __NAMESPACE__.'\Users\\'.ucfirst($type);
        if (!class_exists($class))
            return false;

        if ($type == 'operator' && !$login && !$password) {
            $login = $this->config->operator->login;
            $password = $this->config->operator->password;
        } elseif ($type == 'admin' && !$login && !$password) {
            $login = $this->config->admin->login;
            $password = $this->config->admin->password;
        }

        $url = $this->config->url;
        if (!in_array($type, array('operator', 'admin')))
            $url .= $this->config->apiPath;

        return new $class($url, $login, $password);
    }

    /**
     * @param $type
     * @param $login
     * @param $password
     * @return mixed
     */
    protected function userLogin($type, $login, $password)
    {
        $class = 'ServeristApi'.ucfirst($type);
        if (!class_exists($class))
            $class = 'ServeristApiSession';

        return new $class($this, $type, $login, $password);
    }

}