$(document).ready(function () {

    function supports_input_placeholder() {
        var i = document.createElement('input');
        return 'placeholder' in i;
    }

    if (!supports_input_placeholder())
    {
        var $placeholder = $(":input[placeholder]");
        $placeholder.each(function () {
            if ($(this).attr("type") != "password") {
                var $message = $(this).attr("placeholder");
                $(this).attr("value", $message);
                $(this).css("color", "ccc");
            }
        });
        $placeholder.focus(function () {
            var $value = $(this).attr("value");
            var $placeholderTxt = $(this).attr("placeholder");

            if ($value == $placeholderTxt)
            {
                $(this).attr("value", "");
                $(this).css("color", "000");
            }
        });
        $placeholder.blur(function () {
            var $value = $(this).attr("value");
            var $placeholderTxt = $(this).attr("placeholder");

            if ($value == '')
            {
                $(this).attr("value", $placeholderTxt);
                $(this).css("color", "ccc");
            }
        });
    }
});
