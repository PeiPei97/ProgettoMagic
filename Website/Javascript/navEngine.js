$ (document).ready( function() {

/* Disable zoom */
	$(window).keydown(function(event) {
		if((event.keyCode == 107 && event.ctrlKey == true) || (event.keyCode == 109 && event.ctrlKey == true)){
			event.preventDefault(); 
		}

		$(window).bind('mousewheel DOMMouseScroll', function(event) {
			if(event.ctrlKey == true){
				event.preventDefault(); 
			}
		});
	});
	
/* Website navigation Mecchanism */ 
    const PAGEID = '#nav-page-';
    const ANIMATIONDELAY = 1000;
    const LOADELATE = 6000;
    const LASTPAGE = 3;
    const FIRSTPAGE = 1;
	/* selected item */
	const SELECTEDITEM = 'selected-item';
	/* default card image */
	const PATHIMGDEFAULT = "../Immagini/cards/default-card.jpg";
	/* inizialized filter variable	*/
	$filter = "nome";
    
    $actualPage = 1;
    $isRunning = false;
    
	function pageJumper($actualPage, $nextPage, $inAnimation, $outAnimation) {
		$actualPage.removeClass('current-page');
		$actualPage.addClass($outAnimation);
		$nextPage.addClass($inAnimation);
		setTimeout(function() {
			$actualPage.removeClass($outAnimation);
			$nextPage.removeClass($inAnimation);
            $nextPage.addClass('current-page');
			$isRunning = false;
        }, ANIMATIONDELAY);
	}
	
    function pageIterator($actualPage, $isScrollDown) {
        $tempPage = $actualPage;
        $nextPage = ''; $prevPage = '';
        $classToAdd = 'in-page-ease-'; $classToRemove = 'out-page-ease-'; 
        $finalClass = 'current-page';
        
        if($isScrollDown){
			if($actualPage == FIRSTPAGE){
				$classToAdd += 'bottom';
				$classToRemove += 'top';
				$actualPage++;
			} else {
				$classToAdd += 'left';
				$classToRemove += 'left';
				if($actualPage < LASTPAGE){
					$actualPage++;
				} else {
					$actualPage = FIRSTPAGE;
				}
			}
        } else {
			if($actualPage == (FIRSTPAGE+1)){
				$classToAdd += 'top';
				$classToRemove += 'bottom';
				$actualPage--;
			} else{
				$classToAdd += 'right';
				$classToRemove += 'right';
				if($actualPage > FIRSTPAGE){
					$actualPage--;
				} else {
					$actualPage = LASTPAGE;
				}
			} 
        }
        
        $nextPage = PAGEID + $actualPage.toString();
        $prevPage = PAGEID + $tempPage.toString();
        $($prevPage).removeClass($finalClass);
        $($prevPage).addClass($classToRemove);
        $($nextPage).addClass($classToAdd);
        setTimeout(function() {
            $($prevPage).removeClass($classToRemove);
            $($nextPage).removeClass($classToAdd);
            $($nextPage).addClass($finalClass);
            $isRunning = false;
        }, ANIMATIONDELAY);
        
        return $actualPage;
    }
       
	$(window).scroll(function() {
		if($(window).scrollTop() + $(window).height() == $(document).height()) {
			console.log("Page bottom reached!");
		}else if($(window).scrollTop() == 0){
			console.log("Page top reached!");
		}
	});
	   
	
	
	$(window).bind('mousewheel DOMMouseScroll', function(scrollEvent){
		if($actualPage==1){
			if (scrollEvent.originalEvent.wheelDelta > 0 || scrollEvent.originalEvent.detail < 0) {/*
				//Scroll up
				if(!$isRunning){
					$isRunning = true;
					$actualPage = pageIterator($actualPage, false);
				}*/
			}
			else {
				//Scroll down
				if(!$isRunning){
					$isRunning = true;
					$actualPage = pageIterator($actualPage, true);
				}
			}
		}
    });

	
	$('.pt-page-button').on('click', function (){
		$nextPage = $(this).attr('page-anchor');
		if(!$isRunning && $nextPage!=$actualPage){
			$isRunning = true;
			pageJumper($(PAGEID + $actualPage), $(PAGEID + $nextPage), 'in-page-jump-appear', 'out-page-jump-appear');
			$actualPage = $nextPage;
		}
	});
	
	/* changing option menu header and variable filter */
	$('.dropdown-menu li a').on('click', function() {
		$("#"+$filter).removeClass(SELECTEDITEM);
		$(this).addClass(SELECTEDITEM);
		$filter = $(this).attr('id');
	});
	
	/* on search button click*/
    function onClickBtnSearch(){
		if((search_text = $("#search-text").val())!=""){;
			$.ajax({
				url: "../Php/ricerca.php",
				type: "GET",
				data: {$filter: search_text, },
				contentType: "application/json; charset=utf-8",
				dataType: "json",
				success:function(result){
					//converting the resultant JSon to a bidimensional array
					var cards = eval('(' + JSON.stringify(result) + ')');
					for(i = 0; i < cards.length; i++){
						$nome_carta = cards[i].nome;
						$colore_carta = cards[i].colore;
						$espansione_carta = cards[i].espansione;
						$tipo_carta = cards[i].colore;
						$rarita_carta = cards[i].rarita;
						$path_img_carta = cards[i].immagine;
						if($path_img_carta==""){
							$path_img_carta = PATHIMGDEFAULT;
						}
						$("#div-cards-container").innerHTML = "<div class='card'><div class='col-sm-6 col-md-4'><div class='thumbnail'><img class='card-img' src='" + $path_img_carta + "'><div class='caption'><h3>" + $nome_carta + "</h3><p>" + $colore_carta + "</p><p>" + espansione_carta + "</p><p>" + tipo_carta + "</p><p>" + rarita_carta + "</p><p><a href='#' class='btn btn-primary' role='button'>Aggiungi al mazzo</a></div></div></div></div>";
					}
				},
				error: function(richiesta,stato,errori){
					alert("Impossibile connettersi, riprova più tardi! Ci scusiamo per il disagio");
				}
			})
		}
	}
	
	
});