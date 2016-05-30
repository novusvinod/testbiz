<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Businesstaxivaxi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use PHPExcel_IOFactory;

define ( 'BUSINESSAUTHTAXIVAXIHOME', 'http://taxivaxi.in/business/api/taxivaxiHome');

define ( 'BUSINESSTAXIVAXILOGIN', 'http://taxivaxi.in/business/api/taxivaxilogin' );
define ( 'BUSINESSTAXIVAXILOGOUT', 'http://taxivaxi.in/business/api/taxivaxilogout' );
define ( 'BUSINESSTAXIVAXIFORGOT', 'http://taxivaxi.in/business/api/taxivaxiforgot' );

define ( 'ALLTAXIVAXIBOOKINGS', 'http://taxivaxi.in/business/api/getAllTaxivaxiBookings' );
define ( 'ALLTAXIVAXIBUSBOOKINGS', 'http://taxivaxi.in/business/api/getAllTaxivaxiBusBookings' );
define ( 'BOOKING', 'http://taxivaxi.in/business/api/viewBookingTaxivaxi' );
define ( 'BUSBOOKING', 'http://taxivaxi.in/business/api/viewBusBookingTaxivaxi' );
define ( 'ACCEPTBOOKING', 'http://taxivaxi.in/business/api/acceptTaxivaxiBooking' );
define ( 'ACCEPTBUSBOOKING', 'http://taxivaxi.in/business/api/acceptTaxivaxiBusBooking' );
define ( 'REJECTBOOKING', 'http://taxivaxi.in/business/api/rejectTaxivaxiBooking' );
define ( 'REJECTBUSBOOKING', 'http://taxivaxi.in/business/api/rejectTaxivaxiBusBooking' );

define ( 'BOOK', 'http://taxivaxi.in/business/api/olaBook' );
define ( 'CANCELBOOK', 'http://taxivaxi.in/business/api/olaCancelbook' );

define ( 'ASSIGNBOOKING', 'http://taxivaxi.in/business/api/assignTaxivaxiBooking' );
define ( 'ASSIGNRADIOBOOKING', 'http://taxivaxi.in/business/api/assignRadioBooking' );
define ( 'ASSIGNBUSBOOKING', 'http://taxivaxi.in/business/api/assignTaxivaxiBusBooking' );

define ( 'UPLOADSLIP', 'http://taxivaxi.in/business/api/addDutySlip' );
define ( 'ALLTAXIVAXIINVOICES', 'http://taxivaxi.in/business/api/getAllTaxivaxiadminInvoices' );
define ( 'ADDINVOICERADIO', 'http://taxivaxi.in/business/api/addInvoiceRadio' );
define ( 'ADDINVOICELOCAL', 'http://taxivaxi.in/business/api/addInvoiceLocal' );
define ( 'VIEWINVOICE', 'http://taxivaxi.in/business/api/viewInvoice' );

define ( 'TAXIMODELSBYBOOKINGID', 'http://taxivaxi.in/business/api/taximodelsByBookingId' );

define ( 'RESETBOOKING', 'http://taxivaxi.in/business/api/resetBooking' );

define ( 'BUSBOOKINGBYINVOICEID', 'http://taxivaxi.in/business/api/viewBusBookingTaxivaxiByInvoiceId' );
define ( 'ALLBUSINVOICES', 'http://taxivaxi.in/business/api/getAllTaxivaxiadminBusInvoices' );
define ( 'ADDBUSINVOICE', 'http://taxivaxi.in/business/api/addBusInvoice' );
define ( 'EDITBUSINVOICE', 'http://taxivaxi.in/business/api/editBusInvoice' );
define ( 'VIEWBUSINVOICE', 'http://taxivaxi.in/business/api/viewBusInvoice' );
define ( 'UPLOADBUSTICKET', 'http://taxivaxi.in/business/api/addBusTicket' );

//Reports
define ( 'BOOKINGREPORT', 'http://taxivaxi.in/business/api/getTaxiVaxiBookingReport' );

//Billings
define ( 'COMMENTINVOICE', 'http://taxivaxi.in/business/api/taxivaxiAdminCommentInvoice' );
define ( 'COMMENTBUSINVOICE', 'http://taxivaxi.in/business/api/taxivaxiAdminCommentBusInvoice' );

define ( 'ALLTAXIVAXIBILLS', 'http://taxivaxi.in/business/api/getAllBills' );
define ( 'VIEWBILL', 'http://taxivaxi.in/business/api/viewBill' );

//INVOICE EDIT
define ( 'EDITINVOICERADIO', 'http://taxivaxi.in/business/api/editInvoiceRadio' );
define ( 'EDITINVOICEOUTSTATION', 'http://taxivaxi.in/business/api/editInvoiceOutstation' );
define ( 'EDITINVOICELOCAL', 'http://taxivaxi.in/business/api/editInvoiceLocal' );

class BusinesstaxivaxiController extends AbstractActionController
{

    public function getBusinesstaxivaxiLoginAction()
    {
        if(!isset($_COOKIE['business_taxivaxi']))
        {
            return new ViewModel();
        }
        else
            return $this->redirect()->toRoute('businesstaxivaxi-home');
    }

    public function postBusinesstaxivaxiLoginAction()
    {
            $request = $this->getRequest ();

            $data['username'] = $request->getPost('username');
            $data['password'] = $request->getPost('password');

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSTAXIVAXILOGIN );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            /*return new ViewModel(array('response' => $response,));*/

            if ($r['error'] == ""){
                $cookie_name = "access_token_business_taxivaxi";
                $cookie_value = $r['response']['access_token'];
                setcookie($cookie_name, $cookie_value, 0, "/");

                $cookie_name2 = "business_taxivaxi";
                $cookie_value2 = 1;
                setcookie($cookie_name2, $cookie_value2, 0, "/");
                
                return $this->redirect()->toRoute('businesstaxivaxi-home');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/get-businesstaxivaxi-login.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    public function businesstaxivaxiHomeAction()
    {
        if(isset($_COOKIE['business_taxivaxi']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSAUTHTAXIVAXIHOME );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();

            // return new ViewModel();
            return new ViewModel(array('details' => $response,));
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function businesstaxivaxiLogoutAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSTAXIVAXILOGOUT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                unset($_COOKIE['access_token_business_taxivaxi']);
                setcookie('access_token_business_taxivaxi', '', time() - 3600);

                unset($_COOKIE['business_taxivaxi']);
                setcookie('business_taxivaxi', '', time() - 3600);
                
                return $this->redirect()->toRoute('businesstaxivaxi-home');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function postBusinesstaxivaxiForgotAction()
    {
            $request = $this->getRequest ();

            $data['username'] = $request->getPost('username');

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSTAXIVAXIFORGOT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                
                $m = 'New Passorword Sent Successfully on '.$data['username'];

                $view = new ViewModel(array('mess_success'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/get-businesstaxivaxi-login.phtml'); // path to phtml file under view folder
                return $view;
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/get-businesstaxivaxi-login.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    //Bookings
    public function BusinesstaxivaxiBookingsAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['type'] = '1';

            $getRequest = new Request ();
            $getRequest->setUri ( ALLTAXIVAXIBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function BusinesstaxivaxiBookingsAssignedAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['type'] = '2';

            $getRequest = new Request ();
            $getRequest->setUri ( ALLTAXIVAXIBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings-assigned.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function BusinesstaxivaxiBusBookingsAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['type'] = '1';

            $getRequest = new Request ();
            $getRequest->setUri ( ALLTAXIVAXIBUSBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function BusinesstaxivaxiBusBookingsAssignedAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['type'] = '2';

            $getRequest = new Request ();
            $getRequest->setUri ( ALLTAXIVAXIBUSBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-bookings-assigned.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function BusinesstaxivaxiBookingsOldAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['type'] = '3';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLTAXIVAXIBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings-old.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function BusinesstaxivaxiBusBookingsOldAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['type'] = '3';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLTAXIVAXIBUSBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-bookings-old.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function BusinesstaxivaxiRejectedBookingsAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['type'] = '4';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLTAXIVAXIBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-rejected-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function BusinesstaxivaxiRejectedBusBookingsAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['type'] = '4';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLTAXIVAXIBUSBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-rejected-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }
    
    public function acceptBusinesstaxivaxiBookingAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( ACCEPTBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businesstaxivaxi-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function acceptBusinesstaxivaxiBusBookingAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( ACCEPTBUSBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businesstaxivaxi-bus-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function rejectBusinesstaxivaxiBookingAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');
            $request = $this->getRequest ();

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;
            $data['reason_cancel'] = $request->getPost('reason_cancel');

            $getRequest = new Request ();
            $getRequest->setUri ( REJECTBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businesstaxivaxi-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function rejectBusinesstaxivaxiBusBookingAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');
            $request = $this->getRequest ();

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;
            $data['reason_cancel'] = $request->getPost('reason_cancel');

            $getRequest = new Request ();
            $getRequest->setUri ( REJECTBUSBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businesstaxivaxi-bus-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function rejectreasonBusinesstaxivaxiBookingAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            return new ViewModel(array('booking_id' => $booking_id));
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function rejectreasonBusinesstaxivaxiBusBookingAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            return new ViewModel(array('booking_id' => $booking_id));
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function assignBusinesstaxivaxiBookingAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( TAXIMODELSBYBOOKINGID );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('taxis' => $response, 'booking_id' => $booking_id,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function assignBusinesstaxivaxiBusBookingAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( BUSBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('booking' => $response, 'booking_id' => $booking_id,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function postBusinesstaxivaxiAssignAction()
    {
            $request = $this->getRequest ();
            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $request->getPost('booking_id');
            $data['driver_name'] = $request->getPost('driver_name');

            $data['driver_contact'] = $request->getPost('driver_contact');
            $data['operator_name'] = $request->getPost('operator_name');

            $data['taxi_model_id'] = $request->getPost('taxi_model_id');
            $data['taxi_reg_no'] = $request->getPost('taxi_reg_no');

            $getRequest = new Request ();
            $getRequest->setUri ( ASSIGNBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            /*return new ViewModel(array('response' => $response,));*/

            if ($r['error'] == ""){

                $m = "Booking Assigned Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bookings');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    public function postBusinesstaxivaxiBusAssignAction()
    {
            $request = $this->getRequest ();
            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $request->getPost('booking_id');

            $data['boarding_point_taxivaxi'] = $request->getPost('boarding_point_taxivaxi');
            $data['pickup_datetime_taxivaxi'] = $request->getPost('pickup_datetime_taxivaxi');
            $data['ticket_number'] = $request->getPost('ticket_number');
            $data['pnr_number'] = $request->getPost('pnr_number');
            $data['bus_type_allocated'] = $request->getPost('bus_type_allocated');
            $data['operator_name'] = $request->getPost('operator_name');
            $data['operator_contact'] = $request->getPost('operator_contact');
            $data['seat_number'] = $request->getPost('seat_number');

            $data['total'] = $request->getPost('total');
            $data['taxivaxi_charge'] = $request->getPost('taxivaxi_charge');
            $data['taxivaxi_tax_rate'] = $request->getPost('taxivaxi_tax_rate');
            $data['taxivaxi_tax_charge'] = $request->getPost('taxivaxi_tax_charge');
            $data['sub_total'] = $request->getPost('sub_total');            


            $getRequest = new Request ();
            $getRequest->setUri ( ASSIGNBUSBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if($data['total'] != '')
            {
                $getRequest2 = new Request ();
                $getRequest2->setUri ( ADDBUSINVOICE );
                $getRequest2->setMethod ( 'POST' );
                $getRequest2->setPost ( new Parameters ( $data ) );
                $client2 = new Client ();
                $response2 = $client2->send($getRequest2)->getBody();
                $r2 = json_decode($response2, true);

                $invoice_id = $r2['response']['invoice_id'];
                $data3['invoice_id'] = $invoice_id;
                $data3 ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];

                if($invoice_id)
                {
                    if(isset($_FILES['image']))
                    {
                        if($_FILES['image']['tmp_name'])
                        {
                            if(!$_FILES['image']['error'])
                            {
                                $inputFile = $_FILES['image']['name'];
                                $extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
                                $extension_str = pathinfo($inputFile, PATHINFO_EXTENSION);

                                $data_str = file_get_contents($_FILES['image']['tmp_name']);
                                $data_str = base64_encode($data_str);

                                if($extension == 'JPG' || $extension == 'JPEG' || $extension == 'PNG'){
                                    $data3['img_string'] = "data:image/".$extension_str.";base64,".$data_str;
                                    $data3['img_ext'] = $extension_str;

                                    $getRequest3 = new Request ();
                                    $getRequest3->setUri ( UPLOADBUSTICKET );
                                    $getRequest3->setMethod ( 'POST' );
                                    $getRequest3->setPost ( new Parameters ( $data3 ) );
                                    $client3 = new Client ();
                                    $response3 = $client3->send($getRequest3)->getBody();
                                }
                            }
                        }
                    }
                }
            }

            if ($r['error'] == ""){

                $m = "Booking Assigned Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bus-bookings');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    public function assignBusinessradioBookingAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;


                return new ViewModel(array('booking_id' => $booking_id,));
            
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function postBusinessradioAssignAction()
    {
            $request = $this->getRequest ();
            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];

            $data ['booking_id'] = $request->getPost('booking_id');

            $data ['reference_no'] = $request->getPost('reference_no');
            $data ['operator_name'] = $request->getPost('operator_name');

            $data ['driver_name'] = $request->getPost('driver_name');
            $data ['driver_contact'] = $request->getPost('driver_contact');

            $data ['taxi_type_name'] = $request->getPost('taxi_type_name');
            $data ['taxi_reg_no'] = $request->getPost('taxi_reg_no');
            $data ['taxi_model_name'] = $request->getPost('taxi_model_name');

            $getRequest = new Request ();
            $getRequest->setUri ( ASSIGNRADIOBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            /*return new ViewModel(array('response' => $response,));*/

            if ($r['error'] == ""){

                $m = "Booking Assigned Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bookings');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    public function bookBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( BOOK );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $cookie_name4 = "success";
                $cookie_value4 = $r['response'];
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bookings');
            }
            else{
                $cookie_name4 = "fail";
                $cookie_value4 = $r['error'];
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bookings');
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function cancelbookBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( CANCELBOOK );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $cookie_name4 = "success";
                $cookie_value4 = $r['response'];
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bookings');
            }
            else{
                $cookie_name4 = "fail";
                $cookie_value4 = $r['error'];
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bookings');
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }


    public function getAddRadioinvoiceBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( BOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('booking' => $response, 'booking_id' => $booking_id,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    

    public function postAddRadioinvoiceBusinesstaxivaxiAction()
    {
            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $request = $this->getRequest ();

            $data['booking_id'] = $request->getPost('booking_id');
            $data['tour_type'] = $request->getPost('tour_type');
            $data['taxi_model_name'] = $request->getPost('taxi_model_name');

            $data['hours_done'] = $request->getPost('hours_done');
            $data['allowed_hrs'] = $request->getPost('allowed_hrs');
            $data['extra_hours'] = $request->getPost('extra_hours');
            $data['hour_rate'] = $request->getPost('hour_rate');
            $data['extra_hours_charge'] = $request->getPost('extra_hours_charge');

            $data['kms_done'] = $request->getPost('kms_done');
            $data['allowed_kms'] = $request->getPost('allowed_kms');
            $data['extra_kms'] = $request->getPost('extra_kms');
            $data['km_rate'] = $request->getPost('km_rate');
            $data['extra_kms_charge'] = $request->getPost('extra_kms_charge');

            $data['extras'] = $request->getPost('extras');

            $data['base_rate'] = $request->getPost('base_rate');
            $data['total_ex_tax'] = $request->getPost('total_ex_tax');
            $data['tax_rate'] = $request->getPost('tax_rate');
            $data['tax'] = $request->getPost('tax');
            $data['total'] = $request->getPost('total');

            $data['taxivaxi_rate'] = '10';
            $data['taxivaxi_charge'] = $request->getPost('taxivaxi_charge');
            $data['taxivaxi_tax_rate'] = $request->getPost('taxivaxi_tax_rate');
            $data['taxivaxi_tax_charge'] = $request->getPost('taxivaxi_tax_charge');
            $data['sub_total'] = $request->getPost('sub_total');            

            if(isset($_FILES['image']))
            {
                if($_FILES['image']['tmp_name'])
                {
                    if(!$_FILES['image']['error'])
                    {
                        $inputFile = $_FILES['image']['name'];
                        $extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
                        $extension_str = pathinfo($inputFile, PATHINFO_EXTENSION);

                        $data_str = file_get_contents($_FILES['image']['tmp_name']);
                        $data_str = base64_encode($data_str);

                        if($extension == 'JPG' || $extension == 'JPEG' || $extension == 'PNG'){
                            $data['img_string'] = "data:image/".$extension_str.";base64,".$data_str;
                            $data['img_ext'] = $extension_str;
                        }
                    }
                }
            }

            $getRequest = new Request ();
            $getRequest->setUri ( ADDINVOICERADIO );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if(array_key_exists('img_string', $data))
            {
                $getRequest2 = new Request ();
                $getRequest2->setUri ( UPLOADSLIP );
                $getRequest2->setMethod ( 'POST' );
                $getRequest2->setPost ( new Parameters ( $data ) );
                $client2 = new Client ();
                $response2 = $client2->send($getRequest2)->getBody();
            }

            if ($r['error'] == ""){
                $m = "Invoice Generated Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bookings-old');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings-old.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    public function getAddLocalinvoiceBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( BOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('booking' => $response, 'booking_id' => $booking_id,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings-old.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    

    public function postAddLocalinvoiceBusinesstaxivaxiAction()
    {
            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $request = $this->getRequest ();

            $data['booking_id'] = $request->getPost('booking_id');
            $data['tour_type'] = $request->getPost('tour_type');
            $data['rate_id'] = $request->getPost('rate_id');
            $data['rate_name'] = $request->getPost('rate_name');
            $data['taxi_model_id'] = $request->getPost('taxi_model_id');
            $data['taxi_model_name'] = $request->getPost('taxi_model_name');
            $data['pickup_date'] = $request->getPost('pickup_date');
            $data['pickup_time'] = $request->getPost('pickup_time');
            $data['drop_date'] = $request->getPost('drop_date');

            $data['drop_time'] = $request->getPost('drop_time');
            $data['hours_done'] = $request->getPost('hours_done');
            $data['allowed_hrs'] = $request->getPost('allowed_hrs');
            $data['extra_hours'] = $request->getPost('extra_hours');
            $data['hour_rate'] = $request->getPost('hour_rate');
            $data['extra_hours_charge'] = $request->getPost('extra_hours_charge');
            $data['start_km'] = $request->getPost('start_km');

            $data['end_km'] = $request->getPost('end_km');
            $data['kms_done'] = $request->getPost('kms_done');
            $data['allowed_kms'] = $request->getPost('allowed_kms');
            $data['extra_kms'] = $request->getPost('extra_kms');
            $data['km_rate'] = $request->getPost('km_rate');
            $data['extra_kms_charge'] = $request->getPost('extra_kms_charge');
            $data['parking'] = $request->getPost('parking');

            $data['driver'] = $request->getPost('driver');
            $data['base_rate'] = $request->getPost('base_rate');
            $data['total_ex_tax'] = $request->getPost('total_ex_tax');
            $data['tax_rate'] = $request->getPost('tax_rate');
            $data['tax'] = $request->getPost('tax');
            $data['total'] = $request->getPost('total');

            $data['taxivaxi_rate'] = '100';
            $data['taxivaxi_charge'] = $request->getPost('taxivaxi_charge');
            $data['taxivaxi_tax_rate'] = $request->getPost('taxivaxi_tax_rate');
            $data['taxivaxi_tax_charge'] = $request->getPost('taxivaxi_tax_charge');
            $data['sub_total'] = $request->getPost('sub_total');            

            if(isset($_FILES['image']))
            {
                if($_FILES['image']['tmp_name'])
                {
                    if(!$_FILES['image']['error'])
                    {
                        $inputFile = $_FILES['image']['name'];
                        $extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
                        $extension_str = pathinfo($inputFile, PATHINFO_EXTENSION);

                        $data_str = file_get_contents($_FILES['image']['tmp_name']);
                        $data_str = base64_encode($data_str);

                        if($extension == 'JPG' || $extension == 'JPEG' || $extension == 'PNG'){
                            $data['img_string'] = "data:image/".$extension_str.";base64,".$data_str;
                            $data['img_ext'] = $extension_str;
                        }
                    }
                }
            }

            $getRequest = new Request ();
            $getRequest->setUri ( ADDINVOICELOCAL );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if(array_key_exists('img_string', $data))
            {
                $getRequest2 = new Request ();
                $getRequest2->setUri ( UPLOADSLIP );
                $getRequest2->setMethod ( 'POST' );
                $getRequest2->setPost ( new Parameters ( $data ) );
                $client2 = new Client ();
                $response2 = $client2->send($getRequest2)->getBody();
            }
        

            if ($r['error'] == ""){
                $m = "Invoice Generated Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bookings');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    public function getAddOutstationinvoiceBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( BOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('booking' => $response, 'booking_id' => $booking_id,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings-old.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }
    

    public function postAddOutstationinvoiceBusinesstaxivaxiAction()
    {
            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $request = $this->getRequest ();

            $data['booking_id'] = $request->getPost('booking_id');
            $data['tour_type'] = $request->getPost('tour_type');
            $data['rate_id'] = $request->getPost('rate_id');
            $data['rate_name'] = $request->getPost('rate_name');
            $data['taxi_model_id'] = $request->getPost('taxi_model_id');
            $data['taxi_model_name'] = $request->getPost('taxi_model_name');
            $data['pickup_date'] = $request->getPost('pickup_date');
            $data['pickup_time'] = $request->getPost('pickup_time');
            $data['drop_date'] = $request->getPost('drop_date');

            $data['drop_time'] = $request->getPost('drop_time');
            $data['hours_done'] = $request->getPost('hours_done');
            $data['allowed_hrs'] = $request->getPost('allowed_hrs');
            $data['extra_hours'] = $request->getPost('extra_hours');
            $data['hour_rate'] = $request->getPost('hour_rate');
            $data['extra_hours_charge'] = $request->getPost('extra_hours_charge');
            $data['start_km'] = $request->getPost('start_km');

            $data['end_km'] = $request->getPost('end_km');
            $data['kms_done'] = $request->getPost('kms_done');
            $data['allowed_kms'] = $request->getPost('allowed_kms');
            $data['extra_kms'] = $request->getPost('extra_kms');
            $data['km_rate'] = $request->getPost('km_rate');
            $data['extra_kms_charge'] = $request->getPost('extra_kms_charge');
            $data['parking'] = $request->getPost('parking');

            $data['driver'] = $request->getPost('driver');
            $data['base_rate'] = $request->getPost('base_rate');
            $data['total_ex_tax'] = $request->getPost('total_ex_tax');
            $data['tax_rate'] = $request->getPost('tax_rate');
            $data['tax'] = $request->getPost('tax');
            $data['total'] = $request->getPost('total');

			$data['taxivaxi_rate'] = '100';
            $data['taxivaxi_charge'] = $request->getPost('taxivaxi_charge');
            $data['taxivaxi_tax_rate'] = $request->getPost('taxivaxi_tax_rate');
            $data['taxivaxi_tax_charge'] = $request->getPost('taxivaxi_tax_charge');
            $data['sub_total'] = $request->getPost('sub_total');

            if(isset($_FILES['image']))
            {
                if($_FILES['image']['tmp_name'])
                {
                    if(!$_FILES['image']['error'])
                    {
                        $inputFile = $_FILES['image']['name'];
                        $extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
                        $extension_str = pathinfo($inputFile, PATHINFO_EXTENSION);

                        $data_str = file_get_contents($_FILES['image']['tmp_name']);
                        $data_str = base64_encode($data_str);

                        if($extension == 'JPG' || $extension == 'JPEG' || $extension == 'PNG'){
                            $data['img_string'] = "data:image/".$extension_str.";base64,".$data_str;
                            $data['img_ext'] = $extension_str;
                        }
                    }
                }
            }

            $getRequest = new Request ();
            $getRequest->setUri ( ADDINVOICELOCAL );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if(array_key_exists('img_string', $data))
            {
                $getRequest2 = new Request ();
                $getRequest2->setUri ( UPLOADSLIP );
                $getRequest2->setMethod ( 'POST' );
                $getRequest2->setPost ( new Parameters ( $data ) );
                $client2 = new Client ();
                $response2 = $client2->send($getRequest2)->getBody();
            }            
		

            if ($r['error'] == ""){
                $m = "Invoice Generated Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bookings');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
    }


    public function viewBookingBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data ['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( BOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('booking' => $response,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function viewBusBookingBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data ['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( BUSBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('booking' => $response,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    //Invoices
    public function businesstaxivaxiInvoicesAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            if(!$type)
                $data['type'] = '1';
            else
                $data ['type'] = $type;
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLTAXIVAXIINVOICES );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response, 'type' => $data['type'],));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }


    public function viewRadioinvoiceBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data ['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( VIEWINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('invoice' => $response,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function viewOutstationinvoiceBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data ['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( VIEWINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('invoice' => $response,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function adddutyslipBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi']))
        {
            $request = $this->getRequest ();
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $request->getPost('booking_id');

            //Slip Image
            if(isset($_FILES['image']))
            {
                if($_FILES['image']['tmp_name'])
                {
                    if(!$_FILES['image']['error'])
                    {
                        $inputFile = $_FILES['image']['name'];
                        $extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
                        $extension_str = pathinfo($inputFile, PATHINFO_EXTENSION);

                        $data_str = file_get_contents($_FILES['image']['tmp_name']);
                        $data_str = base64_encode($data_str);

                        if($extension == 'JPG' || $extension == 'JPEG' || $extension == 'PNG'){
                            $data['img_string'] = "data:image/".$extension_str.";base64,".$data_str;
                            $data['img_ext'] = $extension_str;
                        }
                        else
                        {
                            $mess = 'Not supported file format';
                        }
                    }
                    else
                    {
                        $mess = 'Some error occured';
                    }
                }
                else
                {
                    $mess = 'Temp issue';
                }
            }
            else
            {
                $mess = 'No file Selected';
            }

            $getRequest = new Request ();
            $getRequest->setUri ( UPLOADSLIP );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){

                $m = "Payin Slip Added Successfully Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-invoices');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }
    
    public function addbusticketAction()
    {
        if(isset($_COOKIE['business_taxivaxi']))
        {
            $request = $this->getRequest ();
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['invoice_id'] = $request->getPost('invoice_id');

            //Slip Image
            if(isset($_FILES['image']))
            {
                if($_FILES['image']['tmp_name'])
                {
                    if(!$_FILES['image']['error'])
                    {
                        $inputFile = $_FILES['image']['name'];
                        $extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
                        $extension_str = pathinfo($inputFile, PATHINFO_EXTENSION);

                        $data_str = file_get_contents($_FILES['image']['tmp_name']);
                        $data_str = base64_encode($data_str);

                        if($extension == 'JPG' || $extension == 'JPEG' || $extension == 'PNG'){
                            $data['img_string'] = "data:image/".$extension_str.";base64,".$data_str;
                            $data['img_ext'] = $extension_str;
                        }
                
                        else
                        {
                            $mess = 'Not supported file format';
                        }
                    }
                    else
                    {
                        $mess = 'Some error occured';
                    }
                }
                else
                {
                    $mess = 'Temp issue';
                }
            }
            else
            {
                $mess = 'No file Selected';
            }

            $getRequest = new Request ();
            $getRequest->setUri ( UPLOADBUSTICKET );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){

                $m = "Bus Ticket Added Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bus-invoices');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }
    
    public function resetBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data ['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( RESETBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Booking Assigned Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4);
                return $this->redirect()->toRoute('businesstaxivaxi-bookings');
            }
            else{
                $m = $r['error'];

                $cookie_name4 = "fail";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4);
                return $this->redirect()->toRoute('businesstaxivaxi-bookings');
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

	public function businesstaxivaxiBusInvoicesAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            if(!$type)
                $data['type'] = '1';
            else
                $data ['type'] = $type;
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLBUSINVOICES );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response, 'type' => $data['type'],));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }





    public function getAddBusinvoiceBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( BUSBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('booking' => $response, 'booking_id' => $booking_id,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    

    public function postAddBusinvoiceBusinesstaxivaxiAction()
    {
            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $request = $this->getRequest ();

            $data['booking_id'] = $request->getPost('booking_id');
            
            $data['total'] = $request->getPost('total');
            $data['taxivaxi_charge'] = $request->getPost('taxivaxi_charge');
            $data['taxivaxi_tax_rate'] = $request->getPost('taxivaxi_tax_rate');
            $data['taxivaxi_tax_charge'] = $request->getPost('taxivaxi_tax_charge');
            $data['sub_total'] = $request->getPost('sub_total');            

            $getRequest = new Request ();
            $getRequest->setUri ( ADDBUSINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Invoice Generated Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bus-bookings-old');
            }
            else{
                $m = $r['error'];

                $cookie_name4 = "fail";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bus-bookings-old');
            }
    }

    public function getEditBusinvoiceBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $invoice_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $invoice_id;

            $getRequest = new Request ();
            $getRequest->setUri ( BUSBOOKINGBYINVOICEID );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();

            $getRequest = new Request ();
            $getRequest->setUri ( VIEWBUSINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response2 = $client->send($getRequest)->getBody();
            $r = json_decode($response2, true);

            if ($r['error'] == ""){
                return new ViewModel(array('booking' => $response, 'invoice' => $response2, 'invoice_id' => $invoice_id,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    

    public function postEditBusinvoiceBusinesstaxivaxiAction()
    {
            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $request = $this->getRequest ();

            $data['invoice_id'] = $request->getPost('invoice_id');

            $data['total'] = $request->getPost('total');
            $data['taxivaxi_charge'] = $request->getPost('taxivaxi_charge');
            $data['taxivaxi_tax_rate'] = $request->getPost('taxivaxi_tax_rate');
            $data['taxivaxi_tax_charge'] = $request->getPost('taxivaxi_tax_charge');
            $data['sub_total'] = $request->getPost('sub_total');            
            
            $getRequest = new Request ();
            $getRequest->setUri ( EDITBUSINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Invoice Edited Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-bus-invoices');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-invoices.phtml'); // path to phtml file under view folder
                return $view;
            }
    }



    public function viewBusinvoiceBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data ['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( VIEWBUSINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('invoice' => $response,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

	public function downloadUnassignedBookingReportAction()
    {
        if(isset($_COOKIE['business_taxivaxi']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['type'] = '1';
            
            $getRequest = new Request ();
            $getRequest->setUri ( BOOKINGREPORT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);
            
            //var_dump($r); die;

              $data = $r['response']['Bookings'];

             function cleanData(&$str)
              {
                $str = preg_replace("/\t/", "\\t", $str);
                $str = preg_replace("/\r?\n/", "\\n", $str);
                if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
              }

              $filename = "Bookings_Unassigned_" . time() . ".xls";

              header("Content-Disposition: attachment; filename=\"$filename\"");
              header("Content-Type: application/vnd.ms-excel");

              $flag = false;
              foreach($data as $row) 
              {
                if(!$flag) {
                  echo implode("\t", array_keys($row)) . "\r\n";
                  $flag = true;
                }
                echo implode("\t", array_values($row)) . "\r\n";
              }
              exit;
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

	public function downloadAssignedBookingReportAction()
    {
        if(isset($_COOKIE['business_taxivaxi']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['type'] = '4';
            
            $getRequest = new Request ();
            $getRequest->setUri ( BOOKINGREPORT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);
            
            //var_dump($r); die;

              $data = $r['response']['Bookings'];

             function cleanData(&$str)
              {
                $str = preg_replace("/\t/", "\\t", $str);
                $str = preg_replace("/\r?\n/", "\\n", $str);
                if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
              }

              $filename = "Bookings_Assigned_" . time() . ".xls";

              header("Content-Disposition: attachment; filename=\"$filename\"");
              header("Content-Type: application/vnd.ms-excel");

              $flag = false;
              foreach($data as $row) 
              {
                if(!$flag) {
                  echo implode("\t", array_keys($row)) . "\r\n";
                  $flag = true;
                }
                echo implode("\t", array_values($row)) . "\r\n";
              }
              exit;
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

	public function downloadArchivedBookingReportAction()
    {
        if(isset($_COOKIE['business_taxivaxi']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['type'] = '2';
            
            $getRequest = new Request ();
            $getRequest->setUri ( BOOKINGREPORT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);
            
            //var_dump($r); die;

              $data = $r['response']['Bookings'];

             function cleanData(&$str)
              {
                $str = preg_replace("/\t/", "\\t", $str);
                $str = preg_replace("/\r?\n/", "\\n", $str);
                if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
              }

              $filename = "Bookings_Archived_" . time() . ".xls";

              header("Content-Disposition: attachment; filename=\"$filename\"");
              header("Content-Type: application/vnd.ms-excel");

              $flag = false;
              foreach($data as $row) 
              {
                if(!$flag) {
                  echo implode("\t", array_keys($row)) . "\r\n";
                  $flag = true;
                }
                echo implode("\t", array_values($row)) . "\r\n";
              }
              exit;
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }
    
    public function downloadCancelledBookingReportAction()
    {
        if(isset($_COOKIE['business_taxivaxi']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['type'] = '3';
            
            $getRequest = new Request ();
            $getRequest->setUri ( BOOKINGREPORT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);
            
            //var_dump($r); die;

              $data = $r['response']['Bookings'];

             function cleanData(&$str)
              {
                $str = preg_replace("/\t/", "\\t", $str);
                $str = preg_replace("/\r?\n/", "\\n", $str);
                if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
              }

              $filename = "Bookings_Cancelled_" . time() . ".xls";

              header("Content-Disposition: attachment; filename=\"$filename\"");
              header("Content-Type: application/vnd.ms-excel");

              $flag = false;
              foreach($data as $row) 
              {
                if(!$flag) {
                  echo implode("\t", array_keys($row)) . "\r\n";
                  $flag = true;
                }
                echo implode("\t", array_values($row)) . "\r\n";
              }
              exit;
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    //Billings
    public function businesstaxivaxiBillsAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            //1-Unpaid, 2-Paid
            if(!$type)
                $data['type'] = '1';
            else
                $data ['type'] = $type;

            $data['user'] = 'Taxivaxi';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLTAXIVAXIBILLS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bills' => $response, 'type' => $data['type'],));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function commentInvoiceTaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['invoice_id'] = $request->getPost('invoice_id');
            $data['comment'] = $request->getPost('comment');

            $getRequest = new Request ();
            $getRequest->setUri ( COMMENTINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Commented Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);
                return $this->redirect()->toRoute('businesstaxivaxi-invoices', array('type' => 3));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function commentBusInvoiceTaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['invoice_id'] = $request->getPost('invoice_id');
            $data['comment'] = $request->getPost('comment');

            $getRequest = new Request ();
            $getRequest->setUri ( COMMENTBUSINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Commented Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);
                return $this->redirect()->toRoute('businesstaxivaxi-bus-invoices', array('type' => 3));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-bus-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function viewBusinesstaxivaxiBillAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $bill_id = $this->getEvent()->getRouteMatch()->getParam('bill_id');

            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['bill_id'] = $bill_id;
            
            $getRequest = new Request ();
            $getRequest->setUri ( VIEWBILL );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('details' => $response, 'bill_id'=>$bill_id));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function viewBusinesstaxivaxiBusBillAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $bill_id = $this->getEvent()->getRouteMatch()->getParam('bill_id');

            $data['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['bill_id'] = $bill_id;
            
            $getRequest = new Request ();
            $getRequest->setUri ( VIEWBILL );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('details' => $response, 'bill_id'=>$bill_id));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    //Edit Invoices
    public function getEditRadioinvoiceBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $invoice_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $invoice_id;

            $getRequest = new Request ();
            $getRequest->setUri ( VIEWINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('invoice' => $response, 'invoice_id' => $invoice_id,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    

    public function postEditRadioinvoiceBusinesstaxivaxiAction()
    {
        $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
        $request = $this->getRequest ();

        $data['invoice_id'] = $request->getPost('invoice_id');

        
        $data['total'] = $request->getPost('total');
        $data['taxivaxi_rate'] = 10;
        $data['taxivaxi_charge'] = $request->getPost('taxivaxi_charge');
        $data['taxivaxi_tax_rate'] = $request->getPost('taxivaxi_tax_rate');
        $data['taxivaxi_tax_charge'] = $request->getPost('taxivaxi_tax_charge');
        $data['sub_total'] = $request->getPost('sub_total');            

        $getRequest = new Request ();
        $getRequest->setUri ( EDITINVOICERADIO );
        $getRequest->setMethod ( 'POST' );
        $getRequest->setPost ( new Parameters ( $data ) );
        $client = new Client ();
        $response = $client->send($getRequest)->getBody();
        $r = json_decode($response, true);

        if ($r['error'] == ""){
            $m = "Invoice Edited Successfully";

            $cookie_name4 = "success";
            $cookie_value4 = $m;
            setcookie($cookie_name4, $cookie_value4, time()+2);

            return $this->redirect()->toRoute('businesstaxivaxi-invoices');
            
        }
        else{
            $m = $r['error'];

            $view = new ViewModel(array('mess'=>$m));
            $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-invoices.phtml'); // path to phtml file under view folder
            return $view;
        }
    }


    public function getEditOutstationinvoiceBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $invoice_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $invoice_id;

            $getRequest = new Request ();
            $getRequest->setUri ( VIEWINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('invoice' => $response, 'invoice_id' => $invoice_id,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    

    public function postEditOutstationinvoiceBusinesstaxivaxiAction()
    {
            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $request = $this->getRequest ();

            $data['invoice_id'] = $request->getPost('invoice_id');

            $data['pickup_date'] = $request->getPost('pickup_date');
            $data['pickup_time'] = $request->getPost('pickup_time');
            $data['drop_date'] = $request->getPost('drop_date');

            $data['drop_time'] = $request->getPost('drop_time');
            $data['hours_done'] = $request->getPost('hours_done');
            $data['allowed_hrs'] = $request->getPost('allowed_hrs');
            $data['extra_hours'] = $request->getPost('extra_hours');
            $data['hour_rate'] = $request->getPost('hour_rate');
            $data['extra_hours_charge'] = $request->getPost('extra_hours_charge');
            $data['start_km'] = $request->getPost('start_km');

            $data['end_km'] = $request->getPost('end_km');
            $data['kms_done'] = $request->getPost('kms_done');
            $data['allowed_kms'] = $request->getPost('allowed_kms');
            $data['extra_kms'] = $request->getPost('extra_kms');
            $data['km_rate'] = $request->getPost('km_rate');
            $data['extra_kms_charge'] = $request->getPost('extra_kms_charge');
            $data['parking'] = $request->getPost('parking');

            $data['driver'] = $request->getPost('driver');
            $data['base_rate'] = $request->getPost('base_rate');
            $data['total_ex_tax'] = $request->getPost('total_ex_tax');
            $data['tax_rate'] = $request->getPost('tax_rate');
            $data['tax'] = $request->getPost('tax');
            $data['total'] = $request->getPost('total');

            $data['taxivaxi_rate'] = '100';
            $data['taxivaxi_charge'] = $request->getPost('taxivaxi_charge');
            $data['taxivaxi_tax_rate'] = $request->getPost('taxivaxi_tax_rate');
            $data['taxivaxi_tax_charge'] = $request->getPost('taxivaxi_tax_charge');
            $data['sub_total'] = $request->getPost('sub_total');            
            
            $getRequest = new Request ();
            $getRequest->setUri ( EDITINVOICEOUTSTATION );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Invoice Edited Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-invoices');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-invoices.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    public function getEditLocalinvoiceBusinesstaxivaxiAction()
    {
        if(isset($_COOKIE['business_taxivaxi'])){
            $invoice_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $data['booking_id'] = $invoice_id;

            $getRequest = new Request ();
            $getRequest->setUri ( VIEWINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return new ViewModel(array('invoice' => $response, 'invoice_id' => $invoice_id,));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    

    public function postEditLocalinvoiceBusinesstaxivaxiAction()
    {
            $data ['access_token'] = $_COOKIE['access_token_business_taxivaxi'];
            $request = $this->getRequest ();

            $data['invoice_id'] = $request->getPost('invoice_id');

            $data['pickup_date'] = $request->getPost('pickup_date');
            $data['pickup_time'] = $request->getPost('pickup_time');
            $data['drop_date'] = $request->getPost('drop_date');
            $data['drop_time'] = $request->getPost('drop_time');
            $data['hours_done'] = $request->getPost('hours_done');
            $data['allowed_hrs'] = $request->getPost('allowed_hrs');
            $data['extra_hours'] = $request->getPost('extra_hours');
            $data['hour_rate'] = $request->getPost('hour_rate');
            $data['extra_hours_charge'] = $request->getPost('extra_hours_charge');

            $data['start_km'] = $request->getPost('start_km');
            $data['end_km'] = $request->getPost('end_km');
            $data['kms_done'] = $request->getPost('kms_done');
            $data['allowed_kms'] = $request->getPost('allowed_kms');
            $data['extra_kms'] = $request->getPost('extra_kms');
            $data['km_rate'] = $request->getPost('km_rate');
            $data['extra_kms_charge'] = $request->getPost('extra_kms_charge');
            
            $data['parking'] = $request->getPost('parking');
            $data['driver'] = $request->getPost('driver');
            $data['base_rate'] = $request->getPost('base_rate');
            $data['total_ex_tax'] = $request->getPost('total_ex_tax');
            $data['tax_rate'] = $request->getPost('tax_rate');
            $data['tax'] = $request->getPost('tax');
            $data['total'] = $request->getPost('total');

            $data['taxivaxi_rate'] = $request->getPost('taxivaxi_rate');
            $data['taxivaxi_charge'] = $request->getPost('taxivaxi_charge');
            $data['taxivaxi_tax_rate'] = $request->getPost('taxivaxi_tax_rate');
            $data['taxivaxi_tax_charge'] = $request->getPost('taxivaxi_tax_charge');
            $data['sub_total'] = $request->getPost('sub_total');            
            
            $getRequest = new Request ();
            $getRequest->setUri ( EDITINVOICELOCAL );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Invoice Edited Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businesstaxivaxi-invoices');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businesstaxivaxi/businesstaxivaxi/businesstaxivaxi-invoices.phtml'); // path to phtml file under view folder
                return $view;
            }
    }
    
}
