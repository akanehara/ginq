<?php

class Movie
{
    public $id;
    public $title;
    public function __construct($id, $title, $director)
    {
        $this->id       = $id;
        $this->title    = $title;
        $this->director = $director;
    }
}

class Director
{
    public $id;
    public $name;
    public function __construct($id, $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
}

class Person
{
    public $id;
    public $name;
    public $city;
    public function __construct($id, $name, $city)
    {
        $this->id = $id;
        $this->name = $name;
        $this->city = $city;
    }
}

