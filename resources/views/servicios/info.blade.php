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

                            <div class="d-flex align-items-center mt-3">
                                <strong class="me-2">Calificación promedio:</strong>
                                @php $avg = $promedio ?? $servicio->averageRating(); @endphp
                                <div>
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= floor($avg))
                                            <i class="fas fa-star text-warning"></i>
                                        @elseif ($i - $avg < 1)
                                            <i class="fas fa-star-half-alt text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                    <small class="text-muted">({{ number_format($avg, 1) }}/5)</small>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- Blog Detail End -->

                    <!-- Comment List Start -->
                    <div class="bg-white" style="padding: 30px; margin-bottom: 30px;">
                        <h4 class="text-uppercase mb-4" style="letter-spacing: 5px;">Comentarios</h4>
                        @forelse(($comentarios ?? []) as $c)
                            <div class="media mb-4">
                                <div class="media-body">
                                    <h6>
                                        <a href="#">{{ $c->usuario->nombre }}</a>
                                        <small class="text-muted"><i>{{ $c->created_at->format('d M Y') }}</i></small>
                                    </h6>
                                    <div class="mb-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $c->rating ? 'fas' : 'far' }} fa-star text-warning"></i>
                                        @endfor
                                    </div>
                                    <p class="mb-0">{{ $c->comentario }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Sé el primero en comentar este servicio.</p>
                        @endforelse
                    </div>
                    <!-- Comment List End -->

                    <!-- Comment Form Start -->
                    <div class="bg-white mb-3" style="padding: 30px;">
                        <h4 class="text-uppercase mb-4" style="letter-spacing: 5px;">Dejar un Comentario</h4>
                        @auth
                            <form id="commentForm" action="{{ route('servicio.comentar', $servicio) }}" method="POST">
                                @csrf

                                <div class="form-group mb-3">
                                    <span class="d-block mb-1">Tu calificación *</span>
                                    <div class="rating mb-2" aria-label="Selecciona una calificación">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ (old('rating') == $i) ? 'checked' : '' }}>
                                            <label for="rating{{ $i }}" title="{{ $i }} estrellas">
                                                <i class="fas fa-star"></i>
                                            </label>
                                        @endfor
                                    </div>
                                    @error('rating')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="message">Comentario *</label>
                                    <textarea id="message" name="comentario" cols="30" rows="5" class="form-control" required>{{ old('comentario') }}</textarea>
                                    @error('comentario')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-primary font-weight-semi-bold py-2 px-3">Guardar comentario</button>
                                </div>
                            </form>
                        @else
                            <p>Debes <a href="{{ route('login') }}">iniciar sesión</a> para comentar.</p>
                        @endauth
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

@push('styles')
<style>
    /* Star rating styles */
    .rating {
        display: inline-flex;
        flex-direction: row-reverse; /* Needed for sibling selector coloring */
        gap: 6px;
    }
    .rating input {
        position: absolute; /* Keep accessible but hidden */
        left: -9999px;
    }
    .rating label {
        cursor: pointer;
        font-size: 1.6rem; /* ~fa-2x */
        line-height: 1;
        color: #d6d6d6; /* default (unselected) color */
        transition: color .15s ease-in-out, transform .05s ease-in-out;
    }
    .rating label i { pointer-events: none; }
    /* Hover effects */
    .rating label:hover,
    .rating label:hover ~ label { color: #f5c518; }
    /* Selected state */
    .rating input:checked ~ label { color: #f5c518; }
    /* Small press feedback */
    .rating input:focus + label { outline: 2px solid #80bdff; outline-offset: 2px; border-radius: 4px; }
    .rating label:active { transform: scale(0.95); }
</style>
@endpush

@push('scripts')
<script>
    // If redirected with success status, clear the form selections on page load
    (function() {
        const hasStatus = {!! session()->has('status') ? 'true' : 'false' !!};
        if (hasStatus) {
            const form = document.getElementById('commentForm');
            if (form) {
                // Clear radios
                form.querySelectorAll('input[name="rating"]').forEach(r => r.checked = false);
                // Clear textarea
                const ta = form.querySelector('#message');
                if (ta) ta.value = '';
            }
        }
    })();
</script>
@endpush
