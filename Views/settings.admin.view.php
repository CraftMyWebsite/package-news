<?php

use CMW\Entity\News\NewsSettingsEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

Website::setTitle(LangManager::translate('news.dashboard.title') . " - " . LangManager::translate("news.menu.settings"));
Website::setDescription(LangManager::translate('news.dashboard.desc'));

/* @var NewsSettingsEntity|null $settings */
?>

<h3>
    <i class="fa-solid fa-newspaper"></i> <?= LangManager::translate('news.dashboard.title') . " - " . LangManager::translate("news.menu.settings") ?>
</h3>

<div class="center-flex mt-3">
    <div class="flex-content-lg">
        <div class="card">
            <form method="post">
                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                <div class="space-y-4">
                    <div class="alert">
                        <p><i class="fa-solid fa-circle-info"></i> <?= LangManager::translate('news.settings.cron_info') ?></p>

                        <p>
                            <?= LangManager::translate('news.settings.documentation_link') ?> <a
                                href="https://github.com/CraftMyWebsite/package-news/wiki/Publications-programm%C3%A9es"
                                target="_blank" class="text-blue-600 hover:underline"><?= LangManager::translate('news.settings.scheduled_publications') ?></a>
                        </p>
                    </div>

                    <label class="toggle">
                        <p class="toggle-label">
                            <?= LangManager::translate('news.settings.enable_cron_toggle') ?>
                        </p>
                        <input type="checkbox" value="1" class="toggle-input" id="scheduled_publications"
                               name="scheduled_publications"
                            <?= $settings?->isEnableScheduledPublishing() ? 'checked' : '' ?>>
                        <div class="toggle-slider"></div>
                    </label>

                    <?php if ($settings !== null && $settings->isEnableScheduledPublishing()): ?>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <?= LangManager::translate('news.settings.cron_url_label') ?>
                            </label>
                            <div class="input-btn">
                                <input
                                    type="text"
                                    id="cronUrl"
                                    readonly
                                    value="<?= $settings->getFullCronUrl() ?>"
                                >
                                <button
                                    type="button"
                                    onclick="copyToClipboard()"
                                    title="<?= LangManager::translate('news.settings.copy_url') ?>"
                                >
                                    <i class="fa-solid fa-copy"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500">
                                <?= LangManager::translate('news.settings.cron_url_help') ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <div>
                        <button type="submit" class="btn-center btn-primary">
                            <?= LangManager::translate('core.btn.save') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Script to copy the cron URL to clipboard -->
<script>
    function copyToClipboard() {
        const input = document.getElementById('cronUrl');
        const button = document.querySelector('button[onclick="copyToClipboard()"]');
        const icon = button.querySelector('i');

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(input.value).then(() => {
                showSuccessFeedback(button, icon);
            }).catch(err => {
                fallbackCopy(input, button, icon);
            });
        } else {
            fallbackCopy(input, button, icon);
        }
    }

    function fallbackCopy(input, button, icon) {
        input.select();
        input.setSelectionRange(0, 99999);

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showSuccessFeedback(button, icon);
            } else {
                showErrorFeedback(button, icon);
            }
        } catch (err) {
            console.error('Erreur execCommand:', err);
            showErrorFeedback(button, icon);
        }

        window.getSelection().removeAllRanges();
    }

    function showSuccessFeedback(button, icon) {
        const originalClass = icon.className;
        icon.className = 'fa-solid fa-check';
        button.style.backgroundColor = '#10b981';
        button.style.borderColor = '#10b981';

        setTimeout(() => {
            icon.className = originalClass;
            button.style.backgroundColor = '';
            button.style.borderColor = '';
        }, 2000);
    }

    function showErrorFeedback(button, icon) {
        const originalClass = icon.className;
        icon.className = 'fa-solid fa-exclamation-triangle';
        button.style.backgroundColor = '#ef4444';
        button.style.borderColor = '#ef4444';

        setTimeout(() => {
            icon.className = originalClass;
            button.style.backgroundColor = '';
            button.style.borderColor = '';
        }, 2000);
    }
</script>