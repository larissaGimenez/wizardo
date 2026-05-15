@extends('layouts.app')

@section('title', 'Bem-vindo ao Wizardo')

@section('content')
<div class="welcome-container">
    <div class="welcome-card">
        <h1>Bem-vindo ao Wizardo! 🧙‍♂️</h1>
        <p>Este é o painel inicial do seu novo sistema.</p>
        <p>Aqui você pode gerenciar suas rodas e configurações.</p>
        
        <div class="actions">
            <a href="#" class="btn btn-primary">Começar</a>
        </div>
    </div>
</div>

<style>
    .welcome-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 140px); /* Adjust based on top bar and padding */
    }

    .welcome-card {
        background: white;
        padding: 3rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        text-align: center;
        max-width: 500px;
        width: 100%;
        border: 1px solid #e2e8f0;
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .welcome-card h1 {
        color: #0f172a;
        font-size: 2rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .welcome-card p {
        color: #64748b;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .btn {
        display: inline-block;
        margin-top: 1.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-primary {
        background-color: #38bdf8;
        color: #0f172a;
    }

    .btn-primary:hover {
        background-color: #0ea5e9;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection
