<?php

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


class beneficios extends fs_controller {

   // Almacena un array de documentos/articulos de venta
   public $documentos;
   //Almacena un array de cantidades de articulos
   public $cantidades;
   //Almacena el total neto de nueva_venta
   public $neto;
   //Almacena la tabla donde se encuentran los $documentos
   public $table;
   // Acumula el neto de documentos de venta
   public $total_neto;
   // Acumula el precio de coste de los articulos que hay del array de documentos de venta
   public $total_coste;
   // Diferencia entre total_neto y total_coste
   public $total_beneficio;
    // Acumula el precio de coste de los articulos en nueva_venta
   public $total_coste_art;
   public $test;
   public $test2;

   /**
    * Constructor del controlador (heredado de fs_controller)
    * 
    * Crea una entrada 'Beneficios' dentro del menú 'informes'
    */
   public function __construct() {
      /* Como no necesita aparecer en el menú añadimos los parametros opcionales FALSE */
      parent::__construct(__CLASS__, 'Beneficios', 'informes', FALSE, FALSE);
   }

   /**
    * Lógica privada principal del controlador (heredado de fs_controller)
    * 
    * Tu código PHP lo pondrás aquí
    */
   protected function private_core() {
      $this->share_extension();

      $this->test = "";
      $this->documentos = filter_input(INPUT_POST, 'docs', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
      $this->cantidades = filter_input(INPUT_POST, 'cantidades', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
      if (!empty($this->cantidades)) {
          $this->neto=filter_input(INPUT_POST, 'neto', FILTER_DEFAULT);
          $this->total_neto=$this->neto;
          $this->total_coste=$this->totalcoste($this->documentos, $this->cantidades);
          $this->total_beneficio = $this->beneficio($this->total_neto, $this->total_coste);



          //testear recepción de datos (necesario descomentar test y test2 en beneficios.html)
          /*if (!empty($this->documentos)) {

              $this->test = json_encode($this->documentos);
          } else {
              $this->test = "No se han recibido datos";
          }

          if (!empty($this->cantidades)) {
              $this->test2 = json_encode($this->cantidades);
          } else {
              $this->test2 = "No se han recibido cantidades";
          }*/
      }
       else{
           if (!empty($this->documentos)) {
               $this->table=$this->table($this->documentos);
               $this->total_neto = $this->totalneto($this->documentos);
               $this->total_coste = $this->totalcoste($this->documentos, $this->cantidades);
               $this->total_beneficio = $this->beneficio($this->total_neto, $this->total_coste);


               //testear recepción de datos (necesario descomentar test en beneficios.html)
               /*$this->test = json_encode($this->documentos);
           } else {
               $this->test = "No se han recibido datos";*/
           }
       }

   }

   /**
    * Extensión para integrarse en otras páginas (heredado de fs_controller)
    */
   private function share_extension() {

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

    /**
     * Devuelve la tabla donde están los documentos
     *
     * @param $array_documentos
     * @return string
     */
    public function table($array_documentos)
    {
        $value=array_shift($array_documentos);
        $sql="SELECT idfactura FROM facturascli WHERE codigo='$value'";
        $data=$this->db->select("$sql");
        if($data)
        {
            $data='facturascli';
        }
        else
        {
            $sql="SELECT idalbaran FROM albaranescli WHERE codigo='$value'";
            $data=$this->db->select("$sql");
            if ($data)
            {
                $data='albaranescli';
            }
            else
            {
                $sql="SELECT idpedido FROM pedidoscli WHERE codigo='$value'";
                $data=$this->db->select("$sql");
                if ($data)
                {
                    $data='pedidoscli';
                }
                else $data='presupuestoscli';
            }

        }

        return $data;
    }

    /**
    * Devuelve el importe total neto del array de documentos recibido
    * 
    * @param type $array_documentos
    * @return double
    */
   public function totalneto($array_documentos) {
      $totalneto = 0;

      // Buscamos los netos de las facturas recibidas en $array_documentos
      $sql = "SELECT neto FROM ".$this->table." WHERE codigo IN ('" . join("','", $array_documentos) . "')";
      $data = $this->db->select("$sql");

      foreach ($data as $d) {
         $totalneto = $totalneto + $d['neto'];
      }

      return $totalneto;
   }

   /**
    * Devuelve el importe total de coste del array de documentos recibido
    * 
    * @param type $array_documentos
    * @return double
    */
   public function totalcoste($array_documentos, $array_cantidades) {
       $totalcoste = 0;

        //si hay información en $array_cantidades estamos en nueva_venta
       if(!empty($this->cantidades)){


           // Buscamos los costes de los articulos recibidos en $array_documentos
          /* $sql = "SELECT preciocoste FROM articulos WHERE referencia IN ('" . join("','", $array_documentos) . "')";
           $data = $this->db->select("$sql");

           $pointer = 0;
           //por cada articulo calculamos su coste*cantidad incrementando el señalador de $array_cantidades con $pointer
           foreach ($data as $d) {
               $totalcoste = $totalcoste + ($d['preciocoste']*$array_cantidades[$pointer]);
               $pointer++;
           }*/
           $length = count($array_cantidades);
           for ($i=0;$i<$length;$i++){
               $sql = "SELECT preciocoste FROM articulos WHERE referencia='$array_documentos[$i]'";
               $data = $this->db->select("$sql");

               foreach ($data as $d) {
                   $totalcoste = $totalcoste + ($d['preciocoste']*$array_cantidades[$i]);
               }
           }

       }
       //si no hay información en $array_cantidades estamos tratando con documentos guardados y necesitamos saber a qué tabla pertenecen
       else{
           switch ($this->table)
           {
               case 'facturascli':
                   $doc='factura';
                   break;
               case 'albaranescli':
                   $doc='albaran';
                   break;
               case 'pedidoscli';
                   $doc='pedido';
                   break;
               case 'presupuestoscli':
                   $doc='presupuesto';
                   break;

           }

           // Buscamos la referencia, preciocoste, cantidad y pvptotal de las facturas recibidas en $array_facturas
// Alternativa 1
           /*
                 $sql = "SELECT articulos.referencia, articulos.preciocoste, lineasfacturascli.cantidad, lineasfacturascli.pvptotal ";
                 $sql .= "FROM articulos ";
                 $sql .= "LEFT JOIN lineasfacturascli ON lineasfacturascli.referencia = articulos.referencia ";
                 $sql .= "LEFT JOIN facturascli ON lineasfacturascli.idfactura = facturascli.idfactura ";
                 $sql .= "WHERE facturascli.codigo IN ('" . join("','", $array_facturas) . "')";
           */
// Alternativa 2

           $sql = "SELECT articulos.referencia, articulos.preciocoste, lineas".$this->table.".cantidad, lineas".$this->table.".pvptotal ";
           $sql .= "FROM articulos, ".$this->table." ";
           $sql .= "LEFT JOIN lineas".$this->table." ON lineas".$this->table.".id".$doc." = ".$this->table.".id".$doc." ";
           $sql .= "WHERE lineas".$this->table.".referencia = articulos.referencia AND ".$this->table.".codigo IN ('" . join("','", $array_documentos) . "')";


           $data = $this->db->select("$sql");
           if ($data) {
               foreach ($data as $d) {
                   $preciocoste = $d["preciocoste"];
                   $cantidad = $d["cantidad"];
                   $costeporcantidad = $preciocoste * $cantidad;
                   $totalcoste = $totalcoste + $costeporcantidad;
               }
           }
       }



      return $totalcoste;
   }

   /**
    * Devuelve el cálculo del beneficio
    * 
    * @param double $total_neto
    * @param double $total_coste
    * @return double
    */
   public function beneficio($total_neto, $total_coste) {
      return $total_neto - $total_coste;
   }



}
