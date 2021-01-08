$( document ).ready(function() {
  $('.submitState').click(function(e) {
    $.ajax({
        url: 'results.php',
        type: 'POST',
        data: {
            state: $(e.currentTarget).siblings("div").children(".status").val(),
            id: $(e.currentTarget).val()
        },
        success: function() {
          alert('Etat changé');
        },  
        error: function(){
          alert('Erreur');
        }             
    });
  });
});

