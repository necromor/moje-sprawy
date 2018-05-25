// zmienna do adresów ajax
const URLROOT = 'http://ms.test/';

// na dzień dobry ukryj pola w zależności które radio jest zaznaczone
if ($('#radioPismo').is(':checked')) {
    $('#fakturaRow1').hide();
} else {
    $('#pismoRow1').hide();
    $('#pismoRow2').hide();
}

// na dzień dobry ukryj pola grupy
if ($('#radioPojedynczy').is(':checked')) {
    $('#jrwaGrupa').hide();
} else {
    $('#jrwaPojedynczy1').hide();
    $('#jrwaPojedynczy2').hide();
}

// obsługa zmiany opcji pismo czy faktura
$('input[name=czyFaktura]:radio').on('click', function(e) {
  if (e.target.id == 'radioFaktura') {
    // ukryj pola dotyczące pisma
    $('#pismoRow1').hide();
    $('#pismoRow2').hide();
    // pokaż pole dotyczące faktury
    $('#fakturaRow1').show();
    // ustaw domyślne wartości
    $('#liczbaZalacznikow').val('0');
    $('#dekretacja').val('');
  } else {
    // pokaż pola dotyczące pisma
    $('#pismoRow1').show();
    $('#pismoRow2').show();
    // ukryj pole dotyczące faktury
    $('#fakturaRow1').hide();
    // ustaw domyślną wartość
    $('#kwota').val('0.00');
  }
});

// obsługa zmiany opcji istniejący / nowy podmiot
$('input[name=czyNowy]:radio').on('click', function(e) {
  if (e.target.id == 'radioIstniejacy') {
    // zablokuj pola adresu i poczty
    $('#adresPodmiotu').prop('disabled', true);
    $('#pocztaPodmiotu').prop('disabled', true);
  } else {
    // odblokuj pola adresu i poczty
    $('#adresPodmiotu').prop('disabled', false);
    $('#pocztaPodmiotu').prop('disabled', false);
  }
    // wyczyść wszystkie pola
    $('#nazwaPodmiotu').val('');
    $('#adresPodmiotu').val('');
    $('#pocztaPodmiotu').val('');
});

// obsługa zmiany opcji pojedyczny numer / grupa numerów jrwa
$('input[name=czyGrupa]:radio').on('click', function(e) {
  if (e.target.id == 'radioGrupa') {
    // ukryj pola dotyczące pojedynczego numeru
    $('#jrwaPojedynczy1').hide();
    $('#jrwaPojedynczy2').hide();
    // pokaż pole dotyczące faktury
    $('#jrwaGrupa').show();
  } else {
    // ukryj pola dotyczące pojedynczego numeru
    $('#jrwaPojedynczy1').show();
    $('#jrwaPojedynczy2').show();
    // pokaż pole dotyczące faktury
    $('#jrwaGrupa').hide();
  }
});

// potwierdzenie kliknięcia w link resetu
$('.reset').on('click', function() {
  return confirm('Operacja jest nieodwracalna!. Jesteś pewny?');
});


// obsługa wyboru podmiotu z listy
// wyślij zapytanie ajax i wstaw otrzymane dane do pól adresu i poczty
$('#nazwaPodmiotu').on('change', function() {
  // tylko gdy dotyczy istniejącego podmiotu i wartość pola jest niepusta
  if ($('#radioIstniejacy').is(':checked') && $('#nazwaPodmiotu').val() != '') {
    let nazwa = $('#nazwaPodmiotu').val();
    let podzial = nazwa.split("#");
    // nie chcemy string w adresie
    let id = parseInt(podzial[0]);

    // wyślij zapytanie ajax
    $.ajax({
      type: 'GET',
      url: URLROOT + 'podmioty/ajax_podmiot/' + id
    })
      .done(function(data) {
        const podmiot = JSON.parse(data);
        // jeżeli id = -1 to znaczy, że podmiot nie istnieje
        if (podmiot.id != '-1') {
          // wstaw dane adresowe do pól
          $('#adresPodmiotu').val(podmiot.adres_1);
          $('#pocztaPodmiotu').val(podmiot.adres_2);
        } else {
          // wyczyść pola - na wszelki wypadek
          $('#adresPodmiotu').val('');
          $('#pocztaPodmiotu').val('');
        }
    });
  } else {
    // wyczyść pola - na wszelki wypadek
    $('#adresPodmiotu').val('');
    $('#pocztaPodmiotu').val('');
  }
});
