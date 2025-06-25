@extends('layouts.pharmacies.app')

@section('header')

@endsection
@section('content')
    <section class="content-header">
      <h1>Médicos</h1>
    
    </section>
    <section class="content">
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header">
                
                  <form action="/pharmacy/medics" method="GET" autocomplete="off">
                    <div class="form-group">
                       
                        <div class="col-sm-3">
                          <div class="input-group input-group" >
                            
                              
                              <input type="text" name="q" class="form-control pull-right" placeholder="Buscar..." value="{{ isset($search) ? $search['q'] : '' }}">
                              <div class="input-group-btn">

                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                              </div>
                            
                            
                          </div>
                          </div>
                          <div class="col-sm-3">
                              <select name="province" id="province" class="form-control">
                                  <option value="">-- Filtro por Provincia --</option>
                                  <option value="1" {{ isset($search['province']) ? ($search['province'] == '1' ? 'selected' : '') : '' }}>San Jose</option>
                                  <option value="2" {{ isset($search['province']) ? ($search['province'] == '2' ? 'selected' : '') : '' }}>Alajuela</option>
                                  <option value="3" {{ isset($search['province']) ? ($search['province'] == '3' ? 'selected' : '') : '' }}>Cartago</option>
                                  <option value="4" {{ isset($search['province']) ? ($search['province'] == '4' ? 'selected' : '') : '' }}>Heredia</option>
                                  <option value="5" {{ isset($search['province']) ? ($search['province'] == '5' ? 'selected' : '') : '' }}>Guanacaste</option>
                                  <option value="6" {{ isset($search['province']) ? ($search['province'] == '6' ? 'selected' : '') : '' }}>Puntarenas</option>
                                  <option value="7" {{ isset($search['province']) ? ($search['province'] == '7' ? 'selected' : '') : '' }}>Limon</option>
                                  
                              </select>
                          
                          </div>
                          <div class="col-sm-3">
                             
                                
                                <select class="form-control select2" style="width: 100%;" name="canton" placeholder="-- Selecciona canton --">
                                  <option></option>
                                  
                                </select>
                                <input type="hidden" name="selectedCanton" value="{{ isset($search['canton']) ? $search['canton'] : '' }}">
                              
                             
                            <!-- /input-group -->
                          </div>
                          <!-- /.col-lg-6 -->
                          <div class="col-sm-3">
                             
                                <select class="form-control select2" style="width: 100%;" name="district" placeholder="-- Selecciona Distrito --">
                                  <option></option>
                                  
                                </select>
                                <input type="hidden" name="selectedDistrict" value="{{ isset($search['district']) ? $search['district'] : '' }}">
                                  
                            
                            <!-- /input-group -->
                          </div>

                    </div>
                    
                  </form>
                <div class="box-tools">
                  
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body table-responsive no-padding" id="no-more-tables">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Teléfono</th>
                      <th>Email</th>
                      <th>Especialidades</th>
                      <th>Consultorios</th>
                    </tr>
                  </thead>
                  @foreach($medics as $medic)
                    <tr>
                     
                      <td data-title="ID">{{ $medic->id }}</td>
                      <td data-title="Nombre">
                        
                        {{ $medic->name }}
                      
                     
                      </td>
                      <td data-title="Teléfono">{{ $medic->phone_number }}</td>
                      <td data-title="Email">{{ $medic->email }}</td>
                      
                     <td data-title="Especialidades"> 
                       @foreach($medic->specialities as $speciality)
                            <span class="btn btn-warning btn-xs">{{ $speciality->name }}</span>
                       @endforeach

                       </td>
                      <td data-title="" style="padding-left: 5px;">

                         @forelse($medic->offices()->where('verified', 1)->get() as $office)
                              <div class="td-lugar">
                                  <div class="td-lugar-name">
                                  <span >{{ $office->name }}</span> <br>
                                 
                                  </div>
                                
                                  <div class="td-lugar-info">
                                      <p>
                                      <span>{{ $office->province_name }}. {{ $office->address }}</span>
                                      </p>
                                      <p>
                                      
                                      @if($office->active) 
                                        <a href="{{ url('/pharmacy/medics/'.$medic->id.'/offices/'.$office->id .'/agenda') }}" class="btn btn-primary btn-xs"><i class="fa fa-calendar"></i> Ver Agenda</a>
                                      @endif
                                      </p>
                                </div>
                                
                              </div>
                              
                          

                          
                          @empty
                            <span class="label label-danger">Desconocido</span>
                          @endforelse
                       
                        
                        
                       
                      </td>
                    </tr>
                  @endforeach
                    @if ($medics)
                        <td  colspan="5" class="pagination-container">{!!$medics->appends(['q' => $search['q']])->render()!!}</td>
                    @endif
                </table>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
        </div>

    </section>


<form method="post" id="form-active-inactive">
 {{ csrf_field() }}
</form>
<form method="post" id="form-delete" data-confirm="Estas Seguro?">
  <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
</form>
<!-- <form method="post" id="form-addToYourPatients" data-confirm="Estas Seguro?">
  {{ csrf_field() }}
</form> -->
@endsection
@push('scripts')
  <script>

      // $('#province').on('change', function (e) {


      //   $(this).parents('form').submit();

      // });

        // provincias cantones y distritos
         
        var selectProvincias = $('select[name=province]'),
        selectCantones = $('select[name=canton]'),
        selectDistritos = $('select[name=district]'),
        ubicaciones = window.provincias,
        cantonesOfselectedProvince = [],
        selectedCanton = $('input[name=selectedCanton]').val(),
        selectedDistrict = $('input[name=selectedDistrict]').val();
        

        selectCantones.empty();
        selectDistritos.empty();

  
        selectProvincias.change(function() {
          
            var $this =  $(this);
            selectCantones.empty();
            selectDistritos.empty();
            cantonesOfselectedProvince = [];
            $.each(ubicaciones, function(index,provincia) {

                if(provincia.id == $this.val()){
                       selectCantones.append('<option value=""></option>');
                      $.each(provincia.cantones, function(index,canton) {
                        
                    
                          var o = new Option(canton.title, canton.id);
                          
                          if(canton.id == selectedCanton)      
                            o.selected=true;

                          selectCantones.append(o);

                          cantonesOfselectedProvince.push(canton);
                      });
                      
                      selectCantones.change();
                  }
            });

        });

        selectCantones.change(function() {
          
            var $this =  $(this);
            selectDistritos.empty();
           
            $.each(cantonesOfselectedProvince, function(index,canton) {
                
                if(canton.id == $this.val()){
                      selectDistritos.append('<option value=""></option>');
                      $.each(canton.distritos, function(index,distrito) {

                          //distritos.append('<option value="' + distrito + '">' + distrito + '</option>');
                          var o = new Option(distrito.title, distrito.id);
                          
                          if(distrito.id == selectedDistrict)      
                            o.selected=true;

                          selectDistritos.append(o);
                      });
                  }
            });

        });

      
      //selectProvincias.change();
  </script>
@endpush
