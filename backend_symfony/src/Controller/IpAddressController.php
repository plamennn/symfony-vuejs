<?php

namespace App\Controller;

use App\Entity\IpAddress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IpAddressController extends AbstractController
{
    private $entityManager;
    private $client;
    private $ipinfoApiToken;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->ipinfoApiToken = '888b16870ea179'; // Replace with your actual token
    }

    #[Route('/api/ips', name: 'get_ips', methods: ['GET'])]
    public function getIps(): JsonResponse
    {
        $ips = $this->entityManager->getRepository(IpAddress::class)->findAll();
        return $this->json($ips);
    }

    #[Route('/api/ips', name: 'add_ip', methods: ['POST'])]
    public function addIp(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['address']) || empty($data['address'])) {
            return $this->json(['error' => 'IP address is required.'], Response::HTTP_BAD_REQUEST);
        }

        $ipAddressString = $data['address'];
        
        // Validate IP address format
        if (!filter_var($ipAddressString, FILTER_VALIDATE_IP)) {
            return $this->json(['error' => 'Invalid IP address format.'], Response::HTTP_BAD_REQUEST);
        }

        // Check if IP address already exists
        $existingIp = $this->entityManager->getRepository(IpAddress::class)->findOneBy(['address' => $ipAddressString]);

        if ($existingIp) {
            return $this->json(['error' => 'IP address already exists.'], Response::HTTP_CONFLICT);
        }

        // Create and persist the new IP address
        $ipAddress = new IpAddress();
        $ipAddress->setAddress($ipAddressString);

        $this->entityManager->persist($ipAddress);
        $this->entityManager->flush();

        return $this->json($ipAddress, Response::HTTP_CREATED);
    }

    #[Route('/api/ips/{id}/synchronize', name: 'synchronize_ip', methods: ['POST'])]
    public function synchronizeIp(int $id): JsonResponse
    {
        $ipAddress = $this->entityManager->getRepository(IpAddress::class)->find($id);

        if (!$ipAddress) {
            return new JsonResponse(['error' => 'IP Address not found'], Response::HTTP_NOT_FOUND);
        }

        $response = $this->client->request('GET', "https://ipinfo.io/{$ipAddress->getAddress()}", [
            'query' => ['token' => $this->ipinfoApiToken],
        ]);

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse(['error' => 'Failed to synchronize IP data'], Response::HTTP_BAD_REQUEST);
        }

        $data = $response->toArray();
        $ipAddress->setCity($data['city'] ?? null);
        $ipAddress->setRegion($data['region'] ?? null);
        $ipAddress->setCountry($data['country'] ?? null);
        $ipAddress->setLoc($data['loc'] ?? null);
        $ipAddress->setOrg($data['org'] ?? null);
        $ipAddress->setPostal($data['postal'] ?? null);

        $this->entityManager->flush();

        return $this->json($ipAddress);
    }

    #[Route('/api/ips/{id}', methods: ['DELETE'])]
    public function deleteIpAddress(IpAddress $ip, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($ip);
        $em->flush();

        return $this->json(['status' => 'success']);
    }

    #[Route('/api/ips/{id}', name: 'update_ip', methods: ['PUT'])]
    public function updateIp(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ipAddress = $this->entityManager->getRepository(IpAddress::class)->find($id);

        if (!$ipAddress) {
            return new JsonResponse(['error' => 'IP address not found'], Response::HTTP_NOT_FOUND);
        }

        // Validate new IP address
        if (!filter_var($data['address'], FILTER_VALIDATE_IP)) {
            return new JsonResponse(['error' => 'Invalid IP address format'], Response::HTTP_BAD_REQUEST);
        }

        // Check for existing IP address
        $existingIp = $this->entityManager->getRepository(IpAddress::class)->findOneBy(['address' => $data['address']]);
        if ($existingIp && $existingIp->getId() !== $id) {
            return new JsonResponse(['error' => 'IP address already exists'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $ipAddress->setAddress($data['address']);
            $this->entityManager->flush();
            return $this->json($ipAddress);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to update IP address'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
