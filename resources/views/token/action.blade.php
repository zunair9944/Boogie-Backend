
<?php
    $auth_user= authSession();
?>
{{ Form::open(['route' => ['token.destroy', $id], 'method' => 'delete','data--submit'=>'token'.$id]) }}
<div class="d-flex justify-content-end align-items-center">
    @if($auth_user->can('service edit'))
    <a class="mr-2" href="{{ route('token.edit', $id) }}" title="{{ __('message.update_form_title',['form' => __('message.token') ]) }}"><i class="fas fa-edit text-primary"></i></a>
    @endif
    
    {{-- @if($auth_user->can('token delete')) --}}
    <a class="mr-2 text-danger" href="javascript:void(0)" data--submit="token{{$id}}" 
        data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.token') ]) }}"
        title="{{ __('message.delete_form_title',['form'=>  __('message.token') ]) }}"
        data-message='{{ __("message.delete_msg") }}'>
        <i class="fas fa-trash-alt"></i>
    </a>
    {{-- @endif --}}
</div>
{{ Form::close() }}