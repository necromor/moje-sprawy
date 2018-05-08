<?php

  class Przychodzaca {

    private $db;

    public function __construct() {
      $this->db = new Database;
    }

    public function pobierzPrzychodzace($rok) {

      //$sql = "SELECT * FROM przychodzace WHERE data_wplywu LIKE :rok ORDER BY nr_rejestru ASC";
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
 
  }
