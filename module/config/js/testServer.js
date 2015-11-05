function testServer(action){
    
    $.post( 
        action,
        function( data ) {
            //console.log(data);
            $( ".result" ).append( data );
            if (data==='non ci sono server') return 1;
            return process(action,number-1);
        }
    );

}
