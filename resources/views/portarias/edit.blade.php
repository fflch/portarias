@extends('layouts.app')

@section('content')
<div class="container py-4" style="min-height: 70vh;">

    {{-- Alerta geral de erros --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-lg mr-3 me-3"></i>
                <div>
                    <strong>Por favor, verifique o formulário!</strong>
                    <span class="d-block small">Existem campos que precisam de correção.</span>
                </div>
            </div>
            <button type="button" class="close btn-close" data-dismiss="alert" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h4 class="text-dark font-weight-bold mb-0">
                <i class="fas fa-edit text-muted mr-2 me-2"></i>Editar Portaria
            </h4>
            <small class="text-muted">
                {{ $portaria->number ? 'Portaria nº ' . $portaria->number . '/' . $portaria->year : 'Minuta em Análise' }}
            </small>
        </div>
        <a href="{{ route('portarias.show', $portaria) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1 me-1"></i> Cancelar e Voltar
        </a>
    </div>

    {{-- Formulário --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom font-weight-bold text-secondary">
            Dados do Registro
        </div>
        <div class="card-body p-4">
            <form action="{{ route('portarias.update', $portaria) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Tipo de Portaria --}}
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label font-weight-bold text-secondary">
                            Tipo de Portaria <span class="text-danger">*</span>
                        </label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                            @foreach(App\Enums\PortariaType::cases() as $type)
                                <option value="{{ $type->value }}" {{ old('type', $portaria->type->value ?? $portaria->type) === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label font-weight-bold text-secondary">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                            @foreach(App\Enums\PortariaStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ old('status', $portaria->status->value ?? $portaria->status) === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Título / Ementa --}}
                    <div class="col-md-12 mb-3">
                        <label for="title" class="form-label font-weight-bold text-secondary">
                            Título / Ementa <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title', $portaria->title) }}" 
                               required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Número e Ano --}}
                    <div class="col-md-6 mb-3">
                        <label for="number" class="form-label font-weight-bold text-secondary">Número</label>
                        <input type="number" name="number" id="number" class="form-control @error('number') is-invalid @enderror" value="{{ old('number', $portaria->number) }}" placeholder="Ex: 12">
                        @error('number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="year" class="form-label font-weight-bold text-secondary">Ano</label>
                        <input type="number" name="year" id="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $portaria->year) }}" placeholder="Ex: {{ date('Y') }}">
                        @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Anexo (.docx) --}}
                    <div class="col-md-12 mt-2 mb-3">
                        <div class="bg-light p-3 rounded border">
                            <label for="file" class="form-label font-weight-bold text-secondary d-block">
                                Substituir Minuta (.docx)
                            </label>

                            @if($portaria->file_name)
                                <p class="small text-muted mb-2">
                                    <i class="fas fa-file-word text-primary mr-1 me-1"></i> Arquivo atual: <strong>{{ $portaria->file_name }}</strong>
                                </p>
                            @endif

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white text-primary">
                                        <i class="fas fa-file-word"></i>
                                    </span>
                                </div>
                                <input type="file" 
                                       name="file" 
                                       id="file" 
                                       class="form-control @error('file') is-invalid @enderror" 
                                       accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                            </div>
                            <small class="form-text text-muted mt-1">Envie apenas se desejar substituir o arquivo Word atual (Máx: 10MB).</small>
                            @error('file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end align-items-center">
                    <a href="{{ route('portarias.show', $portaria) }}" class="btn btn-light border mr-2 me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary-fflch px-4 font-weight-bold shadow-sm">
                        <i class="fas fa-save mr-1 me-1"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Exclusão --}}
    @if($portaria->status->isDeletable())
        <div class="card border-danger shadow-sm mt-4">
            <div class="card-header bg-danger text-white font-weight-bold py-2">
                Excluir Registro
            </div>
            <div class="card-body d-flex justify-content-between align-items-center p-3">
                <div>
                    <h6 class="font-weight-bold mb-0 text-danger">Excluir esta portaria</h6>
                    <small class="text-muted">Ação permanente. Remove o registro e o arquivo anexo.</small>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm font-weight-bold" data-toggle="modal" data-target="#deleteModal" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash-alt mr-1 me-1"></i> Excluir
                </button>
            </div>
        </div>

        {{-- Modal --}}
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold">Confirmar Exclusão</h5>
                        <button type="button" class="close text-white btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        Tem certeza de que deseja excluir o registro de <strong>{{ $portaria->title }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('portarias.destroy', $portaria) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Sim, Excluir</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection