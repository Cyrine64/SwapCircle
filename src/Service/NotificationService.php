<?php

namespace App\Service;

use MercurySeries\FlashyBundle\FlashyNotifier;

class NotificationService
{
    private $flashy;

    public function __construct(FlashyNotifier $flashy)
    {
        $this->flashy = $flashy;
    }

    public function notifyReclamationSubmitted()
    {
        $this->flashy->success('Votre réclamation a été soumise avec succès!', 'app_reclamation_index');
    }

    public function notifyReclamationUpdated()
    {
        $this->flashy->info('La réclamation a été mise à jour', 'app_reclamation_index');
    }

    public function notifyReclamationProcessed()
    {
        $this->flashy->primary('Votre réclamation est en cours de traitement', 'app_reclamation_index');
    }

    public function notifyReclamationResolved()
    {
        $this->flashy->success('Votre réclamation a été résolue!', 'app_reclamation_index');
    }
}
