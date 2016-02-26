<?php
namespace ADim\ServeristApi\Users;


use ADim\ServeristApi\Apist;
use ADim\ServeristApi\WebApi;

class Admin extends WebApi
{

    /**
     * @return array
     */
    protected function getCardDataConfig()
    {
        return array(
            'purchases' => Apist::filter('#purchases .data tr')->each([
                'date'      => Apist::filter('td')->eq(0)->text()->trim(" \t\n\r\0\x0B"),
                'summ'      => Apist::filter('td')->eq(1)->text()->floatval(),
                'discount'  => Apist::filter('td')->eq(2)->text()->floatval(),
                'totalsumm' => Apist::filter('td')->eq(3)->text()->floatval(),
                'bonus'     => Apist::filter('td')->eq(4)->text()->floatval(),
                'seller'    => Apist::filter('td')->eq(5)->text()->trim(" \t\n\r\0\x0B"),
                'shop'      => Apist::filter('td')->eq(6)->text()->trim(" \t\n\r\0\x0B"),
                'return'    => Apist::filter('td')->eq(7)->text()->trim(" \t\n\r\0\x0B"),
                'type'      => Apist::filter('td')->eq(8)->text()->trim(" \t\n\r\0\x0B"),
                'style'     => Apist::filter('')->attr('style'),
            ]),
        );
    }

    /**
     * @param array $data
     * @return array
     */
    protected  function parseCardData($data)
    {
        $resultData = array();
        if (isset($data['purchases'])) {
            if (count($data['purchases']) > 1 && isset($data['purchases'][0]))
                unset($data['purchases'][0]);

            $resultData['purchases'] = array();

            foreach ($data['purchases'] as $purchase) {
                if (is_numeric($purchase['summ'])) {
                    $purchaseShop = explode("\n", $purchase['shop']);
                    if (count($purchaseShop) >= 5) {
                        $purchase['shop'] = $purchaseShop[0];
                        $purchase['brand'] = $purchaseShop[2];
                        $purchase['enterprise'] = $purchaseShop[4];
                    } else
                        unset($purchase['shop']);

                    if ($purchase['style']) {
                        if (mb_strpos($purchase['style'], 'background-color') !== false && mb_strpos($purchase['style'], '#fde3e3') !== false)
                            $purchase['status'] = 'return';
                    }

                    unset($purchase['style']);

                    if (isset($purchase['shop']))
                        $resultData['purchases'][] = $purchase;
                }
            }
        }

        return $resultData;
    }

    /**
     * Get card data by barcode
     *
     * @param int $card
     * @return array|bool
     */
    public function getCardData($card)
    {
        try {
            $data = $data = $this->get('cards/find', $this->getCardDataConfig(), array(
                'body' => array(
                    'num' => $card,
                ),
                'auth' => $this->auth,
                'allow_redirects' => true,
            ));

            return $this->parseCardData($data);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get card data by id
     *
     * @param int $cardId
     * @return array|bool
     */
    public function getCardDataById($cardId)
    {
        try {
            $data = $data = $this->get('cards/'.$cardId, $this->getCardDataConfig(), array('auth' => $this->auth));

            return $this->parseCardData($data);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * get Terminals list
     *
     * @return array|bool
     */
    public function getTerminals()
    {
        try {
            $data = $this->getPaginatedData('franchiser/contractors', array(
                'terminals' => Apist::filter('table.data')->eq(2)->filter('tr')->each([
                    'enterprise' => Apist::filter('td')->eq(0)->text()->trim(" \t\n\r\0\x0B"),
                    'shop' => Apist::filter('td')->eq(1)->text()->trim(" \t\n\r\0\x0B"),
                    'address' => Apist::filter('td')->eq(2)->text()->trim(" \t\n\r\0\x0B"),
                    'title' => Apist::filter('td')->eq(3)->text()->trim(" \t\n\r\0\x0B"),
                    'login' => Apist::filter('td')->eq(4)->text()->trim(" \t\n\r\0\x0B"),
                    'password' => Apist::filter('td')->eq(5)->text()->trim(" \t\n\r\0\x0B"),
                    'href' => Apist::filter('td')->eq(6)->filter('a')->attr('href'),
                    'class' => Apist::filter('')->attr('class'),
                ]),
            ));

            $terminalsData = array();

            if (is_array($data) && isset($data['terminals'])) {
                foreach ($data['terminals'] as $item) {
                    if (is_array($item) && isset($item['enterprise']) && isset($item['shop']) && isset($item['href'])) {
                        $terminalId = explode('/', $item['href']);
                        if (isset($terminalId[3]) && is_numeric($terminalId[3])) {
                            $terminalId = $terminalId[3];
                            $insertData = $item;
                            $insertData['blocked'] = false;
                            if (isset($item['class'])) {
                                $classes = explode(' ', $item['class']);
                                if (in_array('blocked', $classes))
                                    $insertData['blocked'] = true;
                            }
                            unset($insertData['href']);
                            unset($insertData['class']);
                            $insertData['id'] = $terminalId;
                            $terminalsData[$terminalId] = $insertData;
                        }
                    }
                }
            }

            return $terminalsData;
        } catch (Exception $e) {
            return false;
        }
    }
}