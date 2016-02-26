<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        $serverist = $this->getDI()->get('serverist');

        //Admin
        $admin = $serverist->login('admin');
        if ($admin->isSigned()) {
            echo "\n".'Terminal List ';
            print_r($admin->getTerminals());

            echo "\n".'Card purchases ';
            print_r($admin->getCardData(77700000000000));
        }

        //Operator
        $operator = $serverist->login('operator');
        if ($operator->isSigned()) {
            echo "\n".'Cards list ';
            print_r($operator->getCards());

            $cardData = array(
                'address' => '',
                'birthdate' => '1987-04-14',
                'building' => '',
                'card' => 25008500000000,
                'card_info' => '',
                'city_id' => '',
                'education' => '',
                'email'  => 'apt.dmitry@gmail.com',
                'firstname' => 'Дмитрий',
                'house' => '',
                'lastname' => 'Аптовцев',
                'mobile' => '',
                'more_info' => '',
                'passport' => '',
                'patronymic' => '',
                'phone' => '',
                'post_index' => '',
                'region' => '',
                'room' => '',
                'sex' => 'true', // true - male, false - female
                'social_status' => '',
                'street' => '',
            );

            echo "\n".'Add card ';
            $operator->AddCard($cardData);

            echo "\n".'Update card ';
            $operator->updateCardById(1000000, $cardData);
        }

        //Card
        $card = $serverist->login('card', 2500850000000, 000);
        if ($card->isSigned()) {
            echo "\n".'Profile';
            print_r($card->getProfile());

            echo "\n".'Card purchases ';
            print_r($card->getPurchases());

            echo "\n".'Card acconts ';
            print_r($card->getAccounts());
        } else {
            print_r($card ->getErrors());
        }

        //Manager
        $manager = $serverist->login('manager', '', '');
        if ($manager->isSigned()) {
            echo "\n".'Manager commissions ';
            print_r($manager->getCommissions());
        }


        $this->view->disable();
    }

}

