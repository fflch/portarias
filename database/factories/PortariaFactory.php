<?php

namespace Database\Factories;

use App\Models\Portaria;
use App\Models\User;
use App\Enums\PortariaStatus;
use App\Enums\PortariaType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Portaria>
 */
class PortariaFactory extends Factory
{

    protected $model = Portaria::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        Storage::disk('public')->makeDirectory('portarias/' . now()->year);
        $fakePdfPath = 'portarias/' . now()->year . '/test_sample.pdf';

        if (!Storage::disk('public')->exists($fakePdfPath)) {
            Storage::disk('public')->put($fakePdfPath, '%PDF-1.4 Fake PDF Content for Testing');
        }

        $status = $this->faker->randomElement(PortariaStatus::cases());
        $isPublished = $status === PortariaStatus::PUBLISHED; 

        return [
            'type' => $this->faker->randomElement(PortariaType::cases()),
            'title' => 'Dispõe sobre ' . $this->faker->sentence(6),
            'number' => $isPublished ? $this->faker->unique->numberBetween(1, 100) : null,
            'year' => now()->year,
            'pdf_path' => $fakePdfPath,
            'file_name' => 'Portaria_Teste_' . $this->faker->word() . '.pdf',
            'file_hash' => hash('sha256', 'fake-content'),
            'status' => $status,
            'rejection_reason' => $status === PortariaStatus::REJECTED ? 'PDF ilegível ou sem assinatura.' : null,
            'created_by' => User::factory(),
            'approved_by' => $isPublished ? User::factory() : null,
            'approved_at' => $isPublished ? now() : null,
            'published_at' => $isPublished ? now()->toDateString() : null,
        ];
    }
}
