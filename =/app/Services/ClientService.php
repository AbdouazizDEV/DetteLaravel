<?php

namespace App\Services;

use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Services\Contracts\ClientServiceInterface;
use Illuminate\Support\Facades\Storage;
use App\Models\Client;
use App\Facades\UploadFacade;
use App\Services\Contracts\FileStorageServiceInterface;
use App\Repositories\Contracts\UserRepositoryInterface; // Uncommented this line
use Illuminate\Http\Request;
use App\Events\ClientCreated;
use App\Services\CloudinaryService;
use Cloudinary\Api\Upload\UploadApi;
class ClientService implements ClientServiceInterface
{
    protected $clientRepository;
    protected $fileStorageService;
    protected $userRepository;
    protected $cloudinaryService;


    public function __construct(
        ClientRepositoryInterface $clientRepository,
        UserRepositoryInterface $userRepository,
        FileStorageServiceInterface $fileStorageService,
        CloudinaryService $cloudinaryService
    ) {
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->fileStorageService = $fileStorageService;
        $this->cloudinaryService = $cloudinaryService;
    }
    
    public function storeClient(array $data): Client
{
    $userId = null;
    
    // dd($data['photo']);
    if (isset($data['user'])) {
        $user = $this->userRepository->create($data['user']);
        $userId = $user->id;
        
        if (isset($data["user"]['photo'])) {
            try {
                // Téléchargement de l'image vers Cloudinary
                $uploadedImage = $this->cloudinaryService->upload($data["user"]['photo'], [
                    'folder' => 'avatars',
                    'public_id' => 'user_' . $userId ,
                    'overwrite' => true,
                    'resource_type' => 'image',
                ]);
                
                // Récupération du lien public de l'image
                $path = $uploadedImage['secure_url'];
            // dd($path);
                // Mise à jour du chemin de la photo dans la base de données
                $user->photo = $path;
                $user->save();
            } catch (\Exception $e) {
                // Gestion de l'erreur si le téléchargement échoue, stockage en base64
                //$user->photo = base64_encode(file_get_contents($data["user"]['photo']));
                //Gestion de l'erreur si le téléchargement échoue, stocker le chemin local de l'image dans la base de données
                $user->photo = $data["user"]['photo'];
                $user->save();
            }
        }
    }

    $clientData = [
        'surnom' => $data['surnom'],
        'telephone_portable' => $data['telephone_portable'],
        'adresse' => $data['adresse'] ?? null,
        'user_id' => $userId,
        'avatar' => $userId ? null : ($data['avatar'] ?? $this->getDefaultAvatar()),
    ];

    $client = $this->clientRepository->create($clientData);

    // Lancer un événement pour l'envoi de l'email et d'autres traitements asynchrones
    //event(new ClientCreated($client));
    $emailService = app(EmailService::class);
    $emailService->sendFideliteEmail($client);
    return $client;
}

    
    protected function getDefaultAvatar(): string
    {
        // Retourne l'avatar par défaut (par exemple en base64)
        return base64_encode(file_get_contents(public_path('default_avatar.png')));
    }
    
    public function attachUserToClient(int $clientId, array $userData): Client
    {
        $client = $this->clientRepository->find($clientId);

        $user = $this->userRepository->create($userData);
        $client->user_id = $user->id;

        if (isset($userData['photo'])) {
            $path = 'photos/' . time() . '_' . basename($userData['photo']);
            $this->fileStorageService->store($userData['photo'], $path);
            $user->photo = $path;
            $user->save();
        }

        $client->save();

        return $client;
    }

    public function getAllClients(Request $request)
    {
        // Gestion du filtrage, tri et pagination
        $query = Client::query();

        if ($request->has('surnom')) {
            $query->where('surnom', 'like', '%' . $request->input('surnom') . '%');
        }

        if ($request->has('telephone_portable')) {
            $query->where('telephone_portable', 'like', '%' . $request->input('telephone_portable') . '%');
        }

        if ($request->has('sortBy')) {
            $sortBy = $request->input('sortBy');
            $sortOrder = $request->input('sortOrder', 'asc');
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->input('perPage', 15);
        return $query->paginate($perPage);
    }

    public function getClientById($id): Client|null
    {
        $client = Client::find($id);
    
        if (!$client) {
            return null;
        }
    
        // Convertir la photo en base64 si l'URL de la photo existe
        if (!empty($client->photo)) {
            $client->photo = $this->convertImageToBase64($client->photo);
        } elseif ($client->user_id) {
            $user = $client->user;
            if ($user && !empty($user->photo)) {
                $client->photo = $this->convertImageToBase64($user->photo);
            }
        }
    
        return $client;
    }
protected function convertImageToBase64(string $url): string
{
    $imageData = file_get_contents($url);
    return 'data:image/' . pathinfo($url, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imageData);
}


    public function all($active = null)
    {
        $clients = $this->clientRepository->all($active);
        return $this->convertImagesToBase64($clients);
    }

    public function find($id)
    {
        $client = $this->clientRepository->find($id);
        return $this->convertImageToBase64($client);
    }

    public function create(array $data)
    {
       // Traitement de la photo
       if (isset($data['user']['photo']) && $data['user']['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['user']['photo'] = UploadFacade::uploadImage($data['user']['photo']);
        }
        $photoBase64 = $data['user']['photo'] ? UploadFacade::getImageAsBase64($data['user']['photo']) : null;
        // dd($photoBase64);
      /*   $qrCodeBase64 = QrCodeFacade::generateBase64QrCode($data['telephone']);
        // dd($qrCodeBase64);
        $this->qrCodeService->createLoyaltyCard(
            $data['surname'],
            $data['telephone'],  
            $photoBase64,
            $qrCodeBase64
        ); */
       // $data['qrcode'] = $qrCodeBase64;
        // echo('<img src="'.$qrCodeBase64.'" alt="'.$photoBase64.'" />');
        // die();
        $userData = isset($data['user']) ? [
            'nom' => $data['user']['nom'],
            'prenom' => $data['user']['prenom'],
            'login' => $data['user']['login'],
            'password' => bcrypt($data['user']['password']),
            'etat' => $data['user']['etat'],
            'role_id' => $data['user']['role'],
            'photo' => $photoBase64,
        ] : null;

      //  $client = $this->clientRepository->create($data, $userData);


        //return $client;
    }

    public function update($id, array $data)
    {
        return $this->clientRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->clientRepository->delete($id);
    }

    public function searchByTelephone($telephone)
    {
        $client = $this->clientRepository->searchByTelephone($telephone);
        return $this->convertImageToBase64($client);
    }

    public function listDettes($id)
    {
        $result = $this->clientRepository->listDettes($id);
        if ($result) {
            $result['client'] = $this->convertImageToBase64($result['client']);
        }
        return $result;
    }

    public function showWithUser($id)
    {
        $result = $this->clientRepository->showWithUser($id);
        if ($result) {
            $result['client'] = $this->convertImageToBase64($result['client']);
        }
        return $result;
    }

    /* private function convertImageToBase64($client)
    {
        if ($client && $client->photo) {
            $path = str_replace(url('/'), '', $client->photo);
            if (Storage::exists($path)) {
                $imageData = Storage::get($path);
                $client->photo = base64_encode($imageData);
            } else {
                $client->photo = null; // Si le fichier n'existe pas, définir photo à null
            }
        }
        return $client;
    } */

    private function convertImagesToBase64($clients)
    {
        foreach ($clients as $client) {
            $this->convertImageToBase64($client);
        }
        return $clients;
    }
}