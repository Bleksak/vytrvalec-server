import { Controller } from '@hotwired/stimulus'
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
    static targets = ['image', 'input', 'dropzoneText']
    static values = { disabled: Boolean }

    constructor(context) {
        super(context);

        this.handleDrop = this.handleDrop.bind(this);
        this.chooseFile = this.chooseFile.bind(this);
        this.displayImage = this.displayImage.bind(this);
        this.updateImagePreview = this.updateImagePreview.bind(this);
    }

    async initialize() {
        this.component = await getComponent(this.element.parentElement);
    }

    connect() {
        this.element.addEventListener('dragenter', (e) => e.preventDefault());
        this.element.addEventListener('dragover', (e) => e.preventDefault());
        this.element.addEventListener('dragleave', (e) => e.preventDefault());

        if (!this.disabledValue) {
            this.element.addEventListener('click', this.chooseFile);
            this.element.addEventListener('drop', this.handleDrop);
            this.inputTarget.addEventListener('change', (e) => {
                const file = e.target.files[0];
                this.updateImagePreview(file);
                this.handleUpload();
            });
        }
    }

    async handleUpload() {
        this.component.files('image', this.inputTarget);
        await this.component.action('submit');
    }

    /**
     * @param {DragEvent} event
     */
    handleDrop(event) {
        event.preventDefault();
        const dt = event.dataTransfer;
        if (dt === null) {
            return;
        }

        const files = dt.files;
        if (files.length === 0) {
            return;
        }

        const file = files[0];

        let fileList = new DataTransfer();
        fileList.items.add(file);
        this.inputTarget.files = files;

        this.handleUpload();
        this.updateImagePreview(file);
    }

    /**
     * @param {string} url
     */
    displayImage(url) {
        this.imageTarget.src = url;
        this.imageTarget.style.display = 'block';
        this.dropzoneTextTarget.style.display = 'none';
    }

    /**
     * @param {File} file
     */
    updateImagePreview(file) {
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = () => {
            this.displayImage(reader.result);
        };
    };

    chooseFile(event) {
        if (!this.disabledValue) {
            this.inputTarget.click();
        }
    }
}
