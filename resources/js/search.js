$(function () {

    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };

    var $root = $('html, body');
   
    $(window).on('load', function() {

     
        var href = '#no-more-tables';
        
        if (typeof($(href)) != 'undefined' && $(href).length > 0) {
            var anchor = '';

            if (href.indexOf('#') != -1) {
                anchor = href.substring(href.lastIndexOf('#'));
            }

            if (anchor.length > 0) {
               
                $root.animate({
                    scrollTop: $(anchor).offset().top-80
                }, 500, function() {
                    window.location.hash = anchor;
                });
               
            }
        }
   

    });

    if( isMobile.any() ) {
        $('.box-create-appointment').hide();
        $('.breadcrumb').hide();
      
       
    }else{
      
        //$('.box-search-filters').removeClass('collapsed-box');
     
    }


    //llamando la funcion inicial para ver trabajar la API
    function success(pos) {
        var crd = pos.coords;
        $('.loader-geo').addClass('hide');

        $('input[name="lat"]').val(crd.latitude);
        $('input[name="lon"]').val(crd.longitude);
            
        var speciality = $('select[name="speciality"]').val();

        if(speciality === undefined || speciality != ''){
            $('.btn-search').click();
            
        }else
        {
            alert('Selecciona una especialidad');

        }
    }

    function error(err) {
        // console.warn(`ERROR(${err.code}): ${err.message}`);
        console.warn('Error: '+ err.message);
        $('.loader-geo').addClass('hide');
    }

    $('.btn-geo').on('click', function () {
        var options = {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        };
        $('.loader-geo').removeClass('hide');
        window.navigator.geolocation.getCurrentPosition(success, error, options);

    });

    $('.callout button.close').on('click', function () {
        $(this).parents('.callout').hide();
    });
       


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

      
    selectProvincias.change();
     



    $('#myModal').on('shown.bs.modal', function (event) {
          
        var button = $(event.relatedTarget);
        var lat = button.attr('data-lat');
        var lon = button.attr('data-lon');
        var address = button.attr('data-address');
        var redes = ['email', 'twitter', 'facebook', 'googleplus', 'whatsapp'];

        if( !isMobile.any() ) {
            redes = ['email', 'twitter', 'facebook', 'googleplus'];
        }
      
        $('.share').jsSocials({
            shares: redes,
            url: 'http://maps.google.com/?saddr=Current+Location&daddr='+lat +',' + lon,
            text: address,
            showLabel: false,
            showCount: false,
            shareIn: 'popup',
           
        });
          
        
     
       
    });

    $('#locationModal').on('shown.bs.modal', function (event) {
          
        var button = $(event.relatedTarget);
        var lat = button.attr('data-lat');
        var lon = button.attr('data-lon');
        var phone = button.attr('data-phone');
        
        $(this).find('.modal-body').html('');
        if(phone)
        {

            $(this).find('.modal-body').html('Llamar: <a href="tel:'+ phone +'" title="Llamar a este número">'+ phone +'</a>');
            $(this).find('.modal-title').html('Número de teléfono de contacto');

        }else{

            if(!lat || !lon)
            {

                $(this).find('.modal-body').html('<p>No se puede abrir ninguna direccion por que el consultorio no tiene registrado su latitud o longitud</p>');

            }else{

                $(this).find('.modal-body').append('<a href="waze://?ll='+ lat +','+ lon +'&amp;navigate=yes"  target="_blank" class="btn btn btn-app"><i class="fa fa-map-marker"></i> <strong>Abrir en Waze</strong></a><a href="http://maps.google.com/?saddr=Current+Location&daddr='+ lat +','+ lon + '" target="_blank" class="btn btn btn-app"><i class="fa fa-map-marker"></i> <strong>Abrir en Google Maps</strong></a>');
            }
        }
          
        
     
       
    });



});