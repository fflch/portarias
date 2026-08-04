<?php

namespace App\Enums;

enum PortariaStatus: string {
    case PENDING_APPROVAL = "em_analise";
    case PUBLISHED = "publicado";
    case REJECTED = "rejeitado";
    case REVOKED = "revogado";

    public function label(): string {
        return match($this) {
            self::PENDING_APPROVAL => "Em Análise",
            self::PUBLISHED => "Publicado",
            self::REJECTED => "Rejeitado",
            self::REVOKED => "Revogado",
        };
    }

    public function color(): string {
        return match($this) {
            self::PENDING_APPROVAL => "warning",
            self::PUBLISHED => "success",
            self::REJECTED => "danger",
            self::REVOKED => "dark",
        };
    }

    public function isEditable(): bool {
        return $this === self::PENDING_APPROVAL;
    }

    public function isDeletable(): bool {
        return $this === self::PENDING_APPROVAL;
    }
}

