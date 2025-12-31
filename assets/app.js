import './packages/bootstrap/bootstrap.min.css'
import './packages/bootstrap/bootstrap.bundle.min'
import './styles/app.css'

const updateCampaignLifecycle = (form, toggle, toggleLabel, badge) => {
    if (!form || !toggle || !toggleLabel || !badge) return

    toggle.addEventListener('change', async () => {

        // const archiveCampaign = toggle.value
        const archiveCampaign = toggle.checked ? '0' : '1'

        const formData = new FormData(form)
        formData.set('archive_campaign', archiveCampaign)

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
            });

            const data = await res.json()

            if (!res.ok || !data.ok) {
                throw new Error(data?.error || 'Request failed')
            }

            // Update frontend
            if (data.lifecycle === 'archived') {
                badge.textContent = 'Archiviert'
                badge.classList = ''
                badge.classList = 'badge bg-secondary'

                toggleLabel.textContent = 'Aktivieren'
            } else {
                badge.textContent = 'Aktiv'
                badge.classList = ''
                badge.classList = 'badge bg-success'

                toggleLabel.textContent = 'Archivieren'
            }
        } catch (e) {
            alert('Could not update lifecycle. ' + e.message)
        }
    })
}

const updateCampaignDescription = (form, container, areaShow, buttonShowForm) => {
    if (!form || !container || !areaShow || !buttonShowForm) return

    const buttonCancelEditCampaignDescription = document.getElementById('button-cancel-edit-campaign-description')
    const textAreaEditCampaignDescription = document.getElementById('textarea-edit-campaign-description')

    buttonShowForm.addEventListener('click', () => {
        container.classList.add('d-none')
        form.classList.remove('d-none')
    })

    buttonCancelEditCampaignDescription.addEventListener('click', () => {
        container.classList.remove('d-none')
        form.classList.add('d-none')
    })

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form)

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {'Accept': 'application/json'}
            });

            let data = null;
            const contentType = res.headers.get('content-type') || '';

            if (contentType.includes('application/json')) {
                data = await res.json()
            }

            if (!res.ok || !data.ok) {
                const msg = data?.error ?? 'Du bist nicht berechtigt, diese Notizen zu bearbeiten.'
                throw new Error(msg)
            }

            console.log(data)

            // Update frontend
            areaShow.innerHTML = data.description_html
            container.classList.remove('d-none')
            form.classList.add('d-none')
            textAreaEditCampaignDescription.value = data.description_markdown

        } catch (e) {
            alert('Could not update description. ' + e.message)
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

    // Form update campaign description
    const campaignDescriptionContainer = document.getElementById('campaign-description-container')
    const formEditCampaignDescription = document.getElementById('form-edit-campaign-description')
    const areaShowCampaignDescription = document.getElementById('area-show-campaign-description')
    const buttonShowEditCampaignDescriptionForm = document.getElementById('button-show-edit-campaign-description-form')

    updateCampaignDescription(formEditCampaignDescription, campaignDescriptionContainer, areaShowCampaignDescription, buttonShowEditCampaignDescriptionForm)
})
