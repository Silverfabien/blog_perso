$(document).on('click', '#editComment', function (e) {
    e.preventDefault();

    let $this = $(this);

    var url = $this.attr("href");
    var commentId = $this.attr("data-entity-id");
    var csrfToken = $this.attr("data-csrf-token");

    $.ajax({
        type: "POST",
        dataType: "json",
        data: {"commentId": commentId, "csrfToken": csrfToken},
        url: url,
        success: function (result) {
            console.log(result)
            document.getElementById('mod').innerHTML = result;
            if (CKEDITOR.instances["article_edit_comment_content"]) { CKEDITOR.instances["article_edit_comment_content"].destroy(true); delete CKEDITOR.instances["article_edit_comment_content"]; }
            CKEDITOR.replace("article_edit_comment_content", {"uiColor":"#BBBBBB","toolbar":[["Bold","Italic","Underline","Strike"]],"extraPlugins":"wordcount","wordcount":{"maxCharCount":2000,"showCharCount":true},"language":"fr"});
        },
        error: function () {}
    });
});
