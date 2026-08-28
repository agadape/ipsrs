<?php
namespace App\Controllers;

class Panduan extends BaseController
{
    public function index()
    {
        return $this->render("pages/panduan/index");
    }
}

