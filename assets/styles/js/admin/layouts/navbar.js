$(document).ready(function () {
    $('.navTrigger').click(function () {
        $(this).toggleClass('active');
        $("#navList").toggleClass("showList");
        $("#navList").fadeIn();
    });

    $(window).scroll(function () {
        if ($(document).scrollTop() > 50) {
            $('.myNav').addClass('navScroll');
        } else {
            $('.myNav').removeClass('navScroll');
        }
    })
})
