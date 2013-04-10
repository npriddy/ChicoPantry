<?php

/*
  Module: ChicoPantryDataModule.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Parent function to share data connection with other sql database classes
 */

class ChicoPantryDataModule extends TModule {

    public function init($config)
    /* Purpose: Generic Init required by TModule
     * Parameters
     * 	$config: Used By TModule if there where any special settings
     * Returns: Nothing
     * Side-effects: None
     */ {
        
    }

    protected function connectDatabase()
    /* Purpose: Creates TDbConnection with the url user and pass from
     * application.xml
     * Returns: TDbConnection
     * Side-effects: None
     */
    {
        return new TDbConnection($this->getApplication()->Parameters['DSN'], $this->getApplication()->Parameters['dbUser'], $this->getApplication()->Parameters['dbPass']);
    }

    /**
     * Runs the passed in sql command and returns the DataSet
     * @param string sql
     * @return array() Sql Rows
     */
    public function runQuery($sql)
    /* Purpose: Run the supplied SQL and and return result
     * Parameters
     * 	$sql: Physical SQL to run (Select * from...)
     * Returns: Array of rows of the result
     * Side-effects: None
     */ {
        //Final Result array to return
        $resultsList = array();
        //Database Object
        $_db = self::connectDatabase();
        //Opens a database connection
        $_db->Active = true;
        //Creates the sql command from the sql text
        $command = $_db->createCommand($sql);
        //Physically runs the query
        $reader = $command->query();
        //Convert rows to array
        $i = 0;
        foreach ($reader as $row) {
            $resultsList[$i] = $row;
            $i++;
        }
        //Close Database connection
        $_db->Active = false;
        //Dataset array Generated
        //Utilities::InsertLogEntry("SQL", $sql);
        //Utilities::InsertLogEntry("Qeury Result", $resultsList);
        return $resultsList;
    }

    public function runQueryParam($sql, $param1Text, $param1Value)
    /*
     * Purpose: Run the supplied SQL and and return result
     * Parameters
     *      @param string sql  Physical SQL to run (Select * from...)
     *      @param string param1Text the parameter name that needs to be replaced ex: :Route_ID
     *      @param int param1Value value to be used as parameter
     * Returns: Array() of rows of the result
     * Side-effects: None
     */ {
        //Final Result array to return
        $resultsList = array();
        //Database Object
        $_db = self::connectDatabase();
        //Opens a database connection
        $_db->Active = true;
        //Creates the sql command from the sql text
        $command = $_db->createCommand($sql);
        //Binds the parameters to the sql query
        $command->bindParameter($param1Text, $param1Value);
        //Physically runs the query
        $reader = $command->query();
        //Convert rows to array
        $i = 0;
        foreach ($reader as $row) {
            $resultsList[$i] = $row;
            $i++;
        }
        //Close Database connection
        $_db->Active = false;
        //Dataset array Generated
        //Utilities::InsertLogEntry("runQueryParam " , $sql . ' Param Text ' . $param1Text . ' Param Value ' . $param1Value);
        //Utilities::InsertLogEntry("runQueryParam List " ,$resultsList);
        return $resultsList;
    }

    /**
     * Runs the passed in sql command and returns the DataSet
     * @param string sql
     * @param string param1Text the parameter name that needs to be replaced ex: :Route_ID
     * @param int param1Value value to be used as parameter
     * @return array() Sql Rows
     */
    public function runQueryListOfParam($sql, $parameterList)
    /*
     * Purpose: Run the supplied SQL and and return result with the supplied
     *              array of parameters
     * Parameters
     *      @param string sql  Physical SQL to run (Select * from...)
     *      @param string $parameterList :array of sql parameters
     *             array(':Recipe_ID'=>1);
     * Returns: Array() of rows of the result
     * Side-effects: None
     */ {
        //Final Result array to return
        $resultsList = array();
        //Database Object
        $_db = self::connectDatabase();
        //Opens a database connection
        $_db->Active = true;
        //Creates the sql command from the sql text
        $command = $_db->createCommand($sql);
        //Binds the parameters to the sql query
        foreach ($parameterList as $param) {
            $command->bindParameter($param['text'], $param['value']);
        }
        //Physically runs the query
        $reader = $command->query();
        //Convert rows to array
        $i = 0;
        foreach ($reader as $row) {
            $resultsList[$i] = $row;
            $i++;
        }
        //Close Database connection
        $_db->Active = false;
        //Dataset array Generated
        return $resultsList;
    }

}

?>
