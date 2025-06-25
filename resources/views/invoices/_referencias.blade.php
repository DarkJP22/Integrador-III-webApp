       <table class="table table-striped" style="width:100%;">
            <thead>
                <tr>
                    <th><b>Tipo Documento</b></th>
                    <th><b>Numero Documento</b></th>
                    <th><b>Fecha emision</b></th>
                    <th><b>Codigo referencia</b></th>
                    <th><b>Razon</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->referencias as $ref)
                <tr>
                
                    <td>{{ $ref->tipo_documento_name }}</td>
                    <td>{{ $ref->NumeroDocumento }}</td>
                    <td>{{ $ref->FechaEmision }}</td>
                    <td>{{ $ref->codigo_referencia_name }}</td>
                    <td>{{ $ref->Razon }}</td>
                    
                </tr>
                @endforeach
            
            </tbody>
        </table>