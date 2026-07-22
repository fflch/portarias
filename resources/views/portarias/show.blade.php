@extends('layouts.app')

@section('content')
<style>
    /* Usa a cor institucional para destacar a etapa ativa no fluxo */
    .md-step.active .md-step-circle,
    .md-step.active .md-step-title {
        color: var(--fflch-primary) !important;
    }
    
    .md-step.active .md-step-circle {
        background-color: var(--fflch-primary) !important;
        color: #ffffff !important;
    }

    /* Ajuste visual para as páginas da minuta renderizada */
    .docx-wrapper {
        background-color: transparent !important;
        padding: 0 !important;
    }
    .docx-wrapper > section.docx {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        margin-bottom: 20px !important;
    }
</style>

<div class="container py-4" style="min-height: 80vh;">

    {{-- 1. BARRA SUPERIOR: Navegação e Ações --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap: 15px;">
        <div>
            <a href="{{ route('portarias.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="fas fa-arrow-left mr-1 me-1"></i> Voltar para a Lista
            </a>
            <h4 class="mb-0 font-weight-bold text-dark">
                {{ $portaria->number ? 'Portaria nº ' . $portaria->number . '/' . $portaria->year : 'Pendente de Numeração' }}
            </h4>
            <small class="text-muted">Tipo: {{ is_object($portaria->type) ? $portaria->type->label() : $portaria->type }}</small>
        </div>

        <div class="d-flex align-items-center" style="gap: 10px;">
            {{-- Botão de Download do Documento Word --}}
            @if($portaria->pdf_path)
                <a href="{{ asset('storage/' . $portaria->pdf_path) }}" download class="btn btn-outline-primary-fflch btn-sm font-weight-bold">
                    <i class="fas fa-file-word mr-1 me-1"></i> Baixar Minuta (.docx)
                </a>
            @endif

            {{-- Botão Editar --}}
            @if(!method_exists($portaria->status, 'isEditable') || $portaria->status->isEditable())
                <a href="{{ route('portarias.edit', $portaria) }}" class="btn btn-primary-fflch btn-sm font-weight-bold">
                    <i class="fas fa-edit mr-1 me-1"></i> Editar Portaria
                </a>
            @endif
        </div>
    </div>

    {{-- 2. BLOCO DO STEPPER (Fluxo de Tramitação) --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <h6 class="text-muted text-uppercase font-weight-bold small mb-3">Fluxo de Tramitação</h6>
            
            {{-- Invocação do Componente do Stepper --}}
            {!! $stepper !!}
            
        </div>
    </div>

    {{-- 3. CONTEÚDO PRINCIPAL (Grelha Dividida) --}}
    <div class="row">
        
        {{-- COLUNA DA ESQUERDA: Leitor e Pré-visualização do Documento .docx --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-file-alt text-primary mr-2 me-2"></i> Conteúdo da Portaria / Ementa
                    </h6>
                    @if($portaria->file_name)
                        <small class="text-muted"><i class="fas fa-paperclip mr-1 me-1"></i>{{ $portaria->file_name }}</small>
                    @endif
                </div>
                <div class="card-body p-3">
                    {{-- Exibição da Ementa / Título --}}
                    <div class="alert alert-light border mb-3">
                        <strong class="d-block text-secondary mb-1">Ementa / Título:</strong>
                        <p class="mb-0 text-dark font-weight-bold">{{ $portaria->title }}</p>
                    </div>

                    {{-- Visualizador de DOCX no Navegador --}}
                    @if($portaria->pdf_path)
                        <div id="docx-container" class="border rounded p-3" style="min-height: 550px; max-height: 750px; overflow-y: auto; background-color: #f8f9fa;">
                            <div id="docx-loading" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-2 text-muted font-weight-bold">Carregando pré-visualização do documento...</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-5 border rounded bg-light">
                            <i class="fas fa-file-upload fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0">Nenhum arquivo de minuta foi anexado a esta portaria.</p>
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
                        <i class="fas fa-info-circle text-secondary mr-2 me-2"></i> Informações do Registro
                    </h6>
                </div>
                <div class="card-body">
                    
                    {{-- Status Atual --}}
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Status Atual</small>
                        <span class="badge badge-{{ $portaria->status->color()}} text-white px-3 py-2" style="font-size: 0.9rem;">
                            {{ is_object($portaria->status) ? $portaria->status->label() : $portaria->status }}
                        </span>
                    </div>

                    <hr class="my-3">

                    {{-- Informações do Autor --}}
                    <div class="mb-3">
                        <small class="text-muted d-block">Cadastrado por</small>
                        <span class="font-weight-bold text-dark">
                            {{ $portaria->creator->name ?? 'Sistema' }}
                        </span>
                        @if(isset($portaria->creator->email))
                            <small class="d-block text-muted">
                                {{ $portaria->creator->email }}
                            </small>
                        @endif
                    </div>

                    {{-- Data de Criação --}}
                    <div class="mb-3">
                        <small class="text-muted d-block">Data de Criação</small>
                        <span class="text-dark font-weight-bold">
                            {{ $portaria->created_at ? $portaria->created_at->format('d/m/Y à\s H:i') : 'N/D' }}
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
                                {{ \Carbon\Carbon::parse($portaria->published_at)->format('d/m/Y') }}
                            </span>
                        </div>
                    @endif

                    {{-- Motivo de Rejeição --}}
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

{{-- Scripts para renderizar o .docx direto no navegador sem disparar download --}}
@if($portaria->pdf_path)
    <script src="https://unpkg.com/jszip/dist/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.1.15/dist/docx-preview.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const fileUrl = "{{ asset('storage/' . $portaria->pdf_path) }}";
            const container = document.getElementById("docx-container");
            const loading = document.getElementById("docx-loading");

            fetch(fileUrl)
                .then(response => {
                    if (!response.ok) throw new Error("Erro ao carregar o arquivo.");
                    return response.blob();
                })
                .then(blob => {
                    if (loading) loading.style.display = "none";
                    
                    // Renderiza o Word dentro da DIV
                    docx.renderAsync(blob, container, null, {
                        className: "docx",
                        inWrapper: true,
                        ignoreWidth: false,
                        ignoreHeight: false,
                    });
                })
                .catch(error => {
                    console.error("Erro na pré-visualização:", error);
                    container.innerHTML = `
                        <div class="alert alert-warning m-4 text-center">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>
                            Não foi possível gerar a pré-visualização automática do documento.<br>
                            <a href="${fileUrl}" download class="btn btn-sm btn-primary mt-3 font-weight-bold">
                                <i class="fas fa-download mr-1"></i> Baixar Arquivo para Visualizar
                            </a>
                        </div>`;
                });
        });
    </script>
@endif
@endsection