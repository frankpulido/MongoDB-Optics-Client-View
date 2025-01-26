<?php
declare(strict_types=1);
//use MongoDB\Client;
use MongoDB\BSON\ObjectId;
/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ApplicationController extends Controller {

    public function indexAction()
    {
        $this->view->title = "Warnings";
    }


    public function opticsAction() {
        $clientModel = new Client();
        $db = $clientModel->getDatabase();

        // Get all clients for dropdown
        $allClients = $db->clients->find([], ['projection' => ['_id' => 1, 'client_name' => 1]])->toArray();
        // Get all frames for "Last Shoppings" details
        $allGlassFrames = $db->glassframes->find([], ['projection' => ['_id' => 1, 'name' => 1, 'brand' => 1]])->toArray();

        // Get selected client or default to first
        $selectedClientId = $_POST['client_id'] ?? $allClients[0]['_id'];
        $selectedClient = $db->clients->findOne(['_id' => new MongoDB\BSON\ObjectId($selectedClientId)]);

        if ($selectedClient) {
            // Fetch related data for last shoppings
            foreach ($selectedClient['last_shoppings'] as &$shopping) {
                $shopping['glassframe'] = $db->glassframes->findOne(['_id' => $shopping['glassframe_id']]);
                $shopping['employee'] = $db->employees->findOne(['_id' => $shopping['soldBy']]);
            }
        }

        $this->view->title = 'Client Information';
        $this->view->allClients = $allClients;
        $this->view->allGlassFrames = $allGlassFrames;
        $this->view->selectedClient = $selectedClient;
    }
}