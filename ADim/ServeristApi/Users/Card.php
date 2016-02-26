<?php
namespace ADim\ServeristApi\Users;


use ADim\ServeristApi\SessionUser;

class Card extends SessionUser
{

    /**
     * Get Purchases list
     *
     * @param int $offset
     * @param int $limit
     * @param null $dateFrom
     * @param null $dateTo
     * @return array
     */
    public function getPurchases($offset = 0, $limit = 100, $dateFrom = null, $dateTo = null)
    {

        $params = array(
            'offset' => $offset,
            'limit' => $limit,
        );

        if ($dateFrom)
            $params['date_from'] = $dateFrom;
        if ($dateTo)
            $params['date_to'] = $dateTo;

        return $this->request('get', 'purchases', $params);
    }

    /**
     * Get Accounts list
     *
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getAccounts($offset = 0, $limit = 100)
    {
        $params = array(
            'offset' => $offset,
            'limit' => $limit,
        );

        return $this->request('get', 'accounts', $params);
    }

    /**
     * Get Account
     *
     * @param int $accountId
     * @return array
     */
    public function getAccount($accountId)
    {
        return $this->request('get', 'accounts/'.$accountId);
    }

    /**
     * Transfer founds from account to card
     *
     * @param int $accountId
     * @param int $card
     * @param int $sum
     * @return mixed
     */
    public function transferFoundsFromAccountToCard($accountId, $card, $sum = 0)
    {
        return $this->request('put', 'accounts/'.$accountId, array(
            'sum' => $sum,
            'target_card' => $card
        ));
    }

}