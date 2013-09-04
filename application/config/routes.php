<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
/*
$route['default_controller'] = 'pages/view';
$route['(:any)'] = 'pages/view/$1';
*/

$route['smileys'] = 'smileys';
$route['smileys/(:any)'] = 'smileys/pages/$1';

$route['upload/do_upload'] = 'upload/do_upload';
$route['upload/imagefile'] = 'upload/imagefile';
$route['upload/view'] = 'upload/view';
$route['upload/(:any)'] = 'upload/view/$1';
$route['upload'] = 'upload';

$route['404_override'] = 'errors/site_404';

$route['lists/updlist'] = 'lists/updlist';
$route['lists/updpicks'] = 'lists/updpicks';
$route['lists/gettypes'] = 'lists/gettypes';
$route['lists/getitems'] = 'lists/getitems';
$route['lists/getform'] = 'lists/getform';
$route['lists/getform/(:any)'] = 'lists/getform/$1';
$route['lists/prntsave'] = 'lists/prntsave';
$route['lists/upditem'] = 'lists/upditem';
$route['lists/upditem/(:any)'] = 'lists/upditem/$1';
$route['lists/getgroups'] = 'lists/getgroups';
$route['lists/getgroups/(:any)'] = 'lists/getgroups/$1';
$route['lists/updgroup'] = 'lists/updgroup';
$route['lists/updgroup/(:any)'] = 'lists/updgroup/$1';
$route['lists/itemgrid'] = 'lists/itemgrid';
$route['lists/itemgrid/(:any)'] = 'lists/itemgrid/$1';
$route['lists/itemfind'] = 'lists/itemfind';
$route['lists/itemfind/(:any)'] = 'lists/itemfind/$1';
$route['lists/findform'] = 'lists/findform';
$route['lists/findform/(:any)'] = 'lists/findform/$1';

$route['lists/view'] = 'lists/view';
$route['lists/(:any)'] = 'lists/view/$1';
$route['lists'] = 'lists';

$route['gallery/itemfind'] = 'gallery/itemfind';
$route['gallery/findform'] = 'gallery/findform';
$route['gallery/updimage'] = 'gallery/updimage';
$route['gallery/upditag'] = 'gallery/upditag';
$route['gallery/modimage'] = 'gallery/modimage';
$route['gallery/getform'] = 'gallery/getform';
$route['gallery/images'] = 'gallery/images';
$route['gallery/cleanup'] = 'gallery/cleanup';
$route['gallery/(:any)'] = 'gallery/images/$1';
$route['gallery'] = 'gallery';

$route['login/signout'] = 'login/signout';
$route['login/signon'] = 'login/signon';
$route['login/upduser'] = 'login/upduser';
$route['login/editform'] = 'login/editform';
//$route['login/(:any)'] = 'login/view/$1';
$route['login'] = 'login';

$route['(:any)'] = 'pages/view/$1';
$route['default_controller'] = 'pages/view';

$route['contact/submit'] = 'contact/submit';
$route['contact'] = 'contact';

//$route['userfile/(:any)'] = 'userfile/image/001/$1';
$route['news/create'] = 'news/create';
$route['news/(:any)'] = 'news/view/$1';
$route['news'] = 'news';

$route['users/grid'] = 'users/grid';
$route['users/post'] = 'users/post';
$route['users/(:any)'] = 'users/view/$1';
$route['users'] = 'users';
/*
$route['login/create_member'] = 'login/create_member';
$route['login/logged_in_area'] = 'login/logged_in_area';
$route['login/includes'] = 'login/includes/$1';
$route['login/signup_form'] = 'login/signup_form';
$route['login/logout'] = 'login/logout_out_area';
$route['login/logged_out_area'] = 'login/logged_out_area';
$route['login/validate_credentials'] = 'login/validate_credentials';
$route['login/manage'] = 'login/manage_users';
$route['login/success'] = 'login/success';

$route['wmtest/create'] = 'wmtest/create';
$route['wmtest/show'] = 'wmtest/show';
$route['wmtest/show/(:any)'] = 'wmtest/show/$1';
$route['wmtest/jqry'] = 'wmtest/jqry';
$route['wmtest/jqry/(:any)'] = 'wmtest/jqry/$1';
$route['wmtest/post_action'] = 'wmtest/post_action';
$route['wmtest/post_action/(:any)'] = 'wmtest/post_action/$1';
$route['wmtest/(:any)'] = 'wmtest/view/$1';
$route['wmtest'] = 'wmtest';
*/



/* End of file routes.php */
/* Location: ./application/config/routes.php */