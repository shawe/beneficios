/*
 * This file is part of Beneficios
 * Copyright (C) 2017  Albert Dilme  
 * Copyright (C) 2017  Francesc Pineda Segarra  shawe.ewahs@gmail.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function () {
    //var cantidad = $("#cantidad_" + i).val().replace(",", ".");

    // Recogemos los parámetros de la URL que está visitando el usuario, ya que contiene page
    var userQuery = getQuery();
    var page = userQuery.page;
    //console.log(userQuery.id);
    //console.log(userQuery.page);

    // Recogemos los codigos de los documentos en el listado y los ponemos en el array docs
    var dataCodigo= $("[data-codigo]");
    console.log(dataCodigo);
    var docs=[];
    dataCodigo.each(function(){
        docs.push($(this).attr("data-codigo"));
    });
    //console.log(docs);
    //console.log(dataCodigo);
    // Añadimos el div donde irá la información
    var html = '<div id="beneficios" class="table-responsive"></div>';
    $("#lineas, #lineas_a").append(html);
    
    // Consulta AJAX para generar la tabla de beneficios
    $.ajax({
        url: 'index.php?page=beneficios',
        type: "post",
        data: ({docs: docs, page:page}),
        dataType: 'html',
        success: finished
    });

    //************************************************************************
    //Controlamos las mutaciones (var global)
    
    //Donde hay que observar las mutaciones
    var target = $("#lineas_doc" ).get(0);

    if (target != null) {
        // crear instancia observer
        var observer = new MutationObserver(function (mutations) {
            mutation_observer_callback(mutations);
        });

        // enviar el nodo target y las opciones para observer
        observer.observe(target, {
            attributes: true,
            childList: true,
            characterData: false,
            subtree: true
        });
    }

/**
    $('#lineas_doc tr td input[type="text"],#lineas_doc tr td input[type="number"]').bind('keyup change', function() {
        show_msg();
    });
 */

    //***************************************************************************

    //Guardar datos en la bdd cuando pulsamos el botón Guardar de un documento ya creado
    $($('button.btn-primary')[1]).click(function () {
        var bcodigo = dataCodigo.attr("data-codigo");

        if (bcodigo) {
            var bneto = parseFloat($('#b_neto').text().replace(',', '.'));
            var bcoste = parseFloat($('#b_coste').text().replace(',', '.'));
            var bbeneficio = parseFloat($('#b_beneficio').text().replace(',', '.'));
            var array_beneficios = [bcodigo, bneto, bcoste, bbeneficio];
            $.ajax({
                url: 'index.php?page=beneficios',
                type: "post",
                data: ({array_beneficios: array_beneficios, page:page}),
                dataType: 'html'
            });
        }
    });


    //Guardar datos en la bdd cuando pulsamos el botón Guardar en nueva_venta
    $('#btn_guardar1, #btn_guardar2').click(function () {

        var bcodigo = $('input[name="tipo"]:checked').val();

        if (bcodigo !== '') {
            var bneto = parseFloat($('#b_neto').text().replace(',', '.'));
            var bcoste = parseFloat($('#b_coste').text().replace(',', '.'));
            var bbeneficio = parseFloat($('#b_beneficio').text().replace(',', '.'));
            var array_beneficios = [bcodigo, bneto, bcoste, bbeneficio];
            $.ajax({
                url: 'index.php?page=beneficios',
                type: "post",
                data: ({array_beneficios: array_beneficios}),
                dataType: 'html'
            });
        }
    });


});

// Función que controla las mutaciones
function mutation_observer_callback(mutations) {
    show_msg();
}

//Funcion para enviar los datos de beneficios
function show_msg() {
    //variable que contiene la refererncia del articulo
    var match = $("[data-ref]");
    //console.log(match.text);
    // Array con los codigos de todos los articulos
    var docs = [];
    $(match).each(function () {
        docs.push($(this).attr("data-ref"));
    });

    //variable que contiene el neto
    var neto = parseFloat($('#aneto').text());
    //variable que contiene las cantidades del articulo
    var cantidad = document.querySelectorAll('input[id^="cantidad_"]');
    //console.log($('input[id^="cantidad_"]'));
    //array con todas las cantidades
    var cantidades = [];
    for (var index = 0; index < cantidad.length; index++) {
        //cantidad[index].value.replace('.', ',');
        //console.log(cantidad[index]);
        //cantidad[index].value=cantidad[index].value;
        //cantidad[index].setAttribute('type', 'text');
        cantidades.push(cantidad[index].value);
    }

    //borrar el div beneficios (si existe)
    $('#beneficios').remove();

    // Añadimos el div donde irá la información
    var html = '<div id="beneficios" class="table-responsive"></div>';
    $("#lineas, #lineas_a").append(html);

    // Consulta AJAX para generar la tabla de beneficios
    $.ajax({
        url: 'index.php?page=beneficios',
        type: "post",
        data: ({docs: docs, cantidades: cantidades, neto: neto}),
        dataType: 'html',
        success: finished
    });
}

//función para insertar el resultado
function finished(result) {
    var div = $('#beneficios');
    //controlamos que no exista ya información para evitar duplicidades
    if (div.is(':empty')) {
        //insertamos el resultado
        div.append(result);
    }

}

//función que devuelve los parámetros de la URL visitada
function getQuery() {
    var userQuery = {};
    location.search.substr(1).split('&').forEach(function (item) {
        userQuery[item.split('=')[0]] = item.split('=')[1];
    });
    return userQuery;
}
