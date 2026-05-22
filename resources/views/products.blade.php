@extends('layouts.main')

@section('title', 'Produtos')

@section('content')


@if ($busca != '')
    <p>o usuario está buscando por {{ $busca }}</p>
@endif
<h1>Tela de Produtos</h1>
<a href="/">Voltar para home</a>

@endsection
