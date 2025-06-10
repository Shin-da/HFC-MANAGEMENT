class SettingsManager {
    constructor() {
        this.forms = {
            general: document.getElementById('generalSettingsForm'),
            security: document.getElementById('securitySettingsForm')
        };
        this.init();
    }

    async init() {
        await this.loadSettings();
        this.setupEventListeners();
        this.loadSystemInfo();
    }

    async loadSettings() {
        try {
            const response = await fetch('/api/ceo/settings/get.php');
            if (!response.ok) throw new Error('Failed to fetch settings');
            const data = await response.json();
            this.populateSettings(data);
        } catch (error) {
            showErrorToast('Failed to load settings');
            console.error(error);
        }
    }

    setupEventListeners() {
        document.getElementById('saveAllSettings').addEventListener('click', () => {
            this.saveAllSettings();
        });

        Object.values(this.forms).forEach(form => {
            form.addEventListener('change', () => {
                this.markUnsavedChanges();
            });
        });
    }

    markUnsavedChanges() {
        const saveBtn = document.getElementById('saveAllSettings');
        saveBtn.classList.add('unsaved');
        saveBtn.textContent = 'Save Changes*';
    }

    async saveAllSettings() {
        const settings = {
            general: this.getFormData(this.forms.general),
            security: this.getFormData(this.forms.security)
        };

        try {
            const response = await fetch('/api/ceo/settings/save.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(settings)
            });

            if (!response.ok) throw new Error('Failed to save settings');
            showSuccessToast('Settings saved successfully');
        } catch (error) {
            showErrorToast('Failed to save settings');
            console.error(error);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new SettingsManager();
});
