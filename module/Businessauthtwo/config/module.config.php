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
            'Businessauthtwo\Controller\Businessauthtwo' => 'Businessauthtwo\Controller\BusinessauthtwoController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'businessauthtwo-home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/home',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'businessauthtwo-home',
                    ),
                ),
            ),
            'get-businessauthtwo-profile' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/profile',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'get-businessauthtwo-profile',
                    ),
                ),
            ),
            'businessauthtwo-changepassword' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/changepassword',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'businessauthtwo-changepassword',
                    ),
                ),
            ),
            'get-businessauthtwo-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authtwo/login',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'get-businessauthtwo-login',
                    ),
                ),
            ),
            'post-businessauthtwo-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postBusinessauthtwoLogin',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'post-businessauthtwo-login',
                    ),
                ),
            ),
            'businessauthtwo-logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwoLogout',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'businessauthtwo-logout',
                    ),
                ),
            ),
            'post-businessauthtwo-forgot' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postBusinessauthtwoForgot',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'post-businessauthtwo-forgot',
                    ),
                ),
            ),
            /*Bookings*/
            'businessauthtwo-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/bookings',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'businessauthtwo-bookings',
                    ),
                ),
            ),
            'businessauthtwo-bus-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/busbookings',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'businessauthtwo-bus-bookings',
                    ),
                ),
            ),
            'businessauthtwo-bookings-old' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/bookingsold',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'businessauthtwo-bookings-old',
                    ),
                ),
            ),
            'businessauthtwo-bus-bookings-old' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/busbookingsold',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'businessauthtwo-bus-bookings-old',
                    ),
                ),
            ),
            'businessauthtwo-rejected-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/rejectedbookings',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'businessauthtwo-rejected-bookings',
                    ),
                ),
            ),
            'businessauthtwo-rejected-bus-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/rejectedbusbookings',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'businessauthtwo-rejected-bus-bookings',
                    ),
                ),
            ),
            'accept-businessauthtwo-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authtwobooking/accept',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'accept-businessauthtwo-booking',
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
                                'action' => 'accept-businessauthtwo-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'accept-businessauthtwo-bus-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authtwobusbooking/accept',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'accept-businessauthtwo-bus-booking',
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
                                'action' => 'accept-businessauthtwo-bus-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'reject-businessauthtwo-bus-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authtwobusbooking/reject',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'reject-businessauthtwo-bus-booking',
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
                                'action' => 'reject-businessauthtwo-bus-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'rejectreason-businessauthtwo-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authtwobooking/rejectreason',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'rejectreason-businessauthtwo-booking',
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
                                'action' => 'rejectreason-businessauthtwo-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'rejectreason-businessauthtwo-bus-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authtwobusbooking/rejectreason',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'rejectreason-businessauthtwo-bus-booking',
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
                                'action' => 'rejectreason-businessauthtwo-bus-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'view-booking-businessauthtwo' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/viewBooking',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'view-booking-businessauthtwo',
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
                                'action' => 'view-booking-businessauthtwo',
                            ),
                        ),
                    ),
                ),
            ),
            'view-bus-booking-businessauthtwo' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/viewBusBooking',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'view-bus-booking-businessauthtwo',
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
                                'action' => 'view-bus-booking-businessauthtwo',
                            ),
                        ),
                    ),
                ),
            ),
            //Invoices
            'businessauthtwo-invoices' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/invoices',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'businessauthtwo-invoices',
                    ),
                ),
            ),
            'view-radioinvoice-businessauthtwo' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/viewInvoiceRadio',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'view-radioinvoice-businessauthtwo',
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
                                'action' => 'view-radioinvoice-businessauthtwo',
                            ),
                        ),
                    ),
                ),
            ),
            'view-outstationinvoice-businessauthtwo' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthtwo/viewInvoiceOutstation',
                    'defaults' => array(
                        'controller' => 'Businessauthtwo\Controller\Businessauthtwo',
                        'action'     => 'view-outstationinvoice-businessauthtwo',
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
                                'action' => 'view-outstationinvoice-businessauthtwo',
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
            'layout/genric' => __DIR__ . '/../view/layout/businessauthtwolayout.phtml',
        ),
    )
);
