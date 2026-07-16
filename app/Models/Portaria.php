<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portaria extends Model
{
    protected $fillable = [
        //campo número é criado sequencialmente. 0 a n para cada ano e reseta na virada. (Criar teste de número se é criado sequencialmente)
        'number',
        'date',
        'type',
        'status',
        'is_legacy',
        'revoke_id',
        'created_by',
        'approved_by',
        'published_at'
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
