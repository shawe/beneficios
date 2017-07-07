<?php
/**
 * This file is part of FacturaScripts
 * Copyright (C) 2017  Albert Dilme
 * Copyright (C) 2017  Francesc Pineda Segarra <shawe.ewahs@gmail.com>
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

require_model('beneficio.php');

/**
 * Clase beneficios
 */
class beneficios extends fs_controller
{
    /**
     * Almacena un array de documentos/articulos de venta
     * @var array
     */
    public $documentos;

    /**
     * Almacena un array de documentos existentes en la bdd
     * @var array
     */
    public $documentos_bdd;

    /**
     * Almacena un array de cantidades de articulos
     * @var array
     */
    public $cantidades;

    /**
     * Almacena un array con los datos a guardar
     * @var array
     */
    public $datos;

    /**
     * Almacena el total neto de nueva_venta
     * @var float
     */
    public $neto;

    /**
     * Almacena la tabla donde se encuentran los $documentos
     * @var float
     */
    public $table;

    /**
     * Acumula el neto de documentos de venta
     * @var float
     */
    public $total_neto;

    /**
     * Acumula el precio de coste de los articulos que hay del array de documentos de venta
     * @var float
     */
    public $total_coste;

    /**
     * Diferencia entre total_neto y total_coste
     * @var float
     */
    public $total_beneficio;

    /**
     * Acumula el precio de coste de los articulos en nueva_venta
     * @var float
     */
    public $total_coste_art;

    /**
     * Objeto modelo
     * @var beneficio
     */
    public $beneficio;

    /**
     * Para testear
     * @var type
     */
    public $test;

    /**
     * Para testear
     * @var type
     */
    public $test2;

    /**
     * Constructor del controlador (heredado de fs_controller)
     *
     * Crea una entrada 'Beneficios' dentro del menú 'informes'
     */
    public function __construct()
    {
        /* Como no necesita aparecer en el menú añadimos los parametros opcionales FALSE */
        parent::__construct(__CLASS__, 'Beneficios', 'informes', false, false);
    }

    /**
     * Devuelve la tabla donde están los documentos
     *
     * @param $array_documentos
     *
     * @return string
     */
    public function table($array_documentos)
    {
        $value = array_shift($array_documentos);
        $sql = "SELECT idfactura FROM facturascli WHERE codigo='$value'";
        $data = $this->db->select("$sql");
        if ($data) {
            $data = 'facturascli';
        } else {
            $sql = "SELECT idalbaran FROM albaranescli WHERE codigo='$value'";
            $data = $this->db->select("$sql");
            if ($data) {
                $data = 'albaranescli';
            } else {
                $sql = "SELECT idpedido FROM pedidoscli WHERE codigo='$value'";
                $data = $this->db->select("$sql");
                if ($data) {
                    $data = 'pedidoscli';
                } else {
                    $data = 'presupuestoscli';
                }
            }
        }

        return $data;
    }

    /**
     * Devuelve el importe total neto del array de documentos recibido
     *
     * @param array $array_documentos
     *
     * @return float
     */
    public function totalneto($array_documentos)
    {
        $totalneto = 0;

        // Buscamos los netos de las facturas recibidas en $array_documentos
        $sql = 'SELECT neto FROM ' . $this->table . " WHERE codigo IN ('" . implode("','", $array_documentos) . "')";
        $data = $this->db->select($sql);

        foreach ($data as $d) {
            $totalneto += $d['neto'];
        }

        return (float)$totalneto;
    }

    /**
     * Devuelve el importe total de coste del array de documentos recibido
     *
     * @param array $array_documentos
     * @param array $array_cantidades
     *
     * @return float
     */
    public function totalcoste($array_documentos, $array_cantidades)
    {
        $totalcoste = 0;

        //si hay información en $array_cantidades estamos en nueva_venta
        if (!empty($this->cantidades)) {
            // Buscamos los costes de los articulos recibidos en $array_documentos
            foreach ($array_documentos as $key => $document) {
                $sql = "SELECT preciocoste FROM articulos WHERE referencia='" . $document . "'";
                $data = $this->db->select($sql);

                foreach ($data as $d) {
                    $totalcoste += ($d['preciocoste'] * $array_cantidades[$key]);
                }
            }
        } else {
            //si no hay información en $array_cantidades estamos tratando con documentos guardados y necesitamos saber a qué tabla pertenecen
            switch ($this->table) {
                case 'facturascli':
                    $doc = 'factura';
                    break;
                case 'albaranescli':
                    $doc = 'albaran';
                    break;
                case 'pedidoscli';
                    $doc = 'pedido';
                    break;
                case 'presupuestoscli':
                    $doc = 'presupuesto';
                    break;
            }

            // Buscamos la referencia, preciocoste, cantidad y pvptotal de las facturas recibidas en $array_facturas
            $sql = 'SELECT articulos.referencia, articulos.preciocoste, lineas' . $this->table . '.cantidad, lineas' . $this->table . '.pvptotal'
                . ' FROM articulos, ' . $this->table
                . ' LEFT JOIN lineas' . $this->table . ' ON lineas' . $this->table . '.id' . $doc . ' = ' . $this->table . '.id' . $doc
                . ' WHERE lineas' . $this->table . '.referencia = articulos.referencia AND '
                . $this->table . ".codigo IN ('" . implode("','", $array_documentos) . "')";


            $data = $this->db->select("$sql");
            if ($data) {
                foreach ($data as $d) {
                    $preciocoste = $d['preciocoste'];
                    $cantidad = $d['cantidad'];
                    $costeporcantidad = $preciocoste * $cantidad;
                    $totalcoste += $costeporcantidad;
                }
            }
        }

        return (float)$totalcoste;
    }

    /**
     * Devuelve el cálculo del beneficio
     *
     * @param float $total_neto
     * @param float $total_coste
     *
     * @return float
     */
    public function calc_beneficio($total_neto, $total_coste)
    {
        return $total_neto - $total_coste;
    }

    /**
     * Almacena beneficios en la bdd
     */
    public function guardar()
    {
        $this->beneficio = new beneficio();

        $code = $this->datos[0];
        //si tenemos parte de la tabla en vez del código procesamos
        switch ($code) {
            case 'presupuesto':
                $this->beneficio->codigo = $this->beneficio->lastcod('presupuesto');
                break;
            case 'pedido':
                $this->beneficio->codigo = $this->beneficio->lastcod('pedido');
                break;
            case 'albaran':
                $this->beneficio->codigo = $this->beneficio->lastcod('albaran');
                break;
            case 'factura':
                $this->beneficio->codigo = $this->beneficio->lastcod('factura');
                break;
            default:
                $this->beneficio->codigo = $code;
        }

        $this->beneficio->precioneto = $this->datos[1];
        $this->beneficio->preciocoste = $this->datos[2];
        $this->beneficio->total_beneficio = $this->datos[3];
        $this->beneficio->save();
    }

    /**
     * Lógica privada principal del controlador (heredado de fs_controller)
     *
     * Tu código PHP lo pondrás aquí
     */
    protected function private_core()
    {
        $this->share_extension();

        $this->test = '';
        $this->documentos = filter_input(INPUT_POST, 'docs', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $this->cantidades = filter_input(INPUT_POST, 'cantidades', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        //si eliminamos un documento eliminamos en la bdd
        if (isset($_POST['bcodigo'])) {
            $this->beneficio = new beneficio();
            $this->beneficio->codigo = filter_input(INPUT_POST, 'bcodigo', FILTER_DEFAULT);
            $this->beneficio->delete();
        }

        //si guardamos un documento actualizamos o insertamos en la bdd
        if (isset($_POST['array_beneficios'])) {
            $this->datos = filter_input(INPUT_POST, 'array_beneficios', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $this->guardar();
        } else {
            //si nos pasan cantidades estamos creando o editando un documento
            if (!empty($this->cantidades)) {
                $this->neto = filter_input(INPUT_POST, 'neto', FILTER_DEFAULT);
                $this->total_neto = $this->neto;
                $this->total_coste = $this->totalcoste($this->documentos, $this->cantidades);
                $this->total_beneficio = $this->calc_beneficio($this->total_neto, $this->total_coste);


                //testear recepción de datos (necesario descomentar test y test2 en beneficios.html)
                /* if (!empty($this->documentos)) {

                  $this->test = json_encode($this->documentos);
                  } else {
                  $this->test = "No se han recibido datos";
                  }

                  if (!empty($this->cantidades)) {
                  $this->test2 = json_encode($this->cantidades);
                  } else {
                  $this->test2 = "No se han recibido cantidades";
                  } */
            } else {
                if (!empty($this->documentos)) {
                    $totalneto_bdd = 0;
                    $totalcoste_bdd = 0;
                    $totalbeneficio_bdd = 0;

                    //comprovar códigos existentes en la bdd
                    $ben = new beneficio();
                    $this->documentos_bdd = $ben->collect($this->documentos);
                    //recogemos los datos de la bdd y sumamos
                    foreach ($this->documentos_bdd as $d) {
                        $totalneto_bdd += $d['precioneto'];
                        $totalcoste_bdd += $d['preciocoste'];
                        $totalbeneficio_bdd += $d['beneficio'];
                        //quitamos del array los códigos que ya están en la bdd beneficios
                        if (($key = array_search($d['codigo'], $this->documentos, false)) !== false) {
                            unset($this->documentos[$key]);
                        }
                    }

                    //calculamos valores que no están en la bdd sobre el precio de coste actual del artículo
                    $this->table = $this->table($this->documentos);
                    $this->total_neto = $this->totalneto($this->documentos);
                    $this->total_coste = $this->totalcoste($this->documentos, $this->cantidades);
                    $this->total_beneficio = $this->calc_beneficio($this->total_neto, $this->total_coste);
                    //sumamos los valores que están en la bdd y los que no están
                    $this->total_neto += $totalneto_bdd;
                    $this->total_coste += $totalcoste_bdd;
                    $this->total_beneficio += $totalbeneficio_bdd;


                    //testear recepción de datos (necesario descomentar test en beneficios.html)
                    /* if (!empty($this->documentos_bdd)) {
                      $this->test = json_encode($this->documentos_bdd);
                      }
                      else {
                      $this->test = "No se han recibido datos";
                      } */
                }
            }
        }
    }

    /**
     * Extensión para integrarse en otras páginas (heredado de fs_controller)
     */
    private function share_extension()
    {
        $fsext = new fs_extension();
        $fsext->name = 'beneficios_facturas';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_facturas';
        $fsext->type = 'head';
        $fsext->text = ' <script type="text/javascript" src="plugins/beneficios/view/js/beneficios.js"></script>';
        $fsext->save();

        $fsext = new fs_extension();
        $fsext->name = 'beneficios_albaranes';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_albaranes';
        $fsext->type = 'head';
        $fsext->text = ' <script type="text/javascript" src="plugins/beneficios/view/js/beneficios.js"></script>';
        $fsext->save();

        $fsext = new fs_extension();
        $fsext->name = 'beneficios_pedidos';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_pedidos';
        $fsext->type = 'head';
        $fsext->text = ' <script type="text/javascript" src="plugins/beneficios/view/js/beneficios.js"></script>';
        $fsext->save();

        $fsext = new fs_extension();
        $fsext->name = 'beneficios_presupuestos';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_presupuestos';
        $fsext->type = 'head';
        $fsext->text = ' <script type="text/javascript" src="plugins/beneficios/view/js/beneficios.js"></script>';
        $fsext->save();

        $fsext = new fs_extension();
        $fsext->name = 'beneficios_factura';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_factura';
        $fsext->type = 'head';
        $fsext->text = ' <script type="text/javascript" src="plugins/beneficios/view/js/beneficios.js"></script>';
        $fsext->save();

        $fsext = new fs_extension();
        $fsext->name = 'beneficios_albaran';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_albaran';
        $fsext->type = 'head';
        $fsext->text = ' <script type="text/javascript" src="plugins/beneficios/view/js/beneficios.js"></script>';
        $fsext->save();

        $fsext = new fs_extension();
        $fsext->name = 'beneficios_pedido';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_pedido';
        $fsext->type = 'head';
        $fsext->text = ' <script type="text/javascript" src="plugins/beneficios/view/js/beneficios.js"></script>';
        $fsext->save();

        $fsext = new fs_extension();
        $fsext->name = 'beneficios_presupuesto';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_presupuesto';
        $fsext->type = 'head';
        $fsext->text = ' <script type="text/javascript" src="plugins/beneficios/view/js/beneficios.js"></script>';
        $fsext->save();

        $fsext = new fs_extension();
        $fsext->name = 'beneficios_nueva_venta';
        $fsext->from = __CLASS__;
        $fsext->to = 'nueva_venta';
        $fsext->type = 'head';
        $fsext->text = ' <script type="text/javascript" src="plugins/beneficios/view/js/beneficios.js"></script>';
        $fsext->save();

        $fsext = new fs_extension();
        $fsext->name = 'beneficios_editar_factura';
        $fsext->from = __CLASS__;
        $fsext->to = 'editar_factura';
        $fsext->type = 'head';
        $fsext->text = ' <script type="text/javascript" src="plugins/beneficios/view/js/beneficios.js"></script>';
        $fsext->save();
    }
}
