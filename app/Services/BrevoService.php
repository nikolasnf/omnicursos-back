<?php

namespace App\Services;

use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\ApiException;
use SendinBlue\Client\Model\CreateContact;
use GuzzleHttp\Client;

class BrevoService
{
    protected $contactsApi;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', config('services.brevo.api_key'));

        $client = new Client();
        $this->contactsApi = new ContactsApi($client, $config);
    }

    public function addContact($email, $attributes = [], $listIds = [])
    {
        try {
            $contact = new CreateContact([
                'email' => $email,
                'attributes' => $attributes,
                'listIds' => $listIds,
                'updateEnabled' => true
            ]);

            $this->contactsApi->createContact($contact);
            return true;
        } catch (ApiException $e) {
            \Log::error('Erro ao adicionar contato no Brevo: ' . $e->getMessage());
            return false;
        }
    }
}
