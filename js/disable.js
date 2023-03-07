window.onload = function(){
    require(['jquery'], function($) {
        $(document).ready(function() { 
            console.log('disable it');
            $('a[data-target^="#ow_confirm"').addClass("disabled");
        });
    });
}