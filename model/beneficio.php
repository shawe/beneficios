<?php

/**
 * Clase beneficio
 */
class beneficio extends fs_model
{
    /**
     * clave primaria. varchar(20). código del documento
     *
     * @var null
     */
    public $codigo;
    /**
     * double.total neto del documento
     *
     * @var int
     */
    public $precioneto;
    /**
     * double. total coste del documento
     *
     * @var int
     */
    public $preciocoste;
    /**
     * double. total beneficio del documento
     *
     * @var int
     */
    public $total_beneficio;

    /**
     * beneficio constructor.
     *
     * @param bool $d
     */
    public function __construct($d = false)
    {
        parent::__construct('beneficios');
        if ($d) {
            $this->codigo = $d['codigo'];
            $this->precioneto = floatval($d['precioneto']);
            $this->preciocoste = floatval($d['preciocoste']);
            $this->total_beneficio = floatval($d['beneficio']);
        } else {
            /// valores predeterminados
            $this->codigo = null;
            $this->precioneto = 0;
            $this->preciocoste = 0;
            $this->total_beneficio = 0;
        }
    }

    /**
     * @return string
     */
    public function install()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function exists()
    {
        if (is_null($this->codigo)) {
            return false;
        } else {
            return $this->db->select('SELECT * FROM beneficios WHERE codigo = ' . $this->var2str($this->codigo) . ';');
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        if ($this->exists()) {
            /// UPDATE beneficios SET ... WHERE ...;
            $sql = " UPDATE beneficios SET precioneto = " . $this->var2str($this->precioneto) . ", preciocoste = " . $this->var2str($this->preciocoste) . ", beneficio = " . $this->var2str($this->total_beneficio) . "
                WHERE codigo = " . $this->var2str($this->codigo) . ";";
            return $this->db->exec($sql);
        } else {
            /// INSERT INTO beneficios (...) VALUES (...);
            $sql = "INSERT INTO beneficios (codigo, precioneto, preciocoste, beneficio)
                VALUES (" . $this->var2str($this->codigo) . "," . $this->var2str($this->precioneto) . "," . $this->var2str($this->preciocoste) . "," . $this->var2str($this->total_beneficio) . ");";
            if ($this->db->exec($sql)) {
                $this->codigo = $this->db->lastval();
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @return mixed
     */
    public function delete()
    {
        return $this->db->exec('DELETE FROM beneficios WHERE codigo = ' . $this->var2str($this->codigo) . ';');
    }

    /**
     * recoge el ultimo codigo insertado en la tabla especificada (retrasamos un segundo para darle tiempo al insert)
     *
     * @param $tablax
     *
     * @return string
     */
    public function lastcod($tablax)
    {
        sleep(1);

        if ($tablax == 'albaran') {
            $tabla = $tablax . 'escli';
        } else {
            $tabla = $tablax . 'scli';
        }

        $lastcodigo = "";
        $sql = "SELECT codigo, id" . $tablax . " FROM " . $tabla . " ORDER BY id" . $tablax . " DESC LIMIT 1 ;";
        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $lastcodigo = $d["codigo"];
            }
        }
        return $lastcodigo;
    }

    /**
     * Recoge los datos de los documentos solicitados existentes en la bdd beneficios
     */
    public function collect($array_documentos)
    {

        $lista = [];
        $sql = "SELECT * FROM beneficios WHERE codigo IN ('" . join("','", $array_documentos) . "')";

        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $lista[] = $d;
            }
        }

        return $lista;
    }

    //recoge todos los codigos pasados en el array existentes en la bdd beneficios
    /* public function getcodigo($array_documentos){
      $lista=array();
      $sql = "SELECT codigo FROM beneficios WHERE codigo IN ('" . join("','", $array_documentos) . "')";

      $data=$this->db->select($sql);
      if ($data)
      {
      foreach($data as $d){
      $lista[]=new beneficio($d);
      }
      }
      return $lista;
      }

      //recoge todos los netos de los códigos pasados en el array
      public function getneto($array_documentos){
      $resultado=0;
      $sql = "SELECT precioneto FROM beneficios WHERE codigo IN ('" . join("','", $array_documentos) . "')";

      $data=$this->db->select($sql);
      if ($data)
      {
      foreach($data as $d){
      $resultado=$resultado+$d;
      }
      }
      return $resultado;
      }

      //recoge todos los costes de los códigos pasados en el array
      public function getcoste($array_documentos){
      $resultado=0;
      $sql = "SELECT preciocoste FROM beneficios WHERE codigo IN ('" . join("','", $array_documentos) . "')";

      $data=$this->db->select($sql);
      if ($data)
      {
      foreach($data as $d){
      $resultado=$resultado+$d;
      }
      }
      return $resultado;
      }

      //recoge todos los beneficios de los códigos pasados en el array
      public function getbeneficio($array_documentos){
      $resultado=0;
      $sql = "SELECT beneficio FROM beneficios WHERE codigo IN ('" . join("','", $array_documentos) . "')";

      $data=$this->db->select($sql);
      if ($data)
      {
      foreach($data as $d){
      $resultado=$resultado+$d;
      }
      }
      return $resultado;
      }

      public function all()
      {
      $lista=array();

      $data=$this->db->select('SELECT * FROM beneficios ;');
      if ($data)
      {
      foreach($data as $d){
      $lista[]=new beneficio($d);
      }
      }

      return $lista;
      } */
}
