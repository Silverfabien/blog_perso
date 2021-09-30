$(document).ready(function () {
    $(window).scroll(function () {
        if ($(document).scrollTop() > 50) {
            $('.sidebar').addClass('sidebarScroll');
        } else {
            $('.sidebar').removeClass('sidebarScroll');
        }
    })

    $("#sidebar-control").on("click", "#sidebar-close", function (e) {
        e.preventDefault();
        $("#sidebar-control").hide();
        $("#sidebar-open").show();
        $("#footer").removeClass("footer-copyright").addClass("footer-copyright-full");
        $("#base-admin").removeClass("admin-wrapper").addClass("admin-wrapper-full");
    })

    $("#sidebar-control-mini").on("click", "#sidebar-open", function (e) {
        e.preventDefault();
        $("#sidebar-control").show();
        $("#sidebar-open").hide();
        $("#footer").removeClass("footer-copyright-full").addClass("footer-copyright");
        $("#base-admin").removeClass("admin-wrapper-full").addClass("admin-wrapper");
    })

    $("#menu-user").on("click", "#btn-submenu-user", function (e){
        e.preventDefault();
        $("#menu-user").toggleClass("active");
        $("#submenu-user").toggle();
    })

    $("#menu-rank").on("click", "#btn-submenu-rank", function (e){
        e.preventDefault();
        $("#menu-rank").toggleClass("active");
        $("#submenu-rank").toggle();
    })
})
