
/* selected item */
const SELECTEDITEM = 'selected-item';
/* default card image */
const PATHIMGDEFAULT = "Immagini/cards/default-card.jpg";
/* inizialized filter variable	*/
$filter = "nome";

$(document).ready( function() {
	/* changing option menu header and variable filter */
	$('.dropdown-menu li a').on('click', function() {
		$("#"+$filter).removeClass(SELECTEDITEM);
		$(this).addClass(SELECTEDITEM);
		$filter = $(this).attr('id');
	});
});

/* on search button click*/
function onClickBtnSearch(){
	var search_text = $("#search-text").val();
	if(search_text!=""){
		var ricerca_nome = "";
		var ricerca_tipo = "";
		var ricerca_colore = "";
		var ricerca_espansione = "";
		var ricerca_rarita = "";
		
		if($filter == "nome"){
			ricerca_nome = search_text;
		}else if($filter == "tipo"){
			ricerca_tipo = search_text;
		}else if($filter == "colore"){
			ricerca_colore = search_text;
		}else if($filter == "espansione"){
			ricerca_espansione = search_text;
		}else if($filter == "rarita"){
			ricerca_rarita = search_text;
		}
		
		$.ajax({
			url: "Php/ricerca.php",
			type: "GET",
			data: {"nome": ricerca_nome, "tipo": ricerca_tipo, "colore": ricerca_colore, "espansione": ricerca_espansione, "rarita": ricerca_rarita},
			
			success:function(result){
				document.getElementById("div-cards-container").innerHTML = "";
				var cards = JSON.Parse(result);
				for(i = 0; i < cards.length; i++){
					$nome_carta = cards[i].nome;
					$colore_carta = cards[i].colore;
					$espansione_carta = cards[i].espansione;
					$tipo_carta = cards[i].colore;
					$rarita_carta = cards[i].descrizione;
					$path_img_carta = cards[i].link_immagine;
					if($path_img_carta==""){
						$path_img_carta = PATHIMGDEFAULT;
					}
					$("#div-cards-container").append("<div class='card'><div class='col-sm-6 col-md-4'><div class='thumbnail'><img class='card-img' src='" + $path_img_carta + "'><div class='caption'><p><h3 class='card-name'>" + $nome_carta + "</h3></p><p class='card-color'>colore: " + $colore_carta + "</p><p class='card-expansion'>espansione: " + espansione_carta + "</p><p class='card-type'>tipo: " + tipo_carta + "</p><p class='card-rarity'>rarità: " + rarita_carta + "</p><p><a href='#' class='btn btn-primary btn-add-card' role='button'>Aggiungi al mazzo</a></p></div></div></div></div>");
				}
				if(i==0){
					alert("Nessuna carta trovata");
				}						
			},
			error: function(richiesta,stato,errori){
				alert("Impossibile connettersi, riprova più tardi! Ci scusiamo per il disagio");
			}
		})
	}else{
		alert("Ricerca non valida");
	}
}