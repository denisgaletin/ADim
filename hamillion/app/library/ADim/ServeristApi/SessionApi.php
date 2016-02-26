<?php
namespace ADim\ServeristApi;


use GuzzleHttp\Client;

class SessionApi {

    const STATUS_SIGNED = 1;
    const STATUS_NOT_SIGNED = 0;

    protected $status = self::STATUS_NOT_SIGNED;

    protected $guzzle = null;

    protected $baseUrl = '';

    protected $type = null;

    protected $sid = '';

    protected $errors = array();

    public function __construct($url, $login, $password)
    {
        $this->baseUrl = $url;
        $type = get_class($this);
        if (strpos($type, __NAMESPACE__.'\Users\\') === 0) {
            $type = substr($type, strlen(__NAMESPACE__ . '\Users\\'));
        }
        $this->type = $type;
        $this->login(strtolower($type), $login, $password);
    }

    public function __destruct()
    {
        $this->logout();
    }

    /**
     * @param string $type
     * @param string $login
     * @param string $password
     */
    protected function login($type, $login, $password)
    {
        $response = $this->request('post', 'session', array(
            'type' => $type,
            'login' => $login,
            'password' => $password,
        ));

        if (isset($response['sid']) && $response['sid']) {
            $this->status = self::STATUS_SIGNED;
            $this->sid = $response['sid'];
        }
    }

    /**
     * Close session
     */
    public function logout()
    {
        $response = $this->request('delete', 'session');

        if (isset($response['closed_at']) && $response['closed_at']) {
            $this->sid = '';
            $this->status = self::STATUS_NOT_SIGNED;
        }
    }

    /**
     * Get login status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get login status
     *
     * @return bool
     */
    public function isSigned()
    {
        if ($this->status == self::STATUS_SIGNED)
            return true;

        return false;
    }

    /**
     * @return Client
     */
    protected function getGuzzle()
    {
        if (!$this->guzzle) {
            $this->guzzle = new Client(array(
                'base_url' => $this->baseUrl,
            ));
        }
        return $this->guzzle;
    }

    /**
     * @param array $data
     * @return string
     */
    protected function toJSON($data)
    {
        return json_encode($data);
    }

    /**
     * @param string $method
     * @param $function
     * @param array $params
     * @return mixed
     */
    protected function request($method = 'get', $function, $params = array())
    {
        $guzzle = $this->getGuzzle();

        if ($this->sid)
            $params['sid'] = $this->sid;

        $method = strtolower($method);
        $response = $guzzle->$method($function . '.json', array(
            'query' => array(
                'args' => $this->toJSON($params),
            ),
        ));

        $response = $response->json();

        if (isset($response['errors']) && is_array($response['errors'])) {
            $this->errors = array_merge_recursive($this->errors, $response['errors']);
            $response = false;
        }

        return $response;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

} 