@extends('layouts.app')

@section('title', 'Talento')

@section('content')

    <!-- Header Start -->
    @csrf
    <div class="container-fluid page-header">
        <div class="container">
            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 400px">
                <h3 class="display-4 text-white text-uppercase">Detalles del Servicio</h3>
                <div class="d-inline-flex text-white">
                    <p class="m-0 text-uppercase"><a class="text-white" href="{{ route('inicio') }}">INICIO</a></p>
                    <i class="fa fa-angle-double-right pt-1 px-3"></i>
                    <p class="m-0 text-uppercase">DETALLES</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->


    <!-- Blog Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Blog Detail Start -->
                    <div class="pb-3">
                        <div class="blog-item">
                            <div class="position-relative">
                                <img class="img-fluid w-100" src="{{ $servicio->imagen }}" alt="">

                            </div>
                        </div>
                        <div class="bg-white mb-3" style="padding: 30px;">
                            <h2 class="mb-3">{{ $servicio->nombre_servicio }}</h2>
                            <h4 class="mb-3">Acerca de este Servicio</h4>
                            <p>{{ $servicio->descripcion_servicio }}</p>
                            <h4 class="mb-3">Categoria</h4>
                            <p>{{ $categoria->nombre_categoria }}</p>
                            <h4 class="mb-3">Precio</h4>
                            <p>${{ $servicio->precio }}</p>

                        </div>
                    </div>
                    <!-- Blog Detail End -->

                    <!-- Comment List Start -->
                    <div class="bg-white" style="padding: 30px; margin-bottom: 30px;">
                        <h4 class="text-uppercase mb-4" style="letter-spacing: 5px;">1 Comentarios</h4>
                        <div class="media mb-4">
                            <div class="media-body">
                                <h6><a href="">Edwin Alexander Villalta</a> <small><i>01 Junio 2024</i></small></h6>
                                <p>Es un buen Servicio</p>
                                <button class="btn btn-sm btn-outline-primary" disabled>Responder</button>
                            </div>
                        </div>

                    </div>
                    <!-- Comment List End -->

                    <!-- Comment Form Start -->
                    <div class="bg-white mb-3" style="padding: 30px;">
                        <h4 class="text-uppercase mb-4" style="letter-spacing: 5px;">Dejar un Comentario</h4>
                        <form>

                            <div class="form-group">
                                <label for="message">Comentario *</label>
                                <textarea id="message" cols="30" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="form-group mb-0">
                                <input type="submit" value="Dejar Comentario"
                                    class="btn btn-primary font-weight-semi-bold py-2 px-3" disabled>
                            </div>
                        </form>
                    </div>
                    <!-- Comment Form End -->
                </div>

                <div class="col-lg-4 mt-5 mt-lg-0">
                    <!-- Author Bio -->
                    <div class="d-flex flex-column text-center bg-white mb-5 py-5 px-4">
                        <h3 class="text-primary mb-3 ">{{ $usuario->nombre }}</h3>
                        <p>Email: {{ $usuario->email }}</p>
                        <p>Teléfono: <br> {{ $servicio->numero_contacto }}</p>
                        <div class="d-flex justify-content-center">
                            <a class="text-primary px-2" href="#">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a class="text-primary px-2" href="#">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a class="text-primary px-2" href="#">
                                <i class="fab fa-instagram"></i>
                            </a>

                        </div>
                    </div>


                    <!-- Contact Provider -->
                    <div class="mb-5">
                        <div class="bg-white" style="padding: 30px;">
                            <div class="text-center mb-3">
                                <h5>¿Interesado en este servicio?</h5>
                                <p class="text-muted">Contacta directamente con {{ $usuario->nombre }}</p>
                            </div>
                            <div class="d-grid gap-2 text-center">
                                @auth
                                    @if(auth()->user()->id_usuario !== $usuario->id_usuario)
                                        <a href="{{ route('chat.show', $usuario->id_usuario) }}" class="btn btn-primary btn-lg">
                                            <i class="fas fa-comment-dots me-2"></i>
                                            Contáctame
                                        </a>
                                    @else
                                        <button class="btn btn-outline-secondary btn-lg" disabled>
                                            <i class="fas fa-user me-2"></i>
                                            Este es tu servicio
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Inicia sesión para contactar
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>

                    @php
                        $cat = $categoria->id_categoria;
                        $serviciosFiltrados = $servicios
                            ->filter(function ($servicio) use ($cat) {
                                return $servicio->id_categoria === $cat;
                            })
                            ->take(3);
                    @endphp


                    <!-- Recent Post -->
                    <div class="mb-5">
                        <h4 class="text-uppercase mb-4" style="letter-spacing: 5px;">Recomendado</h4>
                        @foreach ($serviciosFiltrados as $servicio)
                            <a class="d-flex align-items-center text-decoration-none bg-white mb-3" href="#">
                                <div class="img-dad">
                                    <img class="img-son" src="{{ $servicio->imagen }}" alt="">
                                </div>
                                <div class="pl-3">
                                    <h6>{{ $servicio->nombre_servicio }}</h6>
                                    <p style="font-size: 12px;">{{ $servicio->descripcion_servicio }}</p>
                                    <small>${{ $servicio->precio }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>



                </div>
            </div>
        </div>
    </div>
    <!-- Blog End -->

@endsection
