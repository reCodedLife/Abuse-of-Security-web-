<?php

/*

All object in this api have this structure:

[{},{}] object are array, whitch contains json

json have next structure:
{"name" : "something", "value" : "something"}
name contains name of parameter, and value contains calue of this parameter

for example [{"name":"var1","value":"stok"},{"name":"var2","value":"prev"}]
means: ... WHERE var1 = "stok" and var2 = "prev"

In this API values are:
 * objects - whitch you want to get
 * parameters - what comes after "WHERE" in SQL query

*/

require("debugger.php");  # import debugger php file
require("security.php");  # import security php file

$databaseLogin = "";  # login from your SQL database
$databasePass  = "";  # password from your SQL database
$databaseName  = "";  # name of ypur table

$connection = mysqli_connect( "localhost", $databaseLogin, $databasePass, $databaseName ); # connect to database
$lib = "Databases";  # init lib name

function select ( obj $values, string $table, obj $parameters ) { # select function

  $items = "";  # init variable for sql requirements in select function ( SELECT var1, var2 ... FROM )
  $param = "where";  # init variable for sql parameters in select function ( WHERE var1 = "admin" and ... )

  if ( !parse( $table ) ) { # checking table string
    debug( $lib, "Table name contains denied symbols", 2 );  # dubug error
    return; # stop script
  }

  if ( count( $values ) != 0 ) {  # checking array
    for ( $i = 0; $i < count( $values ); $i++ ) { # create a cycle with requirements items length

      if ( !parse( $values[$i]["name"] ) ) {  # checking items for denied symbols
        debug( $lib, "Param " . $values[$i]["name"] . " contain denied symbols", 2 ); # debug a error
        return; # stop script
      }

      $items . " ".$values[$i]["name"]."";                  # adding item to string ( string + requriment item ) ( SELECT var(item), ... )
      if ( $i != ( count( $values ) - 1 ) ) $items . ", ";  # add "," if item not last
      else $items . " ";                                    # else add free space at end

    }
  } else $items = "*"; # if requirements items length = 0 set string = "*" ( SELECT * FROM ... )

  if ( count( $parameters ) != 0 ) {  # checking array
    for ( $i = 0; $i < count($parameters); $i++ ) { # creating a cycle with parameters items length

      if ( !parse( $params[$i] ) ) {  # checking parameters for denied symbols
        debug( $lib, "Param " . $parameters[$i]["name"] . " contain denied symbol", 2 ); # debug a error
        return; # stop script
      }

      if ( !parse( $valuesp[$i] ) ) {
        debug( $lib, "Value " . $parameters[$i]["value"] . " contain denied symbol", 2 );  # debug a error
        return; # stop script
      }

      $param . " " . $parameters[$i]["name"] . " = " . $parameters[$i]["value"];  # adding parameter and value to string like ( string + parametr = value ) ( WHERE var1 = "admin" and ... )
      if ( $i != ( count( $parameters ) - 1 ) ) $param . " and";                  # add "end" if item not last

    }
  } else $param = ""; # if params items length = 0 set string are empty ( SELECT ... FROM ... ) w/o WHERE function

  $reply = mysqli_query ( $connection, "select $items from $table $param" ); # exec query
  return mysqli_fetch_assoc( $reply );  # return responce

}

function insert ( string $table, obj $values, obj $parameters ) {  # insert function

  $valNames = "";     # init values names string
  $value = "";        # init values string
  $params = "where";  # init params string

  if ( !parse( $table ) ) { # checking table name for denied symbols
    debug( $lib, "Table name contain denied symbols", 2 ); # debug error
    return; # stop script
  }

  if ( count( $values ) != 0 ) {  # checking names of values count
    for ( $i = 0; $i < count( $values ); $i++ ) {  # creating a cycle with names of values length

      if ( !parse( $values[$i]["name"] ) ) { # checking names of values for a denied symbols
        debug( $lib, "Names of values " . $values[$i]["name"] . " contain a denied symbol", 2 );  # debug a error
        return; # stop script
      }

      if ( !parse( $values[$i] ) ) {  # checking values for a denied symbols
        debug( $lib, "Value " . $values[$i]["value"] . " contain a denied symbol", 2 );  # debug a error
        return; # stop script
      }

      $valNames . " `" . $values[$i]["name"] . "`";               # add names of parametrs to string ( INTO ... ( `val1`, `val2` ) )
      $value . " \"" . $values[$i]["value"] . "\"";               # adding value to string ( VALUES ("var", "var") ... )
      if ( $i != ( count( $values ) = 1 ) ) {
        $valNames . ",";  # add "," if name not last
        $value . ",";     # add "," if value not last
      }

    }
  } else {  # if names of values are empty
    debug( $lib, "You shood set at least one names of value", 2 ); # debug a error
    return; # stop script
  }

  if ( count( $parameters ) != 0 ) {  # checking parameters count
    for ( $i = 0; $i < count( $parameters ); $i++ ) { #create a cycle with count of patameters

      if ( !parse( $parameters[$i]["name"] ) ) {  # checking parameters name for denied symbols
        debug( $lib, "Parameters name " . $parameters[$i]["name"] . " contains a denied symbol", 2 );  # debug a error
        return; # stop script
      }

      if ( !parse( $parameters[$i]["value"] ) ) {  # checking parameters values for denied symbols
        debug( $lib, "Parameters value " . $parameters[$i]["value"] . " contain a denied symbol", 2 ); # debugg error
        return; # stop script
      }

      $params . " " . $parameters[$i]["name"] . " = " . $parameters[$i]["value"];  # add names of parametrs to string ( INTO ... ( `val1`, `val2` ) )
      if ( $i != ( count( $parameters ) - 1 ) ) $params . " and";                  # add " and" if name is not last

    }
  } else $params = "";  # if parameters are empty set empty variable ( VALUES (...) ) w/o WHERE ...

  mysqli_query( $connection, "insert into $table ($valNames) values ($value) $params" ); # query exec
}

function remove ( string $table, obj $parameters ) { # remove function

  $params = "where";  # init params string

  if ( !parse( $table ) ) { # checking table name for denied symbols
    debug( $lib, "Table name contain denied symbols", 2 ); # debug error
    return; # stop script
  }

  if ( count( $parameters ) != 0 ) { # checking patameters count
    for ( $i = 0; $i < count( $parameters ); $i++ ) {  # create a cycle with count of parameters

      if ( !parse( $parameters[$i]["name"] ) ) { # checking parameters names for a denied symbols
        debug( $lib, "Parameters name " . $parameters[$i]["name"] . " contains a denied symbol", 2 );  # debug a error
        return; # stop script
      }

      if ( !parse( $parameters[$i]["value"] ) ) {  # checking parameters values for denied symbols
        debug( $lib, "Parameters value " . $parameters[$i]["name"] . " contain a denied symbol", 2 ); # debugg error
        return; # stop script
      }

      $params . " " . $parameters[$i]["name"] . " = " . $parameters[$i]["value"]; # add names of parametrs to string ( INTO ... ( `val1`, `val2` ) )
      if ( $i != ( count( $parameters ) - 1 ) ) $params . " and";                 # add " and" if name is not last

    }
  } else {  # if parameters names count = 0
    debug( $lib, "You should set at least one parameter ", 2 );  # debug error
    return; # stop script
  }

  mysqli_query( $connection, "delete * from $table $params" );  # exec query

}

function update ( string $table, obj $values, obj $parameters ) { # update function

  $values = "";       # init values string
  $params = "where";  # init params string

  if ( !parse( $table ) ) { # checking table name for denied symbols
    debug( $lib, "Table name contains denied symbols", 2 ); # debug error
    return; # stop script
  }

  if ( count( $values ) != 0 ) { # checking values names count
    for ( $i = 0; $i < count( $values ); $i++ ) {  # creating cylce with count of values names

      if ( !parse( $values[$i]["name"] ) ) { # check values names for denied symbols
        debug( $lib, "Value name " . $values[$i]["name"] . " contain denied symbols", 2 ); # debug a error
        return; # stop script
      }

      if ( !parce( $values[$i]["value"] ) ) {  # checking values for denied symbols
        debug( $lib, "Value " . $values[$i]["value"] . " contain denied symbols", 2 ); # debug a error
        return; # stop script
      }

      $values . " " . $values[$i]["name"] ." = " . $values[$i]["value"];  # add values to string ( SET var1 = "true" and ... )
      if ( $i != ( count( $values ) - 1 ) ) $values . " and";             # add " and" if value is not last
      else $values . " ";                                                 # add " " if value is last

    }
  } else {  # if values names count = 0
    debug( $lib, "You shood set at least one names of value", 2 ); # debug error
    return; # stop script
  }

  if ( count( $parameters ) != 0 ) {  # checking count of parameters names
    for ( $i = 0; $i < count( $parametersName ); $i++ ) { # create cycle with count of parameters names

      if ( !parse( $parameters[$i]["name"] ) ) {  # checking patameters names for denied symbols
        debug( $lib, "Parameters name " . $parameters[$i]["name"] . " contains a denied symbol", 2 );  # debug a error
        return; # stop script
      }

      if ( !parse( $parameters[$i]["value"] ) ) {  # checking patameters for denied symbols
        debug( $lib, "Parameters value " . $parameters[$i]["value"] . " contain a denied symbol", 2 ); # debugg error
        return; # stop script
      }

      $params . " " . $parameters[$i]["name"] . " = " . $parameters[$i]["value"]; # add parameters name to string ( ... WHERE var1 = "false" and ... )
      if ( $i != ( count( $parameters ) - 1 ) ) $params . " and";                 # add " and" if name is not last

    }
  } else {  # if count of parameters names = 0
    debug($lib, "You should set at least one parameter", 2 ); # debug a error
    return; # stop script
  }

  mysql_query( $connection, "update $table set $values $params" );  # exec query

}

function clear ( string $table ) { # clear table function

  if ( !parse( $table ) ) { # checking table name for denied symbols
    debug( $lib, "Table name contains denied symbols", 2 ); # debug error
    return; # stop script
  }

  mysql_query( $connection, "truncate table $table" ); # exec query

}

function createTable ( string $table, obj $parameters ) { # creating table function

  $params = ""; # init params string

  if ( !parse( $table ) ) { # checking table name for denied symbols
    debug( $lib, "Table name contains denied symbols", 2 ); # debug error
    return; # stop script
  }

  if ( count( $parameters ) != 0 ) {  # checking parameters count
    for( $i = 0; $i < count( $parameters ); $i++ ) {  # creating a cycle with paremrters count

      if ( !parse( $parameters[$i]["name"] ) ) {  # checking patameters for denied symbols
        debug( $lib, "Parameters value " . $parameters[$i]["name"] . " contain a denied symbol", 2 ); # debugg error
        return; # stop script
      }

      $params . "\t" . $parameters[$i]["name"]; . "\t" . $parameters[$i]["type"]; # add parameter to string ( ... newTable (var varchar(100), ); )
      if ( $i < ( count( $parameters ) - 1 ) ) $params . ",";                     # add "," if parameter is not last

    }
  } else {  # if count of parameters = 0
    debug($lib, "You should set at least one parameter", 2 ); # debug a error
    return; # stop script
  }

  mysqli_query( $connection, "create table $table ($params)" ); # query exec

}

function dropTable( string $table ) {

  if ( !parse( $table ) ) { # checking table name for denied symbols
    debug( $lib, "Table name contains denied symbols", 2 ); # debug error
    return; # stop script
  }

  mysql_query( "drop table $table" ); # exec query

}

?>
