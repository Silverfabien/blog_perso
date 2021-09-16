$(document).on("click", "#article-like", function (e) {
    e.preventDefault();

    let $this = $(this);

    var url = $this.attr("href");
    var entitySlug = $this.attr("data-entity-slug");
    var csrfToken = $this.attr("data-csrf-token");
    var findBtn = $this.find("span").html();

    nb = parseInt(findBtn);

    $.ajax({
        type: "POST",
        dataType: "json",
        data: {"entitySlug" : entitySlug, "csrfToken" : csrfToken},
        url: url,
        success: function (result) {
            if (result === "create") {
                $(".fa-thumbs-up").addClass("thumbs-like");
                $this.find("span").html(nb+1);
            } else {
                $(".fa-thumbs-up").removeClass("thumbs-like");
                $this.find("span").html(nb-1);
            }
        },
        error: function () {}
    });
});
