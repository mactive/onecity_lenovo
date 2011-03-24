<?php
/**
 * A class for reading Microsoft Excel Spreadsheets.
 *
 * Version 2.1.3a - 18/03/2009
 *
 * Bug Fix by Matt Roxburgh < http://www.roxburgh.me.uk >
 *
 * In hyperlink records the LENGTH part for the link seems to bear no resemblence to the actual length of the URL so ignore and read string to a zero-zero terminator
 *
 * Version 2.1.3 - 18/03/2009
 *
 * Enhanced by Matt Roxburgh < http://www.roxburgh.me.uk >
 *
 * "hacked" in simple hypertext recognition, each sheet will have a hyperlink element containing the rows on that sheet that have the link, and details on the link itself.

                    [hyperlink] => Array
                        (
                            [0] => Array
                                (
                                    [flags] => 23
                                    [raw] => code [1b8], length [156],  0c 00 0c 00 03 00 03 00 d0 c9 ea 79 f9 ba ce 11 8c 82 00 aa 00 4b a9 0b 02 00 00 00 17 00 00 00 0f 00 00 00 6c 00 69 00 6e 00 6b 00 20 00 74 00 6f 00 20 00 67 00 6f 00 6f 00 67 00 6c 00 65 00 00 00 e0 c9 ea 79 f9 ba ce 11 8c 82 00 aa 00 4b a9 0b 46 00 00 00 68 00 74 00 74 00 70 00 3a 00 2f 00 2f 00 77 00 77 00 77 00 2e 00 67 00 6f 00 6f 00 67 00 6c 00 65 00 2e 00 63 00 6f 00 6d 00 2f 00 00 00 79 58 81 f4 3b 1d 7f 48 af 2c 82 5d c4 85 27 63 00 00 00 00 a5 ab 00 00 67
                                    [desc] => link to google
                                    [link] => http://www.google.com/
                                    [rowfrom] => 12
                                    [rowto] => 12
                                    [colfrom] => 3
                                    [colto] => 3
                                )

                        )
 *
 * also added font name detection which basically adds two extra fields in the fonts array "font" with the font name and "fontType" which is a number 0 - 5, which can be used to reference the new fontTypes array. 


    [formatRecords] => Array 
        ( 
            [fonts] => Array 
                ( 
                    [0] => Array 
                        ( 
                            [height] => 11 
                            [italic] => 0 
                            [colour] => 8 
                            [under] => 0 
                            [bold] => 0 
                            [font] => Calibri 
                            [fontType] => 2 
                        ) 

 *
 * Version 2.1.2 - 10/03/2009
 *
 * Enhanced by Matt Roxburgh < http://www.roxburgh.me.uk >
 *
 * "hacked" in formatting for dates and number fields which were not working
 *
 *  NB Numbers in "general" fields align right in excel but have no alignment that I can see in the XLS file
 *  if you want your number fields right aligned then make them numbers or align right in the excel field.
 * * 
 * Version 2.1.1 - 30/01/2009
 *
 * Enhanced by Matt Roxburgh < http://www.roxburgh.me.uk >
 *
 * Added decoding of simple formatting items
 *
 *    alignment - left, right and centre
 *    background colour 
 *    font colour
 *    style - bold, italic, underline
 * 
 * Version 2.1
 *
 * Enhanced by Matt Kruse < http://mattkruse.com >
 * Maintained at http://code.google.com/p/php-excel-reader/
 *
 * Originally developed by Vadim Tkachenko under the name PHPExcelReader.
 * (http://sourceforge.net/projects/phpexcelreader)
 * Based on the Java version by Andy Khan (http://www.andykhan.com).  Now
 * maintained by David Sanders.  Reads only Biff 7 and Biff 8 formats.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Spreadsheet
 * @package	Spreadsheet_Excel_Reader
 * @author	 Vadim Tkachenko <vt@apachephp.com>
 * @license	http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version	CVS: $Id: reader.php 19 2007-03-13 12:42:41Z shangxiao $
 * @link	   http://pear.php.net/package/Spreadsheet_Excel_Reader
 * @see		OLE, Spreadsheet_Excel_Writer
 */

/*
Example Usage:

$data = new Spreadsheet_Excel_Reader("test.xls");

Or conserve memory for large worksheets by not storing the extended 'cellsInfo':
 $data = new Spreadsheet_Excel_Reader("test.xls",false);

Retrieve formatted value of cell (first or only sheet):

  $data->val($row,$col)

Or using column names:

  $data->val(10,'AZ')

From a sheet other than the first:

  $data->val($row,$col,$sheet_index)

Retrieve cell info:

  $data->type($row,$col);
  $data->raw($row,$col);
  $data->format($row,$col);
  $data->formatIndex($row,$col);

Get sheet size:

  $data->rowcount();
  $data->colcount();

$data->sheets[0]['cells'][$i][$j] - data from $i-row $j-column

$data->sheets[0]['numRows'] - count rows
$data->sheets[0]['numCols'] - count columns

$data->sheets[0]['cellsInfo'][$i][$j] - extended info about cell
$data->sheets[0]['cellsInfo'][$i][$j]['type'] = "date" | "number" | "unknown"
$data->sheets[0]['cellsInfo'][$i][$j]['raw'] = value if cell without format
$data->sheets[0]['cellsInfo'][$i][$j]['format'] = Excel-style Format string of cell
$data->sheets[0]['cellsInfo'][$i][$j]['formatIndex'] = The internal Excel index of format

$data->sheets[0]['cellsInfo'][$i][$j]['colspan']
$data->sheets[0]['cellsInfo'][$i][$j]['rowspan']

*/



define('NUM_BIG_BLOCK_DEPOT_BLOCKS_POS', 0x2c);
define('SMALL_BLOCK_DEPOT_BLOCK_POS', 0x3c);
define('ROOT_START_BLOCK_POS', 0x30);
define('BIG_BLOCK_SIZE', 0x200);
define('SMALL_BLOCK_SIZE', 0x40);
define('EXTENSION_BLOCK_POS', 0x44);
define('NUM_EXTENSION_BLOCK_POS', 0x48);
define('PROPERTY_STORAGE_BLOCK_SIZE', 0x80);
define('BIG_BLOCK_DEPOT_BLOCKS_POS', 0x4c);
define('SMALL_BLOCK_THRESHOLD', 0x1000);
// property storage offsets
define('SIZE_OF_NAME_POS', 0x40);
define('TYPE_POS', 0x42);
define('START_BLOCK_POS', 0x74);
define('SIZE_POS', 0x78);
define('IDENTIFIER_OLE', pack("CCCCCCCC",0xd0,0xcf,0x11,0xe0,0xa1,0xb1,0x1a,0xe1));

function GetInt4d($data, $pos)
{
	$value = ord($data[$pos]) | (ord($data[$pos+1])	<< 8) | (ord($data[$pos+2]) << 16) | (ord($data[$pos+3]) << 24);
	if ($value>=4294967294)
	{
		$value=-2;
	}
	return $value;
}


class OLERead {
	var $data = '';
	
	function OLERead(){	}

	function read($sFileName){
		// check if file exist and is readable (Darko Miljanovic)
		if(!is_readable($sFileName)) {
			$this->error = 1;
			return false;
		}
		$this->data = @file_get_contents($sFileName);
		if (!$this->data) {
			$this->error = 1;
			return false;
   		}
   		if (substr($this->data, 0, 8) != IDENTIFIER_OLE) {
			$this->error = 1;
			return false;
   		}
		$this->numBigBlockDepotBlocks = GetInt4d($this->data, NUM_BIG_BLOCK_DEPOT_BLOCKS_POS);
		$this->sbdStartBlock = GetInt4d($this->data, SMALL_BLOCK_DEPOT_BLOCK_POS);
		$this->rootStartBlock = GetInt4d($this->data, ROOT_START_BLOCK_POS);
		$this->extensionBlock = GetInt4d($this->data, EXTENSION_BLOCK_POS);
		$this->numExtensionBlocks = GetInt4d($this->data, NUM_EXTENSION_BLOCK_POS);

		$bigBlockDepotBlocks = array();
		$pos = BIG_BLOCK_DEPOT_BLOCKS_POS;
		$bbdBlocks = $this->numBigBlockDepotBlocks;
		if ($this->numExtensionBlocks != 0) {
			$bbdBlocks = (BIG_BLOCK_SIZE - BIG_BLOCK_DEPOT_BLOCKS_POS)/4;
		}

		for ($i = 0; $i < $bbdBlocks; $i++) {
			$bigBlockDepotBlocks[$i] = GetInt4d($this->data, $pos);
			$pos += 4;
		}


		for ($j = 0; $j < $this->numExtensionBlocks; $j++) {
			$pos = ($this->extensionBlock + 1) * BIG_BLOCK_SIZE;
			$blocksToRead = min($this->numBigBlockDepotBlocks - $bbdBlocks, BIG_BLOCK_SIZE / 4 - 1);

			for ($i = $bbdBlocks; $i < $bbdBlocks + $blocksToRead; $i++) {
				$bigBlockDepotBlocks[$i] = GetInt4d($this->data, $pos);
				$pos += 4;
			}

			$bbdBlocks += $blocksToRead;
			if ($bbdBlocks < $this->numBigBlockDepotBlocks) {
				$this->extensionBlock = GetInt4d($this->data, $pos);
			}
		}

		// readBigBlockDepot
		$pos = 0;
		$index = 0;
		$this->bigBlockChain = array();

		for ($i = 0; $i < $this->numBigBlockDepotBlocks; $i++) {
			$pos = ($bigBlockDepotBlocks[$i] + 1) * BIG_BLOCK_SIZE;
			//echo "pos = $pos";
			for ($j = 0 ; $j < BIG_BLOCK_SIZE / 4; $j++) {
				$this->bigBlockChain[$index] = GetInt4d($this->data, $pos);
				$pos += 4 ;
				$index++;
			}
		}

		// readSmallBlockDepot();
		$pos = 0;
		$index = 0;
		$sbdBlock = $this->sbdStartBlock;
		$this->smallBlockChain = array();

		while ($sbdBlock != -2) {
		  $pos = ($sbdBlock + 1) * BIG_BLOCK_SIZE;
		  for ($j = 0; $j < BIG_BLOCK_SIZE / 4; $j++) {
			$this->smallBlockChain[$index] = GetInt4d($this->data, $pos);
			$pos += 4;
			$index++;
		  }
		  $sbdBlock = $this->bigBlockChain[$sbdBlock];
		}


		// readData(rootStartBlock)
		$block = $this->rootStartBlock;
		$pos = 0;
		$this->entry = $this->__readData($block);
		$this->__readPropertySets();
	}

	function __readData($bl) {
		$block = $bl;
		$pos = 0;
		$data = '';
		while ($block != -2)  {
			$pos = ($block + 1) * BIG_BLOCK_SIZE;
			$data = $data.substr($this->data, $pos, BIG_BLOCK_SIZE);
			$block = $this->bigBlockChain[$block];
		}
		return $data;
	 }

	function __readPropertySets(){
		$offset = 0;
		while ($offset < strlen($this->entry)) {
			$d = substr($this->entry, $offset, PROPERTY_STORAGE_BLOCK_SIZE);
			$nameSize = ord($d[SIZE_OF_NAME_POS]) | (ord($d[SIZE_OF_NAME_POS+1]) << 8);
			$type = ord($d[TYPE_POS]);
			$startBlock = GetInt4d($d, START_BLOCK_POS);
			$size = GetInt4d($d, SIZE_POS);
			$name = '';
			for ($i = 0; $i < $nameSize ; $i++) {
				$name .= $d[$i];
			}
			$name = str_replace("\x00", "", $name);
			$this->props[] = array (
				'name' => $name,
				'type' => $type,
				'startBlock' => $startBlock,
				'size' => $size);
			if (($name == "Workbook") || ($name == "Book")) {
				$this->wrkbook = count($this->props) - 1;
			}
			if ($name == "Root Entry") {
				$this->rootentry = count($this->props) - 1;
			}
			$offset += PROPERTY_STORAGE_BLOCK_SIZE;
		}

	}


	function getWorkBook(){
		if ($this->props[$this->wrkbook]['size'] < SMALL_BLOCK_THRESHOLD){
			$rootdata = $this->__readData($this->props[$this->rootentry]['startBlock']);
			$streamData = '';
			$block = $this->props[$this->wrkbook]['startBlock'];
			$pos = 0;
			while ($block != -2) {
	  			  $pos = $block * SMALL_BLOCK_SIZE;
				  $streamData .= substr($rootdata, $pos, SMALL_BLOCK_SIZE);
				  $block = $this->smallBlockChain[$block];
			}
			return $streamData;
		}else{
			$numBlocks = $this->props[$this->wrkbook]['size'] / BIG_BLOCK_SIZE;
			if ($this->props[$this->wrkbook]['size'] % BIG_BLOCK_SIZE != 0) {
				$numBlocks++;
			}

			if ($numBlocks == 0) return '';
			$streamData = '';
			$block = $this->props[$this->wrkbook]['startBlock'];
			$pos = 0;
			while ($block != -2) {
			  $pos = ($block + 1) * BIG_BLOCK_SIZE;
			  $streamData .= substr($this->data, $pos, BIG_BLOCK_SIZE);
			  $block = $this->bigBlockChain[$block];
			}
			return $streamData;
		}
	}

}

define('SPREADSHEET_EXCEL_READER_BIFF8',			 0x600);
define('SPREADSHEET_EXCEL_READER_BIFF7',			 0x500);
define('SPREADSHEET_EXCEL_READER_WORKBOOKGLOBALS',   0x5);
define('SPREADSHEET_EXCEL_READER_WORKSHEET',		 0x10);
define('SPREADSHEET_EXCEL_READER_TYPE_BOF',  		 0x809);
define('SPREADSHEET_EXCEL_READER_TYPE_EOF',		     0x0a);
define('SPREADSHEET_EXCEL_READER_TYPE_BOUNDSHEET',   0x85);
define('SPREADSHEET_EXCEL_READER_TYPE_DIMENSION',	 0x200);
define('SPREADSHEET_EXCEL_READER_TYPE_ROW',		     0x208);
define('SPREADSHEET_EXCEL_READER_TYPE_DBCELL',	     0xd7);
define('SPREADSHEET_EXCEL_READER_TYPE_FILEPASS',	 0x2f);
define('SPREADSHEET_EXCEL_READER_TYPE_NOTE',		 0x1c);
define('SPREADSHEET_EXCEL_READER_TYPE_TXO',		     0x1b6);
define('SPREADSHEET_EXCEL_READER_TYPE_HYPER',	     0x1b8);
define('SPREADSHEET_EXCEL_READER_TYPE_RK',		   0x7e);
define('SPREADSHEET_EXCEL_READER_TYPE_RK2',		  0x27e);
define('SPREADSHEET_EXCEL_READER_TYPE_MULRK',		0xbd);
define('SPREADSHEET_EXCEL_READER_TYPE_MULBLANK',	 0xbe);
define('SPREADSHEET_EXCEL_READER_TYPE_INDEX',		0x20b);
define('SPREADSHEET_EXCEL_READER_TYPE_SST',		  0xfc);
define('SPREADSHEET_EXCEL_READER_TYPE_EXTSST',	   0xff);
define('SPREADSHEET_EXCEL_READER_TYPE_CONTINUE',	 0x3c);
define('SPREADSHEET_EXCEL_READER_TYPE_LABEL',		0x204);
define('SPREADSHEET_EXCEL_READER_TYPE_LABELSST',	 0xfd);
define('SPREADSHEET_EXCEL_READER_TYPE_NUMBER',	   0x203);
define('SPREADSHEET_EXCEL_READER_TYPE_NAME',		 0x18);
define('SPREADSHEET_EXCEL_READER_TYPE_ARRAY',		0x221);
define('SPREADSHEET_EXCEL_READER_TYPE_STRING',	   0x207);
define('SPREADSHEET_EXCEL_READER_TYPE_FORMULA',	  0x406);
define('SPREADSHEET_EXCEL_READER_TYPE_FORMULA2',	 0x6);
define('SPREADSHEET_EXCEL_READER_TYPE_FORMAT',	   0x41e);
define('SPREADSHEET_EXCEL_READER_TYPE_XF',		   0xe0);
define('SPREADSHEET_EXCEL_READER_TYPE_BOOLERR',	  0x205);
define('SPREADSHEET_EXCEL_READER_TYPE_FONT',	  0x0031);
define('SPREADSHEET_EXCEL_READER_TYPE_PALETTE',	  0x0092);
define('SPREADSHEET_EXCEL_READER_TYPE_UNKNOWN',	  0xffff);
define('SPREADSHEET_EXCEL_READER_TYPE_NINETEENFOUR', 0x22);
define('SPREADSHEET_EXCEL_READER_TYPE_MERGEDCELLS',  0xE5);
define('SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS' ,	25569);
define('SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS1904', 24107);
define('SPREADSHEET_EXCEL_READER_MSINADAY',		  86400);
define('SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT',	"%s");


/*
* Place includes, constant defines and $_GLOBAL settings here.
* Make sure they have appropriate docblocks to avoid phpDocumentor
* construing they are documented by the page-level docblock.
*/


class Spreadsheet_Excel_Reader {

	// MK: Added to make data retrieval easier
	var $colnames = array();

    function myHex($d) {
        
        if ($d < 16) return "0" . dechex($d);
        
        return dechex($d);
        
    }
	
	function dumpHexData($data, $pos, $length, $code) {

		$info = "code [" . $this->myHex($code) . "], length [" . $length . "], ";
		for ($i = 0; $i <= $length; $i++) {
			$info .= " " . $this->myHex(ord($data[$pos + $i]));
		}
		return $info;
		
	}



	function getCol($col) {
		if (is_string($col)) {
			$col = strtolower($col);
			if (array_key_exists($col,$this->colnames)) {
				$col = $this->colnames[$col];
			}
		}
		return $col;
	}
	function val($row,$col,$sheet=0) {
		$col = $this->getCol($col);
		if (array_key_exists($row,$this->sheets[$sheet]['cells']) && array_key_exists($col,$this->sheets[$sheet]['cells'][$row])) {
			return $this->sheets[$sheet]['cells'][$row][$col];
		}
		return "";
	}
	function value($row,$col,$sheet=0) {
		return $this->val($row,$col,$sheet);
	}
	function info($row,$col,$type='',$sheet=0) {
		$col = $this->getCol($col);
		if (array_key_exists('cellsInfo',$this->sheets[$sheet])
				&& array_key_exists($row,$this->sheets[$sheet]['cellsInfo'])
				&& array_key_exists($col,$this->sheets[$sheet]['cellsInfo'][$row])
				&& array_key_exists($type,$this->sheets[$sheet]['cellsInfo'][$row][$col])) {
			return $this->sheets[$sheet]['cellsInfo'][$row][$col][$type];
		}
		return "";
	}
	function type($row,$col,$sheet=0) {
		return $this->info($row,$col,'type',$sheet);
	}
	function raw($row,$col,$sheet=0) {
		return $this->info($row,$col,'raw',$sheet);
	}
	function format($row,$col,$sheet=0) {
		return $this->info($row,$col,'format',$sheet);
	}
	function formatIndex($row,$col,$sheet=0) {
		return $this->info($row,$col,'formatIndex',$sheet);
	}
	function rowcount($sheet=0) {
		return $this->sheets[$sheet]['numRows'];
	}
	function colcount($sheet=0) {
		return $this->sheets[$sheet]['numCols'];
	}


	var $boundsheets = array();
	var $formatRecords = array();
	var $sst = array();
	/**
	 * Array of worksheets
	 *
	 * The data is stored in 'cells' and the meta-data is stored in an array
	 * called 'cellsInfo'
	 *
	 * Example:
	 *
	 * $sheets  -->  'cells'  -->  row --> column --> Interpreted value
	 *		  -->  'cellsInfo' --> row --> column --> 'type' - Can be 'date', 'number', or 'unknown'
	 *											--> 'raw' - The raw data that Excel stores for that data cell
	 *
	 * @var array
	 * @access public
	 */
	var $sheets = array();

	var $data;
	var $_ole;
	var $_defaultEncoding;
	var $_defaultFormat = SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT;
	var $_columnsFormat = array();
	var $_rowoffset = 1;
	var $_coloffset = 1;

	/**
	 * List of default date formats used by Excel
	 *
	 * @var array
	 * @access public
	 */
	var $dateFormats = array (
		0xe => "m/d/Y",
		0xf => "M-d-Y",
		0x10 => "d-M",
		0x11 => "M-Y",
		0x12 => "h:i a",
		0x13 => "h:i:s a",
		0x14 => "H:i",
		0x15 => "H:i:s",
		0x16 => "d/m/Y H:i",
		0x2d => "i:s",
		0x2e => "H:i:s",
		0x2f => "i:s.S");

	/**
	 * Default number formats used by Excel
	 *
	 * @var array
	 * @access public
	 */
	var $numberFormats = array(
		0x1 => "0",
		0x2 => "0.00",
		0x3 => "#,##0",
		0x4 => "#,##0.00",
		0x5 => "\$#,##0;(\$#,##0)",
		0x6 => "\$#,##0;[Red](\$#,##0)",
		0x7 => "\$#,##0.00;(\$#,##0.00)",
		0x8 => "\$#,##0.00;[Red](\$#,##0.00)",
		0x9 => "0%",
		0xa => "0.00%",
		0xb => "0.00E+00",
		0x25 => "#,##0;(#,##0)",
		0x26 => "#,##0;[Red](#,##0)",
		0x27 => "#,##0.00;(#,##0.00)",
		0x28 => "#,##0.00;[Red](#,##0.00)",
		0x29 => "#,##0;(#,##0)",  // Not exactly
		0x2a => "\$#,##0;(\$#,##0)",  // Not exactly
		0x2b => "#,##0.00;(#,##0.00)",  // Not exactly
		0x2c => "\$#,##0.00;(\$#,##0.00)",  // Not exactly
		0x30 => "##0.0E+0"
		);

	function _colorWrap($str) {
		return "<span class=\"red\">$str</span>";
	}

    var $fontTypes = Array(
        0 => 'Unknown',
        1 => 'Roman',
        2 => 'Swiss',
        3 => 'Modern',
        4 => 'Script',
        5 => 'Decorative');

    var $colours = Array(
        0x00 => "#000000",
        0x01 => "#FFFFFF",
        0x02 => "#FF0000",
        0x03 => "#00FF00",
        0x04 => "#0000FF",
        0x05 => "#FFFF00",
        0x06 => "#FF00FF",
        0x07 => "#00FFFF",
        0x08 => "#000000",
        0x09 => "#FFFFFF",
        0x0A => "#FF0000",
        0x0B => "#00FF00",
        0x0C => "#0000FF",
        0x0D => "#FFFF00",
        0x0E => "#FF00FF",
        0x0F => "#00FFFF",
        0x10 => "#800000",
        0x11 => "#008000",
        0x12 => "#000080",
        0x13 => "#808000",
        0x14 => "#800080",
        0x15 => "#008080",
        0x16 => "#C0C0C0",
        0x17 => "#808080",
        0x18 => "#9999FF",
        0x19 => "#993366",
        0x1A => "#FFFFCC",
        0x1B => "#CCFFFF",
        0x1C => "#660066",
        0x1D => "#FF8080",
        0x1E => "#0066CC",
        0x1F => "#CCCCFF",
        0x20 => "#000080",
        0x21 => "#FF00FF",
        0x22 => "#FFFF00",
        0x23 => "#00FFFF",
        0x24 => "#800080",
        0x25 => "#800000",
        0x26 => "#008080",
        0x27 => "#0000FF",
        0x28 => "#00CCFF",
        0x29 => "#CCFFFF",
        0x2A => "#CCFFCC",
        0x2B => "#FFFF99",
        0x2C => "#99CCFF",
        0x2D => "#FF99CC",
        0x2E => "#CC99FF",
        0x2F => "#FFCC99",
        0x30 => "#3366FF",
        0x31 => "#33CCCC",
        0x32 => "#99CC00",
        0x33 => "#FFCC00",
        0x34 => "#FF9900",
        0x35 => "#FF6600",
        0x36 => "#666699",
        0x37 => "#969696",
        0x38 => "#003366",
        0x39 => "#339966",
        0x3A => "#003300",
        0x3B => "#333300",
        0x3C => "#993300",
        0x3D => "#993366",
        0x3E => "#333399",
        0x3F => "#333333",
        0x40 => "#FFFFFF",
        0x41 => "#000000",

        0x43 => "#000000",
        0x4D => "#000000",

        0x4E => "#FFFFFF",
        0x4F => "#000000",

        0x50 => "#FFFFFF",
        0x51 => "#000000",

        0x7FFF => "#000000"
    );

	// ADDED by Matt Kruse for better formatting
	function _format_value($format,$num,$f) {
		if (!$f && $format=="%s") { return $num; }

		// Custom pattern can be POSITIVE;NEGATIVE;ZERO
		// The "text" option as 4th parameter is not handled
		$parts = split(";",$format);
		$pattern = $parts[0];
		// Negative pattern
		if (count($parts)>2 && $num==0) {
			$pattern = $parts[2];
		}
		// Zero pattern
		if (count($parts)>1 && $num<0) {
			$pattern = $parts[1];
			$num = abs($num);
		}

		$color = "";
		$color_regex = "/^\[(BLACK|BLUE|CYAN|GREEN|MAGENTA|RED|WHITE|YELLOW)\]/i";
		if (preg_match($color_regex,$pattern,$matches)) {
			$color = strtolower($matches[1]);
			$pattern = preg_replace($color_regex,"",$pattern);
		}

		// TEMPORARY - Convert # to 0
		$pattern = preg_replace("/\#/","0",$pattern);

		// Find out if we need comma formatting
		$has_commas = preg_match("/,/",$pattern);
		if ($has_commas) {
			$pattern = preg_replace("/,/","",$pattern);
		}

		// Handle Percentages
		if (preg_match("/\d(\%)([^\%]|$)/",$pattern,$matches)) {
			$num = $num * 100;
			$pattern = preg_replace("/(\d)(\%)([^\%]|$)/","$1%$3",$pattern);
		}

		// Handle the number itself
		$number_regex = "/(\d+)(\.?)(\d*)/";
		if (preg_match($number_regex,$pattern,$matches)) {
			$left = $matches[1];
			$dec = $matches[2];
			$right = $matches[3];
			if ($has_commas) {
				$formatted = number_format($num,strlen($right));
			}
			else {
				$sprintf_pattern = "%1.".strlen($right)."f";
				$formatted = sprintf($sprintf_pattern, $num);
			}
			$pattern = preg_replace($number_regex, $formatted, $pattern);
		}

		if ($color) {
			$pattern = "<span style=\"color:$color\">".$pattern."</span>";
		}
		return $pattern;
	}

	/**
	 * Constructor
	 *
	 * Some basic initialisation
	 */
	function Spreadsheet_Excel_Reader($file='')
	{
		$this->_ole =& new OLERead();
		$this->setUTFEncoder('iconv');
		for ($i=1; $i<245; $i++) {
			$name = ((($i-1)/26>=1)?chr(($i-1)/26+64):'') . chr(($i-1)%26+65);;
			$this->colnames[strtolower($name)] = $i;
		}
		if ($file!="") {
			$this->read($file);
		}
	}

	/**
	 * Set the encoding method
	 *
	 * @param string Encoding to use
	 * @access public
	 */
	function setOutputEncoding($encoding)
	{
		$this->_defaultEncoding = $encoding;
	}

	/**
	 *  $encoder = 'iconv' or 'mb'
	 *  set iconv if you would like use 'iconv' for encode UTF-16LE to your encoding
	 *  set mb if you would like use 'mb_convert_encoding' for encode UTF-16LE to your encoding
	 *
	 * @access public
	 * @param string Encoding type to use.  Either 'iconv' or 'mb'
	 */
	function setUTFEncoder($encoder = 'iconv')
	{
		$this->_encoderFunction = '';
		if ($encoder == 'iconv') {
			$this->_encoderFunction = function_exists('iconv') ? 'iconv' : '';
		} elseif ($encoder == 'mb') {
			$this->_encoderFunction = function_exists('mb_convert_encoding') ?
									  'mb_convert_encoding' :
									  '';
		}
	}

	/**
	 * todo
	 *
	 * @access public
	 * @param offset
	 */
	function setRowColOffset($iOffset)
	{
		$this->_rowoffset = $iOffset;
		$this->_coloffset = $iOffset;
	}

	/**
	 * Set the default number format
	 *
	 * @access public
	 * @param Default format
	 */
	function setDefaultFormat($sFormat)
	{
		$this->_defaultFormat = $sFormat;
	}

	function read16bitstring($data, $start) {

	   $len = 0;
	   while (ord($data[$start + $len]) + ord($data[$start + $len + 1]) > 0) $len++;
	   return substr($data, $start, $len);
	   
	}
	
	/**
	 * Force a column to use a certain format
	 *
	 * @access public
	 * @param integer Column number
	 * @param string Format
	 */
	function setColumnFormat($column, $sFormat)
	{
		$this->_columnsFormat[$column] = $sFormat;
	}


	/**
	 * Read the spreadsheet file using OLE, then parse
	 *
	 * @access public
	 * @param filename
	 * @todo return a valid value
	 */
	function read($sFileName)
	{
		$res = $this->_ole->read($sFileName);

		// oops, something goes wrong (Darko Miljanovic)
		if($res === false) {
			// check error code
			if($this->_ole->error == 1) {
			// bad file
				die('The filename ' . $sFileName . ' is not readable');
			}
			// check other error codes here (eg bad fileformat, etc...)
		}

		$this->data = $this->_ole->getWorkBook();
		$this->_parse();
	}

	/**
	 * Parse a workbook
	 *
	 * @access private
	 * @return bool
	 */
	function _parse()
	{
		$pos = 0;

		$code = ord($this->data[$pos]) | ord($this->data[$pos+1])<<8;
		$length = ord($this->data[$pos+2]) | ord($this->data[$pos+3])<<8;

		$version = ord($this->data[$pos + 4]) | ord($this->data[$pos + 5])<<8;
		$substreamType = ord($this->data[$pos + 6]) | ord($this->data[$pos + 7])<<8;

		if (($version != SPREADSHEET_EXCEL_READER_BIFF8) &&
			($version != SPREADSHEET_EXCEL_READER_BIFF7)) {
			return false;
		}

		if ($substreamType != SPREADSHEET_EXCEL_READER_WORKBOOKGLOBALS){
			return false;
		}

		$pos += $length + 4;

		$code = ord($this->data[$pos]) | ord($this->data[$pos+1])<<8;
		$length = ord($this->data[$pos+2]) | ord($this->data[$pos+3])<<8;

		while ($code != SPREADSHEET_EXCEL_READER_TYPE_EOF) {
			switch ($code) {
				case SPREADSHEET_EXCEL_READER_TYPE_SST:
					//echo "Type_SST\n";
					 $spos = $pos + 4;
					 $limitpos = $spos + $length;
					 $uniqueStrings = $this->_GetInt4d($this->data, $spos+4);
												$spos += 8;
									   for ($i = 0; $i < $uniqueStrings; $i++) {
										// Read in the number of characters
												if ($spos == $limitpos) {
												$opcode = ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
												$conlength = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;
														if ($opcode != 0x3c) {
																return -1;
														}
												$spos += 4;
												$limitpos = $spos + $conlength;
												}
												$numChars = ord($this->data[$spos]) | (ord($this->data[$spos+1]) << 8);
												//echo "i = $i pos = $pos numChars = $numChars ";
												$spos += 2;
												$optionFlags = ord($this->data[$spos]);
												$spos++;
										$asciiEncoding = (($optionFlags & 0x01) == 0) ;
												$extendedString = ( ($optionFlags & 0x04) != 0);

												// See if string contains formatting information
												$richString = ( ($optionFlags & 0x08) != 0);

												if ($richString) {
										// Read in the crun
														$formattingRuns = ord($this->data[$spos]) | (ord($this->data[$spos+1]) << 8);
														$spos += 2;
												}

												if ($extendedString) {
												  // Read in cchExtRst
												  $extendedRunLength = $this->_GetInt4d($this->data, $spos);
												  $spos += 4;
												}

												$len = ($asciiEncoding)? $numChars : $numChars*2;
												if ($spos + $len < $limitpos) {
																$retstr = substr($this->data, $spos, $len);
																$spos += $len;
												}else{
														// found countinue
														$retstr = substr($this->data, $spos, $limitpos - $spos);
														$bytesRead = $limitpos - $spos;
														$charsLeft = $numChars - (($asciiEncoding) ? $bytesRead : ($bytesRead / 2));
														$spos = $limitpos;

														 while ($charsLeft > 0){
																$opcode = ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
																$conlength = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;
																		if ($opcode != 0x3c) {
																				return -1;
																		}
																$spos += 4;
																$limitpos = $spos + $conlength;
																$option = ord($this->data[$spos]);
																$spos += 1;
																  if ($asciiEncoding && ($option == 0)) {
																				$len = min($charsLeft, $limitpos - $spos); // min($charsLeft, $conlength);
																	$retstr .= substr($this->data, $spos, $len);
																	$charsLeft -= $len;
																	$asciiEncoding = true;
																  }elseif (!$asciiEncoding && ($option != 0)){
																				$len = min($charsLeft * 2, $limitpos - $spos); // min($charsLeft, $conlength);
																	$retstr .= substr($this->data, $spos, $len);
																	$charsLeft -= $len/2;
																	$asciiEncoding = false;
																  }elseif (!$asciiEncoding && ($option == 0)) {
																// Bummer - the string starts off as Unicode, but after the
																// continuation it is in straightforward ASCII encoding
																				$len = min($charsLeft, $limitpos - $spos); // min($charsLeft, $conlength);
																		for ($j = 0; $j < $len; $j++) {
																 $retstr .= $this->data[$spos + $j].chr(0);
																}
															$charsLeft -= $len;
																$asciiEncoding = false;
																  }else{
															$newstr = '';
																	for ($j = 0; $j < strlen($retstr); $j++) {
																	  $newstr = $retstr[$j].chr(0);
																	}
																	$retstr = $newstr;
																				$len = min($charsLeft * 2, $limitpos - $spos); // min($charsLeft, $conlength);
																	$retstr .= substr($this->data, $spos, $len);
																	$charsLeft -= $len/2;
																	$asciiEncoding = false;
																		//echo "Izavrat\n";
																  }
														  $spos += $len;

														 }
												}
												$retstr = ($asciiEncoding) ? $retstr : $this->_encodeUTF16($retstr);
//											  echo "Str $i = $retstr\n";
										if ($richString){
												  $spos += 4 * $formattingRuns;
												}

												// For extended strings, skip over the extended string data
												if ($extendedString) {
												  $spos += $extendedRunLength;
												}
														//if ($retstr == 'Derby'){
														//	  echo "bb\n";
														//}
												$this->sst[]=$retstr;
									   }
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_FILEPASS:
					return false;
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_NAME:
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_FORMAT:
						$indexCode = ord($this->data[$pos+4]) | ord($this->data[$pos+5]) << 8;
						if ($version == SPREADSHEET_EXCEL_READER_BIFF8) {
							$numchars = ord($this->data[$pos+6]) | ord($this->data[$pos+7]) << 8;
							if (ord($this->data[$pos+8]) == 0){
								$formatString = substr($this->data, $pos+9, $numchars);
							} else {
								$formatString = substr($this->data, $pos+9, $numchars*2);
							}
						} else {
							$numchars = ord($this->data[$pos+6]);
							$formatString = substr($this->data, $pos+7, $numchars*2);
						}
					$this->formatRecords[$indexCode] = $formatString;
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_FONT:
						$height = ord($this->data[$pos+4]) | ord($this->data[$pos+5]) << 8;
						$option = ord($this->data[$pos+6]) | ord($this->data[$pos+7]) << 8;
						$colour = ord($this->data[$pos+8]) | ord($this->data[$pos+9]) << 8;
						$weight = ord($this->data[$pos+10]) | ord($this->data[$pos+11]) << 8;
						$under  = ord($this->data[$pos+14]);
						$fontType  = ord($this->data[$pos+15]);
                                                if ($version == SPREADSHEET_EXCEL_READER_BIFF8) {
							$numchars = ord($this->data[$pos+18]);
							if ((ord($this->data[$pos+19])  & 1) == 0){
								$fontString = substr($this->data, $pos+20, $numchars);
							} else {
								$fontString = substr($this->data, $pos+20, $numchars*2);
							}
						} else {
							$numchars = ord($this->data[$pos+18]);
							$fontString = substr($this->data, $pos+19, $numchars*2);
						}
						$this->formatRecords['fonts'][] = array(
								'height' => $height / 20,
								'italic' => ($option & 2?1:0),
								'colour' => $colour,
								'under' => ($under==0?0:1),
								'bold' => ($weight==700?1:0),
                                'font' => $fontString,
								'fontType' => $fontType);
					    break;
				case SPREADSHEET_EXCEL_READER_TYPE_PALETTE:
						$colours = ord($this->data[$pos+4]) | ord($this->data[$pos+5]) << 8;
						for ($coli = 1; $coli < $colours; $coli++) {
						    $colOff = $pos + 4 + ($coli * 2);
  						    $colour = ord($this->data[$colOff]) | ord($this->data[$colOff+1]) << 8;
    						$this->formatRecords['colours'][0x07 + $coli] = '#' . dechex($colour);
						}
   						$this->formatRecords['colours'][0] = 'why hello there';
					    break;

				case SPREADSHEET_EXCEL_READER_TYPE_XF:
						$fontIndexCode = (ord($this->data[$pos+4]) | ord($this->data[$pos+5]) << 8) - 1;
						$fontIndexCode = max(0,$fontIndexCode);
						$indexCode = ord($this->data[$pos+6]) | ord($this->data[$pos+7]) << 8;
						$alignbit = ord($this->data[$pos+10]) & 3;

            
						$bgi = (ord($this->data[$pos+22]) | ord($this->data[$pos+23]) << 8) & 0x3FFF;
						$bgcolour = ($bgi & 0x7F);

						switch ($alignbit) {
						    case 3:
						        $align="right";
						        break;
						    case 2:
						        $align="center";
						        break;
						    case 0:
						    case 1:
						    default:
						        $align="";
						        break;
                        }						        
						if (array_key_exists($indexCode, $this->dateFormats)) {
							$this->formatRecords['xfrecords'][] = array(
									'type' => 'date',
									'format' => $this->dateFormats[$indexCode],
									'formatIndex' => $indexCode,
									'align' => $align,
									'fontIndex' => $fontIndexCode,
									'bgColour' => $bgcolour
									);
						}elseif (array_key_exists($indexCode, $this->numberFormats)) {
							$this->formatRecords['xfrecords'][] = array(
									'type' => 'number',
									'format' => $this->numberFormats[$indexCode],
									'formatIndex' => $indexCode,
									'align' => $align,
									'fontIndex' => $fontIndexCode,
									'bgColour' => $bgcolour
									);
						}else{
							$isdate = FALSE;
							$formatstr = '';
							if ($indexCode > 0){
								if (isset($this->formatRecords[$indexCode]))
									$formatstr = $this->formatRecords[$indexCode];
								if ($formatstr!="") {
									$tmp = preg_replace("/\;.*/","",$formatstr);
									$tmp = preg_replace("/^\[[^\]]*\]/","",$tmp);
									if (preg_match("/[^hmsday\/\-:\s\\\,AMP]/i", $tmp) == 0) { // found day and time format
										$isdate = TRUE;

										$formatstr = $tmp;
										$formatstr = str_replace('AM/PM', 'a', $formatstr);
										$formatstr = str_replace('mmmm', 'F', $formatstr);
										$formatstr = str_replace('mmm', 'M', $formatstr);

										// m/mm are used for both minutes and months - oh SNAP!
										// This mess tries to fix for that.
										// 'm' == minutes only if following h/hh or preceding s/ss
										$formatstr = preg_replace("/(h:?)mm?/","$1i", $formatstr);
										$formatstr = preg_replace("/mm?(:?s)/","i$1", $formatstr);
										// A single 'm' = n in PHP
										$formatstr = preg_replace("/(^|[^m])m([^m]|$)/", '$1n$2', $formatstr);
										$formatstr = preg_replace("/(^|[^m])m([^m]|$)/", '$1n$2', $formatstr);
										// else it's months
										$formatstr = str_replace('mm', 'm', $formatstr);

										// Convert single 'd' to 'j'
										$formatstr = preg_replace("/(^|[^d])d([^d]|$)/", '$1j$2', $formatstr);
										$formatstr = str_replace('dddd', 'l', $formatstr);
										$formatstr = str_replace('ddd', 'D', $formatstr);
										$formatstr = str_replace('dd', 'd', $formatstr);

										$formatstr = str_replace('yyyy', 'Y', $formatstr);
										$formatstr = str_replace('yy', 'y', $formatstr);
										$formatstr = str_replace('hh', 'H', $formatstr);
										$formatstr = str_replace('h', 'g', $formatstr);
										$formatstr = preg_replace("/ss?/", 's', $formatstr);
										//echo "\ndate-time $formatstr \n";
									}
								}
							}
							if ($isdate){
								$this->formatRecords['xfrecords'][] = array(
										'type' => 'date',
										'format' => $formatstr,
										'formatIndex' => $indexCode,
									    'align' => $align,
									    'fontIndex' => $fontIndexCode,
									    'bgColour' => $bgcolour
										);
							}else{
								$this->formatRecords['xfrecords'][] = array(
										'type' => 'other',
										'format' => $formatstr,
										'formatIndex' => $indexCode,
										'code' => $indexCode,
									    'align' => $align,
									    'fontIndex' => $fontIndexCode,
									    'bgColour' => $bgcolour
										);
							}
						}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_NINETEENFOUR:
					$this->nineteenFour = (ord($this->data[$pos+4]) == 1);
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_BOUNDSHEET:
						$rec_offset = $this->_GetInt4d($this->data, $pos+4);
						$rec_typeFlag = ord($this->data[$pos+8]);
						$rec_visibilityFlag = ord($this->data[$pos+9]);
						$rec_length = ord($this->data[$pos+10]);

						if ($version == SPREADSHEET_EXCEL_READER_BIFF8){
							$chartype =  ord($this->data[$pos+11]);
							if ($chartype == 0){
								$rec_name	= substr($this->data, $pos+12, $rec_length);
							} else {
								$rec_name	= $this->_encodeUTF16(substr($this->data, $pos+12, $rec_length*2));
							}
						}elseif ($version == SPREADSHEET_EXCEL_READER_BIFF7){
								$rec_name	= substr($this->data, $pos+11, $rec_length);
						}
					$this->boundsheets[] = array('name'=>$rec_name,
												 'offset'=>$rec_offset);

					break;

			}

			$pos += $length + 4;
			$code = ord($this->data[$pos]) | ord($this->data[$pos+1])<<8;
			$length = ord($this->data[$pos+2]) | ord($this->data[$pos+3])<<8;

			//$r = &$this->nextRecord();
			//echo "1 Code = ".base_convert($r['code'],10,16)."\n";
		}

		foreach ($this->boundsheets as $key=>$val){
			$this->sn = $key;
			$this->_parsesheet($val['offset']);
		}
		return true;

	}

	/**
	 * Parse a worksheet
	 *
	 * @access private
	 * @param todo
	 * @todo fix return codes
	 */
	function _parsesheet($spos) {

		$cont = true;
		// read BOF
		$code = ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
		$length = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;

		$version = ord($this->data[$spos + 4]) | ord($this->data[$spos + 5])<<8;
		$substreamType = ord($this->data[$spos + 6]) | ord($this->data[$spos + 7])<<8;

		if (($version != SPREADSHEET_EXCEL_READER_BIFF8) && ($version != SPREADSHEET_EXCEL_READER_BIFF7)) {
			return -1;
		}

		if ($substreamType != SPREADSHEET_EXCEL_READER_WORKSHEET){
			return -2;
		}
		$spos += $length + 4;
		while($cont) {
		
			$lowcode = ord($this->data[$spos]);
			if ($lowcode == SPREADSHEET_EXCEL_READER_TYPE_EOF) break;
			$code = $lowcode | ord($this->data[$spos+1])<<8;
			$length = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;
			$spos += 4;
			$this->sheets[$this->sn]['maxrow'] = $this->_rowoffset - 1;
			$this->sheets[$this->sn]['maxcol'] = $this->_coloffset - 1;
			unset($this->rectype);
			switch ($code) {
				case SPREADSHEET_EXCEL_READER_TYPE_DIMENSION:
					if (!isset($this->numRows)) {
						if (($length == 10) ||  ($version == SPREADSHEET_EXCEL_READER_BIFF7)){
							$this->sheets[$this->sn]['numRows'] = ord($this->data[$spos+2]) | ord($this->data[$spos+3]) << 8;
							$this->sheets[$this->sn]['numCols'] = ord($this->data[$spos+6]) | ord($this->data[$spos+7]) << 8;
						} else {
							$this->sheets[$this->sn]['numRows'] = ord($this->data[$spos+4]) | ord($this->data[$spos+5]) << 8;
							$this->sheets[$this->sn]['numCols'] = ord($this->data[$spos+10]) | ord($this->data[$spos+11]) << 8;
						}
					}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_MERGEDCELLS:
					$cellRanges = ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
					for ($i = 0; $i < $cellRanges; $i++) {
						$fr =  ord($this->data[$spos + 8*$i + 2]) | ord($this->data[$spos + 8*$i + 3])<<8;
						$lr =  ord($this->data[$spos + 8*$i + 4]) | ord($this->data[$spos + 8*$i + 5])<<8;
						$fc =  ord($this->data[$spos + 8*$i + 6]) | ord($this->data[$spos + 8*$i + 7])<<8;
						$lc =  ord($this->data[$spos + 8*$i + 8]) | ord($this->data[$spos + 8*$i + 9])<<8;
						if ($lr - $fr > 0) {
							$this->sheets[$this->sn]['cellsInfo'][$fr+1][$fc+1]['rowspan'] = $lr - $fr + 1;
						}
						if ($lc - $fc > 0) {
							$this->sheets[$this->sn]['cellsInfo'][$fr+1][$fc+1]['colspan'] = $lc - $fc + 1;
						}
					}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_RK:
				case SPREADSHEET_EXCEL_READER_TYPE_RK2:
					$row = ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
					$column = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;
					$xfindex	= ord($this->data[$spos+4]) | ord($this->data[$spos+5])<<8;
					$rknum = $this->_GetInt4d($this->data, $spos + 6);
					$numValue = $this->_GetIEEE754($rknum);
					
					list($string,$raw,$type,$format,$formatIndex) = $this->_getCellDetails($spos,$numValue,$column);
					$this->addcell($row, $column, $string, $raw,$type,$format,$xfindex);
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_LABELSST:
					$row		= ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
					$column	 = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;
					$xfindex	= ord($this->data[$spos+4]) | ord($this->data[$spos+5])<<8;
					$index  = $this->_GetInt4d($this->data, $spos + 6);
					$this->addcell($row, $column, $this->sst[$index], "", "", "", $xfindex);
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_MULRK:
					$row		= ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
					$colFirst   = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;
					$colLast	= ord($this->data[$spos + $length - 2]) | ord($this->data[$spos + $length - 1])<<8;
					$columns	= $colLast - $colFirst + 1;
					$tmppos = $spos+4;
					for ($i = 0; $i < $columns; $i++) {
						$numValue = $this->_GetIEEE754($this->_GetInt4d($this->data, $tmppos + 2));
						list($string,$raw,$type,$format,$formatIndex) = $this->_getCellDetails($tmppos-4,$numValue,$colFirst + $i + 1);
						$tmppos += 6;
						$this->addcell($row, $colFirst + $i, $string, $raw,$type,$format,$formatIndex);
					}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_NUMBER:
					$row	= ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
					$column = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;
					$xfindex	= ord($this->data[$spos+4]) | ord($this->data[$spos+5])<<8;
					$tmp = unpack("ddouble", substr($this->data, $spos + 6, 8)); // It machine machine dependent
					if ($this->isDate($spos)) {
						$numValue = $tmp['double'];
					}
					else {
						$numValue = $this->createNumber($spos);
					}
					list($string,$raw,$type,$format,$formatIndex) = $this->_getCellDetails($spos,$numValue,$column);
					$this->addcell($row, $column, $string, $raw,$type,$format,$xfindex);
					break;

				case SPREADSHEET_EXCEL_READER_TYPE_FORMULA:
				case SPREADSHEET_EXCEL_READER_TYPE_FORMULA2:
					$row	= ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
					$column = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;
					$xfindex	= ord($this->data[$spos+4]) | ord($this->data[$spos+5])<<8;
					if ((ord($this->data[$spos+6])==0) && (ord($this->data[$spos+12])==255) && (ord($this->data[$spos+13])==255)) {
						//String formula. Result follows in a STRING record
						//echo "FORMULA $row $column Formula with a string<br>\n";
					} elseif ((ord($this->data[$spos+6])==1) && (ord($this->data[$spos+12])==255) && (ord($this->data[$spos+13])==255)) {
						//Boolean formula. Result is in +2; 0=false,1=true
					} elseif ((ord($this->data[$spos+6])==2) && (ord($this->data[$spos+12])==255) && (ord($this->data[$spos+13])==255)) {
						//Error formula. Error code is in +2;
					} elseif ((ord($this->data[$spos+6])==3) && (ord($this->data[$spos+12])==255) && (ord($this->data[$spos+13])==255)) {
						//Formula result is a null string.
					} else {
						// result is a number, so first 14 bytes are just like a _NUMBER record
						$tmp = unpack("ddouble", substr($this->data, $spos + 6, 8)); // It machine machine dependent
							  if ($this->isDate($spos)) {
								$numValue = $tmp['double'];
							  }
							  else {
								$numValue = $this->createNumber($spos);
							  }
						list($string,$raw,$type,$format,$formatIndex) = $this->_getCellDetails($spos,$numValue,$column);
						$this->addcell($row, $column, $string, $raw,$type,$format,$xfindex);
					}
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_BOOLERR:
					$row	= ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
					$column = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;
					$string = ord($this->data[$spos+6]);
					$this->addcell($row, $column, $string);
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_ROW:
				case SPREADSHEET_EXCEL_READER_TYPE_DBCELL:
				case SPREADSHEET_EXCEL_READER_TYPE_MULBLANK:
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_LABEL:
					$row	= ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
					$column = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;
					$this->addcell($row, $column, substr($this->data, $spos + 8, ord($this->data[$spos + 6]) | ord($this->data[$spos + 7])<<8));
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_EOF:
					$cont = false;
					break;
				case SPREADSHEET_EXCEL_READER_TYPE_HYPER:
				//  Only handle hyperlinks to a URL
					$row	= ord($this->data[$spos]) | ord($this->data[$spos+1])<<8;
					$row2   = ord($this->data[$spos+2]) | ord($this->data[$spos+3])<<8;
					$column = ord($this->data[$spos+4]) | ord($this->data[$spos+5])<<8;
					$column2 = ord($this->data[$spos+6]) | ord($this->data[$spos+7])<<8;
				    $linkdata = Array();
				    $flags = ord($this->data[$spos + 28]);
					$udesc = "";
					$ulink = "";
					$uloc = 32;
					$linkdata['flags'] = $flags;
					if (($flags & 1) > 0 ) {   // is a type we understand
					    //  is there a description ?
						if (($flags & 0x14) == 0x14 ) {   // has a description
							$uloc += 4;
							$descLen = ord($this->data[$spos + 32]) | ord($this->data[$spos + 33]) << 8;
							$udesc = substr($this->data, $spos + $uloc, $descLen * 2);
							$uloc += 2 * $descLen;
						}
						$ulink = $this->read16bitstring($this->data, $spos + $uloc + 20);
					}
					$linkdata['raw'] = $this->dumpHexData($this->data, $spos, $length, $code);
					$linkdata['desc'] = $udesc;
					$linkdata['link'] = $ulink;
					$linkdata['rowfrom'] = $row;
					$linkdata['rowto'] = $row2;
					$linkdata['colfrom'] = $column;
					$linkdata['colto'] = $column2;
					$this->sheets[$this->sn]['hyperlink'][] = $linkdata;
					break;
				    
				default:
					break;
			}
			$spos += $length;
		}

		if (!isset($this->sheets[$this->sn]['numRows']))
			 $this->sheets[$this->sn]['numRows'] = $this->sheets[$this->sn]['maxrow'];
		if (!isset($this->sheets[$this->sn]['numCols']))
			 $this->sheets[$this->sn]['numCols'] = $this->sheets[$this->sn]['maxcol'];

	}


		function isDate($spos) {
			$xfindex = ord($this->data[$spos+4]) | ord($this->data[$spos+5]) << 8;
			if ($this->formatRecords['xfrecords'][$xfindex]['type'] == 'date') {
				return true;
			}
			return false;
		}

		// Get the type, value, format, etc of a cell
		function _getCellDetails($spos,$numValue,$column) {
			$xfindex = ord($this->data[$spos+4]) | ord($this->data[$spos+5]) << 8;
			$type = $this->formatRecords['xfrecords'][$xfindex]['type'];

			$format = '';
			$formatIndex = '';
			$rectype = '';
			$string = '';
			$raw = '';

			if (isset($this->_columnsFormat[$column + 1])){
				$format = $this->_columnsFormat[$column + 1];
			}

			if ($type == 'date') {
				// See http://groups.google.com/group/php-excel-reader-discuss/browse_frm/thread/9c3f9790d12d8e10/f2045c2369ac79de
				$format = $this->formatRecords['xfrecords'][$xfindex]['format'];
				$formatIndex = $this->formatRecords['xfrecords'][$xfindex]['formatIndex'];
				$rectype = 'date';
				// Convert numeric value into a date
				$utcDays = floor($numValue - ($this->nineteenFour ? SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS1904 : SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS));
				$utcValue = $utcDays * SPREADSHEET_EXCEL_READER_MSINADAY;
				$dateinfo = getdate($utcValue);

				$raw = $numValue;
				$fractionalDay = $numValue - floor($numValue) + .0000001; // The .0000001 is to fix for php/excel fractional diffs

				$totalseconds = floor(SPREADSHEET_EXCEL_READER_MSINADAY * $fractionalDay);
				$secs = $totalseconds % 60;
				$totalseconds -= $secs;
				$hours = floor($totalseconds / (60 * 60));
				$mins = floor($totalseconds / 60) % 60;
				$string = date ($format, mktime($hours, $mins, $secs, $dateinfo["mon"], $dateinfo["mday"], $dateinfo["year"]));
			} else if ($type == 'number') {
				$format = $this->formatRecords['xfrecords'][$xfindex]['format'];
				$formatIndex = $this->formatRecords['xfrecords'][$xfindex]['formatIndex'];
				$rectype = 'number';
				$string = $this->_format_value($format, $numValue, $formatIndex);
				$raw = $numValue;
			} else{
				$formatIndex = $this->formatRecords['xfrecords'][$xfindex]['formatIndex'];
				$format = $this->formatRecords['xfrecords'][$xfindex]['format'];
				if ($format=="") {
					$format = $this->_defaultFormat;
				}
				$rectype = 'unknown';
				$string = $this->_format_value($format, $numValue, $formatIndex);
				$raw = $numValue;
			}

			return array($string,$raw,$rectype,$format,$formatIndex);

		}


	function createNumber($spos) {
		$rknumhigh = $this->_GetInt4d($this->data, $spos + 10);
		$rknumlow = $this->_GetInt4d($this->data, $spos + 6);
		$sign = ($rknumhigh & 0x80000000) >> 31;
		$exp =  ($rknumhigh & 0x7ff00000) >> 20;
		$mantissa = (0x100000 | ($rknumhigh & 0x000fffff));
		$mantissalow1 = ($rknumlow & 0x80000000) >> 31;
		$mantissalow2 = ($rknumlow & 0x7fffffff);
		$value = $mantissa / pow( 2 , (20- ($exp - 1023)));
		if ($mantissalow1 != 0) $value += 1 / pow (2 , (21 - ($exp - 1023)));
		$value += $mantissalow2 / pow (2 , (52 - ($exp - 1023)));
		if ($sign) {$value = -1 * $value;}
		return  $value;
	}

	function addcell($row, $col, $string, $raw = '', $type='', $format='', $formatIndex='') {
		$this->sheets[$this->sn]['maxrow'] = max($this->sheets[$this->sn]['maxrow'], $row + $this->_rowoffset);
		$this->sheets[$this->sn]['maxcol'] = max($this->sheets[$this->sn]['maxcol'], $col + $this->_coloffset);
		$this->sheets[$this->sn]['cells'][$row + $this->_rowoffset][$col + $this->_coloffset] = $string;
		$this->sheets[$this->sn]['cellsInfo'][$row + $this->_rowoffset][$col + $this->_coloffset]['raw'] = $raw;
		$this->sheets[$this->sn]['cellsInfo'][$row + $this->_rowoffset][$col + $this->_coloffset]['type'] = $type;
		$this->sheets[$this->sn]['cellsInfo'][$row + $this->_rowoffset][$col + $this->_coloffset]['format'] = $format;
		$this->sheets[$this->sn]['cellsInfo'][$row + $this->_rowoffset][$col + $this->_coloffset]['formatIndex'] = $formatIndex;
	}


	function _GetIEEE754($rknum)
	{
		if (($rknum & 0x02) != 0) {
				$value = $rknum >> 2;
		} else {
//mmp
// first comment out the previously existing 7 lines of code here
//				$tmp = unpack("d", pack("VV", 0, ($rknum & 0xfffffffc)));
//				//$value = $tmp[''];
//				if (array_key_exists(1, $tmp)) {
//					$value = $tmp[1];
//				} else {
//					$value = $tmp[''];
//				}
// I got my info on IEEE754 encoding from
// http://research.microsoft.com/~hollasch/cgindex/coding/ieeefloat.html
// The RK format calls for using only the most significant 30 bits of the
// 64 bit floating point value. The other 34 bits are assumed to be 0
// So, we use the upper 30 bits of $rknum as follows...
		 $sign = ($rknum & 0x80000000) >> 31;
		$exp = ($rknum & 0x7ff00000) >> 20;
		$mantissa = (0x100000 | ($rknum & 0x000ffffc));
		$value = $mantissa / pow( 2 , (20- ($exp - 1023)));
		if ($sign) {$value = -1 * $value;}
//end of changes by mmp

		}

		if (($rknum & 0x01) != 0) {
			$value /= 100;
		}
		return $value;
	}

	function _encodeUTF16($string)
	{
		$result = $string;
		if ($this->_defaultEncoding){
			switch ($this->_encoderFunction){
				case 'iconv' :	 $result = iconv('UTF-16LE', $this->_defaultEncoding, $string);
								break;
				case 'mb_convert_encoding' :	 $result = mb_convert_encoding($string, $this->_defaultEncoding, 'UTF-16LE' );
								break;
			}
		}
		return $result;
	}

	function _GetInt4d($data, $pos)
	{
		$value = ord($data[$pos]) | (ord($data[$pos+1]) << 8) | (ord($data[$pos+2]) << 16) | (ord($data[$pos+3]) << 24);
		if ($value>=4294967294)
		{
			$value=-2;
		}
		return $value;
	}

}

?>
