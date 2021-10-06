$(document).ready(function () {
    $(window).scroll(function () {
        if ($(document).scrollTop() > 50) {
            $('.sidebar').addClass('sidebarScroll');
        } else {
            $('.sidebar').removeClass('sidebarScroll');
        }
    })

    $("#sidebar-control").on("click", "#sidebar-close", function () {
        $("#sidebar-control").hide();
        $("#sidebar-open").show();
        $("#footer").removeClass("footer-copyright").addClass("footer-copyright-full");
        $("#base-admin").removeClass("admin-wrapper").addClass("admin-wrapper-full");
    })

    $("#sidebar-control-mini").on("click", "#sidebar-open", function () {
        $("#sidebar-control").show();
        $("#sidebar-open").hide();
        $("#footer").removeClass("footer-copyright-full").addClass("footer-copyright");
        $("#base-admin").removeClass("admin-wrapper-full").addClass("admin-wrapper");
    })

    let data = ['user', 'rank', 'article', 'comment'];
    data.forEach(item => {
        $(`#menu-${item}`).on("click", `#btn-submenu-${item}`, function () {
            $(`#menu-${item}`).toggleClass("active");
            $(`#submenu-${item}`).toggle();
        })
    });
})
