@extends('layouts.app')

@section('content')
<div class="container py-4" style="min-height: 70vh;">

    <div class="card shadow-sm border-0">
        {{-- Cabeçalho da Listagem --}}
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0 text-dark font-weight-bold">Portarias Cadastradas</h5>
                    <small class="text-muted">Consulta e gestão do acervo de atos normativos</small>
                </div>
            </div>
            @auth
                <a href="{{ route('portarias.create') }}" class="btn btn-primary-fflch btn-sm font-weight-bold px-3 shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Nova Portaria
                </a>
            @endauth
        </div>

        <div class="card-body p-0">
            {{-- BLOCO COM SCROLL --}}
            <div class="px-3 pt-3 bg-light border-bottom">
                <ul class="nav nav-tabs border-bottom-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request('filter') != 'minhas' ? 'active font-weight-bold' : 'text-secondary' }}" 
                        href="{{ route('portarias.index') }}">
                            <i class="fas fa-list mr-1"></i> Todas as Portarias
                        </a>
                    </li>
                    @auth 
                        <li class="nav-item">
                                <a class="nav-link {{ request('filter') == 'minhas' ? 'active font-weight-bold' : 'text-secondary' }}" 
                                href="{{ route('portarias.index', ['filter' => 'minhas']) }}">
                                <i class="fas fa-user-edit mr-1"></i> Minhas Portarias
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
            <div class="scroll-area p-3" style="max-height: 65vh; overflow-y: auto;">
                <div class="row">
                    @forelse ($portarias as $portaria)
                        <div class="col-12 mb-2">
                            {{-- Linha da Portaria em Formato de Card Horizontal --}}
                            <div class="card border border-light shadow-sm hover-shadow transition">
                                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap">
                                    
                                    {{-- Lado Esquerdo: Ícone + Metadados + Título --}}
                                    <div class="d-flex align-items-start mb-2 mb-md-0" style="max-width: 75%;">
                                        <div class="mr-3 text-secondary mt-1">
                                            <i class="far fa-file-pdf fa-2x text-danger"></i>
                                        </div>
                                        <div>
                                            {{-- Badges de Identificação --}}
                                            <div class="d-flex align-items-center mb-1 flex-wrap" style="gap: 6px;">
                                                <span class="badge badge-secondary">
                                                    Tipo {{ $portaria->type }}
                                                </span>
                                                
                                                {{-- Exibe o número formatado ou 'Pendente' --}}
                                                <span class="font-weight-bold text-dark medium">
                                                    nº {{ $portaria->number . '/'. $portaria->year ?? 'Pendente' }}
                                                </span>

                                                {{-- Badge do Enum de Status --}}
                                                <span class="badge badge-{{ $portaria->status->color() }}">
                                                    {{ $portaria->status->label() }}
                                                </span>
                                            </div>

                                            {{-- Título / Ementa Completa --}}
                                            <h6 class="mb-1 text-dark font-weight-bold">
                                                {{ $portaria->title }}
                                            </h6>

                                            {{-- Rodapé do Item: Autor e Data --}}
                                            <small class="text-muted">
                                                <i class="far fa-user mr-1"></i> Autor: {{ $portaria->creator->name ?? 'Sistema' }}
                                                <span class="mx-2">•</span>
                                                <i class="far fa-calendar-alt mr-1"></i> Criado em: {{ $portaria->created_at->format('d/m/Y à\s H:i') }}
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Lado Direito: Ações do Item --}}
                                    <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                                        <a href="{{ route('portarias.show', $portaria) }}" class="btn btn-outline-primary-fflch btn-sm px-3 font-weight-bold" title="Visualizar detalhes">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </a>
                                        @can('update', $portaria)
                                            <a href="{{ route('portarias.edit', $portaria) }}" class="btn btn-outline-secondary btn-sm px-3" title="Editar">
                                                <i class="fas fa-edit mr-1"></i> Editar
                                            </a>
                                        @endcan
                                    </div>

                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- Estado Vazio (Empty State) --}}
                        <div class="col-12 text-center text-muted py-5">
                            <h5 class="font-weight-bold">Nenhuma portaria encontrada</h5>
                            <p class="small mb-0">Não há registros cadastrados ou que correspondam aos filtros.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection