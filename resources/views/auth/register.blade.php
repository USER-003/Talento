@extends('layouts.app')

@section('content')
<!-- Section: Design Block -->
<section class="vh-100 d-flex align-items-center justify-content-center">
  <!-- Jumbotron -->
  <div class="container">
    <div class="row gx-lg-5 align-items-center">
      <!-- Formulario a la izquierda -->
      <div class="col-lg-6 mb-5 mb-lg-0">
        <div class="card">
          <div class="card-body py-5 px-md-5">
            <h3 class="mb-4 text-center">Regístrate</h3>

            <form action="{{ route('registrarse') }}" method="POST">
            @csrf
              <!-- 2 column grid layout with text inputs for the first and last names -->
              <div class="row">
                <div class="col-md-6 mb-4">
                  <div data-mdb-input-init class="form-outline">
                    <input type="text" id="nombres" class="form-control" name="nombres" value="{{ old('nombres') }}" required />
                    <label class="form-label" for="form3Example1">Nombres</label>
                    @error('nombres')
                      <div style="color: red;">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6 mb-4">
                  <div data-mdb-input-init class="form-outline">
                    <input type="text" id="apellidos" class="form-control" name="apellidos" value="{{ old('apellidos') }}" required/>
                    <label class="form-label" for="form3Example2">Apellidos</label>
                    @error('apellidos')
                      <div style="color: red;">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <!-- Email input -->
              <div data-mdb-input-init class="form-outline mb-4">
                <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}" required/>
                <label class="form-label" for="form3Example3">Email</label>
                @error('email')
                  <div style="color: red;">{{ $message }}</div>
                @enderror
              </div>

              <!-- Password input -->
              <div data-mdb-input-init class="form-outline mb-4">
              <!-- Términos y condiciones -->
              <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="terminos" name="terminos" required>
                <label class="form-check-label" for="terminos">
                  Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#modalTerminos" style="text-decoration: underline;">Términos y Condiciones</a>
                </label>
                @error('terminos')
                  <div style="color: red;">{{ $message }}</div>
                @enderror
              </div>
                <input type="password" id="password" class="form-control" name="password" required/>
                <label class="form-label" for="form3Example4">Contraseña</label>
                @error('password')
                  <div style="color: red;">{{ $message }}</div>
                @enderror
              </div>

              <!-- Password Confirmation input -->
              <div data-mdb-input-init class="form-outline mb-4">
                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" required/>
                <label class="form-label" for="form3Example5">Confirmar Contraseña</label>
                @error('password_confirmation')
                  <div style="color: red;">{{ $message }}</div>
                @enderror
              </div>

              <!-- Submit button -->
              <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4">
                Registrarse
              </button>

              <!-- Register buttons -->
              <div class="text-center">
                  <p>o registrate con:</p>
                  <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
                      <i class="fab fa-facebook-f"></i>
                    </button>
                    
                    <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
                        <i class="fab fa-google"></i>
                    </button>
                    
                    <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
                        <i class="fab fa-twitter"></i>
                    </button>
                    
                    <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
                        <i class="fab fa-github"></i>
                    </button>
                    <p>Ya tienes una cuenta? <a href="{{ route('login') }}" class="link-danger link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Iniciar Sesion</a></p>
              <!-- Modal de Términos y Condiciones -->
              <div class="modal fade" id="modalTerminos" tabindex="-1" aria-labelledby="modalTerminosLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title w-100 text-center" id="modalTerminosLabel">Términos y Condiciones de Talento</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                      <div>
                        <h6 class="text-center">1. Introducción</h6>
                        <p class="text-start">Bienvenido a Talento, una plataforma que conecta personas para publicar y contratar servicios de manera rápida y segura. Al registrarte, aceptas los siguientes términos y condiciones.</p>
                        <h6 class="text-center">2. Registro y Cuenta</h6>
                        <p class="text-start">Debes proporcionar información veraz y mantener la confidencialidad de tus credenciales. Eres responsable de todas las actividades realizadas desde tu cuenta.</p>
                        <h6 class="text-center">3. Publicación y Contratación de Servicios</h6>
                        <p class="text-start">Los usuarios pueden publicar servicios o contratar a otros. Talento no se responsabiliza por la calidad, cumplimiento o resultados de los servicios ofrecidos, pero promueve la buena conducta y el respeto entre usuarios.</p>
                        <h6 class="text-center">4. Pagos y Transacciones</h6>
                        <p class="text-start">Los pagos se gestionan dentro de la plataforma. Talento actúa como intermediario y puede retener comisiones. Los pagos son procesados de acuerdo a las políticas de seguridad y protección al usuario.</p>
                        <h6 class="text-center">5. Comentarios y Valoraciones</h6>
                        <p class="text-start">Los usuarios pueden dejar comentarios y valoraciones sobre los servicios. Talento se reserva el derecho de eliminar contenido ofensivo, fraudulento o que no cumpla con las normas de la comunidad.</p>
                        <h6 class="text-center">6. Chats y Comunicación</h6>
                        <p class="text-start">La plataforma ofrece chat para facilitar la comunicación entre usuarios. Está prohibido el uso de lenguaje ofensivo, spam o cualquier conducta que afecte la experiencia de otros.</p>
                        <h6 class="text-center">7. Prohibiciones</h6>
                        <p class="text-start">No se permite la publicación de servicios ilegales, fraudulentos o que infrinjan derechos de terceros. Talento puede suspender o eliminar cuentas que incumplan estas reglas.</p>
                        <h6 class="text-center">8. Responsabilidad</h6>
                        <p class="text-start">Talento no se hace responsable por daños directos o indirectos derivados del uso de la plataforma. El usuario asume todos los riesgos relacionados con la contratación y prestación de servicios.</p>
                        <h6 class="text-center">9. Modificaciones</h6>
                        <p class="text-start">Talento puede modificar estos términos en cualquier momento. Se notificará a los usuarios sobre cambios importantes.</p>
                        <h6 class="text-center">10. Contacto</h6>
                        <p class="text-start">Para dudas o reclamos, puedes contactarnos a través de los medios oficiales indicados en la plataforma.</p>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </div>
              </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Texto a la derecha -->
      <div class="col-lg-6 mb-5 mb-lg-0">
        <h1 class="my-5 display-3 fw-bold ls-tight">
          TALENTO <br />
          <span class="text-primary">SERVICIOS AL ALCANCE DE TODOS</span>
        </h1>
        <p style="color: hsl(217, 10%, 50.8%)">
          Lorem ipsum dolor sit amet consectetur adipisicing elit.
          Eveniet, itaque accusantium odio, soluta, corrupti aliquam
          quibusdam tempora at cupiditate quis eum maiores libero
          veritatis? Dicta facilis sint aliquid ipsum atque?
        </p>
      </div>
    </div>
  </div>
  <!-- Jumbotron -->
</section>
<!-- Section: Design Block -->
@endsection
