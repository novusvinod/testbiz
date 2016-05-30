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
            'Businessauthone\Controller\Businessauthone' => 'Businessauthone\Controller\BusinessauthoneController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'businessauthone-home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/home',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'businessauthone-home',
                    ),
                ),
            ),
            'get-businessauthone-profile' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/profile',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'get-businessauthone-profile',
                    ),
                ),
            ),
            'businessauthone-changepassword' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/changepassword',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'businessauthone-changepassword',
                    ),
                ),
            ),
            'get-businessauthone-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authone/login',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'get-businessauthone-login',
                    ),
                ),
            ),
            'post-businessauthone-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postBusinessauthoneLogin',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'post-businessauthone-login',
                    ),
                ),
            ),
            'businessauthone-logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthoneLogout',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'businessauthone-logout',
                    ),
                ),
            ),
            'post-businessauthone-forgot' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/postBusinessauthoneForgot',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'post-businessauthone-forgot',
                    ),
                ),
            ),
            /*Bookings*/
            'businessauthone-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/bookings',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'businessauthone-bookings',
                    ),
                ),
            ),
            'businessauthone-bus-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/busbookings',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'businessauthone-bus-bookings',
                    ),
                ),
            ),
            'businessauthone-bookings-old' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/bookingsold',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'businessauthone-bookings-old',
                    ),
                ),
            ),
            'businessauthone-bus-bookings-old' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/busbookingsold',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'businessauthone-bus-bookings-old',
                    ),
                ),
            ),
            'businessauthone-rejected-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/rejectedbookings',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'businessauthone-rejected-bookings',
                    ),
                ),
            ),
            'businessauthone-rejected-bus-bookings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/rejectedbusbookings',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'businessauthone-rejected-bus-bookings',
                    ),
                ),
            ),
            'accept-businessauthone-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authonebooking/accept',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'accept-businessauthone-booking',
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
                                'action' => 'accept-businessauthone-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'accept-businessauthone-bus-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authonebusbooking/accept',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'accept-businessauthone-bus-booking',
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
                                'action' => 'accept-businessauthone-bus-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'reject-businessauthone-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authonebooking/reject',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'reject-businessauthone-booking',
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
                                'action' => 'reject-businessauthone-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'reject-businessauthone-bus-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authonebusbooking/reject',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'reject-businessauthone-bus-booking',
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
                                'action' => 'reject-businessauthone-bus-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'rejectreason-businessauthone-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authonebooking/rejectreason',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'rejectreason-businessauthone-booking',
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
                                'action' => 'rejectreason-businessauthone-booking',
                            ),
                        ),
                    ),
                ),
            ),
            'rejectreason-businessauthone-bus-booking' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/authonebusbooking/rejectreason',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'rejectreason-businessauthone-bus-booking',
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
                                'action' => 'rejectreason-businessauthone-bus-booking',
                            ),
                        ),
                    ),
                ),
            ),
            //Invoices
            'businessauthone-invoices' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/invoices',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'businessauthone-invoices',
                    ),
                ),
            ),
            'view-booking-businessauthone' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/viewBooking',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'view-booking-businessauthone',
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
                                'action' => 'view-booking-businessauthone',
                            ),
                        ),
                    ),
                ),
            ),
            'view-bus-booking-businessauthone' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/viewBusBooking',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'view-bus-booking-businessauthone',
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
                                'action' => 'view-bus-booking-businessauthone',
                            ),
                        ),
                    ),
                ),
            ),
            'view-radioinvoice-businessauthone' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/viewInvoiceRadio',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'view-radioinvoice-businessauthone',
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
                                'action' => 'view-radioinvoice-businessauthone',
                            ),
                        ),
                    ),
                ),
            ),
            'view-outstationinvoice-businessauthone' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/businessauthone/viewInvoiceOutstation',
                    'defaults' => array(
                        'controller' => 'Businessauthone\Controller\Businessauthone',
                        'action'     => 'view-outstationinvoice-businessauthone',
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
                                'action' => 'view-outstationinvoice-businessauthone',
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
            'layout/genric' => __DIR__ . '/../view/layout/businessauthonelayout.phtml',
        ),
    )
);
