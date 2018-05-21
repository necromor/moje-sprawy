<?php

  class Podmioty extends Controller {

    public function __construct() {


      $this->podmiotModel = $this->model('Podmiot');
    }

    public function dodaj() {

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
          'title' => 'Dodaj podmiot',
          'nazwa_podmiotu' => trim($_POST['nazwaPodmiotu']),
          'adres_podmiotu' => trim($_POST['adresPodmiotu']),
          'poczta_podmiotu' => trim($_POST['pocztaPodmiotu']),
          'nazwa_podmiotu_err' => '',
          'adres_podmiotu_err' => '',
          'poczta_podmiotu_err' => ''
        ];

        // tymczasowa walidacja
        if (empty($data['nazwa_podmiotu'])) {
          $data['nazwa_podmiotu_err'] = 'Nazwa nie może pozostać pusta';
        }

        if (empty($data['adres_podmiotu'])) {
          $data['adres_podmiotu_err'] = 'Ulica (miejscowość) nie może pozostać pusta';
        }

        if (empty($data['poczta_podmiotu'])) {
          $data['poczta_podmiotu_err'] = 'Kod pocztwoy i poczta nie może pozostać puste';
        }


        // Dodaj do bazy danych tylko gdy nie ma błędów przypisanych do 
        // któregokolwiek pola
        if (empty($data['nazwa_podmiotu_err']) && empty($data['adres_podmiotu_err']) && empty($data['poczta_podmiotu_err'])) {

          // Pomyślna walidacja - dodaj do bazy danych
          $this->podmiotModel->dodajPodmiot($data);

          // tymczasowo
          redirect('podmioty/zestawienie');

        } else {

          // Wyświetl powtórnie formularz z podanymi danymi i błędami
          $this->view('podmioty/dodaj', $data);
        }


      } else {

        $data = [
          'title' => 'Dodaj podmiot',
          'nazwa_podmiotu' => '',
          'adres_podmiotu' => '',
          'poczta_podmiotu' => '',
          'nazwa_podmiotu_err' => '',
          'adres_podmiotu_err' => '',
          'poczta_podmiotu_err' => ''
        ];

        $this->view('podmioty/dodaj', $data);
      }

    }

    public function edytuj($id) {

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
          'title' => 'Edytuj dane podmiotu',
          'nazwa_podmiotu' => trim($_POST['nazwaPodmiotu']),
          'adres_podmiotu' => trim($_POST['adresPodmiotu']),
          'poczta_podmiotu' => trim($_POST['pocztaPodmiotu']),
          'nazwa_podmiotu_err' => '',
          'adres_podmiotu_err' => '',
          'poczta_podmiotu_err' => '',
          'id' => $id
        ];

        // tymczasowa walidacja
        if (empty($data['nazwa_podmiotu'])) {
          $data['nazwa_podmiotu_err'] = 'Nazwa nie może pozostać pusta';
        }

        if (empty($data['adres_podmiotu'])) {
          $data['adres_podmiotu_err'] = 'Ulica (miejscowość) nie może pozostać pusta';
        }

        if (empty($data['poczta_podmiotu'])) {
          $data['poczta_podmiotu_err'] = 'Kod pocztwoy i poczta nie może pozostać puste';
        }


        // Dodaj do bazy danych tylko gdy nie ma błędów przypisanych do 
        // któregokolwiek pola
        if (empty($data['nazwa_podmiotu_err']) && empty($data['adres_podmiotu_err']) && empty($data['poczta_podmiotu_err'])) {

          // Pomyślna walidacja - dodaj do bazy danych
          $this->podmiotModel->edytujPodmiot($data);

          // tymczasowo
          redirect('podmioty/zestawienie');

        } else {

          // Wyświetl powtórnie formularz z podanymi danymi i błędami
          $this->view('podmioty/edytuj/'.$id, $data);
        }


      } else {

        $podmiot = $this->podmiotModel->pobierzDanePodmiotu($id);

        $data = [
          'title' => 'Zmień dane podmiotu',
          'nazwa_podmiotu' => $podmiot->nazwa,
          'adres_podmiotu' => $podmiot->adres_1,
          'poczta_podmiotu' => $podmiot->adres_2,
          'nazwa_podmiotu_err' => '',
          'adres_podmiotu_err' => '',
          'poczta_podmiotu_err' => '',
          'id' => $id
        ];

        $this->view('podmioty/edytuj', $data);
      }

    }


    public function zestawienie() {

      $podmioty = $this->podmiotModel->pobierzPodmioty();

      $data = [
        'title' => 'Zestawienie podmiotów',
        'podmioty' => $podmioty
      ];

      $this->view('podmioty/zestawienie', $data); 
    }

    public function ajax_podmiot($id) {
      /*
       * Pobiera dane podmiotu i drukuje je w postaci json.
       * Zastosowanie do zapytania ajax.
       * Jeżeli podmiot nie istnieje w miejsu id wstawiona zostaje wartość -1
       *
       * Funkcja nie obsługuje widoku.
       *
       * Parametry: 
       *  - id => id pobieranego podmiotu
       * Zwraca:
       *  - echo json postaci: { id:, nazwa:, adres_1:, adres_2: }
       */

       $podmiot = $this->podmiotModel->pobierzDanePodmiotu($id);
       if ($podmiot) {
         echo json_encode($podmiot);
       } else {
         echo '{"id":"-1"}'; 
       }
    }


  }
