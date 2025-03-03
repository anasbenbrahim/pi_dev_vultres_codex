<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\Security; // Import Security


#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'cart_index')]
    public function index(SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $panier = $session->get("panier", []);
        $dataPanier = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $produit = $produitRepository->find($id);
            if ($produit) {
                $dataPanier[] = [
                    "produit" => $produit,
                    "quantite" => $quantity
                ];
                $total += $produit->getPrix() * $quantity;
            }
        }

        return $this->render('cart/index.html.twig', [
            'dataPanier' => $dataPanier,
            'total' => $total
        ]);
    }

    #[Route('/add/{id}', name: 'cart_addinmarche')]  // Changed route name to match your template
    public function add(Produit $produit, SessionInterface $session): Response
    {
        $panier = $session->get("panier", []);
        $id = $produit->getId();

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set("panier", $panier);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name: 'cart_remove')]
    public function remove(Produit $produit, SessionInterface $session): Response
    {
        $panier = $session->get("panier", []);
        $id = $produit->getId();

        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }

        $session->set("panier", $panier);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/delete/{id}', name: 'cart_delete')]
    public function delete(Produit $produit, SessionInterface $session): Response
    {
        $panier = $session->get("panier", []);
        $id = $produit->getId();

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set("panier", $panier);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/clear', name: 'cart_clear')]
    public function deleteAll(SessionInterface $session): Response
    {
        $session->remove("panier");
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/validate', name: 'cart_validate')]
    public function sendEmail(MailerInterface $mailer, SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $panier = $session->get("panier", []);
        $dataPanier = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $produit = $produitRepository->find($id);
            if ($produit) {
                $dataPanier[] = [
                    "produit" => $produit,
                    "quantite" => $quantity
                ];
                $total += $produit->getPrix() * $quantity;
            }
        }

        $email = (new TemplatedEmail())
            ->from('espritagri11@gmail.com')
            ->to('mannai.dhia.1@gmail.com')
            ->subject('Confirmation Commande')
            ->htmlTemplate('cart/confirmationpayement.html.twig')
            ->context([
                'dataPanier' => $dataPanier,
                'total' => $total,
            ]);

        try {
            $mailer->send($email);
            $this->addFlash('success', 'Commande validée! Vérifiez votre adresse mail.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Échec d\'envoi! ' . $e->getMessage());
        }

        return $this->redirectToRoute('cart_index');
    }
    #[Route('/checkout', name: 'cart_checkout')]
    public function checkout(SessionInterface $session, EntityManagerInterface $em, Security $security): Response
    {
        $panier = $session->get('panier', []);
        $total = 0;
        
        // Validate quantities before processing
        foreach ($panier as $id => $quantity) {
            $produit = $em->getRepository(Produit::class)->find($id);
            if ($produit && $quantity > $produit->getQuantite()) {
                $this->addFlash('danger', 'La quantité demandée pour ' . $produit->getNomprod() . ' dépasse le stock disponible.');
                return $this->redirectToRoute('cart_index');
            }
        }

    
        if (!$security->getUser()) {
            $this->addFlash('warning', 'Vous devez être connecté pour passer une commande.');
            return $this->redirectToRoute('app_login');
        }
    
        if (empty($panier)) {
            $this->addFlash('danger', 'Votre panier est vide.');
            return $this->redirectToRoute('cart_index');
        }
    
        $user = $security->getUser();
        $order = new Order();
        $order->setUser($user);
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setStatus('pending');

    
        foreach ($panier as $id => $quantity) {
            $produit = $em->getRepository(Produit::class)->find($id);
            if ($produit) {
                // Update product quantity
                $newQuantity = $produit->getQuantite() - $quantity;
                $produit->setQuantite($newQuantity);
                
                // Update status if quantity reaches 0
                if ($newQuantity <= 0) {
                    $produit->setStatus('indisponible');
                }
                
                $orderItem = new OrderItem();
                $orderItem->setProduit($produit);
                $orderItem->setQuantite($quantity);
                $orderItem->setPrix($produit->getPrix() * $quantity); // Set price for order item
                
                // Calculate revenue by product
                if (!isset($revenueByProduct[$produit->getId()])) {
                    $revenueByProduct[$produit->getId()] = 0;
                }
                $revenueByProduct[$produit->getId()] += $produit->getPrix() * $quantity; // Aggregate revenue

                $order->addOrderItem($orderItem);
                $em->persist($orderItem);
                $total += $produit->getPrix() * $quantity;
                
                $em->persist($produit); // Persist the updated product
            }
        }

    
        $order->setTotal($total);
        $em->persist($order);
        $em->flush(); // Persist all changes

        // Optionally, you can log or handle the revenueByProduct data as needed
        // For example, you could save it to a database or process it further

    
        $session->remove('panier'); // Vider le panier après la commande
    
        $this->addFlash('success', 'Votre commande a été passée avec succès !');
        
        // Redirect to the order confirmation page
        return $this->redirectToRoute('app_order_confirmation', ['id' => $order->getId()]);
    }
    
#[Route('/cart/confirmation/{id}', name: 'app_order_confirmation')]
    public function showOrderConfirmation(Order $order): Response
    {
        return $this->render('cart/confirmation.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/cart/success', name: 'app_order_success')]
    public function orderSuccess(EntityManagerInterface $em): Response
    {
        // Assume we retrieve the last order for the current user
        $user = $this->getUser();
        $order = $em->getRepository(Order::class)->findOneBy(['user' => $user], ['id' => 'DESC']);

        if (!$order) {
            $this->addFlash('error', 'Aucune commande trouvée.');
            return $this->redirectToRoute('app_marche');
        }

        return $this->redirectToRoute('app_order_confirmation', ['id' => $order->getId()]);
    }

}
