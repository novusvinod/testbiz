<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Businessauthone\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use PHPExcel_IOFactory;

define ( 'BUSINESSAUTHONEHOME', 'http://taxivaxi.in/business/api/authoneHome');

define ( 'BUSINESSAUTHONEPROFILE', 'http://taxivaxi.in/business/api/viewAuthoneProfile' );
define ( 'CHANGEPASSWORD', 'http://taxivaxi.in/business/api/authoneChangePassword' );
define ( 'BUSINESSAUTHONELOGIN', 'http://taxivaxi.in/business/api/authonelogin' );
define ( 'BUSINESSAUTHONELOGOUT', 'http://taxivaxi.in/business/api/authonelogout' );
define ( 'BUSINESSAUTHONEFORGOT', 'http://taxivaxi.in/business/api/authoneforgot' );

define ( 'ALLAUTHONEBOOKINGS', 'http://taxivaxi.in/business/api/getAllAuthoneBookings' );
define ( 'ALLAUTHONEBUSBOOKINGS', 'http://taxivaxi.in/business/api/getAllAuthoneBusBookings' );
define ( 'BOOKING', 'http://taxivaxi.in/business/api/viewBookingTaxivaxi' );
define ( 'BUSBOOKING', 'http://taxivaxi.in/business/api/viewBusBookingTaxivaxi' );
define ( 'ACCEPTBOOKING', 'http://taxivaxi.in/business/api/acceptAuthoneBooking' );
define ( 'REJECTBOOKING', 'http://taxivaxi.in/business/api/rejectAuthoneBooking' );
define ( 'ACCEPTBUSBOOKING', 'http://taxivaxi.in/business/api/acceptAuthoneBusBooking' );
define ( 'REJECTBUSBOOKING', 'http://taxivaxi.in/business/api/rejectAuthoneBusBooking' );

define ( 'ALLAUTHONEINVOICES', 'http://taxivaxi.in/business/api/getAllAuthoneInvoices' );
define ( 'VIEWINVOICE', 'http://taxivaxi.in/business/api/viewInvoice' );

class BusinessauthoneController extends AbstractActionController
{

    public function getBusinessauthoneLoginAction()
    {
        if(!isset($_COOKIE['business_authone']))
        {
            return new ViewModel();
        }
        else
            return $this->redirect()->toRoute('businessauthone-home');
    }

    public function postBusinessauthoneLoginAction()
    {
            $request = $this->getRequest ();

            $data['username'] = $request->getPost('username');
            $data['password'] = $request->getPost('password');

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSAUTHONELOGIN );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            /*return new ViewModel(array('response' => $response,));*/

            if ($r['error'] == ""){
                $cookie_name = "access_token_business_authone";
                $cookie_value = $r['response']['access_token'];
                setcookie($cookie_name, $cookie_value, 0, "/");

                $cookie_name2 = "business_authone";
                $cookie_value2 = 1;
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "business_authone_name";
                $cookie_value2 = $r['response']['user']['name'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "business_authone_email";
                $cookie_value2 = $r['response']['user']['email'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "business_authone_budget";
                $cookie_value2 = $r['response']['user']['budget'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "admin_id_business_authone";
                $cookie_value2 = $r['response']['user']['admin_id'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "subgroup_id_business_authone";
                $cookie_value2 = $r['response']['user']['subgroup_id'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_local_business_authone";
                $cookie_value2 = $r['response']['user']['is_local'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_outstation_business_authone";
                $cookie_value2 = $r['response']['user']['is_outstation'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_radio_business_authone";
                $cookie_value2 = $r['response']['user']['is_radio'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_bus_business_authone";
                $cookie_value2 = $r['response']['user']['is_bus'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "has_auth_level_business_authone";
                $cookie_value2 = $r['response']['user']['has_auth_level'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "has_assessment_codes_business_authone";
                $cookie_value2 = $r['response']['user']['has_assessment_codes'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");
                
                return $this->redirect()->toRoute('businessauthone-home');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthone/businessauthone/get-businessauthone-login.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    public function businessauthoneHomeAction()
    {
        if(isset($_COOKIE['business_authone']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_authone'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSAUTHONEHOME );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();

            // return new ViewModel();
            return new ViewModel(array('details' => $response,));
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function getBusinessauthoneProfileAction()
    {
        if(isset($_COOKIE['business_authone']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_authone'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSAUTHONEPROFILE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();

            // return new ViewModel();
            return new ViewModel(array('details' => $response,));
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function businessauthoneChangepasswordAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_authone'];
            $data['current_password'] = $request->getPost('current_password');
            $data['new_password'] = $request->getPost('new_password');
            $data['confirm_new_password'] = $request->getPost('confirm_new_password');

            $getRequest = new Request ();
            $getRequest->setUri ( CHANGEPASSWORD );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Password Updated Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);       
                
                return $this->redirect()->toRoute('get-businessauthone-profile');
            }
            else{
                $m = $r['error'];

                $cookie_name4 = "fail";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);       
                
                return $this->redirect()->toRoute('get-businessauthone-profile');
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function businessauthoneLogoutAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_authone'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSAUTHONELOGOUT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                unset($_COOKIE['access_token_business_authone']);
                setcookie('access_token_business_authone', '', time() - 3600);

                unset($_COOKIE['business_authone']);
                setcookie('business_authone', '', time() - 3600);

                unset($_COOKIE['business_authone_name']);
                setcookie('business_authone_name', '', time() - 3600);

                unset($_COOKIE['business_authone_email']);
                setcookie('business_authone_email', '', time() - 3600);

                unset($_COOKIE['business_authone_budget']);
                setcookie('business_authone_budget', '', time() - 3600);

                unset($_COOKIE['admin_id_business_authone']);
                setcookie('admin_id_business_authone', '', time() - 3600);

                unset($_COOKIE['subgroup_id_business_authone']);
                setcookie('subgroup_id_business_authone', '', time() - 3600);

                unset($_COOKIE['is_local_business_authone']);
                setcookie('is_local_business_authone', '', time() - 3600);

                unset($_COOKIE['is_outstation_business_authone']);
                setcookie('is_outstation_business_authone', '', time() - 3600);

                unset($_COOKIE['is_radio_business_authone']);
                setcookie('is_radio_business_authone', '', time() - 3600);

                unset($_COOKIE['is_bus_business_authone']);
                setcookie('is_bus_business_authone', '', time() - 3600);

                unset($_COOKIE['has_auth_level_business_authone']);
                setcookie('has_auth_level_business_authone', '', time() - 3600);

                unset($_COOKIE['has_assessment_codes_business_authone']);
                setcookie('has_assessment_codes_business_authone', '', time() - 3600);
                
                return $this->redirect()->toRoute('businessauthone-home');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthone/businessauthone/businessauthone-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function postBusinessauthoneForgotAction()
    {
            $request = $this->getRequest ();

            $data['username'] = $request->getPost('username');

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSAUTHONEFORGOT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                
                $m = 'New Passorword Sent Successfully on '.$data['username'];

                $view = new ViewModel(array('mess_success'=>$m));
                $view->setTemplate('businessauthone/businessauthone/get-businessauthone-login.phtml'); // path to phtml file under view folder
                return $view;
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthone/businessauthone/get-businessauthone-login.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    //Bookings
    public function BusinessauthoneBookingsAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $data['access_token'] = $_COOKIE['access_token_business_authone'];
            $data['admin_id'] = $_COOKIE['admin_id_business_authone'];
            $data['type'] = '1';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHONEBOOKINGS );
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
                $view->setTemplate('businessauthone/businessauthone/businessauthone-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function BusinessauthoneBusBookingsAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $data['access_token'] = $_COOKIE['access_token_business_authone'];
            $data['admin_id'] = $_COOKIE['admin_id_business_authone'];
            $data['type'] = '1';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHONEBUSBOOKINGS );
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
                $view->setTemplate('businessauthone/businessauthone/businessauthone-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function BusinessauthoneBookingsOldAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $data['access_token'] = $_COOKIE['access_token_business_authone'];
            $data['admin_id'] = $_COOKIE['admin_id_business_authone'];
            $data['type'] = '2';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHONEBOOKINGS );
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
                $view->setTemplate('businessauthone/businessauthone/businessauthone-bookings-old.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function BusinessauthoneBusBookingsOldAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $data['access_token'] = $_COOKIE['access_token_business_authone'];
            $data['admin_id'] = $_COOKIE['admin_id_business_authone'];
            $data['type'] = '2';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHONEBUSBOOKINGS );
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
                $view->setTemplate('businessauthone/businessauthone/businessauthone-bus-bookings-old.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function BusinessauthoneRejectedBookingsAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $data['access_token'] = $_COOKIE['access_token_business_authone'];
            $data['admin_id'] = $_COOKIE['admin_id_business_authone'];
            $data['type'] = '3';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHONEBOOKINGS );
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
                $view->setTemplate('businessauthone/businessauthone/businessauthone-rejected-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function BusinessauthoneRejectedBusBookingsAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $data['access_token'] = $_COOKIE['access_token_business_authone'];
            $data['admin_id'] = $_COOKIE['admin_id_business_authone'];
            $data['type'] = '3';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHONEBUSBOOKINGS );
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
                $view->setTemplate('businessauthone/businessauthone/businessauthone-rejected-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }
    
    public function acceptBusinessauthoneBookingAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authone'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( ACCEPTBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessauthone-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthone/businessauthone/businessauthone-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function acceptBusinessauthoneBusBookingAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authone'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( ACCEPTBUSBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessauthone-bus-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthone/businessauthone/businessauthone-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function rejectBusinessauthoneBookingAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $request = $this->getRequest ();
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authone'];
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
                return $this->redirect()->toRoute('businessauthone-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthone/businessauthone/businessauthone-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function rejectBusinessauthoneBusBookingAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $request = $this->getRequest ();
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authone'];
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
                return $this->redirect()->toRoute('businessauthone-bus-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthone/businessauthone/businessauthone-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function rejectreasonBusinessauthoneBookingAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            return new ViewModel(array('booking_id' => $booking_id));
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function rejectreasonBusinessauthoneBusBookingAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            return new ViewModel(array('booking_id' => $booking_id));
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function viewBookingBusinessauthoneAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authone'];
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
                $view->setTemplate('businessauthone/businessauthone/businessauthone-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function viewBusBookingBusinessauthoneAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authone'];
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
                $view->setTemplate('businessauthone/businessauthone/businessauthone-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    //Invoices
    public function businessauthoneInvoicesAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $data['access_token'] = $_COOKIE['access_token_business_authone'];
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHONEINVOICES );
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
                $view->setTemplate('businessauthone/businessauthone/businessauthone-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }

    public function viewRadioinvoiceBusinessauthoneAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authone'];
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
                $view->setTemplate('businessauthone/businessauthone/businessauthone-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function viewOutstationinvoiceBusinessauthoneAction()
    {
        if(isset($_COOKIE['business_authone'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authone'];
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
                $view->setTemplate('businessauthone/businessauthone/businessauthone-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthone-login');
    }
}
