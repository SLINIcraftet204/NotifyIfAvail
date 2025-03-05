<?php

namespace NotifyIfAvail;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\MailTemplate\Service\MailTemplateService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\Snippet\SnippetService;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class NotifyIfAvail extends Plugin
{
    public function install(InstallContext $context): void
    {
        parent::install($context);

        $this->registerMailTemplateType($context->getContext());
        $this->registerEmailTemplate($context->getContext());
        $this->registerSnippets($context->getContext());
    }

    private function registerEmailTemplate(Context $context): void
    {
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        $mailTemplateRepository->upsert([
            [
                'id' => 'c6d2c6141e9f44c4a3eb110d2c58c823',
                'mailTemplateTypeId' => '9f5b31d14c9d4b6a8d2e8f01b5b2a3d7', // ID des Mail-Template-Typs
                'systemDefault' => false,
                'description' => 'Benachrichtigung, wenn Produkt wieder verfügbar ist',
                'contentHtml' => file_get_contents(__DIR__ . '/Resources/email-templates/notification_email.html.twig'),
                'contentPlain' => strip_tags(file_get_contents(__DIR__ . '/Resources/email-templates/notification_email.html.twig')),
                'subject' => 'Ihr gewünschter Artikel ist wieder verfügbar!'
            ]
        ], $context);
    }


    private function registerMailTemplateType(Context $context): void
    {
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');

        $mailTemplateTypeRepository->upsert([
            [
                'id' => '9f5b31d14c9d4b6a8d2e8f01b5b2a3d7',
                'name' => 'Notify If Available Email',
                'technicalName' => 'notify_if_avail_email',
                'availableEntities' => ['product' => 'product'],
            ]
        ], $context);
    }


    private function getMailTemplateTypeId(Context $context): ?string
    {
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('technicalName', 'notify_if_avail_email'));

        $mailTemplateType = $mailTemplateTypeRepository->search($criteria, $context)->first();

        return $mailTemplateType ? $mailTemplateType->getId() : '9f5b31d14c9d4b6a8d2e8f01b5b2a3d7';
    }


    private function registerSnippets(Context $context): void
    {
        $snippetRepository = $this->container->get('snippet.repository');

        $snippetRepository->upsert([
            [
                'id' => '1f5b31d14c9d4b6a8d2e8f01b5b2a3d8',
                'setId' => '0194da4af1f7720a8183c85d4b7fae35', // Standard-Set-ID für Shopware-Snippets
                'translationKey' => 'NotifyIfAvail.notify_me',
                'value' => 'Benachrichtigen, wenn verfügbar',
                'author' => 'NotifyIfAvail Plugin',
            ],
            [
                'id' => '2f5b31d14c9d4b6a8d2e8f01b5b2a3d8',
                'setId' => '0194da4af1f7720a8183c85d4b7fae35',
                'translationKey' => 'NotifyIfAvail.email_placeholder',
                'value' => 'Geben Sie Ihre E-Mail-Adresse ein',
                'author' => 'NotifyIfAvail Plugin',
            ],
            [
                'id' => '3f5b31d14c9d4b6a8d2e8f01b5b2a3d8',
                'setId' => '0194da4af1f7720a8183c85d4b7fae35',
                'translationKey' => 'NotifyIfAvail.success_message',
                'value' => 'Sie werden benachrichtigt, sobald der Artikel verfügbar ist.',
                'author' => 'NotifyIfAvail Plugin',
            ],
            [
                'id' => '4f5b31d14c9d4b6a8d2e8f01b5b2a3d8',
                'setId' => '0194da4af1f7720a8183c85d4c20138e', // Englische ID
                'translationKey' => 'NotifyIfAvail.notify_me',
                'value' => 'Notify me when available',
                'author' => 'NotifyIfAvail Plugin',
            ],
            [
                'id' => '5f5b31d14c9d4b6a8d2e8f01b5b2a3d8',
                'setId' => '0194da4af1f7720a8183c85d4c20138e',
                'translationKey' => 'NotifyIfAvail.email_placeholder',
                'value' => 'Enter your email address',
                'author' => 'NotifyIfAvail Plugin',
            ],
            [
                'id' => '6f5b31d14c9d4b6a8d2e8f01b5b2a3d8',
                'setId' => '0194da4af1f7720a8183c85d4c20138e',
                'translationKey' => 'NotifyIfAvail.success_message',
                'value' => 'You will be notified when the item is available.',
                'author' => 'NotifyIfAvail Plugin',
            ]
        ], $context);
    }

    public function uninstall(UninstallContext $context): void
    {
        if (!$context->keepUserData()) {
            $connection = $this->container->get(Connection::class);
            $connection->executeStatement('DROP TABLE IF EXISTS notifyifavail_plugin_notification');
        }
        parent::uninstall($context);
    }

    public function activate(ActivateContext $context): void
    {
        parent::activate($context);
    }

    public function deactivate(DeactivateContext $context): void
    {
        parent::deactivate($context);
    }
}
