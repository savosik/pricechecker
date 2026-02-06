<?php

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/admin/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/authenticate' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.authenticate',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.logout',
          ),
          1 => NULL,
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.profile.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.notifications.readAll',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/dom-parser/task' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::owrgzO7hZauSNG3B',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/dom-parser/result' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::MNxVn1Uw7jmbVlIr',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/dom-parser/status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CEZ7R0vyEntNhQsd',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/dom-parser/reset-stale' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5V10PxvZQ83Jp2lT',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/up' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4oQhSHao1PsFwcGf',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WKzUUAnJi8yJRNBS',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/price-history/delete-all' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.price-history.delete-all',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/docs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documentation',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/admin/(?|update\\-field/(?|column/([^/]++)/([^/]++)(*:58)|relation/([^/]++)/([^/]++)/([^/]++)(*:100))|async\\-search/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:159)|notifications/([^/]++)(*:189)|component/([^/]++)(?:/([^/]++))?(*:229)|method/([^/]++)(?:/([^/]++))?(*:266)|re(?|active/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:322)|source/([^/]++)/(?|crud(?|(*:356)|/(?|create(*:374)|([^/]++)(?|(*:393)|/edit(*:406)|(*:414)))|(*:424))|handler/([^/]++)(*:449)|([^/]++)(?:/([^/]++))?(*:479)))|has\\-many/(?|form/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:543)|list/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:592))|page/([^/]++)(*:614)|(.*)(*:626))|/storage/(.*)(*:648))/?$}sDu',
    ),
    3 => 
    array (
      58 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.update-field.through-column',
          ),
          1 => 
          array (
            0 => 'resourceUri',
            1 => 'resourceItem',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      100 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.update-field.through-relation',
          ),
          1 => 
          array (
            0 => 'resourceUri',
            1 => 'pageUri',
            2 => 'resourceItem',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      159 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.async-search',
            'resourceUri' => NULL,
            'resourceItem' => NULL,
          ),
          1 => 
          array (
            0 => 'pageUri',
            1 => 'resourceUri',
            2 => 'resourceItem',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      189 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.notifications.read',
          ),
          1 => 
          array (
            0 => 'notification',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      229 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.component',
            'resourceUri' => NULL,
          ),
          1 => 
          array (
            0 => 'pageUri',
            1 => 'resourceUri',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      266 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.method',
            'resourceUri' => NULL,
          ),
          1 => 
          array (
            0 => 'pageUri',
            1 => 'resourceUri',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      322 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.reactive',
            'resourceUri' => NULL,
            'resourceItem' => NULL,
          ),
          1 => 
          array (
            0 => 'pageUri',
            1 => 'resourceUri',
            2 => 'resourceItem',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      356 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.crud.massDelete',
          ),
          1 => 
          array (
            0 => 'resourceUri',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.crud.index',
          ),
          1 => 
          array (
            0 => 'resourceUri',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      374 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.crud.create',
          ),
          1 => 
          array (
            0 => 'resourceUri',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      393 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.crud.show',
          ),
          1 => 
          array (
            0 => 'resourceUri',
            1 => 'resourceItem',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      406 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.crud.edit',
          ),
          1 => 
          array (
            0 => 'resourceUri',
            1 => 'resourceItem',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      414 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.crud.update',
          ),
          1 => 
          array (
            0 => 'resourceUri',
            1 => 'resourceItem',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.crud.destroy',
          ),
          1 => 
          array (
            0 => 'resourceUri',
            1 => 'resourceItem',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      424 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.crud.store',
          ),
          1 => 
          array (
            0 => 'resourceUri',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      449 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.handler',
          ),
          1 => 
          array (
            0 => 'resourceUri',
            1 => 'handlerUri',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      479 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.resource.page',
            'resourceItem' => NULL,
          ),
          1 => 
          array (
            0 => 'resourceUri',
            1 => 'pageUri',
            2 => 'resourceItem',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      543 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.has-many.form',
            'resourceUri' => NULL,
            'resourceItem' => NULL,
          ),
          1 => 
          array (
            0 => 'pageUri',
            1 => 'resourceUri',
            2 => 'resourceItem',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      592 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.has-many.list',
            'resourceUri' => NULL,
            'resourceItem' => NULL,
          ),
          1 => 
          array (
            0 => 'pageUri',
            1 => 'resourceUri',
            2 => 'resourceItem',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      614 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.page',
          ),
          1 => 
          array (
            0 => 'pageUri',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      626 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'moonshine.',
          ),
          1 => 
          array (
            0 => 'fallbackPlaceholder',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      648 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'storage.local',
          ),
          1 => 
          array (
            0 => 'path',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'moonshine.login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/login',
      'action' => 
      array (
        'middleware' => 'moonshine',
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\AuthenticateController@login',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\AuthenticateController@login',
        'as' => 'moonshine.login',
        'namespace' => NULL,
        'prefix' => 'admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.authenticate' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/authenticate',
      'action' => 
      array (
        'middleware' => 'moonshine',
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\AuthenticateController@authenticate',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\AuthenticateController@authenticate',
        'as' => 'moonshine.authenticate',
        'namespace' => NULL,
        'prefix' => 'admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.logout' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/logout',
      'action' => 
      array (
        'middleware' => 'moonshine',
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\AuthenticateController@logout',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\AuthenticateController@logout',
        'as' => 'moonshine.logout',
        'namespace' => NULL,
        'prefix' => 'admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.profile.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\ProfileController@store',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\ProfileController@store',
        'as' => 'moonshine.profile.store',
        'namespace' => NULL,
        'prefix' => 'admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\HomeController@__invoke',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\HomeController',
        'as' => 'moonshine.index',
        'namespace' => NULL,
        'prefix' => 'admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.update-field.through-column' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'admin/update-field/column/{resourceUri}/{resourceItem}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\UpdateFieldController@throughColumn',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\UpdateFieldController@throughColumn',
        'as' => 'moonshine.update-field.through-column',
        'namespace' => NULL,
        'prefix' => 'admin/update-field',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.update-field.through-relation' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'admin/update-field/relation/{resourceUri}/{pageUri}/{resourceItem}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\UpdateFieldController@throughRelation',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\UpdateFieldController@throughRelation',
        'as' => 'moonshine.update-field.through-relation',
        'namespace' => NULL,
        'prefix' => 'admin/update-field',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.async-search' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/async-search/{pageUri}/{resourceUri?}/{resourceItem?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\AsyncSearchController@__invoke',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\AsyncSearchController',
        'as' => 'moonshine.async-search',
        'namespace' => NULL,
        'prefix' => 'admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.notifications.readAll' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\NotificationController@readAll',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\NotificationController@readAll',
        'as' => 'moonshine.notifications.readAll',
        'namespace' => NULL,
        'prefix' => 'admin/notifications',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.notifications.read' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/notifications/{notification}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\NotificationController@read',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\NotificationController@read',
        'as' => 'moonshine.notifications.read',
        'namespace' => NULL,
        'prefix' => 'admin/notifications',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.component' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/component/{pageUri}/{resourceUri?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\ComponentController@__invoke',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\ComponentController',
        'as' => 'moonshine.component',
        'namespace' => NULL,
        'prefix' => 'admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.method' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'admin/method/{pageUri}/{resourceUri?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\MethodController@__invoke',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\MethodController',
        'as' => 'moonshine.method',
        'namespace' => NULL,
        'prefix' => 'admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.reactive' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/reactive/{pageUri}/{resourceUri?}/{resourceItem?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\ReactiveController@__invoke',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\ReactiveController',
        'as' => 'moonshine.reactive',
        'namespace' => NULL,
        'prefix' => 'admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.has-many.form' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/has-many/form/{pageUri}/{resourceUri?}/{resourceItem?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\HasManyController@formComponent',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\HasManyController@formComponent',
        'as' => 'moonshine.has-many.form',
        'namespace' => NULL,
        'prefix' => 'admin/has-many',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.has-many.list' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/has-many/list/{pageUri}/{resourceUri?}/{resourceItem?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\HasManyController@listComponent',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\HasManyController@listComponent',
        'as' => 'moonshine.has-many.list',
        'namespace' => NULL,
        'prefix' => 'admin/has-many',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.page' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/page/{pageUri}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\PageController@__invoke',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\PageController',
        'as' => 'moonshine.page',
        'namespace' => NULL,
        'prefix' => 'admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.crud.massDelete' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/resource/{resourceUri}/crud',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@massDelete',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@massDelete',
        'as' => 'moonshine.crud.massDelete',
        'namespace' => NULL,
        'prefix' => 'admin/resource/{resourceUri}',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.crud.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/resource/{resourceUri}/crud',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'as' => 'moonshine.crud.index',
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@index',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@index',
        'namespace' => NULL,
        'prefix' => 'admin/resource/{resourceUri}',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.crud.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/resource/{resourceUri}/crud/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'as' => 'moonshine.crud.create',
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@create',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@create',
        'namespace' => NULL,
        'prefix' => 'admin/resource/{resourceUri}',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.crud.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/resource/{resourceUri}/crud',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'as' => 'moonshine.crud.store',
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@store',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@store',
        'namespace' => NULL,
        'prefix' => 'admin/resource/{resourceUri}',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.crud.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/resource/{resourceUri}/crud/{resourceItem}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'as' => 'moonshine.crud.show',
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@show',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@show',
        'namespace' => NULL,
        'prefix' => 'admin/resource/{resourceUri}',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.crud.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/resource/{resourceUri}/crud/{resourceItem}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'as' => 'moonshine.crud.edit',
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@edit',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@edit',
        'namespace' => NULL,
        'prefix' => 'admin/resource/{resourceUri}',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.crud.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'admin/resource/{resourceUri}/crud/{resourceItem}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'as' => 'moonshine.crud.update',
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@update',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@update',
        'namespace' => NULL,
        'prefix' => 'admin/resource/{resourceUri}',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.crud.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/resource/{resourceUri}/crud/{resourceItem}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'as' => 'moonshine.crud.destroy',
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@destroy',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\CrudController@destroy',
        'namespace' => NULL,
        'prefix' => 'admin/resource/{resourceUri}',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.handler' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'admin/resource/{resourceUri}/handler/{handlerUri}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\HandlerController@__invoke',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\HandlerController',
        'as' => 'moonshine.handler',
        'namespace' => NULL,
        'prefix' => 'admin/resource/{resourceUri}',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.resource.page' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/resource/{resourceUri}/{pageUri}/{resourceItem?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'moonshine',
          1 => 'MoonShine\\Laravel\\Http\\Middleware\\Authenticate',
        ),
        'uses' => 'MoonShine\\Laravel\\Http\\Controllers\\PageController@__invoke',
        'controller' => 'MoonShine\\Laravel\\Http\\Controllers\\PageController',
        'as' => 'moonshine.resource.page',
        'namespace' => NULL,
        'prefix' => 'admin/resource/{resourceUri}',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/{fallbackPlaceholder}',
      'action' => 
      array (
        'middleware' => 'moonshine',
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:61:"static function (): never {
            \\oops404();
        }";s:5:"scope";s:34:"Illuminate\\Support\\ServiceProvider";s:4:"this";N;s:4:"self";s:32:"00000000000003d30000000000000000";}}',
        'as' => 'moonshine.',
        'namespace' => NULL,
        'prefix' => 'admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => true,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'fallbackPlaceholder' => '.*',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::owrgzO7hZauSNG3B' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/dom-parser/task',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Api\\DomParserController@getTask',
        'controller' => 'App\\Http\\Controllers\\Api\\DomParserController@getTask',
        'namespace' => NULL,
        'prefix' => 'api/dom-parser',
        'where' => 
        array (
        ),
        'as' => 'generated::owrgzO7hZauSNG3B',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::MNxVn1Uw7jmbVlIr' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/dom-parser/result',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Api\\DomParserController@submitResult',
        'controller' => 'App\\Http\\Controllers\\Api\\DomParserController@submitResult',
        'namespace' => NULL,
        'prefix' => 'api/dom-parser',
        'where' => 
        array (
        ),
        'as' => 'generated::MNxVn1Uw7jmbVlIr',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CEZ7R0vyEntNhQsd' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/dom-parser/status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Api\\DomParserController@status',
        'controller' => 'App\\Http\\Controllers\\Api\\DomParserController@status',
        'namespace' => NULL,
        'prefix' => 'api/dom-parser',
        'where' => 
        array (
        ),
        'as' => 'generated::CEZ7R0vyEntNhQsd',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5V10PxvZQ83Jp2lT' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/dom-parser/reset-stale',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Api\\DomParserController@resetStale',
        'controller' => 'App\\Http\\Controllers\\Api\\DomParserController@resetStale',
        'namespace' => NULL,
        'prefix' => 'api/dom-parser',
        'where' => 
        array (
        ),
        'as' => 'generated::5V10PxvZQ83Jp2lT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4oQhSHao1PsFwcGf' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'up',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:807:"function () {
                    $exception = null;

                    try {
                        \\Illuminate\\Support\\Facades\\Event::dispatch(new \\Illuminate\\Foundation\\Events\\DiagnosingHealth);
                    } catch (\\Throwable $e) {
                        if (app()->hasDebugModeEnabled()) {
                            throw $e;
                        }

                        report($e);

                        $exception = $e->getMessage();
                    }

                    return response(\\Illuminate\\Support\\Facades\\View::file(\'/var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Configuration\'.\'/../resources/health-up.blade.php\', [
                        \'exception\' => $exception,
                    ]), status: $exception ? 500 : 200);
                }";s:5:"scope";s:54:"Illuminate\\Foundation\\Configuration\\ApplicationBuilder";s:4:"this";N;s:4:"self";s:32:"00000000000004130000000000000000";}}',
        'as' => 'generated::4oQhSHao1PsFwcGf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WKzUUAnJi8yJRNBS' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '/',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:44:"function () {
    return \\view(\'landing\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000003f30000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::WKzUUAnJi8yJRNBS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'moonshine.price-history.delete-all' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'price-history/delete-all',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'moonshine',
        ),
        'uses' => 'App\\Http\\Controllers\\PriceHistoryController@deleteAll',
        'controller' => 'App\\Http\\Controllers\\PriceHistoryController@deleteAll',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'moonshine.price-history.delete-all',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documentation' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'docs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\DocumentationController@index',
        'controller' => 'App\\Http\\Controllers\\DocumentationController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documentation',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'storage.local' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'storage/{path}',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:3:{s:4:"disk";s:5:"local";s:6:"config";a:5:{s:6:"driver";s:5:"local";s:4:"root";s:33:"/var/www/html/storage/app/private";s:5:"serve";b:1;s:5:"throw";b:0;s:6:"report";b:0;}s:12:"isProduction";b:0;}s:8:"function";s:323:"function (\\Illuminate\\Http\\Request $request, string $path) use ($disk, $config, $isProduction) {
                    return (new \\Illuminate\\Filesystem\\ServeFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path);
                }";s:5:"scope";s:47:"Illuminate\\Filesystem\\FilesystemServiceProvider";s:4:"this";N;s:4:"self";s:32:"00000000000004180000000000000000";}}',
        'as' => 'storage.local',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'path' => '.*',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
