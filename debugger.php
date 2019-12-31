<?php

class Debugger {

  public function debug( string $from, string $message, int $type = 0 ) {  # main funcion

    $message = $message . "<br/>";

    switch ( $type ) {  # get type
      case 0: # plain message type
        echo "{Message}[".$from."] " . $message;  # display data
        break;
      case 1: # warning
        echo "{Warning}[".$from."] " . $message;  # display data
        break;
      case 2: # error
        die(  "{Critical}[".$from."] " . $message ); # display data
        exit(0);
        break;
      default:  # plain text
        echo "{Message}[".$from."] " . $message;  # display data
        break;
    }

  }

}

?>
