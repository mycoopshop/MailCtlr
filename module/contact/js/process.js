var process_status = false;
var duplicate = 0;
var lista = 0;
var limit = 1;
var privacy = 0;
var active = 0;
var p_email = null;
function process_loop(action,number,total){
    if (number <= 0 || !process_status ){
        console.log("stop");
        $('#start').show();
        $('#stop').hide();
        $( "#progress" ).css( 'width', "100%");
        $( "#remain" ).html(0);
        $( "#progress" ).html("100%");
        return 1;
    }
    $.post( 
        action,
        { 
            curr: number, 
            tot: total,
            dup: duplicate,
            lista: lista,
            limit: limit,
            active: active,
            privacy: privacy,
            p_email: p_email
        },
        function( data ) {
            console.log(data);
            $( ".result" ).append( data.message );
            $( "#progress" ).css( 'width', data.progress+"%");
            $( "#remain" ).html(data.remain);
            $( "#progress" ).html(data.progress + "% complete");
            return process_loop(action,number-limit,total);
        }, "json"
    );
    duplicate = 0;
}
function process_stop(){
    console.log("stop");
    process_status = false;
    $('#start').show();
    $('#stop').hide();
}
function process_start(action,number,total){
    process_status = true;
    process_loop(action,number,total);
}
function process(action,number,total){
    console.log("start");
    $('#start').hide();
    $('#stop').show();
    
    duplicate = $( "#duplicate" ).val();
    lista = $( "#lista" ).val();
    limit = $( "#limit" ).val();
    privacy = $("#privacy").val();
    active = $("#active").val();
    p_email = $("#p_email").val();
    
    process_start(action,number,total);
}
