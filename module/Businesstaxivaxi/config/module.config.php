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
            'Businesstaxivaxi\Controller\Businesstaxivaxi' => 'Businesstaxivaxi\Controller\BusinesstaxivaxiController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'businesstaxivaxi-home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/home',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'businesstaxivaxi-home',
                    ),
                ),
            ),
            'get-businesstaxivaxi-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxi/login',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'get-businesstaxivaxi-login',
                    ),
                ),
            ),
            'post-businesstaxivaxi-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postBusinesstaxivaxiLogin',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-businesstaxivaxi-login',
                    ),
                ),
            ),
            'businesstaxivaxi-logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxiLogout',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'businesstaxivaxi-logout',
                    ),
                ),
            ),
            'post-businesstaxivaxi-forgot' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postBusinesstaxivaxiForgot',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-businesstaxivaxi-forgot',
                    ),
                ),
            ),
            /*Bookings*/
            'businesstaxivaxi-bookings-assigned' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/bookingsassigned',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'businesstaxivaxi-bookings-assigned',
                    ),
                ),
            ),
            'businesstaxivaxi-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/bookings',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'businesstaxivaxi-bookings',
                    ),
                ),
            ),
            'businesstaxivaxi-bus-bookings-assigned' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/busbookingsassigned',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'businesstaxivaxi-bus-bookings-assigned',
                    ),
                ),
            ),
            'businesstaxivaxi-bus-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/busbookings',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'businesstaxivaxi-bus-bookings',
                    ),
                ),
            ),
            'businesstaxivaxi-bookings-old' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/bookingsold',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'businesstaxivaxi-bookings-old',
                    ),
                ),
            ),
            'businesstaxivaxi-bus-bookings-old' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/busbookingsold',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'businesstaxivaxi-bus-bookings-old',
                    ),
                ),
            ),
            'businesstaxivaxi-rejected-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/rejectedbookings',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'businesstaxivaxi-rejected-bookings',
                    ),
                ),
            ),
            'businesstaxivaxi-rejected-bus-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/rejectedbusbookings',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'businesstaxivaxi-rejected-bus-bookings',
                    ),
                ),
            ),
            'accept-businesstaxivaxi-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/accept',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'accept-businesstaxivaxi-booking',
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
                                'action' => 'accept-businesstaxivaxi-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'accept-businesstaxivaxi-bus-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibusbooking/accept',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'accept-businesstaxivaxi-bus-booking',
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
                                'action' => 'accept-businesstaxivaxi-bus-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'reject-businesstaxivaxi-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/reject',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'reject-businesstaxivaxi-booking',
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
                                'action' => 'reject-businesstaxivaxi-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'reject-businesstaxivaxi-bus-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibusbooking/reject',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'reject-businesstaxivaxi-bus-booking',
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
                                'action' => 'reject-businesstaxivaxi-bus-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'rejectreason-businesstaxivaxi-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/rejectreason',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'rejectreason-businesstaxivaxi-booking',
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
                                'action' => 'rejectreason-businesstaxivaxi-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'rejectreason-businesstaxivaxi-bus-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibusbooking/rejectreason',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'rejectreason-businesstaxivaxi-bus-booking',
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
                                'action' => 'rejectreason-businesstaxivaxi-bus-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'assign-businesstaxivaxi-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/assign',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'assign-businesstaxivaxi-booking',
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
                                'action' => 'assign-businesstaxivaxi-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'post-businesstaxivaxi-assign' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddAssignBooking',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-businesstaxivaxi-assign',
                    ),
                ),
            ),
            'assign-businessradio-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/assignRadio',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'assign-businessradio-booking',
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
                                'action' => 'assign-businessradio-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'post-businessradio-assign' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddAssignRadioBooking',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-businessradio-assign',
                    ),
                ),
            ),
            'assign-businesstaxivaxi-bus-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibusbooking/assign',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'assign-businesstaxivaxi-bus-booking',
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
                                'action' => 'assign-businesstaxivaxi-bus-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'post-businesstaxivaxi-bus-assign' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddAssignBusBooking',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-businesstaxivaxi-bus-assign',
                    ),
                ),
            ),
            //Invoice
            'get-add-radioinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/addInvoiceRadio',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'get-add-radioinvoice-businesstaxivaxi',
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
                                'action' => 'get-add-radioinvoice-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'post-add-radioinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddInvoiceRadio',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-add-radioinvoice-businesstaxivaxi',
                    ),
                ),
            ),
            //Invoice Local
            'get-add-localinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/addInvoiceLocal',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'get-add-localinvoice-businesstaxivaxi',
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
                                'action' => 'get-add-localinvoice-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'post-add-localinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddInvoiceLocal',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-add-localinvoice-businesstaxivaxi',
                    ),
                ),
            ),
            //Invoice Outstation
            'get-add-outstationinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/addInvoiceOutstation',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'get-add-outstationinvoice-businesstaxivaxi',
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
                                'action' => 'get-add-outstationinvoice-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'post-add-outstationinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddInvoiceOutstation',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-add-outstationinvoice-businesstaxivaxi',
                    ),
                ),
            ),
            'book-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/book',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'book-businesstaxivaxi',
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
                                'action' => 'book-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'cancelbook-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/cancelbook',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'cancelbook-businesstaxivaxi',
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
                                'action' => 'cancelbook-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'view-booking-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/viewBooking',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'view-booking-businesstaxivaxi',
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
                                'action' => 'view-booking-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'view-bus-booking-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/viewBusBooking',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'view-bus-booking-businesstaxivaxi',
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
                                'action' => 'view-bus-booking-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            //Invoices
            'businesstaxivaxi-invoices' => array(
                'type' => 'segment',
                'options' => array(
                    'route'       => '/businesstaxivaxi/invoices[/][:type][/]',
                    'constraints' => array(
                        'type' => '[0-9]*'
                    ),

                    'defaults' => array(
                        'action'        => 'businesstaxivaxi-invoices',
                        'controller'    => 'Businesstaxivaxi\Controller\Businesstaxivaxi'
                    ),
                ),
            ),
            'comment-invoice-taxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/commentInvoice',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'comment-invoice-taxivaxi',
                    ),
                ),
            ),
            'comment-bus-invoice-taxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/commentBusInvoice',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'comment-bus-invoice-taxivaxi',
                    ),
                ),
            ),
            'view-radioinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/viewInvoiceRadio',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'view-radioinvoice-businesstaxivaxi',
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
                                'action' => 'view-radioinvoice-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'view-outstationinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/viewInvoiceOutstation',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'view-outstationinvoice-businesstaxivaxi',
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
                                'action' => 'view-outstationinvoice-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'businesstaxivaxi-bills' => array(
                'type' => 'segment',
                'options' => array(
                    'route'       => '/businesstaxivaxi/bills[/][:type][/]',
                    'constraints' => array(
                        'type' => '[0-9]*'
                    ),

                    'defaults' => array(
                        'action'        => 'businesstaxivaxi-bills',
                        'controller'    => 'Businesstaxivaxi\Controller\Businesstaxivaxi'
                    ),
                ),
            ),
            'view-businesstaxivaxi-bill' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/viewBill',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'view-businesstaxivaxi-bill',
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
                                'action' => 'view-businesstaxivaxi-bill',
                            ),
                        ),
                    ),
                ),
            ),
            'view-businesstaxivaxi-bus-bill' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/viewBusBill',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'view-businesstaxivaxi-bus-bill',
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
                                'action' => 'view-businesstaxivaxi-bus-bill',
                            ),
                        ),
                    ),
                ),
            ),
            'reset-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/reset',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'reset-businesstaxivaxi',
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
                                'action' => 'reset-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'adddutyslip-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/addDutySlip',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'adddutyslip-businesstaxivaxi',
                    ),
                ),
            ),
            'addbusticket' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/addBusTicket',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'addbusticket',
                    ),
                ),
            ),
            'businesstaxivaxi-bus-invoices' => array(
                'type' => 'segment',
                'options' => array(
                    'route'       => '/businesstaxivaxi/businvoices[/][:type][/]',
                    'constraints' => array(
                        'type' => '[0-9]*'
                    ),

                    'defaults' => array(
                        'action'        => 'businesstaxivaxi-bus-invoices',
                        'controller'    => 'Businesstaxivaxi\Controller\Businesstaxivaxi'
                    ),
                ),
            ),
            'comment-bus-invoice-taxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businesstaxivaxi/commentBusInvoice',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'comment-bus-invoice-taxivaxi',
                    ),
                ),
            ),
			'get-add-businvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/addInvoiceBus',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'get-add-businvoice-businesstaxivaxi',
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
                                'action' => 'get-add-businvoice-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'post-add-businvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postAddInvoiceBus',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-add-businvoice-businesstaxivaxi',
                    ),
                ),
            ),
            'get-edit-businvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/editInvoiceBus',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'get-edit-businvoice-businesstaxivaxi',
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
                                'action' => 'get-edit-businvoice-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'post-edit-businvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postEditInvoiceBus',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-edit-businvoice-businesstaxivaxi',
                    ),
                ),
            ),
            'view-businvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/viewBusInvoice',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'view-businvoice-businesstaxivaxi',
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
                                'action' => 'view-businvoice-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'download-unassigned-booking-report' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxi/download/unassigned/booking/report',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'download-unassigned-booking-report',
                    ),
                ),
            ),
            'download-assigned-booking-report' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxi/download/assigned/booking/report',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'download-assigned-booking-report',
                    ),
                ),
            ),
            'download-archived-booking-report' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxi/download/archived/booking/report',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'download-archived-booking-report',
                    ),
                ),
            ),
            'download-cancelled-booking-report' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxi/download/cancelled/booking/report',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'download-cancelled-booking-report',
                    ),
                ),
            ),
            'get-edit-radioinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/editInvoiceRadio',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'get-edit-radioinvoice-businesstaxivaxi',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'post' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:booking_id]',
                            'constraints' => array(
                                'booking_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'get-edit-radioinvoice-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'post-edit-radioinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postEditInvoiceRadio',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-edit-radioinvoice-businesstaxivaxi',
                    ),
                ),
            ),
            'get-edit-outstationinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/editInvoiceOutstation',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'get-edit-outstationinvoice-businesstaxivaxi',
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
                                'action' => 'get-edit-outstationinvoice-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'post-edit-outstationinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postEditInvoiceOutstation',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-edit-outstationinvoice-businesstaxivaxi',
                    ),
                ),
            ),
            'get-edit-localinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/taxivaxibooking/editInvoiceLocal',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'get-edit-localinvoice-businesstaxivaxi',
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
                                'action' => 'get-edit-localinvoice-businesstaxivaxi',
                            ),
                        ),
                    ),
                ),
            ),
            'post-edit-localinvoice-businesstaxivaxi' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postEditInvoiceLocal',
                    'defaults' => array(
                        'controller' => 'Businesstaxivaxi\Controller\Businesstaxivaxi',
                        'action'     => 'post-edit-localinvoice-businesstaxivaxi',
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
            'layout/genric' => __DIR__ . '/../view/layout/businesstaxivaxilayout.phtml',
        ),
    )
);
