@if($subscription)
        <form method="POST" action="{{ url('/subscriptions/'.$user->subscription->id) }}" class="form-horizontal">
            {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
            <div class="form-group">
                <label for="plan" class="col-sm-2 control-label">Subscripcion</label>

                <div class="col-sm-10">
                <select class="form-control select2" style="width: 100%;" name="plan_id" required>
                    
                    @foreach($plans as $plan)
                        <option {{ ($plan->id == $user->subscription->plan_id) ? 'selected' : '' }} value="{{ $plan->id }}" >{{ $plan->title }}</option>
                    @endforeach
                    
                </select>
                
                @if ($errors->has('plan_id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('plan_id') }}</strong>
                    </span>
                @endif
                </div>
            </div>
            
                
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button type="submit" class="btn btn-danger" form="form-delete-subscription" formaction="{!! url('/subscriptions/'.$user->subscription->id) !!}">Cancelar Subscripción</button>
        
    
                    </div>
                </div>
                
        </form>
        @else 
            <form method="POST" action="{{ url('/admin/users/'.$user->id.'/subscriptions') }}" class="form-horizontal">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="plan" class="col-sm-2 control-label">Subscripción</label>

                <div class="col-sm-10">
                <select class="form-control select2" style="width: 100%;" name="plan_id" required>
                    
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" >{{ $plan->title }}</option>
                    @endforeach
                    
                </select>
                
                @if ($errors->has('plan_id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('plan_id') }}</strong>
                    </span>
                @endif
                </div>
            </div>
                
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">Agregar Subscription</button>
                    
    
                    </div>
                </div>
                
        </form>
@endif