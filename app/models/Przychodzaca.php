<?php

  class Przychodzaca {

    private $db;

    public function __construct() {
      $this->db = new Database;
    }

    public function pobierzPrzychodzace($rok) {

      $sql = "SELECT  
              przychodzace.id, 
              przychodzace.data_pisma, 
              przychodzace.data_wplywu, 
              przychodzace.nr_rejestru, 
              przychodzace.znak,
              przychodzace.dotyczy, 
              przychodzace.czy_faktura,
              przychodzace.id_pracownik, 
              przychodzace.liczba_zalacznikow, 
              przychodzace.kwota, 
              przychodzace.nr_rejestru_faktur,
              podmioty.nazwa,
              podmioty.adres_1,
              podmioty.adres_2
              FROM przychodzace, podmioty
              WHERE data_wplywu LIKE :rok 
                AND przychodzace.id_podmiot=podmioty.id 
              ORDER BY nr_rejestru ASC";
      $this->db->query($sql);
      $this->db->bind(':rok', $rok . '%');

      return $this->db->resultSet();
    }

    public function pobierzFaktury($rok) {

      $sql = "SELECT  
              przychodzace.id, 
              przychodzace.data_pisma, 
              przychodzace.data_wplywu, 
              przychodzace.nr_rejestru, 
              przychodzace.znak,
              przychodzace.dotyczy, 
              przychodzace.kwota, 
              przychodzace.nr_rejestru_faktur,
              podmioty.nazwa,
              podmioty.adres_1,
              podmioty.adres_2
              FROM przychodzace, podmioty
              WHERE data_wplywu LIKE :rok 
                AND przychodzace.id_podmiot=podmioty.id 
                AND czy_faktura=1 
              ORDER BY nr_rejestru_faktur ASC";
      $this->db->query($sql);
      $this->db->bind(':rok', $rok . '%');

      return $this->db->resultSet();
    }

    public function dodajPrzychodzaca($data) {

      // pobierz kolejny numer rejestru w danym roku
      // tymczasowo
      $nr_rejestru = $this->tworzNrRejestru($data['data_wplywu']);

      // ustaw wartości domyślne na pozostałe pola
      // w zależności od tego czy dodawane pismo czy faktura
      if ($data['czy_faktura'] == '0') {
        $liczba_zalacznikow = $data['liczba_zalacznikow'];
        $id_pracownik = pobierzIdNazwy($data['dekretacja']);
        $kwota = 0.00;
        $nr_rejestru_faktur = 0;
      } else {
        $liczba_zalacznikow = 0;
        $id_pracownik = 0;
        $kwota = $data['kwota'];
        // pobierz kolejny nr rejestru faktur
        $nr_rejestru_faktur = $this->tworzNrRejestruFaktur($data['data_wplywu']);
      }

      $id_podmiot = pobierzIdNazwy($data['podmiot_nazwa']);

      $sql = "INSERT INTO przychodzace (nr_rejestru, znak, data_pisma, data_wplywu, dotyczy, id_podmiot, czy_faktura, id_pracownik, liczba_zalacznikow, kwota, nr_rejestru_faktur) VALUES (:nr_rejestru, :znak, :data_pisma, :data_wplywu, :dotyczy, :id_podmiot, :czy_faktura, :id_pracownik, :liczba_zalacznikow, :kwota, :nr_rejestru_faktur)";
      $this->db->query($sql);
      $this->db->bind(':nr_rejestru', $nr_rejestru);
      $this->db->bind(':znak', $data['znak']);
      $this->db->bind(':data_pisma', $data['data_pisma']);
      $this->db->bind(':data_wplywu', $data['data_wplywu']);
      $this->db->bind(':dotyczy', $data['dotyczy']);
      $this->db->bind(':id_podmiot', $id_podmiot);
      $this->db->bind(':czy_faktura', $data['czy_faktura']);
      $this->db->bind(':id_pracownik', $id_pracownik);
      $this->db->bind(':liczba_zalacznikow', $liczba_zalacznikow);
      $this->db->bind(':kwota', $kwota);
      $this->db->bind(':nr_rejestru_faktur', $nr_rejestru_faktur);

      // dodaj do bazy i zwróć tablicę z numerem rejestru i nr rejestru faktur
      // jeżeli błąd to zwróć -1 jako wartości
      $numery = [
        'nr_rejestru' => '-1',
        'nr_rejestru_faktur' => '-1'
      ];
      if ($this->db->execute()) {
        $numery['nr_rejestru'] = $nr_rejestru;
        $numery['nr_rejestru_faktur'] = $nr_rejestru_faktur;
      }
      return $numery;
    }

    private function tworzNrRejestru($data) {
      /*
       * Funkcja która tworzy następny numer rejestru w danym roku
       * Algorytm: pobiera liczbę zarejestrowanej korespondencji w danym roku -
       * liczy się data wpływu i zwiększa otrzymaną wartość o 1
       */

       list($rok, $miesiac, $dzien) = explode('-', $data);

       $sql = "SELECT COUNT(id) as total FROM przychodzace WHERE data_wplywu LIKE :rok";
       $this->db->query($sql);
       $this->db->bind(':rok', $rok . '%');
       $row = $this->db->single();
       
       return intval($row->total) + 1;
    }
 
    private function tworzNrRejestruFaktur($data) {
      /*
       * Funkcja która tworzy następny numer rejestru faktur w danym roku
       * Algorytm: pobiera liczbę zarejestrowanych faktur w danym roku -
       * liczy się data wpływu i zwiększa otrzymaną wartość o 1
       */

       list($rok, $miesiac, $dzien) = explode('-', $data);

       $sql = "SELECT COUNT(id) as total FROM przychodzace WHERE data_wplywu LIKE :rok AND czy_faktura=1";
       $this->db->query($sql);
       $this->db->bind(':rok', $rok . '%');
       $row = $this->db->single();
       
       return intval($row->total) + 1;
    }




  }
