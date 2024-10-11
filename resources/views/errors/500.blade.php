@extends('layouts.app')

@section('title', 'Erro no Servidor')

@section('content')
<div class="container mt-5 text-center">
    <h1>Ops! Algo deu errado.</h1>
    <p>{{ $message ?? 'Infelizmente, estamos enfrentando problemas técnicos. Tente novamente mais tarde ou entre em contato com o suporte.' }}</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-3">Voltar para a página inicial</a>
</div>
@endsection
