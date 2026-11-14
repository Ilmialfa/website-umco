export function init(el) {
    const dialog = el.querySelector('[data-gallery-dialog]');
    const dialogImg = el.querySelector('[data-gallery-dialog-img]');
    const dialogCaption = el.querySelector('[data-gallery-dialog-caption]');
    const closeBtn = el.querySelector('[data-gallery-dialog-close]');
    const moreBtn = el.querySelector('[data-gallery-more]');
    const items = Array.from(el.querySelectorAll('[data-gallery-trigger]'));
    const extraMobile = Array.from(el.querySelectorAll('[data-gallery-extra]'));

    const onTriggerClick = (e) => {
        const trigger = e.currentTarget;
        if (!dialog || !dialogImg) return;
        dialogImg.src = trigger.dataset.src || '';
        dialogImg.alt = trigger.dataset.caption || '';
        if (dialogCaption) {
            dialogCaption.textContent = trigger.dataset.caption || '';
        }
        dialog.showModal();
    };

    const onDialogClick = (e) => {
        if (e.target === dialog) {
            dialog.close();
        }
    };

    const onMoreClick = () => {
        extraMobile.forEach((card) => card.classList.remove('hidden'));
        moreBtn?.remove();
    };

    items.forEach((btn) => btn.addEventListener('click', onTriggerClick));
    closeBtn?.addEventListener('click', () => dialog?.close());
    dialog?.addEventListener('click', onDialogClick);
    moreBtn?.addEventListener('click', onMoreClick);

    return () => {
        items.forEach((btn) => btn.removeEventListener('click', onTriggerClick));
        dialog?.removeEventListener('click', onDialogClick);
        moreBtn?.removeEventListener('click', onMoreClick);
    };
}
