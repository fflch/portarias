<?php

namespace App\Models;

use App\Enums\PortariaStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Portaria extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'number',
        'year',
        'published_at',
        'pdf_path',
        'file_name',
        'file_hash',
        'status',
        'rejection_reason',
        'revoke_id',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected function casts(): array {
        return [
            'status' => PortariaStatus::class,
            'approved_at' => 'datetime',
            'published_at' => 'date',
        ];
    }

    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatus(){
        $status = [
            'enviado' => [
                'name' => "Enviado",
            ],

            'em_analise_tecnica' => [
                'name' => "Análise Técnica",
            ],

            'aprovado' => [
                'name' => "Aprovado",
            ],

            'rejeitado' => [
                'name' => "Rejeitado",
            ],

            'revogado' => [
                'name' => "Revogado",
            ]            
        ];

        return $status;
    }
}
