import './packages/bootstrap/bootstrap.min.css'
import './packages/bootstrap/bootstrap.bundle.min'
import './styles/app.css';

const updateCampaignLifecycle = (form, toggle, toggleLabel, badge) => {
    if (!form || !toggle || !toggleLabel || !badge) return

    toggle.addEventListener('change', async (e) => {
        e.preventDefault()

        const archiveCampaign = toggle.value
        console.log(`${+archiveCampaign === 1 ? 'Archiving' : 'Activating'} campaign.`)

        const formData = new FormData(form)
        formData.set('archive_campaign', archiveCampaign)

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
            });

            const data = await res.json();

            if (!res.ok || !data.ok) {
                throw new Error(data?.error || 'Request failed');
            }

            // Update frontend
            if (data.lifecycle === 'archived') {
                badge.textContent = 'Archived'
                badge.classList = ''
                badge.classList = 'badge bg-secondary'

                toggle.value = '0'
                toggleLabel.textContent = 'Aktivieren'
            } else {
                badge.textContent = 'Active'
                badge.classList = ''
                badge.classList = 'badge bg-success'

                toggle.value = '1'
                toggleLabel.textContent = 'Archivieren'
            }
        } catch (e) {
            alert('Could not update lifecycle. ' + e.message);
        }
    })
}

document.addEventListener('DOMContentLoaded', () => {

    // Form update campaign lifecycle
    const formCampaignLifecycle = document.getElementById('form-campaign-lifecycle');
    const toggleCampaignLifecycle = document.getElementById('toggle-switch-lifecycle');
    const toggleCampaignLifecycleLabel = document.getElementById('toggle-switch-lifecycle-lable');
    const campaignLifecycleBadge = document.getElementById('campaign-lifecycle-badge')

    updateCampaignLifecycle(formCampaignLifecycle, toggleCampaignLifecycle, toggleCampaignLifecycleLabel, campaignLifecycleBadge)
})
