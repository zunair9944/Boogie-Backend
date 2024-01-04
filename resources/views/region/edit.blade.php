<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        {!! Form::model($data, ['route' => ['region.update', $id], 'method' => 'patch' , 'enctype' => 'multipart/form-data']) !!}
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
                                    {{ Form::label('name',__('message.name').' <span class="text-danger">*</span>',['class'=>'form-control-label'], false ) }}
                                    {{ Form::text('name',old('name'),['placeholder' => __('message.name'),'class' =>'form-control','required']) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ Form::label('distance_unit',__('message.distance_unit').' <span class="text-danger">*</span>',['class'=>'form-control-label'],false) }}
                                    {{ Form::select('distance_unit',[ 'km' => __('message.km') ,'mile' => __('message.mile') ], old('distance_unit') ,[ 'class' =>'form-control select2js','required']) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ Form::label('timezone', __('message.timezone'), ['class' => 'form-control-label']) }}
                                    {{ Form::select('timezone', [ $data['timezone'] => timeZoneList()[$data['timezone']] ] , old('timezone') , [
                                        'data-ajax--url' => route('ajax-list', [ 'type' => 'timezone' ]),
                                        'data-placeholder' => __('message.select_field', [ 'name' => __('message.timezone') ]),
                                        'class' =>'form-control select2js', 'required'
                                        ])
                                    }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ Form::label('status',__('message.status').' <span class="text-danger">*</span>',['class'=>'form-control-label'],false) }}
                                    {{ Form::select('status',[ '1' => __('message.active') ,'0' => __('message.inactive') ], old('status'), [ 'class' =>'form-control select2js','required']) }}
                                </div>
                                <div class="form-group col-md-12" style="height:500px;">
                                    <div id="map-canvas"></div>
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
        <script>
            $(function(){
                var map;
                var drawingManager;
                var last_latlong = null;
                var bounds = new google.maps.LatLngBounds();
                var polygons = [];
                
              
                function auto_grow() {
                    let element = document.getElementById('coordinates');
                    element.style.height = '5px';
                    element.style.height = (element.scrollHeight)+'px';
                }

                if(window.google || window.google.maps) {
                    initialize();
                }
                function resetMap(controlDiv)
                {
                    // Set CSS for the control border.
                    const controlUI = document.createElement('div');
                    controlUI.style.backgroundColor = '#fff';
                    controlUI.style.border = '2px solid #fff';
                    controlUI.style.borderRadius = '3px';
                    controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
                    controlUI.style.cursor = 'pointer';
                    controlUI.style.marginTop = '8px';
                    controlUI.style.marginBottom = '22px';
                    controlUI.style.textAlign = 'center';
                    controlUI.title = "{{ __('message.reset_map') }}";
                    controlDiv.appendChild(controlUI);
                    // Set CSS for the control interior.
                    const controlText = document.createElement('div');
                    controlText.style.color = 'rgb(25,25,25)';
                    controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
                    controlText.style.fontSize = '10px';
                    controlText.style.lineHeight = '16px';
                    controlText.style.paddingLeft = '2px';
                    controlText.style.paddingRight = '2px';
                    controlText.innerHTML = 'X';
                    controlUI.appendChild(controlText);
                    // Setup the click event listeners: simply set the map to Chicago.
                    controlUI.addEventListener('click', () => {
                        last_latlong.setMap(null);
                        $('#coordinates').val('');
                    });
                }
            });
        </script>
    @endsection
</x-master-layout>
