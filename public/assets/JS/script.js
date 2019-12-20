function confirmRemove(url){
    if(confirm('Voulez-vous vraiment supprimer cette offre ? ')){
        $(location).attr('href',url);
    }

}
