<?php
namespace ADim\ServeristApi;


class SessionUser extends SessionApi
{

    protected $password = null;

    /**
     * Log in with selected user
     *
     * @param string $type
     * @param string $login
     * @param string $password
     * @return mixed
     */
    protected function login($type, $login, $password)
    {
        parent::login($type, $login, $password);

        if ($this->isSigned())
            $this->password = $password;
    }

    /**
     * Get Proofile
     *
     * @return mixed
     */
    public function getProfile()
    {
        return $this->request('get', 'session/user');
    }

    /**
     * UpdateProfile
     *
     * @param array $params
     * @return mixed
     */
    public function updateProfile($params = array())
    {
        if (isset($params['password']) && $params['password']) {
            $params['password'] = array(
                'old' => $this->password,
                'new' => $params['password'],
                'confirmation' => $params['password'],
            );
        }

        return $this->request('put', 'session/user', $params);
    }

}