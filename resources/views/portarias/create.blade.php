@extends('laravel-usp-theme::master')

@section('content')
    <div class="container mt-4" style="min-height: 70vh;">

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Nova Portaria</h5>
                <a href="{{ route('portarias.index') }}" class="btn btn-secondary btn-sm">
                    Voltar
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('portarias.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Tipo de Portaria</label>
                        <select name="tipo" id="tipo" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="A">Tipo A — Eleição CCP</option>
                            <option value="B">Tipo B — Comissões</option>
                            <option value="C">Tipo C — Designação</option>
                            <option value="D">Tipo D — Administrativa</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"></label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Documento (.docx)</label>
                        <input type="file" name="arquivo" class="form-control" accept=".docx" required>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary">
                            Salvar Portaria
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

