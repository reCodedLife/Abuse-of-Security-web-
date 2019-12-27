<?php

require("debugger.php");  # import debugger php file
require("security.php");  # import security php file

$databaseLogin = "";  # login from your SQL database
$databasePass  = "";  # password from your SQL database
$databaseName  = "";  # name of ypur table

$connection = mysqli_connect( "localhost", $databaseLogin, $databasePass, $databaseName ); # connect to database
$lib = "Databases";  # init lib name

function select ( $what = array(), $table, $params = array(), $values = array() ) { # select func

  $items = "";  # init variable for sql requirements in select function ( SELECT var1, var2 ... FROM )
  $param = "where";  # init variable for sql parameters in select function ( WHERE var1 = "admin" and ... )

  if ( !parse( $table ) ) { # checking table string
    debug( $lib, "Table name contains denied symbols", 2 );  # dubug error
    return; # stop script
  }

  if ( count( $params ) != count( $values ) ) { # check equaling length params with values
    debug( $lib, "Count of params should be equal to values count", 2 );  # debug error
    return; # stop script
  }

  if ( count( $what ) != 0 ) {  # checking array
    for ( $i = 0; $i < count( $what ); $i++ ) { # create a cycle with requirements items length

      if ( !parse( $what[$i] ) ) { # checking items for denied symbols
        debug( $lib, "Param " . $what[$i] . " contain denied symbols", 2 ); # debug a error
        return; # stop script
      }

      $items = $items . " ".$what[$i]."";  # adding item to string ( string + requriment item ) ( SELECT var(item), ... )
      if ( $i != ( count( $what ) - 1 ) ) $items . ", ";  # add "," if item not last
      else $items = $items . " ";  # else add free space at end

    }
  } else $items = "*"; # if requirements items length = 0 set string = "*" ( SELECT * FROM ... )

  if ( count( $params ) != 0 ) {  # checking array
    for ( $i = 0; $i < count($params); $i++ ) { # creating a cycle with parameters items length

      if ( !parse( $params[$i] ) ) {  # checking parameters for denied symbols
        debug( $lib, "Param " . $params[$i] . " contain denied symbol", 2 ); # debug a error
        return; # stop script
      }

      if ( !parse( $valuesp[$i] ) ) {
        debug( $lib, "Value " . $values[$i] . " contain denied symbol", 2 );  # debug a error
        return; # stop script
      }

      $param = $param . " ".$params[$i]." = " . $values[$i];  # adding parameter and value to string like ( string + parametr = value ) ( WHERE var1 = "admin" and ... )
      if ( $i != ( count( $params ) - 1 ) ) $param = $param . " and";  # add "end" if item not last

    }
  } else $param = ""; # if params items length = 0 set string are empty ( SELECT ... FROM ... ) w/o WHERE func

  $reply = mysqli_query ( $connection, "select $items from $table $param" ); # exec query
  return mysqli_fetch_assoc( $reply );  # return responce

}

function insert ( $table, $valuesNames = array(), $values = array(), $parametersName = array(), $parameters = array() ) {  # insert func

  $valNames = "";     # init values names string
  $val = "";          # init values string
  $params = "where";  # init params string

  if ( !parse( $table ) ) { # checking table name for denied symbols
    debug( $lib, "Table name contain denied symbols", 2 ); # debug error
    return; # stop script
  }

  if ( count( $valuesNames ) != count( $values ) ) {  # check equaling length params names with values
    debug( $lib, "Count of names of values shood be equal with values count" , 2); # debug a error
    return; # stop script
  }

  if ( count( $parametersName ) != count( $parameters ) ) { # checking equaling count parameters names with parameters
    debug( $lib, "Count of parametrs names shood be equal with parametrs values count", 2);
    return; # stop script
  }

  if ( count( $valuesNames ) != 0 ) {  # checking names of values count
    for ( $i = 0; $i < count( $valuesNames ); $i++ ) {  # creating a cycle with names of values length

      if ( !parse( $valuesNames[$i] ) ) { # checking names of values for a denied symbols
        debug( $lib, "Names of values " . $valuesNames[$i] . " contain a denied symbol", 2 );  # debug a error
        return; # stop script
      }

      $valNames = $valNames . " `".$valuesNames[$i]."`";  # add names of parametrs to string ( INTO ... ( `val1`, `val2` ) )
      if ( $i != ( count( $valuesNames ) = 1 ) ) $valNames = $valNames . ","; # add "," if name not last

    }
  } else {  # if names of values are empty
    debug( $lib, "You shood set at least one names of value and one value", 2 ); # debug a error
    return; # stop script
  }

  if ( count( $values ) != 0 ) {  # create a cycle with values count
    for ( $i = 0; $i < count( $values ); $i++ ) { # create a cycle with count of values

      if ( !parse( $values[$i] ) ) {  # checking values for a denied symbols
        debug( $lib, "Value " . $values[$i] . " contain a denied symbol", 2 );  # debug a error
        return; # stop script
      }

      $val = $val . " \"".$values[$i]."\""; # adding value to string ( VALUES ("var", "var") ... )
      if ( $i != ( count( $values ) - 1 ) ) $val = $val . ",";  # add "," if value not last

    }
  } else {  # if names of values are empty
    debug( $lib, "You shood set at least one names of value and one value", 2 ); # debug a error
    return; # stop script
  }

  if ( count($parametersName) != 0 ) {  # checking parameters count
    for ( $i = 0; $i < count( $parametersName ); $i++ ) { #create a cycle with count of patameters

      if ( !parse( $parametersName[$i] ) ) {  # checking parameters name for denied symbols
        debug( $lib, "Parameters name " . $parametersName[$i] . " contains a denied symbol", 2 );  # debug a error
        return; # stop script
      }

      if ( !parse( $parameters[$i] ) ) {  # checking parameters values for denied symbols
        debug( $lib, "Parameters value " . $parameters[$i] . " contain a denied symbol", 2 ); # debugg error
        return; # stop script
      }

      $params = $params . " " . $parametersName[$i] . " = " . $parameters[$i];  # add names of parametrs to string ( INTO ... ( `val1`, `val2` ) )
      if ( $i != ( count( $parametersName ) - 1 ) ) $params = $params . " and"; # add " and" if name is not last

    }
  } else $params = "";  # if parameters are empty set empty variable ( VALUES (...) ) w/o WHERE ...

  mysqli_query( $connection, "insert into $table ($valNames) values ($val) $params" ); # query exec
}

function remove ( $table, $parametersNames, $parameters ) {

  $params = "where";  # init params string

  if ( !parse( $table ) ) { # checking table name for denied symbols
    debug( $lib, "Table name contain denied symbols", 2 ); # debug error
    return; # stop script
  }

  if ( count( $parametersNames ) != count( $parameters ) ) {  # checking equaling count parameters names with parameters
    debug( $lib, "Count of parametrs names shood be equal with parametrs values count", 2); # debugg error
    return; # stop script
  }

  if ( count( $parametersNames ) != 0 ) { # checking patameters count
    for ( $i = 0; $i < count( $parametersNames ); $i++ ) {  # create a cycle with count of parameters

      if ( !parse( $parametersNames[$i] ) ) { # checking parameters names for a denied symbols
        debug( $lib, "Parameters name " . $parametersNames[$i] . " contains a denied symbol", 2 );  # debug a error
        return; # stop script
      }

      if ( !parse( $parameters[$i] ) ) {  # checking parameters values for denied symbols
        debug( $lib, "Parameters value " . $parameters[$i] . " contain a denied symbol", 2 ); # debugg error
        return; # stop script
      }

      $params = $params . " " . $parametersName[$i] . " = " . $parameters[$i];  # add names of parametrs to string ( INTO ... ( `val1`, `val2` ) )
      if ( $i != ( count( $parametersName ) - 1 ) ) $params = $params . " and"; # add " and" if name is not last

    }
  } else {  # if parameters names count = 0
    debug( $lib, "You should set at least one parameters name and one parameter", 2 );  # debug error
    return; # stop script
  }

  mysqli_query( $connection, "delete * from $table $params" );  # exec query

}

function update ( $table, $valuesNames = array(), $values = array(), $parametersName = array(), $parameters = array() ) {

  $values = "";       # init values string
  $params = "where";  # init params string

  if ( count( $valuesNames ) != count( $values ) ) {  # checking equaling count values names with values
    debug( $lib, "Count of names of values shood be equal with values count" , 2 );  # debug error
    return; # stop script
  }

  if ( count( $parametersName ) != count( $parameters ) ) { # checking equaling count parameters names with parameters
    debug( $lib, "Count of parametrs names shood be equal with parametrs values count", 2 );  # debug error
    return; # stop script
  }

  if ( !parse( $table ) ) { # checking table name for denied symbols
    debug( $lib, "Table name contains denied symbols", 2 ); # debug error
    return; # stop script
  }

  if ( count( $valuesNames ) != 0 ) { # checking values names count
    for ( $i = 0; $i < count( $valuesNames ); $i++ ) {  # creating cylce with count of values names

      if ( !parse( $valuesNames[$i] ) ) { # check values names for denied symbols
        debug( $lib, "Value name " . $valuesNames[$i] . " contain denied symbols", 2 ); # debug a error
        return; # stop script
      }

      if ( !parce( $values[$i] ) ) {  # checking values for denied symbols
        debug( $lib, "Value " . $value[$i] . " contain denied symbols", 2 ); # debug a error
        return; # stop script
      }

      $values . " ".$valuesNames[$i]." = ".$values;                 # add values to string ( SET var1 = "true" and ... )
      if ( $i != ( count( $valuesNames ) - 1 ) ) $values . " and";  # add " and" if value is not last
      else $values . " ";                                           # add " " if value is last

    }
  } else {  # if values names count = 0
    debug( $lib, "You shood set at least one names of value and one value", 2 ); # debug error
    return; # stop script
  }

  if ( count( $parametersName ) != 0 ) {  # checking count of parameters names
    for ( $i = 0; $i < count( $parametersName ); $i++ ) { # create cycle with count of parameters names

      if ( !parse( $parametersName[$i] ) ) {  # checking patameters names for denied symbols
        debug( $lib, "Parameters name " . $parametersNames[$i] . " contains a denied symbol", 2 );  # debug a error
        return; # stop script
      }

      if ( !parse( $parameters[$i] ) ) {  # checking patameters for denied symbols
        debug( $lib, "Parameters value " . $parameters[$i] . " contain a denied symbol", 2 ); # debugg error
        return; # stop script
      }

      $params . " " . $parametersName[$i] . " = " . $parameters;      # add parameters name to string ( ... WHERE var1 = "false" and ... )
      if ( $i != ( count( $parametersName ) - 1 ) ) $params . " and"; # add " and" if name is not last

    }
  } else {  # if count of parameters names = 0
    debug($lib, "You should set at least one parameters name and one parameter", 2 ); # debug a error
    return; # stop script
  }

  mysql_query( $connection, "update $table set $values $params" );  # exec query

}

function clear ( $table ) {

  if ( !parse( $table ) ) { # checking table name for denied symbols
    debug( $lib, "Table name contains denied symbols", 2 ); # debug error
    return; # stop script
  }

  mysql_query( $connection, "delete * from $table" ); # exec query

}

?>
