<?php
/*
  Module: MeasureSQL.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Provides SQL and Utility Functions
 *         For Measure Table
*/
class MeasureSQL Extends ChicoPantryDataModule
{
    public function GetAllMeasure()
     /**
     * Purpose: Returns all measures
     * Parameters: (none)
     * Returns: array of Measures data
     * Side-Effects: None
     */
    {
        $sql ="Select Measure_ID, Name from Measure order by Name asc";
        return parent::runQuery($sql);
    }

    public function GetAllMeasureList()
     /**
     * Purpose: Returns array of all measures
     * Parameters:(none)
     * Returns: array of List data
     * Side-Effects: None
     */
    {
        $result = self::GetAllMeasure();
        $data = array();
        foreach($result as $row)
        {
            $data[$row['Measure_ID']] = $row['Name'];
        }
        return $data;
    }
}
?>
