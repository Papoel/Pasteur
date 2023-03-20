<?php

namespace App\Controller\Payment;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Entity\Order\Order;
use App\Entity\Order\OrderDetails;
use App\Entity\Payment;
use App\Entity\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use UnexpectedValueException;

class WebhooksStripeController extends AbstractController
{
    #[Route('/webhooks', name: 'app_webhooks_stripe', methods: ['POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $session = $request->getSession()->get(name: 'details_inscription');
        $endpoint_secret = $this->getParameter(name: 'STRIPE_WEBHOOK_SECRET');
        $payload = @file_get_contents(filename: 'php://input');

        $header = 'Stripe-Signature';
        $signature = $request->headers->get($header);

        $sig_header = $signature;
        $event = null;

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (UnexpectedValueException $e) {
            // Payload invalide
            echo $e->getMessage();
            http_response_code(response_code: 400);
            exit();
        } catch (SignatureVerificationException $e) {
            // Signature Invalide
            echo $e->getMessage();
            http_response_code(response_code: 400);
            exit();
        }

        // ******** INFORMATION LIÉE À UNE COMMANDE ********
        // Récupérer l'identifiant de la commande
        $orderId = $event->data->object->metadata->orderId;
        // Récupérer la commande
        $order = $em->getRepository(Order::class)->findOneBy(['id' => $orderId]);
        // Récupérer les détails de la commande
        $orderDetails = $em->getRepository(OrderDetails::class)->findBy(['myOrder' => $orderId]);
        // ******** INFORMATION LIÉE À UNE COMMANDE ********

        // Gestion de l'événement 'checkout.session.completed'
        $eventType = $event->type;
        if ($eventType === 'checkout.session.completed') {
            $metadata = $event->data->object->metadata;
            // Création de fichier pour le debug uniquement
            // file_put_contents(filename: 'details_inscription', data: $metadata);
            // file_put_contents(filename: 'checkout.completed', data: $event);

            $registrationEventSlug = $metadata['evenement_slug'];
            $eventRegistered = $em->getRepository(Event::class)
                ->findOneBy(['slug' => $registrationEventSlug]);

            $registrationId = $metadata['inscription_id'];
            $registration = $em->getRepository(RegistrationEvent::class)
                ->findOneBy(['id' => $registrationId]);

            // ******** GESTION D'UNE INSCRIPTION ********
            if ($registration) {
                $registration->setPaid(Paid: true);
                // Sauvegarder les informations de paiement en base de données
                $payment = new Payment();
                $payment->setEvent(event: $eventRegistered);
                $payment->setRegistrationEvent(registrationEvent: $registration);
                $payment->setStripeSessionId(stripeSessionId: $event->data->object->id);
                $payment->setStripePaymentIntentId(stripePaymentIntentId: $event->data->object->payment_intent);
                $payment->setStripePaymentIntentStatus(stripePaymentIntentStatus: $event->data->object->payment_status);
                $payment->setUnitPrice((int)$metadata['prix_unitaire']);
                $payment->setAmount(amount: $event->data->object->amount_total);
                $payment->setReservedPlaces(reservedPlaces: $registration->getChildren()->count());

                $em->persist($payment);
            }

            // ******** GESTION D'UNE COMMANDE ********
            if ($order) {
                // Mettre à jour la commande avec les informations du paiement
                $order->setPaid(paid: true);
                $order->setStripePaymentIntentId(stripePaymentIntentId: $event->data->object->payment_intent);
                $em->persist($order);

                // Parcourir les détails de la commande pour récupérer l'identifiant et la quantité de chaque produit
                foreach ($orderDetails as $orderDetail) {
                    $productId = $orderDetail->getProductId();
                    $quantity = $orderDetail->getQuantity();

                    // Mettre à jour le stock du produit
                    /** @var Product $product */
                    $product = $em->getRepository(Product::class)->findOneBy(['id' => $productId]);
                    $newStock = $product->getStock() - $quantity;
                    $product->setStock($newStock);

                    // Ajouter les produits achetés au nombre de produits vendus (ou réservés)
                    $newReserved = $product->getReserved() + $quantity;
                    $product->setReserved($newReserved);

                    $em->persist($product);
                }
            }

            $em->flush();

        } else {
            // Gérer les événements inconnus ou non traités
            return new Response(content: 'Type d\'événement non géré', status: Response::HTTP_BAD_REQUEST);
        }

        return new Response(content: 'Le webhook inscription a été reçu avec succès.', status: Response::HTTP_OK);
    }
}
