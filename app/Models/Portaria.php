<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portaria extends Model
{
    protected $fillable = [
        //campo número é criado sequencialmente. 0 a n para cada ano e reseta na virada. (Criar teste de número se é criado sequencialmente)
        'number',
        'year',
        'date',
        'title',
        'introduction',
        'content',
        'status',
        'created_by',
        'approved_by',
        'published_at'
    ];

    public function getStatus(){
        $status = [
            'em_elaboracao' => [
                'name' => "Em Elaboração",
                'optional' => 'Empresa'
            ],
            'em_analise_tecnica' => [
                'name' => "Análise Técnica",
                'optional' => 'Setor de Graduação'
            ],

            'assinatura' => [
                'name' => "Assinatura",
                'optional' => 'Setor de Graduação'
            ],

            'em_analise_academica' => [
                'name' => "Parecer de Mérito",
                'optional' => 'Docente'
            ],
            'concluido' => [
                'name' => "Concluído",
                'optional' => 'Docente'
            ],
            'em_alteracao' => [
                'name' => "Aditivo de Alterações",
                'optional' => 'Empresa'
            ],
            'analise_alteracao_parecerista' => [
                'name' => "Aditivo de Alterações - Análise do Parecerista",
                'optional' => 'Docente'
            ],
            'rescisao' => [
                'name' => "Rescisão",
                'optional' => 'Empresa'
            ],
            'cancelado' => [
                'name' => "Cancelado",
                'optional' => 'Estágio Cancelado'
            ],
        ];

        return $status;
    }
}
