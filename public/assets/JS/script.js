function confirmRemove(url){
    if(confirm('Voulez-vous vraiment supprimer cet élément ? ')){
        $(location).attr('href',url);
    }
}

$(document).ready(function(){
    $('.service-header-hidden-button').click(function(){
        $('.service-header-hidden-menu').animate(
            {left:0}, 600, function(){
                $('.service-header-hidden-close').delay(800).click(function(){
                    $('.service-header-hidden-menu').animate({left:'-100vw'},600);
                });
            }
        )
    });
});

