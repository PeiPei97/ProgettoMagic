$(document).ready( function() {

/* Website navigation Mecchanism */ 
    const PAGEID = '#nav-page-';
    const ANIMATIONDELAY = 1000;
    const LOADELATE = 6000;
    const LASTPAGE = 3;
    const FIRSTPAGE = 1;
    
    $actualPage = 1;
    $isRunning = false;
    
	/* Disabled zoom */
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
	
	
});