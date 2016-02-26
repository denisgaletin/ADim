<?php
namespace ADim\ServeristApi;

class WebApi extends Apist
{
    const STATUS_SIGNED = 1;
    const STATUS_NOT_SIGNED = 0;

    protected $status = self::STATUS_NOT_SIGNED;

    protected $auth;

    protected $baseUrl = '';

    public function __construct($url, $login, $password)
    {
        $this->setBaseUrl($url);
        $options['cookies'] = true;
        $options['allow_redirects'] = false;
        parent::__construct($options);
        return $this->login($login, $password);
    }

    protected function login($login, $password)
    {
        $guzzle = $this->getGuzzle();
        $this->auth = array($login, $password);
        $response = $guzzle->post('/user_session', array(
            'auth' => array($login, $password),
            'allow_redirects' => true,
        ));

        if ($login && mb_strpos($response->getBody(), $login) !== false)
            $this->status = self::STATUS_SIGNED;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isSigned()
    {
        if ($this->status == self::STATUS_SIGNED)
            return true;

        return false;
    }

    protected function getPaginatedData($url, $config = array(), $options = null)
    {
        if (!$options || !is_array($options)) {
            $options = array('auth' => $this->auth);
        }

        $data = $this->get($url, array_merge(array(
            'pages' => Apist::filter('.pagination a')->each([
                'page'        => Apist::current()->text()->trim(),
                'href'        => Apist::current()->attr('href'),
            ])), $config
        ), $options);

        if (is_array($data)) {
            $pages = array();
            if (isset($data['pages'])) {
                $pages = $data['pages'];
                unset($data['pages']);
            }

            if (is_array($pages) && count($pages) > 0) {
                foreach ($pages as $page) {
                    if (isset($page['page']) && isset($page['href']) && is_numeric($page['page'])) {
                        $data = array_merge_recursive($data, $this->get($page['href'], $config, $options));
                    }
                }
            }

            return $data;
        }

        return false;
    }




} 