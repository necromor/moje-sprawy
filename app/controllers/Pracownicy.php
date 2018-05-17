<?php

  class Pracownicy extends Controller {

    private $POZIOMY = [
      0 => 'sekretariat',
      1 => 'księgowość',
      2 => 'zwykły'
    ];

    public function __construct() {

      $this->pracownikModel = $this->model('Pracownik');
    }


    public function zestawienie() {

      $pracownicy = $this->pracownikModel->pobierzWszystkichPracownikow();

      // podmiana poziomów i aktywności na tekst
      foreach ($pracownicy as $pracownik) {
        $pracownik->poziom = $this->POZIOMY[$pracownik->poziom];
        if ($pracownik->aktywny == 0) {
          $pracownik->aktywny = 'Nie';
        } else {
          $pracownik->aktywny = 'Tak';
        }
      }

      $data = [
        'title' => 'Zestawienie pracowników',
        'pracownicy' => $pracownicy
      ];

      $this->view('pracownicy/zestawienie', $data);
    }

    public function dodaj() {

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
          'title' => 'Dodaj pracownika',
          'poziomy' => $this->POZIOMY,
          'imie' => trim($_POST['imie']),
          'nazwisko' => trim($_POST['nazwisko']),
          'login' => trim($_POST['login']),
          'poziom' => $_POST['poziom'],
          'imie_err' => '',
          'nazwisko_err' => '',
          'login_err' => '',
        ];

        $data['imie_err'] = $this->sprawdzImie($data['imie']);
        $data['nazwisko_err'] = $this->sprawdzNazwisko($data['nazwisko']);
        $data['login_err'] = $this->sprawdzLogin($data['login']);

        if (empty($data['imie_err']) && empty($data['nazwisko_err']) && empty($data['login_err'])) {
 
          //dodaj podstawowe hasło pracownika jako zakodowany login
          $data['haslo'] = password_hash($data['login'], PASSWORD_DEFAULT);

          $this->pracownikModel->dodajPracownika($data);

          $wiadomosc = "Pracownik <strong>" . $data['imie'] . " " . $data['nazwisko'] . " [" . $data['login'] . "]</strong> został dodany pomyślnie.";
          flash('pracownicy_wiadomosc', $wiadomosc);
          redirect('pracownicy/zestawienie'); 

        } else {
          $this->view('pracownicy/dodaj', $data);
        }

      } else {

        $data = [
          'title' => 'Dodaj pracownika',
          'poziomy' => $this->POZIOMY,
          'imie' => '',
          'nazwisko' => '',
          'login' => '',
          'poziom' => 2,
          'imie_err' => '',
          'nazwisko_err' => '',
          'login_err' => '',
        ];

        $this->view('pracownicy/dodaj', $data);

      }
    }


   /*
    * FUNKCJE SPRAWDZAJĄCE
    */

   private function sprawdzImie($tekst) {

     $error = '';

     if ($tekst == '') {
       $error = "Pracownik musi mieć imię.";
     } elseif (strlen($tekst) < 2) {
       $error = "Imię musi mieć przynajmniej 2 znaki.";
     }

     return $error;
   }

   private function sprawdzNazwisko($tekst) {

     $error = '';

     if ($tekst == '') {
       $error = "Pracownik musi mieć nazwisko.";
     } elseif (strlen($tekst) < 2) {
       $error = "Nazwisko musi mieć przynajmniej 2 znaki.";
     }

     return $error;
   }

   private function sprawdzLogin($tekst) {

     $error = '';

     if ($tekst == '') {
       $error = "Pracownik musi mieć login.";
     } elseif (strlen($tekst) < 2) {
       $error = "Login musi mieć przynajmniej 2 znaki.";
     } elseif ($this->pracownikModel->czyIstniejeLogin($tekst)) {
       $error = "Podany login jest już zajęty.";
     }

     return $error;
   }



  }
