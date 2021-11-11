<?php

namespace Pushword\AdvancedMainImage\EventSuscriber;

use Pushword\Admin\FormField\Event as FormEvent;
use Pushword\Admin\FormField\PageMainImageField;
use Pushword\Admin\PageAdminInterface;
use Pushword\Admin\Utils\FormFieldReplacer;
use Pushword\AdvancedMainImage\PageAdvancedMainImageFormField;
use Pushword\Core\Component\App\AppPool;
use Pushword\Core\Entity\PageInterface;
use Sonata\AdminBundle\Event\PersistenceEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AdminFormEventSuscriber implements EventSubscriberInterface
{
    private AppPool $apps;

    public function __construct(AppPool $appPool)
    {
        $this->apps = $appPool;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'pushword.admin.load_field' => 'replaceFields',
            'sonata.admin.event.persistence.pre_update' => 'setAdvancedMainImage',
            'sonata.admin.event.persistence.pre_persist' => 'setAdvancedMainImage',
        ];
    }

    /** @psalm-suppress  NoInterfaceProperties */
    public function replaceFields(FormEvent $formEvent): void
    {
        /** @var PageInterface $page */
        $page = $formEvent->getAdmin()->getSubject();

        if (! $this->apps->get($page->getHost())->get('advanced_main_image')) {
            return;
        }

        $formFieldReplacer = new FormFieldReplacer();
        $fields = $formFieldReplacer->run(PageMainImageField::class, PageAdvancedMainImageFormField::class, $formEvent->getFields());

        $formEvent->setFields($fields);
    }

    public function setAdvancedMainImage(PersistenceEvent $persistenceEvent): void
    {
        if (! $persistenceEvent->getAdmin() instanceof PageAdminInterface) {
            return;
        }

        $returnValues = $persistenceEvent->getAdmin()->getRequest()->get($persistenceEvent->getAdmin()->getRequest()->get('uniqid'));

        /** @var PageInterface $subject */
        $subject = $persistenceEvent->getAdmin()->getSubject();

        $subject->setCustomProperty('mainImageFormat', isset($returnValues['mainImageFormat']) ? (int) ($returnValues['mainImageFormat']) : 0);
    }
}
