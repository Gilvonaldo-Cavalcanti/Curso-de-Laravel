@extends('layouts.main')

@section('title', 'Escola Protagonista')

@section('content')

<h2>Página de Produtos</h2>

<a href="/">Voltar para Home</a>

@if ($busca != '')
<p>O usuário tá procurando por {{ $busca }}<p>
@endif
@endsection