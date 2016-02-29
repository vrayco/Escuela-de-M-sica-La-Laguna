
jQuery(document).ready(function() {

    $("body").on("click", ".selector-curso-academico-item", function () {
        var url = $(this).data("url-set");
        var url_refresh = $(this).data("url-refresh");

        $.ajax({
            url: url,
            context: document.body
        }).done(function (data) {
                window.location.href = url_refresh;
            })
            .fail(function() {
                Materialize.toast("¡Ups! No se ha podido seleccionar el Curso Académico",4000,'warning');
            });
    });

});

