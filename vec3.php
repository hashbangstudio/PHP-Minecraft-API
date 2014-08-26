<?php

error_reporting(E_ALL);

class Vec3{
     private $x,$y,$z;



    function __construct() {
        $args = func_get_args();
        $numOfArgs = func_num_args();
    
        if(method_exists($this, $func='__construct'.$numOfArgs)){
            call_user_func_array(array($this, $func) ,$args);
        }else{
            $this->__construct3(0,0,0);
        }
    }
    
   
    function __construct1($coords){
        
        if (is_array($coords)){
            $this->__construct3($coords[0], $coords[1], $coords[2]);
        }
    }
    
    function __construct3($x, $y, $z){
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
       
    }
    
    function __toString(){
        return "Vec3: = [x=".$this->x.", y=".$this->y.", z=".$this->z."]\n";
    }

    function x(){
        return $this->x;
    }

    function y(){
        return $this->y;
    }

    function z(){
        return $this->z;
    }

    function length(){
    
        return pow($this->lengthSqr() , 0.5);
    }

    function lengthSqr(){
        return $this->x * $this->x + $this->y * $this->y  + $this->z * $this->z;
    }

    function add($rhs) {
        $c = clone $this;
        return $c->iadd($rhs);
    }

    function iadd($rhs){
        $this->x = $this->x + $rhs->x;
        $this->y = $this->y + $rhs->y;
        $this->z = $this->z + $rhs->z;
        return $this;
    }

    function mul($rhs){
        $c = clone $this;
        $c->imul($rhs);
        return $c;
    }

    function imul($rhs){
        $this->x *= $rhs;
        $this->y *= $rhs;
        $this->z *= $rhs;
        return $this;
    }

    function negate(){
        return new Vec3(-$this->x, -$this->y, -$this->z);
    }

    function minus($rhs){
        return $this->add(-$rhs);
    }
    
    function iminus($rhs){
        return $this->iadd(-$rhs);
    }

    function compare($rhs){
        $dx = $this->x - $rhs->x;
        if ($dx != 0){return $dx;}
        $dy = $this->y - $rhs->y;
        if ($dy != 0) {return $dy;}
        $dz = $this->z - $rhs->z;
        if ($dz != 0){return $dz;}
        return 0;
    }

}
?>
