<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'get-businessadmin-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/login',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'get-businessadmin-login',
                    ),
                ),
            ),
            'get-businessadmin-rates' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/rates',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'get-businessadmin-rates',
                    ),
                ),
            ),
            'post-businessadmin-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postBusinessadminLogin',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'post-businessadmin-login',
                    ),
                ),
            ),
            'businessadmin-home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/home',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'businessadmin-home',
                    ),
                ),
            ),
            'get-businessadmin-profile' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/profile',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'get-businessadmin-profile',
                    ),
                ),
            ),
            'businessadmin-changepassword' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/changepassword',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'businessadmin-changepassword',
                    ),
                ),
            ),
            'businessadmin-logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadminLogout',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'businessadmin-logout',
                    ),
                ),
            ),
            'post-businessadmin-forgot' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postBusinessadminForgot',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'post-businessadmin-forgot',
                    ),
                ),
            ),
            //Bookings
            'businessadmin-bookings' => array(
                'type' => 'segment',
                'options' => array(
                    'route'       => '/businessadmin/bookings[/][:type][/]',
                    'constraints' => array(
                        'type' => '[0-9]*'
                    ),

                    'defaults' => array(
                        'action'        => 'businessadmin-bookings',
                        'controller'    => 'Application\Controller\Businessadmin'
                    ),
                ),
            ),
            'businessadmin-bus-bookings' => array(
                'type' => 'segment',
                'options' => array(
                    'route'       => '/businessadmin/busbookings[/][:type][/]',
                    'constraints' => array(
                        'type' => '[0-9]*'
                    ),

                    'defaults' => array(
                        'action'        => 'businessadmin-bus-bookings',
                        'controller'    => 'Application\Controller\Businessadmin'
                    ),
                ),
            ),
            /*Groups*/
            'businessadmin-groups' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/groups',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'businessadmin-groups',
                    ),
                ),
            ),
            'post-add-group' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddGroup',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'post-add-group',
                    ),
                ),
            ),
            'post-edit-group' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postEditGroup',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'post-edit-group',
                    ),
                ),
            ),
            'delete-group' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/group/delete',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'delete-group',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:group_id]',
                            'constraints' => array(
                                'location_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'delete-group',
                            ),
                        ),
                    ),
                ),
            ),
            'view-group' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/group/view',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'view-group',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:group_id]',
                            'constraints' => array(
                                'location_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view-group',
                            ),
                        ),
                    ),
                ),
            ),
            /*Sub-Groups*/
            'businessadmin-subgroups' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/subgroups',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'businessadmin-subgroups',
                    ),
                ),
            ),
            'post-add-subgroup' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddSubgroup',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'post-add-subgroup',
                    ),
                ),
            ),
            'post-edit-subgroup' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postEditSubgroup',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'post-edit-subgroup',
                    ),
                ),
            ),
            'delete-subgroup' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/subgroup/delete',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'delete-subgroup',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:subgroup_id]',
                            'constraints' => array(
                                'subgroup_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'delete-subgroup',
                            ),
                        ),
                    ),
                ),
            ),
            'view-subgroup' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/subgroup/view',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'view-subgroup',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:subgroup_id]',
                            'constraints' => array(
                                'subgroup_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view-subgroup',
                            ),
                        ),
                    ),
                ),
            ),
            /*Spocs*/
            'businessadmin-spocs' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/spocs',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'businessadmin-spocs',
                    ),
                ),
            ),
            'post-add-spoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddSpoc',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'post-add-spoc',
                    ),
                ),
            ),
            'post-edit-spoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postEditSpoc',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'post-edit-spoc',
                    ),
                ),
            ),
            'delete-spoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/spoc/delete',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'delete-spoc',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:spoc_id]',
                            'constraints' => array(
                                'spoc_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'delete-spoc',
                            ),
                        ),
                    ),
                ),
            ),
            'view-spoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/spoc/view',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'view-spoc',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:spoc_id]',
                            'constraints' => array(
                                'spoc_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view-spoc',
                            ),
                        ),
                    ),
                ),
            ),
            /*Employees*/
            'businessadmin-employees' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/employees',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'businessadmin-employees',
                    ),
                ),
            ),
            'post-add-employee' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddEmployee',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'post-add-employee',
                    ),
                ),
            ),
            'post-edit-employee' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postEditEmployee',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'post-edit-employee',
                    ),
                ),
            ),
            'delete-employee' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/employee/delete',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'delete-employee',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:people_id]',
                            'constraints' => array(
                                'employee_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'delete-employee',
                            ),
                        ),
                    ),
                ),
            ),
            'view-employee' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/employee/view',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'view-employee',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:employee_id]',
                            'constraints' => array(
                                'employee_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view-employee',
                            ),
                        ),
                    ),
                ),
            ),
            //Bookings
            'view-booking-businessadmin' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/viewBooking',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'view-booking-businessadmin',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:booking_id]',
                            'constraints' => array(
                                'location_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view-booking-businessadmin',
                            ),
                        ),
                    ),
                ),
            ),
            'view-bus-booking-businessadmin' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/viewBusBooking',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'view-bus-booking-businessadmin',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:booking_id]',
                            'constraints' => array(
                                'location_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view-bus-booking-businessadmin',
                            ),
                        ),
                    ),
                ),
            ),
            //Invoices
            'get-comment-admin' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/getComment',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'get-comment-admin',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:invoice_id][/][:type]',
                            'constraints' => array(
                                'invoice_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'get-comment-admin',
                            ),
                        ),
                    ),
                ),
            ),
            'businessadmin-invoices' => array(
                'type' => 'segment',
                'options' => array(
                    'route'       => '/businessadmin/invoices[/][:type][/]',
                    'constraints' => array(
                        'type' => '[0-9]*'
                    ),

                    'defaults' => array(
                        'action'        => 'businessadmin-invoices',
                        'controller'    => 'Application\Controller\Businessadmin'
                    ),
                ),
            ),
            'clear-invoice' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/clearInvoice',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'clear-invoice',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:invoice_id][/][:type]',
                            'constraints' => array(
                                'invoice_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'clear-invoice',
                            ),
                        ),
                    ),
                ),
            ),
            'comment-invoice' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/commentInvoice',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'comment-invoice',
                    ),
                ),
            ),
            'businessadmin-bus-invoices' => array(
                'type' => 'segment',
                'options' => array(
                    'route'       => '/businessadmin/businvoices[/][:type][/]',
                    'constraints' => array(
                        'type' => '[0-9]*'
                    ),

                    'defaults' => array(
                        'action'        => 'businessadmin-bus-invoices',
                        'controller'    => 'Application\Controller\Businessadmin'
                    ),
                ),
            ),
            'clear-bus-invoice' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/clearBusInvoice',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'clear-bus-invoice',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:invoice_id][/][:type]',
                            'constraints' => array(
                                'invoice_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'clear-bus-invoice',
                            ),
                        ),
                    ),
                ),
            ),
            'comment-bus-invoice' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/commentBusInvoice',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'comment-bus-invoice',
                    ),
                ),
            ),
            'view-radioinvoice-businessadmin' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/viewInvoiceRadio',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'view-radioinvoice-businessadmin',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:booking_id]',
                            'constraints' => array(
                                'location_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view-radioinvoice-businessadmin',
                            ),
                        ),
                    ),
                ),
            ),
            'view-outstationinvoice-businessadmin' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/viewInvoiceOutstation',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'view-outstationinvoice-businessadmin',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:booking_id]',
                            'constraints' => array(
                                'location_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view-outstationinvoice-businessadmin',
                            ),
                        ),
                    ),
                ),
            ),
            'view-businvoice' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/viewBusinvoice',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'view-businvoice',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:booking_id]',
                            'constraints' => array(
                                'location_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view-businvoice',
                            ),
                        ),
                    ),
                ),
            ),
            'businessadmin-bills' => array(
                'type' => 'segment',
                'options' => array(
                    'route'       => '/businessadmin/bills[/][:type][/]',
                    'constraints' => array(
                        'type' => '[0-9]*'
                    ),

                    'defaults' => array(
                        'action'        => 'businessadmin-bills',
                        'controller'    => 'Application\Controller\Businessadmin'
                    ),
                ),
            ),
            'view-businessadmin-bill' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/adminbooking/viewBill',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'view-businessadmin-bill',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:bill_id]',
                            'constraints' => array(
                                'bill_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view-businessadmin-bill',
                            ),
                        ),
                    ),
                ),
            ),
            'view-businessadmin-bus-bill' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/adminbooking/viewBusBill',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'view-businessadmin-bus-bill',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:bill_id]',
                            'constraints' => array(
                                'bill_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view-businessadmin-bus-bill',
                            ),
                        ),
                    ),
                ),
            ),
            /*Assessment Codes*/
            'businessadmin-assessment-codes' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessadmin/assessment-codes',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'businessadmin-assessment-codes',
                    ),
                ),
            ),
            'post-add-assessment-code' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddAssessmentCode',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'post-add-assessment-code',
                    ),
                ),
            ),
            'post-edit-assessment-code' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postEditAssessmentCode',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'post-edit-assessment-code',
                    ),
                ),
            ),
            'delete-assessment-code' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/assessment-code/delete',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'delete-assessment-code',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:id]',
                            'constraints' => array(
                                'employee_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'delete-assessment-code',
                            ),
                        ),
                    ),
                ),
            ),
            'view-assessment-code' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/assessment-code/view',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'view-assessment-code',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:id]',
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view-assessment-code',
                            ),
                        ),
                    ),
                ),
            ),
            'download-booking-report' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/download/booking/report',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'download-booking-report',
                    ),
                ),
            ),
            'download-busbooking-report' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/download/busbooking/report',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Businessadmin',
                        'action'     => 'download-busbooking-report',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Businessadmin' => 'Application\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            
            'application/index/index' => __DIR__ . '/../view/application/index/get-businessadmin-login.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
