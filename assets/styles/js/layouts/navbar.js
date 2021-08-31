$(document).ready(function () {
    $('.navTrigger').click(function () {
        $(this).toggleClass('active');
        $("#navList").toggleClass("showList");
        $("#navList").fadeIn();
    });
})
