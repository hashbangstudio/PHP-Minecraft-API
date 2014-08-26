<?php
error_reporting(E_ALL);


//TODO when php 5.5 more common change to use yield/generator based version
    function flatten($data){
        $flatArray = array();
        if (is_array($data)){
            foreach($data as $el){
               // echo "el is $el\n";
                $flatArray = array_merge($flatArray, flatten($el));
            }
        }else{
            $flatArray[] = $data;
        }
        
        return $flatArray;
  }
  
  function flattenToString($data){
  
       if(is_array($data)){
           #flatten all elements
           $test = flatten($data);
           return implode(",", $test);
       }else{
           return $data;
       }
  }
   

class Connection {

    private $sock,$lastSent="";

    function __destruct(){
        $this->close();
    }
    
    function __construct($address, $port, $protocol=SOL_TCP){
        $createResult = ($sock = socket_create(AF_INET, SOCK_STREAM, $protocol));
        if ( $createResult === false){
            echo "socket_create failed with error:  ".socket_strerror(socket_last_error())."\n";
            exit();
        }
        $result = socket_connect($sock, $address , $port );
        if ($result === false){
            echo "socket_connect failed with error:  ".socket_strerror(socket_last_error())."\n";
            exit();
        }
        $this->sock = $sock;
        $this->lastSent = "";
    }

    function drain(){
        //echo "In Drain \n";
        $response = "";
        $w=$e=NULL;
        $r = array($this->sock);
        while(true){
            //echo "before select\n";
            $n = socket_select($r, $w, $e, 0);
            //echo "n is $n\n";
            if ($n === false){
                echo "socket_select failed with error:  ".socket_strerror(socket_last_error())."\n";
                break;
            }else if ($n > 0){
                $response = socket_read($this->sock, 1500);
                //echo "response is |$response|";
                if ($response === false){
                    echo "socket_select failed with error:  ".socket_strerror(socket_last_error())."\n";
                    break;
                }
                $err =  "Drained Data: $response\n";
                $err .= "Last Message: ".$this->lastSent."\n";
                fwrite(STDERR, $err);
            }else{
                #n==0 (none to read) and all other condition
                break;
            }
        }
    }

    function send(){
        $args = func_get_args();
        $stem = array_shift($args);
        $data = array();
        foreach ($args as $el){
            if (is_array($el)){
               array_merge($data, $el);
            }else{
                array_push($data, $el);
            }
        }
        $msg = "";
        //flatten incoming arguments into a string if is an array
        $msg .= flattenToString($args);
        $this->drain();
        $command = "$stem($msg)\n";
        #echo $command."\n";
        $this->lastSent = $command;
        socket_write($this->sock, $command, strlen($command));
    }

    function receive(){
    
        $response = rtrim(socket_read($this->sock, 1500, PHP_NORMAL_READ));
        if ($response === "Fail"){
            fwrite(STDERR,"Failed = $this->lastSent \n");
            throw new Exception("Failed on command : $this->lastSent\n");
        }
        return $response;
    }

    function send_and_receive(){
        $args = func_get_args();
        $f = array_shift($args);
        $this->send($f, $args);
        return $this->receive();
    }

    function close(){
        if($this->sock != NULL){
            socket_close($this->sock);
        }
    }

}
?>
