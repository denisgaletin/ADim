<?php
namespace ADim\ServeristApi\Users;


use ADim\ServeristApi\Apist;
use ADim\ServeristApi\SessionUser;

class Manager extends SessionUser
{

    /**
     * Get commissions list
     *
     * @param string|null $date
     * @return mixed
     */
    public function getCommissions($date = null)
    {
        $params = array();
        if ($date)
            $params['date'] = $date;

        return $this->request('get', 'commissions', $params);
    }

    /**
     * Get partner commissions list
     *
     * @param int $partnerId
     * @param string|null $date
     * @return mixed
     */
    public function getPartnerCommissions($partnerId, $date = null)
    {
        $params = array();
        if ($date)
            $params['date'] = $date;

        return $this->request('get', 'commissions/'.$partnerId, $params);
    }

}