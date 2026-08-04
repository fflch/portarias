@extends('layouts.app') 

@section('content')
<div class="container py-4" style="min-height: 70vh;">

    {{-- Alerta geral de erros de validação --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-lg mr-3 me-3"></i>
                <div>
                    <strong>Por favor, verifique o formulário!</strong>
                    <span class="d-block text-sm small">Existem campos que precisam de correção antes de continuar.</span>
                </div>
            </div>
            <button type="button" class="close btn-close" data-dismiss="alert" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        {{-- Cabeçalho Institucional --}}
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 text-dark font-weight-bold">Nova Portaria</h5>
                <small class="text-muted">Preencha os dados e anexe a minuta para submeter à análise</small>
            </div>
            <a href="{{ route('portarias.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1 me-1"></i> Voltar
            </a>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('portarias.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    {{-- Tipo de Portaria (Carregado dinamicamente do Enum) --}}
                    <div class="col-md-12 mb-3">
                        <label for="type" class="form-label font-weight-bold text-secondary">
                            Tipo de Portaria <span class="text-danger">*</span>
                        </label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="" disabled {{ old('type') ? '' : 'selected' }}>Selecione o tipo de ato normativo...</option>
                            @foreach(App\Enums\PortariaType::cases() as $type)
                                <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Origem da Numeração (Toggle) --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label font-weight-bold text-secondary d-block">
                            Origem da Numeração <span class="text-danger">*</span>
                        </label>
                        
                        <div class="form-check form-check-inline">
                            <input type="radio" id="num_auto" name="numbering_type" value="auto" class="form-check-input" 
                                   {{ old('numbering_type', 'auto') === 'auto' ? 'checked' : '' }} onchange="toggleNumberField()">
                            <label class="form-check-label" for="num_auto">Gerar Automaticamente (Nova Portaria)</label>
                        </div>
                        
                        <div class="form-check form-check-inline">
                            <input type="radio" id="num_manual" name="numbering_type" value="manual" class="form-check-input" 
                                   {{ old('numbering_type') === 'manual' ? 'checked' : '' }} onchange="toggleNumberField()">
                            <label class="form-check-label" for="num_manual">Inserir Manualmente (Retroativa / Física)</label>
                        </div>
                    </div>

                    {{-- Campos de Número e Ano (Escondidos por padrão) --}}
                    <div class="col-md-12" id="box-numeracao-manual" style="display: {{ old('numbering_type') === 'manual' ? 'block' : 'none' }};">
                        <div class="p-3 mb-3 bg-light border rounded">
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="number" class="form-label font-weight-bold text-secondary">
                                        Número da Portaria <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="number" id="number" class="form-control @error('number') is-invalid @enderror" 
                                           value="{{ old('number') }}" placeholder="Ex: 12">
                                    @error('number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="year" class="form-label font-weight-bold text-secondary">
                                        Ano <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="year" id="year" class="form-control @error('year') is-invalid @enderror" 
                                           value="{{ old('year', date('Y')) }}" placeholder="Ex: {{ date('Y') }}">
                                    @error('year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Título / Ementa --}}
                    <div class="col-md-12 mb-3">
                        <label for="title" class="form-label font-weight-bold text-secondary">
                            Título / Ementa da Portaria <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               placeholder="Ex: Dispõe sobre a constituição da comissão eleitoral..." 
                               value="{{ old('title') }}" 
                               required>
                        <small class="form-text text-muted">Forneça uma síntese clara do assunto tratado nesta portaria.</small>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Anexo do Documento (.docx) --}}
                    <div class="col-md-12 mb-4">
                        <label for="file" class="form-label font-weight-bold text-secondary">
                            Minuta em Documento (.docx) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light text-primary">
                                    <i class="fas fa-file-word"></i>
                                </span>
                            </div>
                            <input type="file" 
                                   name="file" 
                                   id="file" 
                                   class="form-control @error('file') is-invalid @enderror" 
                                   accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document" 
                                   required>
                        </div>
                        @error('file')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            <small class="form-text text-muted">Apenas arquivos no formato <strong>Word (.docx)</strong> são permitidos. Tamanho máximo: 10MB.</small>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Rodapé com Ações --}}
                <div class="d-flex justify-content-end align-items-center">
                    <a href="{{ route('portarias.index') }}" class="btn btn-light border mr-2 me-2">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary-fflch px-4 font-weight-bold shadow-sm">
                        <i class="fas fa-paper-plane mr-1 me-1"></i> Enviar para Análise
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<script>
    function toggleNumberField() {
        const manualBox = document.getElementById('box-numeracao-manual');
        const isManual = document.getElementById('num_manual').checked;
        
        if (isManual) {
            manualBox.style.display = 'block';
        } else {
            manualBox.style.display = 'none';
            // Opcional: Limpa os campos quando esconde para não enviar lixo no request
            document.getElementById('number').value = '';
            document.getElementById('year').value = '{{ date('Y') }}';
        }
    }

    // Garante que o estado correto seja carregado caso a página venha de um erro de validação
    document.addEventListener("DOMContentLoaded", function() {
        toggleNumberField();
    });
</script>
@endsection