<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use PHPExcel_IOFactory;

define ( 'BUSINESSADMINHOME', 'http://taxivaxi.in/business/api/adminHome');

define ( 'BUSINESSADMINPROFILE', 'http://taxivaxi.in/business/api/viewAdminProfile' );
define ( 'CHANGEPASSWORD', 'http://taxivaxi.in/business/api/adminChangePassword' );
define ( 'BUSINESSADMINLOGIN', 'http://taxivaxi.in/business/api/login' );
define ( 'BUSINESSADMINLOGOUT', 'http://taxivaxi.in/business/api/logout' );
define ( 'BUSINESSADMINFORGOT', 'http://taxivaxi.in/business/api/adminforgot' );

define ( 'ALLGROUPS', 'http://taxivaxi.in/business/api/getAllGroups' );
define ( 'ADDGROUP', 'http://taxivaxi.in/business/api/addGroup' );
define ( 'GROUP', 'http://taxivaxi.in/business/api/viewGroup' );
define ( 'EDITGROUP', 'http://taxivaxi.in/business/api/editGroup' );
define ( 'DELGROUP', 'http://taxivaxi.in/business/api/deleteGroup' );

define ( 'ALLSUBGROUPS', 'http://taxivaxi.in/business/api/getAllSubgroups' );
define ( 'ADDSUBGROUP', 'http://taxivaxi.in/business/api/addSubgroup' );
define ( 'SUBGROUP', 'http://taxivaxi.in/business/api/viewSubgroup' );
define ( 'EDITSUBGROUP', 'http://taxivaxi.in/business/api/editSubgroup' );
define ( 'DELSUBGROUP', 'http://taxivaxi.in/business/api/deleteSubgroup' );

define ( 'ALLSPOCS', 'http://taxivaxi.in/business/api/getAllSpocs' );
define ( 'ADDSPOC', 'http://taxivaxi.in/business/api/addSpoc' );
define ( 'SPOC', 'http://taxivaxi.in/business/api/viewSpoc' );
define ( 'EDITSPOC', 'http://taxivaxi.in/business/api/editSpoc' );
define ( 'DELSPOC', 'http://taxivaxi.in/business/api/deleteSpoc' );

define ( 'ALLEMPLOYEES', 'http://taxivaxi.in/business/api/getAllPeople' );
define ( 'ADDEMPLOYEE', 'http://taxivaxi.in/business/api/addPeople' );
define ( 'EMPLOYEE', 'http://taxivaxi.in/business/api/viewPeople' );
define ( 'EDITEMPLOYEE', 'http://taxivaxi.in/business/api/editPeople' );
define ( 'DELEMPLOYEE', 'http://taxivaxi.in/business/api/deletePeople' );

define ( 'ALLADMINBOOKINGS', 'http://taxivaxi.in/business/api/getAllAdminBookings' );
define ( 'ALLADMINBUSBOOKINGS', 'http://taxivaxi.in/business/api/getAllAdminBusBookings' );
define ( 'BOOKING', 'http://taxivaxi.in/business/api/viewBookingTaxivaxi' );
define ( 'BUSBOOKING', 'http://taxivaxi.in/business/api/viewBusBookingTaxivaxi' );

define ( 'ALLADMININVOICES', 'http://taxivaxi.in/business/api/getAllAdminInvoices' );
define ( 'VIEWINVOICE', 'http://taxivaxi.in/business/api/viewInvoice' );
define ( 'VIEWBUSINVOICE', 'http://taxivaxi.in/business/api/viewBusInvoice' );

define ( 'DEACTIVATEDSPOCS', 'http://taxivaxi.in/business/api/getAllDeactivatedSpocs' );

define ( 'BOOKINGREPORT', 'http://taxivaxi.in/business/api/getAdminBookingReport' );
define ( 'BUSBOOKINGREPORT', 'http://taxivaxi.in/business/api/getAdminBusBookingReport' );

define ( 'ASSESSMENTCODES', 'http://taxivaxi.in/business/api/getAllCodes' );
define ( 'ADDASSESSMENTCODE', 'http://taxivaxi.in/business/api/addAssessmentCode' );

define ( 'ALLADMINBUSINVOICES', 'http://taxivaxi.in/business/api/getAllAdminBusInvoices' );

//Billings
define ( 'CLEARINVOICE', 'http://taxivaxi.in/business/api/adminClearInvoice' );
define ( 'COMMENTINVOICE', 'http://taxivaxi.in/business/api/adminCommentInvoice' );

define ( 'CLEARBUSINVOICE', 'http://taxivaxi.in/business/api/adminClearBusInvoice' );
define ( 'COMMENTBUSINVOICE', 'http://taxivaxi.in/business/api/adminCommentBusInvoice' );

define ( 'ALLBILLS', 'http://taxivaxi.in/business/api/getAllBills' );
define ( 'VIEWBILL', 'http://taxivaxi.in/business/api/viewBill' );

//Rates
define ( 'RATES', 'http://taxivaxi.in/business/api/getAllRates' );

class IndexController extends AbstractActionController
{

    public function getBusinessadminLoginAction()
    {
        if(!isset($_COOKIE['business_admin']))
        {
            return new ViewModel();
        }
        else
            return $this->redirect()->toRoute('businessadmin-home');
    }

    public function postBusinessadminLoginAction()
    {
            $request = $this->getRequest ();

            $data['username'] = $request->getPost('username');
            $data['password'] = $request->getPost('password');

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSADMINLOGIN );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            /*return new ViewModel(array('response' => $response,));*/

            if ($r['error'] == ""){
                $cookie_name = "access_token_business_admin";
                $cookie_value = $r['response']['access_token'];
                setcookie($cookie_name, $cookie_value, 0, "/");

                $cookie_name2 = "business_admin";
                $cookie_value2 = $r['response']['admin_id'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "business_name";
                $cookie_value2 = $r['response']['admin']['contact_name'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "business_budget";
                $cookie_value2 = $r['response']['admin']['budget'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "business_email";
                $cookie_value2 = $r['response']['admin']['email'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "has_auth_level";
                $cookie_value2 = $r['response']['admin']['has_auth_level'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_radio";
                $cookie_value2 = $r['response']['admin']['is_radio'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_local";
                $cookie_value2 = $r['response']['admin']['is_local'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_outstation";
                $cookie_value2 = $r['response']['admin']['is_outstation'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "is_bus";
                $cookie_value2 = $r['response']['admin']['is_bus'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");

                $cookie_name2 = "has_assessment_codes";
                $cookie_value2 = $r['response']['admin']['has_assessment_codes'];
                setcookie($cookie_name2, $cookie_value2, 0, "/");
                
                return $this->redirect()->toRoute('businessadmin-home');
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/get-businessadmin-login.phtml'); // path to phtml file under view folder
                return $view;
            }
    }
    
    public function getBusinessadminRatesAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['user'] = 'Admin';

            $getRequest = new Request ();
            $getRequest->setUri ( RATES );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('rates' => $response,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function businessadminHomeAction()
    {
        if(isset($_COOKIE['business_admin']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_admin'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSADMINHOME );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();

            // return new ViewModel();
            return new ViewModel(array('details' => $response,));
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function getBusinessadminProfileAction()
    {
        if(isset($_COOKIE['business_admin']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_admin'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSADMINPROFILE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();

            // return new ViewModel();
            return new ViewModel(array('details' => $response,));
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function businessadminChangepasswordAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
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
                
                return $this->redirect()->toRoute('get-businessadmin-profile');
            }
            else{
                $m = $r['error'];

                $cookie_name4 = "fail";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);       
                
                return $this->redirect()->toRoute('get-businessadmin-profile');
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function businessadminLogoutAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSADMINLOGOUT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                unset($_COOKIE['access_token_business_admin']);
                setcookie('access_token_business_admin', '', time() - 3600);

                unset($_COOKIE['business_admin']);
                setcookie('business_admin', '', time() - 3600);

                unset($_COOKIE['business_name']);
                setcookie('business_name', '', time() - 3600);

                unset($_COOKIE['business_budget']);
                setcookie('business_budget', '', time() - 3600);

                unset($_COOKIE['business_email']);
                setcookie('business_email', '', time() - 3600);

                unset($_COOKIE['has_auth_level']);
                setcookie('has_auth_level', '', time() - 3600);

                unset($_COOKIE['is_radio']);
                setcookie('is_radio', '', time() - 3600);

                unset($_COOKIE['is_local']);
                setcookie('is_local', '', time() - 3600);

                unset($_COOKIE['is_outstation']);
                setcookie('is_outstation', '', time() - 3600);

                unset($_COOKIE['is_bus']);
                setcookie('is_bus', '', time() - 3600);

                unset($_COOKIE['has_assessment_codes']);
                setcookie('has_assessment_codes', '', time() - 3600);
                
                return $this->redirect()->toRoute('businessadmin-home');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function postBusinessadminForgotAction()
    {
            $request = $this->getRequest ();

            $data['username'] = $request->getPost('username');

            $getRequest = new Request ();
            $getRequest->setUri ( BUSINESSADMINFORGOT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                
                $m = 'New Passorword Sent Successfully on '.$data['username'];

                $view = new ViewModel(array('mess_success'=>$m));
                $view->setTemplate('application/index/get-businessadmin-login.phtml'); // path to phtml file under view folder
                return $view;
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/get-businessadmin-login.phtml'); // path to phtml file under view folder
                return $view;
            }
    }

    //Bookings
    public function businessadminBookingsAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];

            if(!$type)
                $data['type'] = '1';
            else
                $data['type'] = $type;
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLADMINBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response, 'type' => $type));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function businessadminBusBookingsAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];

            if(!$type)
                $data['type'] = '1';
            else
                $data['type'] = $type;
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLADMINBUSBOOKINGS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('bookings' => $response, 'type'=>$type));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function viewBookingBusinessadminAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];
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
                $view->setTemplate('application/index/businessadmin-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function viewBusBookingBusinessadminAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];
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
                $view->setTemplate('application/index/businessadmin-bus-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    //Groups
    public function businessadminGroupsAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLGROUPS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('groups' => $response,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function postAddGroupAction(){
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['group_name'] = $request->getPost('group_name');
            $data['budget'] = $request->getPost('budget');
            $data['auth_cid'] = $request->getPost('auth_cid');
            $data['auth_name'] = $request->getPost('auth_name');
            $data['auth_contact'] = $request->getPost('auth_contact');
            $data['auth_email'] = $request->getPost('auth_email');
            $data['auth_password'] = $request->getPost('auth_password');

            $is_radio = $request->getPost('is_radio');
                if ($is_radio == 'Yes') {
                    $data['is_radio'] = '1';
                }
                else {
                    $data['is_radio'] = '0';
                }

            $is_local = $request->getPost('is_local');
                if ($is_local == 'Yes') {
                    $data['is_local'] = '1';
                }
                else {
                    $data['is_local'] = '0';
                }

            $is_outstation = $request->getPost('is_outstation');
                if ($is_outstation == 'Yes') {
                    $data['is_outstation'] = '1';
                }
                else {
                    $data['is_outstation'] = '0';
                }

            $is_bus = $request->getPost('is_bus');
                if ($is_bus == 'Yes') {
                    $data['is_bus'] = '1';
                }
                else {
                    $data['is_bus'] = '0';
                }
                
            $getRequest = new Request ();
            $getRequest->setUri ( ADDGROUP );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Group Added Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businessadmin-groups');
            }
            else
            {
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-groups.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function postEditGroupAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['group_id'] = $request->getPost('edit_group_id');
            $data['group_name'] = $request->getPost('edit_group_name');
            $data['budget'] = $request->getPost('edit_budget');
            $data['auth_name'] = $request->getPost('edit_auth_name');

            $is_radio = $request->getPost('edit_radio_extra');
                if ($is_radio == 'Yes') {
                    $data['is_radio'] = '1';
                }
                else {
                    $data['is_radio'] = '0';
                }

            $is_local = $request->getPost('edit_local_extra');
                if ($is_local == 'Yes') {
                    $data['is_local'] = '1';
                }
                else {
                    $data['is_local'] = '0';
                }

            $is_outstation = $request->getPost('edit_outstation_extra');
                if ($is_outstation == 'Yes') {
                    $data['is_outstation'] = '1';
                }
                else {
                    $data['is_outstation'] = '0';
                }

            $is_bus = $request->getPost('edit_bus_extra');
                if ($is_bus == 'Yes') {
                    $data['is_bus'] = '1';
                }
                else {
                    $data['is_bus'] = '0';
                }

            $getRequest = new Request ();
            $getRequest->setUri ( EDITGROUP );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Group Edited Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businessadmin-groups');
            }
            else
            {
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-groups.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }
    
    public function deleteGroupAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $group_id = $this->getEvent()->getRouteMatch()->getParam('group_id');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['group_id'] = $group_id;

            $getRequest = new Request ();
            $getRequest->setUri ( DELGROUP );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessadmin-groups');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }
    
    
    //--------------------------------------Sub-Groups---------------------------------------//
    public function businessadminSubgroupsAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLSUBGROUPS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            $getRequest = new Request ();
            $getRequest->setUri ( ALLGROUPS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response2 = $client->send($getRequest)->getBody();
            $r2 = json_decode($response2, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('subgroups' => $response, 'groups' => $response2,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function postAddSubgroupAction(){
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['subgroup_name'] = $request->getPost('subgroup_name');
            $data['group_id'] = $request->getPost('group_id');
            $data['budget'] = $request->getPost('budget');

            $data['auth_cid'] = $request->getPost('auth_cid');
            $data['auth_name'] = $request->getPost('auth_name');
            $data['auth_contact'] = $request->getPost('auth_contact');
            $data['auth_email'] = $request->getPost('auth_email');
            $data['auth_password'] = $request->getPost('auth_password');

            $is_radio = $request->getPost('is_radio');
                if ($is_radio == 'Yes') {
                    $data['is_radio'] = '1';
                }
                else {
                    $data['is_radio'] = '0';
                }

            $is_local = $request->getPost('is_local');
                if ($is_local == 'Yes') {
                    $data['is_local'] = '1';
                }
                else {
                    $data['is_local'] = '0';
                }

            $is_outstation = $request->getPost('is_outstation');
                if ($is_outstation == 'Yes') {
                    $data['is_outstation'] = '1';
                }
                else {
                    $data['is_outstation'] = '0';
                }

            $is_bus = $request->getPost('is_bus');
                if ($is_bus == 'Yes') {
                    $data['is_bus'] = '1';
                }
                else {
                    $data['is_bus'] = '0';
                }
                
            $getRequest = new Request ();
            $getRequest->setUri ( ADDSUBGROUP );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Sub-group Added Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businessadmin-subgroups');
            }
            else
            {
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-subgroups.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function postEditSubgroupAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['subgroup_id'] = $request->getPost('edit_subgroup_id');
            $data['group_id'] = $request->getPost('edit_group_id');
            $data['subgroup_name'] = $request->getPost('edit_subgroup_name');
            $data['budget'] = $request->getPost('edit_budget');
            $data['auth_name'] = $request->getPost('edit_auth_name');

            $is_radio = $request->getPost('edit_radio_extra');
                if ($is_radio == 'Yes') {
                    $data['is_radio'] = '1';
                }
                else {
                    $data['is_radio'] = '0';
                }

            $is_local = $request->getPost('edit_local_extra');
                if ($is_local == 'Yes') {
                    $data['is_local'] = '1';
                }
                else {
                    $data['is_local'] = '0';
                }

            $is_outstation = $request->getPost('edit_outstation_extra');
                if ($is_outstation == 'Yes') {
                    $data['is_outstation'] = '1';
                }
                else {
                    $data['is_outstation'] = '0';
                }

            $is_bus = $request->getPost('edit_bus_extra');
                if ($is_bus == 'Yes') {
                    $data['is_bus'] = '1';
                }
                else {
                    $data['is_bus'] = '0';
                }

            $getRequest = new Request ();
            $getRequest->setUri ( EDITSUBGROUP );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Sub-group Edited Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businessadmin-subgroups');
            }
            else
            {
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-subgroups.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }
    
    public function deleteSubgroupAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $subgroup_id = $this->getEvent()->getRouteMatch()->getParam('subgroup_id');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['subgroup_id'] = $subgroup_id;

            $getRequest = new Request ();
            $getRequest->setUri ( DELSUBGROUP );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessadmin-subgroups');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    //--------------------------------------Spocs---------------------------------------//
    public function businessadminSpocsAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            
            $getRequest = new Request();
            $getRequest->setUri ( ALLSPOCS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            $getRequest = new Request();
            $getRequest->setUri ( ALLSUBGROUPS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client();
            $response2 = $client->send($getRequest)->getBody();

            $getRequest3 = new Request ();
            $getRequest3->setUri ( DEACTIVATEDSPOCS );
            $getRequest3->setMethod ( 'POST' );
            $getRequest3->setPost ( new Parameters ( $data ) );
            $client3 = new Client();
            $response3 = $client3->send($getRequest3)->getBody();

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('employees' => $response, 'subgroups' => $response2, 
                    'acquirespoc' => $response3,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function postAddSpocAction(){
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['user_cid'] = $request->getPost('user_cid');
            $data['user_name'] = $request->getPost('user_name');
            $data['user_contact'] = $request->getPost('user_contact');
            $data['email'] = $request->getPost('email');
            $data['subgroup_id'] = $request->getPost('subgroup_id');
            $data['budget'] = $request->getPost('budget');
            $data['status'] = $request->getPost('status');

            $data['acquired_spoc_id'] = $request->getPost('acquired_spoc_id');

            $is_radio = $request->getPost('is_radio');
                if ($is_radio == 'Yes') {
                    $data['is_radio'] = '1';
                }
                else {
                    $data['is_radio'] = '0';
                }

            $is_local = $request->getPost('is_local');
                if ($is_local == 'Yes') {
                    $data['is_local'] = '1';
                }
                else {
                    $data['is_local'] = '0';
                }

            $is_outstation = $request->getPost('is_outstation');
                if ($is_outstation == 'Yes') {
                    $data['is_outstation'] = '1';
                }
                else {
                    $data['is_outstation'] = '0';
                }

            $is_bus = $request->getPost('is_bus');
                if ($is_bus == 'Yes') {
                    $data['is_bus'] = '1';
                }
                else {
                    $data['is_bus'] = '0';
                }
                
            $getRequest = new Request ();
            $getRequest->setUri ( ADDSPOC );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Spoc Added Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businessadmin-spocs');
            }
            else
            {
                $m = $r['error'];
				$cookie_name4 = "error";
                $cookie_value4 = $m;
                $view = new ViewModel(array('mess'=>$m));
                setcookie($cookie_name4, $cookie_value4, time()+2);
                
                /*$view->setTemplate('application/index/businessadmin-employees.phtml');
                return $view;*/
                return $this->redirect()->toRoute('businessadmin-spocs');
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function postEditSpocAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['employee_id'] = $request->getPost('edit_employee_id');
            $data['user_cid'] = $request->getPost('edit_user_cid');
            $data['subgroup_id'] = $request->getPost('edit_subgroup_id');
            $data['user_name'] = $request->getPost('edit_user_name');
            $data['user_contact'] = $request->getPost('edit_user_contact');
            $data['email'] = $request->getPost('edit_email');
            $data['budget'] = '0';
            $data['status'] = $request->getPost('edit_status');

            $is_radio = $request->getPost('edit_radio_extra');
                if ($is_radio == 'Yes') {
                    $data['is_radio'] = '1';
                }
                else {
                    $data['is_radio'] = '0';
                }

            $is_local = $request->getPost('edit_local_extra');
                if ($is_local == 'Yes') {
                    $data['is_local'] = '1';
                }
                else {
                    $data['is_local'] = '0';
                }

            $is_outstation = $request->getPost('edit_outstation_extra');
                if ($is_outstation == 'Yes') {
                    $data['is_outstation'] = '1';
                }
                else {
                    $data['is_outstation'] = '0';
                }

            $is_bus = $request->getPost('edit_bus_extra');
                if ($is_bus == 'Yes') {
                    $data['is_bus'] = '1';
                }
                else {
                    $data['is_bus'] = '0';
                }

            $getRequest = new Request ();
            $getRequest->setUri ( EDITSPOC );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Spoc Edited Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businessadmin-spocs');
            }
            else
            {
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-employees.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }
    
    public function deleteSpocAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $spoc_id = $this->getEvent()->getRouteMatch()->getParam('spoc_id');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['employee_id'] = $spoc_id;

            $getRequest = new Request ();
            $getRequest->setUri ( DELSPOC );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessadmin-spocs');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    //Invoices
    public function businessadminInvoicesAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];

            if(!$type)
                $data['type'] = '1';
            else
                $data['type'] = $type;
            /*echo $data['type'];
            die();*/
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLADMININVOICES );
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
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function businessadminBusInvoicesAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];

            if(!$type)
                $data['type'] = '1';
            else
                $data['type'] = $type;
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLADMINBUSINVOICES );
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
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function viewRadioinvoiceBusinessadminAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];
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
                $view->setTemplate('application/index/businessadmin-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businesstaxivaxi-login');
    }

    public function viewOutstationinvoiceBusinessadminAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];
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
                $view->setTemplate('application/index/businessadmin-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    //--------------------------------------Employees(People)---------------------------------------//
    public function businessadminEmployeesAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLEMPLOYEES );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            $getRequest = new Request ();
            $getRequest->setUri ( ALLSPOCS );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response2 = $client->send($getRequest)->getBody();
            $r2 = json_decode($response2, true);

            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('employees' => $response, 'spocs' => $response2,));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function postAddEmployeeAction(){
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['people_cid'] = $request->getPost('people_cid');
            $data['people_name'] = $request->getPost('people_name');
            $data['people_contact'] = $request->getPost('people_contact');
            $data['people_email'] = $request->getPost('people_email');
            $data['user_id'] = $request->getPost('user_id');
                
            $getRequest = new Request ();
            $getRequest->setUri ( ADDEMPLOYEE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Employee Added Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businessadmin-employees');
            }
            else
            {
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-employees.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function postEditEmployeeAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['people_id'] = $request->getPost('edit_people_cid');
            $data['people_cid'] = $request->getPost('edit_people_cid');
            $data['people_name'] = $request->getPost('edit_people_name');
            $data['people_contact'] = $request->getPost('edit_people_contact');
            $data['people_email'] = $request->getPost('edit_people_email');

            $getRequest = new Request ();
            $getRequest->setUri ( EDITEMPLOYEE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Employee Edited Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businessadmin-employees');
            }
            else
            {
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-employees.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }
    
    public function deleteEmployeeAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $people_id = $this->getEvent()->getRouteMatch()->getParam('people_id');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['people_id'] = $people_id;

            $getRequest = new Request ();
            $getRequest->setUri ( DELEMPLOYEE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessadmin-employees');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }
    
    /* ------------------------------------Assessment Codes --------------------------------------------*/
     public function businessadminAssessmentCodesAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['admin_id'] = $_COOKIE['business_admin'];
            $getRequest = new Request ();
            $getRequest->setUri ( ASSESSMENTCODES );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );
            $client = new Client ();
            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

           
            if ($r['error'] == "" || $r['error'] == 'No Result Found'){
                return new ViewModel(array('ass_codes' => $response));   
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function postAddAssessmentCodeAction(){
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['assessment_code'] = $request->getPost('assessment_code');
            $data['code_desc'] = $request->getPost('code_desc');
          
                
            $getRequest = new Request ();
            $getRequest->setUri ( ADDASSESSMENTCODE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Assessment Code Added Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businessadmin-assessment-codes');
            }
            else
            {
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-assessment-codes.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function postEditAssessmentCodeAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['people_id'] = $request->getPost('edit_people_cid');
            $data['people_cid'] = $request->getPost('edit_people_cid');
            $data['people_name'] = $request->getPost('edit_people_name');
            $data['people_contact'] = $request->getPost('edit_people_contact');
            $data['people_email'] = $request->getPost('edit_people_email');

            $getRequest = new Request ();
            $getRequest->setUri ( EDITEMPLOYEE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                $m = "Employee Edited Successfully";

                $cookie_name4 = "success";
                $cookie_value4 = $m;
                setcookie($cookie_name4, $cookie_value4, time()+2);

                return $this->redirect()->toRoute('businessadmin-employees');
            }
            else
            {
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-employees.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }
    
    public function deleteAssessmentCodeAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $people_id = $this->getEvent()->getRouteMatch()->getParam('people_id');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['people_id'] = $people_id;

            $getRequest = new Request ();
            $getRequest->setUri ( DELEMPLOYEE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessadmin-employees');
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }
    
    public function downloadBookingReportAction()
    {
        if(isset($_COOKIE['business_admin']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_admin'];
           // $data['type'] = '1';
            
            $getRequest = new Request ();
            $getRequest->setUri ( BOOKINGREPORT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);
            
            //var_dump($r); die;

              $data = $r['response']['Bookings'];

              $filename = "Bookings_" . time() . ".csv";

             /* header("Content-Disposition: attachment; filename=\"$filename\"");
              header("Content-Type: application/vnd.ms-excel");
              //header("Content-Type: application/octet-stream");

              $flag = false;
              foreach($data as $row) 
              {
                if(!$flag) {
                  echo implode("\t", array_keys($row)) . "\r\n";
                  $flag = true;
                }
                echo implode("\t", array_values($row)) . "\r\n";
              }*/
              header("Content-Disposition: attachment; filename=\"$filename\"");
			  header("Content-Type: text/csv");

			  $out = fopen("php://output", 'w');

			  $flag = false;
			 
			  foreach($data as $row) {
			    if(!$flag) {
			      // display field/column names as first row
			      fputcsv($out, array_keys($row), ',', '"');
			      $flag = true;
			    }
			    //array_walk($row, 'cleanData');
			    fputcsv($out, array_values($row), ',', '"');
			  }

			  fclose($out);
			  //return false;
			  exit;
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }
    
    public function downloadBusbookingReportAction()
    {
        if(isset($_COOKIE['business_admin']))
        {
            $data['access_token'] = $_COOKIE['access_token_business_admin'];
           // $data['type'] = '1';
            
            $getRequest = new Request ();
            $getRequest->setUri ( BUSBOOKINGREPORT );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);
            
            //var_dump($r); die;

              $data = $r['response']['Bookings'];

              $filename = "BusBookings_" . time() . ".csv";

             /* header("Content-Disposition: attachment; filename=\"$filename\"");
              header("Content-Type: application/vnd.ms-excel");
              //header("Content-Type: application/octet-stream");

              $flag = false;
              foreach($data as $row) 
              {
                if(!$flag) {
                  echo implode("\t", array_keys($row)) . "\r\n";
                  $flag = true;
                }
                echo implode("\t", array_values($row)) . "\r\n";
              }*/
              header("Content-Disposition: attachment; filename=\"$filename\"");
			  header("Content-Type: text/csv");

			  $out = fopen("php://output", 'w');

			  $flag = false;
			 
			  foreach($data as $row) {
			    if(!$flag) {
			      // display field/column names as first row
			      fputcsv($out, array_keys($row), ',', '"');
			      $flag = true;
			    }
			    //array_walk($row, 'cleanData');
			    fputcsv($out, array_values($row), ',', '"');
			  }

			  fclose($out);
			  //return false;
			  exit;
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }
	
    function cleanData(&$str)
	{
	    $str = preg_replace("/\t/", "\\t", $str);
	    $str = preg_replace("/\r?\n/", "\\n", $str);
	    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
	}

    //Billings
    public function clearInvoiceAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $invoice_id = $this->getEvent()->getRouteMatch()->getParam('invoice_id');
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['invoice_id'] = $invoice_id;
            $data['accepted_by'] = 'Admin';
 
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
                
                return $this->redirect()->toRoute('businessadmin-invoices', array('type' => $type));
                
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function clearBusInvoiceAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $invoice_id = $this->getEvent()->getRouteMatch()->getParam('invoice_id');
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['invoice_id'] = $invoice_id;
            $data['accepted_by'] = 'Admin';
 
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

                return $this->redirect()->toRoute('businessadmin-bus-invoices', array('type' => $type));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-bus-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function commentInvoiceAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['invoice_id'] = $request->getPost('invoice_id');
            $data['comment'] = $request->getPost('comment');
            $data['rejected_by'] = 'Admin';

            $getRequest = new Request ();
            $getRequest->setUri ( COMMENTINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessadmin-invoices', array('type' => '2'));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function commentBusInvoiceAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $request = $this->getRequest ();

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            $data['invoice_id'] = $request->getPost('invoice_id');
            $data['comment'] = $request->getPost('comment');
            $data['rejected_by'] = 'Admin';

            $getRequest = new Request ();
            $getRequest->setUri ( COMMENTBUSINVOICE );
            $getRequest->setMethod ( 'POST' );
            $getRequest->setPost ( new Parameters ( $data ) );

            $client = new Client ();

            $response = $client->send($getRequest)->getBody();
            $r = json_decode($response, true);

            if ($r['error'] == ""){
                return $this->redirect()->toRoute('businessadmin-bus-invoices', array('type' => '2'));
            }
            else{
                $m = $r['error'];

                $view = new ViewModel(array('mess'=>$m));
                $view->setTemplate('application/index/businessadmin-bus-invoices.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function businessadminBillsAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
            //1-Unpaid, 2-Paid
            if(!$type)
                $data['type'] = '1';
            else
                $data ['type'] = $type;

            $data['user'] = 'Admin';
            
            $getRequest = new Request ();
            $getRequest->setUri ( ALLBILLS );
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
                $view->setTemplate('application/index/businessadmin-home.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function viewBusinessadminBillAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $bill_id = $this->getEvent()->getRouteMatch()->getParam('bill_id');

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
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
                $view->setTemplate('application/index/businessadmin-home.phtml.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function viewBusinessadminBusBillAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $bill_id = $this->getEvent()->getRouteMatch()->getParam('bill_id');

            $data['access_token'] = $_COOKIE['access_token_business_admin'];
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
                $view->setTemplate('application/index/businessadmin-home.phtml.phtml');
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }
    
    public function viewBusinvoiceAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $booking_id = $this->getEvent()->getRouteMatch()->getParam('booking_id');

            $data ['access_token'] = $_COOKIE['access_token_business_admin'];
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
                $view->setTemplate('application/index/businessadmin-bus-bookings.phtml'); // path to phtml file under view folder
                return $view;
            }
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login');
    }

    public function getCommentAdminAction()
    {
        if(isset($_COOKIE['business_admin'])){
            $invoice_id = $this->getEvent()->getRouteMatch()->getParam('invoice_id');
            $type = $this->getEvent()->getRouteMatch()->getParam('type');

            return new ViewModel(array('invoice_id' => $invoice_id, 'type' => $type));
        }
        else
            return $this->redirect()->toRoute('get-businessadmin-login-login');
    }
}
