<?php
define('IN_ECS', true);

require_once('includes/tcpdf/config/lang/eng.php');
require_once('includes/tcpdf/mbtcpdf.php');

//require(dirname(__FILE__) . '/includes/lib_solution_operate.php');
require(dirname(__FILE__) . '/includes/init.php');

// create new PDF document
$pdf = new MBTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true); 

//initialize document
$pdf->AliasNbPages();

// add a page
$pdf->AddPage();

$pdf->SetFont('SimHei','U',12);


$data = $_REQUEST['data'];
$order_id = $_REQUEST['order_id'];
/**/
$array = get_step_list($order_id);
$step_list =$array['orders'];
$pdf->Cell(0,10,"设备买卖合同",1,1,'C');
$pdf->Ln();

foreach($step_list AS $key => $val){
	$pdf->Cell(0,10,$val['step_id']."=>".$val['goods_name']."=>".$val['step_price'],0,1,'C');
	$pdf->Ln();
}

//Close and output PDF document

$pdf->Output();
?>