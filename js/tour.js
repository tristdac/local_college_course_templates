window.onload = function(){
    require(['jquery'], function($) {
        $(document).ready(function() { 
        // if($('.pagelayout-course').length()) {
            // $('.path-course span[id*="tour-step-tool_usertours_"]').css('display','none');
            // $('.pagelayout-course #action-menu-1-menubar #action-menu-1-menu').addClass('show').attr('x-placement','bottom-end').css({'position':'absolute','transform':'translate3d(-300px, 24px, 0px)','top':'0px','left':'0px','will-change':'transform'});
            // $('.pagelayout-course #action-menu-1-menubar .dropdown').addClass('show').attr('aria-expanded','true');
            // $('.pagelayout-course #action-menu-1-menubar .dropdown').addClass('show');
            // $('.pagelayout-course #action-menu-1-menubar .dropdown a').trigger( "click" );
            // $('.btn-danger').click(function() {
             var $this = $('.dropdown-menu-right');
             console.log($this);
              if($this.attr('style')){
                console.log($this.attr('style'));
                $this.parent().addClass("class-new");
              }
            $(document).on("click", ".btn-danger" , function() {
                $('.path-course-view #page-header .action-menu  .dropdown-menu').removeClass('show').removeAttr('style','aria-expanded');
                $("html, body").animate({ scrollTop: 0 }, "slow");
            });
            setTimeout(function(){
                $(".path-course-view .usertour a").trigger( "click" );
                $('.path-course-view span[id*="tour-step-tool_usertours_"]').css('display','none');
                $('.path-course-view #page-header .action-menu .menubar').addClass('show').css({'display':'block'});
                $('.path-course-view #page-header .action-menu  .dropdown-menu').addClass('show').attr('aria-expanded','true').css({'display':'block'});
            }, 1000);
        });
    });
}