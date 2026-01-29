import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {

    static targets = ['edit', 'preview'];

    async initialize() {
        this.editComponent = await getComponent(this.editTarget);
        this.previewComponent = await getComponent(this.previewTarget);
    }

    /**
     * @param { {params: { submissionId: number }} } 
     */
    async edit({ params }) {
        const submission_id = params.submissionId;

        await this.editComponent.action('setSubmission', { submission_id });

        document.querySelector('#submission-edit-dialog').showModal();
    }

    /**
     * @param { {params: { submissionId: number }} } 
     */
    async preview({ params }) {
        const submission_id = params.submissionId;

        await this.previewComponent.action('setSubmission', { submission_id });

        document.querySelector('#submission-preview-dialog').showModal();
    }
}
