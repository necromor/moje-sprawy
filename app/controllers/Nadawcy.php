<?php

  class Nadawcy extends Controller {

    public function __construct() {
    }

    public function dodaj() {

      $data = [
              'title' => 'Dodaj nadawcę'
              ];
      $this->view('nadawcy/dodaj', $data);
    }



  }
