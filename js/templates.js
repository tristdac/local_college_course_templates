window.onload = function(){
    require(['jquery'], function($) {
        $(document).ready(function() { 
            $(document).on("click", ".filter-button" , function() {

                var value = $(this).attr('data-filter');
                console.log(value+' clicked');
                if(value == "all")
                {
                    //$('.filter').removeClass('hidden');
                    $('.filter').show('500');
                }
                else
                {
        //            $('.filter[filter-item="'+value+'"]').removeClass('hidden');
        //            $(".filter").not('.filter[filter-item="'+value+'"]').addClass('hidden');
                    $(".filter").not('.'+value).hide('500');
                    $('.filter').filter('.'+value).show('500');
                    
                }
            });
    
            if ($(".filter-button").removeClass("active")) {
                $(this).removeClass("active");
            }
            $(this).addClass("active");

            $(document).on("click", "#begin_import" , function() {
                $('#begin_import').text("Preparing for import...");
                $('#begin_import').append(' <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>');
            });
            function submitform(){
                var formData = JSON.stringify($("#myForm").serializeArray());
                var xhr = new XMLHttpRequest();
                xhr.open(form.method, form.action, true);
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                xhr.send(formData);
            }
        });
    });
}