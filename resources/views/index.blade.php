@extends('layouts.app')

@section('title', 'Talento')

@section('content')

    <!-- Carousel Start -->
    <div class="container-fluid p-0">
        <div id="header-carousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="img/carousel-1.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase mb-md-3">TALENTO express</h4>
                            <h1 class="display-3 text-white mb-md-4">Encuentra el servicio adecuado para ti</h1>
                            <a href="#explorar" class="btn btn-primary py-md-3 px-md-5 mt-2">Explorar</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="img/carousel-2.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase mb-md-3">Talento</h4>
                            <h1 class="display-3 text-white mb-md-4">Ofrece tus servicios de forma fácil y segura </h1>
                            <a href="{{ route('servicio.index') }}" class="btn btn-primary py-md-3 px-md-5 mt-2">Mis
                                Servicios</a>
                        </div>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
                <div class="btn btn-dark" style="width: 45px; height: 45px;">
                    <span class="carousel-control-prev-icon mb-n2"></span>
                </div>
            </a>
            <a class="carousel-control-next" href="#header-carousel" data-slide="next">
                <div class="btn btn-dark" style="width: 45px; height: 45px;">
                    <span class="carousel-control-next-icon mb-n2"></span>
                </div>
            </a>
        </div>
    </div>
    <!-- Carousel End -->




    <!-- About Start -->
    <div class="container-fluid py-5" id="nosotros">
        <div class="container pt-5">
            <div class="row">
                <div class="col-lg-6" style="min-height: 500px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute w-100 h-100" src="img/about.jpg" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 pt-5 pb-lg-5">
                    <div class="about-text bg-white p-4 p-lg-5 my-lg-5">
                        <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Nosotros</h6>
                        <h1 class="mb-3">La Manera Simple y Segura de Encontrar y Ofrecer Servicios Profesionales.
                        </h1>
                        <p>Talento Express es tu plataforma para encontrar y ofrecer servicios profesionales de manera fácil y
                            segura. Con una interfaz intuitiva, puedes explorar diversas categorías de servicios,
                            revisar perfiles detallados y comunicarte directamente con proveedores especializados para
                            acordar los términos. Únete a Talento Express hoy y simplifica la manera en que contratas y ofreces
                            servicios profesionales.</p>
                        <div class="row mb-4">
                            <div class="col-6">
                                <img class="img-fluid" src="img/about-1.jpg" alt="">
                            </div>
                            <div class="col-6">
                                <img class="img-fluid" src="img/about-2.jpg" alt="">
                            </div>
                        </div>
                        <a href="#explorar" class="btn btn-primary mt-1">Explorar</a>
                        <a href="#testimonios" class="btn btn-primary mt-1">Comentarios</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- Feature Start -->
    <div class="container-fluid pb-5">
        <div class="container pb-5">
            <div class="row">
                <div class="col-md-4">
                    <div class="d-flex mb-4 mb-lg-0">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary mr-3"
                            style="height: 100px; width: 100px;">
                            <i class="fa fa-2x fa-money-check-alt text-white"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <h5 class="">Ajústate a tu presupuesto</h5>
                            <p class="m-0">Encuentra el servicio adecuado para ti.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex mb-4 mb-lg-0">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary mr-3"
                            style="height: 100px; width: 100px;">
                            <i class="fa fa-2x fa-award text-white"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <h5 class="">Paga cuando estés conforme</h5>
                            <p class="m-0">Cotizaciones por adelantado para evitar sorpresas.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex mb-4 mb-lg-0">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary mr-3"
                            style="height: 100px; width: 100px;">
                            <i class="fa fa-2x fa-building text-white"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <h5 class="">Obtén un trabajo de calidad con rapidez</h5>
                            <p class="m-0">Entrega tu proyecto a un talento en minutos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Feature End -->


    <!-- Destination Start -->
    <div class="container-fluid py-5" id="explorar">
        <div class="container pt-5 pb-3">
            <div class="text-center mb-3 pb-3">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Explorar</h6>
                <h1>Categorías</h1>
            </div>
            <div class="row">
            @foreach ($categorias as $categoria)
                <div class="col-lg-6 mb-4"> <!-- Para dos categorías por fila en pantallas grandes -->
                    <div class="card">
                        <img class="card-img-top" src="{{ asset('img/destination-' . $categoria->id_categoria . '.jpg') }}" alt="{{ $categoria->nombre_categoria }}">
                        <div class="card-body">
                            <h5 class="card-title text-capitalize">{{ $categoria->nombre_categoria }}</h5>
                            <p class="card-text">{{ $categoria->descripcion_categoria }}</p>
                            @if ('Tecnología' == $categoria->nombre_categoria)
                                <a href="{{ route('categoriaTech') }}" class="btn btn-primary">Ver más</a>
                            @endif

                            @if ('Negocios' == $categoria->nombre_categoria)
                            <a href="{{ route('categoriaNegocios') }}" class="btn btn-primary">Ver más</a>
                            @endif

                            @if ('Educación' == $categoria->nombre_categoria)
                            <a href="{{ route('categoriaEducacion') }}" class="btn btn-primary">Ver más</a>
                            @endif

                            @if ('Legales' == $categoria->nombre_categoria)
                            <a href="{{ route('categoriaLeyes') }}" class="btn btn-primary">Ver más</a>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach



            </div>
        </div>
    </div>
    <!-- Destination Start -->



    <div class="container-fluid py-5" id="tech">
        <div class="container pt-5 pb-3">
            <div class="text-center mb-3 pb-3">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Servicios de</h6>
                <h1>TECNOLOGÍA</h1>
            </div>
            <div class="row">
                @php
                    $serviciosTecnologia = $servicios->filter(function ($servicio) {
                        return $servicio->id_categoria === 1;
                    })->take(3);
                @endphp

                @foreach ($serviciosTecnologia as $servicio)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="package-item bg-white mb-2">
                                
                                <img class="img-fluid" src="{{ $servicio->imagen }}" alt="">
                            
                                <div class="p-4">
                                <a class="h5 text-decoration-none" href="{{ route('servicio.info', $servicio->id_servicios_personales) }}">{{ $servicio->nombre_servicio }}</a>
                                    <p>{{ $servicio->descripcion_servicio }}</p>
                                    <div class="border-top mt-4 pt-4">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="m-0">Precio:</h6>
                                            <h5 class="m-0">${{ $servicio->precio }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                @endforeach

            </div>
        </div>
        <div class="text-center">
            <a href="{{ route('categoriaTech') }}">Todos los servicios</a>
        </div>
    </div>
    <div class="container-fluid py-5" id="educacion">
        <div class="container pt-5 pb-3">
            <div class="text-center mb-3 pb-3">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Servicios de</h6>
                <h1>EDUCACIÓN</h1>
            </div>
            <div class="row">

                @php
                    $serviciosEducacion = $servicios->filter(function ($servicio) {
                        return $servicio->id_categoria === 3;
                    })->take(3);
                @endphp

                @foreach ($serviciosEducacion as $servicio)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="package-item bg-white mb-2">
                                <img class="img-fluid" src="{{ $servicio->imagen }}" alt="">
                     
                                <div class="p-4">
                                <a class="h5 text-decoration-none" href="{{ route('servicio.info', $servicio->id_servicios_personales) }}">{{ $servicio->nombre_servicio }}</a>

                                    <p>{{ $servicio->descripcion_servicio }}</p>
                                    <div class="border-top mt-4 pt-4">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="m-0">Precio:</h6>
                                            <h5 class="m-0">${{ $servicio->precio }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                @endforeach

            </div>
        </div>
        <div class="text-center">
            <a href="{{ route('categoriaEducacion') }}">Todos los servicios</a>
        </div>
    </div>

    <!-- Packages Start -->
    <div class="container-fluid py-5" id="negocios">
        <div class="container pt-5 pb-3">
            <div class="text-center mb-3 pb-3">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Servicios de</h6>
                <h1>NEGOCIOS</h1>
            </div>
            <div class="row">

                @php
                    $serviciosNegocios = $servicios->filter(function ($servicio) {
                        return $servicio->id_categoria === 2;
                    })->take(3);
                @endphp

                @foreach ($serviciosNegocios as $servicio)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="package-item bg-white mb-2">
                                <img class="img-fluid" src="{{ $servicio->imagen }}" alt="">
                                <div class="p-4">
                                <a class="h5 text-decoration-none" href="{{ route('servicio.info', $servicio->id_servicios_personales) }}">{{ $servicio->nombre_servicio }}</a>
                                    <p>{{ $servicio->descripcion_servicio }}</p>
                                    <div class="border-top mt-4 pt-4">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="m-0">Precio:</h6>
                                            <h5 class="m-0">${{ $servicio->precio }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endforeach

            </div>
        </div>
        <div class="text-center">
            <a href="{{ route('categoriaNegocios') }}">Todos los servicios</a>
        </div>
    </div>

    <div class="container-fluid py-5" id="leyes">
        <div class="container pt-5 pb-3">
            <div class="text-center mb-3 pb-3">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Servicios</h6>
                <h1>LEGALES</h1>
            </div>
            <div class="row">

                @php
                    $serviciosLegales = $servicios->filter(function ($servicio) {
                        return $servicio->id_categoria === 4;
                    })->take(3);
                @endphp

                @foreach ($serviciosLegales as $servicio)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="package-item bg-white mb-2">
                                <img class="img-fluid" src="{{ $servicio->imagen }}" alt="">
                                <div class="p-4">
                                <a class="h5 text-decoration-none" href="{{ route('servicio.info', $servicio->id_servicios_personales) }}">{{ $servicio->nombre_servicio }}</a>
                                    <p>{{ $servicio->descripcion_servicio }}</p>
                                    <div class="border-top mt-4 pt-4">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="m-0">Precio:</h6>
                                            <h5 class="m-0">${{ $servicio->precio }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                @endforeach

            </div>
        </div>
        <div class="text-center">
            <a href="{{ route('categoriaLeyes') }}">Todos los servicios</a>
        </div>
    </div>

    <!-- Packages End -->




    <!-- Testimonial Start -->
    <div class="container-fluid py-5" id="testimonios">
        <div class="container py-5">
            <div class="text-center mb-3 pb-3">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Testimonios</h6>
                <h1>Qué Dicen Nuestros Clientes</h1>
                <div class="text-center">
                    <a href="#nosotros">¿Quienes somos?</a>
                </div>
            </div>
            <div class="owl-carousel testimonial-carousel">
                <div class="text-center pb-4">
                    <img class="img-fluid mx-auto" src="img/testimonial-1.jpg" style="width: 100px; height: 100px;">
                    <div class="testimonial-text bg-white p-4 mt-n5">
                        <p class="mt-5">¡Increíble experiencia! Encontré exactamente lo que necesitaba en poco tiempo
                            y a un precio justo. ¡Altamente recomendado!
                        </p>
                        <h5 class="text-truncate">Juan López</h5>
                        <span>Cliente</span>
                    </div>
                </div>
                <div class="text-center">
                    <img class="img-fluid mx-auto" src="img/testimonial-2.jpg" style="width: 100px; height: 100px;">
                    <div class="testimonial-text bg-white p-4 mt-n5">
                        <p class="mt-5">¡El mejor servicio que he encontrado en mucho tiempo! Fácil de usar, rápido y
                            eficiente. ¡No puedo pedir más!
                        </p>
                        <h5 class="text-truncate">Ana García</h5>
                        <span>Diseñadora</span>
                    </div>
                </div>
                <div class="text-center">
                    <img class="img-fluid mx-auto" src="img/testimonial-3.jpg" style="width: 100px; height: 100px;">
                    <div class="testimonial-text bg-white p-4 mt-n5">
                        <p class="mt-5">¡Impresionante! Encontré al profesional perfecto para mi proyecto y la
                            comunicación fue excelente en todo momento.
                        </p>
                        <h5 class="text-truncate">David Rodríguez</h5>
                        <span>Cliente</span>
                    </div>
                </div>
                <div class="text-center">
                    <img class="img-fluid mx-auto" src="img/testimonial-4.jpg" style="width: 100px; height: 100px;">
                    <div class="testimonial-text bg-white p-4 mt-n5">
                        <p class="mt-5">¡Increíble plataforma! Me ayudó a encontrar rápidamente al abogado que
                            necesitaba para mi caso legal. ¡Altamente recomendado!
                        </p>
                        <h5 class="text-truncate">Pedro Martínez</h5>
                        <span>Cliente</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->

    @endsection

  