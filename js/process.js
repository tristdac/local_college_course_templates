window.onload = function(){
    require(['jquery'], function($) {
        var current_progress = 0;     
        var interval = setInterval(function() {
            current_progress += 2;
            $("#dynamic")
            .css("width", current_progress + "%")
            .attr("aria-valuenow", current_progress);
            if (current_progress <= 10) {
                $("#dynamic").text(current_progress + "");
            } else if (current_progress <= 20) {
                $("#dynamic").text(current_progress + "%");
            } else {
                $("#dynamic").text(current_progress + "% Complete");
            }
            if (current_progress >= 100) {
            $("#gotocourse").removeAttr("hidden"); 
                clearInterval(interval);
          }
        }, 100);
    });
}