<?php

declare(strict_types=1);

namespace Pscsession\core;

use Closure;
use RuntimeException;

/**
 * Petit conteneur d'injection de dépendances.
 *
 * Son unique responsabilité est de :
 *  - enregistrer des services ;
 *  - les construire à la demande (lazy loading) ;
 *  - conserver une seule instance de chaque service.
 *
 */
final class ServiceContainer
{
    /**
     * Factories permettant de construire les services.
     *
     * Une factory est une fonction qui sait créer un service.
     * Elle reçoit toujours le conteneur en paramètre afin de pouvoir
     * récupérer d'autres dépendances si nécessaire.
     *
     * Exemple :
     *
     * [
     *     Logger::class => fn(Container $container) => new Logger(),
     *
     *     MyService::class => fn(Container $container) =>
     *         new MyService(
     *             $container->get(Logger::class)
     *         )
     * ]
     *
     * @var array<string, Closure>
     */
    private array $factories = [];

    /**
     * Services déjà construits.
     *
     * Une fois créé, un service est mémorisé ici afin de toujours
     * retourner la même instance.
     *
     * @var array<string, mixed>
     */
    private array $instances = [];

    /**
     * Enregistre un service.
     *
     * Le service n'est PAS construit immédiatement.
     * On enregistre uniquement la fonction qui saura le construire.
     *
     * Exemple :
     *
     * $container->set(
     *     Logger::class,
     *     fn(Container $container) => new Logger()
     * );
     */
    public function set(string $id, Closure $factory): void
    {
        $this->factories[$id] = $factory;
    }

    /**
     * Retourne un service.
     *
     * Si le service existe déjà, on retourne toujours
     * la même instance.
     *
     * Sinon :
     *  - on exécute sa factory ;
     *  - on mémorise l'objet créé ;
     *  - on retourne cette instance.
     *
     * @throws RuntimeException Si aucun service n'est enregistré.
     */
    public function get(string $id): mixed
    {
        /*
         * Le service existe déjà.
         * On le retourne directement.
         */
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        /*
         * Aucun service n'est enregistré sous cet identifiant.
         */
        if (!isset($this->factories[$id])) {
            throw new RuntimeException(sprintf('Service "%s" is not registered.', $id));
        }

        /*
         * Construction du service.
         *
         * La factory reçoit toujours le conteneur.
         * Cela lui permet de récupérer d'autres services si besoin.
         *
         * Exemple :
         *
         * fn(Container $container) => new MyService(
         *     $container->get(Logger::class),
         *     $container->get(Clock::class)
         * );
         */
        $service = $this->factories[$id]($this);

        /*
         * On conserve cette instance afin de ne jamais
         * reconstruire le même service.
         */
        $this->instances[$id] = $service;

        return $service;
    }

    /**
     * Indique si un service est enregistré.
     */
    public function has(string $id): bool
    {
        return isset($this->factories[$id]);
    }
}
