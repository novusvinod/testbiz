<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Businessauthtwo\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use PHPExcel_IOFactory;

define ( 'BUSINESSAUTHTWOHOME', 'http://taxivaxi.in/business/api/authtwoHome');

define ( 'BUSINESSAUTHTWOPROFILE', 'http://taxivaxi.in/business/api/viewAuthtwoProfile' );
define ( 'CHANGEPASSWORD', 'http://taxivaxi.in/business/api/authtwoChangePassword' );
define ( 'BUSINESSAUTHTWOLOGIN', 'http://taxivaxi.in/business/api/authtwologin' );
define ( 'BUSINESSAUTHTWOLOGOUT', 'http://taxivaxi.in/business/api/authtwologout' );
define ( 'BUSINESSAUTHTWOFORGOT', 'http://taxivaxi.in/business/api/authtwoforgot' );

define ( 'ALLAUTHTWOBOOKINGS', 'http://taxivaxi.in/business/api/getAllAuthtwoBookings' );
define ( 'ALLAUTHTWOBUSBOOKINGS', 'http://taxivaxi.in/business/api/getAllAuthtwoBusBookings' );
define ( 'BOOKING', 'http://taxivaxi.in/business/api/viewBookingTaxivaxi' );
define ( 'BUSBOOKING', 'http://taxivaxi.in/business/api/viewBusBookingTaxivaxi' );
define ( 'ACCEPTBOOKING', 'http://taxivaxi.in/business/api/acceptAuthtwoBooking' );
define ( 'ACCEPTBUSBOOKING', 'http://taxivaxi.in/business/api/acceptAuthtwoBusBooking' );
define ( 'REJECTBUSBOOKING', 'http://taxivaxi.in/business/api/rejectAuthtwoBusBooking' );

define ( 'ALLAUTHTWOINVOICES', 'http://taxivaxi.in/business/api/getAllAuthtwoInvoices' );
define ( 'VIEWINVOICE', 'http://taxivaxi.in/business/api/viewInvoice' );

class BusinessauthtwoController extends AbstractActionController
{

    public function getBusinessauthtwoLoginAction()
    {
        if(!isset($_COOKIE['business_authtwo']))
        {
            return new ViewModel();
        }
        else
            return $this->redirect()->toRoute('businessauthtwo-home');
    }

    public function postBusinessauthtwoLoginAction()
    {
            $request = $this->getRequest ();

            $data['username'] = $request->getPost('username');
            $data['password'] = $request->getPost('password');

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSAUTHTWOLOGIN );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            /*return new ViewModel(array('response' => $response,));*/

            if ($r['error'] == ""){
                $cookie_name = "access_token_business_authtwo";
                $cookie_value = $r['response']['access_token'];
                setcookie($cookie_name, $cookie_value, 0, "/");

                $cookie_name2 = "business_authtwo";
                $cookie_value2 = 1;
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "business_authtwo_name";
                $cookie_value2 = $r['response']['user']['name'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "business_authtwo_email";
                $cookie_value2 = $r['response']['user']['email'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "business_authtwo_budget";
                $cookie_value2 = $r['response']['user']['budget'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "admin_id_business_authtwo";
                $cookie_value2 = $r['response']['user']['admin_id'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "subgroup_id_business_authtwo";
                $cookie_value2 = $r['response']['user']['group_id'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_local_business_authtwo";
                $cookie_value2 = $r['response']['user']['is_local'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_outstation_business_authtwo";
                $cookie_value2 = $r['response']['user']['is_outstation'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_radio_business_authtwo";
                $cookie_value2 = $r['response']['user']['is_radio'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_bus_business_authtwo";
                $cookie_value2 = $r['response']['user']['is_bus'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "has_auth_level_business_authtwo";
                $cookie_value2 = $r['response']['user']['has_auth_level'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "has_assessment_codes_business_authtwo";
                $cookie_value2 = $r['response']['user']['has_assessment_codes'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");
                
                return $this->redirect()->toRoute('businessauthtwo-home');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthtwo/businessauthtwo/get-businessauthtwo-login.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    public function businessauthtwoHomeAction()
    {
        if(isset($_COOKIE['business_authtwo']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_authtwo'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSAUTHTWOHOME );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();

            // return new ViewModel();
            return new ViewModel(array('details' => $response,));
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function getBusinessauthtwoProfileAction()
    {
        if(isset($_COOKIE['business_authtwo']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_authtwo'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSAUTHTWOPROFILE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();

            // return new ViewModel();
            return new ViewModel(array('details' => $response,));
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function businessauthtwoChangepasswordAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_authtwo'];
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
                
                return $this->redirect()->toRoute('get-businessauthtwo-profile');
            }
            else{
                $m = $r['error'];

                $cookie_name4 = "fail";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);       
                
                return $this->redirect()->toRoute('get-businessauthtwo-profile');
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function businessauthtwoLogoutAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_authtwo'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSAUTHTWOLOGOUT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                unset($_COOKIE['access_token_business_authtwo']);
                setcookie('access_token_business_authtwo', '', time() - 3600);

                unset($_COOKIE['business_authtwo']);
                setcookie('business_authtwo', '', time() - 3600);

                unset($_COOKIE['business_authtwo_name']);
                setcookie('business_authtwo_name', '', time() - 3600);

                unset($_COOKIE['business_authtwo_email']);
                setcookie('business_authtwo_email', '', time() - 3600);

                unset($_COOKIE['business_authtwo_budget']);
                setcookie('business_authtwo_budget', '', time() - 3600);

                unset($_COOKIE['admin_id_business_authtwo']);
                setcookie('admin_id_business_authtwo', '', time() - 3600);

                unset($_COOKIE['subgroup_id_business_authtwo']);
                setcookie('subgroup_id_business_authtwo', '', time() - 3600);

                unset($_COOKIE['is_local_business_authtwo']);
                setcookie('is_local_business_authtwo', '', time() - 3600);

                unset($_COOKIE['is_outstation_business_authtwo']);
                setcookie('is_outstation_business_authtwo', '', time() - 3600);

                unset($_COOKIE['is_radio_business_authtwo']);
                setcookie('is_radio_business_authtwo', '', time() - 3600);

                unset($_COOKIE['is_bus_business_authtwo']);
                setcookie('is_bus_business_authtwo', '', time() - 3600);

                unset($_COOKIE['has_auth_level_business_authtwo']);
                setcookie('has_auth_level_business_authtwo', '', time() - 3600);

                unset($_COOKIE['has_assessment_codes_business_authtwo']);
                setcookie('has_assessment_codes_business_authtwo', '', time() - 3600);
                
                return $this->redirect()->toRoute('businessauthtwo-home');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function postBusinessauthtwoForgotAction()
    {
            $request = $this->getRequest ();

            $data['username'] = $request->getPost('username');

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSAUTHTWOFORGOT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                
                $m = 'New Passorword Sent Successfully on '.$data['username'];

                $view = new ViewModel(array('mess_success'=>$m));
                $view->setTemplate('businessauthtwo/businessauthtwo/get-businessauthtwo-login.phtml'); // path to phtml file under view folder
                return $view;
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthtwo/businessauthtwo/get-businessauthtwo-login.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    //Bookings
    public function BusinessauthtwoBookingsAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $data['access_token'] = $_COOKIE['access_token_business_authtwo'];
            $data['admin_id'] = $_COOKIE['admin_id_business_authtwo'];
            $data['type'] = '1';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHTWOBOOKINGS );
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
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function BusinessauthtwoBusBookingsAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $data['access_token'] = $_COOKIE['access_token_business_authtwo'];
            $data['admin_id'] = $_COOKIE['admin_id_business_authtwo'];
            $data['type'] = '1';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHTWOBUSBOOKINGS );
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
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function BusinessauthtwoBookingsOldAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $data['access_token'] = $_COOKIE['access_token_business_authtwo'];
            $data['admin_id'] = $_COOKIE['admin_id_business_authtwo'];
            $data['type'] = '2';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHTWOBOOKINGS );
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
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-bookings-old.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function BusinessauthtwoBusBookingsOldAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $data['access_token'] = $_COOKIE['access_token_business_authtwo'];
            $data['admin_id'] = $_COOKIE['admin_id_business_authtwo'];
            $data['type'] = '2';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHTWOBUSBOOKINGS );
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
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-bus-bookings-old.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function BusinessauthtwoRejectedBookingsAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $data['access_token'] = $_COOKIE['access_token_business_authtwo'];
            $data['admin_id'] = $_COOKIE['admin_id_business_authtwo'];
            $data['type'] = '3';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHTWOBOOKINGS );
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
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-rejected-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function BusinessauthtwoRejectedBusBookingsAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $data['access_token'] = $_COOKIE['access_token_business_authtwo'];
            $data['admin_id'] = $_COOKIE['admin_id_business_authtwo'];
            $data['type'] = '3';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHTWOBUSBOOKINGS );
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
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-rejected-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }
    
    public function acceptBusinessauthtwoBookingAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authtwo'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( ACCEPTBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessauthtwo-bus-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function acceptBusinessauthtwoBusBookingAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authtwo'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( ACCEPTBUSBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessauthtwo-bus-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function rejectBusinessauthtwoBookingAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $request = $this->getRequest ();
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authtwo'];
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
                return $this->redirect()->toRoute('businessauthtwo-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function rejectBusinessauthtwoBusBookingAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $request = $this->getRequest ();
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authtwo'];
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
                return $this->redirect()->toRoute('businessauthtwo-bus-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function rejectreasonBusinessauthtwoBookingAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            return new ViewModel(array('booking_id' => $booking_id));
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function rejectreasonBusinessauthtwoBusBookingAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            return new ViewModel(array('booking_id' => $booking_id));
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function viewBookingBusinessauthtwoAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authtwo'];
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
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function viewBusBookingBusinessauthtwoAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authtwo'];
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
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-bus-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    //Invoices
    public function businessauthtwoInvoicesAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $data['access_token'] = $_COOKIE['access_token_business_authtwo'];
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLAUTHTWOINVOICES );
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
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }

    public function viewRadioinvoiceBusinessauthtwoAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authtwo'];
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
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function viewOutstationinvoiceBusinessauthtwoAction()
    {
        if(isset($_COOKIE['business_authtwo'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_authtwo'];
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
                $view->setTemplate('businessauthtwo/businessauthtwo/businessauthtwo-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessauthtwo-login');
    }
}
