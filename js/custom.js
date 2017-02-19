$('document').ready(function() {
    $('.trigger-login').click(function () {
        $('#myModal').remove();

        $.ajax({
            url: "forms.php?mode=login",
            success: function (html) {
                $('.container').append(html);
                $("#myModal").modal();
            },
            error: function () {
                console.log('error!');
            }
        })
    });

    $('.trigger-logout').click(function () {
        $('#myModal').remove();

        $.ajax({
            url: "processor.php",
            type: "POST",
            data: {
                mode: "logout"
            },
            success: function (html) {
                //reload
                location.reload();
            },
            error: function () {
                console.log('error!');
            }
        })
    })


});