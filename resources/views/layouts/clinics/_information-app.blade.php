@if(count($notifications))
    <div class="notification-app alert-danger">
        <div class="swiper slider-notifications">
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">

                @foreach($notifications as $notification)
                    @if($notification === 'location')
                        <div class="swiper-slide notification-app-item ">

                            <update-office-location :office="{{ $office }}"></update-office-location>
                        </div>
                    @endif
                    @if($notification === 'active')
                        <div class="swiper-slide notification-app-item ">

                            Esta cuenta está inactiva mientras el administrador verifica tus datos. Puedes seguir editando
                            tus opciones mientras se activa.

                        </div>
                    @endif
                    @if($notification === 'subscription')
                        <div class="swiper-slide notification-app-item">
                            Tienes facturas pendientes de pago <a href="{{ url('/clinic/profiles?tab=payments') }}"
                                                                  class="btn btn-secondary btn-sm">Ver Detalles</a>

                        </div>
                    @endif
                @endforeach


            </div>


            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev tw-text-white"></div>
            <div class="swiper-button-next tw-text-white"></div>


        </div>


    </div>

@endif