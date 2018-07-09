<?php

  class Pages extends Controller {

    public function __construct() {
    }

    public function index() {
      $data = [
              'title' => 'Moje Sprawy'
              ];

      if (isset($_SESSION['poziom'])) {
        if ($_SESSION['poziom'] == '-1') {
          redirect('pracownicy/zestawienie');
        } else {
          redirect('przychodzace/moje');
        }
      } else {
        redirect('pracownicy/zaloguj');
      }
    }

    public function about() {
      $data = [
              'title' => 'About us'
              ];
      $this->view('pages/about', $data);
    }

  }
