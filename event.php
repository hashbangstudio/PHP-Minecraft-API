<?php
error_reporting(E_ALL);

class BlockEvent {

    const HIT = 0;
    //An Event related to blocks (e.g. placed, removed, hit)
    function __construct($type, $x, $y, $z, $face, $entityId){
        
            $this->type = $type;
            $this->posn = new Vec3($x, $y, $z);
            $this->face = $face;
            $this->entityId = $entityId;

    }


    function __toString(){
    
        $typeText = $this->type === self::HIT ? "BlockEvent.HIT" : "???";
        $output = sprintf("BlockEvent(%s, %d, %d, %d, %d, %d)",  
                         $typeText, 
                         $this->posn->x(), $this->posn->y(), $this->posn->z(), 
                         $this->face, $this->entityId
                         );
        return $output;
    }
    
    public static function Hit(){
        $args = func_get_args();
        $numOfArgs =func_num_args();
        //var_dump($args);
        if($numOfArgs === 1 && is_array($args[0]) ){
           $args = $args[0];
        }
        list($x, $y, $z, $face, $entityId) = $args;
        return new BlockEvent(self::HIT, $x, $y, $z, $face, $entityId);
    }

}

?>
