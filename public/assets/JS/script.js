function confirmRemove(url){
    if(confirm('Voulez-vous vraiment supprimer cet élément ? ')){
        $(location).attr('href',url);
    }
}
