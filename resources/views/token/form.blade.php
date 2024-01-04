<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        @if(isset($id))
            {!! Form::model($data, ['route' => ['token.update', $id], 'method' => 'patch' ]) !!}
        @else
            {!! Form::open(['route' => ['token.store'], 'method' => 'post' ]) !!}
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
                                    {{ Form::label('tokenperprice', __('message.tokenperprice').' <span class="text-danger">*</span>',['class' => 'form-control-label'], false ) }}
                                    {{ Form::text('token_per_price', old('token_per_price'),[ 'placeholder' => __('message.tokenperprice'),'class' =>'form-control','required']) }}
                                </div>
                                
                                 <div class="form-group col-md-4">
                                    {{ Form::label('quantity', __('message.quantity').' <span class="text-danger">*</span>',['class' => 'form-control-label'], false ) }}
                                    {{ Form::text('quantity', old('quantity'),[ 'placeholder' => __('message.quantity'),'class' =>'form-control','required']) }}
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
