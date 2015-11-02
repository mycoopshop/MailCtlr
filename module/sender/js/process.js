function process(action,number){
   
    if (number <= 0 ){
        return 1;
    }
    $.post( 
        action,
        function( data ) {
            //console.log( data );
            $( ".result" ).append( data );
            return process(action,number-1);
        }
    );

}
