<?php
/*
 * The routes for API.
 */
$routes = array();

$routes['/tokens'] = 'tokens';

$routes['/configurations']       = 'configs';
$routes['/configurations/:name'] = 'config';

$routes['/products']         = 'products';
$routes['/products/:id']     = 'product';
$routes['/productlines']     = 'productLines';
$routes['/productlines/:id'] = 'productLine';

$routes['/products/:id/stories'] = 'stories';
$routes['/stories/:id']          = 'story';
$routes['/stories/:id/change']   = 'storyChange';

$routes['/products/:id/bugs'] = 'bugs';
$routes['/bugs/:id']          = 'bug';

$routes['/projects']     = 'projects';
$routes['/projects/:id'] = 'project';

$routes['/projects/:project/executions'] = 'executions';
$routes['/executions']                   = 'executions';
$routes['/executions/:id']               = 'execution';

$routes['/executions/:execution/tasks'] = 'tasks';
$routes['/tasks']                       = 'tasks';
$routes['/tasks/:id']                   = 'task';
$routes['/tasks/:id/assignto']          = 'taskAssignTo';
$routes['/tasks/:id/start']             = 'taskStart';
$routes['/tasks/:id/finish']            = 'taskFinish';

$routes['/users']     = 'users';
$routes['/users/:id'] = 'user';
$routes['/user']      = 'user';

$routes['/programs']     = 'programs';
$routes['/programs/:id'] = 'program';

$routes['/products/:productID/issues'] = 'productIssues';
$routes['/projects/:projectID/issues'] = 'issues';
$routes['/issues']                     = 'issues';
$routes['/issues/:issueID']            = 'issue';

$routes['/todos']     = 'todos';
$routes['/todos/:id'] = 'todo';

$routes['/projects/:projectID/testtasks'] = 'testtasks';
$routes['/testtasks']                     = 'testtasks';
$routes['/testtasks/:id']                 = 'testtask';

$routes['/projects/:projectID/risks'] = 'risks';
$routes['/risks']                     = 'risks';
$routes['/risks/:id']                 = 'risk';

$config->routes = $routes;
