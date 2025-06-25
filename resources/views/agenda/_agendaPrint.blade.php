  <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Paciente</th>
                      <th>Motivo</th>
                      <th>Fecha</th>
                      <th>De</th>
                      <th>A</th>
                      
                    </tr>
                  </thead>
                  
                  @foreach($appointments as $appointment)
                     
                      <tr>
                        <td data-title="ID">{{ $appointment->id }}</td>
                        <td data-title="Paciente">
                         
                            {{ ($appointment->patient) ? $appointment->patient->fullname : 'Paciente Eliminado' }}
                         
                        </td>
                        <td data-title="Motivo">{{ $appointment->title }}</td>
                        <td data-title="Fecha">{{ \Carbon\Carbon::parse($appointment->date)->toDateString() }}</td>
                        <td data-title="De">{{ \Carbon\Carbon::parse($appointment->start)->format('h:i:s A') }}</td>
                        <td data-title="a">{{ \Carbon\Carbon::parse($appointment->end)->format('h:i:s A') }}</td>
                       
                      </tr>
                    
                  @endforeach
                   <tr>

                    @if ($appointments)
                        <td  colspan="7" class="pagination-container">{!!$appointments->appends(['date' => $search['date']])->render()!!}</td>
                    @endif


                    </tr>
                </table>
                  @if (!$appointments->count())
                        <h4 class="text-center">Aun no tienes citas para el dia de hoy {{ $search['date'] }}</h4>
                    @endif