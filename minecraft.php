<?php
error_reporting(E_ALL);

require_once 'mcpi/vec3.php';
require_once 'mcpi/block.php';
require_once 'mcpi/connection.php';
require_once 'mcpi/event.php';

function intFloor($data){
    $dataToSend=array();
    if (is_array($data)){
        foreach ($data as $el){
            if (is_object($el) && is_a($el, 'Block')){
                $dataToSend = array_merge($dataToSend, $el->to_array());
            }else{
                 $dataToSend[] = $el;
            }
        }
    }else{
        if (is_object($data) && is_a($data, 'Block')){
            $dataToSend = array_merge($dataToSend, $el->to_array());
        }else{
             $dataToSend[] = $el;
        }
    }
    $dataToSend = array_map('floor', $dataToSend);
    return $dataToSend;
}

function getNumFromStatusBool($boolean){
    if ($boolean === 1 || strtolower($boolean) === 'true' || $boolean === true){
        return 1;
    }else{
        return 0;
    }
}

class CmdPositioner{

    private $conn, $pkg;
    
    function __construct($conn, $pkg='Unknown'){
        $this->conn = $conn;
        $this->pkg  = $pkg;
    }

    //Methods for setting and getting positions
    function getPos(){
        $args = func_get_args();
        $id = array_shift($args);
        //Get entity position (entityId:int) => Vec3
        $s = $this->conn->send_and_receive($this->pkg. ".getPos", $id);
        return new Vec3(array_map('floatval', explode(",",$s)));
    }

    function setPos(){
        $args = func_get_args();
        $id = array_shift($args);
        $data = flatten($args);
        //Set entity position (entityId:int, x,y,z)
        $this->conn->send($this->pkg.".setPos", $id, $data);
    }

    function getTilePos(){
        $args = func_get_args();
        $id = array_shift($args);
        $response = $this->conn->send_and_receive($this->pkg.".getTile", $id);
        $coords =  explode(",", $response);
        $intversion = array_map("intval", $coords);
        $result = new Vec3($intversion);
        return $result;
    }

    function setTilePos(){
        $args = func_get_args();
        $id = array_shift($args);
        $data = flatten($args);
        //Set entity tile position (entityId:int) => Vec3
        $this->conn->send($this->pkg.".setTile", $id, intFloor($data));
    }

    function setting($setting, $status){
        //Set a player setting (setting, status). keys: autojump
        $status = getNumFromStatusBool($status);
        $this->conn->send($this->pkg. ".setting", $setting, $status);  
    }
}


class CmdEntity extends CmdPositioner{

    //Methods for entities
    function __construct($conn){
        parent::__construct($conn, "entity");
    }
} 

class CmdPlayer extends CmdPositioner{


   //Methods for the host (Raspberry Pi) player
    function __construct($conn){
        parent::__construct($conn, "player");

    }
    
    function getPos(){
        return parent::getPos(array());
    }
    function setPos(){
        $data = func_get_args();
        return parent::setPos(array(), $data);
    }
    function getTilePos(){

        return parent::getTilePos(array());
    }

    function setTilePos(){
        $data = func_get_args();
        return parent::setTilePos(array(), $data);
    }
}

class CmdCamera{

    private $conn;
    
    function __construct($conn){
        $this->conn = $conn;
    }

    function setNormal(){
        $data = func_get_args();
        // Set camera mode to normal Minecraft view ([entityId])
        $this->conn->send("camera.mode.setNormal", $data);
    }

    function setFixed(){
        //  Set camera mode to fixed view
        $this->conn->send("camera.mode.setFixed");
    }

    function setFollow(){
        $data = func_get_args();
        // Set camera mode to follow an entity ([entityId])
        $this->conn->send("camera.mode.setFollow", $data);
    }

    function setPos(){
        $data = func_get_args();
        // Set camera entity position (x,y,z)
        $this->conn->send("camera.setPos", $data);
    }
}
class CmdEvents{

    private $conn;
    function __construct($conn){
        $this->conn = $conn;
    }

    function clearAll(){
        // Clear all old events
        $this->conn->send("events.clear");
    }

    function pollBlockHits(){
        // Only triggered by sword => [BlockEvent]
        $s = $this->conn->send_and_receive("events.block.hits");
        if ($s !== ""){
            $events = explode("|", $s);
            $hitsArray = array();
            foreach($events as $e){
                $e = array_map('intval', explode(",", $e));
                array_push($hitsArray, Blockevent::Hit($e));
            }
            return $hitsArray;
        }
        return array();
    }
}

class Minecraft{
    //The main class to interact with a running instance of Minecraft Pi.
    
    
    private $conn, $camera, $entity, $player, $events;
    
    function __construct($conn){
     
        $this->conn = $conn;
        $this->camera = new CmdCamera($conn);
        $this->entity = new CmdEntity($conn);
        $this->player = new CmdPlayer($conn);
        $this->events = new CmdEvents($conn);
    }

    public function conn(){
        return $this->conn;
    }
    public function camera(){
        return $this->camera;
    }
    public function entity(){
        return $this->entity;
    }
    public function player(){
        return $this->player;
    }
    public function events(){
        return $this->events;
    }

    function getBlock(){
        $data = func_get_args();
        //Get block (x,y,z) => id:int
        return intval($this->conn->send_and_receive("world.getBlock", intFloor($data)));
    }

    function getBlockWithData(){
        $data = func_get_args();
    // Get block with data (x,y,z) => Block
        $ans = $this->conn->send_and_receive("world.getBlockWithData", intFloor($data));
        return new Block(array_map("intval", explode(",", $ans)));
    }

//    
//        TODO
//    
    function getBlocks(){
        $data = func_get_args();
    // Get a cuboid of blocks (x0,y0,z0,x1,y1,z1) => [id:int]
        return intval($this->conn->send_and_receive("world.getBlocks", intFloor($data)));
    }

    function setBlock(){
        $data = flatten(func_get_args());
        //  Set block (x,y,z,id,[data])
        $this->conn->send("world.setBlock", intFloor($data));
    }
    
    function setBlocks(){
        $data = flatten(func_get_args());
        //  Set a cuboid of blocks (x0,y0,z0,x1,y1,z1,id,[data])
        $this->conn->send("world.setBlocks", intFloor($data));
    }

    function getHeight(){
        $data = func_get_args();
        //  Get the height of the world (x,z) => int
        return intval($this->conn->send_and_receive("world.getHeight", intFloor($data)));
    }

    function getPlayerEntityIds(){
            //   Get the entity ids of the connected players => [id:int]
        $ids = $this->conn->send_and_receive("world.getPlayerIds");
        $idArray = explode("|", $ids);
        return $idArray;
    }

    function saveCheckpoint(){
        // Save a checkpoint that can be used for restoring the world
        $this->conn->send("world.checkpoint.save");
    }

    function restoreCheckpoint(){
        // Restore the world state to the checkpoint
        $this->conn->send("world.checkpoint.restore");
    }

    function postToChat($msg){
        //Post a message to the game chat
        $this->conn->send("chat.post", $msg);
    }

    function setting($setting, $status){
            // Set a world setting (setting, status). keys: world_immutable, nametags_visible
            $status = getNumFromStatusBool($status);
            $this->conn->send("world.setting", $setting, $status);
    }

    static function create(){
        $address = gethostbyname('localhost');
        $port = 4711;
        return new Minecraft(new Connection($address, $port));
    }

}

?>
