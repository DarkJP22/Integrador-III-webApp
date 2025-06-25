<div class="general-review">
    <h2>Nivel de satisfacción en general</h2>
    <div class="ratings">
        
        

            @if(1 <= ($profileUser->rating_service_cache + $profileUser->rating_medic_cache)/2 && ($profileUser->rating_service_cache + $profileUser->rating_medic_cache)/2 < 2)
            <img src="/img/muy-malo.png" alt="1" title="Muy Malo">
            @else
            <img src="/img/muy-malo-off.png" alt="1" title="Muy Malo">
            @endif
            @if(2 <= ($profileUser->rating_service_cache + $profileUser->rating_medic_cache)/2 && ($profileUser->rating_service_cache + $profileUser->rating_medic_cache)/2 < 3)
            <img src="/img/malo.png" alt="2" title="Malo">
            @else
            <img src="/img/malo-off.png" alt="2" title="Malo">
            @endif
            @if(3 <= ($profileUser->rating_service_cache + $profileUser->rating_medic_cache)/2 && ($profileUser->rating_service_cache + $profileUser->rating_medic_cache)/2 < 4)
            <img src="/img/regular.png" alt="3" title="regular">
            @else
            <img src="/img/regular-off.png" alt="3" title="regular">
            @endif
            @if(4 <= ($profileUser->rating_service_cache + $profileUser->rating_medic_cache)/2 && ($profileUser->rating_service_cache + $profileUser->rating_medic_cache)/2 < 5)
            <img src="/img/bueno.png" alt="4" title="Bueno">
            @else
            <img src="/img/bueno-off.png" alt="4" title="Bueno">
            @endif
            @if(5 <= ($profileUser->rating_service_cache + $profileUser->rating_medic_cache)/2)
            <img src="/img/excelente.png" alt="5" title="Excelente">
            @else
            <img src="/img/excelente-off.png" alt="5" title="Excelente">
            @endif
        
        

        </div>
        <div class="ratings-targets">{!! number_format(($profileUser->rating_service_cache + $profileUser->rating_medic_cache)/2, 1) !!} Puntos</div>
    </div>
    <h3>Nivel de satisfación del  servicio recibido</h3>
                    
        <!-- @for ($i=1; $i <= 5 ; $i++)
            <span class="fa fa-star{!! ($i <= $profileUser->rating_service_cache) ? '' : '-o'!!}"></span>
        @endfor -->
        <div class="ratings">
        
        

            @if(1 <= $profileUser->rating_service_cache && $profileUser->rating_service_cache < 2)
            <img src="/img/muy-malo.png" alt="1" title="Muy Malo">
            @else
            <img src="/img/muy-malo-off.png" alt="1" title="Muy Malo">
            @endif
            @if(2 <= $profileUser->rating_service_cache && $profileUser->rating_service_cache < 3)
            <img src="/img/malo.png" alt="2" title="Malo">
            @else
            <img src="/img/malo-off.png" alt="2" title="Malo">
            @endif
            @if(3 <= $profileUser->rating_service_cache && $profileUser->rating_service_cache < 4)
            <img src="/img/regular.png" alt="3" title="regular">
            @else
            <img src="/img/regular-off.png" alt="3" title="regular">
            @endif
            @if(4 <= $profileUser->rating_service_cache && $profileUser->rating_service_cache < 5)
            <img src="/img/bueno.png" alt="4" title="Bueno">
            @else
            <img src="/img/bueno-off.png" alt="4" title="Bueno">
            @endif
            @if(5 <= $profileUser->rating_service_cache)
            <img src="/img/excelente.png" alt="5" title="Excelente">
            @else
            <img src="/img/excelente-off.png" alt="5" title="Excelente">
            @endif
        
        

        </div>
        <div class="ratings-targets">{!! number_format($profileUser->rating_service_cache, 1) !!} Puntos</div>
    
    <div class="box box-default box-comments">
    <div class="box-header">
    <i class="fa fa-comments-o"></i>

    <h3 class="box-title">Comentarios</h3>

    <div class="box-tools pull-right" data-toggle="tooltip" title="Status">
        Total: {!! $profileUser->rating_service_count !!}
        
    </div>
    </div>
    <div class="box-body chat comments-box">
    
    @foreach($reviewsS = $profileUser->reviewsService()->orderBy('created_at','DESC')->paginate(5) as $review)
    <!-- chat item -->
    <div class="item">
        <img src="/img/user3-128x128.jpg" alt="user image" class="offline">

        <p class="message">
        <a href="#" class="name">
            <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> {{ $review->created_at }}</small>
            {{ $review->rating }} Puntos
        </a>
        {{ $review->comment }}
        </p>
    </div>
    <!-- /.item -->
        @endforeach
    
    </div>
    <div class="box-footer">
        {{ $reviewsS->appends(['tab' => 'reviews'])->render() }}
    </div>
</div>
    
    
    <h3>Nivel de satisfacción con el desempeño del médico</h3>
    
    <div class="ratings">
        
        

            @if(1 <= $profileUser->rating_medic_cache && $profileUser->rating_medic_cache < 2)
            <img src="/img/muy-malo.png" alt="1" title="Muy Malo">
            @else
            <img src="/img/muy-malo-off.png" alt="1" title="Muy Malo">
            @endif
            @if(2 <= $profileUser->rating_medic_cache && $profileUser->rating_medic_cache < 3)
            <img src="/img/malo.png" alt="2" title="Malo">
            @else
            <img src="/img/malo-off.png" alt="2" title="Malo">
            @endif
            @if(3 <= $profileUser->rating_medic_cache && $profileUser->rating_medic_cache < 4)
            <img src="/img/regular.png" alt="3" title="regular">
            @else
            <img src="/img/regular-off.png" alt="3" title="regular">
            @endif
            @if(4 <= $profileUser->rating_medic_cache && $profileUser->rating_medic_cache < 5)
            <img src="/img/bueno.png" alt="4" title="Bueno">
            @else
            <img src="/img/bueno-off.png" alt="4" title="Bueno">
            @endif
            @if(5 <= $profileUser->rating_medic_cache)
            <img src="/img/excelente.png" alt="5" title="Excelente">
            @else
            <img src="/img/excelente-off.png" alt="5" title="Excelente">
            @endif
        
        

        </div>
        <div class="ratings-targets">{!! number_format($profileUser->rating_medic_cache, 1) !!} Puntos</div>
        <div class="box box-default box-comments">
        <div class="box-header">
            <i class="fa fa-comments-o"></i>

            <h3 class="box-title">Comentarios</h3>

            <div class="box-tools pull-right" data-toggle="tooltip" title="Status">
            Total: {!! $profileUser->rating_service_count !!}
            
            </div>
        </div>
        <div class="box-body chat comments-box">
        
        @foreach($reviewsM = $profileUser->reviewsMedic()->orderBy('created_at','DESC')->paginate(5) as $review)
            <!-- chat item -->
            <div class="item">
            <img src="/img/user3-128x128.jpg" alt="user image" class="offline">

            <p class="message">
                <a href="#" class="name">
                <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> {{ $review->created_at }}</small>
                {{ $review->rating }} Puntos
                </a>
                {{ $review->comment }}
            </p>
            </div>
            <!-- /.item -->
            @endforeach
            
        </div>
            <div class="box-footer">
            {{ $reviewsM->appends(['tab' => 'reviews'])->render() }}
        </div>
        </div>
                  