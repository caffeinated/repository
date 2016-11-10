<?php

namespace Caffeinated\Repository\Listeners;

use Caffeinated\Repository\Contracts\Repository;

class RepositoryEventListener
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen('*.entity.created', __CLASS__.'@entityCreated');
        $events->listen('*.entity.updated', __CLASS__.'@entityUpdated');
        $events->listen('*.entity.deleted', __CLASS__.'@entityDeleted');
    }

    /**
     * Listen for the *.entity.created event.
     *
     * @param Repository $repository
     * @param mixed      $entity
     *
     * @return void
     */
    public function entityCreated(Repository $repository, $entity)
    {
        $repository->flushCache();
    }

    /**
     * Listen for the *.entity.updated event.
     *
     * @param Repository $repository
     * @param mixed      $entity
     *
     * @return void
     */
    public function entityUpdated(Repository $repository, $entity)
    {
        $repository->flushCache();
    }

    /**
     * Listen for the *.entity.deleted event.
     *
     * @param Repository $repository
     * @param mixed      $entity
     *
     * @return void
     */
    public function entityDeleted(Repository $repository, $entity)
    {
        $repository->flushCache();
    }
}
