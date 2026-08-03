<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portaria;
use App\Enums\PortariaStatus;
use App\Enums\PortariaType;
use Fflch\LaravelFflchStepper\Stepper;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PortariaController extends Controller
{
    public function index(Request $request){
        $portarias = Portaria::with('creator')
            ->when($request->filter === 'minhas', function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->latest()
            ->get();

        return view('portarias.index', compact('portarias'));
    }

    public function create() {
        return view('portarias.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'type' => ['required', new Enum(PortariaType::class)],
            'title' => 'required|string|max:500',
            'file' => 'required|file|mimes:docx|max:10240',
            'numbering_type' => 'required|in:auto,manual',
            'number' => [
                'exclude_if:numbering_type,auto',
                'required_if:numbering_type,manual',
                'integer',
                'min:1',
                
                Rule::unique('portarias')->where(fn ($query) => $query->where('year', $request->year))
            ],
            'year' => [
                'exclude_if:numbering_type,auto',
                'required_if:numbering_type,manual',
                'integer',
                'min:1900',
                'max:' . (now()->year + 1)
            ]
        ]);

        $year = $request->numbering_type === 'manual' ? $validated['year'] : now()->year;
        $file = $request->file('file');
        $filePath = $file->store('portarias/' . now()->year, 'public');
        $fileHash = hash_file('sha256', $file->getRealPath());

        $portaria = DB::transaction(function () use ($validated, $request, $year, $file, $filePath, $fileHash) {
            if ($request->numbering_type === 'auto') {
                $lastNum = Portaria::where('year', $year)
                    ->lockForUpdate()
                    ->max('number');
                $number = $lastNum ? $lastNum + 1 : 1;
            }else {
                $number = $validated['number'];
            }

            return Portaria::create([
                'type' => $validated['type'],
                'title' => $validated['title'],
                'number' => $number,
                'year' => $year,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_hash' => $fileHash,
                'status' => PortariaStatus::PENDING_APPROVAL,
                'created_by' => auth()->id(),
            ]);
    
        });

        return redirect()->route('portarias.show', $portaria)
            ->with('alert-success', 'Portaria cadastrada com sucesso!');
        
    }

    public function show(Portaria $portaria, Stepper $stepper){
        $stepper->setCurrentStepName($portaria->status?->label());
        return view('portarias.show',[
            'portaria' => $portaria,
            'stepper' => $stepper->render()
        ]);
    }

    public function edit(Portaria $portaria) {
        if (!$portaria->status->isEditable()) {
            return redirect()->route('portarias.show', $portaria)
                ->with('alert-danger', 'Portarias com status "' . $portaria->status->label() . '" não podem ser editadas.');
        }

        return view('portarias.edit', compact('portaria'));
    }

    public function update(Request $request, Portaria $portaria) {
        if (!$portaria->status->isEditable()) {
            return redirect()->route('portarias.show', $portaria)
                ->with('alert-danger', "Está portaria não pode mais ser alterada.");
        }

        $validated = $request->validate([
            'type'   => ['required', new Enum(PortariaType::class)],
            'title'  => 'required|string|max:500',
            'number' => 'nullable|integer|min:1',
            'year'   => 'nullable|integer|digits:4',
            'status' => ['required', new Enum(PortariaStatus::class)],
            'file'   => 'nullable|file|mimes:docx|max:10240',
        ]);

        if ($request->hasFile('file')) {
            if ($portaria->file_path && Storage::disk('public')->exists($portaria->file_path)) {
                Storage::disk('public')->delete($portaria->file_path);
            }

            $file = $request->file('file');
            $targetYear = $validated['year'] ?? $portaria->year ?? now()->year;

            $portaria->file_path = $file->store('portarias/' . $targetYear, 'public');
            $portaria->file_name = $file->getClientOriginalName();
            $portaria->file_hash = hash_file('sha256', $file->getRealPath());
        }

        $portaria->update([
            'type'   => $validated['type'],
            'title'  => $validated['title'],
            'number' => $validated['number'] ?? null,
            'year'   => $validated['year'] ?? $portaria->year,
            'status' => $validated['status'],
        ]);

        return redirect()->route('portarias.show', $portaria)
            ->with('alert-success', "Portaria atualizada com sucesso!");
    }

    public function destroy(Portaria $portaria) {
        if (!$portaria->status->isDeletable()) {
            return redirect()->route('portarias.show', $portaria)
                ->with('alert-danger', 'Apenas portarias em análise podem ser excluídas.');
        }

        if ($portaria->file_path && Storage::disk('public')->exists($portaria->file_path)) {
            Storage::disk('public')->delete($portaria->file_path);
        }

        $portaria->delete();

        return redirect()->route('portarias.index')
            ->with('alert-success', "Portaria excluída permanentemente.");
    }
}
