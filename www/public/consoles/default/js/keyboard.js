$(function(){
    var keyboard = '';
    var key_arr = [];
    $(document).on("keydown",function(event){  //event.which
        keyboard = event.which;
        key_arr.push(keyboard);
        $('#keyboard').val(key_arr);
        console.log(key_arr);
        if ($('#keyboard').val().length>=5) {
            if ($('#keyboard').val()=='18,49') {
                // location="/#/acorn/submitApply/#2";
            }
            else{
                key_arr = [];
                keyboard = '';
                $('#keyboard').val(null);
            }
        }
    });
});
