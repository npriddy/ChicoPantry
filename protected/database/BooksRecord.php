<?php
/*
  Module: BooksRecord.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: DOA object for Books table
           Auto generated by Pradosoft
*/
class BooksRecord extends TActiveRecord
{
	const TABLE='books';

	public $ID;
	public $name;

	public static function finder($className=__CLASS__)
        // Purpose:Used with active directory prado framework
        // Parameters
        //	@param $className: Class to find object from passed in
        // Returns: finder object
        // Side-effects: None
	{
		return parent::finder($className);
	}
}
?>