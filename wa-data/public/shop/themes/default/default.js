$(document).ready(function () {

    // scroll-dependent animations
    $(window).scroll(function() {    
      	if ( $(this).scrollTop()>=45 ) {
            if (!$("#cart-summary").hasClass('empty')) {
              	$("#cart-summary").addClass( "fixed" );
            }
    	}
    	else if ( $(this).scrollTop()<40 ) {
    		$("#cart-summary").removeClass( "fixed" );
    	}    
    });
  
});
