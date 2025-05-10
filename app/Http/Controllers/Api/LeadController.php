<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\CreateContact;
use GuzzleHttp\Client;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string',
        ]);

        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', env('BREVO_API_KEY'));

        $apiInstance = new ContactsApi(new Client(), $config);

        $contact = new CreateContact([
            'email' => $request->email,
            'attributes' => [
                'FIRSTNAME' => $request->name ?? '',
            ],
            'updateEnabled' => true
        ]);

        try {
            $apiInstance->createContact($contact);
            return response()->json(['success' => true, 'message' => 'Contato adicionado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
