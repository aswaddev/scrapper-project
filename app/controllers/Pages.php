<?php
class Pages extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Welcome To Airline Scrapper',
            'description' => 'A Scrapper App to Gather Airlines Data',
            'active' => 'home',
        ];

        $this->loadView('pages/index', $data);
    }
}
