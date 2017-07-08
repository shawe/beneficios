<?php

/**
 * Clase beneficio
 */
class beneficio extends fs_model
{
    /**
     * Código del documento: clave primaria, character varying (20)
     * @var null
     */
    public $codigo;
    /**
     * Total neto del documento
     * @var float
     */
    public $precioneto;
    /**
     * Total coste del documento
     * @var float
     */
    public $preciocoste;
    /**
     * Total beneficio del documento
     * @var float
     */
    public $beneficio;

    /**
     * beneficio constructor.
     *
     * @param bool|beneficio $d
     */
    public function __construct($d = false)
    {
        parent::__construct('beneficios');
        if ($d) {
            $this->codigo = $d['codigo'];
            $this->precioneto = (float)$d['precioneto'];
            $this->preciocoste = (float)$d['preciocoste'];
            $this->beneficio = (float)$d['beneficio'];
        } else {
            /// valores predeterminados
            $this->codigo = null;
            $this->precioneto = 0;
            $this->preciocoste = 0;
            $this->beneficio = 0;
        }
    }

    /**
     * TODO
     *
     * @return string
     */
    public function install()
    {
        return '';
    }

    /**
     * TODO
     *
     * @return bool
     */
    public function exists()
    {
        if ($this->codigo === null) {
            return false;
        }

        $sql = 'SELECT * FROM beneficios WHERE codigo = ' . $this->var2str($this->codigo) . ';';
        return $this->db->select($sql);
    }

    /**
     * TODO
     *
     * @return bool
     */
    public function save()
    {
        if ($this->exists()) {
            $sql = 'UPDATE beneficios SET precioneto = ' . $this->var2str($this->precioneto)
                . ', preciocoste = ' . $this->var2str($this->preciocoste)
                . ', beneficio = ' . $this->var2str($this->beneficio)
                . ' WHERE codigo = ' . $this->var2str($this->codigo) . ';';
            return $this->db->exec($sql);
        }

        /// INSERT INTO beneficios (...) VALUES (...);
        $sql = 'INSERT INTO beneficios (codigo, precioneto, preciocoste, beneficio) VALUES ('
            . $this->var2str($this->codigo)
            . ', ' . $this->var2str($this->precioneto)
            . ', ' . $this->var2str($this->preciocoste)
            . ', ' . $this->var2str($this->beneficio)
            . ');';
        if ($this->db->exec($sql)) {
            $this->codigo = $this->db->lastval();
            return true;
        }

        return false;
    }

    /**
     * TODO
     *
     * @return mixed
     */
    public function delete()
    {
        $sql = 'DELETE FROM beneficios WHERE codigo = ' . $this->var2str($this->codigo) . ';';
        return $this->db->exec($sql);
    }

    /**
     * Recoge el ultimo codigo insertado en la tabla especificada (retrasamos un segundo para darle tiempo al insert)
     *
     * @param $tablax
     *
     * @return string
     */
    public function lastcod($tablax)
    {
        sleep(1);

        if ($tablax === 'albaran') {
            $tabla = $tablax . 'escli';
        } else {
            $tabla = $tablax . 'scli';
        }

        $lastcodigo = '';
        $sql = 'SELECT codigo, id' . $tablax . ' FROM ' . $tabla . ' ORDER BY id' . $tablax . ' DESC LIMIT 1 ;';
        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $lastcodigo = $d['codigo'];
            }
        }
        return $lastcodigo;
    }

    /**
     * Recoge todos los codigos pasados en el array existentes en la bdd beneficios
     *
     * @param $array_documentos
     *
     * @return array
     */
    public function getByCodigo($array_documentos)
    {
        $lista = [];
        $sql = "SELECT * FROM beneficios WHERE codigo IN ('" . implode("','", $array_documentos) . "')";

        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $lista[] = new beneficio($d);
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
