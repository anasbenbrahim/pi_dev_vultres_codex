<?php
// src/Service/OllamaClient.php
namespace App\Service;

use App\Repository\ChatAiRepository;
use App\Entity\ChatAi;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OllamaClient
{
    private HttpClientInterface $httpClient;
    private array $responses = []; // Stocker les réponses partielles

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function fetchLlamaResponse(ChatAiRepository $repo): array
    {
        $quest=$repo->findOneBy([],['id'=>'DESC']);
        
        if (!$quest) {
            throw new \Exception("Aucune question trouvée dans la base de données.");
        }
        $prompt=$quest->getQuestion();
        $response = $this->httpClient->request('POST', 'http://localhost:11434/api/generate', [
            'json' => ['model' => 'llama3.1', 'prompt' => $prompt, 'stream' => false]
        ]);

        $stream = $this->httpClient->stream($response);

        foreach ($stream as $chunk) { // Lire chaque morceau
            $content = $chunk->getContent();
            try {
                $json = json_decode($content, true);
                if (is_array($json)) {
                    $this->responses[] = $json; // Ajouter chaque fragment
                }
            } catch (\Exception $e) {
                // Gérer les erreurs de JSON
            }
        }

        return $this->mergeResponses(); // Fusionner et renvoyer
    }

    private function mergeResponses(): array
    {
        return array_merge(...$this->responses); // Fusionner les objets JSON
    }
}