<?php

class beneficio extends fs_model
{

    /// clave primaria. varchar(20)
    public $codigo;

    public $precioneto;
    public $preciocoste;
    public $total_beneficio;

    public function __construct($d=FALSE)
    {
        parent::__construct('beneficios');
        if($d)
        {
            $this->codigo = $d['codigo'];
            $this->precioneto = floatval($d['precioneto']);
            $this->preciocoste = floatval($d['preciocoste']);
            $this->total_beneficio = floatval($d['beneficio']);
        }
        else
        {
            /// valores predeterminados
            $this->codigo = NULL;
            $this->precioneto = 0;
            $this->preciocoste = 0;
            $this->total_beneficio = 0;
        }
    }

    public function install()
    {
        return '';
    }

    public function exists()
    {
        if( is_null($this->codigo) )
        {
            return FALSE;
        }
        else
        {
            return $this->db->select('SELECT * FROM beneficios WHERE codigo = '.$this->var2str($this->codigo).';');
        }
    }

    public function save()
    {
        if( $this->exists() )
        {
            /// UPDATE beneficios SET ... WHERE ...;
            $sql=" UPDATE beneficios SET precioneto = ".$this->var2str($this->precioneto).", preciocoste = ".$this->var2str($this->preciocoste).", beneficio = ".$this->var2str($this->total_beneficio)."
                WHERE codigo = ".$this->var2str($this->codigo).";";
            return $this->db->exec($sql);
        }
        else
        {
            /// INSERT INTO beneficios (...) VALUES (...);
            $sql = "INSERT INTO beneficios (codigo, precioneto, preciocoste, beneficio)
                VALUES (".$this->var2str($this->codigo).",".$this->var2str($this->precioneto).",".$this->var2str($this->preciocoste).",".$this->var2str($this->total_beneficio).");";
            if( $this->db->exec($sql) )
            {
                $this->codigo = $this->db->lastval();
                return TRUE;
            }
            else
                return FALSE;
        }
    }

    public function delete()
    {
        return $this->db->exec('DELETE FROM beneficios WHERE codigo = '.$this->var2str($this->codigo).';');
    }

    //recoge el ultimo codigo insertado en la tabla especificada (retrasamos un segundo para darle tiempo al insert)
    public function lastcod($tablax){
        sleep(1);

        if ($tablax=='albaran'){
            $tabla=$tablax.'escli';
        }
        else{
            $tabla=$tablax.'scli';
        }

        $lastcodigo="";
        $sql="SELECT codigo, id".$tablax." FROM ".$tabla." ORDER BY id".$tablax." DESC LIMIT 1 ;";
        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $lastcodigo = $d["codigo"];
            }
        }
        return $lastcodigo;
    }

    /*public function all()
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
    }*/
}
