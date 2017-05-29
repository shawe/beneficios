/**
 * Created by usuari on 23/05/17.
 */

$(document).ready(function(){
//alert('nventa!!');
    //variable global que contiene el nº de mutaciones (addednodes)
    counter=0;
    //variable que contiene el donde hay que observar las mutaciones
    var target = $("#lineas_albaran").get(0);


// create an observer instance
    var observer = new MutationObserver(function(mutations) {
        mutationObserverCallback(mutations);
    });


// pass in the target node, as well as the observer options
    observer.observe(target, {
        attributes: true,
        childList: true,
        characterData: true,
        subtree: true
    });

});

// mutaciones
function mutationObserverCallback(mutations) {

    /* Grab the first mutation */
    var mutationRecord = mutations[0];
    /* If a child node was added,
     show the msg after 1s  */
    if (mutationRecord.addedNodes[0] !== undefined ){

        //agregar onchange
        var cantidad = document.getElementById('cantidad_'+counter);
        cantidad.addEventListener(
            'change',
            function() { showMsg(); },
            true
        );
        var pvp = document.getElementById('pvp_'+counter);
        pvp.addEventListener(
            'change',
            function() { showMsg(); },
            true
        );
        var dto = document.getElementById('dto_'+counter);
        dto.addEventListener(
            'change',
            function() { showMsg(); },
            true
        );
        //lanzar el mensaje e incrementar el contador
        setTimeout(showMsg, 1000);
        counter++;
    }


    if ( mutationRecord.removedNodes[0] !== undefined)
        setTimeout(showMsg, 1000);
}

//Funcion para mostrar los beneficios
function showMsg() {


    //variable que contiene la refererncia del articulo
    match = $("div.form-control a");
    // Array con los codigos de todos los articulos
    var docs = [];
    $(match).each(function () {
        docs.push($(this).text());
    });

    //variable que contiene el neto
    var neto=parseFloat($('#aneto').text());
    //variable que contiene las cantidades del articulo
    var cantidad = document.querySelectorAll('input[id^="cantidad_"]');
    //array con todas las cantidades
    var cantidades=[];
    for (var index = 0; index < cantidad.length; index++) {
        cantidades.push(cantidad[index].value);
    }

    //borrar el div beneficios (si existe)
    $('#beneficios').remove();
    // Añadimos el div donde irá la información
    var html = '<div id="beneficios" class="table-responsive"></div>';
    $(".table-responsive").append(html);

    // Consulta AJAX para generar la tabla de beneficios
    $.ajax({
        url: 'index.php?page=beneficios',
        type: "post",
        data: ({docs: docs, cantidades:cantidades, neto:neto}),
        dataType: 'html',
        success: finished
    });
}

//insertar html
function finished(result) {
    $('#beneficios').append(result);
}


