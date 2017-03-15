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

$(document).ready(function(){

    // Recogemos los codigos de los documentos en el listado
    var match = $("ol li.active");
    var number=0;

    // Array con los codigos de todos los documentos
    var docs = [];
    $(match).each(function () {
        docs.push($(this).text());
    });

    var txt=' <th class="text-right" width="80">Coste</th>';
    $("th:contains(Cantidad)").after(txt);
    // Añadimos el div donde irá la información
    /*var html = '<div id="beneficios" class="table-responsive"></div>';
    $(".table-responsive").append(html);*/

    // Consulta AJAX para generar la tabla de beneficios
    $.ajax({
        url: 'index.php?page=beneficios_documento',
        type: "post",
        data: ({docs: docs}),
        dataType: 'html',
        success: finished
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
        success: finished2
    });

});

function finished(result) {
    $('.table').find('tr').each(function(){
        $(this).find('td').eq(2).after(result);
    });
}

function finished2(result) {
    $('#beneficios').append(result);
}