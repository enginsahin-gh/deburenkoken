@extends('errors::custom-minimal')

@section('title', __('Sessie Verlopen'))
@section('code', '419')
@section('heading', 'Sessie Verlopen')
@section('message', 'Je sessie is verlopen door inactiviteit. Dit kan gebeuren als je de pagina lang open hebt staan zonder actie.')
@section('solution')
    <p style="margin-top: 20px; color: #666; font-size: 16px;">
        <strong>Oplossing:</strong> Ververs de pagina en probeer het opnieuw.
    </p>
    <p style="margin-top: 10px; color: #888; font-size: 14px;">
        Als dit probleem blijft optreden, probeer dan je browsergeschiedenis en cookies te wissen.
    </p>
@endsection
@section('button_text', 'Pagina Verversen')
@section('button_action', 'refresh')
