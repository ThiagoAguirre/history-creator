<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/** Public landing page for History Creator. */
class HomePageController extends AppController
{
    /** @return void */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['index']);
    }

    /** @return void */
    public function index(): void
    {
        $this->viewBuilder()->setLayout('landing');
    }
}
