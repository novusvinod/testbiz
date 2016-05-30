<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Businessspoc\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use PHPExcel_IOFactory;

define ( 'BUSINESSAUTHSPOCHOME', 'http://taxivaxi.in/business/api/spocHome');

define ( 'BUSINESSSPOCPROFILE', 'http://taxivaxi.in/business/api/viewSpocProfile' );
define ( 'CHANGEPASSWORD', 'http://taxivaxi.in/business/api/spocChangePassword' );
define ( 'BUSINESSSPOCLOGIN', 'http://taxivaxi.in/business/api/spoclogin' );
define ( 'BUSINESSSPOCLOGOUT', 'http://taxivaxi.in/business/api/spoclogout' );
define ( 'BUSINESSSPOCFORGOT', 'http://taxivaxi.in/business/api/spocforgot' );

define ( 'ALLSPOCBOOKINGS', 'http://taxivaxi.in/business/api/getAllSpocBookings' );
define ( 'ALLSPOCBUSBOOKINGS', 'http://taxivaxi.in/business/api/getAllSpocBusBookings' );
define ( 'ADDBOOKING', 'http://taxivaxi.in/business/api/addBooking' );
define ( 'ADDBUSBOOKING', 'http://taxivaxi.in/business/api/addBusBooking' );
define ( 'BOOKING', 'http://taxivaxi.in/business/api/viewBookingTaxivaxi' );
define ( 'BUSBOOKING', 'http://taxivaxi.in/business/api/viewBusBookingTaxivaxi' );
define ( 'EDITBOOKING', 'http://taxivaxi.in/business/api/editBooking' );
define ( 'REJECTBOOKING', 'http://taxivaxi.in/business/api/rejectSpocBooking' );
define ( 'REJECTBUSBOOKING', 'http://taxivaxi.in/business/api/rejectSpocBusBooking' );

define ( 'EMPLOYEEBYSPOC', 'http://taxivaxi.in/business/api/employeeBySpoc' );

define ( 'ALLCITIES', 'http://taxivaxi.in/business/api/getAllCities' );
define ( 'ALLTAXITYPES', 'http://taxivaxi.in/business/api/getAllTaxiTypes' );

define ( 'ALLSPOCINVOICES', 'http://taxivaxi.in/business/api/getAllSpocInvoices' );
define ( 'ALLSPOCBUSINVOICES', 'http://taxivaxi.in/business/api/getAllSpocBusInvoices' );

define ( 'VIEWINVOICE', 'http://taxivaxi.in/business/api/viewInvoice' );
define ( 'VIEWBUSINVOICE', 'http://taxivaxi.in/business/api/viewBusInvoice' );

define ( 'ASSESSMENTCODES', 'http://taxivaxi.in/business/api/getAllCodes' );

//Billings
define ( 'CLEARINVOICE', 'http://taxivaxi.in/business/api/spocClearInvoice' );
define ( 'COMMENTINVOICE', 'http://taxivaxi.in/business/api/spocCommentInvoice' );

define ( 'CLEARBUSINVOICE', 'http://taxivaxi.in/business/api/spocClearBusInvoice' );
define ( 'COMMENTBUSINVOICE', 'http://taxivaxi.in/business/api/spocCommentBusInvoice' );


class BusinessspocController extends AbstractActionController
{

    public function getBusinessspocLoginAction()
    {
        if(!isset($_COOKIE['business_spoc']))
        {
            return new ViewModel();
        }
        else
            return $this->redirect()->toRoute('businessspoc-home');
    }

    public function postBusinessspocLoginAction()
    {
            $request = $this->getRequest ();

            $data['username'] = $request->getPost('username');
            $data['password'] = $request->getPost('password');

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSSPOCLOGIN );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            /*return new ViewModel(array('response' => $response,));*/

            if ($r['error'] == ""){
                $cookie_name = "access_token_business_spoc";
                $cookie_value = $r['response']['access_token'];
                setcookie($cookie_name, $cookie_value, 0, "/");

                $cookie_name2 = "business_spoc";
                $cookie_value2 = 1;
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "business_user_name";
                $cookie_value2 = $r['response']['user']['user_name'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "business_user_email";
                $cookie_value2 = $r['response']['user']['email'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "business_spoc_budget";
                $cookie_value2 = $r['response']['user']['budget'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_local_business_spoc";
                $cookie_value2 = $r['response']['user']['is_local'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_outstation_business_spoc";
                $cookie_value2 = $r['response']['user']['is_outstation'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_radio_business_spoc";
                $cookie_value2 = $r['response']['user']['is_radio'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_bus_business_spoc";
                $cookie_value2 = $r['response']['user']['is_bus'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "admin_id_business_spoc";
                $cookie_value2 = $r['response']['user']['admin_id'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "group_id_business_spoc";
                $cookie_value2 = $r['response']['user']['group_id'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "subgroup_id_business_spoc";
                $cookie_value2 = $r['response']['user']['subgroup_id'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "has_auth_level_business_spoc";
                $cookie_value2 = $r['response']['user']['has_auth_level'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "has_assessment_codes_business_spoc";
                $cookie_value2 = $r['response']['user']['has_assessment_codes'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");
                
                return $this->redirect()->toRoute('businessspoc-home');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/get-businessspoc-login.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    public function businessspocHomeAction()
    {
        if(isset($_COOKIE['business_spoc']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_spoc'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSAUTHSPOCHOME );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();

            // return new ViewModel();
            return new ViewModel(array('details' => $response,));
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function getBusinessspocProfileAction()
    {
        if(isset($_COOKIE['business_spoc']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_spoc'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSSPOCPROFILE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();

            // return new ViewModel();
            return new ViewModel(array('details' => $response,));
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function businessspocChangepasswordAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_spoc'];
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
                
                return $this->redirect()->toRoute('get-businessspoc-profile');
            }
            else{
                $m = $r['error'];

                $cookie_name4 = "fail";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);       
                
                return $this->redirect()->toRoute('get-businessspoc-profile');
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function businessspocLogoutAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_spoc'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSSPOCLOGOUT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                unset($_COOKIE['access_token_business_spoc']);
                setcookie('access_token_business_spoc', '', time() - 3600);

                unset($_COOKIE['business_spoc']);
                setcookie('business_spoc', '', time() - 3600);

                unset($_COOKIE['business_user_name']);
                setcookie('business_user_name', '', time() - 3600);

                unset($_COOKIE['business_user_email']);
                setcookie('business_user_email', '', time() - 3600);

                unset($_COOKIE['business_spoc_budget']);
                setcookie('business_spoc_budget', '', time() - 3600);

                unset($_COOKIE['is_local_business_spoc']);
                setcookie('is_local_business_spoc', '', time() - 3600);

                unset($_COOKIE['is_outstation_business_spoc']);
                setcookie('is_outstation_business_spoc', '', time() - 3600);

                unset($_COOKIE['is_radio_business_spoc']);
                setcookie('is_radio_business_spoc', '', time() - 3600);

                unset($_COOKIE['is_bus_business_spoc']);
                setcookie('is_bus_business_spoc', '', time() - 3600);

                unset($_COOKIE['admin_id_business_spoc']);
                setcookie('admin_id_business_spoc', '', time() - 3600);

                unset($_COOKIE['group_id_business_spoc']);
                setcookie('group_id_business_spoc', '', time() - 3600);

                unset($_COOKIE['subgroup_id_business_spoc']);
                setcookie('subgroup_id_business_spoc', '', time() - 3600);

                unset($_COOKIE['has_auth_level_business_spoc']);
                setcookie('has_auth_level_business_spoc', '', time() - 3600);

                unset($_COOKIE['has_assessment_codes_business_spoc']);
                setcookie('has_assessment_codes_business_spoc', '', time() - 3600);                
                
                return $this->redirect()->toRoute('businessspoc-home');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/businessspoc-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function postBusinessspocForgotAction()
    {
            $request = $this->getRequest ();

            $data['username'] = $request->getPost('username');

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSSPOCFORGOT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                
                $m = 'New Passorword Sent Successfully on '.$data['username'];

                $view = new ViewModel(array('mess_success'=>$m));
                $view->setTemplate('businessspoc/businessspoc/get-businessspoc-login.phtml'); // path to phtml file under view folder
                return $view;
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/get-businessspoc-login.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    //Bookings
    public function BusinessspocBookingsAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $data['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['admin_id'] = $_COOKIE['admin_id_business_spoc'];
            $data['type'] = '1';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLSPOCBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            $getRequest = new Request ();
            $getRequest->setUri ( EMPLOYEEBYSPOC );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response5 = $client->send($getRequest)->getBody();

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response, 'employees' => $response5));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function BusinessspocBusBookingsAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $data['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['admin_id'] = $_COOKIE['admin_id_business_spoc'];
            $data['type'] = '1';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLSPOCBUSBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            $getRequest = new Request ();
            $getRequest->setUri ( EMPLOYEEBYSPOC );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response5 = $client->send($getRequest)->getBody();

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response, 'employees' => $response5));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function BusinessspocBookingsOldAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $data['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['admin_id'] = $_COOKIE['admin_id_business_spoc'];
            $data['type'] = '2';

            $getRequest = new Request ();
            $getRequest->setUri ( ALLSPOCBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            $getRequest = new Request ();
            $getRequest->setUri ( EMPLOYEEBYSPOC );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response5 = $client->send($getRequest)->getBody();

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response, 'employees' => $response5,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bookings-old.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function BusinessspocBusBookingsOldAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $data['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['admin_id'] = $_COOKIE['admin_id_business_spoc'];
            $data['type'] = '2';

            $getRequest = new Request ();
            $getRequest->setUri ( ALLSPOCBUSBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            $getRequest = new Request ();
            $getRequest->setUri ( EMPLOYEEBYSPOC );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response5 = $client->send($getRequest)->getBody();

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response, 'employees' => $response5,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bus-bookings-old.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function businessspocRejectedBookingsAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $data['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['admin_id'] = $_COOKIE['admin_id_business_spoc'];
            $data['type'] = '3';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLSPOCBOOKINGS );
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
                $view->setTemplate('businessspoc/businessspoc/businessspoc-rejected-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function businessspocRejectedBusBookingsAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $data['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['admin_id'] = $_COOKIE['admin_id_business_spoc'];
            $data['type'] = '3';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLSPOCBUSBOOKINGS );
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
                $view->setTemplate('businessspoc/businessspoc/businessspoc-rejected-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function rejectBusinessspocBookingAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( REJECTBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessspoc-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function rejectBusinessspocBusBookingAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['booking_id'] = $booking_id;

            $getRequest = new Request ();
            $getRequest->setUri ( REJECTBUSBOOKING );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessspoc-bus-bookings');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bus-bookings.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }
    
    public function getAddBookingBusinessspocAction()
    {
        if(isset($_COOKIE['business_spoc']))
        {

            $data['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['admin_id'] = $_COOKIE['admin_id_business_spoc'];

            $getRequest = new Request ();
            $getRequest->setUri ( ALLCITIES );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();

            $getRequest = new Request ();
            $getRequest->setUri ( ALLTAXITYPES );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response4 = $client->send($getRequest)->getBody();

            $getRequest = new Request ();
            $getRequest->setUri ( EMPLOYEEBYSPOC );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response5 = $client->send($getRequest)->getBody();

            $getRequest = new Request ();
            $getRequest->setUri ( ASSESSMENTCODES );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response6 = $client->send($getRequest)->getBody();
            
            return new ViewModel(array('cities' => $response, 'taxitypes' => $response4, 
                'employees' => $response5, 'ass_codes' => $response6,));
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');   
    }

    public function postAddBookingAction(){
        if(isset($_COOKIE['business_spoc'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['admin_id'] = $request->getPost('admin_id');
            $data['group_id'] = $request->getPost('group_id');
            $data['subgroup_id'] = $request->getPost('subgroup_id');
            $data['tour_type'] = $request->getPost('tour_type');

            $data['ass_code'] = $request->getPost('ass_code');
            $data['reason_booking'] = $request->getPost('reason_booking');

            if ($data['tour_type'] == '0') 
            {
                $data['pickup_location'] = $request->getPost('pickup_location');
                $data['drop_location'] = $request->getPost('drop_location');
                $data['pickup_datetime'] = $request->getPost('pickup_datetime');

                $data['no_of_seats'] = $request->getPost('no_of_seats');
                for($i=1; $i<=(int)$data['no_of_seats']; $i++)
                {
                    $data['employee_id_'.$i] = $request->getPost('employee_id_'.$i);
                    // $data['employee_cid_'.$i] = $request->getPost('employee_cid_'.$i);
                    // $data['employee_name_'.$i] = $request->getPost('employee_name_'.$i);
                    $data['employee_contact_'.$i] = $request->getPost('employee_contact_'.$i);
                    // $data['employee_email_'.$i] = $request->getPost('employee_email_'.$i);
                }
            }
            elseif ($data['tour_type'] == '1') 
            {
                $data['city_id'] = $request->getPost('city_id');
                $data['pickup_location'] = $request->getPost('pickup_location');
                $data['pickup_datetime'] = $request->getPost('pickup_datetime');
                $data['rate_id'] = $request->getPost('rate_id');
                $data['taxi_type_id'] = $request->getPost('taxi_type_id');

                $data['no_of_seats'] = $request->getPost('no_of_seats');
                for($i=1; $i<=(int)$data['no_of_seats']; $i++)
                {
                    $data['employee_id_'.$i] = $request->getPost('employee_id_'.$i);
                    // $data['employee_cid_'.$i] = $request->getPost('employee_cid_'.$i);
                    // $data['employee_name_'.$i] = $request->getPost('employee_name_'.$i);
                    $data['employee_contact_'.$i] = $request->getPost('employee_contact_'.$i);
                    // $data['employee_email_'.$i] = $request->getPost('employee_email_'.$i);
                }
            }
            elseif($data['tour_type'] == '2')
            {
                $data['city_id'] = $request->getPost('city_id');
                $data['pickup_location'] = $request->getPost('pickup_location');
                $data['pickup_datetime'] = $request->getPost('pickup_datetime');
                $data['taxi_type_id'] = $request->getPost('taxi_type_id2');
                $data['days'] = $request->getPost('days');
                $data['drop_city_name'] = $request->getPost('drop_city_name');

                $data['no_of_seats'] = $request->getPost('no_of_seats');
                for($i=1; $i<=(int)$data['no_of_seats']; $i++)
                {
                    $data['employee_id_'.$i] = $request->getPost('employee_id_'.$i);
                    // $data['employee_cid_'.$i] = $request->getPost('employee_cid_'.$i);
                    // $data['employee_name_'.$i] = $request->getPost('employee_name_'.$i);
                    $data['employee_contact_'.$i] = $request->getPost('employee_contact_'.$i);
                    // $data['employee_email_'.$i] = $request->getPost('employee_email_'.$i);
                }
            }
            else
            {
                $data['pickup_city'] = $request->getPost('pickup_city');
                $data['drop_city'] = $request->getPost('drop_city');

                $ttt =  $request->getPost('time_range_to');
                $ttf = $request->getPost('time_range_from');
                $data['time_range'] = $ttt . "::" . $ttf;

                $data['bus_type_priority_1'] = $request->getPost('bus_type_priority_1');
                $data['bus_type_priority_2'] = $request->getPost('bus_type_priority_2');
                $data['bus_type_priority_3'] = $request->getPost('bus_type_priority_3');
                 /*$bus_type = array();
                $types='';
                $bus_type = $request->getPost('bus_type');
                foreach ($bus_type as $bus) {
                    $types = $types . "," . $bus;
                }
                $data['bus_type'] = $types;*/

                $data['boarding_point'] = $request->getPost('boarding_point');
                $data['no_of_seats'] = '1';
                $data['people_id'] = $request->getPost('people_id');
                $data['age'] = $request->getPost('age');
                $data['id_proof_type'] = $request->getPost('id_proof_type');
                $data['id_proof_no'] = $request->getPost('id_card_no');
                $data['reason_booking'] = $request->getPost('reason_of_booking');
            }
                
            if($data['tour_type']!=3)
            {
                $getRequest = new Request ();
                $getRequest->setUri ( ADDBOOKING );
                $getRequest->setMethod ( 'POST' );
                $getRequest->setPost ( new Parameters ( $data ) );
                $client = new Client ();
                $response = $client->send($getRequest)->getBody();
                $r = json_decode($response, true);
            }
            else
            {
                $getRequest = new Request ();
                $getRequest->setUri ( ADDBUSBOOKING );
                $getRequest->setMethod ( 'POST' );
                $getRequest->setPost ( new Parameters ( $data ) );
                $client = new Client ();
                $response = $client->send($getRequest)->getBody();
                $r = json_decode($response, true);
            }

            if ($r['error'] == ""){
                $m = "Booking Added Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);
                if($data['tour_type'] !=3 )
                    return $this->redirect()->toRoute('businessspoc-bookings');
                else
                    return $this->redirect()->toRoute('businessspoc-bus-bookings');
            }
            else
            {
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/get-add-booking-businessspoc.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function viewBookingBusinessspocAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_spoc'];
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
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function viewBusBookingBusinessspocAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_spoc'];
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
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bus-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    //Invoices
    public function businessspocInvoicesAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data['access_token'] = $_COOKIE['access_token_business_spoc'];

            if(!$type)
                $data['type'] = '1';
            else
                $data['type'] = $type;
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLSPOCINVOICES );
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
                $view->setTemplate('businessspoc/businessspoc/businessspoc-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function businessspocBusInvoicesAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data['access_token'] = $_COOKIE['access_token_business_spoc'];

            if(!$type)
                $data['type'] = '1';
            else
                $data['type'] = $type;
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLSPOCBUSINVOICES );
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
                $view->setTemplate('businessspoc/businessspoc/businessspoc-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function viewRadioinvoiceBusinessspocAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_spoc'];
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
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function viewBusinvoiceBusinessspocAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_spoc'];
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
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bus-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function viewOutstationinvoiceBusinessspocAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_spoc'];
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
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }
    
    public function clearInvoiceBusinessspocAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $invoice_id = $this->getEvent()->getRouteMatch()->getParam('invoice_id');
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data ['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['invoice_id'] = $invoice_id;
            $data['accepted_by'] = 'Spoc';

            $getRequest = new Request ();
            $getRequest->setUri ( CLEARINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Approved Successfully";
                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);
    
                return $this->redirect()->toRoute('businessspoc-invoices', array('type' => $type));
            
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/businessspoc-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function commentInvoiceBusinessspocAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['invoice_id'] = $request->getPost('invoice_id');
            $data['comment'] = $request->getPost('comment');
            $data['rejected_by'] = 'Spoc';

            $getRequest = new Request ();
            $getRequest->setUri ( COMMENTINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessspoc-invoices', array('type' => '1'));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/businessspoc-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function clearBusInvoiceBusinessspocAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $invoice_id = $this->getEvent()->getRouteMatch()->getParam('invoice_id');
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data ['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['invoice_id'] = $invoice_id;
            $data['accepted_by'] = 'Spoc';

            $getRequest = new Request ();
            $getRequest->setUri ( CLEARBUSINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Approved Successfully";
                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businessspoc-bus-invoices', array('type' => $type));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bus-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }

    public function commentBusInvoiceBusinessspocAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_spoc'];
            $data['invoice_id'] = $request->getPost('invoice_id');
            $data['comment'] = $request->getPost('comment');
            $data['rejected_by'] = 'Spoc';

            $getRequest = new Request ();
            $getRequest->setUri ( COMMENTBUSINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessspoc-bus-invoices', array('type' => '1'));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('businessspoc/businessspoc/businessspoc-bus-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }    

    public function getCommentAction()
    {
        if(isset($_COOKIE['business_spoc'])){
            $invoice_id = $this->getEvent()->getRouteMatch()->getParam('invoice_id');
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            return new ViewModel(array('invoice_id' => $invoice_id, 'type' => $type));
        }
        else
            return $this->redirect()->toRoute('get-businessspoc-login');
    }
    
}
