<?php

namespace App\Enums;

enum PortariaType:string {
    case COMISSAO = "comissao";
    case DESIGNACAO = "designacao";
    case ELEICAO = "eleicao";
    case ADMINISTRATIVA = "administrativa";

    public function label(): string {
        return match($this) {
            self::COMISSAO => "Comissões e Grupos de Trabalho",
            self::DESIGNACAO => "Deisgnações e Representações",
            self::ELEICAO => "Processos Eleitorais (CCP, Conselhos)",
            self::ADMINISTRATIVA => "Normativas e Administrativas",
        };
    }
}
