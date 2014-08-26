<?php
error_reporting(E_ALL);

class Block{

    private $id, $data;


    function __construct() {
        $args = func_get_args();
        $numOfArgs = func_num_args();
    
        if(method_exists($this, $func='__construct'.$numOfArgs)){
            call_user_func_array(array($this, $func) ,$args);
        }else{
           $this->__construct2(0,0);
        }
    }

    function __construct1($input) {
        if (is_array($input)){
        $numOfElements = count($input);
            if ($numOfElements >= 2){
                $this->__construct2($input[0], $input[1]);
            }else if($numOfElements == 1){
                $this->__construct2($input[0],0);
            }else{
                $this->__construct2(0,0);
            }
        }else{
            $this->__construct2($input,0);
        }
    }

    function __construct2($id, $data) {
        $this->id = $id;
        $this->data = $data;
    }


    function __toString(){
        return "Block: = [".$this->id.", ".$this->data."]\n";
    }
    
    function to_array(){
        return array($this->id, $this->data);
    }
    //Accessors
    function id() {
        return $this->id;
    }

    function data() {
        return $this->data;
    }

    function withData($data){
        return new Block($this->id, $data);
    }

    function compare(){
        $dId = $this->id - $rhs->id;
        if ($dId != 0) return $dId;
        $dData = $this->data - $rhs->data;
        if ($dData != 0) return $dData;
        return 0;
    }



    public static function AIR()                 { return new Block(0);}
    public static function STONE()               { return new Block(1);}
    public static function GRASS()               { return new Block(2);}
    public static function DIRT()                { return new Block(3);}
    public static function COBBLESTONE()         { return new Block(4);}
    public static function WOOD_PLANKS()         { return new Block(5);}
    public static function SAPLING()             { return new Block(6);}
    public static function BEDROCK()             { return new Block(7);}
    public static function WATER_FLOWING()       { return new Block(8);}
    public static function WATER()               { return Block::WATER_FLOWING();}
    public static function WATER_STATIONARY()    { return new Block(9);}
    public static function LAVA_FLOWING()        { return new Block(10);}
    public static function LAVA()                { return Block::LAVA_FLOWING();}
    public static function LAVA_STATIONARY()     { return new Block(11);}
    public static function SAND()                { return new Block(12);}
    public static function GRAVEL()              { return new Block(13);}
    public static function GOLD_ORE()            { return new Block(14);}
    public static function IRON_ORE()            { return new Block(15);}
    public static function COAL_ORE()            { return new Block(16);}
    public static function WOOD()                { return new Block(17);}
    public static function LEAVES()              { return new Block(18);}
    public static function GLASS()               { return new Block(20);}
    public static function LAPIS_LAZULI_ORE()    { return new Block(21);}
    public static function LAPIS_LAZULI_BLOCK()  { return new Block(22);}
    public static function SANDSTONE()           { return new Block(24);}
    public static function BED()                 { return new Block(26);}
    public static function COBWEB()              { return new Block(30);}
    public static function GRASS_TALL()          { return new Block(31);}
    public static function WOOL()                { return new Block(35);}
    public static function FLOWER_YELLOW()       { return new Block(37);}
    public static function FLOWER_CYAN()         { return new Block(38);}
    public static function MUSHROOM_BROWN()      { return new Block(39);}
    public static function MUSHROOM_RED()        { return new Block(40);}
    public static function GOLD_BLOCK()          { return new Block(41);}
    public static function IRON_BLOCK()          { return new Block(42);}
    public static function STONE_SLAB_DOUBLE()   { return new Block(43);}
    public static function STONE_SLAB()          { return new Block(44);}
    public static function BRICK_BLOCK()         { return new Block(45);}
    public static function TNT()                 { return new Block(46);}
    public static function BOOKSHELF()           { return new Block(47);}
    public static function MOSS_STONE()          { return new Block(48);}
    public static function OBSIDIAN()            { return new Block(49);}
    public static function TORCH()               { return new Block(50);}
    public static function FIRE()                { return new Block(51);}
    public static function STAIRS_WOOD()         { return new Block(53);}
    public static function CHEST()               { return new Block(54);}
    public static function DIAMOND_ORE()         { return new Block(56);}
    public static function DIAMOND_BLOCK()       { return new Block(57);}
    public static function CRAFTING_TABLE()      { return new Block(58);}
    public static function FARMLAND()            { return new Block(60);}
    public static function FURNACE_INACTIVE()    { return new Block(61);}
    public static function FURNACE_ACTIVE()      { return new Block(62);}
    public static function DOOR_WOOD()           { return new Block(64);}
    public static function LADDER()              { return new Block(65);}
    public static function STAIRS_COBBLESTONE()  { return new Block(67);}
    public static function DOOR_IRON()           { return new Block(71);}
    public static function REDSTONE_ORE()        { return new Block(73);}
    public static function SNOW()                { return new Block(78);}
    public static function ICE()                 { return new Block(79);}
    public static function SNOW_BLOCK()          { return new Block(80);}
    public static function CACTUS()              { return new Block(81);}
    public static function CLAY()                { return new Block(82);}
    public static function SUGAR_CANE()          { return new Block(83);}
    public static function FENCE()               { return new Block(85);}
    public static function GLOWSTONE_BLOCK()     { return new Block(89);}
    public static function BEDROCK_INVISIBLE()   { return new Block(95);}
    public static function STONE_BRICK()         { return new Block(98);}
    public static function GLASS_PANE()          { return new Block(102);}
    public static function MELON()               { return new Block(103);}
    public static function FENCE_GATE()          { return new Block(107);}
    public static function GLOWING_OBSIDIAN()    { return new Block(246);}
    public static function NETHER_REACTOR_CORE() { return new Block(247);}

}
?>
