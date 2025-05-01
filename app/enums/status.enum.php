<?php

namespace App\Enums;

enum Status: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
enum Etats : string {
    case EN_COURS = 'en_cours';
    case TERMINE = 'termine';
    case ATTENTE = 'en_attente';
}