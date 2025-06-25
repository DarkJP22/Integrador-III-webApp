<div class="modal fade" id="setupSchedule" role="dialog" aria-labelledby="setupSchedule">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title" id="setupScheduleLabel">Programando tu agenda</h4>
            </div>
            <div class="modal-body" data-modaldate data-slotduration="30">
                <div class="callout callout-info">
                    <h4>Informacion importante!</h4>

                    <p>Programa tu agenda buscando los consultorios o clinicas donde trabajas y asignale una fecha y horas determinadas. </p>
                </div>
                <div class="content form-horizontal">

                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label for="selectSlotDuration" class="cffol-sm-7 control-label">Pacientes cada: </label>
                                <div class="ffcol-sm-5">
                                    @php
                                        $user_settings = $medic->getAllSettings();
                                    @endphp
                                    <select name="selectSlotDurationModal" id="selectSlotDurationModal" class="form-control">
                                        @foreach ($slotDurations as $slot)
                                            <option value="{{ $slot->value }}" {{ $user_settings ? ($user_settings['slotDuration'] == $slot->value ? 'selected' : '') : '' }}>{{ $slot->text }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-8">
                            <div class="form-group">
                                <select name="search-offices" id="search-offices" class="search-offices form-control select2 " style="width: 100%;">
                                    <!-- <option value=""></option> -->
                                </select>
                                <ul class="search-list todo-list">

                                </ul>
                            </div>


                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-4">
                            <div class="form-group">
                                <label>Fecha:</label>
                                <div class="input-group date col-sm-10">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>

                                    <input type="text" class="form-control pull-right" name="date" id="datetimepicker1" autocomplete="false">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <div class="form-group">
                                <label>Hora de inicio:</label>

                                <div class="input-group col-xs-9 col-sm-10">
                                    <input type="text" class="form-control " name="start" id="datetimepicker2" autocomplete="false">

                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <div class="form-group">
                                <label>Hora de fin:</label>

                                <div class="input-group col-xs-9 col-sm-10">
                                    <input type="text" class="form-control " name="end" id="datetimepicker3" autocomplete="false">

                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">

                        <button type="button" class="btn btn-primary add-cita">Agregar a agenda</button>
                    </div>

                </div>






            </div>
            <div class="modal-footer">


                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar Asistente</button>

            </div>
        </div>
    </div>
</div>
