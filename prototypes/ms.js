const formularz = document.querySelector('#danePodmiotu');

let showForm = (status = '', lista = false) => {

  // czy powiązać pole nazwy z listą podmiotów
  lista == true ? pokazListe = 'list="listaPodmiotow"' : pokazListe = '';

  const formularzDodaj = '<div class="form-group row">'
            + '<label for="nazwaPodmiotu" class="col-sm-4 col-form-label">Nazwa:</label>'
            + '<input type="text" class="form-control col-sm-8" id="nazwaPodmiotu" name="nazwaPodmiotu" ' + pokazListe + '>'
            + '</div>'
            + '<div class="form-group row">'
            + '<label for="adresPodmiotu" class="col-sm-4 col-form-label">Ulica (miejscowość):</label>'
            + '<input type="text" class="form-control col-sm-8" id="adresPodmiotu" name="adresPodmiotu"' + status + '>'
            + '</div>'
            + '<div class="form-group row">'
            + '<label for="pocztaPodmiotu" class="col-sm-4 col-form-label">Kod pocztowy i poczta:</label>'
            + '<input type="text" class="form-control col-sm-8" id="pocztaPodmiotu" name="pocztaPodmiotu" ' + status + '>'
            + '</div>';

  return formularzDodaj;
};


let wyswietlFormularzDodajPodmiot = () => {
  
  let form = '<h2 class="row px-3">Dodaj nowy podmiot</h2>';
  form += showForm();
  form += '<div class="form-group row">'
        + '<button type="button" class="btn btn-success col-10 col-sm-3 offset-1 offset-sm-4" id="bDodajPodmiot">Dodaj Podmiot</button>'
        + '</div>';
  
  formularz.innerHTML = form; 
  document.querySelector('#bDodajPodmiot').addEventListener('click', dodajPodmiot);
}

let dodajPodmiot = () => {

  let nazwaF = document.querySelector('#nazwaPodmiotu');
  let adresF = document.querySelector('#adresPodmiotu');
  let pocztaF = document.querySelector('#pocztaPodmiotu');

  const nazwaV = nazwaF.value;
  const adresV = adresF.value;
  const pocztaV = pocztaF.value;

  //pokaz pierwotny formularz z uzupełnionymi danymi
  let form = '<h2 class="row px-3"><span class="col-sm-auto mr-auto px-0">Dane nadawcy</span> <a href="#" id="dodajPodmiot" class="btn btn-success col-sm-auto">Dodaj nadawcę</a></h2>';
  form += showForm('disabled', true);

  formularz.innerHTML = form; 
  document.querySelector('#dodajPodmiot').addEventListener('click', wyswietlFormularzDodajPodmiot);

  //wstaw dane do formularza
  //tymczasowo - potem z bazy
  // dodane obiekty trzeba powiazac na nowo
  nazwaF = document.querySelector('#nazwaPodmiotu');
  adresF = document.querySelector('#adresPodmiotu');
  pocztaF = document.querySelector('#pocztaPodmiotu');
  nazwaF.value = '1# ' + nazwaV;
  adresF.value = adresV;
  pocztaF.value = pocztaV;
}

let wyborPodmiotu = () => {
  nazwaF = document.querySelector('#nazwaPodmiotu');
  console.log(nazwaF.value);
}

document.querySelector('#dodajPodmiot').addEventListener('click', wyswietlFormularzDodajPodmiot);
document.querySelector('#nazwaPodmiotu').addEventListener('change', wyborPodmiotu);

$('input[type="radio"]').on('click', function(e) {
  if (e.target.id == 'radioFaktura') {
    $('#pismoRow1').hide();
    $('#pismoRow2').hide();
    $('#fakturaRow1').show();
  } else {
    $('#pismoRow1').show();
    $('#pismoRow2').show();
    $('#fakturaRow1').hide();
  }
});
$('#fakturaRow1').hide();
