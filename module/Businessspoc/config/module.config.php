<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'controllers' => array(
        'invokables' => array(
            'Businessspoc\Controller\Businessspoc' => 'Businessspoc\Controller\BusinessspocController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'businessspoc-home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/home',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'businessspoc-home',
                    ),
                ),
            ),
            'get-businessspoc-profile' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/profile',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'get-businessspoc-profile',
                    ),
                ),
            ),
            'businessspoc-changepassword' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/changepassword',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'businessspoc-changepassword',
                    ),
                ),
            ),
            'get-businessspoc-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/spoc/login',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'get-businessspoc-login',
                    ),
                ),
            ),
            'post-businessspoc-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postBusinessspocLogin',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'post-businessspoc-login',
                    ),
                ),
            ),
            'businessspoc-logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspocLogout',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'businessspoc-logout',
                    ),
                ),
            ),
            'post-businessspoc-forgot' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postBusinessspocForgot',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'post-businessspoc-forgot',
                    ),
                ),
            ),
            /*Bookings*/
            'businessspoc-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/bookings',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'businessspoc-bookings',
                    ),
                ),
            ),
            'businessspoc-bus-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/busbookings',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'businessspoc-bus-bookings',
                    ),
                ),
            ),
            'businessspoc-bookings-old' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/bookingsold',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'businessspoc-bookings-old',
                    ),
                ),
            ),
            'businessspoc-bus-bookings-old' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/busbookingsold',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'businessspoc-bus-bookings-old',
                    ),
                ),
            ),
            'businessspoc-rejected-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/rejectedbookings',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'businessspoc-rejected-bookings',
                    ),
                ),
            ),
            'businessspoc-rejected-bus-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/rejectedbusbookings',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'businessspoc-rejected-bus-bookings',
                    ),
                ),
            ),
            'reject-businessspoc-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/spocbooking/reject',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'reject-businessspoc-booking',
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
                                'action' => 'reject-businessspoc-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'reject-businessspoc-bus-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/spocbusbooking/reject',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'reject-businessspoc-bus-booking',
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
                                'action' => 'reject-businessspoc-bus-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'get-add-booking-businessspoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/addBooking',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'get-add-booking-businessspoc',
                    ),
                ),
            ),
            'post-add-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddBooking',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'post-add-booking',
                    ),
                ),
            ),
            'view-booking-businessspoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/viewBooking',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'view-booking-businessspoc',
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
                                'action' => 'view-booking-businessspoc',
                            ),
                        ),
                    ),
                ),
            ),
            'view-bus-booking-businessspoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/viewBusBooking',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'view-bus-booking-businessspoc',
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
                                'action' => 'view-bus-booking-businessspoc',
                            ),
                        ),
                    ),
                ),
            ),
            //Invoices
            'get-comment-businessspoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/getComment',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'get-comment',
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
                                'action' => 'get-comment',
                            ),
                        ),
                    ),
                ),
            ),
            'businessspoc-invoices' => array(
                'type' => 'segment',
                'options' => array(
                    'route'       => '/businessspoc/invoices[/][:type][/]',
                    'constraints' => array(
                        'type' => '[0-9]*'
                    ),

                    'defaults' => array(
                        'action'        => 'businessspoc-invoices',
                        'controller'    => 'Businessspoc\Controller\Businessspoc'
                    ),
                ),
            ),
            'clear-invoice-businessspoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/clearInvoice',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'clear-invoice-businessspoc',
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
                                'action' => 'clear-invoice-businessspoc',
                            ),
                        ),
                    ),
                ),
            ),
            'comment-invoice-businessspoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/commentInvoice',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'comment-invoice-businessspoc',
                    ),
                ),
            ),
            'businessspoc-bus-invoices' => array(
                'type' => 'segment',
                'options' => array(
                    'route'       => '/businessspoc/businvoices[/][:type][/]',
                    'constraints' => array(
                        'type' => '[0-9]*'
                    ),

                    'defaults' => array(
                        'action'        => 'businessspoc-bus-invoices',
                        'controller'    => 'Businessspoc\Controller\Businessspoc'
                    ),
                ),
            ),
            'clear-bus-invoice-businessspoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/clearBusInvoice',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'clear-bus-invoice-businessspoc',
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
                                'action' => 'clear-bus-invoice-businessspoc',
                            ),
                        ),
                    ),
                ),
            ),
            'comment-bus-invoice-businessspoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/commentBusInvoice',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'comment-bus-invoice-businessspoc',
                    ),
                ),
            ),
            'view-radioinvoice-businessspoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/viewInvoiceRadio',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'view-radioinvoice-businessspoc',
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
                                'action' => 'view-radioinvoice-businessspoc',
                            ),
                        ),
                    ),
                ),
            ),
            'view-outstationinvoice-businessspoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/viewInvoiceOutstation',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'view-outstationinvoice-businessspoc',
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
                                'action' => 'view-outstationinvoice-businessspoc',
                            ),
                        ),
                    ),
                ),
            ),
            'view-businvoice-businessspoc' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessspoc/viewBusInvoice',
                    'defaults' => array(
                        'controller' => 'Businessspoc\Controller\Businessspoc',
                        'action'     => 'view-businvoice-businessspoc',
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
                                'action' => 'view-businvoice-businessspoc',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'layout/genric' => __DIR__ . '/../view/layout/businessspoclayout.phtml',
        ),
    )
);
