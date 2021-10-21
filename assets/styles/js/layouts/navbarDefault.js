$(document).ready(function () {
    $("a.scroll").on("click", function (evt) {
        evt.preventDefault();
        let target = $(this).attr("href");
        $("html").scrollTop($(target).offset().top - 68);
    });

    $('.navTrigger').click(function () {
        $(this).toggleClass('active');
        $("#navList").toggleClass("showList");
        $("#navList").fadeIn();
    });

    $(window).scroll(function () {
        if ($(document).scrollTop() > 50) {
            $('.myNavDefault').addClass('navScroll');
        } else {
            $('.myNavDefault').removeClass('navScroll');
        }
    });
})
