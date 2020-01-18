function confirmRemove(url){
    if(confirm('Voulez-vous vraiment supprimer cet élément ? ')){
        $(location).attr('href',url);
    }
}

$(document).ready(function(){
    $('.header-hidden-button').click(function(){
        $('.header-hidden-menu').animate(
            {left:0}, 600, function(){
                $('.header-hidden-close').delay(800).click(function(){
                    $('.header-hidden-menu').animate({left:'-100vw'},600);
                });
            }
        )
    });

    $('.service-action').click(function(){
        $('html').animate({
            scrollTop: 800
        },'slow');
    });
});

