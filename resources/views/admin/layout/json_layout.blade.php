<?php

echo json_encode([
    'title'=>!empty(trim($__env->yieldContent('title'))) ? $__env->yieldContent('title') : 'Admin',
    'full_title'=>!empty(trim($__env->yieldContent('title'))) ? 'Admin | '.$__env->yieldContent('title') : 'Admin',
    'title_icon'=>!empty(trim($__env->yieldContent('title-icon'))) ? $__env->yieldContent('title-icon') : 'files-o',
    'styles'=>compressContent($__env->yieldPushContent('styles')),
    'breadcrumb'=>compressContent('<li><a href="'.route('admin.dashboard').'"><i class="fa fa-dashboard"></i> '.trans('general.dashboard').'</a></li>'.$__env->yieldContent('breadcrumb').'<li class="active xbp-icon-title"></li>'),
    'content'=>compressContent($__env->yieldContent('content')),
    'scripts'=>compressContent($__env->yieldPushContent('scripts'))
]);
