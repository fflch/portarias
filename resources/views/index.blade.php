@extends('laravel-usp-theme::master')

@section('content')

<div class="container mt-4" style="min-height: 70vh;">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Portarias</h5>
        </div>
        <div class="card-body p-0">
            {{-- BLOCO COM SCROLL --}}
            <div class="scroll-area p-3" style="max-height:65vh; overflow-y:auto;">
                <div class="row g-3">
                    @forelse ($portarias as $portaria)
                        <div class="col-md-4">
                            <div class="card h-100 border">
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">
                                        {{ $portaria->title }}
                                    </h6>
                                    <span class="badge bg-secondary mb-2">
                                        Tipo {{ $portaria->type }}
                                    </span>
                                    <span class="badge bg-secondary mb-2">
                                        Autor {{ \App\Models\User::where('id', $portaria->created_by)->value('name') }}
                                    </span>
                                    <div class="mt-auto">
                                        <a href="{{ route('portarias.show', $portaria) }}" class="btn btn-outline-primary btn-sm w-100 mb-1">
                                            Ver
                                        </a>
                                        <a href="{{ route('portarias.edit', $portaria) }}" class="btn btn-outline-secondary btn-sm w-100">
                                            Editar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty

                        <div class="col-12 text-center text-muted py-5">
                            Nenhuma portaria cadastrada
                        </div>

                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection("content")