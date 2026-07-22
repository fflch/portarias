@extends('layouts.app')

@section('content')
<style>
    /* Usa a cor institucional para destacar a etapa ativa no fluxo */
    .md-step.active .md-step-circle,
    .md-step.active .md-step-title {
        color: var(--fflch-primary) !important; /* Exemplo: Vinho/FFLCH (ou use a sua classe/cor) */
    }
    
    .md-step.active .md-step-circle {
        background-color: var(--fflch-primary) !important; /* Cor do fundo da bolinha */
        color: #ffffff !important;            /* Cor do ícone dentro */
    }
</style>

<div class="container py-4" style="min-height: 80vh;">

    {{-- 1. BARRA SUPERIOR: Navegação e Ações --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap: 15px;">
        <div>
            <a href="{{ route('portarias.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="fas fa-arrow-left mr-1"></i> Voltar para a Lista
            </a>
            <h4 class="mb-0 font-weight-bold text-dark">
                Portaria {{ $portaria->formatted_number ?? 'Pendente de Numeração' }}
            </h4>
            <small class="text-muted">Tipo: {{ $portaria->type }}</small>
        </div>

        <div class="d-flex align-items-center" style="gap: 10px;">
            {{-- Botão de Download do PDF Original --}}
            @if($portaria->pdf_path)
                <a href="{{ asset('storage/' . $portaria->pdf_path) }}" target="_blank" class="btn btn-outline-danger btn-sm font-weight-bold">
                    <i class="fas fa-file-pdf mr-1"></i> Baixar PDF
                </a>
            @endif

            {{-- Botão Editar (só para quem tem permissão) --}}
            @can('update', $portaria)
                <a href="{{ route('portarias.edit', $portaria) }}" class="btn btn-primary-fflch btn-sm font-weight-bold">
                    <i class="fas fa-edit mr-1"></i> Editar Portaria
                </a>
            @endcan
        </div>
    </div>

    {{-- 2. BLOCO DO STEPPER (Fluxo de Aprovação) --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <h6 class="text-muted text-uppercase font-weight-bold small mb-3">Fluxo de Tramitação</h6>
            
            {{-- Invocação do Componente do Stepper --}}
            {!! $stepper !!}
            
        </div>
    </div>

    {{-- 3. CONTEÚDO PRINCIPAL (Grelha Dividida) --}}
    <div class="row">
        
        {{-- COLUNA DA ESQUERDA: Leitor do Documento PDF --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-file-alt text-primary mr-2"></i> Conteúdo da Portaria / Ementa
                    </h6>
                </div>
                <div class="card-body p-3">
                    {{-- Exibição da Ementa / Título --}}
                    <div class="alert alert-light border mb-3">
                        <strong class="d-block text-secondary mb-1">Ementa:</strong>
                        <p class="mb-0 text-dark font-weight-bold">{{ $portaria->title }}</p>
                    </div>

                    {{-- Visualizador de PDF Embutido --}}
                    @if($portaria->pdf_path)
                        <div class="embed-responsive embed-responsive-16by9 border rounded" style="min-height: 550px;">
                            <iframe class="embed-responsive-item w-100 h-100" 
                                    src="{{ asset('storage/' . $portaria->pdf_path) }}#toolbar=0" 
                                    style="min-height: 550px; border: none;">
                            </iframe>
                        </div>
                    @else
                        <div class="text-center text-muted py-5 border rounded bg-light">
                            <i class="fas fa-file-upload fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0">Nenhum arquivo PDF foi anexado a esta portaria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- COLUNA DA DIREITA: Ficha Técnica & Metadados --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-info-circle text-secondary mr-2"></i> Informações do Registro
                    </h6>
                </div>
                <div class="card-body">
                    
                    {{-- Status Atual --}}
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Status Atual</small>
                        <span class="badge badge-{{ $portaria->status->color() }} px-3 py-2" style="font-size: 0.9rem;">
                            {{ $portaria->status->label() }}
                        </span>
                    </div>

                    <hr class="my-3">

                    {{-- Informações do Autor --}}
                    <div class="mb-3">
                        <small class="text-muted d-block">Cadastrado por</small>
                        <span class="font-weight-bold text-dark">
                            {{ $portaria->creator->name ?? 'Sistema' }}
                        </span>
                        <small class="d-block text-muted">
                            {{ $portaria->creator->email ?? '' }}
                        </small>
                    </div>

                    {{-- Data de Criação --}}
                    <div class="mb-3">
                        <small class="text-muted d-block">Data de Criação</small>
                        <span class="text-dark font-weight-bold">
                            {{ $portaria->created_at->format('d/m/Y à\s H:i') }}
                        </span>
                    </div>

                    {{-- Aprovador / Data de Publicação (Se houver) --}}
                    @if($portaria->approved_by)
                        <hr class="my-3">
                        <div class="mb-3">
                            <small class="text-muted d-block">Aprovado por</small>
                            <span class="font-weight-bold text-dark">
                                {{ $portaria->approver->name ?? 'N/D' }}
                            </span>
                        </div>
                    @endif

                    @if($portaria->published_at)
                        <div class="mb-3">
                            <small class="text-muted d-block">Data de Publicação</small>
                            <span class="text-success font-weight-bold">
                                {{ $portaria->published_at->format('d/m/Y') }}
                            </span>
                        </div>
                    @endif

                    {{-- Motivo de Rejeição (Aparece somente se houver) --}}
                    @if($portaria->rejection_reason)
                        <div class="alert alert-danger mb-0 mt-3">
                            <small class="font-weight-bold d-block mb-1">Motivo da Rejeição:</small>
                            <span class="small">{{ $portaria->rejection_reason }}</span>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>
@endsection