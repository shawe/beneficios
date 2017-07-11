/*
 * This file is part of FacturaScripts
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
    // Recogemos los parámetros de la URL que está visitando el usuario, ya que contiene page
    //var userQuery = getQuery();

    // Recogemos los codigos de los documentos en el listado
    var match = $("table tr.clickableRow.success a").not('.cancel_clickable');

    //Si hay versiones de presupuesto solo recogemos el que hemos abierto
    if ($("#modal_versionesp").length === 1) {
        match = $("ol li.active");
    }

    //Si no encuentra nada se trata de un documento y tenemos que buscar en otra parte
    if (match[0] === null) {
        match = $("ol li.active");
        //si sigue sin encontrar nada estamos editando una factura con el plugin editar_faturas
        if (match[0] === null) {
            match = $('h2 small');
        }
    }

    // Array con los codigos de todos los documentos
    var docs = [];
    $(match).each(function () {
        docs.push($(this).text());
    });

    // Añadimos el div donde irá la información
    var html = '<div id="beneficios" class="table-responsive"></div>';
    $(".table-responsive").append(html);

    // Consulta AJAX para generar la tabla de beneficios
    $.ajax({
        url: 'index.php?page=beneficios',
        type: "post",
        data: ({docs: docs}),
        dataType: 'html',
        success: finished
    });

    //************************************************************************
    //Controlamos las mutaciones
    counter = 0;
    //Donde hay que observar las mutaciones
    var target = $("#lineas_documento").get(0);

    if (target != null) {
        // crear instancia observer
        var observer = new MutationObserver(function (mutations) {
            mutation_observer_callback(mutations);
        });

        // enviar el nodo target y las opciones para observer
        observer.observe(target, {
            attributes: true,
            childList: true,
            characterData: true,
            subtree: true
        });
    }


    //***************************************************************************

    //Guardar datos en la bdd cuando pulsamos el botón Guardar de un documento ya creado
    $('.btn-primary').click(function () {

        var bcodigo = match.text();

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


    //Eliminar datos cuando se elimina un documento
    $('.modal-footer .btn-danger').click(function () {
        var bcodigo = match.text();
        // alert(bcodigo);
        if (bcodigo !== '') {
            $.ajax({
                url: 'index.php?page=beneficios',
                type: "post",
                data: ({bcodigo: bcodigo}),
                dataType: 'html'
            });
        }
    });

});


// Función que controla las mutaciones
function mutation_observer_callback(mutations) {

    // acciones a realizar por cada mutación
    var mutationRecord = mutations[0];
    // acciones a realizar por cada mutación
    if (mutationRecord.addedNodes[0] !== undefined) {
        //agregar onchange
        var cantidad = document.getElementById('cantidad_' + counter);
        cantidad.addEventListener(
            'change',
            function () {
                show_msg();
            },
            true
        );
        var pvp = document.getElementById('pvp_' + counter);
        pvp.addEventListener(
            'change',
            function () {
                show_msg();
            },
            true
        );
        var dto = document.getElementById('dto_' + counter);
        dto.addEventListener(
            'change',
            function () {
                show_msg();
            },
            true
        );
        //lanzar el mensaje e incrementar el contador
        show_msg();
        counter++;
    } else if (mutationRecord.removedNodes[0] !== undefined) {
        show_msg();
    } else {
        //si no se han añadido ni borrado líneas estamos en un documento ya creado y hay que contar las lineas y añadir eventos
        var rowCount = $('#lineas_documento tr').length;

        for (var i = 0; i < rowCount; i++) {
            var lineacant = document.getElementById('cantidad_' + i);
            if (lineacant !== null) {
                lineacant.addEventListener(
                    'change',
                    function () {
                        show_msg();
                    },
                    true
                );
                var lineapvp = document.getElementById('pvp_' + i);
                lineapvp.addEventListener(
                    'change',
                    function () {
                        show_msg();
                    },
                    true
                );
                var lineadto = document.getElementById('dto_' + i);
                lineadto.addEventListener(
                    'change',
                    function () {
                        show_msg();
                    },
                    true
                );
                counter++;
            }

        }
    }

}

//Funcion para enviar los datos de beneficios
function show_msg() {

    //variable que contiene la refererncia del articulo
    var match = $("div.form-control a");
    // Array con los codigos de todos los articulos
    var docs = [];
    $(match).each(function () {
        docs.push($(this).text());
    });

    //variable que contiene el neto
    var neto = parseFloat($('#aneto').text());
    //variable que contiene las cantidades del articulo
    var cantidad = document.querySelectorAll('input[id^="cantidad_"]');
    //array con todas las cantidades
    var cantidades = [];
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

function getQuery() {
    var userQuery = {};
    location.search.substr(1).split('&').forEach(function (item) {
        userQuery[item.split('=')[0]] = item.split('=')[1];
    });
    return userQuery;
}
