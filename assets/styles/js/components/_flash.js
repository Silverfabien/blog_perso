(function ($) {
    $(document).ready(function () {
        function leave(i) {
            timerFlash = setTimeout(function () {
                $(i).fadeOut(3000, function () {
                    $(this).hide();
                });
            }, 1500);
        }

        let messageFlash = '#m-flash';
        let blockFlash = '#b-flash';

        $(messageFlash).fadeIn(1000);
        timerFlash = setTimeout(function () {
            $(messageFlash).fadeOut(5000);
        }, 15000);

        $(messageFlash).on('mouseover', function () {
            window.clearTimeout(timerFlash);
            $(this).stop(true).fadeIn(500);
        });

        $(messageFlash).on('mouseleave', function () {
            leave(this);
        });

        $(blockFlash).on('click', '#delete', function (e) {
            e.preventDefault();
            $(messageFlash).hide();
        });
    });
})(jQuery);
