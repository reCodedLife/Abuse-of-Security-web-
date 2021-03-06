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

class DataBase {

  public $connection;         # connection variable
  public $security;           # security class
  public $debugger;           # debugger class
  public $lib = "Databases";  # init lib name

  function __construct() {            # constructor
    $this->security = new Security(); # init security class
    $this->debugger = new Debugger(); # init debugger class
  }

  public function connect( string $login, string $passw, string $name ) {     # connect to database function
    $this->connection = mysqli_connect( "localhost", $login, $passw, $name ); # connect to database
  }

  public function createObject( array $param1, array $param2 ) {  # create object for other functions

    $values = array();   # create values json array
    $object = (object) array(); # init json variable

    for ( $i = 0; $i < count( $param1 ); $i++ ) {
      $object->name  = $param1[$i];   # set name in new object for parameters
      $object->value = $param2[$i];   # set value in object
      $values[] = json_encode( (array) $object );  # add json object to params array
      $object = null;
    }

    return $values;  # return json

  }

  public function getColumns( string $table ) {

    $array = array();  # create array for collumns
    $reply = mysqli_query( $this->connection, "select * from INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = \"$table\"" );  # query exec

    while ( ( $resource = mysqli_fetch_assoc( $reply ) ) )  # get results
      $array[] = $resource["COLUMN_NAME"];  # append result to array

    return $array;  # return array

  }

  public function select ( array $values, string $table, array $parameters ) { # select function

    $items = "";  # init variable for sql include_oncements in select function ( SELECT var1, var2 ... FROM )
    $param = "where";  # init variable for sql parameters in select function ( WHERE var1 = "admin" and ... )

    if ( !$this->security->parse( $table ) ) # checking table string
      $this->debugger->debug( $this->$lib, "Table name contains denied symbols", 2 );  # dubug error

    if ( count( $values ) != 0 ) {  # checking array
      for ( $i = 0; $i < count( $values ); $i++ ) { # create a cycle with include_oncements items length
        $json = json_decode( $values[$i], true );   # create json from string

        if ( !$this->security->parse( $json["name"] ) )  # checking items for denied symbols
          $this->debugger->debug( $this->$lib, "Param " . $this->security->getParsed($json["name"]) . " contain denied symbols", 2 ); # debug a error

        $items . " ".$json["name"];                           # adding item to string ( string + requriment item ) ( SELECT var(item), ... )
        if ( $i != ( count( $values ) - 1 ) ) $items . ", ";  # add "," if item not last
        else $items . " ";                                    # else add free space at end

      }
    } else $items = "*"; # if include_oncements items length = 0 set string = "*" ( SELECT * FROM ... )

    if ( count( $parameters ) != 0 ) {  # checking array
      for ( $i = 0; $i < count($parameters); $i++ ) { # creating a cycle with parameters items length
        $json = json_decode($parameters[$i], true); # create json from string

        if ( !$this->security->parse( $json["name"] ) )   # checking parameters for denied symbols
          $this->debugger->debug( $this->lib, "Param " . $this->security->getParsed($json["name"]) . " contain denied symbol", 2 ); # debug a error

        if ( !$this->security->parse( $json["value"] ) )  # checking values for denied symbols
          $this->debugger->debug( $this->lib, "Value " . $this->security->getParsed($json["value"]) . " contain denied symbol", 2 );  # debug a error

        $param = $param . " " . $json["name"] . " = \"" . $json["value"] . "\"";  # adding parameter and value to string like ( string + parametr = value ) ( WHERE var1 = "admin" and ... )
        if ( $i != ( count( $parameters ) - 1 ) ) $param = $param. " and";        # add "end" if item not last

      }
    } else $param = ""; # if params items length = 0 set string are empty ( SELECT ... FROM ... ) w/o WHERE function

    return  mysqli_query ( $this->connection, "select $items from $table $param" ); # exec query

  }

  public function insert ( string $table, array $values, array $parameters ) {  # insert function

    $valNames = "";     # init values names string
    $value = "";        # init values string
    $params = "where";  # init params string

    if ( !$this->security->parse( $table ) )  # checking table name for denied symbols
      $this->debugger->debug( $this->$lib, "Table name contain denied symbols", 2 ); # debug error

    if ( count( $values ) != 0 ) {  # checking names of values count
      for ( $i = 0; $i < count( $values ); $i++ ) {  # creating a cycle with names of values length
        $json = json_decode( $values[$i], true ); # create json from string

        if ( !$this->security->parse( $json["name"] ) )   # checking names of values for a denied symbols
          $this->debugger->debug( $this->$lib, "Names of values " . $this->security->getParsed($json["name"]) . " contain a denied symbol", 2 );  # debug a error

        if ( !$this->security->parse( $json["value"] ) )  # checking values for a denied symbols
          $this->debugger->debug( $this->$lib, "Value " . $this->security->getParsed($json["value"]) . " contain a denied symbol", 2 );  # debug a error

        $valNames = $valNames . " `" . $json["name"] . "`"; # add names of parametrs to string ( INTO ... ( `val1`, `val2` ) )
        $value = $value . " \"" . $json["value"] . "\"";    # adding value to string ( VALUES ("var", "var") ... )
        if ( $i != ( count( $values ) - 1 ) ) {
          $valNames = $valNames . ",";  # add "," if name not last
          $value = $value . ",";        # add "," if value not last
        }

      }
    } else
      $this->debugger->debug( $this->$lib, "You shood set at least one names of value", 2 ); # debug a error

    if ( count( $parameters ) != 0 ) {  # checking parameters count
      for ( $i = 0; $i < count( $parameters ); $i++ ) { #create a cycle with count of patameters
        $json = json_decode( $parameters, true ); # create json from string

        if ( !$this->security->parse( $json["name"] ) )   # checking parameters name for denied symbols
          $this->debugger->debug( $this->$lib, "Parameters name " . $this->security->getParsed($json["name"]) . " contains a denied symbol", 2 );  # debug a error

        if ( !$this->security->parse( $json["value"] ) )  # checking parameters values for denied symbols
          $this->debugger->debug( $this->$lib, "Parameters value " . $this->security->getParsed($json["value"]) . " contain a denied symbol", 2 ); # debugg error

        $params = $params . " " . $json["name"] . " = \"" . $json["value"] . "\"";  # add names of parametrs to string ( INTO ... ( `val1`, `val2` ) )
        if ( $i != ( count( $parameters ) - 1 ) ) $params = $params . " and";       # add " and" if name is not last

      }
    } else $params = "";  # if parameters are empty set empty variable ( VALUES (...) ) w/o WHERE ...

    mysqli_query( $this->connection, "insert into $table ($valNames) values ($value) $params" ); # query exec
  }

  public function remove ( string $table, array $parameters ) { # remove function

    $params = "where";  # init params string

    if ( !$this->security->parse( $table ) )  # checking table name for denied symbols
      $this->debugger->debug( $this->$lib, "Table name contain denied symbols", 2 ); # debug error

    if ( count( $parameters ) != 0 ) { # checking patameters count
      for ( $i = 0; $i < count( $parameters ); $i++ ) {   # create a cycle with count of parameters
        $json = json_decode( $parameters, true ); # create json from string

        if ( !$this->security->parse( $json["name"] ) )   # checking parameters names for a denied symbols
          $this->debugger->debug( $this->$lib, "Parameters name " . $this->security->getParsed($json["name"]) . " contains a denied symbol", 2 );  # debug a error

        if ( !$this->security->parse( $json["value"] ) )  # checking parameters values for denied symbols
          $this->debugger->debug( $this->$lib, "Parameters value " . $this->security->getParsed($json["name"]) . " contain a denied symbol", 2 ); # debugg error

        $params . " " . $json["name"] . " = \"" . $json["value"] . "\"";      # add names of parametrs to string ( INTO ... ( `val1`, `val2` ) )
        if ( $i != ( count( $parameters ) - 1 ) ) $params = $params . " and"; # add " and" if name is not last

      }
    } else  # if parameters names count = 0
      $this->debugger->debug( $this->$lib, "You should set at least one parameter ", 2 );  # debug error

    mysqli_query( $this->connection, "delete * from $table $params" );  # exec query

  }

  public function update ( string $table, array $values, array $parameters ) { # update function

    $values = "";       # init values string
    $params = "where";  # init params string

    if ( !$this->security->parse( $table ) )  # checking table name for denied symbols
      $this->debugger->debug( $this->$lib, "Table name contains denied symbols", 2 ); # debug error

    if ( count( $values ) != 0 ) { # checking values names count
      for ( $i = 0; $i < count( $values ); $i++ ) {  # creating cylce with count of values names
        $json = json_decode( $values, true );

        if ( !$this->security->parse( $values[$i]["name"] ) )   # check values names for denied symbols
          $this->debugger->debug( $this->$lib, "Value name " . $values[$i]["name"] . " contain denied symbols", 2 ); # debug a error

        if ( !parce( $values[$i]["value"] ) )   # checking values for denied symbols
          $this->debugger->debug( $this->$lib, "Value " . $values[$i]["value"] . " contain denied symbols", 2 ); # debug a error

        $values = $values  . " " . $values[$i]["name"] ." = \"" . $values[$i]["value"] . "\"";  # add values to string ( SET var1 = "true" and ... )
        if ( $i != ( count( $values ) - 1 ) ) $values  = $values . " and";  # add " and" if value is not last
        else $values = $values . " ";                                       # add " " if value is last

      }
    } else  # if values names count = 0
      $this->debugger->debug( $this->$lib, "You shood set at least one names of value", 2 ); # debug error

    if ( count( $parameters ) != 0 ) {  # checking count of parameters names
      for ( $i = 0; $i < count( $parametersName ); $i++ ) { # create cycle with count of parameters names
        $json = json_decode( $parameters, true );

        if ( !$this->security->parse( $json["name"] ) )   # checking patameters names for denied symbols
          $this->debugger->debug( $this->$lib, "Parameters name " . $this->security->getParsed($json["name"]) . " contains a denied symbol", 2 );  # debug a error

        if ( !$this->security->parse( $json["value"] ) )  # checking patameters for denied symbols
          $this->debugger->debug( $this->$lib, "Parameters value " . $this->security->getParsed($json["value"]) . " contain a denied symbol", 2 ); # debugg error

        $params = $params . " " . $json["name"] . " = \"" . $json["value"] . "\"";  # add parameters name to string ( ... WHERE var1 = "false" and ... )
        if ( $i != ( count( $parameters ) - 1 ) ) $params = $params . " and";       # add " and" if name is not last

      }
    } else  # if count of parameters names = 0
      $this->debugger->debug($this->$lib, "You should set at least one parameter", 2 ); # debug a error

    mysql_query( $this->connection, "update $table set $values $params" );  # exec query

  }

  public function clear ( string $table ) { # clear table function

    if ( !$this->security->parse( $table ) )  # checking table name for denied symbols
      $this->debugger->debug( $this->$lib, "Table name contains denied symbols", 2 ); # debug error

    mysql_query( $this->connection, "truncate table $table" ); # exec query

  }

  function createTable ( string $table, array $parameters ) { # creating table function

    /*
    This function use specific object structure:
    [{"name" : "something", "type" : "varchar(256)"}]
    */

    $params = ""; # init params string

    if ( !$this->security->parse( $table ) )  # checking table name for denied symbols
      $this->debugger->debug( $this->$lib, "Table name contains denied symbols", 2 ); # debug error

    if ( count( $parameters ) != 0 ) {  # checking parameters count
      for( $i = 0; $i < count( $parameters ); $i++ ) {  # creating a cycle with paremrters count
        $json = json_decode( $parameters, true );

        if ( !$this->security->parse( $json["name"] ) )   # checking patameters for denied symbols
          $this->debugger->debug( $this->$lib, "Parameters value " . $this->security->getParsed($json["name"]) . " contain a denied symbol", 2 ); # debugg error

        $params = $params . "\t" . $json["name"] . "\t" . $parameters[$i]["type"];  # add parameter to string ( ... newTable (var varchar(100), ); )
        if ( $i < ( count( $parameters ) - 1 ) ) $params = $params . ",";           # add "," if parameter is not last

      }
    } else  # if count of parameters = 0
      $this->debugger->debug($this->$lib, "You should set at least one parameter", 2 ); # debug a error

    mysqli_query( $this->connection, "create table $table ($params)" ); # query exec

  }

  public function dropTable( string $table ) {  # drop table function

    if ( !$this->security->parse( $table ) )  # checking table name for denied symbols
      $this->debugger->debug( $this->$lib, "Table name contains denied symbols", 2 ); # debug error

    mysql_query( "drop table $table" ); # exec query

  }
};

?>
