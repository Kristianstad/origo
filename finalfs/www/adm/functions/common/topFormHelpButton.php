<?php

function topFormHelpButton(string $helpId): string
{
    static $scriptPrinted=false;
    $safeHelpId=htmlspecialchars($helpId, ENT_QUOTES, 'UTF-8');
    $script='';
    if (!$scriptPrinted)
    {
        $scriptPrinted=true;
        $script=<<<'HTML'
<script>
function toggleImportTooltip(button, pin=false) {
    const existing=document.querySelector('.importHelpTooltip');
    if (existing) {
        if (existing.dataset.helpId === button.dataset.helpId) {
            if (pin && existing.dataset.pinned === 'true') {
                existing.remove();
                return;
            }
            if (!pin && existing.dataset.pinned === 'true') {
                return;
            }
            existing.remove();
        }
        else {
            existing.remove();
        }
    }

    const tooltip=document.createElement('div');
    tooltip.className='importHelpTooltip';
    tooltip.dataset.helpId=button.dataset.helpId;
    tooltip.dataset.pinned=pin ? 'true' : 'false';
    tooltip.textContent='Laddar hjälptext...';
    document.body.appendChild(tooltip);
    positionImportTooltip(button, tooltip);

    fetch('help.php?id=' + encodeURIComponent(button.dataset.helpId), { credentials:'same-origin' })
        .then(response => response.text())
        .then(html => {
            const documentParser=new DOMParser();
            const helpDocument=documentParser.parseFromString(html, 'text/html');
            const closeButton=helpDocument.querySelector('.helpCloseButton');
            if (closeButton) {
                closeButton.remove();
            }
            tooltip.innerHTML=helpDocument.body ? helpDocument.body.innerHTML : 'Ingen hjälptext hittades.';
            positionImportTooltip(button, tooltip);
        })
        .catch(() => { tooltip.textContent='Hjälptexten kunde inte hämtas.'; });
}

function positionImportTooltip(button, tooltip) {
    const bounds=button.getBoundingClientRect();
    const margin=8;
    const tooltipWidth=Math.min(tooltip.offsetWidth, window.innerWidth - margin * 2);
    let left=bounds.right + margin;
    if (left + tooltipWidth > window.innerWidth - margin) {
        left=bounds.left - tooltipWidth - margin;
    }
    tooltip.style.left=Math.max(margin, left + window.scrollX) + 'px';
    const top=Math.min(bounds.top + window.scrollY, window.scrollY + window.innerHeight - tooltip.offsetHeight - margin);
    tooltip.style.top=Math.max(window.scrollY + margin, top) + 'px';
}

document.addEventListener('mouseenter', function(event) {
    const button=event.target.closest('.smallHelpButton');
    if (button) {
        toggleImportTooltip(button);
    }
}, true);

document.addEventListener('mouseleave', function(event) {
    if (event.target.closest('.smallHelpButton')) {
        const tooltip=document.querySelector('.importHelpTooltip');
        if (tooltip?.dataset.pinned !== 'true') {
            tooltip?.remove();
        }
    }
}, true);

document.addEventListener('click', function(event) {
    if (!event.target.closest('.smallHelpButton') && !event.target.closest('.importHelpTooltip')) {
        document.querySelector('.importHelpTooltip')?.remove();
    }
});
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelector('.importHelpTooltip')?.remove();
    }
});
</script>
HTML;
    }

    return $script.'<button aria-label="Hjälp" class="smallHelpButton" type="button" data-help-id="'.$safeHelpId.'" onclick="event.stopPropagation(); toggleImportTooltip(this, true);">?</button>';
}