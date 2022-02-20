<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title')</title>
        <link rel="icon" href="img/Laravel.svg" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="/css/style.css" />
    </head>
    <body>

        <header>
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="collapse navbar-collapse" id="navbar">
                    <a href="/" class="navbar-brand">
                        <img src="img/Laravel.svg" alt="Logo">
                    </a>
                    <ul class="navbar-nav">
                        <li class="navbar-item">
                            <a href="/" class="nav-link">Home</a>
                        </li>
                        <li class="navbar-item">
                            <a href="/" class="nav-link">Eventos</a>
                        </li>
                        <li class="navbar-item">
                            <a href="/events/create" class="nav-link">Criar Eventos</a>
                        </li>
                        @auth
                        <li class="navbar-item">
                            <a href="/dashboard" class="nav-link">Meus Eventos</a>
                        </li>
                        <li class="nav-item">
                            <form action="/logout" method="POST">
                                @csrf
                                <a href="/logout" class="nav-link" onclick="event.preventDefault(); this.closest('form').submit();">Sair</a>
                            </form>
                        </li>

                        @endauth
                        @guest
                        <li class="navbar-item">
                            <a href="/login" class="nav-link">Entrar</a>
                        </li>
                        <li class="navbar-item">
                            <a href="/register" class="nav-link">Cadastrar</a>
                        </li>
                        @endguest
                    </ul>
                </div>
            </nav>
        </header>

        <main class="container-fluid">
            <div class="row">
                @if(session('msg'))
                    <p class="msg">
                        {{ session('msg') }}
                    </p>
                @endif
                @yield('content')
            </div>
        </main>
        
        <footer>Curso de Laravel &copy; 2022</footer>
    </body>
</html>
