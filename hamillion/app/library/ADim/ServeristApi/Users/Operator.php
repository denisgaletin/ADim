<?php
namespace ADim\ServeristApi\Users;


use ADim\ServeristApi\WebApi;

class Operator extends WebApi
{

    /**
     * Add Card
     *
     * @param array $params
     * @return bool
     */
    public function addCard($params = array())
    {
        try {
            $data = array();

            foreach ($params as $name => $value)
                $data['card_holder[' . $name . ']'] = $value;

            $response = $this->guzzle->post('operator/card_holders/', array(
                'auth' => $this->auth,
                'body' => $data,
            ));

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Update Card data by card id
     *
     * @param int $cardId
     * @param array $params
     * @return bool
     */
    public function updateCardById($cardId, $params = array())
    {
        try {
            $data = array();

            foreach ($params as $name => $value)
                $data['card_holder[' . $name . ']'] = $value;

            $data['_method'] = 'put';

            $response = $this->guzzle->post('operator/card_holders/' . $cardId, array(
                'auth' => $this->auth,
                'body' => $data,
            ));

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get Cards list
     *
     * @return array|bool
     */
    public function getCards()
    {
        try {
            $data = $this->getPaginatedData('operator/card_holders/', array(
                'cards' => Apist::filter('table.data tr')->each([
                    'barcode'     => Apist::filter('td')->eq(0)->text()->trim(" \t\n\r\0\x0B"),
                    'href'        => Apist::filter('td')->eq(8)->filter('a')->attr('href'),
                ]),
            ));

            $cardsData = array();

            if (is_array($data) && isset($data['cards'])) {
                foreach ($data['cards'] as $card) {
                    if (count($card) > 1 && $card['barcode'] && $card['href']) {
                        $cardId = explode('/', $card['href']);
                        if (isset($cardId[3]) && is_numeric($cardId[3]))
                            $cardsData[$card['barcode']] = $cardId[3];
                    }
                }
            }

            return $cardsData;
        } catch (Exception $e) {
            return false;
        }
    }

}