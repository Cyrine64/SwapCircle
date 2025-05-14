<?php

namespace App\Entity;

enum TypeReclamation : string
{
    case Technique = 'technique';
    case Utilisateur = 'sur utilisateur';
    case Objet = 'sur objet';
    case Echange = 'sur échange';
    case Recyclage = 'sur recyclage';
    case EchangeNonHonore = 'Échange non honoré';
}
