<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portaria;
use App\Enums\PortariaStatus;
use Fflch\LaravelFflchStepper\Stepper;

class PortariaController extends Controller
{
    public function create() {
        return view('portarias.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'type' => 'required|string',
            'title' => 'required|string|max:500',
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('pdf_file');
        $pdfPath = $file->store('portarias/' . now->year(), 'public');
        $fileHash = hash_file('sha256', $file->getRealPath());

        Portaria::create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'number' => $request->number ?? null,
            'year' => now()->year,
            'pdf_path' => $pdfPath,
            'file_name' => $file->getClientOriginalName(),
            'file_hash' => $fileHash,
            'status' => PortariaStatus::PENDING_APPROVAL->value(),
            'created_by' => auth()->id(),
        ]);

        return redirect('/');
    }

    public function show(Portaria $portaria, Stepper $stepper){
        $stepper->setCurrentStepName($portaria->status);
        return view('portarias.show',[
            'portaria' => $portaria,
            'stepper' => $stepper->render()
        ]);
    }

    public function edit(Portaria $portaria) {
        return view('portarias.edit', ['portaria' => $portaria]);
    }

    public function update(Request $request, Portaria $portaria) {
        Portaria::findOrFail($portaria->id)->update([
            'title' => $request->title,
            'introduction' => $request->introduction,
            'content' => $request->introduction,
        ]);
        return redirect('/portaria/{$portaria->id}');
    }

    public function destroy(Portaria $portaria) {
        $portaria::delete();
        return redirect('/');
    }
}
