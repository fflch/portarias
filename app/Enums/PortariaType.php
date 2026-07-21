<?php

namespace App\Enums;

enum PortariaType:string {
    case INTERNA = "Portaria Interna";
    case GD = "Portaria GD";
    case EDITAL = "Edital";
    case CONJUNTA = "Portaria Conjunta";

    public function label(): string {
        return $this->value;
    }
}
