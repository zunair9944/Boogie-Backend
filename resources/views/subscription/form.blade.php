<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        @if(isset($id))
            {!! Form::model($data, ['route' => ['subscription.update', $id], 'method' => 'patch' ]) !!}
        @else
            {!! Form::open(['route' => ['subscription.store'], 'method' => 'post' ]) !!}
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    {{ Form::label('subscriptionname', __('message.subscriptionname').' <span class="text-danger">*</span>',['class' => 'form-control-label'], false ) }}
                                    {{ Form::text('subscription_name', old('subscription_name'),[ 'placeholder' => __('message.subscriptionname'),'class' =>'form-control','required']) }}
                                </div>
                                
                                 <div class="form-group col-md-4">
                                    {{ Form::label('subscriptionprice', __('message.subscriptionprice').' <span class="text-danger">*</span>',['class' => 'form-control-label'], false ) }}
                                    {{ Form::text('price', old('price'),[ 'placeholder' => __('message.subscriptionprice'),'class' =>'form-control','required']) }}
                                </div>
                                
                            </div>
                            <hr>
                            {{ Form::submit( __('message.save'), ['class'=>'btn btn-md btn-primary float-right']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    @section('bottom_script')
    @endsection
</x-master-layout>
