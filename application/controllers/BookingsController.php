<?php

class BookingsController extends Swift_Controller_Action
{

    public function init ()
    {}

    /**
     * Page
     */
    public function bookingsAction ()
    {
	
		//$writer = new Zend_Log_Writer_Stream($_SERVER["DOCUMENT_ROOT"].'/../logs/log.txt');
        //$logger = new Zend_Log($writer);
        
		//$logger->info($_SERVER["DOCUMENT_ROOT"]);
        //$logger->info('Informational message');
        
        $bookingsModel = new Bookings();
        
        $this->view->statuses = $bookingsModel->getStatuses();
        //$logger->info('Informational message 2');
        $status = $this->getRequest()->getParam('status', false);
        $this->view->status = $status;
        //$logger->info('1');
        //$logger->info($this->_me->id);
        
        try {
            
            if (@$this->_me->type == 9) {
                //$logger->info('me is 9');
                $bookings = $bookingsModel->getAll($status);
            } else {
                //$logger->info('me is not 9');
                $bookings = $bookingsModel->getAllByUser($this->_me->id,$status);
            }
        } catch (Exception $e) {
            //$logger->info($e);
        }
        
        $page = $this->_getParam('page', 1);
        
        $paginator = Zend_Paginator::factory($bookings);
        $paginator->setItemCountPerPage(20);
        $paginator->setCurrentPageNumber($page);
        
        $this->view->paginator = $paginator;
    }

    
    
    public function pdfioAction ()
    {
        error_reporting(0);
        $id = $this->getRequest()->getParam('id');
        $origId = $this->view->bookingId($id);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $type = $this->getRequest()->getParam('type');
        $type = strtoupper($type);
        
        $bookingsModel = new Bookings();
        $lineItemsModel = new LineItems();
        $booking = $bookingsModel->getById($id);
        
        $lineItems = $lineItemsModel->getAllByOrder($id);
        
        require_once (APPLICATION_PATH . "/../data/dompdf/dompdf_config.inc.php");
        
        if ($type == 'IO') {
            $title = "Insertion Order";
        }
        if ($type == 'PO') {
            $title = "Print Purchase Order Worksheet";
        }
        
        $isRateCard = $lineItemsModel->isOrderRateCard($id);
$html="";
$html.="
		
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'
'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
<head>
<style>
body {
  margin: 18pt 18pt 24pt 18pt;
}

* {
  font-family: helvetica,georgia,serif;
  font-weight: bold;
}

p {
  text-align: justify;
  font-size: 1em;
  margin: 0.5em;
  padding: 10px;
}
</style>
</head>
<body>";

$html.="
        <script type='text/php'>
        if ( isset($pdf) ) {
        $font = Font_Metrics::get_font('helvetica', 'bold');
        $pdf->page_text(72, 18, 'Header: {PAGE_NUM} of {PAGE_COUNT}', $font, 6, array(0,0,0));
        
        }
        </script> 

";
        $html .= "
		<div style='font-family:Arial, sans-serif;'>
			<!-- HEADER -->
			<table width='100%'>
				<tr>
					<td style='text-align:left;'><img src='" .
                 APPLICATION_PATH .
                 "/../public/images/MBIlogo.jpg'></td>
					<td valign='bottom' style='text-align:center; width:220px;'>
						
								<div style='text-align:center; padding:5px;border-right:1px solid #000; border-left:1px solid #000; '><h2 style='margin:0; padding:0;'>$title</h2></div>
						
								<div style='text-align:center;border-right:1px solid #000; border-left:1px solid #000; padding:5px;'><h2 style='margin:0; padding:0;'># " .
                 $origId .
                 "</h2></div>
					
					</td>
					<td style='text-align:right; font-size:.8em;' width='290px'>
						<h3 style='margin:0; padding:0;'>Media Brokers International, Inc.</h3>
						11720 Amberpark Drive, Suite 600<br>
						Alpharetta, Georgia 30009<br>
						Phone: 678.514.6200<br>
						<table style='width:100%;'>
							<tr>
								<td style='text-align:left;padding-left:15px; font-weight:bold;'>Date: " .
                 date('m/d/Y', strtotime($booking->date)) . "</td>
								<td style='text-align:right;'>Fax: 678.514.6299</td>
							</tr>
						</table>
						
					</td>
				</tr>
			</table>
			<div style='position:relative; top:-5px; border-top:2px solid #000; '>
			</div>
			<!-- END HEADER -->
			<!-- INFO -->
			<br>
			<table width='100%' style='font-size:.8em;'>
				<tr>
					<td style='width:50%;' valign='top'>
						<b><u>Product / Advertiser</u></b><br>
						" . $booking->product_advertiser . "<br><br>
						
						<b><u>Client / Bill To</u></b><br>
						" . $booking->client_company_name . "<br>
						" . $booking->client_street_1;
        if (trim($booking->client_street_2) != '') {
            $html .= ", ";
        }
        $html .= $booking->client_street_2;
        if (trim($booking->client_street_3) != '') {
            $html .= ", ";
        }
        $html .= $booking->client_street_3 .
                 "<br>
						" .
                 $booking->client_city . ", " . $booking->client_state . " " .
                 $booking->client_zip . "<br>
						<br>" . $booking->client_country . "<br>
						<b><u>Client Payment Contact Name, Phone / Fax</u></b><br>
						" .
                 "ATTN: " . $booking->client_primary_contact . "<br>
						" . $booking->client_phone . "<br>
		
					</td>
					<td style='padding-left:5px; border-left:1px solid #000;' valign='top'>
						<b><u>Media Name</u></b><br>
						" . $booking->media_name . "<br><br>
						<b><u>Circulation / Rate base</u></b><br>
						" .
                 nl2br($booking->circulation_rate_base) . "<br><br>
						<b><u>Ad Specs</u></b><br>" ;
						
						$i = 1;
						foreach ($lineItems as $lineItem) {
							if(trim($lineItem->ad_spec_color) <> '' || trim($lineItem->ad_spec_bleed) <> '' || trim($lineItem->ad_spec_size) <> '' || trim($lineItem->ad_spec_position) <> '')
							{
								$html	.=  $i++ . ".  " .$lineItem->ad_spec_color . " , " . $lineItem->ad_spec_bleed . " , " . $lineItem->ad_spec_size . " , " . $lineItem->ad_spec_position . "<br>";
								
							}
						}
						
					
					$html .= "<br></td>
				</tr>	
			</table>
			<!-- END INFO -->
			<!-- LINE ITEMS -->
			<table style='border-collapse:collapse; width:100%; font-size:.7em;' border=1>
				<tr style='font-weight:bold; text-align:center;'>
					<td>Line</td>
					<td style='text-align:left; padding-left:4px;'>Issue / Cover</td>
					<td>On-Sale</td>
					<td>Space Close</td>
					<td>Materials Due</td>";
        if ($isRateCard) {
            $html .= "<td>Rate Card</td>";
        }
        $html .= "<td>Payment Due</td>
					<td style='text-align:right; padding-right:4px;'>MBI Rate</td>
				</tr>";
        $i = 1;
        $totalPayment = 0;
        $rateCardTotal = 0;
        foreach ($lineItems as $lineItem) {
            
            if ($i != 12) {
                $style = "style='border-bottom:0;border-top:0;'";
            } else {
                $style = "style='border-top:0;'";
            }
            $payment = (float) str_replace(",", "", $lineItem->payment_amount);
            $totalPayment = $totalPayment + $payment;
            $html .= "
						<tr style='text-align:center;'>
							<td $style>" . $i .
                     "</td>
							<td $style><div style='text-align:left;'>" .
                     $lineItem->issue_cover . "</div></td>
							<td $style>" .
                     date('m/d/Y', strtotime($lineItem->on_sale)) . "</td>
							<td $style>" .
                     date('m/d/Y', strtotime($lineItem->space_close)) . "</td>
							<td $style>" .
                     date('m/d/Y', strtotime($lineItem->materials_due)) . "</td>";
            if ($isRateCard) {
                $html .= "<td $style>" . $lineItem->rate_card . "</td>";
                $rateCardTotal = $rateCardTotal +
                         str_replace(",", "", 
                                str_replace('$', '', $lineItem->rate_card));
            }
            $html .= "<td $style>" .
                     date('m/d/Y', strtotime($lineItem->payment_due)) . "</td>
							<td $style>" .
                     number_format($payment, 2) . "</td>
						</tr>
					";
            
            $i ++;
        }
        while ($i < 13) {
            if ($i != 12) {
                $style = "style='border-bottom:0;border-top:0;'";
            } else {
                $style = "style='border-top:0;'";
            }
            $html .= "
						<tr style='text-align:center;'>
							<td $style>" . $i . "</td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>";
            if ($isRateCard) {
                $html .= "<td $style></td>";
            }
            $html .= "<td $style></td>
							<td $style></td>
						</tr>
					";
            $i ++;
        }
        
        $html .= "<tr>";
        if ($isRateCard) {
            $number = 1;
            $html .= "<td colspan='5' style='text-align:right;border-right:1px solid #000;'><b>Rate Card (Total):</b></td>
								<td style='text-align:right;border-right:1px solid #000;'><b>\$" .
                     number_format($rateCardTotal, 2) . "</b></td>
								";
        } else {
            
            $number = 6;
        }
        $html .= "<td colspan='$number' style='text-align:right; border-right:1px solid #000;'><b>MBI Rate (Total):</b></td><td style='text-align:right;'><b>USD" .
                 number_format($totalPayment, 2) . "</b></td></tr></table>";
        $html .= "</table>
			<!-- END LINE ITEMS -->
			<!-- TEXT BOXES -->
			<div style='font-size:.8em;'>
				<b><u>Disposition of Materials</u></b><br>
				Materials for each individual Insertion listed above to be sent directly from Client to Publisher, to arrive by the Materials Due Date listed above.<br>
				";
        $spec_instr_io_txtarea=!isset($booking->spec_instr_io_txtarea)?'-':$booking->spec_instr_io_txtarea;
        
        $html .= "<br><br><b><u>Placement / Payment / Special Instructions</u></b><br>
				Payment for each individual insertion listed above to be sent directly to MBI at the address above, and should arrive no later than the corresponding payment due date listed above.<br><br><b>Special Instructions:</b>".$spec_instr_io_txtarea . "<br>"; 
        $usersModel = new Users();
        $salesRep = $usersModel->getUser($booking->sales_rep);
        
        $html .= "
			<!-- END TEXT BOXES -->
			<br><br>
				<table width='100%'>
					<tr>
						<td>
						<span style='font-size:9px;'>Page 1 of 1</span>
						</td>
						<td style='text-align:right;'>
							<b><u>Total (net): USD " .
                 number_format($totalPayment, 2) . "</u></b>
						</td>
					</tr>
				</table>
				<table style='padding-top:20px; width:100%'>
					<tr>
						<td style='width:60%;'>
							<u>Client Authorized Approval: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp;</u>
						</td>
						<td><u>MBI Representative: " . $salesRep->name . "</u></td>
					</tr>
				</table>
				<div style='font-size:8px;'>
					The submission of an insertion order constitutes acceptance of our terms of payment and the advertising agency and/or the advertiser are jointly and severally liable for payment of all advertising invoiced. Please be advised that we do not accept disclaimers, and \"sequential liability\" is NOT acceptable to MBI. Positioning of advertising is at the sole discretion of the publisher. MBI requests positioning from time to time but cannot guarantee special positioning of advertising. MBI does not guarantee results from ads we place on your behalf and assumes no liability for ad content. This Agreement constitutes the entire agreement between the parties with respect to the subject matter hereof and supersedes all prior negotiations, representations or contracts. No written or oral agreements, representations, statements, negotiations, understandings or discussions that are not set out, referenced or specifically incorporated in this Agreement shall in any way be binding or of effect between the parties. All sales are final - no cancellations accepted. This contract shall be governed by Georgia law and venue for purposes of any claim or action arising in any way from this contract shall be in the Superior Court of Fulton County, Georgia. If paying by credit card a 5% Administrative Fee will apply. Default: in the event of any breach and/or default of any obligations to MBI, either existing or arising in the future by the debtor, MBI shall be entitled to recover from the debtor, in addition to all other damages, all costs and expenses, including court costs, reasonable attorney's feed and interest at the maximum rate provided by law. Invoices unpaid after thirty days subject to a 1.5% finance charge per month.
				</div>

			</div>
			
		</div>
		";
 $html .="</body> </html>";       
        
        $dompdf = new DOMPDF();        
        $dompdf->load_html($html);
        $dompdf->render();
//         $canvas = $dompdf->get_canvas();
//         $font = Font_Metrics::get_font("helvetica", "bold");
//         $canvas->page_text(16, 800, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));
        
        $dompdf->stream(
                str_replace(" ", "_", $title) . "_" .
                         $booking->client_company_name . "_" .
                         $booking->media_company_name . "_" .
                         date('Y-m-d', strtotime($booking->date)) . ".pdf");
//         $dompdf->stream("sample.pdf",array("Attachment"=>0));
        
    }
    
    public function pdfiopoAction ()
    {
        error_reporting(0);
        $id = $this->getRequest()->getParam('id');
        $origId = $this->view->bookingId($id);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $type = $this->getRequest()->getParam('type');
        $type = strtoupper($type);
        
        $bookingsModel = new Bookings();
        $lineItemsModel = new LineItems();
        $booking = $bookingsModel->getById($id);
        
        $lineItems = $lineItemsModel->getAllByOrder($id);
        
        require_once (APPLICATION_PATH . "/../data/dompdf/dompdf_config.inc.php");
        
        if ($type == 'IO') {
            $title = "Insertion Order";
        }
        if ($type == 'PO') {
            $title = "Print Purchase Order Worksheet";
        }
        if ($type == 'IOPO'   ) {
            $title = "IO# ".$origId. " Commissions " . "(For Internal Use Only)";
	  // $title = "(For Internal Use Only)";
        }
        
        $isRateCard = $lineItemsModel->isOrderRateCard($id);
        
        $html = "
		<div style='font-family:Arial, sans-serif;'>
			<!-- HEADER -->
			<table width='100%'>
				<tr>
					<td style='text-align:left;'><img src='" .
                 APPLICATION_PATH .
                 "/../public/images/MBIlogo.jpg'></td>
					<td valign='bottom' style='text-align:center; width:220px;'>
						
								<div style='text-align:center; padding:5px;border-right:1px solid #000; border-left:1px solid #000; '><h2 style='margin:0; padding:0;'>$title</h2></div>
						
								<div style='text-align:center;border-right:1px solid #000; border-left:1px solid #000; padding:5px;'><h2 style='margin:0; padding:0;'></h2></div>
					
					</td>
					<td style='text-align:right; font-size:.8em;' width='290px'>
						<h3 style='margin:0; padding:0;'>Media Brokers International, Inc.</h3>
						11720 Amberpark Drive, Suite 600<br>
						Alpharetta, Georgia 30009<br>
						Phone: 678.514.6200<br>
						<table style='width:100%;'>
							<tr>
								<td style='text-align:left;padding-left:15px; font-weight:bold;'>Date: " .
                 date('m/d/Y', strtotime($booking->date)) . "</td>
								<td style='text-align:right;'>Fax: 678.514.6299</td>
							</tr>
						</table>
						
					</td>
				</tr>
			</table>
			<div style='position:relative; top:-5px; border-top:2px solid #000; '>
			</div>
			<!-- END HEADER -->
			<!-- INFO -->
			<br>
			<br>
			<br>
			<table width='100%' style='font-size:.8em;'>
				<tr>
					<td style='width:50%;' valign='top'>
						<b><u>Product / Advertiser</u></b><br>
						" . $booking->product_advertiser . "<br><br>
						
						<b><u>Client / Bill To</u></b><br>
						" . $booking->client_company_name . "<br>
						" . $booking->client_street_1;
        if (trim($booking->client_street_2) != '') {
            $html .= ", ";
        }
        $html .= $booking->client_street_2;
        if (trim($booking->client_street_3) != '') {
            $html .= ", ";
        }
        $html .= $booking->client_street_3 .
                 "<br>
						" .
                 $booking->client_city . ", " . $booking->client_state . " " .
                 $booking->client_zip . "<br>
						<br>" . $booking->client_country . "<br>
						<b><u>Client Payment Contact Name, Phone / Fax</u></b><br>
						" .
                 "ATTN: " . $booking->client_primary_contact . "<br>
						" . $booking->client_phone . "<br>
		
					</td>
					<td style='padding-left:5px; border-left:1px solid #000;' valign='top'>
						<b><u>Media Name</u></b><br>
						" . $booking->media_name . "<br><br>
						<b><u>Circulation / Rate base</u></b><br>
						" .
                 nl2br($booking->circulation_rate_base) . "<br><br>
						<b><u>Ad Specs</u></b><br>" ;
						
						$i = 1;
						foreach ($lineItems as $lineItem) {
							if(trim($lineItem->ad_spec_color) <> '' || trim($lineItem->ad_spec_bleed) <> '' || trim($lineItem->ad_spec_size) <> '' || trim($lineItem->ad_spec_position) <> '')
							{
								$html	.=  $i++ . ".  " .$lineItem->ad_spec_color . " , " . $lineItem->ad_spec_bleed . " , " . $lineItem->ad_spec_size . " , " . $lineItem->ad_spec_position . "<br>";
								
							}
						}
						
					
					$html .= "<br></td>
				</tr>	
			</table>
			<!-- END INFO -->
			<!-- LINE ITEMS -->
			<table style='border-collapse:collapse; width:100%; font-size:.7em;' border=1>
				<tr style='font-weight:bold; text-align:center;'>
					<td>Line</td>
					<td>Client Name</td>
					<td>IO Date</td>
					<td>IO Number</td>
					<td>IO Amount</td>
					<td>PO Amount</td>
					<td>Profit in $</td>
					<td>Sales Rep Name</td>
					<td>Base Comm %</td>
					<td>Base Comm Amt</td>
					<td>Bonus Comm Base Rate</td>
					<td>Bonus Comm %</td>
					<td>Bonus Comm Amt</td>
					<td>Total Comm</td>";
        $html .= "</tr>";
        $i = 1;
        $totalPayment = 0;
        $rateCardTotal = 0;
        foreach ($lineItems as $lineItem) {
            
            if ($i != 12) {
                $style = "style='border-bottom:0;border-top:0;'";
            } else {
                $style = "style='border-top:0;'";
            }
            $users = new Users();
            $clients = new Clients();
            $payment = (float) str_replace(",", "", $lineItem->payment_amount);
            $totalPayment = $totalPayment + $payment;
            $profit = $lineItem->payment_amount-$lineItem->mbi_rate;
	    $totcom = $lineItem->base_commission+$lineItem->sales_bonus;
            $html .= "
						<tr style='text-align:center;'>
							<td $style>" . $i ."</td>
							<td $style>".$clients->getById($booking->client_id)->primary_contact ."</td>
							<td $style>" .date('m/d/Y', strtotime($booking->date)) . "</td>
							<td $style>" . $origId . "</td>
							
							<td $style>" .number_format( $lineItem->payment_amount,2 ). "</td>
							<td $style>" .number_format($lineItem->mbi_rate,2) . "</td>
							<td $style>".number_format($profit,2)."</td>

							<td $style>" .$users->getUser($lineItem->sales_rep)->name . "</td>
							<td $style>" .$users->getUser($lineItem->sales_rep)->commission_rate . "</td>
							<td $style>" .number_format($lineItem->base_commission,2) . "</td>
							
							<td $style>.15</td>
							<td $style>.10</td>
							<td $style>" .number_format($lineItem->sales_bonus,2) . "</td>
							<td $style>" .number_format($totcom,2)."</td>";
            $html .= "</tr>";
            $i ++;
        }
        while ($i < 11) {
            if ($i != 10) {
                $style = "style='border-bottom:0;border-top:0;'";
            } else {
                $style = "style='border-top:0;'";
            }
            $html .= "
						<tr style='text-align:center;'>
							<td $style>" . $i . "</td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>";
            $html .= "</tr>";
            $i ++;
        }
        
        
/*        
        //Footer
        $html .= "<tr>";
        if ($isRateCard) {
            $number = 1;
            $html .= "<td colspan='5' style='text-align:right;border-right:1px solid #000;'><b>Rate Card (Total):</b></td>
								<td style='text-align:right;border-right:1px solid #000;'><b>\$" .
                     number_format($rateCardTotal, 2) . "</b></td>
								";
        } else {
            
            $number = 6;
        }
        $html .= "<td colspan='$number' style='text-align:right; border-right:1px solid #000;'><b>MBI Rate (Total):</b></td><td style='text-align:right;'><b>USD " .
                 number_format($totalPayment, 2) . "</b></td></tr></table>";
        //End of Footer
  */      
        
        //Disposition of Materials
//         $html .= "</table>
// 			<!-- END LINE ITEMS -->
// 			<!-- TEXT BOXES -->
// 			<div style='font-size:.8em;'>
// 				<b><u>Disposition of Materials</u></b><br>
// 				Materials for each individual Insertion listed above to be sent directly from Client to Publisher, to arrive by the Materials Due Date listed above.<br>
// 				";
        
//         $html .= "<br><br><b><u>Placement / Payment / Special Instructions</u></b><br>
// 				Payment for each individual insertion listed above to be sent directly to MBI at the address above, and should arrive no later than the corresponding payment due date listed above.";
        $usersModel = new Users();
        $salesRep = $usersModel->getUser($booking->sales_rep);
        
        $html .= "
			<!-- END TEXT BOXES -->
			<br><br>
			<br><br>
			<br><br>
				<table style='padding-top:20px; width:100%'>
					<tr>
						<td style='width:60%;'>
							 <u>  </u>
						</td>
						<td><u>MBI Representative: " . $salesRep->name . "</u></td>
					</tr>
				</table>
			</div>
			
		</div>
		";
        
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->render();
        $dompdf->stream(
                str_replace(" ", "_", $title) . "_" .
                         $booking->client_company_name . "_" .
                         $booking->media_company_name . "_" .
                         date('Y-m-d', strtotime($booking->date)) . ".pdf");
    }

    function nl2li ($str, $ordered = 0, $type = "1")
    {
        
        // check if its ordered or unordered list, set tag accordingly
        if ($ordered) {
            $tag = "ol";
            // specify the type
            $tag_type = "type=$type";
        } else {
            $tag = "ul";
            // set $type as NULL
            $tag_type = NULL;
        }
        
        // add ul / ol tag
        // add tag type
        // add first list item starting tag
        // add last list item ending tag
        $str = "<$tag $tag_type><li>" . $str . "</li></$tag>";
        
        // replace /n with adding two tags
        // add previous list item ending tag
        // add next list item starting tag
        $str = str_replace("\n", "</li><br />\n<li>", $str);
        
        // spit back the modified string
        return $str;
    }

    public function pdfpoAction ()
    {
        error_reporting(0);
        $id = $this->getRequest()->getParam('id');
        $origId = $this->view->bookingId($id);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $type = $this->getRequest()->getParam('type');
        $type = strtoupper($type);
        
        $bookingsModel = new Bookings();
        $lineItemsModel = new LineItems();
        $booking = $bookingsModel->getById($id);
        
        $lineItems = $lineItemsModel->getAllByOrder($id);
        
        require_once (APPLICATION_PATH . "/../data/dompdf/dompdf_config.inc.php");
        
        if ($type == 'IO') {
            $title = "Print Insertion Order Worksheet";
        }
        if ($type == 'PO') {
            $title = "Purchase Order";
        }
        if ($type == 'IOPO') {
            $title = "Purchase Order - For Internal Use Only ";
        }
        
        $html = "
		<div style='font-family:Arial, sans-serif;'>
			<!-- HEADER -->
			<table width='100%'>
				<tr>
					<td style='text-align:left;'><img src='" .
                 APPLICATION_PATH .
                 "/../public/images/MBIlogo.jpg'></td>
					<td valign='bottom' style='text-align:center; width:220px;'>
						
								<div style='text-align:center; background: #000; color:#fff;padding:5px;'><h2 style='margin:0; padding:0;'>$title</h2></div>
						
								<div style='text-align:center;border:1px solid #000; padding:5px;'><h2 style='margin:0; padding:0;'># " .
                 $origId .
                 "</h2></div>
					
					</td>
					<td style='text-align:right; font-size:.8em;' width='290px'>
						<h3 style='margin:0; padding:0;'>Media Brokers International, Inc.</h3>
						11720 Amberpark Drive, Suite 600<br>
						Alpharetta, Georgia 30009<br>
						Phone: 678.514.6200<br>
						<table style='width:100%;'>
							<tr>
								<td style='text-align:left;padding-left:15px; font-weight:bold;'>Date: " .
                 date('m/d/Y', strtotime($booking->date)) . "</td>
								<td style='text-align:right;'>Fax: 678.514.6299</td>
							</tr>
						</table>
						
					</td>
				</tr>
			</table>
			<div style='position:relative; top:-5px; border-top:2px solid #000; '>
			</div>
			<!-- END HEADER -->
			<!-- INFO -->
			<br>
			<table width='100%' style='font-size:.8em;'>
				<tr>
					<td style='width:50%;' valign='top'>
						<b><u>Product / Advertiser</u></b><br>
						" . $booking->product_advertiser . "<br><br>
						
						<b><u>Invoicing</u></b><br>
						All Invoices must be sent to MBI ONLY. <br>
						See \"Invoicing / Special Instructions\" below.
						
		
					</td>
					<td style='padding-left:5px; border-left:1px solid #000;' valign='top'>
						<b><u>Media Name</u></b><br>
						" . $booking->media_name .
                 "<br><br>
						<b><u>Circulation / Rate base</u></b>: " .
                 nl2br($booking->circulation_rate_base) . "<br><br>
						<b><u>Ad Specs</u></b><br>";
						
						$i = 1;
						
						foreach ($lineItems as $lineItem) {
							if(trim($lineItem->ad_spec_color) <> '' || trim($lineItem->ad_spec_bleed) <> '' || trim($lineItem->ad_spec_size) <> '' || trim($lineItem->ad_spec_position) <> '')
							{
									$html	.=  $i++ . ".  " .$lineItem->ad_spec_color . " , " . $lineItem->ad_spec_bleed . " , " . $lineItem->ad_spec_size . " , " . $lineItem->ad_spec_position . "<br>" ;
									
							}
					
						}
						
						
						$html .= "<br><b><u>Media Contact Name, Phone, Fax</u></b><br>
						" . $booking->media_primary_contact . "<br>
						" . $booking->media_phone . "<br>
						" . $booking->media_company_name . "<br>
						" . $booking->media_email . "
						
					</td>
				</tr>	
			</table>
			<!-- END INFO -->
								
								
			<!-- LINE ITEMS -->
			<table style='border-collapse:collapse; width:100%; font-size:.7em;' border=1>
				<tr style='font-weight:bold; text-align:center;'>
					<td>Line</td>
					<td style='text-align:left; padding-left:4px;'>Issue / Cover</td>
					<td>On-Sale</td>
					<td>Space Close</td>
					<td>Materials Due</td>
					<td style='text-align:right; padding-right:4px;'>MBI Net Rate</td>
				</tr>";
        $i = 1;
        $totalPayment = 0;
        
        foreach ($lineItems as $lineItem) {
            
            if ($i != 10) {
                $style = "style='border-bottom:0;border-top:0;'";
            } else {
                $style = "style='border-top:0;'";
            }
            $payment = (float) str_replace(",", "", $lineItem->mbi_rate);
            $totalPayment = $totalPayment + $payment;
            $html .= "
						<tr style='text-align:center;'>
							<td $style>" . $i .
                     "</td>
							<td $style><div style='text-align:left;'>" .
                     $lineItem->issue_cover . "</div></td>
							<td $style>" .
                     date('m/d/Y', strtotime($lineItem->on_sale)) . "</td>
							<td $style>" .
                     date('m/d/Y', strtotime($lineItem->space_close)) . "</td>
							<td $style>" .
                     date('m/d/Y', strtotime($lineItem->materials_due)) .
                     "</td>
							<td $style>" .
                     number_format(str_replace(",", "", $lineItem->mbi_rate), 2) . "</td>
						</tr>
					";
            
            $i ++;
        }
        while ($i < 11) {
            if ($i != 10) {
                $style = "style='border-bottom:0;border-top:0;'";
            } else {
                $style = "style='border-top:0;'";
            }
            $html .= "
						<tr style='text-align:center;'>
							<td $style>" . $i . "</td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
							<td $style></td>
						</tr>
					";
            $i ++;
        }
        $html .= "<tr><td colspan='5' style='text-align:right; border-right:1px solid #000;'><b>P.O. Total:</b></td><td style='text-align:right;'><b>USD " .
                 number_format($totalPayment, 2) . "</b></td></tr></table>";
        
        $html .= "</table>
			<!-- END LINE ITEMS -->
        		
  
			<!-- TEXT BOXES -->
			<div style='font-size:.8em;'>
				<b><u>Disposition of Materials</u></b><br>
				Materials for each individual Insertion listed above to be sent directly from Client to Publisher, to arrive by the Materials Due Date listed above.<br>
				";
        if (trim($booking->disposition_notes) != '') {
            $html .= $this->nl2li($booking->disposition_notes);
        }
        $spec_instr_po_txtarea=!isset($booking->spec_instr_po_txtarea)?'-':$booking->spec_instr_po_txtarea;
        
        $html .= "<br><br><b><u>Invoicing / Special Instructions</u></b><br>
				<li>Send all Invoices and correspondence with " . $booking->checking_copies . " checking copies to MBI at the address above.</li>
				<li><b>Include MBI's Purchase Order (P.O.) Number on all Invoices.</b></li>s
				<li>Invoices that are received without an MBI P.O. number for reference will be referred back to the originator.</li>
				<li>For any questions regarding billing and/or payment, please contact MBI's Accounts Payable at 678-514-6200 x 221 or ap@media-brokers.com.</li><br><br><b>Special Instructions:</b>". $spec_instr_po_txtarea."<br><br>" ;
        $usersModel = new Users();
        $salesRep = $usersModel->getUser($booking->sales_rep);
        
        $html .= "
				<li>For questions regarding ad materials, please contact " .
                 $salesRep->name . " at " . $salesRep->phone . " or " .
                 $salesRep->email . "</li>
				<br><br><Br><br><br>
				<table width='100%'>
					<tr>
						<td>
						<span style='font-size:9px;'>Page 1 of 1</span>
						</td>
						<td style='text-align:right;'>
							<b><u>P.O. Total: USD " .
                 number_format($totalPayment, 2) . "</u></b>
						</td>
					</tr>
				</table>
				<table style='padding-top:20px; width:100%;'>
					<tr>
						<td style='width:70%;'>
							<u>MBI Authorized Approval: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</u>
						</td>
						<td><u>MBI Representative: " . $salesRep->name . "</u></td>
					</tr>
				</table>"; 
				if($booking->po_footer <> 'off')
				{
					$html .=
					"<div style='font-size:8px;'>
						Media Brokers International (MBI) acts only as an agent for the Advertiser and does not guarantee payment from the Advertiser, who is solely responsible for payment for this order. MBI will bill the Advertiser and upon receipt of payment we will remit to Media owner payment due. The advertiser guarantees that his reproduction is for a one time use only. Any unauthorized insertions will not be the responsibility of ABI or the Advertiser. In the event that all or part of any ad key codes are incorrect due to any fault of the Media owner, MBI cannot hold Advertiser or itself responsible for payment of the advertisement. By accepting this purchase order Media understands all the terms and conditions here above set forth and agrees to be bound by them. No verbal agreements are recognized. This is the complete and entire understanding.
					</div>
					";
				}
        $html .= "</div>
			<!-- END TEXT BOXES -->
		
		</div>
		";
        
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->render();
        $dompdf->stream(
                str_replace(" ", "_", $title) . "_" .
                         $booking->client_company_name . "_" .
                         $booking->media_company_name . "_" .
                         date('Y-m-d', strtotime($booking->date)) . ".pdf");
    }


    public function deletebookingAction()
    {
    	$bookingsModel = new Bookings();
    	$lineItemsModel = new LineItems();
    	$id = $this->getRequest()->getParam('id');
    	$lineItems = $lineItemsModel->getAllByOrder($id);
    
    	$where_bookings = 'id='.$id;
    	$where_lineItems ='order_id='.$id;
    
    	if($bookingsModel->delete($where_bookings) && $lineItemsModel->delete($where_lineItems))
    	{
    		$this->view->success = "Booking successfully deleted";
    		$this->_forward('bookings');
    		$this->redirect('/bookings');
    	}
    	else
    	{
    		$this->view->errors = array('An Error Occurred. Please Try Again.');
    	}
    
    }
   
    
    
    public function viewbookingAction ()
    {
        $bookingsModel = new Bookings();
        $id = $this->getRequest()->getParam('id');
        $this->view->booking = $bookingsModel->getById($id);
        $booking = $this->view->booking;
        $lineItemsModel = new LineItems();
        
		//$writer = new Zend_Log_Writer_Stream('/logs/log.txt');
		
        $this->view->lineItems = $lineItemsModel->getAllByOrder($id);
        $attachmentsModel = new Attachments();
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            if (! isset($data['id'])) {
                $file = $_FILES['file'];
                $fileData = array();
                if (trim($data['title']) <> '') {
                    $fileData['title'] = $data['title'];
                } else {
                    $fileData['title'] = "Untitled File";
                }
                $fileData['booking'] = $id;
                $fileData['type'] = $file['type'];
                $fileData['filename'] = uniqid() . $file['name'];
                print_r($file);
                if (move_uploaded_file($file['tmp_name'], 
                        APPLICATION_PATH . '/../public/uploads/' .
                                 $fileData['filename'])) {
                    
                    $attachmentsModel->insert($fileData);
                }
            } else {
                $newData['status'] = $data['status'];
                $bookingsModel->updateRecord($data['id'], $newData);
                $this->view->success = "Booking Status Set to '" .
                         $newData['status'] . "'.";
                
                if ($data['status'] == 'Approved') {
                    $emailModel = new Email();
                    $usersModel = new Users();
                    $salesRep = $usersModel->getUser($booking->sales_rep);
                    $options = array();
                    // EMAIL TO CLIENT
                    $options['subject'] = "Your Booking Has Been Submitted";
                    // CLIENT'S EMAIL ADDRESS
                    $options['to'] = $booking['client_email'];
                    $options['message'] = "<img src='http://" .
                             $_SERVER['HTTP_HOST'] .
                             "/images/MBIlogo.jpg'><hr>
   						<h1>Your Booking has Been Submitted</h1>
   						The Insertion Order of the booking is attached. Please sign and return to " .
                             $salesRep->email . ".<br><br>
   						Thank You,<br>" . $salesRep->name;

                    
                    //io
                    $options['attachments'] = array(
                            "http://" . $_SERVER['HTTP_HOST'] . "/pdf-gen/io/" .
                                     $data['id']
                    );
                    $emailModel->sendMail($options);
                    
                    $options = array();
                    // EMAIL TO PUBLISHER
                    $options['subject'] = "A Booking has Been Submitted";
                    // PUBLISHER'S EMAIL ADDRESS
                    $options['to'] = $booking['media_email'];
                    $options['message'] = "<img src='http://" .
                             $_SERVER['HTTP_HOST'] .
                             "/images/MBIlogo.jpg'><hr>
   						<h1>A Booking has Been Submitted</h1>
   						The Purchase Order of the booking is attached. Please sign and return to " .
                             $salesRep->email . ".<br><br>
   						Thank You,<br>" . $salesRep->name;
                    
                    
                    //po
                    $options['attachments'] = array(
                            "http://" . $_SERVER['HTTP_HOST'] . "/pdf-gen/po/" .
                                     $data['id']
                    );
                    $emailModel->sendMail($options);
                    
                    // EMAIL TO SALES REP
                    $options['subject'] = "Booking " .
                             $this->view->bookingId($data['id']) .
                             " has Been Approved";
                    // PUBLISHER'S EMAIL ADDRESS
                    $options['to'] = $salesRep->email;
                    $options['message'] = "<img src='http://" .
                             $_SERVER['HTTP_HOST'] .
                             "/images/MBIlogo.jpg'><hr>
   						<h1>Booking #" .
                             $this->view->bookingId($data['id']) .
                             " has Been Approved</h1>
   						The Purchase Order and Insertion Order of the booking are attached. <br><br>";
                    
                    $options['attachments'] = array(
                            "http://" . $_SERVER['HTTP_HOST'] . "/pdf-gen/po/" .
                                     $data['id'],
                                    "http://" . $_SERVER['HTTP_HOST'] .
                                     "/pdf-gen/io/" . $data['id']
                    );
                    $emailModel->sendMail($options);
                }
                
                if ($data['status'] == 'Denied') {
                    $emailModel = new Email();
                    // EMAIL TO SALES REP
                    $options['subject'] = "Booking " .
                             $this->view->bookingId($data['id']) .
                             " has Been Denied";
                    // PUBLISHER'S EMAIL ADDRESS
                    $options['to'] = $salesRep->email;
                    $options['message'] = "<img src='http://" .
                             $_SERVER['HTTP_HOST'] .
                             "/images/MBIlogo.jpg'><hr>
   						<h1>Booking #" .
                             $this->view->bookingId($data['id']) . " has Been Denied</h1>
   						The Purchase Order and Insertion Order of the booking are attached. <br><br>
   						<b>Reason for Denial:</b><br>" . $data['reason'];
                    
                    $options['attachments'] = array(
                            "http://" . $_SERVER['HTTP_HOST'] . "/pdf-gen/po/" .
                                     $data['id'],
                                    "http://" . $_SERVER['HTTP_HOST'] .
                                     "/pdf-gen/io/" . $data['id']
                    );
                    $emailModel->sendMail($options);
                }
            }
        }
        $this->view->attachments = $attachmentsModel->getAllByBooking($id);
        $booking = $bookingsModel->getById($id);
        $this->view->booking = $booking;
        if ($booking->orig_id <> '') {
            $this->view->relatedBookings = $bookingsModel->getRelated(
                    $booking->orig_id);
        } else {
            $this->view->relatedBookings = $bookingsModel->getRelated($id);
        }
    }
}
