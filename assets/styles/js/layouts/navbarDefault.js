$(document).ready(function () {
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
    })
})
