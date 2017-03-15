<?php
/**
 * Created by PhpStorm.
 * User: usuari
 * Date: 14/03/17
 * Time: 20:16
 */

class beneficios_documento extends fs_controller{

    // Almacena un array de documentos de venta
    public $documentos;
    //Almacena la tabla donde se encuentran los $documentos
    public $table;
    // Acumula el neto de documentos de venta
    public $total_neto;
    // Acumula el precio de coste de los articulos que hay del array de documentos de venta
    public $total_coste;
    // Diferencia entre total_neto y total_coste
    public $total_beneficio;

    public $coste;
    public $test;

    public function __construct() {
        /* Como no necesita aparecer en el menú añadimos los parametros opcionales FALSE */
        parent::__construct(__CLASS__, 'Beneficios_documento', 'informes', FALSE, FALSE);
    }

    protected function private_core(){

        $this->documentos = filter_input(INPUT_POST, 'docs', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($this->documentos)) {
            $this->table=$this->table($this->documentos);
            $this->total_neto = $this->totalneto($this->documentos);
            $this->total_coste = $this->totalcoste($this->documentos);
            $this->total_beneficio = $this->beneficio($this->total_neto, $this->total_coste);

            $this->coste=$this->coste();



            $this->test = json_encode($this->documentos);
        } else {
            $this->test = "No se han recibido datos";
        }

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

    public function coste(){
        $array_test=array(10,20,30);

        return $array_test;
    }

    /**
     * Devuelve el importe total de coste del array de documentos recibido
     *
     * @param type $array_documentos
     * @return double
     */
    public function totalcoste($array_documentos) {
        $totalcoste = 0;

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