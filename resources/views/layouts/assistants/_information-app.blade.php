@if(!auth()->user()->active)
    <div class="notification-app alert-danger">
        <div class="swiper slider-notifications">
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">
                <!-- Slides -->


                @if( !auth()->user()->active )
                    <div class="swiper-slide notification-app-item ">
                        Esta cuenta está inactiva mientras el administrador verifica tus datos!

                    </div>
                @endif


            </div>


            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev tw-text-white"></div>
            <div class="swiper-button-next tw-text-white"></div>


        </div>


    </div>

@endif