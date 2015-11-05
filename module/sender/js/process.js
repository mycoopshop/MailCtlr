var process_status = false;

function process_loop(action,number){
   
    if (number <= 0 || !process_status ){
        $('#stop').hide();
        $('#start').show();
        return 1;
    }
    $.post( 
        action,
        function( data ) {
            //console.log(data);
            $( ".result" ).append( data + "\n\r" );
            if (data==='non ci sono server') return 1;
            return process_loop(action,number-1);
        }
    );

}

function process_stop(){
    process_status = false;    
}
function process_start(action,number){
    process_status = true;
    process_loop(action,number);
}

function process(action,number){
    $('#start').hide();
    $('#stop').show();
    process_start(action,number);
}
