<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portaria;
use App\Enums\PortariaStatus;
use App\Enums\PortariaType;
use Fflch\LaravelFflchStepper\Stepper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UpdatePortariaRequest;
use App\Http\Requests\StorePortariaRequest;
use App\Http\Requests\DestroyPortariaRequest;

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

    public function store(StorePortariaRequest $request) {
        $validated = $request->validated();

        $isAuto = $validated['numbering_type'] === 'auto';
        $year = $isAuto ? now()->year : $validated['year'];

        $file = $request->file('file');
        $filePath = $file->store('portarias/' . $year, 'public'); 
        $fileHash = hash_file('sha256', $file->getRealPath());

        $portaria = DB::transaction(function () use ($isAuto, $validated, $year, $filePath, $file, $fileHash) {
        
            if ($isAuto) {
                $lastNumber = Portaria::where('year', $year)->lockForUpdate()->max('number');
                $number = $lastNumber ? $lastNumber + 1 : 1;
            } else {
                $number = $validated['number'];
            }

            return Portaria::create([
                'type'       => $validated['type'],
                'title'      => $validated['title'],
                'number'     => $number,
                'year'       => $year,
                'file_path'  => $filePath,
                'file_name'  => $file->getClientOriginalName(),
                'file_hash'  => $fileHash,
                'status'     => PortariaStatus::PENDING_APPROVAL,
                'created_by' => auth()->id(),
            ]);
        });

        Log::info("Nova portaria criada", [
            'portaria_id' => $portaria->id,
            'number'      => "{$portaria->number}/{$portaria->year}",
            'user_id'     => auth()->id(),
            'user_name'   => auth()->user()->name,
            'type'        => $portaria->type,
        ]);

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

    public function update(UpdatePortariaRequest $request, Portaria $portaria) {
        $validated = $request->validated();
        $canEditNumber = auth()->user()->can('admin');

        $oldFilePath = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $targetYear = $validated['year'] ?? $portaria->year;

            $oldFilePath = $portaria->file_path;

            $portaria->file_path = $file->store('portarias/' . $targetYear, 'public');
            $portaria->file_name = $file->getClientOriginalName();
            $portaria->file_hash = hash_file('sha256', $file->getRealPath());
        }

        $updateData = [
            'type'   => $validated['type'],
            'title'  => $validated['title'],
            'status' => $validated['status'],
        ];

        if ($canEditNumber) {
            $updateData['number'] = $validated['number'] ?? null;
            $updateData['year']   = $validated['year'] ?? $portaria->year;
        }

        $portaria->update($updateData);

        if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
            Storage::disk('public')->delete($oldFilePath);
        }

        Log::info("Portaria atualizada", [
            'portaria_id'  => $portaria->id,
            'user_id'      => auth()->id(),
            'user_name'    => auth()->user()->name,
            'changes'      => $portaria->getChanges(), // Pega apenas os campos que mudaram
            'file_changed' => $request->hasFile('file'),
        ]);

        return redirect()->route('portarias.show', $portaria)
            ->with('alert-success', "Portaria atualizada com sucesso!");
    }

    public function destroy(DestroyPortariaRequest $request, Portaria $portaria) {
        $portariaId = $portaria->id;
        $portariaNumber = "{$portaria->number}/{$portaria->year}";
        $filePath = $portaria->file_path;

        $portaria->delete();

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        Log::warning("Portaria excluída permanentemente", [
            'portaria_id' => $portariaId,
            'number'      => $portariaNumber,
            'user_id'     => auth()->id(),
            'user_name'   => auth()->user()->name,
        ]);

        return redirect()->route('portarias.index')
            ->with('alert-success', 'Portaria excluída com sucesso!');
    }
}
