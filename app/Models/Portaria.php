<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portaria extends Model
{
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
