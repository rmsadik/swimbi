<?php

class Zend_View_Helper_BookingId {

    public $view;

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function bookingId($id) {
    	$bookingsModel = new Bookings;
    	$booking = $bookingsModel->getById($id);
    	
    	if($booking->revision == 0){
    		return $booking->id;
    	} else {
    		return $booking->orig_id."-R".$booking->revision;
    	}
    	
 
    }
}
?>
