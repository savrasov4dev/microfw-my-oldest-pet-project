<?php


namespace microfw\app\controllers;

use microfw\app\models\Main;

class MainController extends AppController
{
    public function indexAction()
    {
        $model = new Main();
        $meta = [
            'title' => 'MAIN TITLE',
            'description' => 'MAIN DESC',
            'keywords' => 'MAIN KEYWORD',
        ];

        $data = compact('meta');
        $this->setData($data);
    }
}